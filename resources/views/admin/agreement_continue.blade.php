<?php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\URL;

    $me = Auth::User();
    if (!isset($return)) $return = "error";
?>

@extends('layouts.welcome_first')
{{ Html::script(mix('assets/app/js/app.js')) }}
@tojs
{{ Html::script(mix('assets/admin/js/admin.js')) }}

@section('content')
    @if ($return == "rejected")
        We're sorry, but you need to accept the agreement to use our services.<BR><BR>
        You should be redirected back in a few seconds.
    @elseif ($return == "accepted")
        Thank you!<BR><BR>
        You should be redirected to the dashboard in a few seconds.
    @else
        An error occurred while processing your request. Please try again later.
    @endif
@endsection

<script>
    $(document).ready(function(){
        setTimeout(function() {
            <?php
                if ($return == "rejected") {
                   echo "window.history.back();";
                } else if ($return == "accepted") {
                   echo "window.location.href = '" . route("admin.dashboard") . "'";
                }
            ?>
        }, 5000);
    });
</script>