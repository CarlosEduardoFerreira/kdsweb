<?php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\URL;

    $me = Auth::User();
?>

@extends('layouts.welcome_first')
{{ Html::script(mix('assets/app/js/app.js')) }}
@tojs
{{ Html::script(mix('assets/admin/js/admin.js')) }}

@section('content')
    OK
@endsection
