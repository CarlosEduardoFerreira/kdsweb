<div class="row" style="min-height:900px;">

    <table id="devices-table" class="table table-striped dt-responsive nowrap">
    		
    </table>

</div>


<!-- Modal Device Settings -->
<div class="modal fade" id="modalDeviceSettings" tabindex="-1" role="dialog" aria-labelledby="modalDeviceSettings" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content">
        		<div class="modal-header">
        			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
            			<span aria-hidden="true">&times;</span>
            		</button>
        			<h3 class="modal-title" id="modalLongTitle">KDS Station Settings</h3>
        		</div>
        		<div id="are-you-sure" class="modal-body">
        		
        			<div class="card card-outline-secondary">
                    
                    <div class="card-body">
                        <form id="device-settings-form" name="device-settings-form" class="form" role="form" autocomplete="off">
                        
                        		<input type="hidden" id="device-settings-store-guid" name="device-settings-store-guid">
                        		<input type="hidden" id="device-settings-device-guid" name="device-settings-device-guid">
                        		<input type="hidden" id="device-settings-device-screen-id" name="device-settings-device-screen-id">
                        
                        		<div id="device-settings-basic" class="col-lg-12">
                            
                                <div class="form-group row">
                                
                                		<label class="col-lg-2 col-form-label form-control-label">Name</label>
                                    	<div class="col-lg-4">
                                     	<input id="device-settings-name" name="device-settings-name" class="form-control" type="text">
                                     </div>
                                		
                                		<label class="col-lg-3 col-form-label form-control-label">ID</label>
                                    	<div class="col-lg-3">
                                     	<input type="number" min="0" id="device-settings-id" name="device-settings-id" 
                                     		class="form-control num-99999" style="width:100px;text-align:center;"
                                     		data-placement="bottom">
                                    	</div>
                                    	
                                 </div>
                                 
                                 <div class="form-group row">
                                		
                                		<label class="col-lg-2 col-form-label form-control-label">Function</label>
                                    	<div class="col-lg-3">
                                     	<select id="device-settings-function" name="device-settings-function" class="form-control selectpicker">
                                            <option value="EXPEDITOR">Expeditor</option>
                                            <option value="PREPARATION">Preparation</option>
                                            <option value="BACKUP_PREP">Backup/Prep</option>
                                            <option value="BACKUP_EXPE">Backup/Exp</option>
                                            <option value="MIRROR">Mirror</option>
                                            <option value="DUPLICATE">Duplicate</option>
                                            <option value="WORKLOAD">Workload</option>
                                        	</select>
                                    	</div>
                                     
                                     <label class="col-lg-4 col-form-label form-control-label">Expeditors</label>
                                    	<div class="col-lg-3">
                                     	<select id="device-settings-expeditor" name="device-settings-expeditor" 
                                     		class="selectpicker" multiple data-live-search="true">
                                     	
                                         </select>
                                     </div>
                                    	
                                 </div>
                                 
                                 <div class="form-group row">
                                 
                                    	<label class="col-lg-2 col-form-label form-control-label">Parent ID</label>
                                    	<div class="col-lg-2">
                                     	<select id="device-settings-parent-id" name="device-settings-parent-id" 
                                     		class="selectpicker" data-live-search="true">
    
                                         </select>
                                     </div>
                                     
                                     <label class="col-lg-5 col-form-label form-control-label">Host / XML File Order</label>
                                    	<div class="col-lg-3">
                                     	<input type="number" min="0" id="device-settings-host" name="device-settings-host" 
                                     		class="form-control num-99999" style="width:100px;text-align:center;">
                                     </div>
                                     
                                 </div>
                                 
                                 <div class="form-group row">
                                		
                                    	<label class="col-lg-2 col-form-label form-control-label">Orders Columns</label>
                                    	<div class="col-lg-2">
                                        <select id="device-settings-orders-columns" name="device-settings-orders-columns" class="form-control selectpicker">
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                    	</div>
                                    
                                    	<label class="col-lg-5 col-form-label form-control-label">Sort Orders</label>
                                    	<div class="col-lg-3">
                                        	<select id="device-settings-sort-orders" name="device-settings-sort-orders" class="form-control selectpicker">
                                            <option value="WAITING_TIME_ASCEND">Waiting Time Asc.</option>
                                            <option value="WAITING_TIME_DESCEND">Waiting Time Desc.</option>
                                            <option value="ORDER_NUMBER_ASCEND">Order Number Asc.</option>
                                            <option value="ORDER_NUMBER_DESCEND">Order Number Desc.</option>
                                            <option value="ITEM_COUNT_ASCEND">Item Count Asc.</option>
                                            <option value="ITEM_COUNT_DESCEND">Item Count Desc.</option>
                                            <option value="PREPARATION_TIME_ASCEND">Preparation Time Asc.</option>
                                            <option value="PREPARATION_TIME_DESCEND">Preparation Time Desc.</option>
                                        	</select>
                                    	</div>
                                    	
                                </div>
                            
                            </div>
                            
                            <hr class="separator-1" />
                            
                            {{-- Summary ----------------------------------------------------------------------------------------- --}}
                            
                            <div id="device-settings-feature-summary" class="col-lg-12 device-settings-feature-title" scroll-to="240">
                            
                                <label class="col-lg-3 col-form-label form-control-label label-left label-group">Summary</label>
                                	<div class="col-lg-8">
                                    	<label class="col-lg-11 col-form-label form-control-label">Enable</label>
                                		<label class="switch">
                                     	<input type="checkbox" id="device-settings-summary-enable" name="device-settings-summary-enable" 
                                     		class="device-settings-feature-enable">
                                     	<span class="slider round device-settings-switch-slider" ></span>
                                    </label>
                                	</div>
                                	
                                	<div class="col-lg-1 device-settings-arrow">
                                		<i class="fa fa-angle-down fa-lg"></i>
                                		<i class="fa fa-angle-up fa-lg" style="display:none;"></i>
                                	</div>
                                	
                            	</div>
                            	
                            	<div id="device-settings-feature-summary-config" class="col-lg-12 device-settings-feature-config">
                            		
                                	<div class="form-group row">
                                    	<label class="col-lg-3 col-form-label form-control-label">Show as</label>
                                    	<div class="col-lg-9" style="padding:0;">
                                    		<div class="col-lg-9">
                                    		</div>
                                        	<div class="col-lg-3" style="padding:0;">
                                            	<select id="device-settings-summary-type" name="device-settings-summary-type" class="form-control selectpicker">
                                                	<option value="0">Item</option>
                                                	<option value="1">Item + Condiment</option>
                                                	<option value="2">Ingredient</option>
                                                	<option value="3">Ingredient + Condiment</option>
                                            	</select>
                                         </div>
                                    	</div>
                                	</div>
                             	
                             </div>
                             
                             {{-- ----------------------------------------------------------------------------------------- Summary --}}
                             
                             <hr class="separator-1" />
                            	
                            	{{-- Line Display ------------------------------------------------------------------------------------- --}}
                            	
                            	<div id="device-settings-feature-line-display" class="col-lg-12 device-settings-feature-title" scroll-to="350">
                            	
                                	<label class="col-lg-3 col-form-label form-control-label label-left label-group device-settings-line-display-text">
                                		Line Display
                                	</label>
                                	<div class="col-lg-8 device-settings-line-display-hide">
                                    	<label class="col-lg-11 col-form-label form-control-label">Enable</label>
                                		<label class="switch">
                                     	<input type="checkbox" id="device-settings-line-display-enable" name="device-settings-line-display-enable" 
                                     		class="device-settings-feature-enable">
                                     	<span class="slider round device-settings-switch-slider" ></span>
                                    </label>
                                	</div>
                                	
                                	<div class="col-lg-1 device-settings-arrow device-settings-line-display-hide device-settings-line-display-arrow">
                                		<i class="fa fa-angle-down fa-lg"></i>
                                		<i class="fa fa-angle-up fa-lg" style="display:none;"></i>
                                	</div>
                                	
                            	</div>
                            
                            	<div id="device-settings-feature-line-display-config" class="col-lg-12 device-settings-feature-config">
                            	
                            		{{-- Line Display Column 1 --}}
                            		<div class="form-group row separator-bottom">
                                    	<label class="col-lg-3 col-form-label form-control-label">Column 1</label>
                                    	<div class="col-lg-9" style="padding:0;">
                                    		<div class="col-lg-4">
                                    		</div>
                                    		<div class="col-lg-5">
                                            	<select id="device-settings-line-display-column-1-text" name="device-settings-line-display-column-1-text" 
                                            		class="form-control selectpicker">
                                                	
                                            	</select>
                                        	</div>
                                        	<div class="col-lg-3" style="padding:0;">
                                            	<select id="device-settings-line-display-column-1-percent" name="device-settings-line-display-column-1-percent"
                                            		class="form-control selectpicker">
                                                	
                                            	</select>
                                         </div>
                                    	</div>
                                	</div>
                                	
                                	{{-- Line Display Column 2 --}}
                                	<div class="form-group row separator-bottom">
                                    	<label class="col-lg-3 col-form-label form-control-label">Column 2</label>
                                    	<div class="col-lg-9" style="padding:0;">
                                    		<div class="col-lg-4">
                                    		</div>
                                    		<div class="col-lg-5">
                                            	<select id="device-settings-line-display-column-2-text" name="device-settings-line-display-column-2-text" 
                                            		class="form-control selectpicker">
                                                	
                                            	</select>
                                        	</div>
                                        	<div class="col-lg-3" style="padding:0;">
                                            	<select id="device-settings-line-display-column-2-percent" name="device-settings-line-display-column-2-percent"
                                            		class="form-control selectpicker">
                                                	
                                            	</select>
                                         </div>
                                    	</div>
                                	</div>
                                	
                                	{{-- Line Display Column 3 --}}
                                	<div class="form-group row separator-bottom">
                                    	<label class="col-lg-3 col-form-label form-control-label">Column 3</label>
                                    	<div class="col-lg-9" style="padding:0;">
                                    		<div class="col-lg-4">
                                    		</div>
                                    		<div class="col-lg-5">
                                            	<select id="device-settings-line-display-column-3-text" name="device-settings-line-display-column-3-text" 
                                            		class="form-control selectpicker">
                                                	
                                            	</select>
                                        	</div>
                                        	<div class="col-lg-3" style="padding:0;">
                                            	<select id="device-settings-line-display-column-3-percent" name="device-settings-line-display-column-3-percent"
                                            		class="form-control selectpicker">
                                                	
                                            	</select>
                                         </div>
                                    	</div>
                                	</div>
                                	
                                	{{-- Line Display Column 4 --}}
                                	<div class="form-group row separator-bottom">
                                    	<label class="col-lg-3 col-form-label form-control-label">Column 4</label>
                                    	<div class="col-lg-9" style="padding:0;">
                                    		<div class="col-lg-4">
                                    		</div>
                                    		<div class="col-lg-5">
                                            	<select id="device-settings-line-display-column-4-text" name="device-settings-line-display-column-4-text" 
                                            		class="form-control selectpicker">
                                                	
                                            	</select>
                                        	</div>
                                        	<div class="col-lg-3" style="padding:0;">
                                            	<select id="device-settings-line-display-column-4-percent" name="device-settings-line-display-column-4-percent"
                                            		class="form-control selectpicker">
                                                	
                                            	</select>
                                         </div>
                                    	</div>
                                	</div>
                                	
                                	{{-- Line Display Bump Transfer Device ID --}}
                                	<div class="form-group row" style="padding-top:25px;">
                                    	<label class="col-lg-3 col-form-label form-control-label">Bump Transfer Device ID</label>
                                    	<div class="col-lg-9" style="padding:0;">
                                    		<div class="col-lg-9">
                                    		</div>
                                        	<div class="col-lg-3" style="padding:0;">
                                            	<select id="device-settings-line-display-transfer-device-id" name="device-settings-line-display-transfer-device-id" 
                                            		class="form-control selectpicker">

                                            	</select>
                                         </div>
                                    	</div>
                                	</div>
                                	
                            	</div>
                            	
                            	{{-- ------------------------------------------------------------------------------------- Line Display --}}
                            	
                            	<hr class="separator-1" />
                            	
                            	{{-- Printer ------------------------------------------------------------------------------------------ --}}
                            	
                            	<div id="device-settings-feature-printer" class="col-lg-12 device-settings-feature-title" scroll-to="450">
                            	
                                	<label class="col-lg-3 col-form-label form-control-label label-left label-group">Printer</label>
                                	<div class="col-lg-8">
                                    	<label class="col-lg-11 col-form-label form-control-label">Enable</label>
                                		<label class="switch">
                                     	<input type="checkbox" id="device-settings-printer-network-enable" name="device-settings-printer-network-enable" 
                                     		class="device-settings-feature-enable">
                                     	<span class="slider round device-settings-switch-slider" ></span>
                                    </label>
                                	</div>
                                	
                                	<div class="col-lg-1 device-settings-arrow">
                                		<i class="fa fa-angle-down fa-lg"></i>
                                		<i class="fa fa-angle-up fa-lg" style="display:none;"></i>
                                	</div>
                            	
                            	</div>
                            	
                            	<div id="device-settings-feature-printer-config" class="col-lg-12 device-settings-feature-config">
                            	
                                	<div class="form-group row">
                                		<label class="col-lg-5 col-form-label form-control-label">IP Address</label>
                                    	<div class="col-lg-4">
                                     	<input type="text" id="device-settings-printer-network-ip" required pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$"
                                     		name="device-settings-printer-network-ip" class="form-control center">
                                     </div>
                                		
                                		<label class="col-lg-1 col-form-label form-control-label">Port</label>
                                    	<div class="col-lg-2">
                                     	<input type="number" min="0" id="device-settings-printer-network-port" 
                                     		name="device-settings-printer-network-port" class="form-control" 
                                     		style="width:100px;text-align:center;" readonly>
                                    	</div>
                                 </div>
                                 
                                 <div class="form-group row">
                                		<label class="col-lg-5 col-form-label form-control-label printer-when-text">
                                			When this KDS Station receives a <b style="color:#26A69A;">new</b> order
                                		</label>
                                    	
                                    	<div class="col-lg-7 v-center-padding">
                                     	<label class="switch">
                                         	<input type="checkbox" id="device-settings-printer-network-new-enable" 
                                         		name="device-settings-printer-network-new-enable">
                                         	<span class="slider round device-settings-switch-slider" ></span>
                                        </label>
                                     </div>
                                 </div>
                                	
                                	<div class="form-group row">
                                		<label class="col-lg-5 col-form-label form-control-label printer-when-text">
                                			When this KDS Station <b style="color:#039BE5;">bumps</b> an order
                                		</label>
                                    	
                                    	<div class="col-lg-7 v-center-padding">
                                     	<label class="switch">
                                         	<input type="checkbox" id="device-settings-printer-network-bump-enable" 
                                         		name="device-settings-printer-network-bump-enable">
                                         	<span class="slider round device-settings-switch-slider" ></span>
                                        </label>
                                    	</div>
                                 </div>
                             
                             </div>
                            	
                            	{{-- ------------------------------------------------------------------------------------------ Printer --}}
                            	
                            	<hr class="separator-1" />
                            
                            	{{-- Order Status ------------------------------------------------------------------------------------- --}}
                            
                            	<div id="device-settings-feature-order-status" class="col-lg-12 device-settings-feature-title" scroll-to="560">
                            	
                            		<label class="col-lg-11 col-form-label form-control-label label-left label-group">Order Status</label>
                            		
                            		<div class="col-lg-1 device-settings-arrow">
                                		<i class="fa fa-angle-down fa-lg"></i>
                                		<i class="fa fa-angle-up fa-lg" style="display:none;"></i>
                                	</div>
                                	
                            	</div>
                            
                            	<div id="device-settings-feature-order-status-config" class="col-lg-12 device-settings-feature-config">
                            	
                                	<div class="form-group row">
                                		<div class="col-lg-3">
                                		</div>
                                		<div class="col-lg-3 status-box">
                                    		<div class="col-lg-12 status-box-card" style="background:#f5f5f5;">
                                    			<label class="col-lg-12 col-form-label form-control-label label-left">On Time Before</label>
                                            	<div class="col-lg-6">
                                                	<input type="number" min="0" id="device-settings-order-status-ontime" 
                                                		name="device-settings-order-status-ontime" class="form-control order-status-input num-99999">
                                            	</div>
                                            	<label class="col-lg-6 col-form-label form-control-label label-left">seconds</label>
                                    		</div>
                                		</div>
                                		<div class="col-lg-3 status-box">
                                    		<div class="col-lg-12 status-box-card" style="background:#FFCC80;">
                                    			<label class="col-lg-12 col-form-label form-control-label label-left">Almost Delayed After</label>
                                            	<div class="col-lg-6">
                                                	<input type="number" min="0" id="device-settings-order-status-almost" 
                                                		name="device-settings-order-status-almost" class="form-control order-status-input num-99999">
                                            	</div>
                                            	<label class="col-lg-6 col-form-label form-control-label label-left">seconds</label>
                                        	</div>
                                    	</div>
                                    	<div class="col-lg-3 status-box">
                                        	<div class="col-lg-12 status-box-card" style="background:#FF5252;">
                                    			<label class="col-lg-12 col-form-label form-control-label label-left">Delayed After</label>
                                            	<div class="col-lg-6">
                                                	<input type="number" min="0" id="device-settings-order-status-delayed" 
                                                		name="device-settings-order-status-delayed" class="form-control order-status-input num-99999">
                                            	</div>
                                            	<label class="col-lg-6 col-form-label form-control-label label-left">seconds</label>
                                        	</div>
                                    	</div>
                                	</div>
                                	
                            	</div>
                            	
                            	{{-- ------------------------------------------------------------------------------------- Order Status --}}
                            
                            	<hr class="separator-1" />
                            	
                            	{{-- Order header ------------------------------------------------------------------------------------- --}}
                            	
                            	<div id="device-settings-feature-order-header" class="col-lg-12 device-settings-feature-title" scroll-to="670">
                            	
                            		<label class="col-lg-11 col-form-label form-control-label label-left label-group">Order Header</label>
                            		
                            		<div class="col-lg-1 device-settings-arrow">
                                		<i class="fa fa-angle-down fa-lg"></i>
                                		<i class="fa fa-angle-up fa-lg" style="display:none;"></i>
                                	</div>
                                	
                            	</div>
                            	
                            	<div id="device-settings-feature-order-header-config" class="col-lg-12 device-settings-feature-config">
                            	
                                	<div class="form-group row separator-bottom">
                                    	<label class="col-lg-3 col-form-label form-control-label">Top Left</label>
                                    	<div class="col-lg-9" style="padding:0;">
                                    		<div class="col-lg-9">
                                    		</div>
                                        	<div class="col-lg-3" style="padding:0;">
                                            	<select id="device-settings-order-header-top-left" name="device-settings-order-header-top-left" 
                                            		class="form-control selectpicker">
                                                	
                                            	</select>
                                         </div>
                                    	</div>
                                	</div>
                                	<div class="form-group row separator-bottom">
                                    	<label class="col-lg-3 col-form-label form-control-label">Top Right</label>
                                    	<div class="col-lg-9" style="padding:0;">
                                    		<div class="col-lg-9">
                                    		</div>
                                        	<div class="col-lg-3" style="padding:0;">
                                            	<select id="device-settings-order-header-top-right" name="device-settings-order-header-top-right" 
                                            		class="form-control selectpicker">
                                                	
                                            	</select>
                                         </div>
                                    	</div>
                                	</div>
                                	<div class="form-group row separator-bottom">
                                    	<label class="col-lg-3 col-form-label form-control-label">Bottom Left</label>
                                    	<div class="col-lg-9" style="padding:0;">
                                    		<div class="col-lg-9">
                                    		</div>
                                        	<div class="col-lg-3" style="padding:0;">
                                            	<select id="device-settings-order-header-bottom-left" name="device-settings-order-header-bottom-left" 
                                            		class="form-control selectpicker">
                                                	
                                            	</select>
                                         </div>
                                    	</div>
                                	</div>
                                	<div class="form-group row separator-bottom">
                                    	<label class="col-lg-3 col-form-label form-control-label">Bottom Right</label>
                                    	<div class="col-lg-9" style="padding:0;">
                                    		<div class="col-lg-9">
                                    		</div>
                                        	<div class="col-lg-3" style="padding:0;">
                                            	<select id="device-settings-order-header-bottom-right" name="device-settings-order-header-bottom-right" 
                                            		class="form-control selectpicker">
                                                	
                                            	</select>
                                         </div>
                                    	</div>
                                	</div>
                                	
                             </div>
                             
                            	{{-- ------------------------------------------------------------------------------------- Order header --}}
                            	
                            	<hr class="separator-1" />
                            	
                            	{{-- Anchor Dialog ------------------------------------------------------------------------------------ --}}
                            	
                            	<div id="device-settings-feature-anchor-dialog" class="col-lg-12 device-settings-feature-title" scroll-to="760">
                            	
                            		<label class="col-lg-11 col-form-label form-control-label label-left label-group">Anchor Dialog</label>
                            		
                            		<div class="col-lg-1 device-settings-arrow">
                                		<i class="fa fa-angle-down fa-lg"></i>
                                		<i class="fa fa-angle-up fa-lg" style="display:none;"></i>
                                	</div>
                            		
                            	</div>
                            	
                            	<div id="device-settings-feature-anchor-dialog-config" class="col-lg-12 device-settings-feature-config">
                            	
                            		<div class="form-group row separator-bottom">
                                    	<label class="col-lg-3 col-form-label form-control-label">New Orders</label>
                                    	<div class="col-lg-9" style="padding:0;">
                                    		<div class="col-lg-6">
                                    		</div>
                                        	<div class="col-lg-4" style="padding:0;">
                                            	<div class="col-lg-5">
                                                	<input type="number" min="0" id="device-settings-anchor-seconds-new" 
                                                		name="device-settings-anchor-seconds-new" class="form-control anchor-input num-99999">
                                            	</div>
                                            	<label class="col-lg-5 col-form-label form-control-label label-left">seconds</label>
                                         </div>
                                         <div class="col-lg-2 right">
                                         	<label class="switch">
                                             	<input type="checkbox" id="device-settings-anchor-enable-new" 
                                             		name="device-settings-anchor-enable-new">
                                             	<span class="slider round device-settings-switch-slider" ></span>
                                            </label>
                                         </div>
                                    	</div>
                                	</div>
                                	
                                	<div class="form-group row separator-bottom">
                                    	<label class="col-lg-3 col-form-label form-control-label">Prioritized Orders</label>
                                    	<div class="col-lg-9" style="padding:0;">
                                    		<div class="col-lg-6">
                                    		</div>
                                        	<div class="col-lg-4" style="padding:0;">
                                            	<div class="col-lg-5">
                                                	<input type="number" min="0" id="device-settings-anchor-seconds-prioritized" 
                                                		name="device-settings-anchor-seconds-prioritized" class="form-control anchor-input num-99999">
                                            	</div>
                                            	<label class="col-lg-5 col-form-label form-control-label label-left">seconds</label>
                                         </div>
                                         <div class="col-lg-2 right">
                                         	<label class="switch">
                                             	<input type="checkbox" id="device-settings-anchor-enable-prioritized" 
                                             		name="device-settings-anchor-enable-prioritized" >
                                             	<span class="slider round device-settings-switch-slider" ></span>
                                            </label>
                                         </div>
                                    	</div>
                                	</div>
                                	
                                	<div class="form-group row separator-bottom">
                                    	<label class="col-lg-3 col-form-label form-control-label">Delayed Orders</label>
                                    	<div class="col-lg-9" style="padding:0;">
                                    		<div class="col-lg-6">
                                    		</div>
                                        	<div class="col-lg-4" style="padding:0;">
                                            	<div class="col-lg-5">
                                                	<input type="number" min="0" id="device-settings-anchor-seconds-delayed" 
                                                		name="device-settings-anchor-seconds-delayed" class="form-control anchor-input num-99999">
                                            	</div>
                                            	<label class="col-lg-5 col-form-label form-control-label label-left">seconds</label>
                                         </div>
                                         <div class="col-lg-2 right">
                                         	<label class="switch">
                                             	<input type="checkbox" id="device-settings-anchor-enable-delayed" 
                                             		name="device-settings-anchor-enable-delayed">
                                             	<span class="slider round device-settings-switch-slider" ></span>
                                            </label>
                                         </div>
                                    	</div>
                                	</div>
                                	
                                	<div class="form-group row separator-bottom">
                                    	<label class="col-lg-3 col-form-label form-control-label">Ready Orders</label>
                                    	<div class="col-lg-9" style="padding:0;">
                                    		<div class="col-lg-6">
                                    		</div>
                                        	<div class="col-lg-4" style="padding:0;">
                                            	<div class="col-lg-5">
                                                	<input type="number" min="0" id="device-settings-anchor-seconds-ready" 
                                                		name="device-settings-anchor-seconds-ready" class="form-control anchor-input num-99999">
                                            	</div>
                                            	<label class="col-lg-5 col-form-label form-control-label label-left">seconds</label>
                                         </div>
                                         <div class="col-lg-2 right">
                                         	<label class="switch">
                                             	<input type="checkbox" id="device-settings-anchor-enable-ready" 
                                             		name="device-settings-anchor-enable-ready">
                                             	<span class="slider round device-settings-switch-slider" ></span>
                                            </label>
                                         </div>
                                    	</div>
                                	</div>
                                	
                            	</div>
                            	
                            	{{-- ------------------------------------------------------------------------------------ Anchor Dialog --}}
                            	
                            	<hr class="separator-1" />
                            
                        </form>
                    </div>
                    
                </div>
                
        		</div>
    			<div class="modal-footer">
    				<button type="button" id="device-settings-close" class="btn btn-secondary">Close</button>
    				<button type="button" id="device-settings-save" class="btn btn-success">Save</button>
    			</div>
        	</div>
    </div>
