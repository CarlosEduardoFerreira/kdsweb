
<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog" aria-labelledby="modalDelete" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content">
        		<div class="modal-header">
        			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
            			<span aria-hidden="true">&times;</span>
            		</button>
        			<h5 class="modal-title" style="font-size:18px;font-weight:300;">Delete <span class="modal-item">item</span></h5>
        		</div>
        		<div class="modal-body">
        			<div id="modal-extrahtml" style="text-align:center;font-size:16px;font-weight:300;">
        			
        			</div>
        			<div style="text-align:center;font-size:16px;font-weight:300;">
        				IF you are sure you want to delete this 
        				<b><span class="modal-item">item</span></b>, 
        				type <span style="color:red;letter-spacing:1px;">DELETE</span>:
        			</div>
        			<div style="text-align:center;">
        				<input id="modal-input-delete" type="text" 
        				style="margin-top:25px;margin-bottom:20px;border:1px solid #ccc;border-radius:5px;
        				width:200px;text-align:center;font-size:20px;font-weight:300;color:red;letter-spacing:2px;">
        			</div>
        		</div>
    			<div class="modal-footer">
    				<button type="button" class="btn btn-primary" onclick="javascript:$('#modal-delete').modal('toggle')">Cancel</button>
    			</div>
        	</div>
    </div>
</div>
