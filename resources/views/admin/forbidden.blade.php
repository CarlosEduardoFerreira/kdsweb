@extends('layouts.welcome_first')

@section('content')
    <div class="title m-b-md" id="main_title">
        {{ config('app.name') }}
    </div>
    <div class="m-b-md">
        Access Unauthorized.
        <br/>
        <br/>
        Your User does not have access to see this page.
        <br/>
        <br/>
        Redirecting to dashboard page...
    </div>
    
    <script language="javascript" type="text/javascript">
         window.setTimeout('window.open("/admin", "_self")',5000); // miliseconds
     </script>
             
@endsection
