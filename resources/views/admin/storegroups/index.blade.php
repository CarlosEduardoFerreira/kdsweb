@extends('admin.layouts.admin')

@section('title', "Store Groups")

@section('content')
<div id="back-button-div" style="width:100%;">
    <button onclick="goBack()" type="button" id="back-button" class="btn">Back</button>
</div>
<style>   
#back-button-div {
    float:left;
	margin-left:-15px;
    margin-top:-80px;
    font-size: 11px; 
    background: #26b99a00;
}
#back-button {font-size: 11px;  background:none; }
#back-button:hover { 
    text-decoration:underline;
}
.page-title { padding-top:80px; }

.page-title .title_left{
	padding-top:0px;
}
.h3 {
	padding-top:2.5px;
}
#store-filters #search-input {
	margin-top:-20px;
}
#btn-search.btn.btn-info{
	margin-top:-20px;
}
</style>
    <div class="row" style="min-height:700px;">
    
    		<div style="text-align:right;padding:10px;">
    			<a class="btn btn-success" type="button" href="{{ route('admin.storegroups.new', ['filter' => false]) }}">New</a>
            </div>
            
            <div id="store-filters">
        		<input type="text" id="search-input" class="btn">
        		<a id="btn-search" class="btn btn-info">Go</a>
    		</div> 

    		
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
            <tr>
            		<th width="18%">@sortablelink('business_name', 'Store Group Name',['page' => $storegroups->currentPage()])</th>
                	<th width="20%">@sortablelink('email', __('views.admin.users.index.table_header_0'),['page' => $storegroups->currentPage()])</th>
                	<th width="7%">@sortablelink('active', __('views.admin.users.index.table_header_3'),['page' => $storegroups->currentPage()])</th>
					<th width="12%">@sortablelink('apps', 'Apps',['page' => $storegroups->currentPage()])</th>
					<th width="15%">@sortablelink('envs', 'Store Types',['page' => $storegroups->currentPage()])</th>
                	<th width="15%">@sortablelink('updated_at', __('views.admin.users.index.table_header_6'),['page' => $storegroups->currentPage()])</th>
                	<th width="18%">Actions</th>
            </tr>
            </thead>
            <tbody>

            @foreach($storegroups as $storegroup)
                <tr class="tr-storegroup" sg_id="{{$storegroup->id}}">
                		<td>{{ $storegroup->business_name }}</td>
                    	<td>{{ $storegroup->email }}</td>
                    	<td style="text-align:center;">
                        @if($storegroup->active)
                            <span class="label label-primary">{{ __('views.admin.users.index.active') }}</span>
                        @else
                            <span class="label label-danger">{{ __('views.admin.users.index.inactive') }}</span>
                        @endif
                    	</td>
                    
						<td class="td-apps"  style="text-align:center;"> {{ $storegroup->apps }} </td>
                    	<td class="td-envs"  style="text-align:center;"> {{ $storegroup->envs }} </td>

                    	<td style="text-align:center;">
                    		{{ $storegroup->updated_at }}
                    	</td>
                    	
                    	<td style="text-align:center;">

                        <a class="btn btn-xs btn-info" href="{{ route('admin.storegroups.show', [$storegroup->id]) }}" data-toggle="tooltip" 
                        data-placement="top" data-title="{{ __('views.admin.users.index.show') }}">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a class="btn btn-xs btn-warning" href="{{ route('admin.storegroups.edit', [$storegroup->id, 'filter' => false]) }}" data-toggle="tooltip" 
                        data-placement="top" data-title="{{ __('views.admin.users.index.edit') }}">
                            <i class="fa fa-pencil"></i>
                        </a>

                        {{--@if(!$storegroup->hasRole('administrator'))--}}
                            {{--<button class="btn btn-xs btn-danger user_destroy"--}}
                                    {{--data-url="{{ route('admin.storegroups.destroy', [$storegroup->id]) }}" data-toggle="tooltip" 
                                    data-placement="top" data-title="{{ __('views.admin.users.index.delete') }}">--}}
                                {{--<i class="fa fa-trash"></i>--}}
                            {{--</button>--}}
                        {{--@endif--}}

                        <?php if ($storegroup->role_id == 2 || $storegroup->role_id == 3) { ?>
                            <a class="btn btn-xs btn-primary" href="{{ route('admin.stores', ['storegroupId' => $storegroup->id, 'filter' => true]) }}"
                            				data-toggle="tooltip" data-placement="top" data-title="Stores">
                                <i class="fa fa-cutlery"></i>
                            </a>
                        <?php } ?>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        
        <div class="pull-right">
            {{ $storegroups->links() }}
        </div>
        
    </div>
@endsection

@section('styles')
    @parent

    <style>
        #store-filters { margin-bottom:10px; }
		#store-filters #search-input { width:280px; height:36px; text-align:center; border:1px solid #ddd; 
		  font-size:16px; font-weight:200; color:#000; cursor:text; border:1px solid #5bc0de; }
    </style>
@endsection


@section('scripts')
	@parent

    <script>
		function goBack() {
    window.history.back();
    }
		$(function(){
			const searchInput = $('#search-input');
			var getUrlParameter = function getUrlParameter(sParam) {
			    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
			        sURLVariables = sPageURL.split('&'),
			        sParameterName,
			        i;
			    for (i = 0; i < sURLVariables.length; i++) {
			        sParameterName = sURLVariables[i].split('=');
			        if (sParameterName[0] === sParam) {
			            return sParameterName[1] === undefined ? true : sParameterName[1];
			        }
			    }
			};
			if (getUrlParameter('search') != undefined) {
				var search = getUrlParameter('search');
				searchInput.val(search);
				searchInput.focus();
			}
			function goFilter() {
				var search = emptyStr(searchInput.val()) ? "" : "&search=" + searchInput.val() ;
				var URL_BASE = window.location.protocol + "//" + window.location.host;
				var url =  URL_BASE + "/admin/storegroups/0?filter=0" + search;
				window.location.href = url;
			}
			/** Search Input Placeholder **********************************/
			var searchPlaceholder = "Search by Group Name or E-mail";
			searchInput.attr("placeholder", searchPlaceholder);
			searchInput.focusout(function(){
				$(this).attr("placeholder", searchPlaceholder);
			});
			searchInput.focusin(function(){
				$(this).attr("placeholder", "");
			});
			/********************************** Search Input Placeholder **/
			
			$('#btn-search').click(function(){
				goFilter();
			});
			function emptyStr(str) {
				return str.replace(/\s/g, '') == "";
			}
            searchInput.keyup(function(){
            		event.preventDefault();
            	  	if (event.keyCode === 13) {
            	  		goFilter();
            	  	}
            });
		});
    </script>
@endsection





