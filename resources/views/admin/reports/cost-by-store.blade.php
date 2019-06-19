
<?php 
$adm = $me->hasRole('administrator');
$res = $me->hasRole('reseller');
$stg = $me->hasRole('storegroup');
?>

<table id="report-table" style="display:none;"
			data-toggle="table"
			data-search="true"
			data-filter-control="false" 
			data-show-export="true"
			data-export-data-type="all"
			data-export-types="['excel','csv','xml']"
			data-export-options='{
             "fileName": "admin-report", 
             "ignoreColumn": ["state"]
        }'
			data-click-to-select="true"
			data-pagination="true"
			class="table table-striped table-hover">
    	<thead>
    		<tr>
    		<?php if ($adm) { ?>
    			<th width="15%" data-field="reseller" 	  data-sortable="true">Reseller</th>
    		<?php } 
    		      if ($adm || $res) { ?>
    			<th width="15%" data-field="storegroup" 	  data-sortable="true">Store Group</th>
    		<?php } ?>
    			<th width="15%" data-field="store" 		  data-sortable="true">Store</th>
    			<th width="15%" data-field="license-type"  data-sortable="true">License <br>Type</th>
    			<th width="5%"  data-field="devices-count" data-sortable="true" class="text-right">Total <br>Stations</th>
    			<th width="10%" data-field="license-cost"  data-sortable="true" class="text-right">Price per<br>License</th>
<!--     			<th width="10%" data-field="support-cost"  data-sortable="true" class="text-right">Support <br>Cost</th> -->
    			<th width="10%" data-field="Total Cost" 	  data-sortable="true" class="text-right">Total <br>Price</th>
    		</tr>
    	</thead>
    	<tbody>
    		<?php 
    		foreach($stores as $store) {
    		?>
    		<tr>
    		<?php if ($adm) { ?>
    			<td><?=$store->resellerBName?></td>
    		<?php } 
    		      if ($adm || $res) { ?>
    			<td><?=$store->storegroupBName?></td>
    		<?php } ?>
    			<td><?=$store->storeBName?></td>
    			<td><?=$store->planName?></td>
    			<td><?php echo $store->live ? $store->devicesTotal : "0"; ?></td>
    			<td><?=number_format($store->planCost, 2, '.', '')?></td>
    			<!-- <td><?=number_format($store->devicesTotal * 10, 2, '.', '')?></td> -->
    			<td><?php echo $store->live ? number_format($store->devicesTotal * $store->planCost, 2, '.', '') : "0.00"; ?></td>
    		</tr>
    		<?php } ?>
    	</tbody>
</table>



<style>
    #sync-page-container { margin: 0px 0px 50px; }
    #report-container { min-height:900px; }
    
    #report-table > tbody > tr:nth-child(odd) > td, .table-striped > tbody > tr:nth-child(odd) > th { background: #fcfcfc; }
    #report-table > tbody > tr:nth-child(even) > td, .table-striped > tbody > tr:nth-child(even) > th { background: #f3f3f3; }
    #report-table { margin-top:20px; }
    #report-table th { font-size:16px; font-weight:200 !important; color:#666; padding-bottom:5px; }
    #report-table tbody tr td { font-size:14px; font-weight:200; color:#111; padding-top:10px; }
    #report-table tbody tr td .edit { margin-right:10px; color:#326c7c; }
    #report-table tbody tr td .color-box { display:inline-block; width:5px; height:22px; background:#326c7c; margin-right:10px; opacity:0; }
    #report-table td, #report-table th { border:none; }
    .bootstrap-table { margin-top:-10px; }
    .bootstrap-table tr { border-bottom:1px solid #ccc; }
    .fixed-table-container { border:none; }
    .fixed-table-toolbar { display:none; }
    .fixed-table-pagination .page-list  ul { box-shadow:5px -5px 6px 0px #999; }
    .fixed-table-pagination .page-list  ul li a, .fixed-table-pagination .page-size { font-weight:200; font-size:14px; text-align:center; }
    .fixed-table-pagination .pagination-detail, .fixed-table-pagination ul.pagination { font-weight:300; font-size:14px; }
    .bold-blue { font-weight: bold; color: #0277BD; }
    .text-right { text-align:right; }
</style>


<script>

    $(function () {

    		$('#report-table').bootstrapTable();
		$('#report-table').fadeIn();

		var $toolbar = $('.fixed-table-toolbar');
		$('#toolbarContainer').html($toolbar.fadeIn());
		
		var $exportMenu = $toolbar.find('ul.dropdown-menu')
			$exportMenu.css({ 'width':'50px', 'margin-left':'-108px', 'box-shadow':'-5px 5px 6px 0px #999' });
			$exportMenu.find('li a').css({ 'font-weight':'200', 'font-size':'14px', 'text-align':'center' });

		$('#report-table').on("click", "tr", function (){
        		$(this).toggleClass("bold-blue");
        });

    });

</script>









