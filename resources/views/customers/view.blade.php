@extends('layouts.app', ['title' => $title, 'subTitle' => $subTitle])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Customer Details</div>
            <div class="card-body">
                <!-- Nav Tabs -->
                <ul class="nav nav-tabs" id="customerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" type="button" role="tab" aria-controls="customer" aria-selected="true">Customer</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="locations-tab" data-bs-toggle="tab" data-bs-target="#locations" type="button" role="tab" aria-controls="locations" aria-selected="false">Locations</button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3" id="customerTabsContent">
                    <!-- Customer Tab -->
                    <div class="tab-pane fade show active" id="customer" role="tabpanel" aria-labelledby="customer-tab">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <img src="{{ $engineer->profile ? asset('storage/users/profile/' . $engineer->profile) : asset('assets/images/profile.png') }}" alt="Profile" class="img-thumbnail" width="120">
                            </div>
                            <div class="col-md-9">
                                <h4>{{ $engineer->name }}</h4>
                                @if($engineer->alternate_name)
                                    <p><strong>Contact Name:</strong> {{ $engineer->alternate_name }}</p>
                                @endif
                                <p><strong>Email:</strong> {{ $engineer->email }}</p>
                                <p><strong>Phone:</strong> +{{ $engineer->alternate_dial_code }} {{ $engineer->alternate_phone_number }}</p>
                                <p><strong>Status:</strong> {!! $engineer->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>' !!}</p>
                            </div>
                        </div>
                        
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>

                    <!-- Locations Tab -->
                    <div class="tab-pane fade" id="locations" role="tabpanel" aria-labelledby="locations-tab">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Contact Person</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="locationsTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    const customerId = "{{ encrypt($engineer->id) }}";

    // Load locations when Locations tab is clicked
    $('#locations-tab').on('click', function() {
        loadLocations();
    });

    function loadLocations() {
        $.ajax({
            url: `/customers/${customerId}/locations`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    renderLocations(response.locations);
                }
            },
            error: function() {
                $('#locationsTableBody').html('<tr><td colspan="6" class="text-center text-danger">Failed to load locations</td></tr>');
            }
        });
    }

    function renderLocations(locations) {
        const tbody = $('#locationsTableBody');
        tbody.empty();

        if (locations.length === 0) {
            tbody.append('<tr><td colspan="6" class="text-center">No locations found</td></tr>');
            return;
        }

        locations.forEach(function(location) {
            const statusBadge = location.status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            const phone = location.dial_code ? `+${location.dial_code} ${location.phone_number}` : (location.phone_number || '-');
            
            // Build address
            const addressParts = [];
            if (location.address_line_1) addressParts.push(location.address_line_1);
            if (location.address_line_2) addressParts.push(location.address_line_2);
            if (location.cityr?.name) addressParts.push(location.cityr.name);
            if (location.stater?.name) addressParts.push(location.stater.name);
            if (location.countryr?.name) addressParts.push(location.countryr.name);
            const address = addressParts.length > 0 ? addressParts.join(', ') : '-';
            
            const row = `
                <tr>
                    <td>${location.name || '-'}</td>
                    <td>${location.contact_person || '-'}</td>
                    <td>${location.email || '-'}</td>
                    <td>${phone}</td>
                    <td>${address}</td>
                    <td>${statusBadge}</td>
                </tr>
            `;
            tbody.append(row);
        });
    }
});
</script>
@endpush