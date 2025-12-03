@extends('layouts.master')

@section('sidebar')
<x-dev-admin.sidebar />
@endsection

@section('header')
<x-dev-admin.header />
@endsection

@section('content')
<p>Admin specific content goes here.</p>
@endsection

@section('scripts')
<script>
    console.log('DevAdmin scripts loaded');
</script>
@endsection