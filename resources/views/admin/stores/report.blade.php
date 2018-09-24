@extends('admin.layouts.report_base')

@section('title',"Store Report")


@section('report')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  
<div class="row" style="min-height:900px; margin:0px;">
    <div id="report_main" style="padding-top:10px;">
    
    		<!-- This input is necessary for the report table (file:report.js) -->
    		<input id="store-id" type="hidden" value="{{ $store->id }}">
    		
    		<div id="report_filter" style="height:60px;padding:10px;font-weight:200;color:#000;font-size:16px;">
    		
            <button type="button" id="showModalDevices" class="btn btn-primary" 
            		data-toggle="modal" data-target="#modalDevices" style="font-weight:200;font-size:16px;">
				KDS Stations <?php if(count($devices) > 0) { echo "(All)"; } ?>
			</button>
			
			<?php if(count($devices) > 0) { ?>
				<input type="text" name="daterange" value="" style="width:280px;float:right;text-align:center;" />
			<?php } ?>
    		
    		</div>
    		
    		<!-- Report Table -->
    		<div id="report_div" style="margin:0px 0px 0px 0px;">
    			
    		</div>
    		
    		<!-- Total -->
    		<div id="report-total" style="margin:0px 0px 0px 0px;display:none;">
    			<table style="width:100%;font-weight:200;">
    				<tr height="40px">
    					<td width="20%" class="report-total-tds" style="padding-left:15px;">Total</td>
    					<td width="15%" class="report-total-tds" id="total-orders" style="text-align:center;">0</td>
    					<td width="15%" class="report-total-tds" id="total-items" style="text-align:center;">0</td>
    					<td width="20%" class="report-total-tds" id="total-orders-avg-time" style="text-align:center;">0</td>
    					<td width="20%" class="report-total-tds" id="total-items-avg-time" style="text-align:center;">0</td>
    					<td width="10%" class="report-total-tds" id="total-actives"  style="text-align:center;">&nbsp;</td>
    				</tr>
    			</table>
    		</div>
    		
    		
    		<div id="no-data" style="display:none;width:100%;padding:50px;text-align:center;
    		                  background:#feffff;border:1px solid #666;font-weight:200;font-size:16px;color:#222;">
				There is no data to show. Check the filters.
			</div>
    		
    </div>
</div>

@endsection


<div class="modal fade" id="modalDevices" role="dialog" data-backdrop="static" aria-labelledby="modalDevicesLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="height:50px;">
            		<button type="button" id="close-filter-devices" class="close" data-dismiss="modal" aria-label="Close">
            			<span aria-hidden="true">&times;</span>
            		</button>
            		<h5 class="modal-title" id="modalDevicesLabel">Filter KDS Stations</h5>
            </div>
            <div class="modal-body">
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


@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/daterangepicker.css')) }}
    <style>
        /* modal */
        .form-group input[type="checkbox"] { display: none; }
        .form-group input[type="checkbox"] + .btn-group > label span { width: 20px; }
        .form-group input[type="checkbox"] + .btn-group > label span:first-child { display: none; }
        .form-group input[type="checkbox"] + .btn-group > label span:last-child { display: inline-block; }
        .form-group input[type="checkbox"]:checked + .btn-group > label span:first-child { display: inline-block; }
        .form-group input[type="checkbox"]:checked + .btn-group > label span:last-child { display: none; }
        .form-group .checkbox-minus-pLus { height:34px; }

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
        
        /* reporrt total */
        .report-total-tds { border:1px solid #ccc; }
        
    </style>
@endsection


@section('scripts')
    @parent
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	{{ Html::script(mix('assets/admin/js/report.js')) }}
	{{ Html::script(mix('assets/admin/js/moment.min.js')) }}
	{{ Html::script(mix('assets/admin/js/daterangepicker.js')) }}
@endsection





