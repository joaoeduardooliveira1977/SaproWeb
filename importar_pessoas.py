# -*- coding: utf-8 -*-
"""
Importa Empresas (clientes), Advogados e Partes Contrarias
Uso:
    python importar_pessoas.py --dry-run
    python importar_pessoas.py
"""

import csv
import sys
import psycopg2
from tqdm import tqdm

# --- CONFIGURACOES ---
EMPRESAS_FILE   = r"C:\projetos\saproweb-base\banco\empresas.TXT"
ADVOG_FILE      = r"C:\projetos\saproweb-base\banco\advog.TXT"
PCONTRARIA_FILE = r"C:\projetos\saproweb-base\banco\pcontraria.TXT"
ENCODING = "ISO-8859-1"

DB = {
    "host":     "127.0.0.1",
    "port":     5432,
    "dbname":   "sapro_db",
    "user":     "postgres",
    "password": "sapro2026",
}

DRY_RUN = "--dry-run" in sys.argv


def conectar():
    try:
        conn = psycopg2.connect(**DB)
        print("Conectado ao PostgreSQL!")
        return conn
    except Exception as e:
        print("Erro ao conectar: {}".format(e))
        sys.exit(1)


def vincular_tipo(cur, pessoa_id, tipo):
    cur.execute("""
        INSERT INTO pessoa_tipos (pessoa_id, tipo)
        VALUES (%s, %s) ON CONFLICT DO NOTHING
    """, (pessoa_id, tipo))


def pessoa_existe(cur, cpf_cnpj, nome):
    if cpf_cnpj:
        cur.execute("SELECT id FROM pessoas WHERE cpf_cnpj = %s LIMIT 1", (cpf_cnpj,))
        if cur.fetchone():
            return True
    cur.execute("SELECT id FROM pessoas WHERE nome = %s LIMIT 1", (nome,))
    return cur.fetchone() is not None


def inserir_pessoa(cur, conn, dados, tipo):
    nome = dados.get('nome', '').strip()
    if not nome:
        return None

    cpf_cnpj = dados.get('cpf_cnpj', '').strip() or None

    if not DRY_RUN and pessoa_existe(cur, cpf_cnpj, nome):
        return None

    try:
        if not DRY_RUN:
            cur.execute("""
                INSERT INTO pessoas
                    (nome, cpf_cnpj, telefone, celular, email,
                     logradouro, cidade, estado, cep,
                     observacoes, ativo, created_at, updated_at)
                VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,true,NOW(),NOW())
                RETURNING id
            """, (
                nome,
                cpf_cnpj,
                dados.get('telefone', '') or '',
                dados.get('celular', '') or '',
                dados.get('email', '') or '',
                dados.get('logradouro', '') or '',
                dados.get('cidade', '') or '',
                dados.get('estado', '') or '',
                dados.get('cep', '') or '',
                dados.get('observacoes', '') or '',
            ))
            pessoa_id = cur.fetchone()[0]
            vincular_tipo(cur, pessoa_id, tipo)
            conn.commit()
        return 1
    except Exception as e:
        conn.rollback()
        print("  Erro ao inserir {}: {}".format(nome, e))
        return None


def importar_empresas(conn):
    print("\nImportando Empresas (clientes)...")
    cur = conn.cursor()
    inseridos = 0
    ignorados = 0

    with open(EMPRESAS_FILE, 'r', encoding=ENCODING, newline='') as f:
        rows = list(csv.DictReader(f))

    for row in tqdm(rows, desc="Empresas"):
        nome = (row.get('RAZAO SOCIAL') or row.get('NM FANTASIA') or '').strip()
        if not nome:
            ignorados += 1
            continue

        dados = {
            'nome':       nome,
            'cpf_cnpj':   row.get('CNPJ/CPF', '').strip(),
            'telefone':   row.get('FONE1', '').strip(),
            'celular':    row.get('FONE2', '').strip(),
            'email':      row.get('EMAIL', '').strip(),
            'logradouro': row.get('ENDERECO', '').strip(),
            'cidade':     row.get('CIDADE', '').strip(),
            'estado':     row.get('UF', '').strip(),
            'cep':        row.get('CEP', '').strip(),
            'observacoes':row.get('OBSERVACAO', '').strip(),
        }

        result = inserir_pessoa(cur, conn, dados, 'Cliente')
        if result is not None:
            inseridos += 1
        else:
            ignorados += 1

    print("  Inseridos:  {}".format(inseridos))
    print("  Ignorados:  {} (ja existiam)".format(ignorados))
    return inseridos


def importar_advogados(conn):
    print("\nImportando Advogados...")
    cur = conn.cursor()
    inseridos = 0
    ignorados = 0

    with open(ADVOG_FILE, 'r', encoding=ENCODING, newline='') as f:
        rows = list(csv.DictReader(f))

    for row in tqdm(rows, desc="Advogados"):
        nome = row.get('NOME', '').strip()
        if not nome:
            ignorados += 1
            continue

        if not DRY_RUN and pessoa_existe(cur, None, nome):
            ignorados += 1
            continue

        try:
            if not DRY_RUN:
                cur.execute("""
                    INSERT INTO pessoas
                        (nome, oab, ativo, created_at, updated_at)
                    VALUES (%s, %s, true, NOW(), NOW())
                    RETURNING id
                """, (nome, row.get('OAB', '').strip() or None))
                pessoa_id = cur.fetchone()[0]
                vincular_tipo(cur, pessoa_id, 'Advogado')
                conn.commit()
            inseridos += 1
        except Exception as e:
            conn.rollback()
            print("  Erro: {}".format(e))
            ignorados += 1

    print("  Inseridos:  {}".format(inseridos))
    print("  Ignorados:  {} (ja existiam)".format(ignorados))
    return inseridos


def importar_pcontraria(conn):
    print("\nImportando Partes Contrarias...")
    cur = conn.cursor()
    inseridos = 0
    ignorados = 0

    with open(PCONTRARIA_FILE, 'r', encoding=ENCODING, newline='') as f:
        rows = list(csv.DictReader(f))

    for row in tqdm(rows, desc="Partes Contrarias"):
        nome = (row.get('RAZAO SOCIAL') or row.get('NM FANTASIA') or '').strip()
        if not nome:
            ignorados += 1
            continue

        dados = {
            'nome':     nome,
            'cpf_cnpj': row.get('CNPJ/CPF', '').strip(),
            'telefone': row.get('FONE1', '').strip(),
            'email':    row.get('EMAIL', '').strip(),
            'cidade':   row.get('CIDADE', '').strip(),
            'estado':   row.get('UF', '').strip(),
        }

        result = inserir_pessoa(cur, conn, dados, 'Parte Contrária')
        if result is not None:
            inseridos += 1
        else:
            ignorados += 1

    print("  Inseridos:  {}".format(inseridos))
    print("  Ignorados:  {} (ja existiam)".format(ignorados))
    return inseridos


if __name__ == "__main__":
    print("=" * 50)
    print("  SAPRO - Importacao de Pessoas")
    if DRY_RUN:
        print("  MODO DRY-RUN (nada sera salvo)")
    print("=" * 50)

    conn = conectar()
    e = importar_empresas(conn)
    a = importar_advogados(conn)
    p = importar_pcontraria(conn)
    conn.close()

    print("\n" + "=" * 50)
    print("  Concluido!")
    print("  Empresas/Clientes: {}".format(e))
    print("  Advogados:         {}".format(a))
    print("  Partes Contrarias: {}".format(p))
    print("=" * 50)
