@extends('layouts.app', ['title' => $title, 'subTitle' => $subTitle])

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/intel-tel.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">

    <style>
        div.iti--inline-dropdown {
            min-width: 100% !important;
        }

        .iti__selected-flag {
            height: 32px !important;
        }

        .iti--show-flags {
            width: 100% !important;
        }

        label.error {
            color: red;
        }

        #phone_number,
        #location_phone_number {
            font-family: "Hind Vadodara", -apple-system, BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", Arial, sans-serif;
            font-size: 15px;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Add New Customer</div>
                <div class="card-body">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs" id="customerTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="customer-tab" data-bs-toggle="tab"
                                data-bs-target="#customer" type="button" role="tab" aria-controls="customer"
                                aria-selected="true">Customer</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="locations-tab" data-bs-toggle="tab" data-bs-target="#locations"
                                type="button" role="tab" aria-controls="locations" aria-selected="false">Locations</button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content mt-3" id="customerTabsContent">
                        <!-- Customer Tab -->
                        <div class="tab-pane fade show active" id="customer" role="tabpanel" aria-labelledby="customer-tab">
                            <form id="engineerForm" method="POST" action="{{ route('customers.store') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="locations" id="locations_data">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="name" class="form-label"> Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" placeholder="Name" value="{{ old('name') }}" required>
                                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-4 row">
                                            <div class="col-12">
                                                <label for="alternate_dial_code" class="form-label"> Phone Number <span
                                                        class="text-danger">*</span></label>
                                                <input type="hidden" name="alternate_dial_code" id="dial_code">
                                                <input type="tel"
                                                    class="form-control @error('alternate_phone_number') is-invalid @enderror"
                                                    id="phone_number" name="alternate_phone_number"
                                                    value="{{ old('alternate_phone_number') }}"
                                                    placeholder="Enter your phone number" required>
                                                @error('alternate_phone_number')<div class="invalid-feedback">{{ $message }}
                                                </div>@enderror
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="email" class="form-label">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                placeholder="Email" id="email" name="email" value="{{ old('email') }}"
                                                required>
                                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="profile" class="form-label">Profile Image</label>
                                            <input type="file" class="form-control @error('profile') is-invalid @enderror"
                                                id="profile" name="profile" accept="image/*">
                                            @error('profile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="status" class="form-label">Status <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status"
                                                name="status" required>
                                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active
                                                </option>
                                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive
                                                </option>
                                            </select>
                                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="status" class="form-label">Password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="Enter your Password" id="password" name="password" required>
                                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">Create Customer</button>
                                    <a href="{{ route('customers.index') }}" class="btn btn-secondary ms-3">Cancel</a>
                                </div>
                            </form>
                        </div>

                        <!-- Locations Tab -->
                        <div class="tab-pane fade" id="locations" role="tabpanel" aria-labelledby="locations-tab">
                            <div class="mb-3">
                                <button type="button" class="btn btn-primary" id="addLocationBtn">
                                    <i class="fa fa-plus"></i> Add Location
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="locationsTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Contact Person</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="7" class="text-center">No locations added yet</td>
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

    <!-- Add/Edit Location Modal -->
    <div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="locationModalLabel">Add Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="locationForm">
                    <div class="modal-body">
                        <input type="hidden" id="location_index" name="location_index">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location_name" class="form-label">Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="location_name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="location_contact_person" class="form-label">Contact Person Name</label>
                                    <input type="text" class="form-control" id="location_contact_person"
                                        name="contact_person">
                                </div>
                                <div class="mb-3">
                                    <label for="location_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="location_email" name="email">
                                </div>
                                <div class="mb-3">
                                    <label for="location_phone_number" class="form-label">Phone Number</label>
                                    <input type="hidden" name="dial_code" id="location_dial_code">
                                    <input type="tel" class="form-control" id="location_phone_number" name="phone_number">
                                </div>
                                <div class="mb-3">
                                    <label for="location_status" class="form-label">Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="location_status" name="status" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location_address_line_1" class="form-label">Address Line 1</label>
                                    <textarea name="address_line_1" id="location_address_line_1" class="form-control"
                                        rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="location_address_line_2" class="form-label">Address Line 2</label>
                                    <textarea name="address_line_2" id="location_address_line_2" class="form-control"
                                        rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="location_country" class="form-label">Country</label>
                                    <select name="country" id="location_country" class="form-select">
                                        <option value="">Select Country</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="location_state" class="form-label">State</label>
                                    <select name="state" id="location_state" class="form-select">
                                        <option value="">Select State</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="location_city" class="form-label">City</label>
                                    <select name="city" id="location_city" class="form-select">
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary" id="mapButton">
                                        <i class="fa fa-map-marker"></i> Map (Drop pin on Location)
                                    </button>
                                    <input type="hidden" name="location_url" id="location_location_url">
                                    <input type="hidden" name="latitude" id="location_latitude">
                                    <input type="hidden" name="longitude" id="location_longitude">
                                </div>
                                <div id="mapContainer" style="display: none;">
                                    <div class="search-fix mb-4">
                                        <input class="form-control" id="pac-input" type="text"
                                            placeholder="Search for a place" />
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </div>
                                    <div id="map" style="height: 400px; width: 100%; margin-top: 10px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveLocationBtn">Save Location</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/jquery-validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/intel-tel.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
    <script>
        $(document).ready(function () {

            let tempLocations = [];
            let locationIti;
            let map, marker, autocomplete;
            let countryNames = {};
            let stateNames = {};
            let cityNames = {};

            const input = document.querySelector('#phone_number');
            const locationInput = document.querySelector('#location_phone_number');
            const iti = window.intlTelInput(input, {
                initialCountry: "{{  Helper::$defaulDialCode  }}",
                separateDialCode: true,
                nationalMode: false,
                preferredCountries: @json(\App\Models\Country::select('iso2')->pluck('iso2')->toArray()),
                utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
            });
            input.addEventListener("countrychange", function () {
                if (iti.isValidNumber()) {
                    $('#dial_code').val(iti.s.dialCode);
                }
            });
            input.addEventListener('keyup', () => {
                if (iti.isValidNumber()) {
                    $('#dial_code').val(iti.s.dialCode);
                }
            });

            function handleLocationBarbadosUI(countryId) {
                const BARBADOS_ID = "20";

                if (countryId == BARBADOS_ID) {

                    $('#location_city').closest('div').hide();

                    $('label[for="location_state"]').text('Parish');

                    const statePlaceholder = $('#location_state').data('select2');
                    if (statePlaceholder) {
                        statePlaceholder.$container.find('.select2-selection__placeholder').text('Select Parish');
                    }
                } else {
                    $('#location_city').closest('div').show();

                    $('label[for="location_state"]').text('State');

                    const statePlaceholder = $('#location_state').data('select2');
                    if (statePlaceholder) {
                        statePlaceholder.$container.find('.select2-selection__placeholder').text('Select State');
                    }
                }
            }

            function initializeLocationSelect2() {
                $('#location_country').select2({
                    allowClear: true,
                    placeholder: 'Select country',
                    width: '100%',
                    dropdownParent: $('#locationModal'),
                    ajax: {
                        url: "{{ route('country-list') }}",
                        type: "POST",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                searchQuery: params.term,
                                page: params.page || 1,
                                _token: "{{ csrf_token() }}"
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: $.map(data.items, function (item) {
                                    countryNames[item.id] = item.text;
                                    return {
                                        id: item.id,
                                        text: item.text
                                    };
                                }),
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        },
                        cache: true
                    }
                });

                $('#location_country').on('change', function () {
                    handleLocationBarbadosUI($(this).val());
                });

                $('#location_state').select2({
                    allowClear: true,
                    placeholder: 'Select State',
                    width: '100%',
                    dropdownParent: $('#locationModal'),
                    ajax: {
                        url: "{{ route('state-list') }}",
                        type: "POST",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                searchQuery: params.term,
                                page: params.page || 1,
                                country_id: $('#location_country').val(),
                                _token: "{{ csrf_token() }}"
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: $.map(data.items, function (item) {
                                    stateNames[item.id] = item.text;
                                    return {
                                        id: item.id,
                                        text: item.text
                                    };
                                }),
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        },
                        cache: true
                    }
                });

                $('#location_city').select2({
                    allowClear: true,
                    placeholder: 'Select city',
                    width: '100%',
                    dropdownParent: $('#locationModal'),
                    ajax: {
                        url: "{{ route('city-list') }}",
                        type: "POST",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                searchQuery: params.term,
                                page: params.page || 1,
                                state_id: $('#location_state').val(),
                                _token: "{{ csrf_token() }}"
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: $.map(data.items, function (item) {
                                    cityNames[item.id] = item.text;
                                    return {
                                        id: item.id,
                                        text: item.text
                                    };
                                }),
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        },
                        cache: true
                    }
                });
            }

            initializeLocationSelect2();

            function renderTempLocations() {
                const tbody = $('#locationsTable tbody');
                tbody.empty();

                if (tempLocations.length === 0) {
                    tbody.append('<tr><td colspan="7" class="text-center">No locations added yet</td></tr>');
                    return;
                }

                tempLocations.forEach(function (location, index) {
                    const statusBadge = location.status == '1' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                    const phone = location.dial_code ? `+${location.dial_code} ${location.phone_number}` : (location.phone_number || '-');

                    const addressParts = [];
                    if (location.address_line_1) addressParts.push(location.address_line_1);
                    if (location.city_name) addressParts.push(location.city_name);
                    if (location.state_name) addressParts.push(location.state_name);
                    if (location.country_name) addressParts.push(location.country_name);
                    const address = addressParts.length > 0 ? addressParts.join(', ') : '-';

                    const row = `
                            <tr>
                                <td>${location.name || '-'}</td>
                                <td>${location.contact_person || '-'}</td>
                                <td>${location.email || '-'}</td>
                                <td>${phone}</td>
                                <td>${address}</td>
                                <td>${statusBadge}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary edit-temp-location" data-index="${index}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-temp-location" data-index="${index}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    tbody.append(row);
                });

                $('#locations_data').val(JSON.stringify(tempLocations));
            }

            $('#addLocationBtn').on('click', function () {
                $('#locationModalLabel').text('Add Location');
                $('#locationForm')[0].reset();
                $('#location_index').val('');
                $('#location_dial_code').val(''); // Clear dial code
                $('#location_country').val(null).trigger('change');
                $('#location_state').val(null).trigger('change');
                $('#location_city').val(null).trigger('change');

                if (locationIti) {
                    locationIti.setCountry("{{  Helper::$defaulDialCode  }}");
                    locationIti.setNumber('');

                    setTimeout(() => {
                        if (locationIti && locationIti.s) {
                            $('#location_dial_code').val(locationIti.s.dialCode);
                        }
                    }, 100);
                }

                $('#locationModal').modal('show');
            });

            $('#locationModal').on('shown.bs.modal', function () {
                if (!locationIti) {
                    locationIti = window.intlTelInput(locationInput, {
                        initialCountry: "{{  Helper::$defaulDialCode  }}",
                        separateDialCode: true,
                        nationalMode: false,
                        preferredCountries: @json(\App\Models\Country::select('iso2')->pluck('iso2')->toArray()),
                        utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
                    });

                    locationInput.addEventListener("countrychange", function () {

                        if (locationIti && locationIti.s && locationIti.s.dialCode) {
                            $('#location_dial_code').val(locationIti.s.dialCode);
                        }
                    });

                    locationInput.addEventListener('blur', () => {

                        if (locationIti && locationIti.s && locationIti.s.dialCode) {
                            $('#location_dial_code').val(locationIti.s.dialCode);
                        }
                    });

                    locationInput.addEventListener('keyup', () => {

                        if (locationIti && locationIti.s && locationIti.s.dialCode) {
                            $('#location_dial_code').val(locationIti.s.dialCode);
                        }
                    });
                }
            });

            $(document).on('click', '.edit-temp-location', function () {
                const index = $(this).data('index');
                const location = tempLocations[index];

                $('#locationModalLabel').text('Edit Location');
                $('#location_index').val(index);
                $('#location_name').val(location.name);
                $('#location_contact_person').val(location.contact_person);
                $('#location_email').val(location.email);
                $('#location_status').val(location.status);
                $('#location_address_line_1').val(location.address_line_1);
                $('#location_address_line_2').val(location.address_line_2);
                $('#location_latitude').val(location.latitude);
                $('#location_longitude').val(location.longitude);
                $('#location_location_url').val(location.location_url);

                if (location.phone_number && locationIti) {

                    const fullNumber = location.dial_code ? `+${location.dial_code}${location.phone_number}` : location.phone_number;
                    locationIti.setNumber(fullNumber);

                    $('#location_dial_code').val(location.dial_code || '');
                } else if (locationIti) {
                    locationIti.setNumber('');
                    $('#location_dial_code').val('');
                }

                if (location.country) {
                    $('#location_country').append(new Option(location.country_name, location.country, true, true));

                    handleLocationBarbadosUI(location.country);
                }
                if (location.state) {
                    $('#location_state').append(new Option(location.state_name, location.state, true, true));
                }
                if (location.city) {
                    $('#location_city').append(new Option(location.city_name, location.city, true, true));
                }

                $('#locationModal').modal('show');
            });

            $(document).on('click', '.delete-temp-location', function () {
                const index = $(this).data('index');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This location will be removed from the list!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        tempLocations.splice(index, 1);
                        renderTempLocations();
                        Swal.fire('Deleted!', 'Location has been removed.', 'success');
                    }
                });
            });

            $('#locationForm').on('submit', function (e) {
                e.preventDefault();

                const index = $('#location_index').val();

                // Always capture dial code from iti instance if available
                if (locationIti && locationIti.s && locationIti.s.dialCode) {
                    $('#location_dial_code').val(locationIti.s.dialCode);
                }

                const locationData = {
                    name: $('#location_name').val(),
                    contact_person: $('#location_contact_person').val(),
                    email: $('#location_email').val(),
                    dial_code: $('#location_dial_code').val(),
                    phone_number: $('#location_phone_number').val(),
                    address_line_1: $('#location_address_line_1').val(),
                    address_line_2: $('#location_address_line_2').val(),
                    country: $('#location_country').val(),
                    country_name: countryNames[$('#location_country').val()] || $('#location_country option:selected').text(),
                    state: $('#location_state').val(),
                    state_name: stateNames[$('#location_state').val()] || $('#location_state option:selected').text(),
                    city: $('#location_city').val(),
                    city_name: cityNames[$('#location_city').val()] || $('#location_city option:selected').text(),
                    status: $('#location_status').val(),
                    latitude: $('#location_latitude').val(),
                    longitude: $('#location_longitude').val(),
                    location_url: $('#location_location_url').val()
                };

                if (index !== '') {
                    tempLocations[parseInt(index)] = locationData;
                } else {
                    tempLocations.push(locationData);
                }

                renderTempLocations();
                $('#locationModal').modal('hide');
                Swal.fire('Success', 'Location saved successfully!', 'success');
            });

            $('#mapButton').click(function () {
                $('#mapContainer').toggle();
                if ($('#mapContainer').is(':visible')) {
                    initMap();
                }
            });

            function initMap() {
                if (map) return;

                let defaultLocation;
                if ($('#location_latitude').val() && $('#location_longitude').val()) {
                    defaultLocation = {
                        lat: parseFloat($('#location_latitude').val()),
                        lng: parseFloat($('#location_longitude').val())
                    };
                } else {
                    defaultLocation = { lat: 13.174103138553395, lng: -59.55183389025077 };
                }

                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 15,
                    center: defaultLocation,
                });

                marker = new google.maps.Marker({
                    position: defaultLocation,
                    map: map,
                    draggable: true
                });

                autocomplete = new google.maps.places.Autocomplete(
                    document.getElementById('pac-input'),
                    { types: ['geocode'] }
                );

                autocomplete.bindTo('bounds', map);

                autocomplete.addListener('place_changed', function () {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) {
                        return;
                    }

                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);
                    }

                    marker.setPosition(place.geometry.location);
                    updateLocationFields(place.geometry.location.lat(), place.geometry.location.lng(), place.formatted_address);
                });

                google.maps.event.addListener(marker, 'dragend', function () {
                    const position = marker.getPosition();
                    const geocoder = new google.maps.Geocoder();

                    geocoder.geocode({ location: position }, function (results, status) {
                        if (status === 'OK' && results[0]) {
                            updateLocationFields(position.lat(), position.lng(), results[0].formatted_address);
                        } else {
                            updateLocationFields(position.lat(), position.lng(), '');
                        }
                    });
                });

                google.maps.event.addListener(map, 'click', function (event) {
                    marker.setPosition(event.latLng);
                    const geocoder = new google.maps.Geocoder();

                    geocoder.geocode({ location: event.latLng }, function (results, status) {
                        if (status === 'OK' && results[0]) {
                            updateLocationFields(event.latLng.lat(), event.latLng.lng(), results[0].formatted_address);
                        } else {
                            updateLocationFields(event.latLng.lat(), event.latLng.lng(), '');
                        }
                    });
                });
            }

            function updateLocationFields(lat, lng, address) {
                $('#location_latitude').val(lat);
                $('#location_longitude').val(lng);
                $('#location_location_url').val(address);
            }

            $('#locationModal').on('hidden.bs.modal', function () {
                map = null;
                $('#mapContainer').hide();
            });

            $('#engineerForm').validate({
                rules: {
                    name: { required: true },
                    email: { required: true, email: true },
                    alternate_dial_code: { required: true },
                    status: { required: true },
                    password: { required: true }
                },
                errorPlacement: function (error, element) {
                    if (element.attr('id') === 'phone_number') {
                        error.insertAfter(element.parent());
                    } else {
                        error.appendTo(element.parent());
                    }
                },
                submitHandler: function (form) {
                    $('#dial_code').val(iti.s.dialCode);

                    $('#locations_data').val(JSON.stringify(tempLocations));

                    $('body').find('.LoaderSec').removeClass('d-none');
                    form.submit();
                }
            });
        });
    </script>
@endpush