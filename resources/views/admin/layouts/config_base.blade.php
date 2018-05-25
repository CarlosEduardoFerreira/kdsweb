@extends('layouts.app')

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

                <div style="height:150px;">

                    <div class="page-title" style="background:#ffffff;">

                        <div class="title_left" style="margin-left:20px;">
                            <h1 class="h3">@yield('title')</h1>
                        </div>

                        @if(Breadcrumbs::exists())
                            <div class="title_right">
                                <div class="pull-right">
                                    {!! Breadcrumbs::render() !!}
                                </div>
                            </div>
                        @endif

                    </div>

                    <ul class="nav nav-tabs" role="tablist" style="background:white;">
                        <li role="presentation" class="active"><a class="atabs" href="#settings" role="tab" data-toggle="tab">Settings</a></li>
                        <li role="presentation" ><a class="atabs" href="#devices" role="tab" data-toggle="tab">Devices</a></li>
                        <!--<li role="presentation" ><a class="atabs" href="#licenses" role="tab" data-toggle="tab">Licenses</a></li>-->
                    </ul>

                </div>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="settings">
                        @yield('settings')
                    </div>
                    <div role="tabpanel" class="tab-pane" id="devices">
                        @yield('devices')
                    </div>
                    <!--<div role="tabpanel" class="tab-pane active" id="licenses">
                        @yield('server')
                    </div>-->
                </div>

            </div>

            <footer>
                @include('admin.sections.footer')
            </footer>
        </div>
    </div>
@stop

@section('styles')
    {{ Html::style(mix('assets/admin/css/admin.css')) }}
@endsection

@section('scripts')
    {{ Html::script(mix('assets/admin/js/admin.js')) }}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0-alpha1/jquery.min.js"></script>
    <script type="text/javascript">

        $(document).ready(function(){

            $('.active a').css('background-color','#f7f7f7')

            $(".atabs").click(function(){
                $(".atabs").each(function(){
                    $(this).removeClass('active')
                    $(this).css('background-color','#ffffff')
                    $(this).css('border-bottom','1px solid #ddd')
                })
                $(this).css('background-color','#f7f7f7')
                $(this).css('border-bottom','1px solid #f7f7f7')
            })

        });

    </script>
@endsection
