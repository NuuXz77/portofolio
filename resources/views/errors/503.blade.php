@extends('layouts.public')

@section('title', '503 | Service Unavailable')
@section('description', 'Service temporarily unavailable, please try again later.')

@section('content')
    <x-error-layout
        code="503"
        title="Service Unavailable"
        description="Service temporarily unavailable, please try again later."
        icon="server-crash"
        :showBack="false"
    />
@endsection
