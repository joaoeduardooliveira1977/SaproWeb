# -*- coding: utf-8 -*-
"""
Importa processos e andamentos do CSV para o PostgreSQL (SAPRO)
Uso:
    python importar_dados.py --dry-run    (so mostra o que vai importar)
    python importar_dados.py              (importa de verdade)
"""

import csv
import sys
import psycopg2
from datetime import datetime
from tqdm import tqdm

# --- CONFIGURACOES ---
PROCESSO_FILE = r"C:\projetos\saproweb-base\banco\processo.TXT"
ANDAMENTO_FILE = r"C:\projetos\saproweb-base\banco\andamento.TXT"
ENCODING = "ISO-8859-1"

DB = {
    "host":     "127.0.0.1",
    "port":     5432,
    "dbname":   "sapro_db",
    "user":     "postgres",
    "password": "sapro2026",
}

DRY_RUN = "--dry-run" in sys.argv


def parse_date(s):
    s = s.strip()
    if not s:
        return None
    for fmt in ("%d/%m/%Y", "%Y-%m-%d"):
        try:
            return datetime.strptime(s, fmt).date()
        except Exception:
            pass
    return None


def parse_valor(s):
    s = str(s).strip().replace(',', '.')
    try:
        return float(s)
    except Exception:
        return 0.0


def conectar():
    try:
        conn = psycopg2.connect(**DB)
        print("Conectado ao PostgreSQL!")
        return conn
    except Exception as e:
        print("Erro ao conectar: {}".format(e))
        sys.exit(1)


def importar_processos(conn):
    print("\nImportando processos...")
    cur = conn.cursor()

    cur.execute("SELECT id FROM fases LIMIT 1")
    row = cur.fetchone()
    fase_default = row[0] if row else None

    cur.execute("SELECT id FROM graus_risco LIMIT 1")
    row = cur.fetchone()
    risco_default = row[0] if row else None

    cur.execute("SELECT id FROM pessoas WHERE ativo = true LIMIT 1")
    row = cur.fetchone()
    cliente_default = row[0] if row else None

    with open(PROCESSO_FILE, 'r', encoding=ENCODING, newline='') as f:
        reader = csv.DictReader(f)
        rows = list(reader)

    inseridos = 0
    ignorados = 0
    erros = 0

    for row in tqdm(rows, desc="Processos"):
        numero = row['PROCESSO'].strip()
        if not numero:
            ignorados += 1
            continue

        cur.execute("SELECT id FROM processos WHERE numero = %s", (numero,))
        if cur.fetchone():
            ignorados += 1
            continue

        parte_contraria = row.get('CONTRARIA', '').strip() or None
        data_dist = parse_date(row.get('DTDIST', ''))
        valor = parse_valor(row.get('VALOR', 0))
        obs = row.get('OBS', '').strip() or None
        arquivado = row.get('ARQUIVADO', 'False').strip()
        status = 'Arquivado' if arquivado == 'True' else 'Ativo'

        dados = {
            'numero':            numero,
            'cliente_id':        cliente_default,
            'parte_contraria':   parte_contraria,
            'data_distribuicao': data_dist,
            'valor_causa':       valor,
            'valor_risco':       0,
            'observacoes':       obs,
            'status':            status,
            'fase_id':           fase_default,
            'risco_id':          risco_default,
        }

        try:
            if not DRY_RUN:
                cur.execute("""
                    INSERT INTO processos
                        (numero, cliente_id, parte_contraria, data_distribuicao,
                         valor_causa, valor_risco, observacoes, status,
                         fase_id, risco_id, created_at, updated_at)
                    VALUES
                        (%(numero)s, %(cliente_id)s, %(parte_contraria)s,
                         %(data_distribuicao)s, %(valor_causa)s, %(valor_risco)s,
                         %(observacoes)s, %(status)s, %(fase_id)s, %(risco_id)s,
                         NOW(), NOW())
                """, dados)
            inseridos += 1
        except Exception as e:
            erros += 1
            print("  Erro no processo {}: {}".format(numero, e))
            conn.rollback()

    if not DRY_RUN:
        conn.commit()

    print("  Inseridos:  {}".format(inseridos))
    print("  Ignorados:  {} (ja existiam)".format(ignorados))
    print("  Erros:      {}".format(erros))
    return inseridos


def importar_andamentos(conn):
    print("\nImportando andamentos...")
    cur = conn.cursor()

    # Buscar processos ja existentes no banco
    cur.execute("SELECT id, numero FROM processos")
    mapa_processos = {row[1]: row[0] for row in cur.fetchall()}

    # Em dry-run o banco pode estar vazio - usar CSV para contar
    numeros_csv = set()
    with open(PROCESSO_FILE, 'r', encoding=ENCODING, newline='') as f:
        reader = csv.DictReader(f)
        for row in reader:
            numeros_csv.add(row['PROCESSO'].strip())

    with open(ANDAMENTO_FILE, 'r', encoding=ENCODING, newline='') as f:
        reader = csv.DictReader(f)
        rows = list(reader)

    inseridos = 0
    ignorados = 0
    erros = 0
    sem_processo = set()

    for row in tqdm(rows, desc="Andamentos"):
        descricao = row.get('DESCRICAO', '').strip()
        data = parse_date(row.get('DATA', ''))
        numero = row['PROCESSO'].strip()

        if not descricao or not data:
            ignorados += 1
            continue

        if DRY_RUN:
            # Em dry-run apenas verifica se o numero existe no CSV de processos
            if numero not in numeros_csv:
                sem_processo.add(numero)
                ignorados += 1
            else:
                inseridos += 1
            continue

        processo_id = mapa_processos.get(numero)
        if not processo_id:
            sem_processo.add(numero)
            ignorados += 1
            continue

        try:
            cur.execute("""
                INSERT INTO andamentos
                    (processo_id, data, descricao, created_at, updated_at)
                VALUES (%s, %s, %s, NOW(), NOW())
            """, (processo_id, data, descricao))
            inseridos += 1
        except Exception as e:
            erros += 1
            conn.rollback()

    if not DRY_RUN:
        conn.commit()

    print("  Inseridos:  {}".format(inseridos))
    print("  Ignorados:  {}".format(ignorados))
    print("  Erros:      {}".format(erros))
    if sem_processo:
        print("  {} andamentos sem processo correspondente".format(len(sem_processo)))

    return inseridos


if __name__ == "__main__":
    print("=" * 50)
    print("  SAPRO - Importacao de Dados")
    if DRY_RUN:
        print("  MODO DRY-RUN (nada sera salvo)")
    print("=" * 50)

    conn = conectar()
    p = importar_processos(conn)
    a = importar_andamentos(conn)
    conn.close()

    print("\n" + "=" * 50)
    print("  Concluido!")
    print("  Processos:  {}".format(p))
    print("  Andamentos: {}".format(a))
    print("=" * 50)
