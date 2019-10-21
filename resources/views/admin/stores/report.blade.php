@extends('admin.layouts.report_base')

@section('title',"Store Report")


@section('report')

  
<div class="row" style="min-height:900px; margin:0px;">
    <div id="report_main" style="padding-top:10px;">
    
    		<!-- These inputs are necessary for the report table (file:report.js) -->
            <input id="store-id" type="hidden" value="{{ $store->id }}">
            <input id="store-guid" type="hidden" value="{{ $store->store_guid }}">
    		
    		<div id="report_filter" style="height:60px;padding:10px;font-weight:200;color:#000;font-size:16px;">

    			<input type="hidden" id="report-default-id" value="{{ $reports[0]['id'] }}">
    			
    			<!-- Reports by id ------------------------------------------------------------------------------------------------- -->
    			<!-- Handled by reports.js ----------------------------------------------------------------------------------------- -->
    			<!--  ["title" => "Quantity and Average Time by Order",      "id" => "quantity_and_average_time_by_order"] -->
    				<input type="hidden" id="report-0" value="{{ $reports[0]['id'] }}">
    			
    			<!-- ["title" => "Quantity and Average Time by Item",       "id" => "quantity_and_average_time_by_item"] -->
    				<input type="hidden" id="report-1" value="{{ $reports[1]['id'] }}">
    			
    			<!-- ["title" => "Quantity and Average Time by Item Name",  "id" => "quantity_and_average_time_by_item_name"] -->
    				<input type="hidden" id="report-2" value="{{ $reports[2]['id'] }}">
    			<!-- ------------------------------------------------------------------------------------------------- Reports by id -->
    			
            <button type="button" id="showModalChooseReport" class="btn btn-success" 
            		data-toggle="modal" data-target="#modalChooseReport" style="float:left;font-weight:200;font-size:16px;">
				{{ $reports[0]["title"] }}
			</button>
			
            <div class="dropdown per-page-dropdown" style="float:left;">
                	<button class="btn btn-info dropdown-toggle" type="button" id="dropdownPerPage" data-toggle="dropdown" aria-expanded="true">
                		<span id="per-page-value">10</span> <span>per page</span> <span class="caret"></span>
                	</button>
            		
                	<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownPerPage">
                		<li class="per-page-li"><a class="per-page-a" role="menuitem" tabindex="-1" href="#">10</a></li>
                		<li class="per-page-li"><a class="per-page-a" role="menuitem" tabindex="-1" href="#">25</a></li>
                		<li class="per-page-li"><a class="per-page-a" role="menuitem" tabindex="-1" href="#">50</a></li>
                		<li class="per-page-li"><a class="per-page-a" role="menuitem" tabindex="-1" href="#">100</a></li>
                </ul>
            </div>

			<div id="report-refresh-div" style="float:right;width:60px;height:36px;text-align:right;padding-top:6px;">
				<img id="report-refresh-img" src="/images/refresh-static.png" title="Refresh" style="margin:auto;height:26px;cursor:hand;">
			</div>
			
			<div id="report-download-div" style="">
				<a id="report-export-excel" href="#" style="display:none;">
					<img src="/images/cloud-download.png" title="Download" style="margin:auto;height:30px;cursor:hand;">
				</a>
			</div>
			
			<input type="text" id="daterange" name="daterange" class="btn" value="" />
			
			<button type="button" id="showModalDevices" class="btn btn-primary" 
            		data-toggle="modal" data-target="#modalDevices" style="font-weight:200;font-size:16px;">
				KDS Stations <?php if(count($devices) > 0) { echo "(All)"; } ?>
			</button>
			
    		</div>
    		
    		<!-- Report Table -->
    		<div id="report_div" style="margin:0px 0px 0px 0px;">
    			
    		</div>
    		
    		<!-- Total -->
    		<div id="report-total" style="margin:0px 0px 200px 0px;display:none;">
    			<table style="width:100%;font-weight:200;">
    				<tr height="40px" id="report-total-tr">
					<!-- Total is handled on reports.js -->
    				</tr>
    			</table>
    		</div>
    		
    		<div id="no-data" style="display:none;width:100%;min-height:300px;padding:50px;text-align:center;
    		                  background:#feffff;border:1px solid #666;font-weight:200;font-size:16px;color:#222;">
			There is no data to show. Check the filters.
		</div>
		
		<div id="report-loading" style="display:none;width:100%;height:300px;padding:50px;text-align:center;background:#feffff;border:1px solid #666;">
			<div style="font-weight:200;font-size:16px;color:#666;"> &nbsp; loading...</div>
			<img src="/images/loading.gif" style="width:250px;">
		</div>
    		
    </div>
