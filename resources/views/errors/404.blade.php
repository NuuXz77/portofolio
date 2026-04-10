@extends('layouts.public')

@section('title', '404 | Page Not Found')
@section('description', "The page you're looking for doesn't exist or has been moved.")

@section('content')
    <x-error-layout
        code="404"
        title="Page Not Found"
        description="The page you're looking for doesn't exist or has been moved."
        icon="link-2-off"
    >
        Try checking the URL or go back to homepage.
    </x-error-layout>
@endsection
