@extends('admin.layouts.admin')

@section('title', "Settings")

@section('content')

<?php 
    $adm = $me->hasRole('administrator'); 
    $res = $me->hasRole('reseller');
    $stg = $me->hasRole('storegroup');
?>

<div id="tabs" style="margin-top:50px;">
    <ul  class="nav nav-pills">
        	<li class="active"><a class="tab-a" data-url="{{ route('admin.settings.plans') }}" href="#1a" data-toggle="tab">Plans</a></li>
        
        	<?php if($adm) { ?>
        		<li><a class="tab-a" data-url="{{ route('admin.settings.plansXresellers') }}" href="#1a" data-toggle="tab">Plans x Resellers</a></li>
        	<?php } ?>
        
        	<?php if($adm || $res) { ?>
        		<li><a class="tab-a" data-url="{{ route('admin.settings.plansXstoregroups') }}" href="#1a" data-toggle="tab">Plans x Store Groups</a></li>
        	<?php } ?>
        
        	<?php if($adm || $res || $stg) { ?>
        		<li><a class="tab-a" data-url="{{ route('admin.settings.plansXstores') }}" href="#1a" data-toggle="tab">Plans x Stores</a></li>
		<?php } ?>
    </ul>
</div>


<div style="margin-top:-30px;">
	<div id="settings-container" class="row" style="min-height:700px;">
		
    </div>
</div>


@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/bootstrap-table.min.css')) }}
    {{ Html::style(mix('assets/admin/css/bootstrap-select.css')) }}
    {{ Html::style(mix('assets/admin/css/checkbox.switch.css')) }}
    <style>
        #tabs .nav-pills { font-size:16px; font-weight:300; }
        #tabs .nav-pills > li > a { width:180px; text-align:center; border:1px solid #ccc; margin-right:10px; border-radius:5px; padding:7px 15px; }
        #tabs .nav-pills > li > a:hover { border:1px solid #666; }
    </style>
@endsection


@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/jquery-ui.min.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-table.min.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-select.min.js')) }}
    {{ Html::script(mix('assets/admin/js/jquery.mask.js')) }}
    {{ Html::script(mix('assets/admin/js/ModalDelete.js')) }}
    <script>
        class AdminSettings {
        		static getContent(url, $container) {
        			$.ajax({
        				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        				url: url,
        				type: "GET",
        				data : {},
        				success: function(response){
            				$container.html("");
    
        					var $btns = $("<div></div>");
        						$btns.css({'height':'50px', 'text-align':'right', 'margin-right':'20px'});
        						
        					$container.append($btns);
            				
        					if(url == "{{ route('admin.settings.plans') }}") {
            					var $btnNew = $("<a class=\"btn btn-success\" id=\"settings-btn-new\" href=\"#\">New</a>");
            						$btnNew.css({'margin-top':'-6px'});
            						
            					$btns.html($btnNew);
        					}
    					
        					var $content = $("<div><div style=\"width:95%;margin:10px auto;\">" + response + "</div></div>");
        					
            					$content.css({'min-height':'700px', 'margin-top':'20px', 'margin-bottom':'50px', 'margin-left':'20px',
                							  'margin-right':'20px', 'padding-top':'10px', 'background-color':'#fff'});
    						
        					$container.append($content);
        				},
        				error : function (xhr, ajaxOptions, thrownError) {
        					alert("error: " + xhr.status + " - " + xhr.responseText)
//         					location.href = "{{ route('admin.dashboard') }}";
        				}
        			});
        		}
        }

        	AdminSettings.getContent("{{ route('admin.settings.plans') }}", $('#settings-container'));

    		$('.tab-a').click(function(){
			url = $(this).attr('data-url');
			AdminSettings.getContent(url, $('#settings-container'));
        	});
    </script>
    
@endsection









