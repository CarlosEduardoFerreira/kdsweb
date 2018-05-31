@extends('admin.layouts.admin')

<?php
    $title = Session::get('title')
?>

@section('title', $title)

<!-- @section('title', __('views.admin.users.index.title')) -->

@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%">
            <thead>
            <tr>
            	<th>@sortablelink('name',  __('views.admin.users.index.table_header_1'),['page' => $users->currentPage()])</th>
                <th>@sortablelink('email', __('views.admin.users.index.table_header_0'),['page' => $users->currentPage()])</th>
                <th>@sortablelink('active', __('views.admin.users.index.table_header_3'),['page' => $users->currentPage()])</th>
                <th>@sortablelink('created_at', __('views.admin.users.index.table_header_5'),['page' => $users->currentPage()])</th>
                <th>@sortablelink('updated_at', __('views.admin.users.index.table_header_6'),['page' => $users->currentPage()])</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>

            @foreach($users as $user)
                <tr>
                		<td>{{ $user->name }}</td>
                    	<td>{{ $user->email }}</td>
                    <td>
                        @if($user->active)
                            <span class="label label-primary">{{ __('views.admin.users.index.active') }}</span>
                        @else
                            <span class="label label-danger">{{ __('views.admin.users.index.inactive') }}</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at }}</td>
                    <td>{{ $user->updated_at }}</td>
                    <td>
                    		<?php
                    		  $role = $user->role_id;

                    		  $action = route('admin.storegroups', ['resellerId' => $user->id]);
                    		  $dataTitle  = "Store Groups";
                    		  $icon = "fa-sitemap";

                    		  if ($role == 'storegroup') {
                    		      $action = route('admin.stores', ['storegroupId' => $user->id]);
                    		      $dataTitle  = "Stores";
                    		      $icon = "fa-cutlery";
                    		  }
                    		  //echo "role : " . $role;
                    		?>

                            <a class="btn btn-xs btn-info" href="{{ route('admin.users.show', [$user->id]) }}" data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.users.index.show') }}">
                                <i class="fa fa-eye"></i>
                            </a>

                            <a class="btn btn-xs btn-warning" href="{{ route('admin.users.edit', [$user->id]) }}" data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.users.index.edit') }}">
                                <i class="fa fa-pencil"></i>
                            </a>

                            {{--@if(!$user->hasRole('administrator'))--}}
                                {{--<button class="btn btn-xs btn-danger user_destroy"--}}
                                        {{--data-url="{{ route('admin.users.destroy', [$user->id]) }}" data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.users.index.delete') }}">--}}
                                    {{--<i class="fa fa-trash"></i>--}}
                                {{--</button>--}}
                            {{--@endif--}}

                            <?php if ($role == 'reseller' || $role == 'storegroup') { ?>
                                <a class="btn btn-xs btn-primary" href="<?php echo $action; ?>"
                                				data-toggle="tooltip" data-placement="top" data-title="<?php echo $dataTitle; ?>">
                                    <i class="fa <?php echo $icon; ?>"></i>
                                </a>
                            <?php } ?>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pull-right">
            {{ $users->links() }}
        </div>
    </div>
@endsection
