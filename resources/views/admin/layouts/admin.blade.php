@extends('layouts.app')

@section('body_class','nav-md')

@section('page')
    <div class="container body">
        <div class="main_container">
            @section('header')
                @include('admin.sections.navigation')
                @include('admin.sections.header')
            @show

            <div class="right_col" role="main">
                <div class="page-title">
                    <div class="title_left">
                        <h1 class="h3">@yield('title')</h1>
                    </div>
                </div>
                @yield('content')
            </div>

            <footer>
                @include('admin.sections.footer')
            </footer>
        </div>
    </div>
    
<?php 
    include "assets/includes/modal.default.php";
    include "assets/includes/modal.success.php";
    include "assets/includes/modal.delete.php";
    include "assets/includes/modal.error.php"; 
?>

@stop

@section('styles')
    {{ Html::style(mix('assets/admin/css/admin.css')) }}
    
@endsection

@section('scripts')
    {{ Html::script(mix('assets/admin/js/admin.js')) }}
    {{ Html::script(mix('assets/admin/js/ModalDelete.js')) }}
@endsection




