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
                	
                	<div id="license-info" style="display:inline-table;padding:10px;float:right;font-size:18px;"><?=$licenseInfo;?></div> 

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
                        <li role="presentation"><a id="first-tab" class="atabs" href="#settings" role="tab" data-toggle="tab">Settings</a></li>
                        <li role="presentation"><a class="atabs" href="#devices" role="tab" data-toggle="tab">KDS Stations</a></li>
                        <!--<li role="presentation" ><a class="atabs" href="#licenses" role="tab" data-toggle="tab">Licenses</a></li>-->
                        
                    </ul>

                </div>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane" id="settings">
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
    <style>
        .nav-tabs li { margin-right:10px; }
    </style>
@endsection

@section('scripts')
    {{ Html::script(mix('assets/admin/js/admin.js')) }}

    <script type="text/javascript">

        $(document).ready(function(){

            $('.active a').css('background-color','#f7f7f7');

            var hasSelected = false;
            var atabSelected = window.location.href.split('#')[1];
            $(".atabs").each(function(){
            		var atabs = $(this).attr('href');
            		if (atabSelected != undefined) {
                		if (atabs == '#'+atabSelected) {
                			setTab($(this));
                			hasSelected = true;
                		}
            		}
            		$(this).click(function(){
                		setTab($(this));
                })
            });

            if (!hasSelected) {
            		setTab($('#first-tab'));
            }

            function setTab(selected) {
            		$(".atabs").each(function(){
            			$(this).removeClass('active');
                    $(this).css('border','1px solid #f7f7f7');
                    $(this).css('border-bottom','1px solid #ddd');
                    $(this).css('background-color','#ffffff');
                    
                });
                	selected.css('border','1px solid #ddd');
                	selected.css('border-bottom','1px solid #f7f7f7');
            		selected.css('background-color','#f7f7f7');
                var atabs = selected.attr('href');
                window.location.href = window.location.href.split('#')[0] + atabs;
                window.scrollTo(0, 0);
                $('.tab-pane').hide();
                $(atabs).fadeIn();
            }

            $(this).scrollTop(0);
			
        });

    </script>
@endsection