</div>


<!-- Modal Remove Device -->
<div class="modal fade" id="modalRemoveDevice" tabindex="-1" role="dialog" aria-labelledby="modalRemoveDevice" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content">
        		<div class="modal-header">
        			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
            			<span aria-hidden="true">&times;</span>
            		</button>
        			<h5 class="modal-title" id="modalLongTitle">Remove KDS Station</h5>
        		</div>
        		<div id="are-you-sure" class="modal-body">
        			Are you sure you want to remove this KDS Station?
        		</div>
    			<div class="modal-footer">
    				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    				<button id="remove-device-confirm" type="button" class="btn btn-danger" data-dismiss="modal">Remove</button>
    			</div>
        	</div>
    </div>
</div>


<style>

#modalDeviceSettings #modalLongTitle { font-weight:300; }
#modalDeviceSettings .modal-dialog { width:70%; font-size:16px; font-weight:300 !important; }
#modalDeviceSettings .modal-header { border-bottom: 1px solid #9FA8DA; }
#modalDeviceSettings .card-body { padding:30px 80px 30px 40px; max-height:calc(90vh - 26vh); overflow-y:scroll; }
#modalDeviceSettings label { height:36px; font-size:16px; font-weight:300; color:#000; text-align:right; padding-top:6px; }
#modalDeviceSettings select, input { font-size:16px; height:36px !important; border-radius:3px !important; }
#modalDeviceSettings .status-box { padding:5px; }
#modalDeviceSettings .status-box .status-box-card { border:1px solid #ccc; border-radius:3px; padding-bottom:5px; }
#modalDeviceSettings .label-left { text-align:left; }
#modalDeviceSettings .label-group { font-weight:400; color:#333; }
#modalDeviceSettings .device-settings-switch-slider { height:22px; }
#modalDeviceSettings .bootstrap-select .dropdown-menu ul { max-height:300px !important; padding:0px 0px 20px 0px;  }
#modalDeviceSettings .bootstrap-select li a { font-size:16px; font-weight:200;  }
#modalDeviceSettings .bootstrap-select .filter-option { font-size:16px; font-weight:200; }
#modalDeviceSettings .v-center-padding { padding-top:4px; }
#modalDeviceSettings .order-status-input { text-align:center; }
#modalDeviceSettings .anchor-input { text-align:center; }

