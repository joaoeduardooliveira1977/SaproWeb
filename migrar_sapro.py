"""
╔══════════════════════════════════════════════════════════════════════╗
║  SAPRO — Script de Migração Access → PostgreSQL                     ║
║  De: PROCESSOBittar.mdb  Para: sapro_db (PostgreSQL)                ║
║                                                                      ║
║  Uso:                                                                ║
║    python migrar_sapro.py                                            ║
║    python migrar_sapro.py --dry-run    (só mostra o que faria)      ║
║    python migrar_sapro.py --tabela PROCESSO  (só uma tabela)        ║
╚══════════════════════════════════════════════════════════════════════╝

Dependências (instale antes):
  pip install pyodbc psycopg2-binary tqdm

No Windows 64 bits, o Access Database Engine precisa ser 64 bits:
  https://www.microsoft.com/en-us/download/details.aspx?id=54920
"""

import pyodbc
import psycopg2
import psycopg2.extras
import argparse
import sys
import re
from datetime import datetime, date
from decimal import Decimal, InvalidOperation
from tqdm import tqdm

# ══════════════════════════════════════════════
# CONFIGURAÇÕES — edite aqui
# ══════════════════════════════════════════════

ACCESS_FILE = r"C:\projetos\saproweb-base\banco\PROCESSOBittar.mdb"  # ← altere para o caminho real

PG_CONFIG = {
    "host":     "localhost",
    
    "port":     5432,
    "dbname":   "sapro_db",
    "user":     "postgres",
    "password": "sapro_2025",   # ← altere
}

# ══════════════════════════════════════════════
# MAPEAMENTO Access → PostgreSQL
# Baseado na estrutura real do banco Bittar
# ══════════════════════════════════════════════

