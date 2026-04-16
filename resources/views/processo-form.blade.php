@extends('layouts.app')
@section('page-title', 'Andamento de Processos')
@section('content')
@livewire('processo-form', ['processoId' => $id ?? null])
@endsection