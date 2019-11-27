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
            echo "<span class='group-subtitle'>$error</span>";
        } elseif (isset($accepted)) {
    ?>
        <span class='group-subtitle'>
            Thank you for accepting the agreement.<BR>
            Our team will soon contact you to obtain the full credit card's details and perform the card authorization.<BR><BR>
            <a href=<?= URL::to("./agreements/$sig.pdf") ?> download>You may click here to download your electronically-signed agreement</a>
        </span>
    <?php
        } else {
    ?>
        <div class="col-12 text-center">
            <embed id="embeddedPDF" src="./pdf" width="800px" height="400px" 
                    alt="pdf" pluginspage="http://www.adobe.com/products/acrobat/readstep2.html">
        </div> 
        <BR> 
        <div class="col-12 text-center">
            <form action="./accept" method='POST' onsubmit="return validateForm()">
                {{ csrf_field() }}
                <div class="form-group">
                    <input type="checkbox" id="agree" name="agree" value="ok">&nbsp;
                    <label for=agree>By checking this box, you agree with the Reseller Subscription Agreement.</label>
                </div>
                <BR>
                <button type='submit' class="btn btn-success" id="continue">Continue</button>
            </form>
        </div>
    <?php } ?>

    <script>
        function validateForm() {
            var isChecked = $("#agree").is(":checked");
            if (!isChecked) {
                $("#modal-error-body").html("We're sorry, but you need to accept the agreement to use our services.");
                $("#modal-error").modal("show");
            }
            return isChecked;
        }
    </script>
@endsection