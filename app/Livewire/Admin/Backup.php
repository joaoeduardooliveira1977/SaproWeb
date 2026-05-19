<?php
namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Backup extends Component
{
    public bool   $executando   = false;
    public string $mensagem     = '';
    public string $tipoMensagem = '';
    public array  $backups      = [];
    public array  $logLinhas    = [];

    public function mount(): void
    {
        $this->carregarBackups();
    }

    public function executarBackup(): void
    {
        $perfil = auth('usuarios')->user()?->perfil;
        if (!in_array($perfil, ['admin', 'administrador', 'super_admin'])) {
            $this->mensagem     = 'Acesso negado.';
            $this->tipoMensagem = 'error';
            return;
        }

        $this->executando   = true;
        $this->mensagem     = '';
        $this->tipoMensagem = '';

        $output = shell_exec('/var/www/saproweb/scripts/backup_db.sh 2>&1');

        if (str_contains($output ?? '', 'SUCCESS')) {
            preg_match('/SUCCESS:([^:]+):([^\n]+)/', $output, $matches);
            $arquivo = $matches[1] ?? 'backup gerado';
            $tamanho = trim($matches[2] ?? '');
            $this->mensagem     = "Backup criado com sucesso: {$arquivo} ({$tamanho})";
            $this->tipoMensagem = 'success';
            Log::info('Backup manual executado por: ' . auth('usuarios')->user()?->login);
        } else {
            $this->mensagem     = 'Erro ao gerar backup. Verifique o log.';
            $this->tipoMensagem = 'error';
            Log::error('Backup manual falhou: ' . $output);
        }

        $this->executando = false;
        $this->carregarBackups();
    }

    public function carregarBackups(): void
    {
        $dir = '/var/backups/sapro';

        if (!is_dir($dir)) {
            $this->backups = [];
            return;
        }

        $arquivos = glob($dir . '/sapro_*.sql.gz') ?: [];
        rsort($arquivos);

        $this->backups = array_map(function ($arquivo) {
            return [
                'nome'    => basename($arquivo),
                'tamanho' => $this->formatarBytes(file_exists($arquivo) ? filesize($arquivo) : 0),
                'data'    => date('d/m/Y H:i', filemtime($arquivo)),
                'idade'   => \Carbon\Carbon::createFromTimestamp(filemtime($arquivo))->diffForHumans(),
            ];
        }, array_slice($arquivos, 0, 10));
    }

    public function carregarLog(): void
    {
        $logFile = '/var/backups/sapro/backup.log';
        if (!file_exists($logFile)) {
            $this->logLinhas = ['Log ainda não disponível.'];
            return;
        }

        $linhas          = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $this->logLinhas = array_slice(array_reverse($linhas), 0, 30);
    }

    private function formatarBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.backup')
            ->extends('layouts.app')
            ->section('content');
    }
}
