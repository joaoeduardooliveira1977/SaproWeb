# Deploy em Produção — SAPRO
> Gerado em 2026-03-16
> Execute os passos **na ordem abaixo** via pgAdmin ou terminal do servidor.

---

## 1. Backup antes de tudo

```bash
pg_dump -U postgres saproweb > backup_pre_deploy_$(date +%Y%m%d).sql
```

---

## 2. Migrations — SQL direto (pgAdmin)

Execute cada bloco separadamente e confirme o sucesso antes de prosseguir.

---

### 2.1 — OFX / Conciliação Bancária
_Migration: `2024_01_03_000012_create_ofx_tables.php`_

```sql
CREATE TABLE ofx_importacoes (
    id              BIGSERIAL PRIMARY KEY,
    arquivo         VARCHAR(200) NOT NULL,
    banco           VARCHAR(100),
    agencia         VARCHAR(30),
    conta           VARCHAR(60),
    data_ini        DATE,
    data_fim        DATE,
    total_lancamentos INTEGER NOT NULL DEFAULT 0,
    conciliados     INTEGER NOT NULL DEFAULT 0,
    usuario_id      BIGINT REFERENCES usuarios(id) ON DELETE SET NULL,
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP
);

CREATE TABLE ofx_lancamentos (
    id              BIGSERIAL PRIMARY KEY,
    importacao_id   BIGINT NOT NULL REFERENCES ofx_importacoes(id) ON DELETE CASCADE,
    data            DATE NOT NULL,
    valor           NUMERIC(15,2) NOT NULL,
    tipo            VARCHAR(20),
    descricao       VARCHAR(500),
    fitid           VARCHAR(150),
    conciliado      BOOLEAN NOT NULL DEFAULT false,
    referencia_tipo VARCHAR(20),
    referencia_id   BIGINT,
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP
);

CREATE INDEX idx_ofx_lanc_importacao_conciliado ON ofx_lancamentos(importacao_id, conciliado);
CREATE INDEX idx_ofx_lanc_data ON ofx_lancamentos(data);
```

---

### 2.2 — CRM / Pipeline
_Migration: `2024_01_03_000013_create_crm_tables.php`_

```sql
CREATE TABLE crm_oportunidades (
    id              BIGSERIAL PRIMARY KEY,
    nome            VARCHAR(150) NOT NULL,
    telefone        VARCHAR(30),
    email           VARCHAR(150),
    cpf_cnpj        VARCHAR(18),
    origem          VARCHAR(30) NOT NULL DEFAULT 'indicacao',
    titulo          VARCHAR(200),
    area_direito    VARCHAR(80),
    valor_estimado  NUMERIC(15,2),
    descricao       TEXT,
    etapa           VARCHAR(30) NOT NULL DEFAULT 'novo_contato',
    responsavel_id  BIGINT REFERENCES usuarios(id) ON DELETE SET NULL,
    data_previsao   DATE,
    data_fechamento DATE,
    motivo_perda    VARCHAR(200),
    convertido      BOOLEAN NOT NULL DEFAULT false,
    pessoa_id       BIGINT REFERENCES pessoas(id) ON DELETE SET NULL,
    usuario_id      BIGINT REFERENCES usuarios(id) ON DELETE SET NULL,
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP
);

CREATE INDEX idx_crm_op_etapa        ON crm_oportunidades(etapa);
CREATE INDEX idx_crm_op_responsavel  ON crm_oportunidades(responsavel_id);

CREATE TABLE crm_atividades (
    id              BIGSERIAL PRIMARY KEY,
    oportunidade_id BIGINT NOT NULL REFERENCES crm_oportunidades(id) ON DELETE CASCADE,
    tipo            VARCHAR(20) NOT NULL DEFAULT 'tarefa',
    descricao       TEXT NOT NULL,
    data_prevista   DATE,
    data_realizada  DATE,
    concluida       BOOLEAN NOT NULL DEFAULT false,
    usuario_id      BIGINT REFERENCES usuarios(id) ON DELETE SET NULL,
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP
);

CREATE INDEX idx_crm_atv_op_concluida ON crm_atividades(oportunidade_id, concluida);
CREATE INDEX idx_crm_atv_data         ON crm_atividades(data_prevista);
```

---

### 2.3 — Configurações de Notificação WhatsApp
_Migration: `2024_01_03_000014_create_notificacao_configs_table.php`_

