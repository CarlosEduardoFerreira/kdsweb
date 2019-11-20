@extends('blank')

@section('styles')  
    <style>
        #back-button-div {
            float:left;
            margin-top:-80px;
            font-size: 11px; 
            background: #26b99a00;
        }
        #back-button {font-size: 11px;  background:none; }
        #back-button:hover { 
            text-decoration:underline;
        }
        .page-title { padding-top:90px; }
        #modal-default-title { letter-spacing:2px; }
        .required { 
            color:red; 
            margin-left: 4px;
            margin-right: 4px;
        }
        .group-title { 
            font-weight: 600;
        }
        .group-subtitle { 
            color: #555;
            font-weight: 200;
            font-style:italic;
        }
        .middle {
            align-items: center;
            display: flex;
            justify-content: center;
            height: 100%;
            width: 100%;
        }
        html, body { height: 100% !important; }
    </style>
@endsection

@section('content')
    <?php
        if (isset($error)) {
            echo "<span class='group-subtitle'>$error</span>";
        } elseif (isset($success)) {
    ?>
        <div class="text-center group-subtitle">
            Your password was successfully set.<BR><BR>
            You will be redirected to log in now.

            <script>
                $("#div_content").addClass("middle");
                setTimeout(function() {
                    window.location.href = '<?= route("admin.dashboard") ?>';
                }, 5000);
            </script>
        </div>

    <?php } else { ?>
        <BR>
        <form class="col-6" method="POST" action="./{{ $hash }}/set" onsubmit="return validate()">
            {{ csrf_field() }}

            <div class='group-subtitle mb-3'>
                Welcome, {{ $fullname }}! Please register your password.<BR><BR>
            </div>

            <div class="form-group form-inline">
                <label class="control-label col-md-5 col-sm-6 col-xs-12 justify-content-end" for="email" >
                E-Mail: 
                <span class="required">*</span>
                </label>
                <input id="email" name="email" type="text" class="form-control col-md-7 col-xs-12" value="{{ $email }}" readonly>
            </div>

            <div class="form-group form-inline">
                <label class="control-label col-md-5 col-sm-6 col-xs-12 justify-content-end" for="password">
                Password: 
                <span class="required">*</span>
                </label>
                <input id="password" name="password" type="password" class="form-control col-md-7 col-xs-12" value="" minlength=6 required>
            </div>

            <div class="form-group form-inline">
                <label class="control-label col-md-5 col-sm-6 col-xs-12 justify-content-end" for="password2">
                Password (confirm): 
                <span class="required">*</span>
                </label>
                <input id="password2" name="password2" type="password" class="form-control col-md-7 col-xs-12" value="" minlength=6 required>
            </div>

            <div class="form-group form-inline justify-content-end">
                <input type="submit" class="btn btn-success pull-right" value="Register">
            </div>
        </form>
        <BR>
    <?php } ?>

@endsection

@section('scripts')
<script>
    function validate() {
        pass1 = $("#password").val();
        pass2 = $("#password2").val();

        if (pass1.length < 6) {
            alert("Password must be at least 6 characters long.");
            return false;
        }
        
        if (pass1 !== pass2) {
            alert("Passwords must match.");
            return false;
        }
        
        return true;
    }
</script>
@endsection