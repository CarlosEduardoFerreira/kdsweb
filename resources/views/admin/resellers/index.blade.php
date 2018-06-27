@extends('admin.layouts.admin')

@section('title', "Resellers")

@section('content')
    <div class="row" style="min-height:700px;">
    
    		<div style="text-align:right;padding:10px;">
    			<a class="btn btn-success" type="button" href="{{ route('admin.resellers.new') }}">New</a>
    		</div>
    		
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
            <tr>
            	<th>@sortablelink('name',  'Reseller Name',['page' => $resellers->currentPage()])</th>
                <th>@sortablelink('email', __('views.admin.users.index.table_header_0'),['page' => $resellers->currentPage()])</th>
                <th>@sortablelink('active', __('views.admin.users.index.table_header_3'),['page' => $resellers->currentPage()])</th>
                <th>@sortablelink('created_at', __('views.admin.users.index.table_header_5'),['page' => $resellers->currentPage()])</th>
                <th>@sortablelink('updated_at', __('views.admin.users.index.table_header_6'),['page' => $resellers->currentPage()])</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>

            @foreach($resellers as $reseller)
                <tr>
                		<td>{{ $reseller->name }}</td>
                    	<td>{{ $reseller->email }}</td>
                    <td>
                        @if($reseller->active)
                            <span class="label label-primary">{{ __('views.admin.users.index.active') }}</span>
                        @else
                            <span class="label label-danger">{{ __('views.admin.users.index.inactive') }}</span>
                        @endif
                    </td>
                    <td>{{ $reseller->created_at }}</td>
                    <td>{{ $reseller->updated_at }}</td>
                    <td>

                        <a class="btn btn-xs btn-info" href="{{ route('admin.resellers.show', [$reseller->id]) }}" data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.users.index.show') }}">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a class="btn btn-xs btn-warning" href="{{ route('admin.resellers.edit', [$reseller->id]) }}" data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.users.index.edit') }}">
                            <i class="fa fa-pencil"></i>
                        </a>

                        {{--@if(!$reseller->hasRole('administrator'))--}}
                            {{--<button class="btn btn-xs btn-danger user_destroy"--}}
                                    {{--data-url="{{ route('admin.resellers.destroy', [$reseller->id]) }}" data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.users.index.delete') }}">--}}
                                {{--<i class="fa fa-trash"></i>--}}
                            {{--</button>--}}
                        {{--@endif--}}

                        <?php if ($reseller->role_id == 2) { ?>
                            <a class="btn btn-xs btn-primary" href="{{ route('admin.storegroups', ['resellerId' => $reseller->id]) }}"
                            				data-toggle="tooltip" data-placement="top" data-title="Store Groups">
                                <i class="fa fa-sitemap"></i>
                            </a>
                        <?php } ?>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pull-right">
            {{ $resellers->links() }}
        </div>
    </div>
@endsection
