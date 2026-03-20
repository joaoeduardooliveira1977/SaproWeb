@extends('layouts.app')
@section('page-title', 'Administração')

@section('content')
<x-hub-page
    title="Administração"
    subtitle="Gerenciamento de usuários, tabelas auxiliares e configurações do sistema."
    :cards="[
        ['icon'=>'usuarios',          'title'=>'Usuários',            'desc'=>'Gerenciar usuários e permissões',             'route'=>'usuarios'],
        ['icon'=>'tabelas',           'title'=>'Tabelas Auxiliares',  'desc'=>'Fases, riscos, tipos e outros cadastros',     'route'=>'tabelas'],
        ['icon'=>'administradoras',   'title'=>'Administradoras',     'desc'=>'Cadastro de administradoras',                 'route'=>'administradoras'],
        ['icon'=>'indices',           'title'=>'Índices',             'desc'=>'Índices econômicos (INPC, IPCA, SELIC…)',     'route'=>'indices'],
        ['icon'=>'auditoria',         'title'=>'Auditoria',           'desc'=>'Log de ações e auditoria do sistema',         'route'=>'auditoria'],
        ['icon'=>'portal-acesso',     'title'=>'Portal — Acessos',    'desc'=>'Gerenciar acessos ao portal do cliente',      'route'=>'admin.portal-acesso'],
        ['icon'=>'portal-mensagens',  'title'=>'Portal — Mensagens',  'desc'=>'Mensagens enviadas pelo portal',              'route'=>'admin.portal-mensagens'],
        ['icon'=>'whatsapp',          'title'=>'WhatsApp / SMS',      'desc'=>'Notificações via WhatsApp e SMS',             'route'=>'admin.notificacoes-whatsapp'],
    ]"
/>
@endsection