</div>

@endsection


<div class="modal fade" id="modalChooseReport" role="dialog" data-backdrop="static" aria-labelledby="modalChooseReportLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="height:50px;">
            		<button type="button" id="close-filter-report" class="close" data-dismiss="modal" aria-label="Close">
            			<span aria-hidden="true">&times;</span>
            		</button>
            		<h5 class="modal-title" id="modalChooseReportLabel">Select the Report:</h5>
            </div>
            
            <div class="modal-body">
            		
            		<div class="choose-report-main">
            			<?php $i = 0; ?>
    					@foreach($reports as $report)
                         <div class="choose-report-div">
                            <input type="radio" name="choose_report" id="{{ $report['id'] }}" <?php echo $i == 0 ? 'checked="checked"' : "" ?> />
                            <label class="choose-report" for="{{ $report['id'] }}" data-dismiss="modal">{{ $report["title"] }}</label>
                        </div>
                        <?php $i++; ?>
    					@endforeach
    					<div style="height:20px;"></div>
				</div>
			
            </div>
            
        </div>
    </div>
</div>


<div class="modal fade" id="modalDevices" role="dialog" data-backdrop="static" aria-labelledby="modalDevicesLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="height:50px;">
            		<button type="button" id="close-filter-devices" class="close" data-dismiss="modal" aria-label="Close">
            			<span aria-hidden="true">&times;</span>
            		</button>
            		<h5 class="modal-title" id="modalDevicesLabel">Filter KDS Stations</h5>
            </div>
            <div class="modal-body" style="padding-top:30px;padding-bottom:40px;">
            		<?php $i = 0; ?>
				@foreach($devices as $device)
					<?php if($i == 0) { ?>
					    <div class="[ form-group ]" style="display:inline;bottom:20px;">
                        		<input type="checkbox" name="all-kds-stations" id="all-kds-stations" checked="checked" autocomplete="off" />
                             <div class="[ btn-group ]" style="width:32%;">
                                <label for="all-kds-stations" class="[ btn btn-success checkbox-minus-pLus ]">
                                    <span class="[ glyphicon glyphicon-plus ]"></span>
                                    <span class="[ glyphicon glyphicon-minus ]"></span>
                                </label>
                                <label id="all-kds-stations-label" for="all-kds-stations" class="[ btn btn-default active ]">
                                    All KDS Stations
                                </label>
                             </div>
                        </div>
                        <div style="height:20px;"></div>
					<?php } ?>
				
                    <div class="[ form-group ]" style="display:inline;bottom:20px;">
                    		<input type="checkbox" class="checkbox-device" name="{{ $device->guid }}" deviceId="{{ $device->id }}" checked="checked"
                    			id="{{ $device->guid }}" autocomplete="off" />
                         <div class="[ btn-group ]" style="width:32%;">
                            <label for="{{ $device->guid }}" class="[ btn btn-primary checkbox-minus-pLus ]">
                                <span class="[ glyphicon glyphicon-plus ]"></span>
                                <span class="[ glyphicon glyphicon-minus ]"></span>
                            </label>
                            <label for="{{ $device->guid }}" class="[ btn btn-default active ]">
                                {{ $device->name }}
                            </label>
                         </div>
                    </div>
                    <?php 
                        $i++;
                        if($i % 3 == 0) {
                    ?>
                    	<div style="height:20px;"></div>
                    <?php } ?>
				@endforeach
				
				<?php if($i == 0) { ?>
				    <div style="width:100%;padding:40px;text-align:center;
    		                  background:#feffff;font-weight:200;font-size:16px;color:#222;">
    		                  There are no KDS Stations registered for this store.
    		             </div>
				<?php } ?>
            </div>
            <div class="modal-footer" style="height:66px;">
            		<span style="float:left;">* You must select at least 1 KDS Station.</span>
            		<!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
            		<button id="filter-devices" type="button" class="btn btn-primary" data-dismiss="modal" style="display:none;">Filter</button>
            </div>
        </div>
    </div>
