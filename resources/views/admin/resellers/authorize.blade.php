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
    </style>
@endsection

@section('content')
    <?php
        if (isset($error)) {
            echo "<span class='group-subtitle'>The page you are trying to access is not available.</span>";
        } elseif ($approve == "approve") {
            echo "<span class='group-subtitle'>Thank you, the user was authorized. You may close this tab now.</span>";
        } elseif ($approve == "error") {
            echo "<span class='group-subtitle'>An error occurred while authorizing the user. Please try again later.</span>";
        } else {
    ?>
    <BR>
    <div class="col-9">
        <!-- Authorization confirmation -->
        <div class="group-subtitle col-9 mb-3">Please check the information below before authorizing.</div>
        <BR>
        <div class="form-group form-inline">
            <label class="control-label col-md-3 col-sm-3 col-xs-12 justify-content-end" for="email" >
            E-Mail: 
            </label>
            <input id="email" name="email" type="text" class="form-control col-md-7 col-xs-12" value="{{ $email }}" readonly>
        </div>

        <div class="form-group form-inline">
            <label class="control-label col-md-3 col-sm-3 col-xs-12 justify-content-end" for="business_name" >
            Business Name: 
            </label>
            <input id="business_name" name="business_name" type="text" class="form-control col-md-7 col-xs-12" value="{{ $business_name }}" readonly>
        </div>

        <div class="form-group form-inline">
            <label class="control-label col-md-3 col-sm-3 col-xs-12 justify-content-end" for="card_summary" >
            Credit Card: 
            </label>
            <input id="card_summary" name="card_summary" type="text" class="form-control col-md-7 col-xs-12" value="{{ $card_summary }}" readonly>
        </div>

        <div class="form-group form-inline">
            <label class="control-label col-md-3 col-sm-3 col-xs-12 justify-content-end"></label>
            <div class="ml-3 col-md-7 col-xs-12">
                <input type="button" id="auth" class="btn btn-success pull-right" value="Authorize User">
            </div>
        </div>
    </div>
    <BR>
    <?php } ?>

@endsection

@section('scripts')
<script>
    $("#auth").click(function() {
        window.location.href = window.location.href + "/approve"
    });
</script>
@endsection