```sql
CREATE TABLE notificacao_configs (
    id            BIGSERIAL PRIMARY KEY,
    tipo          VARCHAR(40) NOT NULL UNIQUE,
    label         VARCHAR(100) NOT NULL,
    ativo         BOOLEAN NOT NULL DEFAULT true,
    antecedencias JSONB NOT NULL,
    canal         VARCHAR(10) NOT NULL DEFAULT 'whatsapp',
    created_at    TIMESTAMP,
    updated_at    TIMESTAMP
);

-- Dados padrão
INSERT INTO notificacao_configs (tipo, label, ativo, antecedencias, canal, created_at, updated_at) VALUES
  ('prazo_fatal',    'Prazos fatais',          true, '[1, 3]',       'whatsapp', NOW(), NOW()),
  ('prazo_vencendo', 'Prazos normais',         true, '[1, 3, 7]',    'whatsapp', NOW(), NOW()),
  ('audiencia',      'Audiências',              true, '[1]',          'whatsapp', NOW(), NOW()),
  ('cobranca',       'Cobranças de honorários', true, '[3, 7, 15]',   'whatsapp', NOW(), NOW());
```

---

### 2.4 — Administradoras
_Migration: `2024_01_03_000015_create_administradoras_table.php`_

```sql
CREATE TABLE administradoras (
    id          BIGSERIAL PRIMARY KEY,
    nome        VARCHAR(150) NOT NULL,
    cnpj        VARCHAR(18) UNIQUE,
    telefone    VARCHAR(30),
    email       VARCHAR(150),
    contato     VARCHAR(100),
    observacoes TEXT,
    ativo       BOOLEAN NOT NULL DEFAULT true,
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP
);

ALTER TABLE pessoas
    ADD COLUMN administradora_id BIGINT REFERENCES administradoras(id) ON DELETE SET NULL;
```

---

### 2.5 — Novos campos no formulário de Processos
_Migration: `2024_01_03_000016_alter_processos_add_new_fields.php`_

```sql
ALTER TABLE processos
    ADD COLUMN extrajudicial BOOLEAN NOT NULL DEFAULT false,
    ADD COLUMN autor_reu     VARCHAR(10),
    ADD COLUMN unidade       VARCHAR(100);
```

---

### 2.6 — Tabela pivot Processo × Advogados
_Migration: `2024_01_03_000017_create_processo_advogado_table.php`_

```sql
CREATE TABLE processo_advogado (
    processo_id BIGINT NOT NULL REFERENCES processos(id) ON DELETE CASCADE,
    advogado_id BIGINT NOT NULL REFERENCES pessoas(id)   ON DELETE CASCADE,
    PRIMARY KEY (processo_id, advogado_id)
);
```

> **Atenção:** Processos já cadastrados com `advogado_id` preenchido **não** são migrados automaticamente para a nova tabela pivot. Se quiser manter o histórico, execute também:
> ```sql
> INSERT INTO processo_advogado (processo_id, advogado_id)
> SELECT id, advogado_id FROM processos
> WHERE advogado_id IS NOT NULL
> ON CONFLICT DO NOTHING;
> ```

---

## 3. Atualizar o código da aplicação

```bash
cd /var/www/saproweb   # ajuste para o seu caminho

git pull origin main

composer install --no-dev --optimize-autoloader

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Marcar as migrations como executadas (já rodou o SQL manual acima)
php artisan migrate --pretend   # só para conferir — não deve sobrar nada pendente
```

Se preferir rodar as migrations pelo artisan em vez de SQL manual, use:
```bash
php artisan migrate
```

---

## 4. Verificações pós-deploy

- [ ] Acessar `/processos/novo` e confirmar autocomplete de cliente funcionando
- [ ] Marcar "Extrajudicial" sem número → campos judiciais desabilitam
- [ ] Salvar processo com múltiplos advogados e conferir na tela de detalhes
- [ ] Acessar `/administradoras` → cadastrar uma administradora
- [ ] Acessar `/tabelas` → clicar nos cards → CRUD funcionando
- [ ] Acessar `/crm` → pipeline visível
- [ ] Acessar `/conciliacao-bancaria` → aba importar visível
- [ ] Acessar Administração → Notificações WhatsApp → aba Configurações visível

---

## 5. Cron — garantir que está ativo

Confirmar que o agendamento do Laravel está no crontab do servidor:

```bash
crontab -l
# deve conter:
* * * * * cd /var/www/saproweb && php artisan schedule:run >> /dev/null 2>&1
```
