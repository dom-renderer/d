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
                <div class="card-header">Edit Customer</div>
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
                            <form id="engineerForm" method="POST"
                                action="{{ route('customers.update', encrypt($engineer->id)) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $engineer->name) }}" required>
                                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3 row">
                                            <div class="col-12">
                                                <label for="phone_number" class="form-label">Phone Number <span
                                                        class="text-danger">*</span></label>
                                                <input type="hidden" name="alternate_dial_code" id="dial_code">
                                                <input type="tel"
                                                    class="form-control @error('alternate_phone_number') is-invalid @enderror"
                                                    id="phone_number" name="alternate_phone_number"
                                                    value="{{ old('alternate_phone_number', $engineer->alternate_phone_number) }}"
                                                    required>
                                                @error('alternate_phone_number')<div class="invalid-feedback">{{ $message }}
                                                </div>@enderror
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" name="email" value="{{ old('email', $engineer->email) }}"
                                                required>
                                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="profile" class="form-label">Profile Image</label>
                                            <input type="file" class="form-control @error('profile') is-invalid @enderror"
                                                id="profile" name="profile" accept="image/*">
                                            @if($engineer->profile)
                                                <img src="{{ asset('storage/users/profile/' . $engineer->profile) }}"
                                                    alt="Profile" class="img-thumbnail mt-2" width="80">
                                            @endif
                                            @error('profile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status"
                                                name="status" required>
                                                <option value="1" {{ old('status', $engineer->status) == '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ old('status', $engineer->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" id="password"
                                                name="password">
                                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            <small class="form-text text-muted">Leave blank to keep unchanged.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">Update Customer</button>
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
                                        <!-- Locations will be loaded via AJAX -->
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
                        <input type="hidden" id="location_id" name="location_id">
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

            const customerId = "{{ encrypt($engineer->id) }}";
            let locationIti;
            let map, marker, autocomplete;

            const input = document.querySelector('#phone_number');
            const locationInput = document.querySelector('#location_phone_number');
            const iti = window.intlTelInput(input, {
                initialCountry: "{{ Helper::getIso2ByDialCode($engineer->alternate_dial_code) }}",
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

            $('#engineerForm').validate({
                rules: {
                    name: { required: true },
                    email: { required: true, email: true },
                    alternate_dial_code: { required: true },
                    status: { required: true }
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
                    $('body').find('.LoaderSec').removeClass('d-none');
                    form.submit();
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

            $('#locations-tab').on('click', function () {
                loadLocations();
            });

            function loadLocations() {
                $.ajax({
                    url: "{{ route('customers.locations.index', encrypt($engineer->id)) }}",
                    type: 'GET',
                    success: function (response) {
                        if (response.success) {
                            renderLocations(response.locations);
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Failed to load locations', 'error');
                    }
                });
            }

            function renderLocations(locations) {
                const tbody = $('#locationsTable tbody');
                tbody.empty();

                if (locations.length === 0) {
                    tbody.append('<tr><td colspan="7" class="text-center">No locations found</td></tr>');
                    return;
                }

                locations.forEach(function (location) {
                    const statusBadge = location.status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                    const phone = location.dial_code ? `+${location.dial_code} ${location.phone_number}` : (location.phone_number || '-');
                    const address = [location.address_line_1, location.cityr?.name, location.stater?.name, location.countryr?.name].filter(Boolean).join(', ') || '-';

                    const row = `
                            <tr>
                                <td>${location.name || '-'}</td>
                                <td>${location.contact_person || '-'}</td>
                                <td>${location.email || '-'}</td>
                                <td>${phone}</td>
                                <td>${address}</td>
                                <td>${statusBadge}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-location" data-id="${location.id}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-location" data-id="${location.id}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    tbody.append(row);
                });
            }

            $('#addLocationBtn').on('click', function () {
                $('#locationModalLabel').text('Add Location');
                $('#locationForm')[0].reset();
                $('#location_id').val('');
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

            $(document).on('click', '.edit-location', function () {
                const locationId = $(this).data('id');

                $.ajax({
                    url: `/locations/${locationId}`,
                    type: 'GET',
                    success: function (response) {
                        if (response.success) {
                            const location = response.location;

                            $('#locationModalLabel').text('Edit Location');
                            $('#location_id').val(location.id);
                            $('#location_name').val(location.name);
                            $('#location_contact_person').val(location.contact_person);
                            $('#location_email').val(location.email);
                            $('#location_status').val(location.status ? '1' : '0');
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

                            if (location.country && location.countryr) {
                                $('#location_country').append(new Option(location.countryr.name, location.country, true, true));

                                handleLocationBarbadosUI(location.country);
                            }
                            if (location.state && location.stater) {
                                $('#location_state').append(new Option(location.stater.name, location.state, true, true));
                            }
                            if (location.city && location.cityr) {
                                $('#location_city').append(new Option(location.cityr.name, location.city, true, true));
                            }

                            $('#locationModal').modal('show');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Failed to load location details', 'error');
                    }
                });
            });

            $(document).on('click', '.delete-location', function () {
                const locationId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/locations/${locationId}`,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    loadLocations();
                                }
                            },
                            error: function () {
                                Swal.fire('Error', 'Failed to delete location', 'error');
                            }
                        });
                    }
                });
            });

            $('#locationForm').on('submit', function (e) {
                e.preventDefault();

                const locationId = $('#location_id').val();
                const url = locationId ? `/locations/${locationId}` : "{{ route('customers.locations.store', encrypt($engineer->id)) }}";
                const method = locationId ? 'PUT' : 'POST';

                if (locationIti && locationIti.s && locationIti.s.dialCode) {
                    $('#location_dial_code').val(locationIti.s.dialCode);
                }

                const formData = $(this).serializeArray();
                const data = {};
                formData.forEach(item => {
                    data[item.name] = item.value;
                });
                data._token = "{{ csrf_token() }}";

                $.ajax({
                    url: url,
                    type: method,
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Success', response.message, 'success');
                            $('#locationModal').modal('hide');
                            loadLocations();
                        }
                    },
                    error: function (xhr) {
                        let errorMessage = 'Failed to save location';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage = errors.join('<br>');
                        }
                        Swal.fire('Error', errorMessage, 'error');
                    }
                });
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
        });
    </script>
@endpush