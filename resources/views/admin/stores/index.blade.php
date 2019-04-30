@extends('admin.layouts.admin')

@section('title', "Stores")

@section('content')
    <div class="row" style="min-height:800px;">
    
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
						<th>@sortablelink('app_name', 'App',['page' => $stores->currentPage()])</th>
                    	<th>@sortablelink('env_name', 'Type',['page' => $stores->currentPage()])</th>
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
                    
                    <td>
						{{ $store->app_name }}
                    </td>
                    
                    <td>
                        {{ $store->env_name }}
                    </td>
                    
                    <td>{{ $store->updated_at }}</td>
                    
                    <td width="220px" style="text-align:center;">
                    
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

						<a class="btn btn-xs btn-danger settings-icons remove-store" href="#" store_name="{{$store->business_name}}"
						   	store_guid="{{$store->store_guid}}"
							data-toggle="modal" data-target="#modalRemoveStore" data-title="Remove Store"
						   	data-placement="top">
							<i class="fa fa-trash"></i>
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


	{{-- Modal Store Device -------------------------------------------------------------------------------------------- --}}
	<div class="modal fade" id="modalRemoveStore" tabindex="-1" role="dialog" aria-labelledby="modalRemoveStore" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h5 class="modal-title" id="modalLongTitle">Remove Store</h5>
				</div>
				<div id="are-you-sure" class="modal-body">
					Are you sure you want to remove this Store?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button id="remove-device-confirm" type="button" class="btn btn-danger" data-dismiss="modal">Remove</button>
				</div>
			</div>
		</div>
	</div>
	{{-- -------------------------------------------------------------------------------------------- Modal Remove Store --}}
    
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
	{{ Html::script(mix('assets/admin/js/firebase-api.js')) }}
    <script>
		$(function(){
            var token = { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }

            var storeToRemoveGuid = "";

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

            $('.remove-store').click(function(){
                storeToRemoveGuid = $(this).attr('store_guid');
                var storeName = $(this).attr('store_name');
                $('#modalRemoveStore #are-you-sure').html('Are you sure you want to remove the Store ' +
                    '\"<span style="color:red;">' + storeName +  '\</span>"?')
            });

            $('#remove-device-confirm').click(function(){
                if(storeToRemoveGuid != "") {
                    $.ajax({
                        headers: token,
                        url: 'removeStore',
                        type: 'POST',
                        data: {
                            storeToRemoveGuid: storeToRemoveGuid
                        },
                        success: function (response) {
                            if(response !== "") {
                            	console.log(response);
                                alert(response);

                            } else {
                                sendNotificationToFirebase();
                                location.reload();
                            }
                        }
                    });
                }
            });

		});
    </script>
@endsection




