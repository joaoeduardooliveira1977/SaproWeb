#!/bin/bash
DATA=$(date +%Y%m%d_%H%M%S)
DIR_BACKUP="/var/backups/sapro"
LOG="/var/backups/sapro/backup.log"
RETENCAO_DIAS=7

ENV_FILE="/var/www/saproweb/.env"
DB_HOST=$(grep '^DB_HOST='     $ENV_FILE | cut -d'=' -f2 | tr -d '\r')
DB_PORT=$(grep '^DB_PORT='     $ENV_FILE | cut -d'=' -f2 | tr -d '\r')
DB_NAME=$(grep '^DB_DATABASE=' $ENV_FILE | cut -d'=' -f2 | tr -d '\r')
DB_USER=$(grep '^DB_USERNAME=' $ENV_FILE | cut -d'=' -f2 | tr -d '\r')
DB_PASS=$(grep '^DB_PASSWORD=' $ENV_FILE | cut -d'=' -f2 | tr -d '\r')

ARQUIVO="${DIR_BACKUP}/sapro_${DATA}.sql.gz"

mkdir -p $DIR_BACKUP
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Iniciando backup..." >> $LOG

PGPASSWORD=$DB_PASS pg_dump \
    -h ${DB_HOST:-localhost} \
    -p ${DB_PORT:-5432} \
    -U $DB_USER \
    -d $DB_NAME \
    --format=plain \
    --no-password \
    | gzip > $ARQUIVO

if [ $? -eq 0 ] && [ -s $ARQUIVO ]; then
    TAMANHO=$(du -sh $ARQUIVO | cut -f1)
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] OK: $(basename $ARQUIVO) (${TAMANHO})" >> $LOG
    echo "SUCCESS:$(basename $ARQUIVO):${TAMANHO}"
else
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] ERRO: Falha ao criar backup!" >> $LOG
    rm -f $ARQUIVO
    echo "ERROR:Falha ao criar backup"
    exit 1
fi

REMOVIDOS=$(find $DIR_BACKUP -name "sapro_*.sql.gz" -mtime +$RETENCAO_DIAS -delete -print | wc -l)
if [ $REMOVIDOS -gt 0 ]; then
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Removidos ${REMOVIDOS} backup(s) antigo(s)" >> $LOG
fi

echo "[$(date '+%Y-%m-%d %H:%M:%S')] Concluído." >> $LOG
echo "────────────────────────────────────────" >> $LOG
exit 0
