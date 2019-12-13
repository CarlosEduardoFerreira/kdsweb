@extends('admin.layouts.admin')
@if($user->exists)
    @section('title',"Edit Store" )   
@else
    @section('title',"New Store" )
@endif

@section('content')

<?php
        use App\Http\Controllers\Vars;

        function print_field($id, $title, $value, $required = false, $readonly = false, $placeholder = "", $inputExtraClasses = "", $password = false) {
            $required_string1 = $required ? "required" : "";
            $required_string2 = $required ? "<span class='required'>*</span>" : "";
            $readonly_string = $readonly ? "readonly" : "";
            $inputType = $password ? "password" : "text";
            echo "<div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12 justify-content-end' for='$id'>
                        $title $required_string2
                    </label>
                    <div class='col-md-7 col-sm-7 col-xs-12'>
                        <input id='$id' name='$id' type='$inputType' class='form-control full-width umb6 $inputExtraClasses' 
                            value='$value' $required_string1 $readonly_string placeholder='$placeholder'>
                    </div>
                    </div>";
        }

        function print_hidden($id, $value) {
            echo "<input type='hidden' id='$id' name='$id' value='$value'>";
        }

        function print_checkboxes($grouplabel, $ids, $values, $labels, $checked) {
            echo "<div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12'>
                        $grouplabel
                    </label>
                    <div class='col-md-6 col-sm-6 col-xs-12' style='margin-top: 7px;'>";
            
            if ((count($ids) == count($values)) && 
                (count($values) == count($labels)) && 
                (count($labels) == count($checked)) && 
                (count($ids) > 0)) {

                for ($k = 0; $k < count($ids); $k++) {
                    $id = $ids[$k];
                    $value = $values[$k];
                    $label = $labels[$k];
                    $is_checked = $checked[$k] == true ? "checked" : "";
                    echo "<div class='form-check'>
                                <input type='checkbox' class='form-check-input big-checkbox' id='$id' name='$id' value='$value' $is_checked>
                                <label class='form-check-label label-checkbox' style='margin-top: -7px;' for='$id'>$label</label>
                            </div> ";
                }
            }

            echo "</div></div>";
        }

        function print_combo($id, $title, $values, $options, $selectedValue = '', $required = false, $readonly = false) {
            if ($readonly) {
                $idx = array_search($selectedValue, $values);
                print_hidden($id, $values[$idx]);
                if ($idx) {
                    print_field($id . "_readonly", $title, $options[$idx], false, true, "", "", false);
                }
            } else {
                $required_string1 = $required ? "required" : "";
                $required_string2 = $required ? "<span class='required'>*</span>" : "";
                $readonly_string = $readonly ? "readonly" : "";
                echo "<div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12 justify-content-end' for='$id'>
                            $title $required_string2
                        </label>
                        <div class='col-md-7 col-sm-7 col-xs-12'>
                            <select id='$id' name='$id' class='form-control select2 umb6' style='width: 100%' $required_string1 $readonly_string>";
                            
                                if ((count($options) > 0) && (count($options) == count($values))) {
                                    for ($k = 0; $k < count($options); $k++) {
                                        echo "<option value='" . $values[$k];
                                        if ($values[$k] == $selectedValue) {
                                            echo "' selected>";
                                        } else {
                                            echo "'>";
                                        }
                                        echo $options[$k] . "</option>";
                                    }
                                }
                echo "      </select>
                        </div>
                      </div>";
            }
        }
?>

<div id="back-button-div" style="width:100%;">
    <button onclick="goBack()" type="button" id="back-button" class="btn">Back</button>
</div>
       
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
    .umb6 {
        margin-bottom: 6px!important;
    }
    .label-checkbox {font-weight:400;}
    .big-checkbox {width:20px; height:15px;}
