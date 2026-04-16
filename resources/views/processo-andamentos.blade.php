@extends('layouts.app')
@section('content')
@section('page-title', 'Andamento de Processos')
@livewire('processo-andamentos', ['processoId' => $processo->id])
@endsection