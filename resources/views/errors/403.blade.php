@extends('layouts.public')

@section('title', '403 | Access Forbidden')
@section('description', "You don't have permission to access this page.")

@section('content')
    <x-error-layout
        code="403"
        title="Access Forbidden"
        description="You don't have permission to access this page."
        icon="shield-alert"
    />
@endsection
