@extends('admin.layouts.admin')

@section('title', "Stores")

@section('content')
    <div class="row" style="min-height:700px;">
    
    		<div style="text-align:right;padding:10px;">
    			<a class="btn btn-success" type="button" href="{{ route('admin.stores.new', ['filter' => false]) }}">New</a>
    		</div>
    		
    		<div id="store-filters">
        		<input type="text" id="search-input" class="btn">
        		<a id="btn-search" class="btn btn-info">Go</a>
    		</div> 
    
        <table class="table table-striped table-bordered dt-responsive nowrap">
        
            <thead>
                <tr>
                		<th>@sortablelink('business_name', 'Legal Business Name',['page' => $stores->currentPage()])</th>
                    	<th>@sortablelink('email', __('views.admin.users.index.table_header_0'),['page' => $stores->currentPage()])</th>
                    	<th>@sortablelink('active', __('views.admin.users.index.table_header_3'),['page' => $stores->currentPage()])</th>
                    	<th>@sortablelink('created_at', __('views.admin.users.index.table_header_5'),['page' => $stores->currentPage()])</th>
                    	<th>@sortablelink('updated_at', __('views.admin.users.index.table_header_6'),['page' => $stores->currentPage()])</th>
                    	<th>Actions</th>
                </tr>
            </thead>
            
            <tbody>

            @foreach($stores as $store)
                <tr>
                		<td>{{ $store->business_name }}</td>
                    	<td>{{ $store->email }}</td>
                    <td>
                        @if($store->active)
                            <span class="label label-primary">{{ __('views.admin.users.index.active') }}</span>
                        @else
                            <span class="label label-danger">{{ __('views.admin.users.index.inactive') }}</span>
                        @endif
                    </td>
                    <td>{{ $store->created_at }}</td>
                    <td>{{ $store->updated_at }}</td>
                    <td width="200px" style="text-align:center;">
                    
                    		<style>
                    		  .settings-icons { width:30px; }
                    		</style>

                        <a class="btn btn-xs btn-info settings-icons" href="{{ route('admin.stores.show', [$store->id]) }}"
                            data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.users.index.show') }}">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a class="btn btn-xs btn-primary settings-icons" href="{{ route('admin.stores.edit', [$store->id, 'filter' => false]) }}"
                            data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.users.index.edit') }}">
                            <i class="fa fa-pencil"></i>
                        </a>

                        <a class="btn btn-xs btn-warning settings-icons" href="{{ route('admin.stores.config', [$store->id]) }}"
                        			data-toggle="tooltip" data-placement="top" data-title="Config Store" style="background:#e7bf3f;">
                            <i class="fa fa-cogs"></i>
                        </a>
                        
                        <a class="btn btn-xs btn-success settings-icons" href="{{ route('admin.stores.report', [$store->id]) }}"
                        			data-toggle="tooltip" data-placement="top" data-title="Reports" style="background:#29a66b;">
                            <i class="fa fa-line-chart"></i>
                        </a>
                        
						<?php 
						  //echo "store_guid: " . $store->store_guid;
						?>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pull-right">
            {{ $stores->links() }}
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
				var url =  URL_BASE + "/admin/stores/0?filter=0" + search;
				window.location.href = url;
			}

			/** Search Input Placeholder **********************************/
			var searchPlaceholder = "Search by Business Name or E-mail";

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




