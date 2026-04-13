@extends('layouts.app')
@section('page-title', 'Pasta do Cliente')
@section('breadcrumb')
    <a href="{{ route('pessoas') }}">Clientes</a>
    <span class="sep">›</span>
    <span class="current">Pasta do Cliente</span>
@endsection
@section('content')
@livewire('pasta-cliente', ['clienteId' => $clienteId])
@endsection
