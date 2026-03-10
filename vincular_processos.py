# -*- coding: utf-8 -*-
"""
Vincula processos aos clientes corretos no PostgreSQL (SAPRO)
Uso:
    python vincular_processos.py --dry-run
    python vincular_processos.py
"""

import csv
import sys
import psycopg2
from tqdm import tqdm

# --- CONFIGURACOES ---
PROCESSO_FILE = r"C:\projetos\saproweb-base\banco\processo.TXT"
EMPRESAS_FILE = r"C:\projetos\saproweb-base\banco\empresas.TXT"
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
    print("  SAPRO - Vinculacao Processos x Clientes")
    if DRY_RUN:
        print("  MODO DRY-RUN (nada sera salvo)")
    print("=" * 50)

    conn = conectar()
    cur = conn.cursor()

    # 1. Montar mapa CODIGO -> CNPJ a partir do CSV de empresas
    mapa_codigo_cnpj = {}
    with open(EMPRESAS_FILE, 'r', encoding=ENCODING, newline='') as f:
        for row in csv.DictReader(f):
            codigo = row.get('CODIGO', '').strip()
            cnpj   = row.get('CNPJ/CPF', '').strip()
            nome   = row.get('RAZAO SOCIAL', '').strip()
            if codigo:
                mapa_codigo_cnpj[codigo] = {'cnpj': cnpj, 'nome': nome}

    # 2. Montar mapa CNPJ -> pessoa_id a partir do banco
    cur.execute("SELECT id, cpf_cnpj, nome FROM pessoas WHERE cpf_cnpj IS NOT NULL AND cpf_cnpj != ''")
    mapa_cnpj_id = {row[1]: row[0] for row in cur.fetchall()}

    # Tambem por nome (fallback)
    cur.execute("SELECT id, nome FROM pessoas")
    mapa_nome_id = {row[1].strip().upper(): row[0] for row in cur.fetchall()}

    # 3. Ler processos do CSV
    with open(PROCESSO_FILE, 'r', encoding=ENCODING, newline='') as f:
        processos_csv = list(csv.DictReader(f))

    # 4. Montar mapa numero_processo -> cliente_id
    vinculados   = 0
    nao_encontrados = 0
    erros = 0

    for row in tqdm(processos_csv, desc="Vinculando"):
        numero     = row.get('PROCESSO', '').strip()
        codempresa = row.get('CODEMPRESA', '').strip()

        if not numero or not codempresa:
            nao_encontrados += 1
            continue

        # Buscar cliente pelo CODIGO -> CNPJ -> pessoa_id
        empresa = mapa_codigo_cnpj.get(codempresa)
        if not empresa:
            nao_encontrados += 1
            continue

        # Tentar pelo CNPJ primeiro
        pessoa_id = mapa_cnpj_id.get(empresa['cnpj'])

        # Fallback pelo nome
        if not pessoa_id:
            pessoa_id = mapa_nome_id.get(empresa['nome'].strip().upper())

        if not pessoa_id:
            nao_encontrados += 1
            continue

        try:
            if not DRY_RUN:
                cur.execute("""
                    UPDATE processos SET cliente_id = %s, updated_at = NOW()
                    WHERE numero = %s
                """, (pessoa_id, numero))
                conn.commit()
            vinculados += 1
        except Exception as e:
            conn.rollback()
            erros += 1
            print("  Erro no processo {}: {}".format(numero, e))

    print("\n  Vinculados:      {}".format(vinculados))
    print("  Nao encontrados: {}".format(nao_encontrados))
    print("  Erros:           {}".format(erros))

    conn.close()
    print("\n  Concluido!")
