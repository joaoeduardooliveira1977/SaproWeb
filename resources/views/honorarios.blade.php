@extends('layouts.app')

@section('page-title', 'Honorários')
@section('breadcrumb')Financeiro <span class="sep">›</span> <span class="current">Honorários</span>@endsection
@section('content')
    @livewire('honorarios')
@endsection
