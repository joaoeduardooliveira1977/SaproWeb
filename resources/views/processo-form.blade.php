@extends('layouts.app')

@section('content')
@livewire('processo-form', ['processoId' => $id ?? null])
@endsection