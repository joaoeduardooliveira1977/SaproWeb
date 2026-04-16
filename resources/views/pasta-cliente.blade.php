@extends('layouts.app')
@section('page-title', 'Pasta do Cliente')
@section('content')
@livewire('pasta-cliente', ['clienteId' => $clienteId])
@endsection
