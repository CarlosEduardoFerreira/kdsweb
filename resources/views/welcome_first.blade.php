@extends('layouts.welcome_first')

@section('content')
    <div class="title m-b-md" id="main_title">
        {{ config('app.name') }}
    </div>
    <div class="m-b-md">
        Thank you for register.<br/>
        <br/>
        Provide your username for your reseller to start using KDS Portal.
    </div>
@endsection
