<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    protected $signature   = 'backup:database';
    protected $description = 'Gera backup do banco PostgreSQL do SAPRO';

    public function handle(): int
    {
        $output = shell_exec('/var/www/saproweb/scripts/backup_db.sh 2>&1');

        if (str_contains($output ?? '', 'ERROR')) {
            Log::error('Backup falhou: ' . $output);
            $this->error('Backup falhou: ' . $output);
            return self::FAILURE;
        }

        Log::info('Backup executado com sucesso: ' . $output);
        $this->info('Backup concluído: ' . $output);
        return self::SUCCESS;
    }
}
