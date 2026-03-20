@extends('layouts.app')
@section('page-title', 'Ferramentas')

@section('content')
<x-hub-page
    title="Ferramentas"
    subtitle="Ferramentas jurídicas, consultas e assistentes."
    :cards="[
        ['icon'=>'calculadora',        'title'=>'Calculadora Jurídica', 'desc'=>'Cálculos de correção monetária e juros',      'route'=>'calculadora'],
        ['icon'=>'consulta-judicial',  'title'=>'Consulta Judicial',    'desc'=>'Consultar processos nos tribunais (TJSP)',    'route'=>'tjsp'],
        ['icon'=>'publicacoes-aasp',   'title'=>'Publicações AASP',     'desc'=>'Acompanhar publicações e intimações',         'route'=>'aasp-publicacoes'],
        ['icon'=>'monitoramento',      'title'=>'Monitoramento',        'desc'=>'Monitorar processos automaticamente',         'route'=>'monitoramento'],
        ['icon'=>'assistente-ia',      'title'=>'Assistente IA',        'desc'=>'Assistente inteligente para redação e análise','route'=>'assistente'],
        ['icon'=>'crm',                'title'=>'Pipeline / CRM',       'desc'=>'Gestão comercial e funil de clientes',        'route'=>'crm'],
    ]"
/>
@endsection
