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
 
                <div style="height:100px;">

                    <div class="page-title" style="background:#ffffff;height:100px;">
                    
                        <div style="padding-top:10px;padding-left:20px;font-size:20px;">
                        		<?=$store->business_name?>
                        </div>
                        
                    </div>

                </div>
                
                @yield('report')

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

