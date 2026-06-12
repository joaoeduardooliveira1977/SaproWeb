<?php

namespace App\Http\Controllers;

use App\Models\{AaspAdvogado, AaspPublicacao};
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;

class AaspWordController extends Controller
{
    public function download(Request $request)
    {
        $tenantId = tenant_id();

        $q = AaspPublicacao::where('tenant_id', $tenantId)
            ->orderBy('data', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filtro_data) {
            $q->whereDate('data', $request->filtro_data);
        }
        if ($request->filtro_advogado) {
            $q->where('codigo_aasp', $request->filtro_advogado);
        }
        if ($request->filtro_vinculo === 'vinculadas') {
            $q->whereNotNull('processo_id');
        } elseif ($request->filtro_vinculo === 'pendentes') {
            $q->whereNull('processo_id');
        }

        $publicacoes = $q->get();

        if ($publicacoes->isEmpty()) {
            abort(404, 'Nenhuma publicação encontrada com os filtros selecionados.');
        }

        $advogados = AaspAdvogado::where('tenant_id', $tenantId)->pluck('nome', 'codigo_aasp');
        $tenant    = auth('usuarios')->user()?->tenant;
        $data      = $request->filtro_data ?: now()->format('Y-m-d');
        $dataFmt   = Carbon::parse($data)->format('d/m/Y');
        $agrupadas = $publicacoes->groupBy('codigo_aasp');
        $totalPubs = $publicacoes->count();
        $totalAdvs = $agrupadas->count();
        $geradoEm  = now()->format('d/m/Y \à\s H:i');

        $diasSemana = ['domingo','segunda-feira','terça-feira','quarta-feira','quinta-feira','sexta-feira','sábado'];
        $meses      = ['janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro'];

        $phpWord = new PhpWord();

        // Estilos de parágrafo nomeados
        $phpWord->addParagraphStyle('hr', [
            'borderBottomSize'  => 4,
            'borderBottomColor' => '000000',
            'spaceAfter'        => 120,
            'spaceBefore'       => 60,
        ]);
        $phpWord->addParagraphStyle('center', ['alignment' => Jc::CENTER]);

        $section = $phpWord->addSection([
            'marginTop'    => Converter::cmToTwip(2),
            'marginBottom' => Converter::cmToTwip(2),
            'marginLeft'   => Converter::cmToTwip(2.5),
            'marginRight'  => Converter::cmToTwip(2.5),
        ]);

        // ── Cabeçalho ────────────────────────────────────────────
        $section->addText(
            'Publicações AASP',
            ['bold' => true, 'size' => 16, 'name' => 'Arial'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 80]
        );

        if ($tenant?->nome) {
            $section->addText(
                $tenant->nome,
                ['bold' => true, 'size' => 12, 'name' => 'Arial'],
                ['alignment' => Jc::CENTER, 'spaceAfter' => 60]
            );
        }

        $section->addText(
            "Data: {$dataFmt}  |  {$totalPubs} publicação(ões)  |  {$totalAdvs} advogado(s)",
            ['size' => 9, 'name' => 'Arial'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 40]
        );
        $section->addText(
            "Gerado em: {$geradoEm}",
            ['size' => 9, 'name' => 'Arial'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 100]
        );
        $section->addText(' ', [], 'hr');

        // ── Publicações agrupadas por advogado ────────────────────
        foreach ($agrupadas as $codigo => $pubs) {
            $nomeAdv = $advogados[$codigo] ?? "Código {$codigo}";
            $qtd     = $pubs->count();

            $section->addText(
                $nomeAdv,
                ['bold' => true, 'size' => 12, 'name' => 'Arial'],
                ['spaceBefore' => 160, 'spaceAfter' => 40]
            );
            $section->addText(
                "{$qtd} publicação(ões)",
                ['size' => 9, 'color' => '555555', 'name' => 'Arial'],
                ['spaceAfter' => 100]
            );

            $seq   = 1;
            $total = $pubs->count();

            foreach ($pubs as $pub) {
                if ($pub->data) {
                    $carbon      = Carbon::parse($pub->data);
                    $dataExtenso = $diasSemana[$carbon->dayOfWeek]
                        . ', ' . $carbon->day
                        . ' de ' . $meses[$carbon->month - 1]
                        . ' de ' . $carbon->year . '.';
                } else {
                    $dataExtenso = '—';
                }

                $jornalMaiusculo = strtoupper($pub->jornal ?? '');
                $identificador   = "{$seq}. D J E N" . ($jornalMaiusculo ? " - {$jornalMaiusculo}" : '');

                // Linha identificadora
                $section->addText(
                    $identificador,
                    ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    ['spaceAfter' => 40]
                );

                // Metadados
                $section->addText(
                    "Disponibilização: {$dataExtenso}",
                    ['size' => 10, 'name' => 'Arial'],
                    ['spaceAfter' => 20]
                );
                $section->addText(
                    'Arquivo: 1',
                    ['size' => 10, 'name' => 'Arial'],
                    ['spaceAfter' => 20]
                );

                if ($pub->numero_publicacao) {
                    $section->addText(
                        'Publicação: ' . $pub->numero_publicacao,
                        ['size' => 10, 'name' => 'Arial'],
                        ['spaceAfter' => 20]
                    );
                }

                if ($pub->jornal) {
                    $section->addText(
                        $pub->jornal,
                        ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        ['spaceAfter' => 40]
                    );
                }

                // Texto da publicação
                if ($pub->texto) {
                    $textoLimpo = $this->limparTexto($pub->texto);
                    $paragrafos = preg_split('/\n{2,}/', $textoLimpo);

                    foreach ($paragrafos as $para) {
                        if (trim($para) === '') continue;

                        $lines   = explode("\n", $para);
                        $textRun = $section->addTextRun(['spaceAfter' => 40]);

                        foreach ($lines as $li => $line) {
                            if ($nomeAdv && stripos($line, $nomeAdv) !== false) {
                                // Destaca o nome do advogado em negrito
                                $parts = preg_split(
                                    '/(' . preg_quote($nomeAdv, '/') . ')/iu',
                                    $line,
                                    -1,
                                    PREG_SPLIT_DELIM_CAPTURE
                                );
                                foreach ($parts as $part) {
                                    $bold = (mb_strtolower($part) === mb_strtolower($nomeAdv));
                                    $textRun->addText(
                                        $part,
                                        ['bold' => $bold, 'size' => 9, 'name' => 'Arial']
                                    );
                                }
                            } else {
                                $textRun->addText($line, ['size' => 9, 'name' => 'Arial']);
                            }

                            if ($li < count($lines) - 1) {
                                $textRun->addTextBreak();
                            }
                        }
                    }
                }

                // Separador entre publicações
                if ($seq < $total) {
                    $section->addText(' ', [], 'hr');
                }

                $seq++;
            }
        }

        // ── Rodapé ───────────────────────────────────────────────
        $section->addText(' ', [], 'hr');
        $section->addText(
            "Total: {$totalPubs} publicação(ões) referentes a {$dataFmt}. Gerado em {$geradoEm}.",
            ['size' => 8, 'color' => '666666', 'name' => 'Arial'],
            ['alignment' => Jc::CENTER]
        );

        $nomeArquivo = 'publicacoes_aasp_' . Carbon::parse($data)->format('Y-m-d') . '.docx';

        return response()->streamDownload(function () use ($phpWord) {
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save('php://output');
        }, $nomeArquivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    private function limparTexto(string $texto): string
    {
        $texto      = str_replace(["\r\n", "\r"], "\n", $texto);
        $paragrafos = preg_split('/\n{2,}/', $texto);

        $paragrafos = array_map(function (string $p): string {
            $p = str_replace("\n", ' ', $p);
            $p = preg_replace('/\s+/', ' ', $p);
            $p = preg_replace('/\s+([;:,\.])/', '$1', $p);
            return trim($p);
        }, $paragrafos);

        $paragrafos = array_values(array_filter($paragrafos, fn($p) => $p !== ''));

        return trim(implode("\n\n", $paragrafos));
    }
}
