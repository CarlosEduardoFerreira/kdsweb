@extends('admin.layouts.admin')

@section('content')
    <!-- page content -->
    <!-- top tiles -->

    <?php

        $roleId = $me->roles[0]["id"];

        // for Admin // resellers, storegroups, stores, employees, devices, licenses
        $dashshow = ['dashshow','dashshow','dashshow','dashhide','dashshow','dashhide'];

        // Bootstrap -------------------------------------------------------------------------- //
        // xs = extra small
        // sm = small
        // md = medium
        // lg = large
        $colwidth = "col-xs-12 col-sm-6 col-md-3 col-lg-3"; // 6 columns
        // -------------------------------------------------------------------------- Bootstrap //

        if ($roleId == 2) { // reseller
            $dashshow = ['dashhide','dashshow','dashshow','dashhide','dashshow','dashhide'];
            $colwidth = "col-xs-12 col-sm-6 col-md-4 col-lg-4"; // 4 columns

        } else if ($roleId == 3) { // storegroup
            $dashshow = ['dashhide','dashhide','dashshow','dashhide','dashshow','dashhide'];
            $colwidth = "col-xs-12 col-sm-6 col-md-6 col-lg-6"; // 3 columns

        } else if ($roleId == 4) { // store
            $dashshow = ['dashhide','dashhide','dashhide','dashhide','dashshow','dashhide'];
            $colwidth = "col-xs-12 col-sm-6 col-md-3 col-lg-3"; // 1 columns
        }

    ?>

    <style>
        .dashshow   { display:inline-table; }
        .dashhide   { display:none; }
        .dashtext   { float:left; }
        .dashnum    { clear:both;}
        .link_page  { cursor:pointer; }
    </style>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $(".link_page").hover(function() {
                    $(this).find(".dashtext").css("color","#2e3f52");
                }, function() {
                    $(this).find(".dashtext").css("color","#76879a");
                }
             );
             $(".link_page").click(function(){
                 var $link = $(this).attr("goto");
                 if($link != "") {
                    window.open($link, "_self");
                }
             });
        });
    </script>

    <div class="row tile_count" style="text-align:center;">
        <div class="tile_stats_count link_page {{ $dashshow[0] }} {{ $colwidth }}" goto="{{ route('admin.resellers', ['adminId' => '0', 'filter' => false]) }}">
            <span class="count_top dashtext" style="font-size:22px;"><i class="fa fa-briefcase"></i> Resellers</span>
            <div class="count green dashnum" style="text-align:right;margin-right:20px;">{{ $counts['resellers'] }}</div>
        </div>
        <div class="tile_stats_count link_page {{ $dashshow[1] }} {{ $colwidth }}" goto="{{ route('admin.storegroups', ['resellerId' => '0', 'filter' => false]) }}">
            <span class="count_top dashtext" style="font-size:22px;"><i class="fa fa-sitemap"></i> Store Groups</span>
            <div class="count green dashnum" style="text-align:right;margin-right:20px;">{{ $counts['storegroups'] }}</div>
        </div>
        <div class="tile_stats_count link_page {{ $dashshow[2] }} {{ $colwidth }}" goto="{{ route('admin.stores', ['storegroupId' => '0', 'filter' => false]) }}">
            <span class="count_top dashtext" style="font-size:22px;"><i class="fa fa-cutlery"></i> Stores</span>
            <div class="count green dashnum" style="text-align:right;margin-right:20px;">{{ $counts['stores'] }}</div>
        </div>
 		<div class="tile_stats_count {{ $dashshow[3] }} {{ $colwidth }}" goto="">
            <span class="count_top dashtext" style="font-size:22px;"><i class="fa fa-users"></i> Employees</span>
            <div class="count green dashnum" style="text-align:right;margin-right:20px;">{{ $counts['employees'] }}</div>
        </div>
        <div class="tile_stats_count {{ $dashshow[4] }} {{ $colwidth }}" goto="">
            <span class="count_top dashtext" style="font-size:22px;"><i class="fa fa-desktop"></i> Devices</span>
            <div class="count green dashnum" style="text-align:right;margin-right:20px;">{{ $counts['devices'] }}</div>
        </div>
        <div class="tile_stats_count {{ $dashshow[5] }} {{ $colwidth }}" goto="">
            <span class="count_top dashtext" style="font-size:22px;"><i class="fa fa-key"></i> Licenses</span>
            <div class="count green dashnum" style="text-align:right;margin-right:20px;">{{ $counts['licenses'] }}</div>
        </div>
    </div>
    <!-- /top tiles -->
    
    
    <div class="row">
    
    		<!-- Log Chart -------------------------------------------------------->
    		<div class="col-md-8 col-sm-8 col-xs-8">
            <div id="log_activity" class="dashboard_graph x_panel tile fixed_height_320 overflow_hidden">

                <div class="row x_title">
                    <div class="col-md-4">
                        <h3>Orders</h3>
                    </div>

                    <div class="col-md-3">
                        <select id="sel_store" name="sel_store" class="form-control select2 pull-right" style="width: 100%" required>
                        <option id="" selected>All stores</option>
                        <?php
                            if (isset($stores)) {
                                foreach ($stores as $k => $v) {
                                    ?> 
                                        <option id="{{ $k }}">{{ $v }}</option> 
                                    <?php
                                }
                            }
                        ?>
                        </select>
                    </div>

                    <div class="col-md-5">
                        <div class="date_piker pull-right"
                             style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                            <span class="date_piker_label">
                                {{ \Carbon\Carbon::now()->addDays(-29)->format('F Y') }} - {{ \Carbon\Carbon::now()->endOfDay()->format('F Y') }}
                            </span>
                            <b class="caret"></b>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="chart demo-placeholder" style="width: 100%; height:240px;"></div>
                </div>
				<!-- 
                <div class="col-md-3 col-sm-3 col-xs-12 bg-white">
                    <div class="x_title">
                        <h2>{{ __('views.admin.dashboard.sub_title_1') }}</h2>
                        <div class="clearfix"></div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-6">
                        <div>
                            <p>{{ __('views.admin.dashboard.log_level_0') }}</p>
                            <div class="">
                                <div class="progress progress_sm" style="width: 76%;">
                                    <div class="progress-bar log-info" role="progressbar" data-transitiongoal="0"></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <p>{{ __('views.admin.dashboard.log_level_1') }}</p>
                            <div class="">
                                <div class="progress progress_sm" style="width: 76%;">
                                    <div class="progress-bar log-notice" role="progressbar" data-transitiongoal="0"></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <p>{{ __('views.admin.dashboard.log_level_2') }}</p>
                            <div class="">
                                <div class="progress progress_sm" style="width: 76%;">
                                    <div class="progress-bar log-warning" role="progressbar" data-transitiongoal="0"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
				-->
                <div class="clearfix"></div>
            </div>
        </div>
    		<!--------------------------------------------------------------------->
    
    		<!-- 
    		<div class="col-md-8 col-sm-8 col-xs-8">
        		<div id="bar_graph">
				<canvas id="canvas" height="240px" width="100%" ></canvas>
        		</div>
		</div>
		-->
		
		<!-- Pie Chart -------------------------------------------------------->
        <div class="col-md-4 col-sm-4 col-xs-4">
        		
            <div id="active_inactive_licenses_graph" class="x_panel tile fixed_height_320 overflow_hidden">
                <div class="x_title">
                    <h2>Licenses Stats</h2>
                    <div class="clearfix"></div>
                </div>
                <br>
                <div class="x_content">
                    <table class="" style="width:100%">
                        <tr>
                            <td>
                                <canvas class="canvasChart" height="140" width="140" style="margin: 15px 10px 10px 0">
                                </canvas>
                            </td>
                            <td>
                                <table class="tile_info">
                                		<tr>
                                        <td>
                                            <p><i class="fa fa-square"></i>
                                                <span class="tile_label">
                                                    Total
                                                </span>
                                            </p>
                                        </td>
                                        <td id="active_inactive_licenses_graph_quantity" style="text-align:right;"></td>
                                    </tr>
                                		<tr>
                                        <td>
                                            <p><i class="fa fa-square"></i>
                                                <span class="tile_label">
                                                    Active
                                                </span>
                                            </p>
                                        </td>
                                        <td id="active_inactive_licenses_graph_active" style="text-align:right;"></td>
                                    </tr>
                                    
                                    <tr>
                                        <td>
                                            <p><i class="fa fa-square"></i>
                                                <span class="tile_label">
                                                    Inactive
                                                </span>
                                            </p>
                                        </td>
                                        <td id="active_inactive_licenses_graph_inactive" style="text-align:right;"></td>
                                    </tr>
                                    
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    

    <div class="row">
        
    </div>
    <br />

    

    

	<!--

    {{--Carousel--}}
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="row x_title">
                    <div class="col-md-6">
                        <h3>
                            {!! __('views.admin.dashboard.sub_title_5',['href'=>'https://photolancer.zone']) !!}
                        </h3>
                    </div>
                </div>
                <div class="x_content">
                    <div class="col-md-12">
                        <div class="jcarousel">
                            <div class="loading">{{ __('views.admin.dashboard.loading') }}</div>
                        </div>

                    </div>
                    <div class="col-md-12 text-center jcarousel-control">
                        <a href="#" class="jcarousel-control-prev">&lsaquo;</a>
                        <a href="#" class="jcarousel-control-next">&rsaquo;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    -->

@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection
