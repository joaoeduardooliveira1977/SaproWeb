# ⚖️ SaproWeb — Laravel + Livewire

Sistema de Acompanhamento de Processos Jurídicos.
Construído com **PHP + Laravel 11 + Livewire 3 + PostgreSQL**.

---

## 📋 Pré-requisitos

| Ferramenta | Versão mínima |
|---|---|
| PHP         | 8.2+ |
| Composer    | 2.x  |
| PostgreSQL  | 14+  |
| Node.js     | 18+ (só para npm run dev) |

---

## 🚀 Instalação Passo a Passo

### 1. Instalar dependências PHP
```bash
composer install
```

### 2. Configurar o ambiente
```bash
cp .env.example .env
php artisan key:generate
```
Edite o `.env` com os dados do seu banco PostgreSQL:
```
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=sapro_db
DB_USERNAME=postgres
DB_PASSWORD=sua_senha
```

### 3. Criar o banco de dados
```sql
-- No psql ou pgAdmin:
CREATE DATABASE sapro_db;
```

### 4. Rodar as migrations (cria todas as tabelas)
```bash
php artisan migrate
```

### 5. Popular dados iniciais
```bash
php artisan db:seed
```

### 6. Iniciar o servidor
```bash
php artisan serve
```

Acesse: **http://localhost:8000**

---

## 🔑 Credenciais Padrão

| Campo | Valor |
|---|---|
| Login | `admin` |
| Senha | `sapro2025` |

> ⚠️ **Altere a senha após o primeiro acesso em "Minha Conta".**

---

## 📂 Estrutura do Projeto

```
saproweb/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php       ← Login / Logout
│   │   │   └── ProcessoController.php   ← Detalhes do processo
│   │   └── Livewire/
│   │       ├── Dashboard.php            ← Tela inicial
│   │       ├── Processos.php            ← Lista de processos
│   │       ├── ProcessoForm.php         ← Cadastro/edição
│   │       ├── Pessoas.php              ← CRUD pessoas (com modal)
│   │       └── Agenda.php              ← Agenda (com modal)
│   └── Models/
│       ├── Pessoa.php                   ← Multi-tipo sem duplicidade
│       ├── Usuario.php                  ← Autenticável
│       ├── Processo.php                 ← Relacionamentos completos
│       ├── Modulos.php                  ← Andamento, Agenda, Custa, Auditoria
│       └── Dominio.php                  ← Tabelas de lookup
├── database/
│   ├── migrations/
│   │   ├── ..._create_tabelas_dominio.php
│   │   ├── ..._create_pessoas_usuarios.php
│   │   └── ..._create_processos_e_modulos.php
│   └── seeders/
│       └── DatabaseSeeder.php           ← Dados iniciais
├── resources/views/
│   ├── layouts/app.blade.php            ← Layout com sidebar
│   ├── auth/login.blade.php             ← Tela de login
│   ├── livewire/                        ← Templates Livewire
│   └── *.blade.php                      ← Páginas
├── routes/web.php                        ← Todas as rotas
└── config/auth.php                       ← Guard 'usuarios'
```

---

## 🧩 Como o Livewire Funciona

No Livewire, o PHP e o HTML ficam integrados. Não é necessário escrever JavaScript:

```php
// Livewire Component (PHP)
class Pessoas extends Component {
    public string $busca = '';          // propriedade reativa

    public function salvar() { ... }    // action chamada pelo botão

    public function render() {
        return view('livewire.pessoas', [
            'pessoas' => Pessoa::busca($this->busca)->paginate(15)
        ]);
    }
}
```

```html
<!-- Blade Template (HTML) -->
<input wire:model.live="busca" placeholder="Buscar...">  <!-- reativo! -->
<button wire:click="salvar">Salvar</button>               <!-- sem JS! -->
```

---

## 🗄️ Migração dos Dados do Access

Execute o script Python para importar os dados do banco Access:

```bash
# Instalar dependências
pip install pyodbc psycopg2-binary pandas openpyxl

# Executar migração
python migrate_access.py \
  --access "C:\Caminho\SaproDB.accdb" \
  --pg-host localhost \
  --pg-db sapro_db \
  --pg-user postgres \
  --pg-password sua_senha
```

> O script `migrate_access.py` será gerado na próxima etapa.

---

## 🌐 Hospedagem Recomendada (uso interno)

Para 1-3 usuários internos, a opção mais simples:

| Opção | Custo | Facilidade |
|---|---|---|
| **Laragon** (Windows local)    | Grátis  | ⭐⭐⭐⭐⭐ |
| **VPS própria** (Linux)        | ~R$30/mês | ⭐⭐⭐⭐ |
| **Railway.app**                | ~R$25/mês | ⭐⭐⭐⭐⭐ |
| **Hospedagem PHP** (HostGator) | ~R$15/mês | ⭐⭐⭐ |

---

## ✅ Funcionalidades Implementadas

- [x] Login com sessão (sem JWT — mais simples para uso interno)
- [x] Dashboard com estatísticas em tempo real
- [x] CRUD de Pessoas com multi-tipo (sem duplicidade)
- [x] CRUD de Processos com todos os relacionamentos
- [x] Módulo de Agenda com modal (Livewire)
- [x] Módulo de Andamentos por processo
- [x] Módulo de Custas com totalizadores
- [x] Tabelas de domínio (fases, riscos, tipos, etc.)
- [x] Índices Monetários (IPCA, IGP-M, TR)
- [x] Auditoria automática de todas as operações
- [x] Controle de perfis (admin / advogado / operador)
- [x] Paginação em todas as listagens
- [x] Busca em tempo real com Livewire

## 🔜 Próximas Etapas Sugeridas

- [ ] Script de migração do banco Access
- [ ] Módulo de Relatórios (PDF com DomPDF)
- [ ] Notificações de prazos por e-mail
- [ ] Upload de documentos por processo
