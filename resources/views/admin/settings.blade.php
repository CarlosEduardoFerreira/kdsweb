@extends('admin.layouts.admin')

@section('title', "Settings")

@section('content')


<div id="tabs" style="margin-top:50px;">
    <ul  class="nav nav-pills">
        	<li class="active">
        		<a class="tab-a" data-url="{{ route('admin.settings.plans') }}" href="#1a" data-toggle="tab">Plans</a>
        	</li>

    		<li class="li-plans" id="li-plans-res">
    			<a class="tab-a" data-url="{{ route('admin.settings.plansXresellers') }}" href="#1a" data-toggle="tab">Plans x Resellers</a>
    		</li>
        
    		<li class="li-plans" id="li-plans-stg">
    			<a class="tab-a" data-url="{{ route('admin.settings.plansXstoregroups') }}" href="#1a" data-toggle="tab">Plans x Store Groups</a>
    		</li>
        
    		<li class="li-plans" id="li-plans-str">
    			<a class="tab-a" data-url="{{ route('admin.settings.plansXstores') }}" href="#1a" data-toggle="tab">Plans x Stores</a>
    		</li>
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
        #tabs .nav-pills li.li-plans { display:none; }
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
    {{ Html::script(mix('assets/admin/js/SyncPage.js')) }}
    <script>

    		SyncPage.getContent("{{ route('admin.settings.plans') }}", $('#settings-container'), '');

    		$('.tab-a').click(function(){
    			url = $(this).attr('data-url');
    			SyncPage.getContent(url, $('#settings-container'), '');
        	});
        	
    </script>
    
@endsection









