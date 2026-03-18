@extends('layouts.app')
@section('titulo', 'Publicações AASP')
@section('page-title', 'Publicações AASP')
@section('breadcrumb')Ferramentas <span class="sep">›</span> <span class="current">Publicações AASP</span>@endsection
@section('conteudo')
    @livewire('aasp-publicacoes')
@endsection