#modalDeviceSettings #device-settings-basic { height:230px; }
#modalDeviceSettings .device-settings-feature-title { height:54px; }
#modalDeviceSettings .device-settings-feature-title .label-group { font-size:18px; font-weight:300; color:#333; }
#modalDeviceSettings .device-settings-feature-config { padding-top:30px; padding-bottom:30px; }
#modalDeviceSettings .device-settings-arrow { text-align:right; font-size:20px; color:#aaa; }
#modalDeviceSettings .device-settings-arrow i { cursor:pointer; padding:5px 7px; border-radius:40px; }
#modalDeviceSettings .device-settings-arrow i:hover { background:#eef; }
#modalDeviceSettings .device-settings-arrow i:active { background:#eff; }

{{-- Popover Error --}}
#modalDeviceSettings .popover .popover-title {  }
#modalDeviceSettings .popover .popover-content { color:red; font-weight:200; font-size:15px; }

{{--
hr.separator-0 { border:0; clear:both; display:block; width:100%; background-color:#9FA8DA; height:1px; margin:40px 0px 20px 0px; }
--}}
hr.separator-1 { border:none; width:100%; height:20px; border-bottom:1px solid #C5CAE9; 
                    box-shadow:0 10px 10px -10px #9FA8DA; margin:-20px auto 30px;  }
.separator-bottom { border-bottom:0.01em solid #eee;  }

.separator-bottom:hover { box-shadow:inset 0 -10px 20px -7px #E8EAF6; }

</style>


<script>

function fillColumnTextAndPercent(columnId) {
	var selectText = document.getElementById('device-settings-line-display-column-' + columnId + '-text');
		selectText.innerHTML  = '<option value="ORDER_ID">Order ID</option>';
		selectText.innerHTML += '<option value="ITEM_NAME">Name</option>';
		selectText.innerHTML += '<option value="WAITING_TIME">Wait Time</option>';
		selectText.innerHTML += '<option value="DESTINATION">Destination</option>';
		selectText.innerHTML += '<option value="CONDIMENTS">Condiments</option>';
		selectText.innerHTML += '<option value="TABLE_NAME">Table Name</option>';
		selectText.innerHTML += '<option value="USER_INFO">User Info</option>';

	var selectPercent = document.getElementById('device-settings-line-display-column-' + columnId + '-percent');
		selectPercent.innerHTML  = '<option value="5">05%</option>';
		selectPercent.innerHTML += '<option value="10">10%</option>';
		selectPercent.innerHTML += '<option value="20">20%</option>';
		selectPercent.innerHTML += '<option value="30">30%</option>';
		selectPercent.innerHTML += '<option value="40">40%</option>';
		selectPercent.innerHTML += '<option value="50">50%</option>';
		selectPercent.innerHTML += '<option value="60">60%</option>';
		selectPercent.innerHTML += '<option value="70">70%</option>';
		selectPercent.innerHTML += '<option value="80">80%</option>';
		selectPercent.innerHTML += '<option value="90">90%</option>';
		selectPercent.innerHTML += '<option value="100">100%</option>';
}

for(var i=1; i<=4; i++) {
	fillColumnTextAndPercent(i);
}

function fillOrderHeaderSelect(id) {
	var selectText = document.getElementById('device-settings-order-header-' + id);
	
	selectText.innerHTML  = '<option value="ORDER_ID">Order ID</option>';
	selectText.innerHTML += '<option value="SERVER_NAME">Server Name</option>';
	selectText.innerHTML += '<option value="WAITING_TIME">Wait Time</option>';
	selectText.innerHTML += '<option value="DESTINATION">Destination</option>';
	selectText.innerHTML += '<option value="POS_STATION">POS Station</option>';
	selectText.innerHTML += '<option value="TABLE_NAME">Table Name</option>';
	selectText.innerHTML += '<option value="USER_INFO">User Info</option>';
	selectText.innerHTML += '<option value="ORDER_TYPE">Order Type</option>';
}

fillOrderHeaderSelect('top-left');
fillOrderHeaderSelect('top-right');
fillOrderHeaderSelect('bottom-left');
fillOrderHeaderSelect('bottom-right');

</script>











