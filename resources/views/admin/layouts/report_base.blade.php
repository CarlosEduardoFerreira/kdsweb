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
            <button onclick="goBack()" type="button" id="back-button" class="btn">Back</button>
                <div style="height:100px;">

                    <div class="page-title" style="background:#ffffff;height:100px;">
                    
                        <div style="padding-top:10px;padding-left:20px;font-size:20px;">
                        		<?=$store->business_name?>
                        </div>
                        
                    </div>

                </div>
<style>   
button[type=button]:hover {
    text-decoration:underline;
}
#back-button {
font-size: 11px; 
background: #26b99a00;
}
</style>
<script>
    function goBack() {
    window.history.back();
    }
</script>
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

