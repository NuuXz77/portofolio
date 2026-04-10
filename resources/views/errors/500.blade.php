@extends('layouts.public')

@section('title', '500 | Server Error')
@section('description', 'Something went wrong on our server.')

@section('content')
    <x-error-layout
        code="500"
        title="Something Went Wrong"
        description="Something went wrong on our server."
        icon="triangle-alert"
    />
@endsection
