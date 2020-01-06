@extends('admin.layouts.admin')
@section('title', "")
@section('content')


    <div class="page-title">
    <button onclick="goBack()" type="button" class="btn button4">Back</button>   
    <div class="title_left">
    <h1 class="h3">Store View</h1>
    </div>
    </div>
    <div class="row">
        <table class="table table-striped table-hover">
            <tbody>

            <!--
            <tr>
                <th>Store:</th>
                <td><img src="{{ $store->avatar }}" class="user-profile-image"></td>
            </tr>
            -->

             <tr>
                <th>Store Group</th>
                <td>{{ $storegroup->business_name }}</td>
            </tr>
            <tr>
                <th>Legal Business Name</th>
                <td>{{ $store->business_name }}</td>
            </tr>
            <tr>
                <th>DBA (Doing business as)</th>
                <td>{{ $store->dba }}</td>
            </tr>
            <tr>
                <th>Last Name</th>
                <td>{{ $store->last_name }}</td>
            </tr>
            <tr>
                <th>First Name</th>
                <td>{{ $store->name }}</td>
            </tr>
            <tr>
                <th>{{ __('views.admin.users.show.table_header_2') }}</th>
                <td>
                    <a href="mailto:{{ $store->email }}">
                        {{ $store->email }}
                    </a>
                </td>
            </tr>
            <tr>
                <th>Contact Phone Number</th>
                <td>{{ $store->phone_number }}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>{{ $store->address }}</td>
            </tr>
             <tr>
                <th>Address</th>
                <td>{{ $store->address2 }}</td>
            </tr>
            <tr>
                <th>City</th>
                <td>{{ $store->city }}</td>
            </tr>
            <tr>
                <th>State</th>
                <td>{{ $store->state }}</td>
            </tr>
             <tr>
                <th>Zip Code</th>
                <td>{{ $store->zipcode }}</td>
            </tr>
            <tr>
                <th>Country</th>
                <td>{{ $store->country }}</td>
            </tr>
           

            <!--
            <tr>
                <th>{{ __('views.admin.users.show.table_header_3') }}</th>
                <td>
                    {{ $store->roles->pluck('name')->implode(',') }}
                </td>
            </tr>
            -->

            <tr>
                <th>{{ __('views.admin.users.show.table_header_4') }}</th>
                <td>
                    @if($store->active)
                        <span class="label label-primary">{{ __('views.admin.users.show.active') }}</span>
                    @else
                        <span class="label label-danger">{{ __('views.admin.users.show.inactive') }}</span>
                    @endif
                </td>
            </tr>

            <tr>
                <th>Username</th>
                <td>{{ $store->username }}</td>
            </tr>

            <!--
            <tr>
                <th>{{ __('views.admin.users.show.table_header_5') }}</th>
                <td>
                    @if($store->confirmed)
                        <span class="label label-success">{{ __('views.admin.users.show.confirmed') }}</span>
                    @else
                        <span class="label label-warning">{{ __('views.admin.users.show.not_confirmed') }}</span>
                    @endif</td>
                </td>
            </tr>
            -->

            <tr>
                <th>{{ __('views.admin.users.show.table_header_6') }}</th>
                <td>
                    @if (isset($store->created_at))
                    {{ $store->created_at }} ({{ $store->created_at->diffForHumans() }})
                    @endif
                </td>
            </tr>

            <tr>
                <th>{{ __('views.admin.users.show.table_header_7') }}</th>
                <td>
                    @if (isset($store->updated_at))
                    {{ $store->updated_at }} ({{ $store->updated_at->diffForHumans() }})
                    @endif
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <style>   
   button[type=button]:hover {
    text-decoration:underline;
}
.button4 {
position: absolute;
left;
margin-top:-20px;   
font-size: 11px; 
background: #26b99a00;

}
</style>
<script>
    function goBack() {
    window.history.back();
    }
</script>
@endsection
