

@extends('layouts.welcome_first')

@section('content')
    <div class="title m-b-md" id="main_title">
        {{ config('app.name') }}
    </div>
    <div class="m-b-md">
        Welcome to KitchenGo!<br/>
        <br/>
        This page is reserved for KDS API.
    </div>
@endsection