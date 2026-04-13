@extends('layouts.app')

@section('title', 'Financeiro Consolidado')
@section('page-title', 'Financeiro Consolidado')
@section('breadcrumb')Financeiro <span class="sep">›</span> <span class="current">Consolidado</span>@endsection

@section('content')
@livewire('financeiro-consolidado')
@endsection
