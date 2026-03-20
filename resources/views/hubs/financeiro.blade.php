@extends('layouts.app')
@section('page-title', 'Financeiro')

@section('content')
<x-hub-page
    title="Financeiro"
    subtitle="Controle financeiro, honorários, inadimplência e relatórios."
    :cards="[
        ['icon'=>'visao-geral',   'title'=>'Visão Geral',          'desc'=>'Resumo consolidado do financeiro',            'route'=>'financeiro.consolidado'],
        ['icon'=>'por-processo',  'title'=>'Por Processo',         'desc'=>'Receitas e despesas por processo',            'route'=>'financeiro'],
        ['icon'=>'honorarios',    'title'=>'Honorários',           'desc'=>'Gestão de honorários e cobranças',            'route'=>'honorarios'],
        ['icon'=>'conciliacao',   'title'=>'Conciliação Bancária', 'desc'=>'Conciliar extratos bancários',                'route'=>'conciliacao-bancaria'],
        ['icon'=>'inadimplencia', 'title'=>'Inadimplência',        'desc'=>'Clientes com pagamentos em atraso',           'route'=>'inadimplencia'],
        ['icon'=>'relatorios',    'title'=>'Relatórios',           'desc'=>'Relatórios financeiros e exportações',        'route'=>'relatorios.index'],
        ['icon'=>'analytics',     'title'=>'Analytics',            'desc'=>'Estatísticas e desempenho do escritório',     'route'=>'analytics'],
        ['icon'=>'produtividade', 'title'=>'Produtividade',        'desc'=>'Relatório de produtividade por advogado',     'route'=>'produtividade'],
    ]"
/>
@endsection
