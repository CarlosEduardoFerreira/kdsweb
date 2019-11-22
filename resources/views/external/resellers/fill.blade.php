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
        .thin-text {
            font-weight: 200;
        }
        .group-subtitle { 
                color: #555;
                font-weight: 200;
                font-style: italic;
            }
        .full-width {
            width: 100%!important;
        }
    </style>
@endsection

@section('content')
    <?php
        function print_field($id, $title, $value, $required = false, $readonly = false, $placeholder = "", $inputExtraClasses = "") {
            $required_string1 = $required ? "required" : "";
            $required_string2 = $required ? "<span class='required'>*</span>" : "";
            $readonly_string = $readonly ? "readonly" : "";
            echo "<div class='form-group form-inline'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12 justify-content-end' for='$id'>
                            $title $required_string2
                        </label>
                        <div class='col-md-7 col-sm-7 col-xs-12'>
                            <input id='$id' name='$id' type='text' class='form-control full-width $inputExtraClasses' 
                                value='$value' $required_string1 $readonly_string placeholder='$placeholder'>
                        </div>
                    </div>";
        }


        if (isset($error)) {
            echo "<span class='group-subtitle'>$error</span>";
        } else {
    ?>

    <BR>
    <form class="col-9" method="POST" action="./{{ $hash }}/update">
        {{ csrf_field() }}

        <!-- Subcription Info -->
        <div class="group-title col-9">Subscription Information</div>
        <div class="group-subtitle col-9 mb-3">If you do not agree with the information below, please contact us before filling out this form.</div>
        
        <div class="ml-5">
            <div class="group-title">License Fees</div>
            <table class='table' style='width:300px;'>
                <tbody>
                <?php 
                    foreach ($plans as $plan) {
                        echo "<tr><td class='thin-text p-1 border-0' style='max-width:150px;'>" . $plan->app_name . 
                                        ($plan->hardware === 1 ? " + Hardware" : "") . "</td>";
                        echo "<td class='thin-text p-1 border-0' style='max-width:150px;'>US$" . 
                                        number_format($plan->cost, 2) . "/station</td></tr>";      
                    }
                ?>
                </tbody>
            </table>

            <div class='group-title'>Extra Support</div>
            <table class='table' style='width:500px;'>
                <tbody>
                    <tr>
                        <td class='p-1 border-0' style='max-width:150px;'>{{ $basic->extended_support == "1" ? "INCLUDED" : "NOT INCLUDED" }}</td>
                        <td class='thin-text p-1 border-0' style='max-width:350px;'>Extended Support Package (extra $10/Month)</td>
                    </tr>
                    <tr>
                        <td class='p-1 border-0' style='max-width:150px;'>{{ $basic->onsite_training == "1" ? "INCLUDED" : "NOT INCLUDED" }}</td>
                        <td class='thin-text p-1 border-0' style='max-width:350px;'>On-site training / implementation (prices range)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <BR>

        <!-- Company Address -->
        <div class="group-title col-9">Company Information</div>
        <?php
            print_field("business_name", "Business Name", $basic->business_name, true, false);
            print_field("company_dba", "Doing Business As", $basic->dba, false, false);
            print_field("company_first_name", "First Name", $basic->name, true, false);
            print_field("company_last_name", "Last Name", $basic->last_name, true, false);
            print_field("company_address1", "Address", "", true, false, "Start typing to search...");
            print_field("company_address2", "Address (line 2)", "", false, false);
            print_field("company_city", "City", "", false, true);
            print_field("company_state", "State", "", false, true);
            print_field("company_country", "Country", "", false, true);
            print_field("company_zipcode", "Zip Code", "", false, true);
            print_field("company_email", "E-Mail", $basic->email, true, true);
            print_field("company_phone", "Phone", $basic->email, true, false);
        ?>
        
        <!-- Shipping Contact Info -->
        <div class="col-9 mb-3">
            <span class="group-title">Shipping Contact Information</span><BR>
            <input type="checkbox" id="chk_shipping" name="chk_shipping" value="chk" class="chk_form" data-form="shipping_form" checked>&nbsp;&nbsp;
            <label for='chk_shipping'>Same as Company Contact Information</label>
        </div>
        <div id="shipping_form" style="display:none">
            <?php
                print_field("shipping_careof", "Name / Care Of", "", false, false, "", "shipping_form_req");
                print_field("shipping_address1", "Address", "", false, false, "", "shipping_form_req");
                print_field("shipping_address2", "Address (line 2)", "", false, false);
                print_field("shipping_city", "City", "", false, true);
                print_field("shipping_state", "State", "", false, true);
                print_field("shipping_country", "Country", "", false, true);
                print_field("shipping_zipcode", "Zip Code", "", false, true);
                print_field("shipping_email", "E-Mail", "", false, false);
                print_field("shipping_phone", "Phone", "", false, false);
            ?>
        </div>

        <!-- Billing Contact Info -->
        <div class="col-9 mb-3">
            <span class="group-title">Billing Contact Information</span><BR>
            <input type="checkbox" id="chk_billing" name="chk_billing" value="chk" class="chk_form" data-form="billing_form" checked>&nbsp;&nbsp;
            <label for='chk_billing'>Same as Company Contact Information</label>
        </div>
        <div id="billing_form" style="display:none">
            <?php
                print_field("billing_careof", "Name / Care Of", "", false, false, "", "billing_form_req");
                print_field("billing_address1", "Address", "", false, false, "", "billing_form_req");
                print_field("billing_address2", "Address (line 2)", "", false, false);
                print_field("billing_city", "City", "", false, true);
                print_field("billing_state", "State", "", false, true);
                print_field("billing_country", "Country", "", false, true);
                print_field("billing_zipcode", "Zip Code", "", false, true);
                print_field("billing_email", "E-Mail", "", false, false);
                print_field("billing_phone", "Phone", "", false, false);
            ?>
        </div>

        <!-- Credit Card Info -->
        <div class="group-title col-9">Credit Card Basic Information</div>
        <div class="group-subtitle col-9 mb-3">
            This information will be used in the reseller agreement, which is shown in the next page.
            You will not be charged at this moment. We will call you later to obtain the full 
            credit card's details and perform the card authorization.
        </div>

        <div class="form-group form-inline">
            <label class="control-label col-md-3 col-sm-3 col-xs-12 justify-content-end" for="card_brand" >
                Brand:
                <span class="required">*</span>
            </label>
            <select id="card_type" name="card_type" required>
                <option id="MASTERCARD" selected>Mastercard</option>
                <option id="VISA">Visa</option>
                <option id="AMEX">American Express</option>
                <option id="DISCOVER">Discover</option>
            </select>
        </div>

        <div class="form-group form-inline">
            <label class="control-label col-md-3 col-sm-3 col-xs-12 justify-content-end" for="card_expiration_month">
                Expiration date:
                <span class="required">*</span>
            </label>

            <select id="card_expiration_month" name="card_expiration_month" class="control-label" required>
                <?php
                    for ($month = 1; $month <= 12; $month++) { ?>
                        <option id="{{ $month }}"><?= sprintf("%02d", $month); ?></option>
                    <?php }
               ?>
            </select>
            <select id="card_expiration_year" name="card_expiration_year" class="ml-2" required>
               <?php
                    for ($year = date('Y', time()); $year < date('Y', time()) + 25; $year++) { ?>
                        <option id="{{ $year }}">{{ $year }}</option>
                    <?php }
               ?>
            </select>
        </div>

        <div class="form-group form-inline">
            <label class="control-label col-md-3 col-sm-3 col-xs-12 justify-content-end" for="card_last4" >
                Last 4 numbers:
                <span class="required">*</span>
            </label>
            <input id="card_last4" name="card_last4" type="text" maxlength=4 class="form-control col-md-7 col-xs-12" value="" required>
        </div>

        <div class="form-group form-inline">
            <label class="control-label col-md-3 col-sm-3 col-xs-12 justify-content-end" for="card_cvv" >
                Security Code (CVV):
                <span class="required">*</span>
            </label>
            <input id="card_cvv" name="card_cvv" type="text" maxlength=3 class="form-control col-md-7 col-xs-12" value="" required>
        </div>

        <BR>

        <div class="form-group form-inline">
            <label class="control-label col-md-3 col-sm-3 col-xs-12 justify-content-end"></label>
            <div class="ml-3 col-md-7 col-xs-12">
                <input type="submit" class="btn btn-success pull-right" value="Proceed to the Agreement"/>
            </div>
        </div>
    </form>
    <BR>
    <?php } ?>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/places.js@1.17.0"></script>

