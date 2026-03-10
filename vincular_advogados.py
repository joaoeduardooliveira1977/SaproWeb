# -*- coding: utf-8 -*-
"""
Vincula advogados aos processos no PostgreSQL (SAPRO)
Uso:
    python vincular_advogados.py --dry-run
    python vincular_advogados.py
"""

import csv
import sys
import psycopg2
from tqdm import tqdm

# --- CONFIGURACOES ---
ADVOG_FILE    = r"C:\projetos\saproweb-base\banco\advog.TXT"
ADVOGPRO_FILE = r"C:\projetos\saproweb-base\banco\advogpro.TXT"
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


if __name__ == "__main__":
    print("=" * 50)
    print("  SAPRO - Vinculacao Advogados x Processos")
    if DRY_RUN:
        print("  MODO DRY-RUN (nada sera salvo)")
    print("=" * 50)

    conn = conectar()
    cur = conn.cursor()

    # 1. Montar mapa CODIGO -> nome do advogado
    mapa_advog = {}
    with open(ADVOG_FILE, 'r', encoding=ENCODING, newline='') as f:
        for row in csv.DictReader(f):
            mapa_advog[row['CODIGO'].strip()] = row['NOME'].strip()

    print("Advogados encontrados:")
    for cod, nome in mapa_advog.items():
        print("  {} - {}".format(cod, nome))

    # 2. Buscar advogados no banco pelo nome
    mapa_nome_id = {}
    for cod, nome in mapa_advog.items():
        cur.execute("SELECT id FROM pessoas WHERE nome ILIKE %s LIMIT 1", ('%' + nome + '%',))
        row = cur.fetchone()
        if row:
            mapa_nome_id[cod] = row[0]
            print("  Encontrado no banco: {} -> ID {}".format(nome, row[0]))
        else:
            print("  NAO encontrado no banco: {}".format(nome))

    # 3. Ler vinculos do ADVOGPRO
    with open(ADVOGPRO_FILE, 'r', encoding=ENCODING, newline='') as f:
        vinculos = list(csv.DictReader(f))

    # 4. Buscar processos no banco
    cur.execute("SELECT id, numero FROM processos")
    mapa_processos = {row[1]: row[0] for row in cur.fetchall()}

    # 5. Vincular
    vinculados = 0
    ignorados  = 0
    erros      = 0

    for row in tqdm(vinculos, desc="Vinculando"):
        numero   = row.get('PROCESSO', '').strip()
        codadvog = row.get('CODADVOG', '').strip()

        processo_id = mapa_processos.get(numero)
        advogado_id = mapa_nome_id.get(codadvog)

        if not processo_id or not advogado_id:
            ignorados += 1
            continue

        try:
            if not DRY_RUN:
                cur.execute("""
                    UPDATE processos SET advogado_id = %s, updated_at = NOW()
                    WHERE id = %s
                """, (advogado_id, processo_id))
                conn.commit()
            vinculados += 1
        except Exception as e:
            conn.rollback()
            erros += 1
            print("  Erro: {}".format(e))

    print("\n  Vinculados:  {}".format(vinculados))
    print("  Ignorados:   {}".format(ignorados))
    print("  Erros:       {}".format(erros))

    conn.close()
    print("\n  Concluido!")
