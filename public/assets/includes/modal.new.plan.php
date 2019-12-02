<div class="modal fade" id="modal-new" tabindex="-2" role="dialog" aria-labelledby="modalNew" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content">
        		<div class="modal-header">
        			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
            			<span aria-hidden="true">&times;</span>
            		</button>
        			<h5 class="modal-title" id="modal-new-title" style="font-size:18px;font-weight:300;">
        				New plan
        			</h5>
        		</div>
        		<div class="modal-body p-3" id="modal-new-body" style="min-height:200px;">
                    <input type=hidden name='plan_app' id='plan_app' value=''>
                    <input type=hidden name='plan_hardware' id='plan_hardware' value=0>

                    <!-- Read-only -->
                    <div class="form-group row">
                        <label for="plan_app_ro" class="col-sm-4 col-form-label text-right">App </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="plan_app_ro" value="Allee" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="plan_hardware_ro" class="col-sm-4 col-form-label text-right">Hardware </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="plan_hardware_ro" value="No" readonly>
                        </div>
                    </div>

                    <!-- New plan information -->
                    <div class="form-group row">
                        <label for="plan_name" class="col-sm-4 col-form-label text-right">Plan name <span class='required'>*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="plan_name" value="" placeholder="E.g. Special Price 1" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="plan_cost" class="col-sm-4 col-form-label text-right">Cost/Unit (US$) <span class='required'>*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="plan_cost" value="" placeholder="E.g. 30" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="plan_longevity" class="col-sm-4 col-form-label text-right">Longevity (months)<span class='required'>*</span></label>
                        <div class="col-sm-8">
                            <div type="input-group">
                                <input type="text" class="form-control" id="plan_longevity" value="" placeholder="E.g. 36" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="plan_frequency" class="col-sm-4 col-form-label text-right">Payment Frequency <span class='required'>*</span></label>
                        <div class="col-sm-8">
                            <select id="plan_frequency" class="form-control pull-right">
                                <option value="YEARLY">Yearly</option>
                                <option value="MONTHLY">Monthly</option>
                                <option value="ONE-TIME">One-time payment</option>
                            </select>
                        </div>
                    </div>
        		</div>
    			<div class="modal-footer" id="modal-new-footer">
    				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    				<button type="button" class="btn btn-primary" id="btnApply">Create & Apply</button>
    			</div>
        	</div>
    </div>
</div>

<style>
    #modal-new-title { letter-spacing:2px; }
    .required { color:red; }
</style>