MAPEAMENTO = {

    # ── Tabelas de domínio ─────────────────────

    "ACAO": {
        "destino": "tipos_acao",
        "campos": {
            "CODIGO":    "codigo",
            "DESCRICAO": "descricao",
        },
        "defaults": {"ativo": True},
        "pk_origem": "CODIGO",
    },

    "ASSUNTO": {
        "destino": "assuntos",
        "campos": {
            "CODIGO":  "codigo",
            "ASSUNTO": "descricao",
        },
        "defaults": {"ativo": True},
        "pk_origem": "CODIGO",
    },

    "RISCO": {
        "destino": "graus_risco",
        "campos": {
            "CODIGO": "codigo",
            "Desc":   "descricao",
        },
        "defaults": {"cor_hex": "#64748b"},
        "pk_origem": "CODIGO",
        "transform": {
            "descricao": lambda v: _risco_cor(v),  # define cor por nome
        }
    },

    "REPART": {
        "destino": "reparticoes",
        "campos": {
            "CODIGO":    "codigo",
            "DESCRICAO": "descricao",
        },
        "defaults": {"ativo": True},
        "pk_origem": "CODIGO",
    },

    "SECRET": {
        "destino": "secretarias",
        "campos": {
            "CODIGO":    "codigo",
            "DESCRICAO": "descricao",
        },
        "defaults": {"ativo": True},
        "pk_origem": "CODIGO",
    },

    "SITUA": {
        "destino": None,  # status em processos — mapeado inline
        "info": "Campo STATUS em processos. Valores mapeados diretamente.",
    },

    "TIPO": {
        "destino": "tipos_processo",
        "campos": {
            "CODIGO": "codigo",
            "Desc":   "descricao",
        },
        "defaults": {"ativo": True},
        "pk_origem": "CODIGO",
    },

    # ── Advogados → pessoas ────────────────────

    "ADVOG": {
        "destino": "pessoas",
        "campos": {
            "CODIGO": "_codigo_origem",   # guardamos para mapear depois
            "NOME":   "nome",
        },
        "tipo_pessoa": "Advogado",
        "pk_origem": "CODIGO",
    },

    "JUIZES": {
        "destino": "pessoas",
        "campos": {
            "CODIGO": "_codigo_origem",
            "NOME":   "nome",
        },
        "tipo_pessoa": "Juiz",
        "pk_origem": "CODIGO",
    },

    # ── Empresas/Clientes → pessoas ───────────

    "EMPRESAS": {
        "destino": "pessoas",
        "campos": {
            "CODIGO":       "_codigo_origem",
            "RAZAO SOCIAL": "nome",
            "NM FANTASIA":  "nome_fantasia",    # observacoes
            "CNPJ/CPF":     "cpf_cnpj",
            "FONE1":        "telefone",
            "FONE2":        "celular",
            "EMAIL":        "email",
            "ENDERECO":     "logradouro",
            "CIDADE":       "cidade",
            "OBSERVACAO":   "observacoes",
            "SEXO":         "_sexo",
            "ESTCIVIL":     "_estcivil",
        },
        "tipo_pessoa": "Cliente",
        "pk_origem": "CODIGO",
    },

    "PCONTRARIA": {
        "destino": "pessoas",
        "campos": {
            "CODIGO":      "_codigo_origem",
            "RAZAO SOCIAL":"nome",
            "CNPJ/CPF":    "cpf_cnpj",
            "FONE1":       "telefone",
            "EMAIL":       "email",
            "ENDERECO":    "logradouro",
            "CIDADE":      "cidade",
            "OBSERVACAO":  "observacoes",
        },
        "tipo_pessoa": "Parte Contrária",
        "pk_origem": "CODIGO",
    },

    # ── Processos ──────────────────────────────

    "PROCESSO": {
        "destino": "processos",
        "campos": {
            "PROCESSO":    "numero",
            "CONTRARIA":   "parte_contraria",
            "DTDIST":      "data_distribuicao",
            "VALOR":       "valor_causa",
            "CODASSUNTO":  "_cod_assunto",
            "CODACAO":     "_cod_acao",
            "CODREPART":   "_cod_repart",
            "CODSECRET":   "_cod_secret",
            "CODADVOG":    "_cod_advog",
            "CODSITUA":    "_cod_situa",
            "JUIZID":      "_cod_juiz",
            "TPCLIENTE":   "_cod_cliente",
            "RISCO":       "_cod_risco",
            "TIPO":        "_cod_tipo",
            "ARQUIVADO":   "_arquivado",
            "CODEMPRESA":  "_cod_empresa",
        },
        "pk_origem": "PROCESSO",
    },

    # ── Andamentos ─────────────────────────────

    "ANDAMENTO": {
        "destino": "andamentos",
        "campos": {
            "PROCESSO":   "_cod_processo",
            "DATA":       "data",
            "DESCRICAO":  "descricao",
            "CODEMPRESA": "_cod_empresa",
        },
    },

    # ── Agenda ─────────────────────────────────

    "AGENDA": {
        "destino": "agenda",
        "campos": {
            "PROCESSO":   "_cod_processo",
            "DATA":       "data_hora",
            "DESCRICAO":  "titulo",
            "DTPRAZO":    "_dtprazo",
            "CODADVOG":   "_cod_advog",
            "CODEMPRESA": "_cod_empresa",
        },
    },

    # ── Custas ─────────────────────────────────

    "CUSTAS": {
        "destino": "custas",
        "campos": {
            "PROCESSO":   "_cod_processo",
            "DATA":       "data",
            "DESCRICAO":  "descricao",
            "VALOR":      "valor",
            "DEBITO":     "_debito",
            "CODEMPRESA": "_cod_empresa",
        },
    },
}

# ══════════════════════════════════════════════
# FUNÇÕES AUXILIARES
# ══════════════════════════════════════════════

def _risco_cor(descricao):
    """Retorna dicionário com cor baseada na descrição do risco."""
    d = (descricao or "").lower()
    if "baixo"    in d or "improvável" in d or "improvavel" in d:
        return {"descricao": descricao, "cor_hex": "#16a34a"}
    if "médio"    in d or "medio"    in d:
        return {"descricao": descricao, "cor_hex": "#d97706"}
    if "alto"     in d or "elevado"  in d:
        return {"descricao": descricao, "cor_hex": "#dc2626"}
    return {"descricao": descricao, "cor_hex": "#64748b"}

def limpar_cpf(valor):
    if not valor:
        return None
    v = re.sub(r'[^\d/.-]', '', str(valor)).strip()
    return v[:18] if v else None

def limpar_telefone(valor):
    if not valor:
        return None
    v = re.sub(r'[^\d\s()\-+]', '', str(valor)).strip()
    return v[:20] if v else None

def converter_data(valor):
    if not valor:
        return None
    if isinstance(valor, (datetime, date)):
        return valor.date() if isinstance(valor, datetime) else valor
    s = str(valor).strip()
    for fmt in ("%d/%m/%Y", "%Y-%m-%d", "%m/%d/%Y", "%d-%m-%Y"):
        try:
            return datetime.strptime(s, fmt).date()
        except ValueError:
            continue
    return None