</div>


<?php include "assets/includes/modal.error.php"; ?>


@section('styles')
    @parent
    
    {{ Html::style(mix('assets/admin/css/daterangepicker.css')) }}
    {{ Html::style(mix('assets/admin/css/jquery-ui.min.css')) }}
    {{ Html::style(mix('assets/admin/css/tableexport.css')) }}
    
    <style>
    
    /* modal choose report */
        .choose-report-main div { clear:both; overflow:hidden; }
        .choose-report-main label { width:100%; border-radius:3px; border:1px solid #D1D3D4; font-weight:normal; }
        .choose-report-main input[type="radio"]:empty, .choose-report-main input[type="checkbox"]:empty { display:none; }
        .choose-report-main input[type="radio"]:empty ~ label, .choose-report-main input[type="checkbox"]:empty ~ label {
          position:relative; line-height:2.5em; text-indent:3.25em; margin-top:2em; cursor:pointer;
          -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none; }
        .choose-report-main input[type="radio"]:empty ~ label:before, .choose-report-main input[type="checkbox"]:empty ~ label:before {
          position:absolute; display:block; top:0; bottom:0; left:0; content:''; width:2.5em; 
          background:#D1D3D4; border-radius: px 0 0 3px; }
        .choose-report-main input[type="radio"]:hover:not(:checked) ~ label, 
        .choose-report-main input[type="checkbox"]:hover:not(:checked) ~ label { color:#888; }
        .choose-report-main input[type="radio"]:hover:not(:checked) ~ label:before,
        .choose-report-main input[type="checkbox"]:hover:not(:checked) ~ label:before { content:'\2714'; text-indent:.9em; color:#C2C2C2; }
        .choose-report-main input[type="radio"]:checked ~ label, .choose-report-main input[type="checkbox"]:checked ~ label { color: #777; }
        .choose-report-main input[type="radio"]:checked ~ label:before,
        .choose-report-main input[type="checkbox"]:checked ~ label:before {
          content:'\2714'; text-indent:.9em; color:#333; background-color:#ccc; }
        .choose-report-main input[type="radio"]:focus ~ label:before,
        .choose-report-main input[type="checkbox"]:focus ~ label:before { box-shadow:0 0 0 3px #999; }
        .choose-report-main-default input[type="radio"]:checked ~ label:before,
        .choose-report-main-default input[type="checkbox"]:checked ~ label:before { color:#333; background-color:#ccc; }
        .choose-report-main-primary input[type="radio"]:checked ~ label:before,
        .choose-report-main-primary input[type="checkbox"]:checked ~ label:before { color:#fff; background-color:#337ab7; }
        .choose-report-div input[type="radio"]:checked ~ label:before,
        .choose-report-div input[type="checkbox"]:checked ~ label:before { color:#fff; background-color:#5cb85c; }
        .choose-report-main-danger input[type="radio"]:checked ~ label:before,
        .choose-report-main-danger input[type="checkbox"]:checked ~ label:before { color:#fff; background-color:#d9534f; }
        .choose-report-main-warning input[type="radio"]:checked ~ label:before,
        .choose-report-main-warning input[type="checkbox"]:checked ~ label:before { color:#fff; background-color:#f0ad4e; }
        .choose-report-main-info input[type="radio"]:checked ~ label:before,
        .choose-report-main-info input[type="checkbox"]:checked ~ label:before { color:#fff; background-color:#5bc0de; }
        
    /* modal devices filter */
        .form-group input[type="checkbox"] { display: none; }
        .form-group input[type="checkbox"] + .btn-group > label span { width: 20px; }
        .form-group input[type="checkbox"] + .btn-group > label span:first-child { display: none; }
        .form-group input[type="checkbox"] + .btn-group > label span:last-child { display: inline-block; }
        .form-group input[type="checkbox"]:checked + .btn-group > label span:first-child { display: inline-block; }
        .form-group input[type="checkbox"]:checked + .btn-group > label span:last-child { display: none; }
        .form-group .checkbox-minus-pLus { height:34px; }
        
    /* report filter per page */
        .per-page-dropdown .dropdown-menu { min-width: 130px !important; }
        .per-page-dropdown .btn { min-width: 130px !important; font-weight:200; font-size:16px; }
        .per-page-dropdown li { min-width: 130px !important; text-align:center; }
        .per-page-dropdown a  { font-weight:200; font-size:14px; }

    /* report table */
        .google-visualization-table { width:100% !important; margin:0 !important; }
        .google-visualization-table-div-page { height:40px; background:#86b8dd !important; color:#fff !important;  
            vertical-align:middle !important; margin:0px auto !important; }
        
    /* report headers */
        .tblHeaderClass th { color:#fff !important; text-align:center !important; background:#86b8dd !important; }
        .google-visualization-table-table tr { height:40px; }
        .google-visualization-table-td { text-align:center !important; }
        
    /* report pagination prev next */
        .goog-custom-button { background:#eee !important; padding:1px 6px !important; }
        .goog-custom-button-outer-box { border:none !important; }
        .goog-custom-button-inner-box { border:none !important; }
        .goog-custom-button-collapse-right { border-radius:4px 0px 0px 4px; margin-top:10px !important; margin-left:20px !important; }
        .goog-custom-button-collapse-left  { border-radius:0px 4px 4px 0px; margin-top:10px !important; }
        
    /* report page numbers */
        .google-visualization-table-page-numbers { margin-left:10px !important; margin-top:6px !important; }
        .google-visualization-table-page-numbers a { border:none !important; padding:0px 6px !important; color:#fff !important;
            text-weight:400 !important; opacity:0.6 !important; background:transparent !important; }
        .google-visualization-table-page-numbers .current { opacity:1 !important; }
        .google-visualization-table-page-numbers .undefined:hover { opacity:1 !important; }
        
    /* report total */
        .report-total-tds { border:1px solid #ccc; }
        
    /* report date range */
        #daterange { margin-right:auto !important; width:280px; text-align:center; border:1px solid #ddd; font-size:16px; font-weight:200; color:#333; }
        
    /* filters and refresh button */
        #report_filter { white-space: nowrap; }
        #report-refresh-div { position:relative; }

    /* report download button */
        #report-download-div { float:right; width:60px; height:36px; text-align:right;padding-top:3px; position:relative;}
    </style>
@endsection


@section('scripts')
    @parent
    
    {{ Html::script(mix('assets/admin/js/jquery-3.3.1.min.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-3.3.7.min.js')) }}
    {{ Html::script(mix('assets/admin/js/google.charts.js')) }}
	{{ Html::script(mix('assets/admin/js/moment.min.js')) }}
	{{ Html::script(mix('assets/admin/js/daterangepicker.js')) }}
	{{ Html::script(mix('assets/admin/js/jquery-ui.min.js')) }}
	{{ Html::script(mix('assets/admin/js/FileSaver.min.js')) }}
	{{ Html::script(mix('assets/admin/js/Blob.min.js')) }}
	{{ Html::script(mix('assets/admin/js/xlsx-core.min.js')) }}
	{{ Html::script(mix('assets/admin/js/tableexport.js')) }}
	{{ Html::script(mix('assets/admin/js/report.js')) }}
	
	<script>
        	$(function(){
        		$('#report-refresh-img').tooltip();
        		$('#report-export-excel img').tooltip();
        	});
	</script>
	
@endsection





