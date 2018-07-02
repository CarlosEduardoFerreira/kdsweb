@extends('admin.layouts.admin')

@section('title', "Reseller View")

@section('content')
    <div class="row">
        <table class="table table-striped table-hover">
            <tbody>

            <!--
            <tr>
                <th>reseller:</th>
                <td><img src="{{ $reseller->avatar }}" class="user-profile-image"></td>
            </tr>
            -->

            <tr>
                <th>Reseller Name</th>
                <td>{{ $reseller->business_name }}</td>
            </tr>
            <tr>
                <th>Contact Name</th>
                <td>{{ $reseller->name }}</td>
            </tr>
            <tr>
                <th>{{ __('views.admin.users.show.table_header_2') }}</th>
                <td>
                    <a href="mailto:{{ $reseller->email }}">
                        {{ $reseller->email }}
                    </a>
                </td>
            </tr>
            <tr>
                <th>Contact Phone Number</th>
                <td>{{ $reseller->phone_number }}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>{{ $reseller->address }}</td>
            </tr>
            <tr>
                <th>Address 2</th>
                <td>{{ $reseller->address2 }}</td>
            </tr>
            <tr>
                <th>City</th>
                <td>{{ $reseller->city }}</td>
            </tr>
            <tr>
                <th>State</th>
                <td>{{ $reseller->state }}</td>
            </tr>
            <tr>
                <th>Zip Code</th>
                <td>{{ $reseller->zipcode }}</td>
            </tr>
            <tr>
                <th>Country</th>
                <td>{{ $reseller->country }}</td>
            </tr>
            

            <!--
            <tr>
                <th>{{ __('views.admin.users.show.table_header_3') }}</th>
                <td>
                    {{ $reseller->roles->pluck('name')->implode(',') }}
                </td>
            </tr>
            -->

            <tr>
                <th>{{ __('views.admin.users.show.table_header_4') }}</th>
                <td>
                    @if($reseller->active)
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
                    @if($reseller->confirmed)
                        <span class="label label-success">{{ __('views.admin.users.show.confirmed') }}</span>
                    @else
                        <span class="label label-warning">{{ __('views.admin.users.show.not_confirmed') }}</span>
                    @endif</td>
                </td>
            </tr>
            -->

            <tr>
                <th>{{ __('views.admin.users.show.table_header_6') }}</th>
                <td>{{ $reseller->created_at }} ({{ $reseller->created_at->diffForHumans() }})</td>
            </tr>

            <tr>
                <th>{{ __('views.admin.users.show.table_header_7') }}</th>
                <td>{{ $reseller->updated_at }} ({{ $reseller->updated_at->diffForHumans() }})</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