def converter_valor(valor):
    if not valor:
        return Decimal("0.00")
    try:
        s = str(valor).replace("R$", "").replace(" ", "").replace(".", "").replace(",", ".")
        return Decimal(s).quantize(Decimal("0.01"))
    except (InvalidOperation, ValueError):
        return Decimal("0.00")

def mapear_status(cod_situa):
    mapa = {
        "1": "Ativo", "A": "Ativo",
        "2": "Arquivado", "ARQ": "Arquivado",
        "3": "Encerrado", "ENC": "Encerrado",
        "4": "Suspenso",
    }
    return mapa.get(str(cod_situa or "").upper(), "Ativo")

def sanitizar_texto(valor, max_len=None):
    if valor is None:
        return None
    s = str(valor).strip()
    if not s:
        return None
    if max_len:
        s = s[:max_len]
    return s

# ══════════════════════════════════════════════
# CONEXÕES
# ══════════════════════════════════════════════

def conectar_access(path):
    conn_str = (
        r"Driver={Microsoft Access Driver (*.mdb, *.accdb)};"
        f"DBQ={path};"
    )
    try:
        conn = pyodbc.connect(conn_str)
        print(f"✅ Access conectado: {path}")
        return conn
    except pyodbc.Error as e:
        print(f"❌ Erro ao conectar ao Access: {e}")
        print("\n💡 Verifique se instalou o Access Database Engine 64-bit:")
        print("   https://www.microsoft.com/en-us/download/details.aspx?id=54920")
        sys.exit(1)

def conectar_postgres():
    try:
        conn = psycopg2.connect(**PG_CONFIG)
        print(f"✅ PostgreSQL conectado: {PG_CONFIG['dbname']}@{PG_CONFIG['host']}")
        return conn
    except psycopg2.Error as e:
        print(f"❌ Erro ao conectar ao PostgreSQL: {e}")
        sys.exit(1)

# ══════════════════════════════════════════════
# MIGRADOR PRINCIPAL
# ══════════════════════════════════════════════

