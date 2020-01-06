@extends('admin.layouts.admin')

@section('title', "Store Group View")

@section('content')
<div id="back-button-div" style="width:100%;">
    <button onclick="goBack()" type="button" id="back-button" class="btn">Back</button>
</div>
       
<style>   
#back-button-div {
    float:left;
    margin-top:-80px;
    font-size: 11px; 
    background: #26b99a00;
}
#back-button {font-size: 11px;  background:none; }
#back-button:hover { 
    text-decoration:underline;
}
.page-title { padding-top:90px; }
</style>
    <div class="row">
        <table class="table table-striped table-hover">
            <tbody>

            <!--
            <tr>
                <th>storegroup:</th>
                <td><img src="{{ $storegroup->avatar }}" class="user-profile-image"></td>
            </tr>
            -->
            
			<tr>
                <th>Reseller Name</th>
                <td>{{ isset($reseller) ? $reseller->business_name : '??? Unassigned ???' }}</td>
            </tr>
            <tr>
                <th>Store Group Name</th>
                <td>{{ $storegroup->business_name }}</td>
            </tr>
            <tr>
                <th>Contact Name</th>
                <td>{{ $storegroup->name }}</td>
            </tr>
            <tr>
                <th>{{ __('views.admin.users.show.table_header_2') }}</th>
                <td>
                    <a href="mailto:{{ $storegroup->email }}">
                        {{ $storegroup->email }}
                    </a>
                </td>
            </tr>
            <tr>
                <th>Contact Phone Number</th>
                <td>{{ $storegroup->phone_number }}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>{{ $storegroup->address }}</td>
            </tr>
            <tr>
                <th>Address 2</th>
                <td>{{ $storegroup->address2 }}</td>
            </tr>
            <tr>
                <th>City</th>
                <td>{{ $storegroup->city }}</td>
            </tr>
            <tr>
                <th>State</th>
                <td>{{ $storegroup->state }}</td>
            </tr>
             <tr>
                <th>Zip Code</th>
                <td>{{ $storegroup->zipcode }}</td>
            </tr>
            <tr>
                <th>Country</th>
                <td>{{ $storegroup->country }}</td>
            </tr>
           

            <!--
            <tr>
                <th>{{ __('views.admin.users.show.table_header_3') }}</th>
                <td>
                    {{ $storegroup->roles->pluck('name')->implode(',') }}
                </td>
            </tr>
            -->

            <tr>
                <th>{{ __('views.admin.users.show.table_header_4') }}</th>
                <td>
                    @if($storegroup->active)
                        <span class="label label-primary">{{ __('views.admin.users.show.active') }}</span>
                    @else
                        <span class="label label-danger">{{ __('views.admin.users.show.inactive') }}</span>
                    @endif
                </td>
            </tr>

           

            <!--
            <tr>
                <th>{{ __('views.admin.users.show.table_header_5') }}</th>
                <td>
                    @if($storegroup->confirmed)
                        <span class="label label-success">{{ __('views.admin.users.show.confirmed') }}</span>
                    @else
                        <span class="label label-warning">{{ __('views.admin.users.show.not_confirmed') }}</span>
                    @endif</td>
                </td>
            </tr>
            -->

            <tr>
                <th>{{ __('views.admin.users.show.table_header_6') }}</th>
                <td>{{ $storegroup->created_at }} ({{ $storegroup->created_at->diffForHumans() }})</td>
            </tr>

            <tr>
                <th>{{ __('views.admin.users.show.table_header_7') }}</th>
                <td>{{ $storegroup->updated_at }} ({{ $storegroup->updated_at->diffForHumans() }})</td>
            </tr>
            </tbody>
        </table>
    </div>
    <script>
        function goBack() {
    window.history.back();
    }
        </script>
@endsection
