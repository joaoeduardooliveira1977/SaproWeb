@extends('layouts.app')

@section('page-title', 'Documentos')
@section('breadcrumb')<span class="current">Documentos</span>@endsection
@section('content')
    @livewire('documentos')
@endsection
