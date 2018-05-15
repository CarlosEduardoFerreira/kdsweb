@extends('layouts.welcome')

@section('content')
    <div class="title m-b-md" id="main_title">
        {{ config('app.name') }}
    </div>
    <!-- <div class="m-b-md">
        Sample users:<br/>
        Admin user: admin.laravel@labs64.com / password: admin<br/>
        Demo user: demo.laravel@labs64.com / password: demo
    </div> -->
@endsection
