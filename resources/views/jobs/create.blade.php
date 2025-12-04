@extends('layouts.app', ['title' => $title, 'subTitle' => $subTitle, 'rightSideTitle' => Helper::jobCode(), 'datepicker' => true, 'rightSideFilter' => $rightSideFilter])

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/intel-tel.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.datetimepicker.css') }}">

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

        #phone_number {
            font-family: "Hind Vadodara", -apple-system, BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", Arial, sans-serif;
            font-size: 15px;
        }

        #map {
            height: 400px;
            width: 100%;
            margin-top: 10px;
        }

        .pac-card {
            background-color: #fff;
            border: 0;
            border-radius: 2px;
            box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
            margin: 10px;
            padding: 0 0.5em;
            font: 400 18px Roboto, Arial, sans-serif;
            overflow: hidden;
            font-family: Roboto;
            padding: 0;
        }

        .pac-container {
            z-index: 9999 !important;
        }

        #pac-container {
            padding-bottom: 12px;
            margin-right: 12px;
            z-index: 999999999;
        }

        .pac-controls {
            display: inline-block;
            padding: 5px 11px;
        }

        .pac-controls label {
            font-family: Roboto;
            font-size: 13px;
            font-weight: 300;
        }

        #pac-input {
            background-color: #fff;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            text-overflow: ellipsis;
        }

        #pac-input:focus {
            border-color: #4d90fe;
        }

        .map-search-container {
            margin-bottom: 10px;
        }

        .table.table-striped thead th {
            border: none;
        }

        .table.table-striped #services-container td,
        .table.table-striped #materials-container td {
            background: none !important;
            border: none;
            box-shadow: none;
        }

        .add-remove-container {
            width: 10%;
        }

        .add-remove-container .btn {
            padding: 0;
        }

        @media screen and (max-width: 1100px) {
            .ovfl-mn {
                overflow: auto;
            }

            .ovfl-mn .table.table-striped {
                width: 1100px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    @if ($errors->any())
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('jobs.store') }}" enctype="multipart/form-data" id="customerForm">
                        @csrf

                        <div class="mb-4">
                            {{-- <label for="customer" class="form-label"> Customer <span
                                    class="text-danger">*</span></label> --}}
                            <select name="customer" id="customer" required>

                            </select>
                            @error('customer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4" id="location_container" style="display:none;">
                            <select name="location_id" id="location" required>
                                <option value="">Select Location</option>
                            </select>
                            @error('location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row mb-2 mt-2">
                            {{-- <div class="col-5" style="border-right: 1px solid #0000001f;"> --}}

                                <div class="col-5">

                                    <div class="mb-4">
                                        {{-- <label for="customer_name" class="form-label"> Contact Name <span
                                                class="text-danger">*</span></label> --}}
                                        <input type="text" class="form-control " id="customer_name" name="customer_name"
                                            placeholder="Contact Name" value="{{ old('customer_name') }}" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-4">
                                        {{-- <label for="customer_email" class="form-label"> Email <span
                                                class="text-danger">*</span></label> --}}
                                        <input type="text" class="form-control" id="customer_email" name="customer_email"
                                            value="{{ old('customer_email') }}" placeholder="Email" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-4">
                                        {{-- <label for="customer_address_line_1" class="form-label"> Address Line 1 <span
                                                class="text-danger">*</span></label> --}}
                                        <textarea name="customer_address_line_1" id="customer_address_line_1"
                                            placeholder="Address Line 1" class="form-control"
                                            required>{{ old('customer_address_line_1') }}</textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                </div>
                                <div class="col-7">
                                    <div class="mb-4">
                                        {{-- <label for="customer_billing_name" class="form-label"> Billing Name <span
                                                class="text-danger">*</span></label> --}}
                                        <input type="text" class="form-control" id="customer_billing_name"
                                            placeholder="Billing Name" name="customer_billing_name"
                                            value="{{ old('customer_billing_name') }}" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="col-12">
                                            {{-- <label for="customer_phone_number" class="form-label">Phone Number <span
                                                    class="text-danger">*</span></label> <br> --}}
                                            <input type="hidden" name="customer_alternate_dial_code"
                                                id="customer_dial_code">
                                            <input type="tel" class="form-control" id="customer_phone_number"
                                                name="customer_alternate_phone_number" placeholder="Phone Number"
                                                value="{{ old('customer_alternate_phone_number') }}" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        {{-- <label for="customer_address_line_2" class="form-label"> Address Line 2
                                        </label> --}}
                                        <textarea name="customer_address_line_2" id="customer_address_line_2"
                                            placeholder="Address Line 2"
                                            class="form-control">{{ old('customer_address_line_2') }}</textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="mb-5">
                                        <button type="button" class="btn btn-primary" id="mapButton2">
                                            Map (Drop pin on Location)
                                        </button>
                                        <input type="hidden" name="customer_location_url" id="customer_location_url">
                                        <input type="hidden" name="customer_latitude" id="customer_latitude">
                                        <input type="hidden" name="customer_longitude" id="customer_longitude">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-4 mt-5">
                                <div class="col-12 mb-3">
                                    <label for="title"> Title <span class="text-danger"> * </span> </label>
                                    <input type="text" id="title" name="title" class="form-control" placeholder="Title"
                                        value="{{ old('title') }}" required>
                                </div>
                                <div class="col-4 datemng">
                                    <label for="opening_date" class="d-inline-block mb-3"> Job Added Date <span
                                            class="text-danger "> * </span> </label>
                                    <input type="text" id="opening_date" name="opening_date"
                                        class="form-control dms_datepicker"
                                        value="{{ old('opening_date', \Carbon\Carbon::now()->format('d-m-Y h:i')) }}"
                                        required>
                                </div>
                                <div class="col-4 datemng">
                                    <label for="visiting_date" class="d-inline-block mb-3">Visiting Date <span
                                            class="text-danger"> *</span></label>
                                    <input type="text" id="visiting_date" name="visiting_date"
                                        class="form-control dms_datepicker"
                                        value="{{ old('visiting_date', \Carbon\Carbon::now()->format('d-m-Y h:i')) }}"
                                        required>
                                </div>
                                {{-- <div class="col-3 datemng">
                                    <label for="expected_date" class="d-inline-block mb-3">Expected Service Date<span
                                            class="text-danger"> *</span></label>
                                    <input type="text" id="expected_date" name="expected_date"
                                        class="form-control dms_datepicker" value="{{ old( 'expected_date' ) }}" readonly
                                        required>
                                </div> --}}
                                <div class="col-4 datemng">
                                    <label class="d-inline-block mb-3">Approx Time <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="dms_days"
                                            value="{{ old('dms_days') }}" id="dms_days" min="0" placeholder="Days">
                                        <span class="input-group-text">Days</span>

                                        <input type="number" class="form-control" name="dms_hours"
                                            value="{{ old('dms_hours') }}" id="dms_hours" min="0" max="23"
                                            placeholder="Hours">
                                        <span class="input-group-text">Hours</span>

                                        <input type="number" class="form-control" name="dms_minutes"
                                            value="{{ old('dms_minutes') }}" id="dms_minutes" min="0" max="59"
                                            placeholder="Minutes">
                                        <span class="input-group-text">Minutes</span>
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <label for="expertise"> Expertise </label>
                                    <select name="expertise[]" id="expertise" multiple></select>
                                </div>
                                <div class="col-12 mt-4">
                                    <label for="engineers"> Engineers <span class="text-danger"> * </span> </label>
                                    <select name="engineers[]" id="engineers" multiple></select>
                                </div>
                                <div class="col-12 mt-4">
                                    <label for="technicians"> Technicians <span class="text-danger"> * </span> </label>
                                    <select name="technicians[]" id="technicians" multiple></select>
                                </div>
                                <div class="col-12 mt-4">
                                    <label for="description"> Description <span class="text-danger"> * </span> </label>
                                    <textarea name="description" placeholder="Description" id="description"
                                        class="form-control" required></textarea>
                                </div>
                                <div class="col-12 mt-4">
                                    <label for="summary"> Summary </label>
                                    <textarea name="summary" id="summary" placeholder="Summary"
                                        class="form-control"></textarea>
                                </div>
                                <div class="col-12 mt-4">
                                    <label for="dms_attachment" class="d-inline-block mb-3">Attach Photos</label>
                                    <input type="file" name="dms_attachment[]" id="dms_attachment" class="form-control"
                                        accept="image/*" multiple>
                                </div>
                                <div class="col-12 mt-4">
                                    <label for="dms_job_priority" class="d-inline-block mb-3">Job Priority</label>
                                    <select class="form-select" name="dms_job_priority" id="dms_job_priority">
                                        <option value="LOW" @if(old('dms_job_priority') == 'LOW') selected @endif>Low Priority
                                        </option>
                                        <option value="NORMAL" @if(old('dms_job_priority') == 'NORMAL') selected @endif>Normal
                                            Priority</option>
                                        <option value="HIGH" @if(old('dms_job_priority') == 'HIGH') selected @endif>High
                                            Priority</option>
                                        <option value="CRITICAL" @if(old('dms_job_priority') == 'CRITICAL') selected @endif>
                                            Critical Priority</option>
                                    </select>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-3">
                                <h4> Deposit Information </h4>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="requires_deposit" class="form-label"> Requires Deposit </label>
                                        <select name="requires_deposit" id="requires_deposit" class="form-select">
                                            <option value="0" @if(old('requires_deposit') == 0) selected @endif>No</option>
                                            <option value="1" @if(old('requires_deposit') == 1) selected @endif>Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 deposit-fields" style="display: none;">
                                    <div class="mb-3">
                                        <label for="deposit_type" class="form-label"> Deposit Type </label>
                                        <select name="deposit_type" id="deposit_type" class="form-select">
                                            <option value="FIX" @if(old('deposit_type') == 'FIX') selected @endif>Fixed Amount
                                            </option>
                                            <option value="PERCENT" @if(old('deposit_type') == 'PERCENT') selected @endif>
                                                Percentage</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 deposit-fields" style="display: none;">
                                    <div class="mb-3">
                                        <label for="deposit_amount" class="form-label"> Deposit Amount </label>
                                        <input type="number" name="deposit_amount" id="deposit_amount" class="form-control"
                                            value="{{ old('deposit_amount') }}" step="0.01" min="0">
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="accordion dms_accordion pt-3" id="dms_job_data">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Services
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                        data-bs-parent="#dms_job_data">
                                        <div class="accordion-body">
                                            <button type="button" class="service-row-add btn  btn-primary float-end"><i
                                                    class="fa fa-plus"></i>Add Item</button>
                                            <div class="row mb-3 mt-6 d-block">
                                                <div class="col-12 ovfl-mn">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Title</th>
                                                                <th>Description</th>
                                                                <th width="10%">Quantity</th>
                                                                <th width="10%">Price</th>
                                                                <th width="10%">Total Amount</th>
                                                                <th width="7%">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="services-container">
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="5"> Total </td>
                                                                <td colspan="2" id="service-total">
                                                                    0.00
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- Services Discount and VAT Section -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <h5 class="text-primary mb-3 sml-hd">Services Discount</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="services_discount_type"
                                                                    class="form-label">Discount Type</label>
                                                                <select name="services_discount_type"
                                                                    id="services_discount_type" class="form-select">
                                                                    <option value="FIX">Fixed Amount</option>
                                                                    <option value="PERCENT">Percentage</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="services_discount_amount"
                                                                    class="form-label">Discount Amount</label>
                                                                <input type="number" name="services_discount_amount"
                                                                    id="services_discount_amount" class="form-control"
                                                                    step="0.01" min="0" value="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5 class="text-primary mb-3 sml-hd">Services VAT</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="services_vat_type" class="form-label">VAT
                                                                    Type</label>
                                                                <select name="services_vat_type" id="services_vat_type"
                                                                    class="form-select">
                                                                    <option value="PERCENT">Percentage</option>
                                                                    <option value="FIX">Fixed Amount</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="services_vat_amount" class="form-label">VAT
                                                                    Amount</label>
                                                                <input type="number" name="services_vat_amount"
                                                                    id="services_vat_amount" class="form-control"
                                                                    step="0.01" min="0" value="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Services Total Section -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card serice-tabe">
                                                        <div class="card-body border-0">
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Services Subtotal</label>
                                                                        <div class="form-control-plaintext"
                                                                            id="services-subtotal-display">$0.00</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="mb-3">
                                                                        <label class="form-label head-sml">Services
                                                                            Discount</label>
                                                                        <div class="form-control-plaintext"
                                                                            id="services-discount-display">$0.00</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Services VAT</label>
                                                                        <div class="form-control-plaintext"
                                                                            id="services-vat-display">$0.00</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="mb-3">
                                                                        <label class="form-label"><strong>Services
                                                                                Total</strong></label>
                                                                        <div class="form-control-plaintext"
                                                                            id="services-total-display">
                                                                            <strong>$0.00</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#dms_requisitions" aria-expanded="false"
                                            aria-controls="dms_requisitions">
                                            Requisitions
                                        </button>
                                    </h2>
                                    <div id="dms_requisitions" class="accordion-collapse collapse"
                                        aria-labelledby="headingThree" data-bs-parent="#dms_job_data">
                                        <div class="accordion-body">
                                            <button type="button" class="requisition-row-add btn btn-primary float-end"><i
                                                    class="fa fa-plus"></i>Add Item</button>
                                            <div class="col-12 mt-6 ovfl-mn d-block">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Type</th>
                                                            <th>Product</th>
                                                            <th>Description</th>
                                                            <th>Quantity</th>
                                                            <th>Price</th>
                                                            <th>Total</th>
                                                            <th width="145px">Status</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="requisition-items"></tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="6"> Total </td>
                                                            <td colspan="2" id="requisition-grand-total-column">
                                                                0.00
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5">
                                <input type="hidden" name="dms_job_status" id="dms_job_status_hidden" value="PENDING">
                                <button type="submit" class="btn btn-primary">Create Job</button>
                                <a href="{{ route('jobs.index') }}" class="btn btn-secondary ms-3">Cancel</a>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="newCustomerAdditionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="newCustomerAdditionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form action="{{ route('customers.store') }}?response_type=json" method="POST" id="engineerForm"
                enctype="multipart/form-data"> @csrf
                <input type="hidden" name="modal_locations" id="modal_locations_data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="newCustomerAdditionModalLabel">Add Customer</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs mb-3" id="modalCustomerTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="modal-customer-tab" data-bs-toggle="tab"
                                    data-bs-target="#modal-customer" type="button" role="tab">Customer</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="modal-locations-tab" data-bs-toggle="tab"
                                    data-bs-target="#modal-locations" type="button" role="tab">Locations</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="modalCustomerTabsContent">
                            <div class="tab-pane fade show active" id="modal-customer" role="tabpanel">
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="name" class="form-label">Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                                value="" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-4 row">
                                            <div class="col-12">
                                                <label for="phone_number" class="form-label">Phone Number <span
                                                        class="text-danger">*</span></label> <br>
                                                <input type="hidden" name="alternate_dial_code" id="dial_code">
                                                <input type="tel" class="form-control" id="phone_number"
                                                    name="alternate_phone_number" value="" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="email" class="form-label">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="Email" value="{{ old('email') }}" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="profile" class="form-label">Profile Image</label>
                                            <input type="file" class="form-control" id="profile" name="profile"
                                                accept="image/*">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="status" class="form-label">Status <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="password" class="form-label">Password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" placeholder="Password" id="password"
                                                name="password" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="modal-locations" role="tabpanel">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary" id="modalAddLocationBtn">
                                        <i class="fa fa-plus"></i> Add Location
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="modalLocationsTable">
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary ms-3"> Save </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Location Modal for Customer Modal -->
    <div class="modal fade" id="modalLocationModal" tabindex="-1" aria-labelledby="modalLocationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLocationModalLabel">Add Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="modalLocationForm">
                    <div class="modal-body">
                        <input type="hidden" id="modal_location_index" name="location_index">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modal_location_name" class="form-label">Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="modal_location_name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="modal_location_contact_person" class="form-label">Contact Person
                                        Name</label>
                                    <input type="text" class="form-control" id="modal_location_contact_person"
                                        name="contact_person">
                                </div>
                                <div class="mb-3">
                                    <label for="modal_location_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="modal_location_email" name="email">
                                </div>
                                <div class="mb-3">
                                    <label for="modal_location_phone_number" class="form-label">Phone Number</label>
                                    <input type="hidden" name="dial_code" id="modal_location_dial_code">
                                    <input type="tel" class="form-control" id="modal_location_phone_number"
                                        name="phone_number">
                                </div>
                                <div class="mb-3">
                                    <label for="modal_location_status" class="form-label">Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="modal_location_status" name="status" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modal_location_address_line_1" class="form-label">Address Line 1</label>
                                    <textarea name="address_line_1" id="modal_location_address_line_1" class="form-control"
                                        rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="modal_location_address_line_2" class="form-label">Address Line 2</label>
                                    <textarea name="address_line_2" id="modal_location_address_line_2" class="form-control"
                                        rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="modal_location_country" class="form-label">Country</label>
                                    <select name="country" id="modal_location_country" class="form-select">
                                        <option value="">Select Country</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="modal_location_state" class="form-label">State</label>
                                    <select name="state" id="modal_location_state" class="form-select">
                                        <option value="">Select State</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="modal_location_city" class="form-label">City</label>
                                    <select name="city" id="modal_location_city" class="form-select">
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary" id="modalMapButton">
                                        <i class="fa fa-map-marker"></i> Map (Drop pin on Location)
                                    </button>
                                    <input type="hidden" name="location_url" id="modal_location_location_url">
                                    <input type="hidden" name="latitude" id="modal_location_latitude">
                                    <input type="hidden" name="longitude" id="modal_location_longitude">
                                </div>
                                <div id="modalMapContainer" style="display: none;">
                                    <div class="search-fix mb-4">
                                        <input class="form-control" id="modal-pac-input" type="text"
                                            placeholder="Search for a place" />
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </div>
                                    <div id="modal-map" style="height: 400px; width: 100%; margin-top: 10px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Location</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Customer Map Modal -->
    <div class="modal fade" id="customerMapModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="customerMapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="customerMapModalLabel">Select Customer Location</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="map-search-container">
                        <div class="search-fix mb-4">
                            <input class="form-control" id="customer-pac-input" type="text"
                                placeholder="Search for a place" />
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div id="customer-map" style="height: 400px; width: 100%;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary ms-3" id="confirmCustomerLocation">Confirm
                        Location</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/jquery-validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/intel-tel.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.datetimepicker.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
    <script>
        $(document).ready(function () {

            handleBarbadosUI($('#country').val());

            $(document).on('change', '#country', function () {
                handleBarbadosUI($(this).val());
            });

            $('#expertise').select2({
                placeholder: 'Select an expertise',
                width: '100%',
                ajax: {
                    url: "{{ route('expertise-list') }}",
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
                },
                templateResult: function (data) {
                    if (data.loading) {
                        return data.text;
                    }

                    var $result = $('<span></span>');
                    $result.text(data.text);
                    return $result;
                }
            });

            $('.dms_datepicker').datetimepicker({
                format: 'd-m-Y H:i',
                onSelectDate: function (ct, $input) {
                    $input.trigger('change');
                },
                onSelectTime: function (ct, $input) {
                    $input.trigger('change');
                }
            });

            let customerInput = document.querySelector('#customer_phone_number');
            let customerIti = window.intlTelInput(customerInput, {
                initialCountry: "{{  Helper::$defaulDialCode  }}",
                separateDialCode: true,
                nationalMode: false,
                preferredCountries: @json(\App\Models\Country::select('iso2')->pluck('iso2')->toArray()),
                utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
            });

            customerInput.addEventListener("countrychange", function () {
                if (customerIti.isValidNumber()) {
                    $('#customer_dial_code').val(customerIti.s.dialCode);
                }
            });
            customerInput.addEventListener('keyup', () => {
                if (customerIti.isValidNumber()) {
                    $('#customer_dial_code').val(customerIti.s.dialCode);
                }
            });

            let customerMap, customerMarker, customerSearchBox;

            $('#mapButton2').on('click', function () {
                if (!customerMap) {
                    let lat = $('#customer_latitude').val();
                    let long = $('#customer_longitude').val();
                    initCustomerMap(lat, long);
                }

                $('#customerMapModal').modal('show');
            });

            function initCustomerMap(lat = null, long = null) {
                const defaultLocation = { lat: lat ? parseFloat(lat) : 13.174103138553395, lng: long ? parseFloat(long) : -59.55183389025077 };

                customerMap = new google.maps.Map(document.getElementById('customer-map'), {
                    zoom: 10,
                    center: defaultLocation,
                });

                customerMarker = new google.maps.Marker({
                    map: customerMap,
                    position: defaultLocation,
                    draggable: true
                });

                const mapInput = document.getElementById('customer-pac-input');
                customerSearchBox = new google.maps.places.SearchBox(mapInput);

                customerMap.addListener('bounds_changed', () => {
                    customerSearchBox.setBounds(customerMap.getBounds());
                });

                customerSearchBox.addListener('places_changed', () => {
                    const places = customerSearchBox.getPlaces();

                    if (places.length === 0) {
                        return;
                    }

                    const place = places[0];

                    if (!place.geometry || !place.geometry.location) {
                        window.alert("No details available for input: '" + place.name + "'");
                        return;
                    }

                    if (place.geometry.viewport) {
                        customerMap.fitBounds(place.geometry.viewport);
                    } else {
                        customerMap.setCenter(place.geometry.location);
                        customerMap.setZoom(17);
                    }

                    customerMarker.setPosition(place.geometry.location);
                    updateCustomerLocationData(place.geometry.location.lat(), place.geometry.location.lng(), place.url);
                });

                customerMap.addListener('click', (event) => {
                    customerMarker.setPosition(event.latLng);
                    updateCustomerLocationData(event.latLng.lat(), event.latLng.lng());
                });

                customerMarker.addListener('dragend', () => {
                    const position = customerMarker.getPosition();
                    updateCustomerLocationData(position.lat(), position.lng());
                });
            }

            function updateCustomerLocationData(lat, lng, url = null) {
                $('#customer_latitude').val(lat);
                $('#customer_longitude').val(lng);
                if (url) {
                    $('#customer_location_url').val(url);
                }
            }

            /* jQuery.validator.addMethod("greaterThan", function (value, element, param) {
                var startDate = $(param).val();
                if (!value || !startDate) {
                    return true;
                }

                var format = "DD-MM-YYYY HH:mm";

                var expected = moment(value, format);
                var visiting = moment(startDate, format);

                return expected.isAfter(visiting);
            }, "Expected Service date must be greater than visiting date"); */

            $('#customerForm').validate({
                ignore: [],
                rules: {
                    customer: { required: true },
                    location_id: { required: true },
                    title: { required: true },
                    description: { required: true },
                    'engineers[]': {
                        required: true
                    },
                    customer_name: { required: true },
                    customer_email: {
                        required: true,
                        email: true
                    },
                    customer_alternate_phone_number: { required: true },
                    customer_billing_name: { required: true },
                    customer_address_line_1: { required: true },
                    opening_date: { required: true },
                    visiting_date: { required: true },
                    dms_days: {
                        required: function () {
                            return $('#dms_hours').val() == 0 && $('#dms_minutes').val() == 0;
                        }
                    },
                    dms_hours: {
                        required: function () {
                            return $('#dms_days').val() == 0 && $('#dms_minutes').val() == 0;
                        }
                    },
                    dms_minutes: {
                        required: function () {
                            return $('#dms_days').val() == 0 && $('#dms_hours').val() == 0;
                        }
                    },
                },
                errorPlacement: function (error, element) {
                    if (element.attr('id') === 'customer_phone_number' || element.attr('id') === 'technicians' || element.attr('id') === 'engineers' || element.attr('id') === 'dms_days' || element.attr('id') === 'dms_hours' || element.attr('id') === 'dms_minutes' || element.attr('id') === 'location') {
                        error.insertAfter(element.parent());
                    } else {
                        error.appendTo(element.parent());
                    }
                },
                submitHandler: function (form) {
                    if (customerIti && customerIti.isValidNumber()) {
                        $('#customer_dial_code').val(customerIti.s.dialCode);
                    }
                    return true;
                }
            });

            let materialRow = `
                <tr>
                    <td>
                        1
                    </td>
                    <td class="category-container">
                        <select class="input-category" id="input-category-0" required></select>
                    </td>
                    <td class="product-container">
                        <select class="input-product" id="input-product-0" name="material[0][product]" required></select>
                    </td>
                    <td>
                        <textarea class="input-description form-control" id="input-description-0" name="material[0][description]"></textarea>
                    </td>
                    <td>
                        <input type="number" min="1" class="input-quantity form-control" id="input-quantity-0" name="material[0][quantity]" value="1" required>
                    </td>
                    <td>
                        <input type="number" min="0" class="input-price form-control" id="input-price-0" name="material[0][price]" readonly>
                    </td>
                    <td>
                        <input type="number" min="0" class="input-amount form-control" id="input-amount-0" name="material[0][amount]" readonly>
                    </td>
                    <td class="add-remove-container">
                        <button type="button" class="btn  row-add"><img src="{{ url('settings-media/dms-minus.svg') }}" alt="" /></button>
                        <button type="button" class="btn  row-remove"><img src="{{ url('settings-media/plus-sms.svg') }}" alt="" /></button>
                    </td>
                </tr>
                `;

            let serviceRow = `
                <tr>
                    <td>
                        1
                    </td>
                    <td>
                        <input type="text" class="input-title form-control" id="input-title-0" name="service[0][title]" placeholder="Service Title" required>
                    </td>
                    <td>
                        <textarea class="input-description form-control" id="input-description-0" name="service[0][description]" placeholder="Description"></textarea>
                    </td>
                    <td>
                        <input type="number" min="1" class="input-quantity form-control" id="input-quantity-0" name="service[0][quantity]" value="1" required>
                    </td>
                    <td>
                        <input type="number" min="0" class="input-price form-control" id="input-price-0" name="service[0][price]" step="0.01" required>
                    </td>
                    <td>
                        <input type="number" min="0" class="input-total-amount form-control" id="input-total-amount-0" name="service[0][total_amount]" step="0.01" readonly>
                    </td>
                    <td class="add-remove-container">
                        <button type="button" class="btn  service-row-add"><img src="{{ url('settings-media/dms-minus.svg') }}" alt="" /></button>
                        <button type="button" class="btn service-row-remove"><img src="{{ url('settings-media/plus-sms.svg') }}" alt="" /></button>
                    </td>
                </tr>
                `;

            $(document).on('click', '.row-add', function () {
                var rowCount = $('#materials-container tr').length;

                var row = $(materialRow);

                row.find('.input-category').attr('id', `input-category-${rowCount}`);
                row.find('.input-product').attr('id', `input-product-${rowCount}`).attr('name', `material[${rowCount}][product]`);
                row.find('.input-description').attr('id', `input-description-${rowCount}`).attr('name', `material[${rowCount}][description]`);
                row.find('.input-quantity').attr('id', `input-quantity-${rowCount}`).attr('name', `material[${rowCount}][quantity]`);
                row.find('.input-price').attr('id', `input-price-${rowCount}`).attr('name', `material[${rowCount}][price]`);
                row.find('.input-amount').attr('id', `input-amount-${rowCount}`).attr('name', `material[${rowCount}][amount]`);
                row.find('td:eq(0)').text(rowCount + 1);

                initializeCategories(row.find('.input-category'))
                initializeProducts(row.find('.input-product'))

                $('#materials-container').append(row);
            });

            $(document).on('click', '.service-row-add', function () {
                var rowCount = $('#services-container tr').length;

                var row = $(serviceRow);

                row.find('.input-title').attr('id', `input-title-${rowCount}`).attr('name', `service[${rowCount}][title]`);
                row.find('.input-description').attr('id', `input-description-${rowCount}`).attr('name', `service[${rowCount}][description]`);
                row.find('.input-quantity').attr('id', `input-quantity-${rowCount}`).attr('name', `service[${rowCount}][quantity]`);
                row.find('.input-price').attr('id', `input-price-${rowCount}`).attr('name', `service[${rowCount}][price]`);
                row.find('.input-total-amount').attr('id', `input-total-amount-${rowCount}`).attr('name', `service[${rowCount}][total_amount]`);
                row.find('td:eq(0)').text(rowCount + 1);

                $('#services-container').append(row);
            });

            $(document).on('change', '#materials-container .input-quantity', function () {
                let quantity = parseFloat($(this).val()) || 0;
                let price = parseFloat($(this).parent().next().find('input.input-price').val()) || 0;

                $(this).parent().next().next().find('input.input-amount').val(parseFloat(quantity * price).toFixed(2));
                calculateMaterialTotal();
            });

            $(document).on('change', '#services-container .input-quantity, #services-container .input-price', function () {
                let $row = $(this).closest('tr');
                let quantity = parseFloat($row.find('.input-quantity').val()) || 0;
                let price = parseFloat($row.find('.input-price').val()) || 0;

                $row.find('.input-total-amount').val(parseFloat(quantity * price).toFixed(2));
                calculateServiceTotal();
            });

            $(document).on('click', '.row-remove', function () {
                if ($('#materials-container tr').length > 0) {
                    let that = this;
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'You will not be able to recover this line item!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, remove it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(that).closest('tr').remove();
                            calculateMaterialTotal();
                        }
                    });
                }
            });

            $(document).on('click', '.service-row-remove', function () {
                if ($('#services-container tr').length > 0) {
                    let that = this;
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'You will not be able to recover this service!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, remove it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(that).closest('tr').remove();
                            calculateServiceTotal();
                        }
                    });
                }
            });

            let calculateMaterialTotal = () => {
                var total = 0;
                $('.input-amount').each(function () {
                    var val = parseFloat($(this).val()) || 0;
                    total += val;
                });

                $('#material-total').html(convertIntoAmount.format(total.toFixed(2)));
                calculateGrandTotal();
            }

            let calculateServiceTotal = () => {
                var total = 0;
                $('.input-total-amount').each(function () {
                    var val = parseFloat($(this).val()) || 0;
                    total += val;
                });

                $('#service-total').html(convertIntoAmount.format(total.toFixed(2)));
                calculateGrandTotal();
            }

            let calculateServicesTotal = () => {
                let serviceTotal = 0;
                $('.input-total-amount').each(function () {
                    serviceTotal += parseFloat($(this).val()) || 0;
                });

                let servicesDiscountType = $('#services_discount_type').val();
                let servicesDiscountAmount = parseFloat($('#services_discount_amount').val()) || 0;
                let servicesDiscountValue = 0;

                if (servicesDiscountType === 'PERCENT') {
                    servicesDiscountValue = (serviceTotal * servicesDiscountAmount) / 100;
                } else {
                    servicesDiscountValue = servicesDiscountAmount;
                }

                let servicesVatType = $('#services_vat_type').val();
                let servicesVatAmount = parseFloat($('#services_vat_amount').val()) || 0;
                let servicesVatValue = 0;

                if (servicesVatType === 'PERCENT') {
                    servicesVatValue = ((serviceTotal - servicesDiscountValue) * servicesVatAmount) / 100;
                } else {
                    servicesVatValue = servicesVatAmount;
                }

                let servicesFinalTotal = serviceTotal - servicesDiscountValue + servicesVatValue;

                $('#services-subtotal-display').text('$' + convertIntoAmount.format(serviceTotal.toFixed(2)));
                $('#services-discount-display').text('$' + convertIntoAmount.format(servicesDiscountValue.toFixed(2)));
                $('#services-vat-display').text('$' + convertIntoAmount.format(servicesVatValue.toFixed(2)));
                $('#services-total-display').html('<strong>$' + convertIntoAmount.format(servicesFinalTotal.toFixed(2)) + '</strong>');
                $('#final-services-total-display').text('$' + convertIntoAmount.format(servicesFinalTotal.toFixed(2)));

                return servicesFinalTotal;
            }

            let calculatePartsTotal = () => {

                let materialTotal = 0;
                $('.input-amount').each(function () {
                    materialTotal += parseFloat($(this).val()) || 0;
                });


                let partsDiscountType = $('#parts_discount_type').val();
                let partsDiscountAmount = parseFloat($('#parts_discount_amount').val()) || 0;
                let partsDiscountValue = 0;

                if (partsDiscountType === 'PERCENT') {
                    partsDiscountValue = (materialTotal * partsDiscountAmount) / 100;
                } else {
                    partsDiscountValue = partsDiscountAmount;
                }


                let partsVatType = $('#parts_vat_type').val();
                let partsVatAmount = parseFloat($('#parts_vat_amount').val()) || 0;
                let partsVatValue = 0;

                if (partsVatType === 'PERCENT') {
                    partsVatValue = ((materialTotal - partsDiscountValue) * partsVatAmount) / 100;
                } else {
                    partsVatValue = partsVatAmount;
                }


                let partsFinalTotal = materialTotal - partsDiscountValue + partsVatValue;


                $('#parts-subtotal-display').text('$' + convertIntoAmount.format(materialTotal.toFixed(2)));
                $('#parts-discount-display').text('$' + convertIntoAmount.format(partsDiscountValue.toFixed(2)));
                $('#parts-vat-display').text('$' + convertIntoAmount.format(partsVatValue.toFixed(2)));
                $('#parts-total-display').html('<strong>$' + convertIntoAmount.format(partsFinalTotal.toFixed(2)) + '</strong>');
                $('#final-parts-total-display').text('$' + convertIntoAmount.format(partsFinalTotal.toFixed(2)));

                return partsFinalTotal;
            }

            let calculateGrandTotal = () => {
                let servicesTotal = calculateServicesTotal();
                let partsTotal = calculatePartsTotal();
                let grandTotal = servicesTotal + partsTotal;


                $('#grand-total-display').html('<strong>$' + convertIntoAmount.format(grandTotal.toFixed(2)) + '</strong>');
                $('#grand_total').val(grandTotal.toFixed(2));
            }

            let initializeCategories = (element = null) => {
                if (element) {
                    $(element).select2({
                        placeholder: 'Select a category',
                        width: '100%',
                        ajax: {
                            url: "{{ route('category-list') }}",
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
                        },
                        templateResult: function (data) {
                            if (data.loading) {
                                return data.text;
                            }
                            var $result = $('<span></span>');
                            $result.text(data.text);
                            return $result;
                        }
                    });
                }
            }

            let initializeProducts = (element = null) => {
                if (element) {
                    $(element).select2({
                        placeholder: 'Select a product',
                        width: '100%',
                        ajax: {
                            url: "{{ route('product-list') }}",
                            type: "POST",
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    searchQuery: params.term,
                                    page: params.page || 1,
                                    _token: "{{ csrf_token() }}",
                                    category: function () {
                                        return $(element).parent().prev().find('.input-category option:selected').val()
                                    }
                                };
                            },
                            processResults: function (data, params) {
                                params.page = params.page || 1;
                                return {
                                    results: $.map(data.items, function (item) {
                                        return {
                                            id: item.id,
                                            text: item.text,
                                            price: item.price
                                        };
                                    }),
                                    pagination: {
                                        more: data.pagination.more
                                    }
                                };
                            },
                            cache: true
                        },
                        templateResult: function (data) {
                            if (data.loading) {
                                return data.text;
                            }
                            var $result = $('<span></span>');
                            $result.attr('data-price', data.price);
                            $result.text(data.text);
                            return $result;
                        },
                        templateSelection: function (container) {
                            $(container.element).attr("data-price", container.price);

                            return container.text;
                        }
                    }).on('change', function () {
                        var selectedElement = $(this);
                        var selectedProduct = $(this).select2('data')[0];

                        if (selectedElement) {
                            selectedProductPrice = parseFloat(selectedProduct?.price) || 0;
                            let qty = parseFloat(selectedElement.parent().parent().find('input.input-quantity')) || 1;

                            selectedElement.parent().parent().find('input.input-price').val(parseFloat(selectedProductPrice * qty).toFixed(2));
                            selectedElement.parent().parent().find('input.input-amount').val(parseFloat(selectedProductPrice * qty).toFixed(2));
                        }

                        calculateMaterialTotal();
                    });
                }
            }

            $(document).on('input', '.input-quantity', calculateMaterialTotal);
            $(document).on('click', '.row-add, .row-remove', calculateMaterialTotal);
            $(document).on('input', '.services-container .input-quantity, .services-container .input-price', calculateServiceTotal);
            $(document).on('click', '.service-row-add, .service-row-remove', calculateServiceTotal);
            $(document).on('change', '#services_discount_type, #services_discount_amount, #services_vat_type, #services_vat_amount, #parts_discount_type, #parts_discount_amount, #parts_vat_type, #parts_vat_amount', calculateGrandTotal);

            let input = document.querySelector('#phone_number');
            const errorMap = ["Phone number is invalid.", "Invalid country code", "Too short", "Too long"];

            let iti = window.intlTelInput(input, {
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

            let map, marker, searchBox;

            $('#mapButton').on('click', function () {
                $('#mapContainer').toggle();
                if ($('#mapContainer').is(':visible') && !map) {
                    initMap();
                }
            });

            function initMap() {
                const defaultLocation = { lat: 13.174103138553395, lng: -59.55183389025077 };

                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 10,
                    center: defaultLocation,
                });

                marker = new google.maps.Marker({
                    map: map,
                    draggable: true,
                    position: defaultLocation
                });

                const mapContainer = document.getElementById('mapContainer');

                let mapInput = document.getElementById('pac-input');
                mapInput.style.cssText = `
                        width: 100%;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                    `;

                searchBox = new google.maps.places.SearchBox(mapInput);

                map.addListener('bounds_changed', () => {
                    searchBox.setBounds(map.getBounds());
                });

                searchBox.addListener('places_changed', () => {
                    const places = searchBox.getPlaces();

                    if (places.length === 0) {
                        return;
                    }

                    const place = places[0];

                    if (!place.geometry || !place.geometry.location) {
                        window.alert("No details available for input: '" + place.name + "'");
                        return;
                    }

                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);
                    }

                    marker.setPosition(place.geometry.location);
                    updateLocationData(place.geometry.location.lat(), place.geometry.location.lng(), place.url);
                });

                map.addListener('click', (event) => {
                    marker.setPosition(event.latLng);
                    updateLocationData(event.latLng.lat(), event.latLng.lng());
                });

                marker.addListener('dragend', () => {
                    const position = marker.getPosition();
                    updateLocationData(position.lat(), position.lng());
                });
            }

            function updateLocationData(lat, lng, url = null) {
                $('#latitude').val(lat);
                $('#longitude').val(lng);
                if (url) {
                    $('#location_url').val(url);
                }
            }

            $(document).on('shown.bs.modal', '#newCustomerAdditionModal', function (e) {
                if (e.namespace == 'bs.modal') {
                    let currentTarget = $(e.currentTarget);
                }
            });

            // ========== MODAL LOCATION MANAGEMENT ==========
            let modalTempLocations = [];
            let modalLocationIti;
            let modalMap, modalMarker, modalAutocomplete;
            let modalCountryNames = {};
            let modalStateNames = {};
            let modalCityNames = {};

            function initializeModalLocationSelect2() {
                $('#modal_location_country').select2({
                    allowClear: true,
                    placeholder: 'Select country',
                    width: '100%',
                    dropdownParent: $('#modalLocationModal'),
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
                                    modalCountryNames[item.id] = item.text;
                                    return { id: item.id, text: item.text };
                                }),
                                pagination: { more: data.pagination.more }
                            };
                        },
                        cache: true
                    }
                });

                $('#modal_location_state').select2({
                    allowClear: true,
                    placeholder: 'Select Parish',
                    width: '100%',
                    dropdownParent: $('#modalLocationModal'),
                    ajax: {
                        url: "{{ route('state-list') }}",
                        type: "POST",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                searchQuery: params.term,
                                page: params.page || 1,
                                country_id: $('#modal_location_country').val(),
                                _token: "{{ csrf_token() }}"
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: $.map(data.items, function (item) {
                                    modalStateNames[item.id] = item.text;
                                    return { id: item.id, text: item.text };
                                }),
                                pagination: { more: data.pagination.more }
                            };
                        },
                        cache: true
                    }
                });

                $('#modal_location_city').select2({
                    allowClear: true,
                    placeholder: 'Select city',
                    width: '100%',
                    dropdownParent: $('#modalLocationModal'),
                    ajax: {
                        url: "{{ route('city-list') }}",
                        type: "POST",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                searchQuery: params.term,
                                page: params.page || 1,
                                state_id: $('#modal_location_state').val(),
                                _token: "{{ csrf_token() }}"
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: $.map(data.items, function (item) {
                                    modalCityNames[item.id] = item.text;
                                    return { id: item.id, text: item.text };
                                }),
                                pagination: { more: data.pagination.more }
                            };
                        },
                        cache: true
                    }
                });
            }

            initializeModalLocationSelect2();

            $('#modal_location_country').on('change', function() {
                const selectedCountryId = $(this).val();
                const stateLabel = $('label[for="modal_location_state"]');
                const cityField = $('#modal_location_city').closest('.mb-3');
                
                if (selectedCountryId == '20') {

                    stateLabel.html('Parish');
                    cityField.hide();

                    $('#modal_location_city').val(null).trigger('change');
                } else {

                    stateLabel.html('State');
                    cityField.show();
                }
                
                $('#modal_location_state').val(null).trigger('change');
            });

            function renderModalTempLocations() {
                const tbody = $('#modalLocationsTable tbody');
                tbody.empty();

                if (modalTempLocations.length === 0) {
                    tbody.append('<tr><td colspan="7" class="text-center">No locations added yet</td></tr>');
                    return;
                }

                modalTempLocations.forEach(function (location, index) {
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
                                    <button type="button" class="btn btn-sm btn-primary edit-modal-temp-location" data-index="${index}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-modal-temp-location" data-index="${index}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    tbody.append(row);
                });

                $('#modal_locations_data').val(JSON.stringify(modalTempLocations));
            }

            $('#modalAddLocationBtn').on('click', function () {
                $('#modalLocationModalLabel').text('Add Location');
                $('#modalLocationForm')[0].reset();
                $('#modal_location_index').val('');
                $('#modal_location_country').val(null).trigger('change');
                $('#modal_location_state').val(null).trigger('change');
                $('#modal_location_city').val(null).trigger('change');

                $('label[for="modal_location_state"]').html('State');
                $('#modal_location_city').closest('.mb-3').show();

                if (modalLocationIti) {
                    modalLocationIti.setNumber('');
                }

                $('#modalLocationModal').modal('show');
            });

            $('#modalLocationModal').on('shown.bs.modal', function () {
                if (!modalLocationIti) {
                    const modalLocationInput = document.querySelector('#modal_location_phone_number');
                    modalLocationIti = window.intlTelInput(modalLocationInput, {
                        initialCountry: "{{  Helper::$defaulDialCode  }}",
                        separateDialCode: true,
                        nationalMode: false,
                        preferredCountries: @json(\App\Models\Country::select('iso2')->pluck('iso2')->toArray()),
                        utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
                    });

                    modalLocationInput.addEventListener("countrychange", function () {

                        const dialCode = modalLocationIti.getSelectedCountryData().dialCode;
                        $('#modal_location_dial_code').val(dialCode);
                    });
                    modalLocationInput.addEventListener('keyup', () => {

                        const dialCode = modalLocationIti.getSelectedCountryData().dialCode;
                        $('#modal_location_dial_code').val(dialCode);
                    });
                }
            });

            $(document).on('click', '.edit-modal-temp-location', function () {
                const index = $(this).data('index');
                const location = modalTempLocations[index];

                $('#modalLocationModalLabel').text('Edit Location');
                $('#modal_location_index').val(index);
                $('#modal_location_name').val(location.name);
                $('#modal_location_contact_person').val(location.contact_person);
                $('#modal_location_email').val(location.email);
                $('#modal_location_status').val(location.status);
                $('#modal_location_address_line_1').val(location.address_line_1);
                $('#modal_location_address_line_2').val(location.address_line_2);
                $('#modal_location_latitude').val(location.latitude);
                $('#modal_location_longitude').val(location.longitude);
                $('#modal_location_location_url').val(location.location_url);

                if (modalLocationIti) {

                    if (location.dial_code) {

                        const countryData = modalLocationIti.getSelectedCountryData();

                        const fullNumber = location.dial_code && location.phone_number ? '+' + location.dial_code + location.phone_number : (location.phone_number || '');
                        modalLocationIti.setNumber(fullNumber);
                        $('#modal_location_dial_code').val(location.dial_code);
                    } else if (location.phone_number) {
                        modalLocationIti.setNumber(location.phone_number);

                        const dialCode = modalLocationIti.getSelectedCountryData().dialCode;
                        $('#modal_location_dial_code').val(dialCode);
                    } else {
                        modalLocationIti.setNumber('');
                    }
                }

                if (location.country) {
                    $('#modal_location_country').append(new Option(location.country_name, location.country, true, true));
                    
                    if (location.country == '20') {
                        $('label[for="modal_location_state"]').html('Parish');
                        $('#modal_location_city').closest('.mb-3').hide();
                    } else {
                        $('label[for="modal_location_state"]').html('State');
                        $('#modal_location_city').closest('.mb-3').show();
                    }
                }
                if (location.state) {
                    $('#modal_location_state').append(new Option(location.state_name, location.state, true, true));
                }
                if (location.city) {
                    $('#modal_location_city').append(new Option(location.city_name, location.city, true, true));
                }

                $('#modalLocationModal').modal('show');
            });

            $(document).on('click', '.delete-modal-temp-location', function () {
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
                        modalTempLocations.splice(index, 1);
                        renderModalTempLocations();
                        Swal.fire('Deleted!', 'Location has been removed.', 'success');
                    }
                });
            });

            $('#modalLocationForm').on('submit', function (e) {
                e.preventDefault();

                const index = $('#modal_location_index').val();

                if (modalLocationIti) {
                    const dialCode = modalLocationIti.getSelectedCountryData().dialCode;
                    $('#modal_location_dial_code').val(dialCode);
                }

                const locationData = {
                    name: $('#modal_location_name').val(),
                    contact_person: $('#modal_location_contact_person').val(),
                    email: $('#modal_location_email').val(),
                    dial_code: $('#modal_location_dial_code').val(),
                    phone_number: $('#modal_location_phone_number').val(),
                    address_line_1: $('#modal_location_address_line_1').val(),
                    address_line_2: $('#modal_location_address_line_2').val(),
                    country: $('#modal_location_country').val(),
                    country_name: modalCountryNames[$('#modal_location_country').val()] || $('#modal_location_country option:selected').text(),
                    state: $('#modal_location_state').val(),
                    state_name: modalStateNames[$('#modal_location_state').val()] || $('#modal_location_state option:selected').text(),
                    city: $('#modal_location_city').val(),
                    city_name: modalCityNames[$('#modal_location_city').val()] || $('#modal_location_city option:selected').text(),
                    status: $('#modal_location_status').val(),
                    latitude: $('#modal_location_latitude').val(),
                    longitude: $('#modal_location_longitude').val(),
                    location_url: $('#modal_location_location_url').val()
                };

                if (index !== '') {
                    modalTempLocations[parseInt(index)] = locationData;
                } else {
                    modalTempLocations.push(locationData);
                }

                renderModalTempLocations();
                $('#modalLocationModal').modal('hide');
                Swal.fire('Success', 'Location saved successfully!', 'success');
            });

            $('#modalMapButton').click(function () {
                $('#modalMapContainer').toggle();
                if ($('#modalMapContainer').is(':visible')) {
                    initModalMap();
                }
            });

            function initModalMap() {
                if (modalMap) return;

                let defaultLocation;
                if ($('#modal_location_latitude').val() && $('#modal_location_longitude').val()) {
                    defaultLocation = {
                        lat: parseFloat($('#modal_location_latitude').val()),
                        lng: parseFloat($('#modal_location_longitude').val())
                    };
                } else {
                    defaultLocation = { lat: 13.174103138553395, lng: -59.55183389025077 };
                }

                modalMap = new google.maps.Map(document.getElementById('modal-map'), {
                    zoom: 15,
                    center: defaultLocation,
                });

                modalMarker = new google.maps.Marker({
                    position: defaultLocation,
                    map: modalMap,
                    draggable: true
                });

                modalAutocomplete = new google.maps.places.Autocomplete(
                    document.getElementById('modal-pac-input'),
                    { types: ['geocode'] }
                );

                modalAutocomplete.bindTo('bounds', modalMap);

                modalAutocomplete.addListener('place_changed', function () {
                    const place = modalAutocomplete.getPlace();
                    if (!place.geometry) return;

                    if (place.geometry.viewport) {
                        modalMap.fitBounds(place.geometry.viewport);
                    } else {
                        modalMap.setCenter(place.geometry.location);
                        modalMap.setZoom(17);
                    }

                    modalMarker.setPosition(place.geometry.location);
                    updateModalLocationFields(place.geometry.location.lat(), place.geometry.location.lng(), place.formatted_address);
                });

                google.maps.event.addListener(modalMarker, 'dragend', function () {
                    const position = modalMarker.getPosition();
                    const geocoder = new google.maps.Geocoder();

                    geocoder.geocode({ location: position }, function (results, status) {
                        if (status === 'OK' && results[0]) {
                            updateModalLocationFields(position.lat(), position.lng(), results[0].formatted_address);
                        } else {
                            updateModalLocationFields(position.lat(), position.lng(), '');
                        }
                    });
                });

                google.maps.event.addListener(modalMap, 'click', function (event) {
                    modalMarker.setPosition(event.latLng);
                    const geocoder = new google.maps.Geocoder();

                    geocoder.geocode({ location: event.latLng }, function (results, status) {
                        if (status === 'OK' && results[0]) {
                            updateModalLocationFields(event.latLng.lat(), event.latLng.lng(), results[0].formatted_address);
                        } else {
                            updateModalLocationFields(event.latLng.lat(), event.latLng.lng(), '');
                        }
                    });
                });
            }

            function updateModalLocationFields(lat, lng, address) {
                $('#modal_location_latitude').val(lat);
                $('#modal_location_longitude').val(lng);
                $('#modal_location_location_url').val(address);
            }

            $('#modalLocationModal').on('hidden.bs.modal', function () {
                modalMap = null;
                $('#modalMapContainer').hide();
            });

            $('#newCustomerAdditionModal').on('hidden.bs.modal', function () {
                modalTempLocations = [];
                renderModalTempLocations();
            });
            // ========== END MODAL LOCATION MANAGEMENT ==========


            $('#technicians').select2({
                placeholder: 'Select technicians',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('user-list') }}",
                    type: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            _token: "{{ csrf_token() }}",
                            searchQuery: params.term,
                            page: params.page || 1,
                            expertises: function () {
                                return $('#expertise').val();
                            },
                            customer_id: $('#customer').val(),
                            job_id: "",
                            roles: ['technician'],
                            visiting_date: function () {
                                return $('#visiting_date').val();
                            },
                            dms_days: function () {
                                return $('#dms_days').val();
                            },
                            dms_hours: function () {
                                return $('#dms_hours').val();
                            },
                            dms_minutes: function () {
                                return $('#dms_minutes').val();
                            },
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data.items, function (item) {
                                return { id: item.id, text: item.text };
                            }),
                            pagination: { more: data.pagination.more }
                        };
                    },
                    cache: true
                },
                templateResult: function (data) {
                    if (data.loading) return data.text;
                    var $result = $('<span></span>');
                    $result.text(data.text);
                    return $result;
                }
            }).on('change', function () {

            });
            $('#engineers').select2({
                placeholder: 'Select engineers',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('user-list') }}",
                    type: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchQuery: params.term,
                            page: params.page || 1,
                            _token: "{{ csrf_token() }}",
                            expertises: function () {
                                return $('#expertise').val();
                            },
                            roles: ['engineer']
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data.items, function (item) {
                                return { id: item.id, text: item.text };
                            }),
                            pagination: { more: data.pagination.more }
                        };
                    },
                    cache: true
                },
                templateResult: function (data) {
                    if (data.loading) return data.text;
                    var $result = $('<span></span>');
                    $result.text(data.text);
                    return $result;
                }
            }).on('change', function () {

            });

            $('#country').select2({
                placeholder: 'Select Country',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#newCustomerAdditionModal'),
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
                                return { id: item.id, text: item.text };
                            }),
                            pagination: { more: data.pagination.more }
                        };
                    },
                    cache: true
                },
                templateResult: function (data) {
                    if (data.loading) return data.text;
                    var $result = $('<span></span>');
                    $result.text(data.text);
                    return $result;
                }
            }).on('change', function () {
                $('#state').val(null).trigger('change');
                $('#city').val(null).trigger('change');
            });

            $('#state').select2({
                placeholder: 'Select State',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#newCustomerAdditionModal'),
                ajax: {
                    url: "{{ route('state-list') }}",
                    type: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchQuery: params.term,
                            page: params.page || 1,
                            _token: "{{ csrf_token() }}",
                            country_id: function () { return $('#country').val(); }
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data.items, function (item) {
                                return { id: item.id, text: item.text };
                            }),
                            pagination: { more: data.pagination.more }
                        };
                    },
                    cache: true
                },
                templateResult: function (data) {
                    if (data.loading) return data.text;
                    var $result = $('<span></span>');
                    $result.text(data.text);
                    return $result;
                }
            }).on('change', function () {
                $('#city').val(null).trigger('change');
            });

            $('#city').select2({
                placeholder: 'Select City',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#newCustomerAdditionModal'),
                ajax: {
                    url: "{{ route('city-list') }}",
                    type: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchQuery: params.term,
                            page: params.page || 1,
                            _token: "{{ csrf_token() }}",
                            state_id: function () { return $('#state').val(); }
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data.items, function (item) {
                                return { id: item.id, text: item.text };
                            }),
                            pagination: { more: data.pagination.more }
                        };
                    },
                    cache: true
                },
                templateResult: function (data) {
                    if (data.loading) return data.text;
                    var $result = $('<span></span>');
                    $result.text(data.text);
                    return $result;
                }
            });

            $('#location').select2({
                allowClear: true,
                placeholder: 'Select location',
                width: '100%'
            });

            function loadCustomerLocations(customerId) {
                if (!customerId || customerId === 'ADD_NEW_USER') {
                    $('#location_container').hide();
                    $('#location').empty().append('<option value="">Select Location</option>');
                    return;
                }

                $('#location_container').show();

                $.ajax({
                    url: `{{ url('/customers/${customerId}/locations') }}`,
                    type: 'GET',
                    success: function (response) {
                        $('#location').empty().append('<option value="">Select Location</option>');

                        if (response.success && response.locations && response.locations.length > 0) {
                            response.locations.forEach(function (location) {
                                const locationText = location.name + (location.address_line_1 ? ' - ' + location.address_line_1 : '');
                                const option = new Option(locationText, location.id, false, false);
                                $(option).data('location', location);
                                $('#location').append(option);
                            });
                        } else {
                            $('#location').append('<option value="" disabled>No locations found</option>');
                        }

                        $('#location').trigger('change');
                    },
                    error: function () {
                        console.error('Failed to load locations');
                        $('#location_container').hide();
                    }
                });
            }

            $('#location').on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const locationData = selectedOption.data('location');

                if (locationData) {
                    if (locationData.contact_person) {
                        $('#customer_name').val(locationData.name);
                        $('#customer_billing_name').val(locationData.contact_person);
                    }
                    if (locationData.email) {
                        $('#customer_email').val(locationData.email);
                    }

                    if (locationData.phone_number && locationData.dial_code) {
                        const fullNumber = `+${locationData.dial_code}${locationData.phone_number}`;
                        if (customerIti) {
                            customerIti.setNumber(fullNumber);
                            $('#customer_dial_code').val(locationData.dial_code);
                        }
                    }

                    if (locationData.address_line_1) {
                        $('#customer_address_line_1').val(locationData.address_line_1);
                    }
                    if (locationData.address_line_2) {
                        $('#customer_address_line_2').val(locationData.address_line_2);
                    }

                    if (locationData.latitude) {
                        $('#customer_latitude').val(locationData.latitude);
                    }
                    if (locationData.longitude) {
                        $('#customer_longitude').val(locationData.longitude);
                    }
                    if (locationData.location_url) {
                        $('#customer_location_url').val(locationData.location_url);
                    }

                    if (locationData.latitude && locationData.longitude && customerMap) {
                        const locationCoords = {
                            lat: parseFloat(locationData.latitude),
                            lng: parseFloat(locationData.longitude)
                        };
                        customerMap.setCenter(locationCoords);
                        customerMap.setZoom(15);
                        customerMarker.setPosition(locationCoords);
                    }
                }
            });

            $('#customer').select2({
                allowClear: true,
                placeholder: 'Select customer',
                width: '100%',
                ajax: {
                    url: "{{ route('user-list') }}",
                    type: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchQuery: params.term,
                            page: params.page || 1,
                            _token: "{{ csrf_token() }}",
                            addNewOption: 1,
                            roles: ['customer'],
                            includeUserData: true
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        return {
                            results: $.map(data.items, function (item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    user: item.user || null,
                                    alternate_dial_code_iso: item.alternate_dial_code_iso || null
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                templateResult: function (data) {
                    if (data.loading) {
                        return data.text;
                    }

                    var $result = $('<span></span>');
                    $result.text(data.text);
                    return $result;
                }
            }).on('change', function () {
                if ($('option:selected', this).val() == 'ADD_NEW_USER') {
                    $('#newCustomerAdditionModal').modal('show');
                }

                const selectedOption = $(this).find('option:selected');
                const userData = $(this).select2('data')[0]?.user;
                const alternaticeIso = $(this).select2('data')[0]?.alternate_dial_code_iso;

                const customerId = $(this).val();
                loadCustomerLocations(customerId);

                if (userData) {
                    $('#customer_name').val(userData.name || '');
                    $('#customer_email').val(userData.email || '');

                    if (alternaticeIso && userData.alternate_phone_number) {
                        customerIti.setCountry(alternaticeIso);
                        customerInput.value = userData.alternate_phone_number;
                        $('#customer_dial_code').val(userData.alternate_dial_code);
                    }

                    let fullAddress = userData.address_line_1 || '';
                    if (userData.address_line_2) {
                        fullAddress += (fullAddress ? ', ' : '') + userData.address_line_2;
                    }

                    $('#customer_address_line_1').val(userData.address_line_1 || '');
                    $('#customer_address_line_2').val(userData.address_line_2 || '');

                    $('#customer_location_url').val(userData.location_url || '');
                    $('#customer_latitude').val(userData.latitude || '');
                    $('#customer_longitude').val(userData.longitude || '');

                    if (userData.latitude && userData.longitude) {
                        if (customerMap) {
                            const userLocation = {
                                lat: parseFloat(userData.latitude),
                                lng: parseFloat(userData.longitude)
                            };
                            customerMap.setCenter(userLocation);
                            customerMap.setZoom(15);
                            customerMarker.setPosition(userLocation);
                        }
                    }
                } else {
                    $('#customer_name').val('');
                    $('#customer_email').val('');
                    customerInput.value = '';
                    $('#customer_dial_code').val('');
                    $('#customer_address_line_1').val('');
                    $('#customer_address_line_2').val('');
                    $('#customer_location_url').val('');
                    $('#customer_latitude').val('');
                    $('#customer_longitude').val('');
                }
            });

            $('#confirmCustomerLocation').on('click', function () {
                $('#customerMapModal').modal('hide');
            });

            $('#requires_deposit').on('change', function () {
                if ($(this).val() == '1') {
                    $('.deposit-fields').show();
                    $('#deposit_type, #deposit_amount').prop('required', true);
                } else {
                    $('.deposit-fields').hide();
                    $('#deposit_type, #deposit_amount').prop('required', false);
                    $('#deposit_amount').val('');
                }
            });

            $('#deposit_type').on('change', function () {
                if ($(this).val() == 'PERCENT') {
                    $('#deposit_amount').attr('max', '100');
                    $('#deposit_amount').attr('placeholder', 'Enter percentage (0-100)');
                } else {
                    $('#deposit_amount').removeAttr('max');
                    $('#deposit_amount').attr('placeholder', 'Enter amount');
                }
            });

            $.validator.addMethod("imageFiles", function (value, element) {

                if (value === "") return true;

                var files = element.files;
                var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                var maxSize = 20480 * 1024;

                for (var i = 0; i < files.length; i++) {
                    var file = files[i];

                    if ($.inArray(file.type.toLowerCase(), allowedTypes) === -1) {
                        return false;
                    }

                    if (file.size > maxSize) {
                        return false;
                    }
                }

                return true;
            }, "Please upload valid image files (jpeg, png, jpg, gif, webp) max 20MB each.");

            $('#engineerForm').validate({
                rules: {
                    name: { required: true },
                    email: { required: true, email: true },
                    alternate_dial_code: { required: true },
                    status: { required: true },
                    password: { required: true },
                    "dms_attachment[]": {
                        imageFiles: true
                    }
                },
                errorPlacement: function (error, element) {
                    if (element.attr('id') === 'phone_number' || element.attr('id') === 'country' || element.attr('id') === 'state' || element.attr('id') === 'city') {
                        error.insertAfter(element.parent());
                    } else {
                        error.appendTo(element.parent());
                    }
                },
                submitHandler: function (form) {
                    if (iti) {
                        $('#dial_code').val(iti.s.dialCode);
                    }

                    $('#modal_locations_data').val(JSON.stringify(modalTempLocations));

                    let formData = new FormData(form);

                    formData.delete('modal_locations');
                    formData.append('locations', JSON.stringify(modalTempLocations));

                    $.ajax({
                        url: "{{ route('customers.store') }}?response_type=json",
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $('body').find('.LoaderSec').removeClass('d-none');
                        },
                        success: function (response) {
                            if (response.status) {
                                Swal.fire('Success', 'Customer added successfully!', 'success');
                                $('#newCustomerAdditionModal').modal('hide');

                                if (response.user.id) {
                                    $('#customer').html(`<option value="${response.user.id}" selected> ${response.user.name} </option>`).val(response.user.id).trigger('change');
                                    $('#customer_name').val(response.user.name);
                                    $('#customer_email').val(response.user.email);
                                    $('#customer_billing_name').val(response.user.name);

                                    loadCustomerLocations(response.user.id);

                                    if (response.user.locations && response.user.locations.length > 0) {
                                        const firstLocation = response.user.locations[0];
                                        $('#customer_address_line_1').val(firstLocation.address_line_1 || '');
                                        $('#customer_address_line_2').val(firstLocation.address_line_2 || '');
                                        $('#customer_location_url').val(firstLocation.location_url || '');
                                        $('#customer_latitude').val(parseFloat(firstLocation.latitude) || 13.174103138553395);
                                        $('#customer_longitude').val(parseFloat(firstLocation.longitude) || -59.55183389025077);
                                    } else {
                                        $('#customer_address_line_1').val('');
                                        $('#customer_address_line_2').val('');
                                        $('#customer_location_url').val('');
                                        $('#customer_latitude').val(13.174103138553395);
                                        $('#customer_longitude').val(-59.55183389025077);
                                    }

                                    let alternaticeIso = response.alternate_dial_code_iso || 'in';

                                    if (alternaticeIso && response.user.alternate_phone_number) {
                                        customerIti.setCountry(alternaticeIso);
                                        customerInput.value = response.user.alternate_phone_number;
                                        $('#customer_dial_code').val(response.user.alternate_dial_code);
                                    }
                                }

                            } else {
                                Swal.fire('Error', 'Something went wrong!', 'error');
                            }
                        },
                        error: function (xhr, status, error) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                $('.invalid-feedback').empty();
                                $('.form-control, .form-select').removeClass('is-invalid');

                                $.each(errors, function (field, messages) {
                                    const input = $(`[name="${field}"]`);
                                    const feedback = input.siblings('.invalid-feedback');

                                    input.addClass('is-invalid');
                                    feedback.html(messages.join('<br>'));
                                });
                            } else {
                                Swal.fire('Error', 'Something went wrong', 'error');
                            }
                        },
                        complete: function () {
                            $('body').find('.LoaderSec').addClass('d-none');
                        }
                    });
                }
            });

            $(document).on('change', '#dms_job_status', function () {
                $('#dms_job_status_hidden').val($(this).val());
            });

            let requisitionItemRow = `
                    <tr>
                        <td> 0 </td>
                        <td class="requisition-type-container">
                            <select class="requisition-type form-select" id="requisition-type-0" name="requisition[0][type]" required>
                                <option value="INVENTORY"> Inventory </option>
                                <option value="VENDOR"> Vendor </option>
                            </select>
                        </td>
                        <td class="requisition-product-container">
                            <select class="requisition-product form-select" id="requisition-product-0" name="requisition[0][product]" required>
                            </select>
                        </td>
                        <td class="requisition-description-container">
                            <textarea class="requisition-description form-control" id="requisition-description-0" name="requisition[0][description]" placeholder="Description"></textarea>
                        </td>
                        <td class="requisition-quantity-container">
                            <input type="number" min="1" class="requisition-quantity form-control" id="requisition-quantity-0" name="requisition[0][quantity]" value="1" required>
                        </td>
                        <td class="requisition-amount-container">
                            <input type="number" min="0" step="0.01" class="requisition-amount form-control" id="requisition-amount-0" name="requisition[0][amount]" required>
                        </td>
                        <td class="requisition-total-container">
                            <input type="number" min="0" class="requisition-total form-control" id="requisition-total-0" name="requisition[0][total]" readonly>
                        </td>
                        <td class="requisition-status-container">
                            <select class="requisition-status form-select" id="requisition-status-0" name="requisition[0][status]" required>
                                <option value="PENDING" selected>PENDING</option>
                                <option value="APPROVED">APPROVED</option>
                                <option value="REJECTED">REJECTED</option>
                            </select>
                            <div class="rejection-note-container mt-2" style="display: none;">
                                <textarea class="form-control rejection-note" id="requisition-rejection-note-0" name="requisition[0][rejection_note]" placeholder="Rejection reason..." rows="2"></textarea>
                            </div>
                        </td>
                        <td>
                            <button class="btn requisition-remove-row" type="button"><img src="{{ url('settings-media/plus-sms.svg') }}" alt="" /></button>
                        </td>
                    </tr>
                `;

            let calculateRequisitionTotal = () => {
                var total = 0;

                $('.requisition-total').each(function () {
                    var val = parseFloat($(this).val()) || 0;
                    total += val;
                });

                $('#requisition-grand-total-column').html(convertIntoAmount.format(total.toFixed(2)));
            }

            let initializeRequisitionProducts = (element = null) => {
                if (element) {
                    $(element).select2({
                        placeholder: 'Select a product',
                        width: '100%',
                        ajax: {
                            url: "{{ route('product-list') }}",
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
                                            text: item.text,
                                            price: item.price
                                        };
                                    }),
                                    pagination: {
                                        more: data.pagination.more
                                    }
                                };
                            },
                            cache: true
                        },
                        templateResult: function (data) {
                            if (data.loading) {
                                return data.text;
                            }
                            var $result = $('<span></span>');
                            $result.attr('data-price', data.price);
                            $result.text(data.text);
                            return $result;
                        },
                        templateSelection: function (container) {
                            $(container.element).attr("data-price", container.price);

                            return container.text;
                        }
                    }).on('change', function () {
                        var selectedElement = $(this);
                        var selectedProduct = $(this).select2('data')[0];

                        if (selectedElement) {
                            selectedProductPrice = parseFloat(selectedProduct?.price) || 0;
                            let qty = parseFloat(selectedElement.parent().parent().find('input.requisition-quantity').val()) || 1;

                            selectedElement.parent().parent().find('input.requisition-amount').val(parseFloat(selectedProductPrice).toFixed(2));
                            selectedElement.parent().parent().find('input.requisition-total').val(parseFloat(selectedProductPrice * qty).toFixed(2));
                        }

                        calculateRequisitionTotal();
                    });
                }
            }

            let initializeRequisitionType = (element = null) => {
                if (element) {
                    $(element).select2({
                        placeholder: 'Select type',
                        width: '100%',
                    }).on('change', function () {
                        let nextId = parseInt($(this).attr('id').replace('requisition-type-', '')) || 0;

                        if (nextId >= 0) {
                            if ($('option:selected', this).val() == 'INVENTORY') {
                                $(this).parent().next().html(`
                                        <select class="requisition-product form-select" id="requisition-product-${nextId}" name="requisition[${nextId}][product]" required>
                                        </select>
                                    `);

                                initializeRequisitionProducts(`#requisition-product-${nextId}`)
                            } else {
                                $(this).parent().next().html(`
                                        <select class="requisition-vendor form-select" id="requisition-vendor-${nextId}" name="requisition[${nextId}][vendor]" required>
                                        </select> <br/> <br/>

                                        <input class="requisition-product form-control" placeholder="Product Name" id="requisition-product-${nextId}" name="requisition[${nextId}][product]" required>
                                    `);

                                initializeRequisitionVendor(`#requisition-vendor-${nextId}`)
                            }
                        }

                    });
                }
            }

            let initializeRequisitionVendor = (element = null) => {
                if (element) {
                    $(element).select2({
                        placeholder: 'Select vendor',
                        allowClear: true,
                        width: '100%',
                        ajax: {
                            url: "{{ route('user-list') }}",
                            type: "POST",
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    searchQuery: params.term,
                                    page: params.page || 1,
                                    _token: "{{ csrf_token() }}",
                                    expertises: '',
                                    roles: ['vendor']
                                };
                            },
                            processResults: function (data, params) {
                                params.page = params.page || 1;
                                return {
                                    results: $.map(data.items, function (item) {
                                        return { id: item.id, text: item.text };
                                    }),
                                    pagination: { more: data.pagination.more }
                                };
                            },
                            cache: true
                        },
                        templateResult: function (data) {
                            if (data.loading) return data.text;
                            var $result = $('<span></span>');
                            $result.text(data.text);
                            return $result;
                        }
                    }).on('change', function () {
                    });
                }
            }

            $(document).on('input', '.requisition-amount, .requisition-quantity', calculateRequisitionTotal);
            $(document).on('click', '.requisition-add-row, .requisition-remove-row', calculateRequisitionTotal);

            $('.requisition-product').each(function () {
                if ($(this).is('select')) {
                    initializeRequisitionProducts($(this));
                }
            });

            $('.requisition-type').each(function () {
                initializeRequisitionType($(this));
            });

            $('.requisition-vendor').each(function () {
                initializeRequisitionVendor($(this));
            });

            $(document).on('click', '.requisition-add-row, .requisition-row-add', function () {
                var rowCount = $('#requisition-items tr').length;

                var row = $(requisitionItemRow);

                row.find('.requisition-type').attr('id', `requisition-type-${rowCount}`).attr('name', `requisition[${rowCount}][type]`);
                row.find('.requisition-product').attr('id', `requisition-product-${rowCount}`).attr('name', `requisition[${rowCount}][product]`);
                row.find('.requisition-description').attr('id', `requisition-description-${rowCount}`).attr('name', `requisition[${rowCount}][description]`);
                row.find('.requisition-quantity').attr('id', `requisition-quantity-${rowCount}`).attr('name', `requisition[${rowCount}][quantity]`);
                row.find('.requisition-amount').attr('id', `requisition-amount-${rowCount}`).attr('name', `requisition[${rowCount}][amount]`);
                row.find('.requisition-total').attr('id', `requisition-total-${rowCount}`).attr('name', `requisition[${rowCount}][total]`);
                row.find('.requisition-status').attr('id', `requisition-status-${rowCount}`).attr('name', `requisition[${rowCount}][status]`);
                row.find('.rejection-note').attr('id', `requisition-rejection-note-${rowCount}`).attr('name', `requisition[${rowCount}][rejection_note]`);
                row.find('td:eq(0)').text(rowCount + 1);

                initializeRequisitionProducts(row.find('.requisition-product'));
                initializeRequisitionType(row.find('.requisition-type'));

                $('#requisition-items').append(row);
            });

            $(document).on('change', '.requisition-quantity', function () {
                let quantity = parseFloat($(this).val()) || 0;
                let price = parseFloat($(this).parent().next().find('input.requisition-amount').val()) || 0;

                $(this).parent().next().next().find('input.requisition-total').val(parseFloat(quantity * price).toFixed(2));
                calculateRequisitionTotal();
            });

            $(document).on('change', '.requisition-amount', function () {
                let price = parseFloat($(this).val()) || 0;
                let quantity = parseFloat($(this).parent().prev().find('input.requisition-quantity').val()) || 0;

                $(this).parent().next().find('input.requisition-total').val(parseFloat(quantity * price).toFixed(2));
                calculateRequisitionTotal();
            });

            $(document).on('click', '.requisition-remove-row', function () {
                let that = this;
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this line item!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(that).closest('tr').remove();
                        calculateRequisitionTotal();
                    }
                });
            });

            $(document).on('change', '#visiting_date, #dms_days, #dms_hours, #dms_minutes', function () {
                $('#technicians').val('').trigger('change');
            });

            // Handle requisition status change to show/hide rejection note
            $(document).on('change', '.requisition-status', function () {
                let statusValue = $(this).val();
                let rejectionNoteContainer = $(this).siblings('.rejection-note-container');
                let rejectionNoteTextarea = rejectionNoteContainer.find('.rejection-note');

                if (statusValue === 'REJECTED') {
                    rejectionNoteContainer.show();
                    rejectionNoteTextarea.attr('required', true);
                } else {
                    rejectionNoteContainer.hide();
                    rejectionNoteTextarea.attr('required', false);
                    rejectionNoteTextarea.val('');
                }
            });

            // Initialize requisition calculations on page load
            calculateRequisitionTotal();

        });
    </script>
@endpush