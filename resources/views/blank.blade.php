<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Meta title & meta -->
        @meta

        @yield('styles')

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" crossorigin="anonymous">

        {{ Html::style(mix('assets/app/css/app.css')) }}
        {{ Html::script(mix('assets/app/js/app.js')) }}
        
        <!-- Laravel variables for js -->
        @tojs
    </head>
    <body>
        <div id='div_content' class="col m-3">
            @yield('content')
        </div>

        @yield('scripts')
    </body>
</html>