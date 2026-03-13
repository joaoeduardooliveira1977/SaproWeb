<?php

namespace App\Livewire;

use App\Models\AaspAdvogado;
use App\Models\AaspConfig;
use App\Models\AaspPublicacao;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class AaspPublicacoes extends Component
{
    // ── Aba ──────────────────────────────────────────────────
    public string $aba = 'publicacoes';

    // ── Busca ────────────────────────────────────────────────
    public string $dataBusca    = '';
    public string $filtroData   = '';
    public string $filtroAdvogado = '';
    public array  $logBusca     = [];

    // ── Modal Advogado ────────────────────────────────────────
    public bool    $modalAdvogado = false;
    public ?int    $advogadoId    = null;
    public string  $nomeAdv       = '';
    public string  $codigoAasp    = '';
    public string  $chaveAasp     = '';
    public string  $emailAdv      = '';
    public bool    $ativoAdv      = true;

    // ── Config ────────────────────────────────────────────────
    public string $emailsDestino  = '';
    public string $horarioRotina  = '08:00';
    public bool   $configAtiva    = true;

    // ── Confirmações ─────────────────────────────────────────
    public ?int $confirmarExcluirAdv = null;

    // ─────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->dataBusca  = now()->format('Y-m-d');
        $this->filtroData = now()->format('Y-m-d');
        $this->carregarConfig();
    }

    public function carregarConfig(): void
    {
        $config = AaspConfig::first();
        if ($config) {
            $this->emailsDestino = $config->emails_destino ?? '';
            $this->horarioRotina = $config->horario_rotina ?? '08:00';
            $this->configAtiva   = (bool) $config->ativo;
        }
    }

    // ══ Publicações ══════════════════════════════════════════

    public function buscarPublicacoes(): void
    {
        $this->logBusca = [];

        $advogados = AaspAdvogado::where('ativo', true)->get();

        if ($advogados->isEmpty()) {
            $this->logBusca[] = ['tipo' => 'erro', 'msg' => 'Nenhum advogado ativo cadastrado.'];
            return;
        }

        $total = 0;

        foreach ($advogados as $adv) {
            try {
                $dataFormatada = Carbon::parse($this->dataBusca)->format('d/m/Y');

                $response = Http::timeout(30)
                    ->withoutVerifying()
                    ->get('https://intimacaoapi.aasp.org.br/api/Associado/intimacao/json', [
                        'chave'       => $adv->chave_aasp,
                        'data'        => $dataFormatada,
                        'diferencial' => 'false',
                    ]);

                if ($response->successful()) {
                    $payload = $response->json();

                    // A API retorna { "intimacoes": [...] } ou array direto ou { "value": [...] }
                    if (isset($payload['intimacoes']) && is_array($payload['intimacoes'])) {
                        $publicacoes = $payload['intimacoes'];
                    } elseif (isset($payload['value']) && is_array($payload['value'])) {
                        $publicacoes = $payload['value'];
                    } elseif (isset($payload[0])) {
                        $publicacoes = $payload;
                    } else {
                        $this->logBusca[] = ['tipo' => 'aviso', 'msg' => "{$adv->nome}: resposta inesperada da API."];
                        continue;
                    }

                    $count = 0;
                    foreach ($publicacoes as $pub) {
                        $numPub = $pub['numeroPublicacao']
                            ?? $pub['numero_publicacao']
                            ?? $pub['NumeroPublicacao']
                            ?? null;

                        // Evita duplicatas
                        if ($numPub && AaspPublicacao::where('numero_publicacao', $numPub)->exists()) {
                            continue;
                        }

                        $dataPubl = $pub['data']
                            ?? $pub['dataPublicacao']
                            ?? $pub['Data']
                            ?? $this->dataBusca;

                        // Normaliza data para Y-m-d
                        try {
                            $dataPubl = Carbon::parse($dataPubl)->format('Y-m-d');
                        } catch (\Exception) {
                            $dataPubl = $this->dataBusca;
                        }

                        // jornal pode ser objeto { "nomeJornal": "..." } ou string
                        $jornalRaw = $pub['jornal'] ?? null;
                        if (is_array($jornalRaw)) {
                            $jornal = $jornalRaw['nomeJornal'] ?? $jornalRaw['nome'] ?? '';
                        } else {
                            $jornal = $jornalRaw ?? $pub['nomeJornal'] ?? $pub['Jornal'] ?? '';
                        }

                        AaspPublicacao::create([
                            'codigo_aasp'       => $adv->codigo_aasp,
                            'data'              => $dataPubl,
                            'jornal'            => $jornal,
                            'numero_processo'   => $pub['numeroUnicoProcesso'] ?? $pub['numeroProcesso'] ?? $pub['numero_processo'] ?? $pub['NumeroProcesso'] ?? '',
                            'titulo'            => $pub['titulo'] ?? $pub['Titulo'] ?? '',
                            'texto'             => $pub['textoPublicacao'] ?? $pub['texto'] ?? $pub['conteudo'] ?? $pub['Texto'] ?? '',
                            'numero_publicacao' => $numPub,
                        ]);
                        $count++;
                        $total++;
                    }

                    $this->logBusca[] = [
                        'tipo' => 'sucesso',
                        'msg'  => "{$adv->nome}: {$count} publicação(ões) novas encontradas.",
                    ];
                } else {
                    $this->logBusca[] = [
                        'tipo' => 'erro',
                        'msg'  => "{$adv->nome}: erro HTTP {$response->status()} da API.",
                    ];
                }
            } catch (\Exception $e) {
                $this->logBusca[] = [
                    'tipo' => 'erro',
                    'msg'  => "{$adv->nome}: " . $e->getMessage(),
                ];
            }
        }

        $this->logBusca[] = [
            'tipo' => 'info',
            'msg'  => "Busca concluída — {$total} nova(s) publicação(ões) salvas.",
        ];

        $this->filtroData = $this->dataBusca;
    }

    public function gerarPdf(): mixed
    {
        $publicacoes = $this->queryPublicacoes()->get();

        if ($publicacoes->isEmpty()) {
            session()->flash('erro', 'Nenhuma publicação para gerar o PDF.');
            return null;
        }

        $advogados   = AaspAdvogado::pluck('nome', 'codigo_aasp');
        $data        = $this->filtroData ?: now()->format('Y-m-d');
        $dataFmt     = Carbon::parse($data)->format('d/m/Y');
        $agrupadas   = $publicacoes->groupBy('codigo_aasp');
        $totalPubs   = $publicacoes->count();
        $totalAdvs   = $agrupadas->count();
        $geradoEm    = now()->format('d/m/Y \à\s H:i');

        $html  = "<!DOCTYPE html><html><head><meta charset='utf-8'>";
        $html .= "<style>
            @page { margin: 14mm; }
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9pt; color: #1e293b; background: #fff;
                   padding: 14mm; }

            /* ── Capa ── */
            .capa { text-align: center; padding: 50pt 0 36pt; border-bottom: 3pt solid #1a3a5c; margin-bottom: 30pt; }
            .capa-escritorio { font-size: 8pt; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 2.5pt; margin-bottom: 18pt; }
            .capa-logo   { font-size: 30pt; color: #1a3a5c; font-weight: bold; letter-spacing: 1pt; margin-bottom: 2pt; }
            .capa-sub    { font-size: 10pt; color: #64748b; letter-spacing: 1pt; text-transform: uppercase; margin-bottom: 28pt; }
            .capa-titulo { font-size: 17pt; color: #1a3a5c; font-weight: bold; margin-bottom: 8pt; }
            .capa-data   { font-size: 13pt; color: #334155; margin-bottom: 22pt; }
            .capa-stats  { background: #1a3a5c; color: #fff; padding: 10pt 28pt; font-size: 10pt; }
            .capa-gerado { font-size: 7.5pt; color: #94a3b8; margin-top: 18pt; }

            /* ── Seção advogado ── */
            .secao-adv {
                background: #1a3a5c;
                color: #fff;
                padding: 8pt 14pt;
                font-size: 12pt;
                font-weight: bold;
                margin-top: 24pt;
                margin-bottom: 0;
            }
            .secao-adv-count {
                background: #e8eef4;
                color: #1a3a5c;
                font-size: 8pt;
                padding: 4pt 14pt;
                border-left: 3pt solid #1a3a5c;
                margin-bottom: 12pt;
            }

            /* ── Publicação ── */
            .pub { margin-bottom: 0; page-break-inside: avoid; padding: 10pt 14pt; }
            .pub-numero { font-size: 7.5pt; font-weight: bold; color: #94a3b8; margin-bottom: 5pt; }
            .pub-campo { font-size: 8pt; color: #475569; margin-bottom: 2.5pt; line-height: 1.4; }
            .pub-campo strong { color: #1a3a5c; }
            .pub-processo { font-size: 9pt; font-weight: bold; color: #1a3a5c; font-family: DejaVu Sans Mono, monospace; margin-bottom: 5pt; }
            .pub-titulo-pub { font-size: 9pt; font-weight: bold; color: #1e293b; margin-bottom: 5pt; }
            .pub-texto { font-size: 8.5pt; color: #334155; text-align: left; line-height: 1.55; white-space: pre-wrap; padding-top: 6pt; border-top: 0.5pt solid #e2e8f0; }
            .pub-sep { border: none; border-top: 1pt solid #cbd5e1; margin: 10pt 14pt; }

            /* ── Rodapé ── */
            .rodape {
                margin-top: 28pt;
                padding-top: 8pt;
                border-top: 1.5pt solid #e2e8f0;
                text-align: center;
                font-size: 7.5pt;
                color: #94a3b8;
            }
        </style></head><body>";

        // ── Capa ──
        $html .= "<div class='capa'>";
        $html .= "<div class='capa-escritorio'>Escritório de Advocacia Ferreira</div>";
       // $html .= "<div class='capa-logo'>SAPRO</div>";
        $html .= "<div class='capa-sub'>Sistema Jurídico</div>";
        $html .= "<div class='capa-titulo'>Publicações AASP</div>";
        $html .= "<div class='capa-data'>{$dataFmt}</div>";
        $html .= "<div class='capa-stats'>{$totalPubs} publicação(ões) &nbsp;&nbsp;|&nbsp;&nbsp; {$totalAdvs} advogado(s)</div>";
        $html .= "<div class='capa-gerado'>Gerado em {$geradoEm}</div>";
        $html .= "</div>";

        // ── Publicações agrupadas por advogado ──
        foreach ($agrupadas as $codigo => $pubs) {
            $nomeAdv = $advogados[$codigo] ?? "Código {$codigo}";
            $qtd     = $pubs->count();

            $html .= "<div class='secao-adv'>" . htmlspecialchars($nomeAdv) . "</div>";
            $html .= "<div class='secao-adv-count'>{$qtd} publicação(ões)</div>";

            $seq    = 1;
            $total  = $pubs->count();
            foreach ($pubs as $pub) {
                $html .= "<div class='pub'>";
                $html .= "<div class='pub-numero'>Publicação #{$seq}</div>";

                if ($pub->numero_processo) {
                    $html .= "<div class='pub-processo'>Processo: " . htmlspecialchars($pub->numero_processo) . "</div>";
                }

                $html .= "<div class='pub-campo'><strong>Jornal:</strong> " . htmlspecialchars($pub->jornal ?? '—') . "</div>";
                $html .= "<div class='pub-campo'><strong>Data de disponibilização:</strong> " . ($pub->data ? $pub->data->format('d/m/Y') : '—') . "</div>";
                if ($pub->numero_publicacao) {
                    $html .= "<div class='pub-campo'><strong>Número da publicação:</strong> " . htmlspecialchars($pub->numero_publicacao) . "</div>";
                }

                if ($pub->titulo) {
                    $html .= "<div class='pub-titulo-pub'>" . htmlspecialchars($pub->titulo) . "</div>";
                }
                if ($pub->texto) {
                    $textoEscapado = htmlspecialchars($this->limparTexto($pub->texto));
                    $nomeEscapado  = htmlspecialchars($nomeAdv);
                    if ($nomeEscapado) {
                        $textoEscapado = str_ireplace(
                            $nomeEscapado,
                            "<strong>{$nomeEscapado}</strong>",
                            $textoEscapado
                        );
                    }
                    $html .= "<div class='pub-texto'>" . $textoEscapado . "</div>";
                }

                $html .= "</div>"; // .pub

                if ($seq < $total) {
                    $html .= "<hr class='pub-sep'>";
                }
                $seq++;
            }
        }

        // ── Rodapé ──
        $html .= "<div class='rodape'>Gerado pelo Sistema Jurídico SAPRO &nbsp;·&nbsp; {$geradoEm} &nbsp;·&nbsp; Publicações AASP referentes a {$dataFmt}</div>";

        $html .= "</body></html>";

        $nomeArquivo = 'publicacoes-aasp-' . Carbon::parse($data)->format('Y-m-d') . '.pdf';

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $nomeArquivo, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function enviarEmail(): void
    {
        $config = AaspConfig::first();

        if (!$config || empty($config->emails_destino)) {
            session()->flash('erro', 'Nenhum e-mail de destino configurado. Configure na aba Configurações.');
            return;
        }

        $publicacoes = $this->queryPublicacoes()->get();

        if ($publicacoes->isEmpty()) {
            session()->flash('erro', 'Nenhuma publicação para enviar.');
            return;
        }

        $emails    = $config->getEmailsArray();
        $advogados = AaspAdvogado::pluck('nome', 'codigo_aasp');
        $data      = $this->filtroData ?: now()->format('Y-m-d');
        $dataFmt   = Carbon::parse($data)->format('d/m/Y');
        $agrupadas = $publicacoes->groupBy('codigo_aasp');
        $totalPubs = $publicacoes->count();
        $totalAdvs = $agrupadas->count();
        $geradoEm  = now()->format('d/m/Y H:i');

        // ── Cabeçalho ──
        $corpo  = "
        <div style='font-family:Arial,Helvetica,sans-serif;max-width:680px;margin:0 auto;background:#f1f5f9;'>

        <div style='background:#1a3a5c;padding:28px 36px 24px;border-radius:8px 8px 0 0;'>
            <div style='color:#93c5fd;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;'>Sistema Jurídico SAPRO</div>
            <div style='color:#fff;font-size:24px;font-weight:700;line-height:1.2;'>Publicações AASP &mdash; {$dataFmt}</div>
        </div>

        <div style='background:#fff;border:1px solid #e2e8f0;border-top:none;padding:18px 36px;'>
            <table width='100%' cellpadding='0' cellspacing='0'>
                <tr>
                    <td style='text-align:center;padding:8px 0;'>
                        <div style='font-size:30px;font-weight:700;color:#1a3a5c;line-height:1;'>{$totalPubs}</div>
                        <div style='font-size:11px;color:#64748b;margin-top:4px;'>Publicações</div>
                    </td>
                    <td style='width:1px;background:#e2e8f0;'></td>
                    <td style='text-align:center;padding:8px 0;'>
                        <div style='font-size:30px;font-weight:700;color:#1a3a5c;line-height:1;'>{$totalAdvs}</div>
                        <div style='font-size:11px;color:#64748b;margin-top:4px;'>Advogado(s)</div>
                    </td>
                    <td style='width:1px;background:#e2e8f0;'></td>
                    <td style='text-align:center;padding:8px 16px;'>
                        <div style='font-size:10px;color:#94a3b8;margin-bottom:4px;'>Gerado em</div>
                        <div style='font-size:12px;font-weight:600;color:#334155;'>{$geradoEm}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div style='padding:24px 36px;'>";

        // ── Publicações por advogado ──
        foreach ($agrupadas as $codigo => $pubs) {
            $nomeAdv = $advogados[$codigo] ?? "Código {$codigo}";
            $qtd     = $pubs->count();

            $corpo .= "
            <div style='background:#e2e8f0;padding:10px 16px;border-radius:4px;margin-top:24px;margin-bottom:4px;'>
                <div style='font-size:14px;font-weight:700;color:#1e293b;'>" . htmlspecialchars($nomeAdv) . "</div>
                <div style='font-size:11px;color:#64748b;margin-top:2px;'>{$qtd} publicação(ões)</div>
            </div>";

            $seq   = 1;
            $total = $pubs->count();
            foreach ($pubs as $pub) {
                $corpo .= "
                <div style='background:#fff;border:1px solid #e2e8f0;border-left:4px solid #2563eb;border-radius:0 4px 4px 0;margin-bottom:0;overflow:hidden;'>

                    <div style='background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:8px 14px;'>
                        <span style='font-size:11px;font-weight:700;color:#94a3b8;margin-right:8px;'>#{$seq}</span>
                        <span style='font-size:12px;color:#475569;margin-right:12px;'>
                            <strong style='color:#1a3a5c;'>Jornal:</strong> " . htmlspecialchars($pub->jornal ?? '—') . "
                        </span>
                        <span style='font-size:12px;color:#475569;margin-right:12px;'>
                            <strong style='color:#1a3a5c;'>Data disp.:</strong> " . ($pub->data ? $pub->data->format('d/m/Y') : '—') . "
                        </span>";

                if ($pub->numero_publicacao) {
                    $corpo .= "
                        <span style='font-size:12px;color:#475569;'>
                            <strong style='color:#1a3a5c;'>Nº pub.:</strong> " . htmlspecialchars($pub->numero_publicacao) . "
                        </span>";
                }

                $corpo .= "
                    </div>

                    <div style='padding:12px 14px;'>";

                if ($pub->numero_processo) {
                    $corpo .= "
                        <div style='font-size:13px;font-weight:700;color:#1a3a5c;font-family:monospace;margin-bottom:8px;'>
                            Processo: " . htmlspecialchars($pub->numero_processo) . "
                        </div>";
                }

                if ($pub->titulo) {
                    $corpo .= "<div style='font-size:13px;font-weight:600;color:#1e293b;margin-bottom:8px;line-height:1.4;'>"
                              . htmlspecialchars($pub->titulo) . "</div>";
                }

                if ($pub->texto) {
                    $corpo .= "<div style='font-size:12px;color:#475569;line-height:1.65;border-top:1px solid #f1f5f9;padding-top:8px;'>"
                              . $this->limparTextoHtml($pub->texto) . "</div>";
                }

                $corpo .= "
                    </div>
                </div>";

                if ($seq < $total) {
                    $corpo .= "<div style='border-top:1px solid #cbd5e1;margin:0;'></div>";
                }

                $seq++;
            }
        }

        $corpo .= "
        </div>
        <div style='background:#e2e8f0;border:1px solid #cbd5e1;border-top:none;padding:14px 36px;border-radius:0 0 8px 8px;text-align:center;'>
            <span style='font-size:11px;color:#64748b;'>Gerado pelo Sistema Jurídico SAPRO &nbsp;·&nbsp; {$geradoEm} &nbsp;·&nbsp; Publicações AASP referentes a {$dataFmt}</span>
        </div>

        </div>";

        $erros = 0;
        foreach ($emails as $email) {
            try {
                Mail::html($corpo, function ($message) use ($email, $dataFmt) {
                    $message->to($email)
                            ->subject("Publicações AASP — {$dataFmt}");
                });
            } catch (\Exception $e) {
                $erros++;
            }
        }

        $enviados = count($emails) - $erros;
        if ($enviados > 0) {
            session()->flash('sucesso', "{$enviados} e-mail(s) enviado(s) com sucesso.");
        } else {
            session()->flash('erro', 'Falha ao enviar os e-mails. Verifique a configuração de e-mail do sistema.');
        }
    }

    // ══ Helpers ══════════════════════════════════════════════

    /**
     * Limpa o textoPublicacao antes de renderizar no PDF ou e-mail.
     *
     * - Normaliza quebras de linha (\r\n → \n)
     * - Preserva parágrafos (2+ \n consecutivos → marcador de parágrafo)
     * - Dentro de cada parágrafo: múltiplos espaços → espaço único
     * - Remove espaços antes de pontuação (: ; , .)
     * - Trim geral
     */
    private function limparTexto(string $texto): string
    {
        // Normalizar quebras de linha
        $texto = str_replace(["\r\n", "\r"], "\n", $texto);

        // Dividir em parágrafos (2 ou mais \n consecutivos)
        $paragrafos = preg_split('/\n{2,}/', $texto);

        $paragrafos = array_map(function (string $p): string {
            // Quebras simples dentro do parágrafo viram espaço
            $p = str_replace("\n", ' ', $p);
            // Múltiplos espaços/tabs → espaço único
            $p = preg_replace('/\s+/', ' ', $p);
            // Remover espaços antes de pontuação
            $p = preg_replace('/\s+([;:,\.])/', '$1', $p);
            return trim($p);
        }, $paragrafos);

        // Descartar parágrafos que ficaram vazios
        $paragrafos = array_values(array_filter($paragrafos, fn($p) => $p !== ''));

        return trim(implode("\n\n", $paragrafos));
    }

    /**
     * Versão para HTML (e-mail): converte \n\n em <br><br> e \n em <br>.
     */
    private function limparTextoHtml(string $texto): string
    {
        $limpo = $this->limparTexto($texto);
        // Escapa HTML antes de converter quebras
        $limpo = htmlspecialchars($limpo, ENT_QUOTES, 'UTF-8');
        // Parágrafo duplo → dois <br>
        $limpo = str_replace("\n\n", '<br><br>', $limpo);
        // Quebra simples residual → <br>
        return str_replace("\n", '<br>', $limpo);
    }

    // ══ Advogados CRUD ═══════════════════════════════════════

    public function novoAdvogado(): void
    {
        $this->resetAdvogadoForm();
        $this->modalAdvogado = true;
    }

    public function editarAdvogado(int $id): void
    {
        $adv = AaspAdvogado::findOrFail($id);
        $this->advogadoId = $adv->id;
        $this->nomeAdv    = $adv->nome;
        $this->codigoAasp = $adv->codigo_aasp;
        $this->chaveAasp  = $adv->chave_aasp;
        $this->emailAdv   = $adv->email ?? '';
        $this->ativoAdv   = (bool) $adv->ativo;
        $this->modalAdvogado = true;
    }

    public function salvarAdvogado(): void
    {
        $this->validate([
            'nomeAdv'   => 'required|min:3|max:150',
            'codigoAasp' => 'required|max:20',
            'chaveAasp'  => 'required|max:100',
            'emailAdv'   => 'nullable|email|max:150',
        ], [
            'nomeAdv.required'    => 'Informe o nome do advogado.',
            'nomeAdv.min'         => 'Nome muito curto.',
            'codigoAasp.required' => 'Informe o código AASP.',
            'chaveAasp.required'  => 'Informe a chave AASP.',
            'emailAdv.email'      => 'E-mail inválido.',
        ]);

        $dados = [
            'nome'       => trim($this->nomeAdv),
            'codigo_aasp' => trim($this->codigoAasp),
            'chave_aasp'  => trim($this->chaveAasp),
            'email'      => trim($this->emailAdv) ?: null,
            'ativo'      => $this->ativoAdv,
        ];

        if ($this->advogadoId) {
            AaspAdvogado::find($this->advogadoId)?->update($dados);
            session()->flash('sucesso', 'Advogado atualizado com sucesso.');
        } else {
            AaspAdvogado::create($dados);
            session()->flash('sucesso', 'Advogado cadastrado com sucesso.');
        }

        $this->modalAdvogado = false;
        $this->resetAdvogadoForm();
    }

    public function confirmarExcluirAdvogado(int $id): void
    {
        $this->confirmarExcluirAdv = $id;
    }

    public function excluirAdvogado(): void
    {
        if ($this->confirmarExcluirAdv) {
            AaspAdvogado::find($this->confirmarExcluirAdv)?->delete();
            session()->flash('sucesso', 'Advogado removido.');
            $this->confirmarExcluirAdv = null;
        }
    }

    public function fecharModal(): void
    {
        $this->modalAdvogado       = false;
        $this->confirmarExcluirAdv = null;
        $this->resetAdvogadoForm();
    }

    private function resetAdvogadoForm(): void
    {
        $this->advogadoId = null;
        $this->nomeAdv    = '';
        $this->codigoAasp = '';
        $this->chaveAasp  = '';
        $this->emailAdv   = '';
        $this->ativoAdv   = true;
        $this->resetValidation();
    }

    // ══ Configurações ════════════════════════════════════════

    public function salvarConfig(): void
    {
        $this->validate([
            'horarioRotina' => 'required|regex:/^\d{2}:\d{2}$/',
        ], [
            'horarioRotina.required' => 'Informe o horário da rotina.',
            'horarioRotina.regex'    => 'Formato inválido. Use HH:MM.',
        ]);

        AaspConfig::updateOrCreate(
            ['id' => 1],
            [
                'emails_destino' => trim($this->emailsDestino),
                'horario_rotina' => $this->horarioRotina,
                'ativo'          => $this->configAtiva,
            ]
        );

        session()->flash('sucesso', 'Configurações salvas com sucesso.');
    }

    // ══ Query ════════════════════════════════════════════════

    private function queryPublicacoes()
    {
        $q = AaspPublicacao::query()->orderBy('data', 'desc')->orderBy('id', 'desc');

        if ($this->filtroData) {
            $q->whereDate('data', $this->filtroData);
        }

        if ($this->filtroAdvogado) {
            $q->where('codigo_aasp', $this->filtroAdvogado);
        }

        return $q;
    }

    // ══ Render ═══════════════════════════════════════════════

    public function render(): \Illuminate\View\View
    {
        $publicacoes = $this->queryPublicacoes()->get();
        $advogados   = AaspAdvogado::orderBy('nome')->get();
        $totalDia    = AaspPublicacao::whereDate('data', $this->filtroData ?: now())->count();

        return view('livewire.aasp-publicacoes', compact('publicacoes', 'advogados', 'totalDia'));
    }
}
