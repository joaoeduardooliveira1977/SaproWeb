# Configuração DATAJUD Automático

O agendamento já está configurado em `routes/console.php`:
```
datajud:verificar → dias úteis às 06:00
```

---

## 1. Instalar Supervisor (se não tiver)
```bash
sudo apt-get install supervisor
```

## 2. Configurar o Queue Worker
```bash
sudo cp /var/www/saproweb/supervisor-worker.conf /etc/supervisor/conf.d/saproweb-worker.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start saproweb-worker:*
```

## 3. Configurar o Cron (Laravel Scheduler)
```bash
crontab -e
# Adicionar a linha:
* * * * * cd /var/www/saproweb && php artisan schedule:run >> /dev/null 2>&1
```

## 4. Verificar a fila de jobs
```bash
php artisan queue:table   # cria a migration se a tabela jobs não existir
php artisan migrate
```

## 5. Testar manualmente
```bash
# Roda normalmente (respeita verificação já em andamento):
php artisan datajud:consultar

# Força nova consulta mesmo com verificação em andamento:
php artisan datajud:consultar --force

# Consulta apenas processos específicos:
php artisan datajud:consultar --processos=1,2,3

# Comando original com dry-run:
php artisan datajud:verificar --dry-run
```

## 6. Verificar agendamentos configurados
```bash
php artisan schedule:list
```

## 7. Acompanhar logs
```bash
tail -f /var/www/saproweb/storage/logs/worker.log
tail -f /var/www/saproweb/storage/logs/laravel.log
```

## 8. Status do Supervisor
```bash
sudo supervisorctl status
sudo supervisorctl restart saproweb-worker:*
```

---

## Variáveis de ambiente necessárias (.env)

```env
QUEUE_CONNECTION=database   # já configurado
```

---

## Agendamentos ativos (routes/console.php)

| Comando | Frequência | Descrição |
|---------|-----------|-----------|
| `notificacoes:gerar` | Diário 07:00 | Notificações internas + e-mail resumo |
| `notificacoes:whatsapp --tipo=prazo` | Diário 07:15 | WhatsApp prazos |
| `notificacoes:whatsapp --tipo=audiencia` | Diário 07:15 | WhatsApp audiências |
| `notificacoes:whatsapp --tipo=cobranca` | Diário 08:00 | WhatsApp cobranças |
| `indices:atualizar` | Mensal dia 15 06:00 | IPCA, IGPM, SELIC, TR |
| `datajud:verificar` | Dias úteis 06:00 | Consulta DATAJUD/CNJ |
| `aasp:buscar` | Dias úteis 08:30 | Publicações AASP |
