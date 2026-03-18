@extends('layouts.app')
@section('titulo', 'Usuários')
@section('page-title', 'Gerenciar Usuários')
@section('breadcrumb')Administração <span class="sep">›</span> <span class="current">Usuários</span>@endsection
@section('conteudo')
    @livewire('usuarios')
@endsection
