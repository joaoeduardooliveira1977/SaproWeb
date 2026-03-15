@extends('layouts.app')
@section('page-title', 'Produtividade por Advogado')
@section('content')
    @livewire('produtividade-advogado', lazy: true)
@endsection
