@extends('layouts.master')

@section('sidebar')
<x-admin.sidebar />
@endsection

@section('header')
<x-admin.header />
@endsection

@section('content')
<p>Admin specific content goes here.</p>
@endsection

@section('scripts')
<script>
    console.log('Admin scripts loaded');
</script>
@endsection