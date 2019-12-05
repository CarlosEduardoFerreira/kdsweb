<?php 
	$adm = $me->hasRole('administrator');
	if (!$adm) {
		die("You are not allowed to access this page");
	}
?>

<div id="statement-filters">
	Month:
    <select id="statement-filter-month" name="statement-filter-month">
    <?php 
    echo "<option value=\"" . date('Y-m') . "\">Current</option>";
    for ($i = 1; $i <= 12; $i++) {
        $monthValue = date("Y-m", strtotime( date('Y-m-01')." -$i months"));
        $monthDisplay = date("M Y", strtotime( date('Y-m-01')." -$i months"));
        echo "<option value=\"$monthValue\">$monthDisplay</option>";
    }
    ?>
    </select>
</div>

<table id="report-table" style="display:none;"
			data-toggle="table"
			data-search="true"
			data-filter-control="false" 
			data-show-export="true"
			data-export-data-type="all"
			data-export-types="['excel','csv','xml']"
			data-export-options='{
             		"fileName": "statements"
        		}'
			data-click-to-select="true"
			data-pagination="true"
			class="table table-striped table-hover">
    	<thead>
    		<tr>
     			<th width="15%" data-field="reseller" 	data-sortable="true">Reseller</th>
    			<th width="15%" data-field="storegroup" data-sortable="true">Store Group</th>
    			<th width="15%" data-field="store" 		data-sortable="true">Store</th>
				<th width="5%"  data-field="licenses"  	data-sortable="true">Licenses</th>
				<th width="10%" data-field="price"  	data-sortable="true">Price</th>
				<th width="5%" data-field="freq"  		data-sortable="true">Frequency</th>
				<th width="35%" data-field="remarks"  	data-sortable="true">Remarks</th>
    		</tr>
    	</thead>
    	<tbody>
			<?php 
				if ($report !== false) {
					foreach($report as $reseller) {
						$store_groups = $reseller->getStoreGroups();
						if (count($store_groups) > 0) {
							foreach ($store_groups as $store_group) {
								$stores = $store_group->getStores();
								if (count($stores) > 0) {
									foreach ($stores as $store) {
										echo "<tr>";
										echo "<td>" . $reseller->getBusinessName() . "</td>";
										echo "<td>" . $store_group->getBusinessName() . "</td>";
										echo "<td>" . $store->getBusinessName() . "</td>";
										echo "<td>" . $store->getLicenses() . "</td>";
										echo "<td>US$" . number_format($store->getTotalCost(), 2) . "</td>";
										echo "<td>" . $reseller->getFrequencyText() . "</td>";
										echo "<td>" . $store->getRemarks() . "</td>";
										echo "</tr>";
									}
								}
							}
						}
					}
				}
    		?>
    	</tbody>
</table>


<style>
    #sync-page-container { margin: 0px 0px 50px; }
    #report-container { min-height:900px; }
    
    .fixed-table-toolbar .search .form-control { border-radius:5px; }
    #statement-filters { display:none; font-weight:300; font-size:14px; float:right; margin-top:-9px; width:45%; height:100%; }
    #statement-filter-month { height:34px; min-width:150px; background-color:#FFF; border-color:#ccc; }
    
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
    
    .licenses-total { text-align:center; }
</style>


<script>

    $(function () {

    	var token = { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }

	 	$('#report-table').fadeIn();

	 	var $filters = $('#statement-filters').css({'width':'250px', 'margin-top':'10px', 'margin-right':'10px'});

	 	var $toolbar = $('.fixed-table-toolbar').css({'width':'510px', 'float':'right'});

	 	$toolbar.find('.export').css({'width':'40px'});
		
	 	$('#toolbarContainer').append($toolbar.append($filters.fadeIn()).fadeIn());

		$('#report-table').on("click", "tr", function() {
        		$(this).toggleClass("bold-blue");
        });

		// Set filter month
        $('#statement-filter-month').val("<?= $month ?>");
        
        // Set filter search
        $(function(){
        	$(".fixed-table-toolbar .search .form-control").val("<?= $search ?>").blur();
		});


		f$trDataLength = $('.tr-data').length;
		$trDataLoaded = 0;

		$imgDownload = $('<img src="/images/cloud-download.png" title="Download">')
    			.css({'display':'none','margin-left':'10px','height':'30px','cursor':'pointer'});
    		$imgLoading = $('<img src="/images/loading2.gif" title="Please Wait">')
    			.css({'display':'none','margin-left':'10px','height':'30px','cursor':'pointer'});
    	
    		$toolbar.find('.export').html($imgDownload.fadeIn());
    		$toolbar.find('.export').append($imgLoading);

    		$imgDownload.click(function(){
    			$imgDownload.hide();
    			$imgLoading.show();
    			
    			$.ajax({
        		 	headers: token,
                url: 'reports/getStatementListExcelFile',
                type: 'GET',
                data: {
                		search: $('.fixed-table-toolbar .search .form-control').val(),
                		month: $('#statement-filter-month').val()
                	},
                success: function (response) {
                		$imgLoading.hide();
                		$imgDownload.fadeIn();
                		
                	    window.location.href = response;

                	    downloadCompleted(response);
                }
            });
    		});

		$loaded = false;
		function handleTrDataLoaded() {
			$trDataLoaded++;
			if($trDataLength <= $trDataLoaded) {
				$loaded = true;
				$imgLoading.hide();
				$imgDownload.fadeIn();
			}
		}
		
		$('#statement-filter-month').change(function(){
			var month = $('#statement-filter-month').val();
			var search = $('.fixed-table-toolbar .search .form-control').val();
			
			filter = "?month=" + month + "&search=" + search;
			
			SyncPage.getContent("{{ route('admin.reports.costByStatement') }}" + filter, $('#reports-container'), '');
		});


		function downloadCompleted($file) {
			$.ajax({
			    xhr: function() {
			       var xhrd = new window.XMLHttpRequest();
			       xhrd.addEventListener("progress", function(evt) {
			            if(evt.lengthComputable) {
			              var percentComplete = evt.loaded / evt.total;
			              console.log("23: " + percentComplete + ":25");
			            }
			       }, false);
			       return xhrd;
			    },
			    complete: function() {
			        $.ajax({
                		 	headers: token,
                        url: 'reports/downloadCompleted',
                        type: 'POST',
                        data: { file: $file },
                        success: function (response) {}
               		});
			    }
			});
		}
		

    });

</script>









