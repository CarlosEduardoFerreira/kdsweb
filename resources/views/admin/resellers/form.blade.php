@extends('admin.layouts.admin')

@section('title', "New Reseller")

@section('content')
    <?php
        include "assets/admin/modal.new.plan.php";

        $error = filter_input(INPUT_GET, "error");
        if (isset($error)) {
            ?>
                <div id="back-button-div" style="width:100%;">
                    <button onclick="goBack()" type="button" id="back-button" class="btn">Back</button>
                </div>
                <BR><BR><BR>
                <div class="col-12 m-3">
                {{ $error }}
                </div>
            <?php
        } else {

            function print_text_field($id, $label, $value, $required = true) {
                echo "<div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='$id' >
                                $label: " . ($required ? "<span class='required'>*</span>" : "") .
                            "</label>
                            <div class='col-md-6 col-sm-6 col-xs-12'>
                                <input id='$id' name='$id' type='text' class='form-control col-md-7 col-xs-12' 
                                    value='$value' " . ($required ? "required" : "") . ">
                            </div>
                        </div>";
            }
    ?>

    <div id="back-button-div" style="width:100%;">
        <button onclick="goBack()" type="button" id="back-button" class="btn">Back</button>
    </div>
 
    <div class="row" style="min-height:600px">
        <div class="col-12">
            <form class='form-horizontal form-label-left' id='main-form' onsubmit="return newReseller()">
            {{ csrf_field() }}

            <?php
                print_text_field("business_name", "Business Name", $user->business_name);
                print_text_field("dba", "Doing Business As", $user->dba, false);
                print_text_field("name", "Contact First Name", $user->name);
                print_text_field("last_name", "Contact Last Name", $user->last_name);
                print_text_field("email", "E-Mail", $user->email);
            ?>

            <!-- Optional Add-ons -->
            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12'>
                    Extra Support:
                </label>
                <div class='col-md-6 col-sm-6 col-xs-12' style='margin-top: 7px;'>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input big-checkbox" id="check_extended_support" name="check_extended_support" value="1">
                        <label class="form-check-label label-checkbox" for="check_extended_support">Extended Support Package (Extra $10/mo)</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input big-checkbox" id="check_onsite_training" name="check_onsite_training" value="1">
                        <label class="form-check-label label-checkbox" for="check_onsite_training">On-site training/implementation (Prices range)</label>
                    </div>
                </div>
            </div>

            <!-- Plans -->
            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12'>
                    Plans: <span class='required'>*</span>
                </label>
            </div>
            <div class='form-group'>
                <div class='control-label col-md-3 col-sm-3 col-xs-12'>
                    Allee
                </div>
                <div class='col-md-6 col-sm-6 col-xs-12'>
                    <select id="plan_allee" name="plan_allee" data-app="0fbaafa7-7194-4ce7-b45d-3ffc69b2486f" data-hw=0 
                            data-app-name="Allee" class="form-control pull-right select_plan" style="width: 100%" required></select>
                </div>
            </div>
            <div class='form-group'>
                <div class='control-label col-md-3 col-sm-3 col-xs-12'>
                    Premium
                </div>
                <div class='col-md-6 col-sm-6 col-xs-12'>
                    <select id="plan_premium" name="plan_premium" data-app="bc68f95c-1af5-47b1-a76b-e469f151ec3f" data-hw=0 
                            data-app-name="Premium" class="form-control pull-right select_plan" style="width: 100%" required></select>
                </div>
            </div>
            <div class='form-group'>
                <div class='control-label col-md-3 col-sm-3 col-xs-12'>
                    Premium + Hardware
                </div>
                <div class='col-md-6 col-sm-6 col-xs-12'>
                    <select id="plan_premium_hardware" name="plan_premium_hardware" data-app="bc68f95c-1af5-47b1-a76b-e469f151ec3f" 
                            data-app-name="Premium" data-hw=1 class="form-control pull-right select_plan" style="width: 100%" required></select>
                </div>
            </div>
            <BR>
            <div class='form-group'>
                <div class='col-md-9 col-sm-9 col-xs-12 text-right'>
                    <button type='submit' class="btn btn-primary" id="continue">Create Reseller & Send Form Link</button>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/users/edit.css')) }}
    {{ Html::style(mix('assets/admin/css/bootstrap-select.css')) }}
    <style>
        .required { color:red; }
        hr.separator-1 { border:none; width:100%; height:20px; border-bottom:1px solid #C5CAE9; 
                    box-shadow:0 10px 10px -10px #9FA8DA;  }
        #back-button-div {
            float:left;
            margin-top:-80px;
            font-size: 11px; 
            background: #26b99a00;
        }
        #back-button { font-size: 11px;  background:none; }
        #back-button:hover { 
            text-decoration:underline;
        }
        .page-title { padding-top:90px; }
        label { margin-top: 7px; }
        .label-checkbox {font-weight:400;}
        .big-checkbox {width:20px; height:15px;}
    </style>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/users/edit.js')) }}
    {{ Html::script(mix('assets/admin/js/location.js')) }}
    {{ Html::script(mix('assets/admin/js/validation.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-select.min.js')) }}
    {{ Html::script(mix('assets/admin/js/jquery.mask.js')) }}
    {{ Html::script(mix('assets/admin/js/firebase-api.js')) }}
    {{ Html::script(mix('assets/admin/js/ModalDelete.js')) }}

    <script>
        var last_select = null;
        populateSelects();

        function goBack() {
            window.history.back();
        }

        $(".select_plan").change(function() {
            var select = $(this);
            last_select = select;
            if (select.val() == "add_new") {
                $("#modal-new").modal("show");
                $("#plan_app").val(select.data("app"));
                $("#plan_hardware").val(select.data("hw"));
                $("#plan_app_ro").val(select.data("app-name"));
                $("#plan_hardware_ro").val(select.data("hw") == 1 ? "Yes" : "No");
                $("#plan_name").val("");
                $("#plan_cost").val("");
                $("#plan_longevity").val("");
            }
        });

        $("#btnApply").click(function() {
            val_name = $("#plan_name")[0].checkValidity();
            val_cost = $("#plan_cost")[0].checkValidity();
            val_longevity = $("#plan_longevity")[0].checkValidity();

            if (val_name && val_cost && val_longevity) {
                var data = {"app": $("#plan_app").val(), 
                            "hardware": $("#plan_hardware").val(), 
                            "name": $("#plan_name").val(), 
                            "cost": $("#plan_cost").val(), 
                            "frequency": $("#plan_frequency").val(),
                            "longevity": $("#plan_longevity").val()}

                $.ajax({url: './add_plan', 
                        data: data
                    }).done(function(response, textStatus, jqXHR) {
                        // Reload options and pre-choose the recently created one
                        if (response.success) {
                            populateSelects(response.id);
                            $("#modal-new").modal("hide");
                        } else {
                            $("#modal-error-body").html(response.error);
                            $("#modal-error").modal("show");
                        }
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        // Show unknown error message
                        $("#modal-error-body").html("An unknown error occurred while creating the plan");
                        $("#modal-error").modal("show");
                    });
            } else {
                $("#modal-error-body").html("Please fill out all the required fields.");
                $("#modal-error").modal("show");
            }
        });

        function newReseller(e) {
            var data = $("#main-form").serialize();
            $.ajax({url: './insert', 
                        data: data,
                        type: "POST"
                    }).done(function(response, textStatus, jqXHR) {
                        if (response.success) {
                            $("#modal-success-title").html("Success");
                            $("#modal-success-body").html("Thank you! The reseller shall soon receive the link.<BR>" + 
                                                            "You will be redirected to the reseller's page in a few seconds.");
                            $("#modal-success").modal("show");
                        } else {
                            $("#modal-error-body").html(response.error);
                            $("#modal-error").modal("show");
                        }
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        // Show unknown error message
                        $("#modal-error-body").html("An unknown error occurred while creating the reseller");
                        $("#modal-error").modal("show");
                    });
            return false;
        }

        $("#modal-success-button-ok").click(function() {
            location.href = "./?filter=0";
        });

        function populateSelects(selection = "") {
            var select;

            // Save values
            val_a = $("#plan_allee").val();
            val_p = $("#plan_premium").val();
            val_phw = $("#plan_premium_hardware").val();

            // Empty
            $("#plan_allee").empty();
            $("#plan_premium").empty();
            $("#plan_premium_hardware").empty();

            $("#plan_allee").append('<option value="add_new">Create new plan...</option>');
            $("#plan_premium").append('<option value="add_new">Create new plan...</option>');
            $("#plan_premium_hardware").append('<option value="add_new">Create new plan...</option>');

            $.ajax({
                url: './plans'
            }).then(function(options) {
                options.map(function(option) {
                    var optionHTML = $('<option>');
                    var apphw = option["app"] + "-" + option["hardware"];
                    switch (apphw) {
                        case "0fbaafa7-7194-4ce7-b45d-3ffc69b2486f-0":
                            select = $("#plan_allee");
                            break;

                        case "bc68f95c-1af5-47b1-a76b-e469f151ec3f-1":
                            select = $("#plan_premium_hardware");
                            break;

                        default:
                            select = $("#plan_premium");
                            break;
                    }
                    
                    optionHTML.val(option["guid"]).text(option["name"]);
                    select.append(optionHTML);
                });

                // Restore values 
                $("#plan_allee").val(val_a);
                $("#plan_premium").val(val_p);
                $("#plan_premium_hardware").val(val_phw);

                if (selection != "") {
                    last_select.val(selection);
                } else {
                    $('#plan_allee option:eq(1)').attr('selected', 'selected');
                    $('#plan_premium option:eq(1)').attr('selected', 'selected');
                    $('#plan_premium_hardware option:eq(1)').attr('selected', 'selected');
                }
            });
        }
    </script>
@endsection
