@extends('layouts.app')
@section('content')
@livewire('processo-andamentos', ['processoId' => $processo->id])
@endsection