</style>

    <div class="row" style="min-height:800px">
        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top:20px;">
            @if($user->exists)
                {{ Form::open(['route'=>['admin.stores.update', $user->id], 'id' => 'main-form', 
                    'method' => 'put','class'=>'form-horizontal form-label-left']) }}
            @else
                {{ Form::open(['route'=>['admin.stores.insert'], 'id' => 'main-form', 
                    'method' => 'put', 'class'=>'form-horizontal form-label-left']) }}
            @endif
            {{ csrf_field() }}
            <?php
                print_hidden("user_obj", $obj);
                print_hidden("user_id", $user->id);

                if ($me->roles[0]->id == 2 && $obj == 'storegroup') {
                    print_hidden("parent_id", $me->id);

                } else if ($me->roles[0]->id == 3 && $obj == 'store') { 
                    print_hidden("parent_id", $me->id);

                } else if ($obj != 'reseller' && $me->id != $user->id) { 
                    $values = [];
                    $options = [];
                    foreach ($parents as $parent) {
                        $values[] = $parent->id;
                        $options[] = $parent->business_name;
                    }
                    $title = ($obj == "store") ? "Store Group" : "Reseller";
                    print_combo("parent_id", $title, $values, $options, $user->parent_id, true, false);

                } else {
                    print_hidden("parent_id", $user->parent_id);
                }
      
                print_hidden("active", 1);

                $title = ($obj == "storegroup") ? "Store Group Name" : "Legal Business Name";
                print_field("business_name", $title, $user->business_name, true, false, "", "");
                print_field("dba", "Doing Business As", $user->dba, false, false, "", "");
                print_field("name", "First Name", $user->name, true, false, "", "");
                print_field("last_name", "Last Name", $user->last_name, true, false, "", "");
                print_field("email", "E-Mail", $user->email, true, false, "", "");
                print_field("phone_number", "Phone Number", $user->phone_number, true, false, "", "");
                print_field("address", "Address", $user->address, true, false, "Start typing", "");
                print_field("address2", "Address 2", $user->address2, false, false, "", "");
                print_field("city", "City", $user->city, false, true, "", "");
                print_field("state", "State", $user->state, false, true, "", "");
                print_field("country", "Country", $user->country, false, true, "", "");
                print_field("zipcode", "Zip Code", $user->zipcode, false, true, "", "");
                print_field("timezone", "Timezone", $user->timezone, false, true, "", "");

                $options = [];
                $values = [];
                $readonly = $user->exists ? ($me->roles[0]->weight != 1000) : false;
                foreach ($apps as $app) {
                    $options[] = $app->name;
                    $values[] = $app->guid;
                }
                print_combo("user_apps", "App", $values, $options, $app_guid, true, $readonly);

                if ($readonly) {
                    print_hidden("hardware", $user->hardware);
                    print_field("hardware_display", "Hardware", ($user->hardware == 1 ? "Yes" : "No"), false, true, "", "");
                } else {
                    print_checkboxes("Hardware", ["hardware"], ["1"], ["The store is using LogicControl's hardware"], [$user->hardware == 1]);
                }

                //
                // Store type (Demo/Pilot/Live):
                // [Reseller]
                // -- New store: selection is possible
                // -- Existing store: existing value
                //
                // [Other users]
                // -- New store: Store group owns at least 1 'Live' store? --> 'Live', otherwise 'Pilot'
                // -- Existing store: existing value
                //
                $options = [];
                $values = [];
                foreach ($envs as $env) {
                    $options[] = $env->name;
                    $values[] = $env->guid;
                }

                if (!$user->exists) {
                    if ($me->roles[0]->weight >= 900) {
                        print_combo("user_envs", "Type", $values, $options, $env_guid, true, $readonly);
                    } else {
                        if ($total_live > 0) {
                            print_hidden("user_envs", "b78ba4b7-6534-4e3e-87a5-ee496b1b4264");
                            print_field("user_envs_display", "Type", "Live Store", true, true, "", "", false);
                        } else {
                            print_hidden("user_envs", "750375a5-699a-4e76-beda-1ba82233fade");
                            print_field("user_envs_display", "Type", "Pilot Store", true, true, "", "", false);
                        }
                    }
                } else {
                    print_combo("user_envs", "Type", $values, $options, $env_guid, true, $readonly);
                }
                
                print_field("username", "Username", $user->username, true, false, "", "");
                $passwordRequired = !isset($user->id) ? "required" : "";
                print_field("password", "Password", "", $passwordRequired, false, "", "", true);
                print_field("password_confirmation", "Confirm Password", "", $passwordRequired, false, "", "", true);
            ?>

            <div class="form-group" style="text-align:right;padding-top:50px;padding-bottom:100px;">
                <div class="col-md-7 col-sm-7 col-xs-12 col-md-offset-3">
                    <?php if ($obj == 'store' && $user->exists) { ?>
                        <a class="btn btn-danger remove-store pull-left">Delete</a>
                    <?php } ?>
                    <a class="btn btn-primary" href="{{ URL::previous() }}" style="margin-right:50px;">
                         {{ __('views.admin.users.edit.cancel') }}</a>
                    <button id="btn-save-form" type="button" class="btn btn-success" obj="<?=$obj?>" 
                        edit="<?=$user->exists?>"> {{ __('views.admin.users.edit.save') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
    
    
<?php include "assets/includes/modal.error.php"; ?>

<?php include "assets/includes/modal.delete.php"; ?>

    
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/users/edit.css')) }}
    {{ Html::style(mix('assets/admin/css/bootstrap-select.css')) }}
    <style>
        .required { color:red; }
        hr.separator-1 { border:none; width:100%; height:20px; border-bottom:1px solid #C5CAE9; 
                    box-shadow:0 10px 10px -10px #9FA8DA;  }
    </style>
@endsection

@section('scripts')

    @parent
    {{ Html::script(mix('assets/admin/js/users/edit.js')) }}
    {{ Html::script(mix('assets/admin/js/validation.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-select.min.js')) }}

    {{ Html::script(mix('assets/admin/js/jquery.mask.js')) }}

    {{ Html::script(mix('assets/admin/js/firebase-api.js')) }}
    {{ Html::script(mix('assets/admin/js/ModalDelete.js')) }}
    
    <script src="https://cdn.jsdelivr.net/npm/places.js@1.17.0"></script>

    <script>
        function goBack() {
            window.history.back();
        }

        // Algolia
        var acStore = places({
            appId: '{{ env("ALGOLIA_APP_ID") }}',
            apiKey: '{{ env("ALGOLIA_API_KEY") }}',
            container: document.querySelector('#address'),
            templates: { value: function(suggestion) { return suggestion.name; }}
        }).configure({ type: 'address' });

        acStore.on('change', function resultSelected(e) {
            document.querySelector('#city').value = e.suggestion.city || '';
            document.querySelector('#state').value = e.suggestion.administrative || '';
            document.querySelector('#country').value = e.suggestion.country || '';
            document.querySelector('#zipcode').value = e.suggestion.postcode || '';

            // Update Timezone
            lat = e.suggestion.latlng.lat;
            lon = e.suggestion.latlng.lng;
            url = '<?= env('TIMEZONE_DB_API') ?>';
            key = '<?= env('TIMEZONE_DB_KEY') ?>';
            $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            });
            $.ajax({
                url: url, 
                dataType: 'jsonp',
                data: {
                    'key': key,
                    'format': 'json',
                    'fields': 'zoneName',
                    'by': 'position',
                    'lat': lat,
                    'lng': lon
                },
                crossDomain: true
            }).done(function(data) {
                console.log(data);
                if (data.status == "OK") {
                    document.querySelector('#timezone').value = data.zoneName;
                } else {
                    document.querySelector('#timezone').value = '<?= Vars::$timezoneDefault ?>';
                }
            });
        });

        $(function(){
            $('.remove-store').click(function(){
                var url = "{{ route('admin.stores.removeStore') }}";
                var guids = ["{{ $user->store_guid }}"];
                var itemText = "Store";

                new ModalDelete(url, guids, itemText, "", function(error) {
                    $('#modal-delete').modal('hide');
                    setTimeout(function(){
                        if(error == '') {
                            $('#modal-default').modal('hide');

                            sendNotificationToFirebase();
                            setTimeout(function(){ window.location.href = "/admin/stores/0"; }, 3000);

                        } else {
                            $('#modal-error').find('#modal-error-title').html("Error Delete Store");
                            $('#modal-error').find('#modal-error-body').html("<div>" + error + "</div>");
                            $('#modal-error').modal('show');
                        }
                    }, 400);
                });
            });
        });
    </script>
@endsection








