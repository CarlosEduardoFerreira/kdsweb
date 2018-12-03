@extends('admin.layouts.admin')

@section('body_class','nav-md')

@section('page')
    <div class="container body">
        <div class="main_container" style="height:100%;">
            @section('header')
                @include('admin.sections.navigation')
                @include('admin.sections.header')
            @show

            @yield('left-sidebar')

            <div class="right_col" role="main">
            
                Admin Settings

            </div>

            <footer>
                @include('admin.sections.footer')
            </footer>
        </div>
    </div>
@stop

@section('scripts')
    @parent
@endsection

@section('styles')
    @parent
@endsection
