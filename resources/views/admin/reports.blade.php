@extends('admin.layouts.admin')

@section('title', "Reports")

@section('content')

<div id="tabs" style="margin-top:50px;">
    <ul  class="nav nav-pills">
        	<li>
        		<a class="tab-a" data-id="0" href="#1a" data-toggle="tab">by Plan</a>
        	</li>

    		<li>
    			<a class="tab-a" data-id="1" href="#1a" data-toggle="tab">by Store</a>
    		</li>
    		
    		<li class="active">
    			<a class="tab-a" data-id="2" href="#1a" data-toggle="tab">by Statement</a>
    		</li>
    </ul>
</div>


<div style="margin-top:-30px;">
	<div id="reports-container" class="row" style="min-height:700px;">
		
    </div>
</div>


@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/bootstrap-table.min.css')) }}
    {{ Html::style(mix('assets/admin/css/bootstrap-select.css')) }}
    {{ Html::style(mix('assets/admin/css/checkbox.switch.css')) }}
    {{ Html::style(mix('assets/admin/css/bootstrap-tabs-custom.css')) }}
@endsection


@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/jquery-ui.min.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-table.min.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-select.min.js')) }}
    {{ Html::script(mix('assets/admin/js/jquery.mask.js')) }}
    {{ Html::script(mix('assets/admin/js/jquery-table-export.js')) }}
	{{ Html::script(mix('assets/admin/js/bootstrap-table-export.js')) }}
	{{ Html::script(mix('assets/admin/js/SyncPage.js')) }}
	<script>
		
        reports = [
            ['costByPlan' , "{{ route('admin.reports.costByPlan') }} "],
            ['costByStore' , "{{ route('admin.reports.costByStore') }}"],
            ['costByStatement' , "{{ route('admin.reports.costByStatement') }}"]
        ];
		
		$(function(){
			SyncPage.getContent(reports[2][1], $('#reports-container'), '');
		});
		
    		$('.tab-a').click(function(){
    			id = $(this).attr('data-id');
    			SyncPage.getContent(reports[id][1], $('#reports-container'), '');
        	});
        	
    </script>
@endsection