<script>
    // Algolia
    var acCompany = places({
        appId: '{{ env("ALGOLIA_APP_ID") }}',
        apiKey: '{{ env("ALGOLIA_API_KEY") }}',
        container: document.querySelector('#company_address1'),
        templates: { value: function(suggestion) { return suggestion.name; }}
    }).configure({ type: 'address' });

    var acShipping = places({
        appId: '{{ env("ALGOLIA_APP_ID") }}',
        apiKey: '{{ env("ALGOLIA_API_KEY") }}',
        container: document.querySelector('#shipping_address1'),
        templates: { value: function(suggestion) { return suggestion.name; }}
    }).configure({ type: 'address' });

    var acBilling = places({
        appId: '{{ env("ALGOLIA_APP_ID") }}',
        apiKey: '{{ env("ALGOLIA_API_KEY") }}',
        container: document.querySelector('#billing_address1'),
        templates: { value: function(suggestion) { return suggestion.name; }}
    }).configure({ type: 'address' });

    acCompany.on('change', function resultSelected(e) {
        document.querySelector('#company_city').value = e.suggestion.city || '';
        document.querySelector('#company_state').value = e.suggestion.administrative || '';
        document.querySelector('#company_country').value = e.suggestion.country || '';
        document.querySelector('#company_zipcode').value = e.suggestion.postcode || '';
    });

    acShipping.on('change', function resultSelected(e) {
        document.querySelector('#shipping_city').value = e.suggestion.city || '';
        document.querySelector('#shipping_state').value = e.suggestion.administrative || '';
        document.querySelector('#shipping_country').value = e.suggestion.country || '';
        document.querySelector('#shipping_zipcode').value = e.suggestion.postcode || '';
    });

    acBilling.on('change', function resultSelected(e) {
        document.querySelector('#billing_city').value = e.suggestion.city || '';
        document.querySelector('#billing_state').value = e.suggestion.administrative || '';
        document.querySelector('#billing_country').value = e.suggestion.country || '';
        document.querySelector('#billing_zipcode').value = e.suggestion.postcode || '';
    });

    $(".chk_form").click(function() {
        $('#' + $(this).data('form')).slideToggle();
        $("." + $(this).data('form') + "_req").prop('required', $(this).is(":checked") == false);
    });
</script>
@endsection

