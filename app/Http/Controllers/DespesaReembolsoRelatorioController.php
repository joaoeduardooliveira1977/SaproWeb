<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{DespesaReembolso, Pessoa, Tenant};
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class DespesaReembolsoRelatorioController extends Controller
{
    public function relatorioDespesas(Request $request)
    {
        $request->validate([
            'cliente_id'  => 'required|integer',
            'data_inicio' => 'required|date',
            'data_fim'    => 'required|date|after_or_equal:data_inicio',
        ], [
            'cliente_id.required'  => 'Selecione um cliente.',
            'data_inicio.required' => 'Informe a data de início.',
            'data_fim.required'    => 'Informe a data de fim.',
        ]);

        [$ini, $fim] = $this->datas($request);

        $tenantId = tenant_id();
        $tenant   = Tenant::find($tenantId);
        $cliente  = Pessoa::with('filial')->findOrFail($request->cliente_id);
        $usuario  = auth('usuarios')->user();

        $lancamentos = DespesaReembolso::where('tenant_id', $tenantId)
            ->where('cliente_id', $cliente->id)
            ->where('tipo', 'despesa')
            ->whereBetween('data_lancamento', [$ini->toDateString(), $fim->toDateString()])
            ->orderBy('data_lancamento')
            ->get();

        $dadosBancarios = $tenant?->configuracoes['dados_bancarios'] ?? [];

        return $this->pdf('relatorios.despesas-por-cliente', [
            'tenant'         => $tenant,
            'cliente'        => $cliente,
            'lancamentos'    => $lancamentos,
            'dataIni'        => $ini,
            'dataFim'        => $fim,
            'dadosBancarios' => $dadosBancarios,
            'responsavel'    => $usuario->nome ?? $usuario->login,
            'cargo'          => $usuario->cargo ?? 'Departamento Financeiro',
        ], "Relatorio de Despesas — {$cliente->nome}");
    }

    public function relatorioReembolso(Request $request)
    {
        $request->validate([
            'cliente_id'  => 'required|integer',
            'data_inicio' => 'required|date',
            'data_fim'    => 'required|date|after_or_equal:data_inicio',
        ], [
            'cliente_id.required'  => 'Selecione um cliente.',
            'data_inicio.required' => 'Informe a data de início.',
            'data_fim.required'    => 'Informe a data de fim.',
        ]);

        [$ini, $fim] = $this->datas($request);

        $tenantId = tenant_id();
        $tenant   = Tenant::find($tenantId);
        $cliente  = Pessoa::with('filial')->findOrFail($request->cliente_id);
        $usuario  = auth('usuarios')->user();

        $lancamentos = DespesaReembolso::where('tenant_id', $tenantId)
            ->where('cliente_id', $cliente->id)
            ->where('tipo', 'reembolso')
            ->whereBetween('data_lancamento', [$ini->toDateString(), $fim->toDateString()])
            ->orderBy('data_lancamento')
            ->get();

        $dadosBancarios = $tenant?->configuracoes['dados_bancarios'] ?? [];

        return $this->pdf('relatorios.pedido-reembolso', [
            'tenant'         => $tenant,
            'cliente'        => $cliente,
            'lancamentos'    => $lancamentos,
            'dataIni'        => $ini,
            'dataFim'        => $fim,
            'dadosBancarios' => $dadosBancarios,
            'responsavel'    => $usuario->nome ?? $usuario->login,
            'cargo'          => $usuario->cargo ?? 'Departamento Financeiro',
        ], "Pedido de Reembolso — {$cliente->nome}");
    }

    private function datas(Request $request): array
    {
        $ini = Carbon::parse($request->data_inicio)->startOfDay();
        $fim = Carbon::parse($request->data_fim)->endOfDay();
        return [$ini, $fim];
    }

    private function pdf(string $view, array $dados, string $titulo): \Illuminate\Http\Response
    {
        $dados['geradoEm'] = Carbon::now()->format('d/m/Y');

        $pdf = Pdf::loadView($view, $dados)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'DejaVu Sans',
                'isRemoteEnabled'      => false,
                'isHtml5ParserEnabled' => true,
            ]);

        $nome = preg_replace('/[^a-z0-9_\-]/', '_', strtolower(
            iconv('UTF-8', 'ASCII//TRANSLIT', $titulo)
        )) . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->stream($nome);
    }
}
