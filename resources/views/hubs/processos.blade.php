@extends('layouts.app')
@section('page-title', 'Processos')

@section('content')
<x-hub-page
    title="Processos"
    subtitle="Gerencie processos, pessoas, documentos e prazos."
    :cards="[
        ['icon'=>'processos',          'title'=>'Processos',          'desc'=>'Listar e gerenciar todos os processos',        'route'=>'processos'],
        ['icon'=>'novo-processo',      'title'=>'Novo Processo',      'desc'=>'Cadastrar um novo processo',                   'route'=>'processos.novo'],
        ['icon'=>'pessoas',            'title'=>'Pessoas',            'desc'=>'Cadastro de pessoas e partes',                 'route'=>'pessoas'],
        ['icon'=>'correspondentes',    'title'=>'Correspondentes',    'desc'=>'Gerenciar correspondentes jurídicos',          'route'=>'correspondentes'],
        ['icon'=>'procuracoes',        'title'=>'Procurações',        'desc'=>'Controle de procurações e validades',          'route'=>'procuracoes'],
        ['icon'=>'documentos',         'title'=>'Documentos',         'desc'=>'Documentos vinculados aos processos',          'route'=>'documentos'],
        ['icon'=>'minutas',            'title'=>'Minutas',            'desc'=>'Minutas e modelos de documentos',              'route'=>'minutas'],
        ['icon'=>'assinatura-digital', 'title'=>'Assinatura Digital', 'desc'=>'Assinar documentos eletronicamente',           'route'=>'assinatura-digital'],
        ['icon'=>'audiencias',         'title'=>'Audiências',         'desc'=>'Agenda e controle de audiências',              'route'=>'audiencias'],
        ['icon'=>'prazos',             'title'=>'Prazos',             'desc'=>'Controle e alertas de prazos processuais',     'route'=>'prazos'],
        ['icon'=>'agenda',             'title'=>'Agenda',             'desc'=>'Agenda de compromissos e eventos',             'route'=>'agenda'],
    ]"
/>
@endsection
