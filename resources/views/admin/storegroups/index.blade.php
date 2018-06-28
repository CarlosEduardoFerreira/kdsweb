@extends('admin.layouts.admin')

@section('title', "Store Groups")

@section('content')
    <div class="row" style="min-height:700px;">
    
    		<div style="text-align:right;padding:10px;">
    			<a class="btn btn-success" type="button" href="{{ route('admin.storegroups.new') }}">New</a>
    		</div>
    		
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
            <tr>
            	<th>@sortablelink('name', 'Store Group Name',['page' => $storegroups->currentPage()])</th>
                <th>@sortablelink('email', __('views.admin.users.index.table_header_0'),['page' => $storegroups->currentPage()])</th>
                <th>@sortablelink('active', __('views.admin.users.index.table_header_3'),['page' => $storegroups->currentPage()])</th>
                <th>@sortablelink('created_at', __('views.admin.users.index.table_header_5'),['page' => $storegroups->currentPage()])</th>
                <th>@sortablelink('updated_at', __('views.admin.users.index.table_header_6'),['page' => $storegroups->currentPage()])</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>

            @foreach($storegroups as $storegroup)
                <tr>
                		<td>{{ $storegroup->name }}</td>
                    	<td>{{ $storegroup->email }}</td>
                    <td>
                        @if($storegroup->active)
                            <span class="label label-primary">{{ __('views.admin.users.index.active') }}</span>
                        @else
                            <span class="label label-danger">{{ __('views.admin.users.index.inactive') }}</span>
                        @endif
                    </td>
                    <td>{{ $storegroup->created_at }}</td>
                    <td>{{ $storegroup->updated_at }}</td>
                    <td>

                        <a class="btn btn-xs btn-info" href="{{ route('admin.storegroups.show', [$storegroup->id]) }}" data-toggle="tooltip" 
                        data-placement="top" data-title="{{ __('views.admin.users.index.show') }}">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a class="btn btn-xs btn-warning" href="{{ route('admin.storegroups.edit', [$storegroup->id]) }}" data-toggle="tooltip" 
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
                            <a class="btn btn-xs btn-primary" href="{{ route('admin.stores', ['storegroupId' => $storegroup->id]) }}"
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
