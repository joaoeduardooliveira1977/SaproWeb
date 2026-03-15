@extends('layouts.app')
@section('title', 'Analytics')
@section('page-title', '📊 Analytics')
@section('content')
    @livewire('analytics', lazy: true)
@endsection
