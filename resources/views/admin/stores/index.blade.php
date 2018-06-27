@extends('admin.layouts.admin')

@section('title', "Stores")

@section('content')
    <div class="row" style="min-height:700px;">
    
    		<div style="text-align:right;padding:10px;">
    			<a class="btn btn-success" type="button" href="{{ route('admin.stores.new') }}">New</a>
    		</div>
    
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        
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
                		<td>{{ $store->name }}</td>
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
                    <td>

                        <a class="btn btn-xs btn-info" href="{{ route('admin.stores.show', [$store->id]) }}"
                            data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.users.index.show') }}">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a class="btn btn-xs btn-warning" href="{{ route('admin.stores.edit', [$store->id]) }}"
                            data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.users.index.edit') }}">
                            <i class="fa fa-pencil"></i>
                        </a>

                        {{--@if(!$store->hasRole('administrator'))--}}
                            {{--<button class="btn btn-xs btn-danger user_destroy"--}}
                                    {{--data-url="{{ route('admin.stores.destroy', [$store->id]) }}"
                                    data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.users.index.delete') }}">--}}
                                {{--<i class="fa fa-trash"></i>--}}
                            {{--</button>--}}
                        {{--@endif--}}

                        <a class="btn btn-xs btn-primary" href="{{ route('admin.stores.config', [$store->id]) }}"
                        			data-toggle="tooltip" data-placement="top" data-title="Config Store">
                            <i class="fa fa-sign-in"></i>
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