class Migrador:
    def __init__(self, access_path, pg_config, dry_run=False):
        self.dry_run = dry_run
        self.acc = conectar_access(access_path)
        self.pg  = conectar_postgres() if not dry_run else None

        # Mapas de IDs: {tabela_origem: {codigo_access: id_postgres}}
        self.id_map = {
            "advogados":   {},  # ADVOG.CODIGO → pessoas.id
            "juizes":      {},  # JUIZES.CODIGO → pessoas.id
            "clientes":    {},  # EMPRESAS.CODIGO → pessoas.id
            "partes":      {},  # PCONTRARIA.CODIGO → pessoas.id
            "processos":   {},  # PROCESSO.PROCESSO → processos.id
            "assuntos":    {},  # ASSUNTO.CODIGO → assuntos.id
            "acoes":       {},  # ACAO.CODIGO → tipos_acao.id
            "reparticoes": {},  # REPART.CODIGO → reparticoes.id
            "secretarias": {},  # SECRET.CODIGO → secretarias.id
            "riscos":      {},  # RISCO.CODIGO → graus_risco.id
            "tipos":       {},  # TIPO.CODIGO → tipos_processo.id
            "situacoes":   {},  # SITUA → status mapeado inline
        }

        self.stats = {
            "pessoas":     {"ok": 0, "erro": 0, "skip": 0},
            "processos":   {"ok": 0, "erro": 0, "skip": 0},
            "andamentos":  {"ok": 0, "erro": 0, "skip": 0},
            "agenda":      {"ok": 0, "erro": 0, "skip": 0},
            "custas":      {"ok": 0, "erro": 0, "skip": 0},
            "dominio":     {"ok": 0, "erro": 0, "skip": 0},
        }

    def executar(self):
        print("\n" + "═"*60)
        print("  SAPRO — Migração Access → PostgreSQL")
        if self.dry_run:
            print("  MODO DRY-RUN (nenhum dado será gravado)")
        print("═"*60 + "\n")

        self._migrar_dominio()
        self._migrar_advogados()
        self._migrar_juizes()
        self._migrar_clientes()
        self._migrar_partes_contrarias()
        self._migrar_processos()
        self._migrar_andamentos()
        self._migrar_agenda()
        self._migrar_custas()

        self._exibir_resumo()

        if self.pg:
            self.pg.commit()
            self.pg.close()
        self.acc.close()

    # ── Tabelas de domínio ─────────────────────

    def _migrar_dominio(self):
        print("📋 Migrando tabelas de domínio...")

        self._migrar_tabela_simples(
            "ACAO", "tipos_acao", "acoes",
            lambda r: {"codigo": sanitizar_texto(r.CODIGO, 10) or f"A{r.CODIGO}",
                       "descricao": sanitizar_texto(r.DESCRICAO, 100) or "Sem descrição",
                       "ativo": True}
        )

        self._migrar_tabela_simples(
            "ASSUNTO", "assuntos", "assuntos",
            lambda r: {"codigo": sanitizar_texto(r.CODIGO, 10) or f"S{r.CODIGO}",
                       "descricao": sanitizar_texto(r.ASSUNTO, 100) or "Sem descrição",
                       "ativo": True}
        )

        self._migrar_tabela_simples(
            "REPART", "reparticoes", "reparticoes",
            lambda r: {"codigo": sanitizar_texto(r.CODIGO, 10) or f"R{r.CODIGO}",
                       "descricao": sanitizar_texto(r.DESCRICAO, 100) or "Sem descrição",
                       "ativo": True}
        )

        self._migrar_tabela_simples(
            "SECRET", "secretarias", "secretarias",
            lambda r: {"codigo": sanitizar_texto(r.CODIGO, 10) or f"SC{r.CODIGO}",
                       "descricao": sanitizar_texto(r.DESCRICAO, 100) or "Sem descrição",
                       "ativo": True}
        )

        self._migrar_tabela_simples(
            "TIPO", "tipos_processo", "tipos",
            lambda r: {"codigo": sanitizar_texto(getattr(r, 'CODIGO', None), 10) or f"T{getattr(r,'CODIGO','')}",
                       "descricao": sanitizar_texto(getattr(r, 'Desc', None), 100) or "Sem descrição",
                       "ativo": True}
        )

        # RISCO com mapeamento de cor
        self._migrar_riscos()

        print(f"   ✅ Domínio: {self.stats['dominio']['ok']} registros\n")

    def _migrar_riscos(self):
        cursor = self.acc.cursor()
        try:
            cursor.execute("SELECT * FROM RISCO")
            rows = cursor.fetchall()
            cols = [c[0] for c in cursor.description]
        except Exception as e:
            print(f"   ⚠️  RISCO: {e}")
            return

        for row in rows:
            r = dict(zip(cols, row))
            codigo = sanitizar_texto(r.get('CODIGO'), 10) or "R"
            desc   = sanitizar_texto(r.get('Desc'), 50)   or "Sem descrição"
            info   = _risco_cor(desc)
            cor    = info.get("cor_hex", "#64748b") if isinstance(info, dict) else "#64748b"

            if self.dry_run:
                print(f"   [DRY] RISCO → graus_risco: {codigo} / {desc}")
                self.id_map["riscos"][r.get('CODIGO')] = 0
                continue

            try:
                with self.pg.cursor() as c:
                    c.execute("""
                        INSERT INTO graus_risco (codigo, descricao, cor_hex)
                        VALUES (%s, %s, %s)
                        ON CONFLICT (codigo) DO UPDATE SET descricao=EXCLUDED.descricao
                        RETURNING id
                    """, (codigo, desc, cor))
                    novo_id = c.fetchone()[0]
                    self.id_map["riscos"][r.get('CODIGO')] = novo_id
                    self.stats["dominio"]["ok"] += 1
            except Exception as e:
                self.stats["dominio"]["erro"] += 1
                print(f"   ⚠️  RISCO {codigo}: {e}")

    def _migrar_tabela_simples(self, tabela_acc, tabela_pg, mapa_key, transform_fn):
        cursor = self.acc.cursor()
        try:
            cursor.execute(f"SELECT * FROM {tabela_acc}")
            rows   = cursor.fetchall()
            cols   = [c[0] for c in cursor.description]
        except Exception as e:
            print(f"   ⚠️  {tabela_acc}: {e}")
            return

        for row in rows:
            r   = dict(zip(cols, row))
            cod = r.get('CODIGO') or r.get('Desc') or r.get('ASSUNTO')
            try:
                dados = transform_fn(type('R', (), r)())
            except Exception as e:
                self.stats["dominio"]["erro"] += 1
                continue

            if self.dry_run:
                self.id_map[mapa_key][cod] = 0
                self.stats["dominio"]["ok"] += 1
                continue

            try:
                with self.pg.cursor() as c:
                    cols_pg   = list(dados.keys())
                    vals      = list(dados.values())
                    placeholders = ", ".join(["%s"] * len(vals))
                    col_names    = ", ".join(cols_pg)
                    c.execute(f"""
                        INSERT INTO {tabela_pg} ({col_names})
                        VALUES ({placeholders})
                        ON CONFLICT (codigo) DO UPDATE SET descricao=EXCLUDED.descricao
                        RETURNING id
                    """, vals)
                    novo_id = c.fetchone()[0]
                    self.id_map[mapa_key][cod] = novo_id
                    self.stats["dominio"]["ok"] += 1
            except Exception as e:
                self.stats["dominio"]["erro"] += 1

    # ── Pessoas ────────────────────────────────

    def _inserir_pessoa(self, dados, tipo_pessoa, mapa_key, cod_origem):
        """Insere uma pessoa e registra o tipo. Retorna o ID."""
        nome = sanitizar_texto(dados.get("nome"), 150)
        if not nome:
            self.stats["pessoas"]["skip"] += 1
            return None

        if self.dry_run:
            self.id_map[mapa_key][cod_origem] = 0
            self.stats["pessoas"]["ok"] += 1
            return 0

        try:
            with self.pg.cursor() as c:
                cpf = limpar_cpf(dados.get("cpf_cnpj"))

                # Verifica se já existe por CPF
                if cpf:
                    c.execute("SELECT id FROM pessoas WHERE cpf_cnpj = %s", (cpf,))
                    row = c.fetchone()
                    if row:
                        pessoa_id = row[0]
                        # Adiciona novo tipo se não existir
                        c.execute("""
                            INSERT INTO pessoa_tipos (pessoa_id, tipo)
                            VALUES (%s, %s) ON CONFLICT DO NOTHING
                        """, (pessoa_id, tipo_pessoa))
                        self.id_map[mapa_key][cod_origem] = pessoa_id
                        self.stats["pessoas"]["skip"] += 1
                        return pessoa_id

                obs_partes = []
                if dados.get("nome_fantasia"):
                    obs_partes.append(f"Nome fantasia: {dados['nome_fantasia']}")
                if dados.get("observacoes"):
                    obs_partes.append(dados["observacoes"])

                c.execute("""
                    INSERT INTO pessoas (nome, cpf_cnpj, telefone, celular, email,
                                        logradouro, cidade, observacoes, ativo)
                    VALUES (%s,%s,%s,%s,%s,%s,%s,%s,true)
                    RETURNING id
                """, (
                    nome,
                    cpf,
                    limpar_telefone(dados.get("telefone")),
                    limpar_telefone(dados.get("celular")),
                    sanitizar_texto(dados.get("email"), 150),
                    sanitizar_texto(dados.get("logradouro"), 200),
                    sanitizar_texto(dados.get("cidade"), 100),
                    "\n".join(obs_partes)[:500] if obs_partes else None,
                ))
                pessoa_id = c.fetchone()[0]

                c.execute("""
                    INSERT INTO pessoa_tipos (pessoa_id, tipo)
                    VALUES (%s, %s) ON CONFLICT DO NOTHING
                """, (pessoa_id, tipo_pessoa))

                self.id_map[mapa_key][cod_origem] = pessoa_id
                self.stats["pessoas"]["ok"] += 1
                return pessoa_id

        except Exception as e:
            self.stats["pessoas"]["erro"] += 1
            print(f"   ⚠️  Pessoa {nome}: {e}")
            return None

    def _migrar_advogados(self):
        print("👨‍⚖️  Migrando advogados...")
        cursor = self.acc.cursor()
        cursor.execute("SELECT CODIGO, NOME FROM ADVOG")
        rows = cursor.fetchall()
        for cod, nome in tqdm(rows, desc="   ADVOG"):
            self._inserir_pessoa({"nome": nome}, "Advogado", "advogados", cod)
        print(f"   ✅ {self.stats['pessoas']['ok']} pessoas até agora\n")

    def _migrar_juizes(self):
        print("⚖️  Migrando juízes...")
        cursor = self.acc.cursor()
        try:
            cursor.execute("SELECT * FROM JUIZES")
            rows = cursor.fetchall()
            cols = [c[0] for c in cursor.description]
        except:
            return
        for row in tqdm(rows, desc="   JUIZES"):
            r = dict(zip(cols, row))
            cod  = r.get('CODIGO') or r.get('ID DO JUIZ')
            nome = r.get('NOME')
            self._inserir_pessoa({"nome": nome}, "Juiz", "juizes", cod)

    def _migrar_clientes(self):
        print("👤  Migrando clientes (EMPRESAS)...")
        cursor = self.acc.cursor()
        cursor.execute("SELECT * FROM EMPRESAS")
        rows = cursor.fetchall()
        cols = [c[0] for c in cursor.description]
        for row in tqdm(rows, desc="   EMPRESAS"):
            r = dict(zip(cols, row))
            dados = {
                "nome":         r.get("RAZAO SOCIAL") or r.get("NM FANTASIA"),
                "nome_fantasia":r.get("NM FANTASIA"),
                "cpf_cnpj":     r.get("CNPJ/CPF"),
                "telefone":     r.get("FONE1"),
                "celular":      r.get("FONE2"),
                "email":        r.get("EMAIL"),
                "logradouro":   r.get("ENDERECO"),
                "cidade":       r.get("CIDADE"),
                "observacoes":  r.get("OBSERVACAO"),
            }
            self._inserir_pessoa(dados, "Cliente", "clientes", r.get("CODIGO"))

    def _migrar_partes_contrarias(self):
        print("⚡  Migrando partes contrárias...")
        cursor = self.acc.cursor()
        cursor.execute("SELECT * FROM PCONTRARIA")
        rows = cursor.fetchall()
        cols = [c[0] for c in cursor.description]
        for row in tqdm(rows, desc="   PCONTRARIA"):
            r = dict(zip(cols, row))
            dados = {
                "nome":       r.get("RAZAO SOCIAL") or r.get("NM FANTASIA"),
                "cpf_cnpj":   r.get("CNPJ/CPF"),
                "telefone":   r.get("FONE1"),
                "email":      r.get("EMAIL"),
                "logradouro": r.get("ENDERECO"),
                "cidade":     r.get("CIDADE"),
                "observacoes":r.get("OBSERVACAO"),
            }
            self._inserir_pessoa(dados, "Parte Contrária", "partes", r.get("CODIGO"))
        print(f"   ✅ {self.stats['pessoas']['ok']} pessoas migradas\n")

    # ── Processos ──────────────────────────────

    def _migrar_processos(self):
        print("📁  Migrando processos...")
        cursor = self.acc.cursor()
        cursor.execute("SELECT * FROM PROCESSO")
        rows = cursor.fetchall()
        cols = [c[0] for c in cursor.description]

        for row in tqdm(rows, desc="   PROCESSO"):
            r = dict(zip(cols, row))

            numero = sanitizar_texto(r.get("PROCESSO"), 30)
            if not numero:
                self.stats["processos"]["skip"] += 1
                continue

            # Resolve chaves estrangeiras
            cod_cliente  = r.get("TPCLIENTE") or r.get("CODEMPRESA")
            cod_advog    = r.get("CODADVOG")
            cod_juiz     = r.get("JUIZID")
            cod_assunto  = r.get("CODASSUNTO")
            cod_acao     = r.get("CODACAO")
            cod_repart   = r.get("CODREPART")
            cod_secret   = r.get("CODSECRET")
            cod_risco    = r.get("RISCO")
            cod_tipo     = r.get("TIPO")
            arquivado    = str(r.get("ARQUIVADO") or "").upper() in ("S","SIM","1","TRUE","T")

            cliente_id   = self.id_map["clientes"].get(cod_cliente)
            if not cliente_id:
                # Tenta também no mapa de partes
                cliente_id = self.id_map["partes"].get(cod_cliente)
            if not cliente_id and not self.dry_run:
                self.stats["processos"]["skip"] += 1
                continue

            status = "Arquivado" if arquivado else mapear_status(r.get("CODSITUA"))

            dados = {
                "numero":           numero,
                "data_distribuicao":converter_data(r.get("DTDIST")),
                "cliente_id":       cliente_id or 1,
                "parte_contraria":  sanitizar_texto(r.get("CONTRARIA"), 150),
                "advogado_id":      self.id_map["advogados"].get(cod_advog),
                "juiz_id":          self.id_map["juizes"].get(cod_juiz),
                "tipo_acao_id":     self.id_map["acoes"].get(cod_acao),
                "tipo_processo_id": self.id_map["tipos"].get(cod_tipo),
                "assunto_id":       self.id_map["assuntos"].get(cod_assunto),
                "risco_id":         self.id_map["riscos"].get(cod_risco),
                "secretaria_id":    self.id_map["secretarias"].get(cod_secret),
                "reparticao_id":    self.id_map["reparticoes"].get(cod_repart),
                "valor_causa":      converter_valor(r.get("VALOR")),
                "status":           status,
            }

            if self.dry_run:
                self.id_map["processos"][numero] = 0
                self.stats["processos"]["ok"] += 1
                continue

            try:
                with self.pg.cursor() as c:
                    c.execute("""
                        INSERT INTO processos (numero, data_distribuicao, cliente_id,
                            parte_contraria, advogado_id, juiz_id, tipo_acao_id,
                            tipo_processo_id, assunto_id, risco_id, secretaria_id,
                            reparticao_id, valor_causa, status)
                        VALUES (%(numero)s, %(data_distribuicao)s, %(cliente_id)s,
                            %(parte_contraria)s, %(advogado_id)s, %(juiz_id)s,
                            %(tipo_acao_id)s, %(tipo_processo_id)s, %(assunto_id)s,
                            %(risco_id)s, %(secretaria_id)s, %(reparticao_id)s,
                            %(valor_causa)s, %(status)s)
                        ON CONFLICT (numero) DO NOTHING
                        RETURNING id
                    """, dados)
                    row_pg = c.fetchone()
                    if row_pg:
                        self.id_map["processos"][numero] = row_pg[0]
                        self.stats["processos"]["ok"] += 1
                    else:
                        self.stats["processos"]["skip"] += 1
            except Exception as e:
                self.stats["processos"]["erro"] += 1
                print(f"\n   ⚠️  Processo {numero}: {e}")

        print(f"   ✅ {self.stats['processos']['ok']} processos migrados\n")

    # ── Andamentos ─────────────────────────────

    def _migrar_andamentos(self):
        print("📝  Migrando andamentos...")
        cursor = self.acc.cursor()
        cursor.execute("SELECT * FROM ANDAMENTO ORDER BY PROCESSO, DATA")
        rows = cursor.fetchall()
        cols = [c[0] for c in cursor.description]

        for row in tqdm(rows, desc="   ANDAMENTO"):
            r = dict(zip(cols, row))
            num_proc   = sanitizar_texto(r.get("PROCESSO"))
            processo_id = self.id_map["processos"].get(num_proc)
            if not processo_id:
                self.stats["andamentos"]["skip"] += 1
                continue

            descricao = sanitizar_texto(r.get("DESCRICAO"))
            if not descricao:
                self.stats["andamentos"]["skip"] += 1
                continue

            data = converter_data(r.get("DATA")) or date.today()

            if self.dry_run:
                self.stats["andamentos"]["ok"] += 1
                continue

            try:
                with self.pg.cursor() as c:
                    c.execute("""
                        INSERT INTO andamentos (processo_id, data, descricao)
                        VALUES (%s, %s, %s)
                    """, (processo_id, data, descricao))
                    self.stats["andamentos"]["ok"] += 1
            except Exception as e:
                self.stats["andamentos"]["erro"] += 1

        print(f"   ✅ {self.stats['andamentos']['ok']} andamentos migrados\n")

    # ── Agenda ─────────────────────────────────

    def _migrar_agenda(self):
        print("📅  Migrando agenda...")
        cursor = self.acc.cursor()
        cursor.execute("SELECT * FROM AGENDA ORDER BY PROCESSO, DATA")
        rows = cursor.fetchall()
        cols = [c[0] for c in cursor.description]

        for row in tqdm(rows, desc="   AGENDA"):
            r = dict(zip(cols, row))
            num_proc    = sanitizar_texto(r.get("PROCESSO"))
            processo_id = self.id_map["processos"].get(num_proc)

            titulo = sanitizar_texto(r.get("DESCRICAO"), 200) or "Compromisso importado"
            data   = converter_data(r.get("DATA"))
            if not data:
                self.stats["agenda"]["skip"] += 1
                continue

            data_hora = datetime.combine(data, datetime.min.time())

            # Detecta tipo pelo texto
            titulo_lower = titulo.lower()
            if "audiência" in titulo_lower or "audiencia" in titulo_lower:
                tipo = "Audiência"
            elif "prazo" in titulo_lower:
                tipo = "Prazo"
            elif "reunião" in titulo_lower or "reuniao" in titulo_lower:
                tipo = "Reunião"
            else:
                tipo = "Outros"

            if self.dry_run:
                self.stats["agenda"]["ok"] += 1
                continue

            try:
                with self.pg.cursor() as c:
                    c.execute("""
                        INSERT INTO agenda (titulo, data_hora, tipo, processo_id)
                        VALUES (%s, %s, %s, %s)
                    """, (titulo, data_hora, tipo, processo_id))
                    self.stats["agenda"]["ok"] += 1
            except Exception as e:
                self.stats["agenda"]["erro"] += 1

        print(f"   ✅ {self.stats['agenda']['ok']} compromissos migrados\n")

    # ── Custas ─────────────────────────────────

    def _migrar_custas(self):
        print("💰  Migrando custas...")
        cursor = self.acc.cursor()
        cursor.execute("SELECT * FROM CUSTAS ORDER BY PROCESSO, DATA")
        rows = cursor.fetchall()
        cols = [c[0] for c in cursor.description]

        for row in tqdm(rows, desc="   CUSTAS"):
            r = dict(zip(cols, row))
            num_proc    = sanitizar_texto(r.get("PROCESSO"))
            processo_id = self.id_map["processos"].get(num_proc)
            if not processo_id:
                self.stats["custas"]["skip"] += 1
                continue

            descricao = sanitizar_texto(r.get("DESCRICAO"), 200) or "Custa importada"
            data      = converter_data(r.get("DATA")) or date.today()
            valor     = converter_valor(r.get("VALOR"))
            pago      = str(r.get("DEBITO") or "").upper() in ("P","PAGO","S","SIM","1")

            if self.dry_run:
                self.stats["custas"]["ok"] += 1
                continue

            try:
                with self.pg.cursor() as c:
                    c.execute("""
                        INSERT INTO custas (processo_id, data, descricao, valor, pago)
                        VALUES (%s, %s, %s, %s, %s)
                    """, (processo_id, data, descricao, valor, pago))
                    self.stats["custas"]["ok"] += 1
            except Exception as e:
                self.stats["custas"]["erro"] += 1

        print(f"   ✅ {self.stats['custas']['ok']} custas migradas\n")

    # ── Resumo ─────────────────────────────────

    def _exibir_resumo(self):
        print("\n" + "═"*60)
        print("  RESUMO DA MIGRAÇÃO")
        print("═"*60)
        total_ok = sum(v["ok"] for v in self.stats.values())
        total_er = sum(v["erro"] for v in self.stats.values())
        total_sk = sum(v["skip"] for v in self.stats.values())

        for cat, s in self.stats.items():
            print(f"  {cat:15s}  ✅ {s['ok']:5d}  ⚠️  {s['erro']:4d}  ⏭️  {s['skip']:4d}")

        print("─"*60)
        print(f"  {'TOTAL':15s}  ✅ {total_ok:5d}  ⚠️  {total_er:4d}  ⏭️  {total_sk:4d}")
        print("═"*60)
        if self.dry_run:
            print("\n  ℹ️  MODO DRY-RUN — nenhum dado foi gravado.")
        else:
            print("\n  ✅ Migração concluída! Dados gravados no PostgreSQL.")
        print()


# ══════════════════════════════════════════════
# ENTRY POINT
# ══════════════════════════════════════════════

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="SAPRO — Migração Access → PostgreSQL")
    parser.add_argument("--dry-run",  action="store_true", help="Simula sem gravar")
    parser.add_argument("--tabela",   help="Migra apenas uma tabela específica")
    parser.add_argument("--access",   default=ACCESS_FILE,    help="Caminho do .mdb")
    parser.add_argument("--pg-host",  default=PG_CONFIG["host"])
    parser.add_argument("--pg-db",    default=PG_CONFIG["dbname"])
    parser.add_argument("--pg-user",  default=PG_CONFIG["user"])
    parser.add_argument("--pg-pass",  default=PG_CONFIG["password"])
    args = parser.parse_args()

    PG_CONFIG.update({
        "host": args.pg_host,
        "dbname": args.pg_db,
        "user": args.pg_user,
        "password": args.pg_pass,
    })

    migrador = Migrador(args.access, PG_CONFIG, dry_run=args.dry_run)
    migrador.executar()
