@extends('admin.layouts.config_base')

@section('title',"Store Settings" )

<!-- ******************************** settings ******************************** -->

@section('settings')

    <div class="row" style="width:100%;">

        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::open(['route'=>['admin.stores.update', $store->id],'method' => 'put','class'=>'form-horizontal form-label-left']) }}

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="server_address" >
                    Server Address:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="server_address" name="server_address" type="text"
                    value="{{ $settings->server_address_ }}" class="form-control col-md-7 col-xs-12">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="server_username" >
                    Server Username:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="server_username" name="server_username" type="text"
                    value="{{ $settings->server_username_ }}" class="form-control col-md-7 col-xs-12">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="server_password" >
                    Server Password:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="server_password" name="server_password" type="password"
                    value="{{ $settings->server_password_ }}" class="form-control col-md-7 col-xs-12">
                </div>
            </div>

            <div class="divider" style="width:50%;margin:auto;margin-top:20px;margin-bottom:20px;"></div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="socket_port" >
                    Local Sync Socket Port:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="socket_port" name="socket_port" type="number" style="width:100px;display:inline;text-align:center;"
                    value="{{ $settings->socket_port_ }}" class="form-control" required>
                </div>
            </div>

            <div class="divider" style="width:50%;margin:auto;margin-top:20px;margin-bottom:20px;"></div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="auto_bump_type" >
                    Automatic Bump Time:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <span class="radio-bump" name="radio_bump">
                        Daily: &nbsp; {{ Form::radio('auto_bump_type', 'Daily', true) }}
                    </span>
                    <span class="radio-bump">
                        Hourly: &nbsp; {{ Form::radio('auto_bump_type', 'Hourly', $settings->auto_done_order_hourly_) }}
                    </span>
                    <span class="radio-bump-time">
                        <select style="width:80px;height:30px;">
                        <?php
                            $kdsTime = new DateTime();
                            $kdsTime->setTimezone(new DateTimeZone($settings->timezone_));
                            $kdsTime->setTimestamp($settings->auto_done_order_time_);
                            $kdsTime = $kdsTime->format('H:i');
                            $selected = "";
                            $found = false;
                            for($hours=0; $hours<24; $hours++) {
                                for($mins=0; $mins<60; $mins+=30) {
                                    $optionTime = str_pad($hours,2,'0',STR_PAD_LEFT).':'.str_pad($mins,2,'0',STR_PAD_LEFT);
                                    if($kdsTime == $optionTime) {
                                        $selected = "selected";
                                        $found = true;
                                    } else {
                                        $selected = "";
                                    }
                                    echo "<option $selected>$optionTime</option>";
                                }
                            }
                            if(!$found) {
                                echo "<option selected>$kdsTime</option>";
                            }
                           ?>
                       </select>
                    </span>
                </div>
            </div>

            <div class="divider" style="width:50%;margin:auto;margin-top:20px;margin-bottom:20px;"></div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="smart_order" >
                    Smart Order:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <?php
                        $sel  = $settings->smart_order_ ? ["","selected"] : ["selected",""];
                    ?>
                    <select style="width:60px;height:30px;">
                        <option <?=$sel[0]?>>No</option>
                        <option <?=$sel[1]?>>Yes</option>
                   </select>
                </div>
            </div>

            <div class="divider" style="width:50%;margin:auto;margin-top:20px;margin-bottom:20px;"></div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="licenses" >
                    Licenses Amount:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="licenses" name="licenses" type="number" style="width:100px;display:inline;text-align:center;"
                    value="{{ $settings->licenses_quantity_ }}" class="form-control" required>
                </div>
            </div>

        <!--
            <div class="divider" style="width:50%;margin:auto;margin-top:20px;margin-bottom:20px;"></div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="auto_order_status">
                    Automatic Change Order Status:
                    <span class="required">*</span>
                </label>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div style="height:50px;">
                        On Time before
                        <input id="ontime" name="ontime" type="number" style="width:60px;display:inline;"
                                value="30" class="form-control" required>
                        seconds
                    </div>
                    <div style="height:50px;">
                        Almost Delayed after
                        <input id="almostd" name="almostd" type="number" style="width:60px;display:inline;"
                                value="60" class="form-control" required>
                        seconds
                    </div>
                    <div style="height:50px;">
                        Delayed after
                        <input id="delayed" name="delayed" type="number" style="width:60px;display:inline;"
                                value="90" class="form-control" required>
                        seconds
                    </div>
                </div>
            </div>
        -->
            <?php
                for($i=0;$i<6;$i++)
                    echo "<br>"
            ?>

                <div class="form-group" style="margin-bottom:100px;">
                    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" style="text-align:right;">
                        <button type="submit" class="btn btn-success"> {{ __('views.admin.users.edit.save') }}</button>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection

<!-- ******************************** devices ******************************** -->

@section('devices')
<div class="row">
    <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
           width="100%">
        <thead>
        <tr>
            <th>@sortablelink('id',  'ID',['page' => 1])</th>
            <th>@sortablelink('name', 'Name',['page' => 1])</th>
            <th>@sortablelink('function', 'Function',['page' => 1])</th>
            <th>@sortablelink('parent_id', 'Parent ID',['page' => 1])</th>
            <th>@sortablelink('expeditor', 'Expeditor',['page' => 1])</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>

        @foreach($devices as $device)
            <tr>
                <td>{{ $device->id_ }}</td>
                <td>{{ $device->name_}}</td>
                <td>{{ $device->function_ }}</td>
                <td>{{ $device->parent_id_ == 0 ? "" : $device->parent_id_ }}</td>
                <td>{{ $device->expeditor_ }}</td>
                <td>

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection

<!-- ******************************** server ******************************** -->

@section('server')
    server config
    <?php
        for($i=0;$i<30;$i++)
            echo "<br>"
    ?>
@endsection



@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/users/edit.css')) }}
    <style>
        .radio-bump { display:inline-table; width:100px; }
        .radio-bump-time { display:inline-table; width:120px; text-align:right; }
    </style>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/users/edit.js')) }}
@endsection
