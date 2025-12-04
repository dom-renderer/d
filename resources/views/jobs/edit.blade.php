@extends('layouts.app', ['title' => $title, 'subTitle' => $subTitle, 'rightSideTitle' => $job->code, 'datepicker' => true, 'rightSideFilter' => $rightSideFilter])

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/intel-tel.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/jquery.datetimepicker.css') }}">

<style>
    div.iti--inline-dropdown {
		min-width: 100%!important;
	}
	.iti__selected-flag {
		height: 32px!important;
	}
	.iti--show-flags {
		width: 100%!important;
	}  
	label.error {
		color: red;
	}
	#phone_number{
		font-family: "Hind Vadodara",-apple-system,BlinkMacSystemFont,"Segoe UI","Helvetica Neue",Arial,sans-serif;
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
        z-index: 9999!important;
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
    }.table tbody, .table td, .table tfoot, .table th, .table thead, .table tr {
        border: none;
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
                <form method="POST" action="{{ route('jobs.update', encrypt($job->id)) }}" enctype="multipart/form-data" id="customerForm">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="customer" class="form-label"> Customer <span class="text-danger">*</span></label>
                        <select name="customer" id="customer" required disabled>
                            <option value="{{ $job->customer_id }}" selected>{{ $job->customer->name }}</option>
                        </select>
                        @error('customer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row mb-2 mt-2">
                        <div class="col-5">
                            <div class="mb-4">
                                <label for="customer_name" class="form-label"> Contact Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ old('customer_name', $job->contact_name) }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-4">
                                <label for="customer_email" class="form-label"> Email <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customer_email" name="customer_email" value="{{ old('customer_email', $job->email) }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-4 row">
                                <div class="col-12">
                                    <label for="customer_phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label> <br>
                                    <input type="hidden" name="customer_alternate_dial_code" id="customer_dial_code" value="{{ old('customer_alternate_dial_code', $job->contact_dial_code) }}">
                                    <input type="tel" class="form-control" id="customer_phone_number" name="customer_alternate_phone_number" value="{{ old('customer_alternate_phone_number', $job->contact_phone_number) }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="mb-4">
                                <label for="customer_billing_name" class="form-label"> Billing Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customer_billing_name" name="customer_billing_name" value="{{ old('customer_billing_name', $job->billing_name) }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-4">
                                <label for="customer_address_line_1" class="form-label"> Address Line 1 <span class="text-danger">*</span></label>
                                <textarea name="customer_address_line_1" id="customer_address_line_1" class="form-control" required>{{ old('customer_address_line_1', $job->address_line_1) }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-4">
                                <label for="customer_address_line_2" class="form-label"> Address Line 2 </label>
                                <textarea name="customer_address_line_2" id="customer_address_line_2" class="form-control">{{ old('customer_address_line_2', $job->address_line_2) }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-4">
                                <button type="button" class="btn btn-primary" id="mapButton2">
                                    Map (Drop pin on Location)
                                </button>
                                <input type="hidden" name="customer_location_url" id="customer_location_url" value="{{ old('customer_location_url', $job->location_url) }}">
                                <input type="hidden" name="customer_latitude" id="customer_latitude" value="{{ old('customer_latitude', $job->latitude) }}">
                                <input type="hidden" name="customer_longitude" id="customer_longitude" value="{{ old('customer_longitude', $job->longitude) }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-12 mb-3">
                            <label for="title" class="d-inline-block mb-3"> Title <span class="text-danger"> * </span> </label>
                            <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $job->title) }}" required>
                        </div>
                        <div class="col-4 datemng">
                            <label for="opening_date" class="d-inline-block mb-3">Job Added Date <span class="text-danger"> *</span></label>
                            <input type="text" id="opening_date" name="opening_date" class="form-control dms_datepicker" value="{{ old('opening_date', \Carbon\Carbon::parse( $job->opening_date )->format( 'd-m-Y H:i' )) }}" required>
                        </div>
                        <div class="col-4 datemng">
                            <label for="visiting_date" class="d-inline-block mb-3">Expected Service Date<span class="text-danger"> *</span></label>
                            <input type="text" id="visiting_date" name="visiting_date" class="form-control dms_datepicker" value="{{ old('visiting_date', \Carbon\Carbon::parse( $job->visiting_date )->format( 'd-m-Y H:i' )) }}" required>
                        </div>
                        {{-- <div class="col-3 datemng">
                            <label for="expected_date" class="d-inline-block mb-3">Expected Service Date<span class="text-danger"> *</span></label>
                            <input type="text" id="expected_date" name="expected_date" class="form-control dms_datepicker" value="{{ old('expected_date', \Carbon\Carbon::parse( $job->expected_date )->format( 'd-m-Y h:i' )) }}" readonly required>
                        </div> --}}
                        <div class="col-4 datemng">
                            <label class="d-inline-block mb-3">Approx Time <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="dms_days" id="dms_days" min="0" placeholder="Days" value="{{ isset($extractdTime[0]) ? $extractdTime[0] : '00' }}">
                                <span class="input-group-text">Days</span>

                                <input type="number" class="form-control" name="dms_hours" id="dms_hours" min="0" max="23" placeholder="Hours" value="{{ isset($extractdTime[1]) ? $extractdTime[1] : '00' }}">
                                <span class="input-group-text">Hours</span>

                                <input type="number" class="form-control" name="dms_minutes" id="dms_minutes" min="0" max="59" placeholder="Minutes" value="{{ isset($extractdTime[2]) ? $extractdTime[2] : '00' }}">
                                <span class="input-group-text">Minutes</span>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <label for="expertise" class="d-inline-block mb-3"> Expertise </label>
                            <select name="expertise[]" id="expertise" multiple>
                            @foreach($job->expertise as $expertise)
                                @if(isset($expertise->expertise->id))
                                    <option value="{{ $expertise->expertise->id }}" selected>{{ $expertise->expertise->name }}</option>
                                @endif
                            @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <label for="engineers" class="d-inline-block mb-3"> Engineers <span class="text-danger"> * </span> </label>
                            <select name="engineers[]" id="engineers" multiple>
                                @foreach($job->engineers as $engineer)
                                    @if(isset($engineer->engineer->id))
                                        <option value="{{ $engineer->engineer->id }}" selected>{{ $engineer->engineer->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <label for="technicians" class="d-inline-block mb-3"> Technicians </label>
                            <select name="technicians[]" id="technicians" multiple>
                                @foreach($job->technicians as $technician)
                                    @if(isset($technician->technician->id))
                                        <option value="{{ $technician->technician->id }}" selected>{{ $technician->technician->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <label for="description" class="d-inline-block mb-3"> Description <span class="text-danger"> * </span> </label>
                            <textarea name="description" id="description" class="form-control" required>{{ old('description', $job->description) }}</textarea>
                        </div>
                        <div class="col-12 mt-2">
                            <label for="summary" class="d-inline-block mb-3"> Summary </label>
                            <textarea name="summary" id="summary" class="form-control">{{ old('summary', $job->summary) }}</textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <label class="d-inline-block mb-3">Job Attached Photos</label>
                            @foreach ( $job->jobbeforeattachment as $attachment )
                                <a href="{{ $attachment }}" target="_blank">
                                    <img src="{{ $attachment }}" class="img-thumbnail mt-3" width="100" style="margin-right:20px;">
                                </a>
                            @endforeach
                        </div>
                        <div class="col-12 mt-4">
                            <label for="dms_attachment" class="d-inline-block mb-3">After Uploaded Photos</label>
                            <input type="file" name="dms_attachment[]" id="dms_attachment" class="form-control" accept="image/*" multiple>
                            <input type="hidden" name="dms_old_attachment" value="{{ $job->attachment }}">
                            @foreach ( $job->jobattachment as $attachment )
                                <a href="{{ $attachment }}" target="_blank">
                                    <img src="{{ $attachment }}" class="img-thumbnail mt-3" width="100" style="margin-right:20px;">
                                </a>
                            @endforeach
                        </div>
                        <div class="col-12 mt-4">
                            <label for="dms_job_priority" class="d-inline-block mb-3">Job Priority</label>
                            <select class="form-select" name="dms_job_priority" id="dms_job_priority">
                                <option value="LOW" {{ $job->priority == 'LOW' ? 'selected' : '' }}>Low Priority</option>
                                <option value="NORMAL" {{ $job->priority == 'NORMAL' ? 'selected' : '' }}>Normal Priority</option>
                                <option value="HIGH" {{ $job->priority == 'HIGH' ? 'selected' : '' }}>High Priority</option>
                                <option value="CRITICAL" {{ $job->priority == 'CRITICAL' ? 'selected' : '' }}>Critical Priority</option>
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
                                    <option value="0" {{ $job->requires_deposit ? '' : 'selected' }}>No</option>
                                    <option value="1" {{ $job->requires_deposit ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 deposit-fields" style="display: {{ $job->requires_deposit ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label for="deposit_type" class="form-label"> Deposit Type </label>
                                <select name="deposit_type" id="deposit_type" class="form-select">
                                    <option value="FIX" {{ $job->deposit_type == 'FIX' ? 'selected' : '' }}>Fixed Amount</option>
                                    <option value="PERCENT" {{ $job->deposit_type == 'PERCENT' ? 'selected' : '' }}>Percentage</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 deposit-fields" style="display: {{ $job->requires_deposit ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label for="deposit_amount" class="form-label"> Deposit Amount </label>
                                <input type="number" name="deposit_amount" id="deposit_amount" class="form-control" step="0.01" min="0" value="{{ old('deposit_amount', $job->deposit_amount) }}">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="accordion dms_accordion pt-3" id="dms_job_accordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dms_add_services" aria-expanded="true" aria-controls="dms_add_services">
                                    Services
                                </button>
                            </h2>
                            <div id="dms_add_services" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#dms_job_accordion">
                                <div class="accordion-body">
                                    <button type="button" class="service-row-add btn  btn-primary float-end"><i class="fa fa-plus"></i>Add Item</button>
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
                                                    @foreach($job->services as $service)
                                                    <tr data-service-id="{{ $service->id }}">
                                                        <td>
                                                            {{ $loop->iteration }}
                                                        </td>
                                                        <td>
                                                            <input type="text" name="service[{{ $service->id }}][title]" class="form-control" value="{{ $service->title }}" required>
                                                        </td>
                                                        <td>
                                                            <textarea name="service[{{ $service->id }}][description]" class="form-control">{{ $service->description }}</textarea>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="service[{{ $service->id }}][quantity]" class="form-control service-quantity" value="{{ $service->quantity }}" min="1" step="1" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="service[{{ $service->id }}][price]" class="form-control service-price" value="{{ $service->price }}" min="0" step="0.01" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="service[{{ $service->id }}][total_amount]" class="form-control service-total-amount" value="{{ $service->total_amount }}" min="0" step="0.01" required readonly>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-sm service-remove-row"> <i class="fa fa-trash"> </i> </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="5"> Total </td>
                                                        <td colspan="2" id="service-total">
                                                            {{ number_format($job->services->sum('total_amount'), 2) }}
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
                                                        <label for="services_discount_type" class="form-label">Discount Type</label>
                                                        <select name="services_discount_type" id="services_discount_type" class="form-select">
                                                            <option value="FIX" {{ $job->services_discount_type == 'FIX' ? 'selected' : '' }}>Fixed Amount</option>
                                                            <option value="PERCENT" {{ $job->services_discount_type == 'PERCENT' ? 'selected' : '' }}>Percentage</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="services_discount_amount" class="form-label">Discount Amount</label>
                                                        <input type="number" name="services_discount_amount" id="services_discount_amount" class="form-control" step="0.01" min="0" value="{{ old('services_discount_amount', $job->services_discount_amount) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5 class="text-primary mb-3 sml-hd">Services VAT</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="services_vat_type" class="form-label">VAT Type</label>
                                                        <select name="services_vat_type" id="services_vat_type" class="form-select">
                                                            <option value="PERCENT" {{ $job->services_vat_type == 'PERCENT' ? 'selected' : '' }}>Percentage</option>
                                                            <option value="FIX" {{ $job->services_vat_type == 'FIX' ? 'selected' : '' }}>Fixed Amount</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="services_vat_amount" class="form-label">VAT Amount</label>
                                                        <input type="number" name="services_vat_amount" id="services_vat_amount" class="form-control" step="0.01" min="0" value="{{ old('services_vat_amount', $job->services_vat_amount) }}">
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
                                                                <div class="form-control-plaintext" id="services-subtotal-display">$0.00</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="mb-3">
                                                                <label class="form-label head-sml">Services Discount</label>
                                                                <div class="form-control-plaintext" id="services-discount-display">$0.00</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="mb-3">
                                                                <label class="form-label">Services VAT</label>
                                                                <div class="form-control-plaintext" id="services-vat-display">$0.00</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="mb-3">
                                                                <label class="form-label"><strong>Services Total</strong></label>
                                                                <div class="form-control-plaintext" id="services-total-display"><strong>$0.00</strong></div>
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
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dms_requisitions" aria-expanded="false" aria-controls="dms_requisitions">
                                    Requisitions
                                </button>
                            </h2>
                            <div id="dms_requisitions" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#dms_job_accordion">
                                <div class="accordion-body">
                                    <button type="button" class="requisition-row-add btn btn-primary float-end"><i class="fa fa-plus"></i>Add Item</button>
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
                                            <tbody id="requisition-items">
                                                @if(isset($job->requisitions) && $job->requisitions->count() > 0)
                                                    @foreach($job->requisitions->first()->items ?? [] as $index => $item)
                                                    <tr>
                                                        <td>
                                                            {{ $loop->iteration }}
                                                        </td>
                                                        <td class="requisition-type-container">
                                                            <select class="requisition-type form-select" id="requisition-type-{{ $index }}" name="requisition[{{ $index }}][type]" required> 
                                                                <option value="INVENTORY" {{ $item->type == 'INVENTORY' ? 'selected' : '' }}> Inventory </option>
                                                                <option value="VENDOR" {{ $item->type == 'VENDOR' ? 'selected' : '' }}> Vendor </option>
                                                            </select>
                                                            <input type="hidden" name="requisition[{{ $index }}][id]" value="{{ $item->id }}">
                                                        </td>
                                                        <td class="requisition-product-container">
                                                            @if($item->type == 'INVENTORY')
                                                                <select class="requisition-product form-select" id="requisition-product-{{ $index }}" name="requisition[{{ $index }}][product]" required>
                                                                    @if($item->product)
                                                                        <option value="{{ $item->product_id }}" selected>{{ $item->product->name }}</option>
                                                                    @endif
                                                                </select>
                                                            @else
                                                            <select class="requisition-vendor form-select" id="requisition-vendor-{{ $index }}" name="requisition[{{ $index }}][vendor]" required>
                                                                @if(isset($item->vendor->id))
                                                                    <option value="{{ $item->vendor_id }}" required> {{ $item->vendor->name }} </option>
                                                                @endif
                                                            </select> <br/> <br/>
                                                                <input class="requisition-product form-control" placeholder="Product Name" id="requisition-product-{{ $index }}" name="requisition[{{ $index }}][product]" value="{{ $item->product_name ?? '' }}" required>
                                                            @endif
                                                        </td>
                                                        <td class="requisition-description-container">
                                                            <textarea class="requisition-description form-control" id="requisition-description-{{ $index }}" name="requisition[{{ $index }}][description]" placeholder="Description">{{ $item->description }}</textarea>
                                                        </td>
                                                        <td class="requisition-quantity-container">
                                                            <input type="number" min="1" class="requisition-quantity form-control" id="requisition-quantity-{{ $index }}" name="requisition[{{ $index }}][quantity]" value="{{ $item->quantity }}" required>
                                                        </td>
                                                        <td class="requisition-amount-container">
                                                            <input type="number" min="0" step="0.01" class="requisition-amount form-control" id="requisition-amount-{{ $index }}" name="requisition[{{ $index }}][amount]" value="{{ $item->amount }}" required>
                                                        </td>
                                                        <td class="requisition-total-container">
                                                            <input type="number" min="0" class="requisition-total form-control" id="requisition-total-{{ $index }}" name="requisition[{{ $index }}][total]" value="{{ $item->total }}" readonly>
                                                        </td>
                                                        <td class="requisition-status-container">
                                                            <select class="requisition-status form-select" id="requisition-status-{{ $index }}" 
                                                                    name="requisition[{{ $index }}][status]" required>
                                                                <option value="PENDING" {{ $item->status == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                                                <option value="APPROVED" {{ $item->status == 'APPROVED' ? 'selected' : '' }}>APPROVED</option>
                                                                <option value="REJECTED" {{ $item->status == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                                                            </select>
                                                            <div class="rejection-note-container mt-2" style="display: {{ $item->status == 'REJECTED' ? 'block' : 'none' }};">
                                                                <textarea class="form-control rejection-note" id="requisition-rejection-note-{{ $index }}" 
                                                                          name="requisition[{{ $index }}][rejection_note]" placeholder="Rejection reason..." 
                                                                          rows="2" {{ $item->status == 'REJECTED' ? 'required' : '' }}>{{ $item->rejection_note ?? '' }}</textarea>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button class="btn requisition-remove-row" type="button"> <img src="{{ url('settings-media/plus-sms.svg') }}" alt="" /></button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="6"> Total </td>
                                                    <td colspan="2" id="requisition-grand-total-column">
                                                        @if(isset($job->requisitions) && $job->requisitions->count() > 0)
                                                            {{ number_format($job->requisitions->first()->items->sum('total'), 2) }}
                                                        @else
                                                            0.00
                                                        @endif
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
                        <input type="hidden" name="dms_job_status" id="dms_job_status_hidden" value="{{ $job->status }}">
                        <button type="submit" class="btn btn-primary">Update Job</button>
                        <a href="{{ route('jobs.index') }}" class="btn btn-secondary ms-3">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Customer Map Modal -->
<div class="modal fade" id="customerMapModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="customerMapModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="customerMapModalLabel">Select Customer Location</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="map-search-container">
                <div class="search-fix mb-4">
                    <input class="form-control" id="customer-pac-input" type="text" placeholder="Search for a place"/>
                    <i class="fa fa-search" aria-hidden="true"></i>
                </div>
            </div>
            <div id="customer-map" style="height: 400px; width: 100%;"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary ms-3" id="confirmCustomerLocation">Confirm Location</button>
        </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script src="{{ asset('assets/js/jquery-validate.min.js') }}"></script>
<script src="{{ asset('assets/js/intel-tel.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
<script src="{{ asset('assets/js/jquery.datetimepicker.js') }}"></script>
<script>
$(document).ready(function() {

    $('#customer').select2({
        width: '100%'
    });

    $('#expertise').select2({
        placeholder: 'Select an expertise',
        width: '100%',
        ajax: {
            url: "{{ route('expertise-list') }}",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    searchQuery: params.term,
                    page: params.page || 1,  
                    _token: "{{ csrf_token() }}"
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function(item) {
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
        templateResult: function(data) {
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
        initialCountry: "{{ $dial_code_iso }}",
        separateDialCode: true,
        nationalMode: false,
        preferredCountries: @json(\App\Models\Country::select('iso2')->pluck('iso2')->toArray()),
        utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
    });

    customerInput.addEventListener("countrychange", update_dial_code);
    customerInput.addEventListener('keyup', update_dial_code);
    function update_dial_code() {
        let countryData = customerIti.getSelectedCountryData();
        if ( typeof countryData.dialCode != undefined && countryData.dialCode != '' && countryData.dialCode != null ) {
            $('#customer_dial_code').val(countryData.dialCode);
        }
    }

    $('#technicians').select2({
        allowClear: true,
        placeholder: 'Select technicians',
        width: '100%',
        ajax: {
            url: "{{ route('user-list') }}",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    _token: "{{ csrf_token() }}",
                    searchQuery: params.term,
                    page: params.page || 1,
                    expertises: function () {
                        return $('#expertise').val();
                    },
                    customer_id: $('#customer').val(),
                    job_id: "{{ $job->id }}",
                    roles: ['technician'],
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function(item) {
                        return { id: item.id, text: item.text };
                    }),
                    pagination: { more: data.pagination.more }
                };
            },
            cache: true
        },
        templateResult: function(data) {
            if (data.loading) return data.text;
            var $result = $('<span></span>');
            $result.text(data.text);
            return $result;
        }
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
            data: function(params) {
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
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function(item) {
                        return { id: item.id, text: item.text };
                    }),
                    pagination: { more: data.pagination.more }
                };
            },
            cache: true
        },
        templateResult: function(data) {
            if (data.loading) return data.text;
            var $result = $('<span></span>');
            $result.text(data.text);
            return $result;
        }
    }).on('change', function() {

    });

    let customerMap, customerMarker, customerSearchBox;

    $('#mapButton2').on('click', function() {
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

            customerMap.setCenter(place.geometry.location);
            customerMap.setZoom(15);
            customerMarker.setPosition(place.geometry.location);
            updateCustomerLocationData(place.geometry.location, place.formatted_address);
        });

        customerMap.addListener('click', (event) => {
            customerMarker.setPosition(event.latLng);
            updateCustomerLocationData(event.latLng);
        });

        customerMarker.addListener('dragend', (event) => {
            updateCustomerLocationData(event.latLng);
        });
    }

    function updateCustomerLocationData(latLng, address = null) {
        $('#customer_latitude').val(latLng.lat());
        $('#customer_longitude').val(latLng.lng());
        
        if (address) {
            $('#customer_location_url').val(address);
        } else {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    $('#customer_location_url').val(results[0].formatted_address);
                }
            });
        }
    }

    $('#requires_deposit').on('change', function() {
        if ($(this).val() == '1') {
            $('.deposit-fields').show();
            $('#deposit_type, #deposit_amount').prop('required', true);
        } else {
            $('.deposit-fields').hide();
            $('#deposit_type, #deposit_amount').prop('required', false);
            $('#deposit_amount').val('');
        }
    });

    $('#deposit_type').on('change', function() {
        if ($(this).val() == 'PERCENT') {
            $('#deposit_amount').attr('max', '100');
            $('#deposit_amount').attr('placeholder', 'Enter percentage (0-100)');
        } else {
            $('#deposit_amount').removeAttr('max');
            $('#deposit_amount').attr('placeholder', 'Enter amount');
        }
    });

    let materialRowCount = {{ $job->materials->count() }};
    let serviceRowCount = {{ $job->services->count() }};

    $(document).on('click', '.row-add', function() {
        materialRowCount++;
        const newRow = `
            <tr data-material-id="new_${materialRowCount}">
                <td>
                    ${materialRowCount}
                </td>
                <td>
                    <select name="material[new_${materialRowCount}][category]" class="form-select material-category" required>
                        <option value="">Select Category</option>
                    </select>
                </td>
                <td>
                    <select name="material[new_${materialRowCount}][product]" class="form-select material-product" required>
                        <option value="">Select Product</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="material[new_${materialRowCount}][description]" class="form-control" >
                </td>
                <td>
                    <input type="number" name="material[new_${materialRowCount}][quantity]" class="form-control material-quantity" min="1" step="1" required>
                </td>
                <td>
                    <input type="number" name="material[new_${materialRowCount}][price]" class="form-control material-price" min="0" step="0.01" required>
                </td>
                <td>
                    <input type="number" name="material[new_${materialRowCount}][amount]" class="form-control material-amount" min="0" step="0.01" required readonly>
                </td>
                <td>
                    <button type="button" class="btn  remove-row"><img src="{{ url('settings-media/plus-sms.svg') }}" alt="" /></button>
                </td>
            </tr>
        `;

        $('#materials-container').append(newRow);

        const newRowElement = $(`tr[data-material-id="new_${materialRowCount}"]`);
        initializeMaterialSelects(newRowElement);
    });

    $(document).on('click', '.service-row-add', function() {
        serviceRowCount++;
        const newRow = `
            <tr data-service-id="new_${serviceRowCount}">
                <td>
                    ${serviceRowCount}
                </td>
                <td>
                    <input type="text" name="service[new_${serviceRowCount}][title]" class="form-control" placeholder="Service Title" required>
                </td>
                <td>
                    <textarea name="service[new_${serviceRowCount}][description]" class="form-control" placeholder="Description"></textarea>
                </td>
                <td>
                    <input type="number" name="service[new_${serviceRowCount}][quantity]" class="form-control service-quantity" min="1" step="1" value="1" required>
                </td>
                <td>
                    <input type="number" name="service[new_${serviceRowCount}][price]" class="form-control service-price" min="0" step="0.01" required>
                </td>
                <td>
                    <input type="number" name="service[new_${serviceRowCount}][total_amount]" class="form-control service-total-amount" min="0" step="0.01" readonly>
                </td>
                <td>
                    <button type="button" class="btn service-remove-row"><img src="{{ url('settings-media/plus-sms.svg') }}" alt="" /></button>
                </td>
            </tr>
        `;

        $('#services-container').append(newRow);
    });

    $(document).on('click', '.remove-row', function() {
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
                calculateTotal();
            }
        });
    });

    $(document).on('click', '.service-remove-row', function() {
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
    });

    $(document).on('change', '.service-quantity, .service-price', function() {
        let quantity = parseFloat($(this).closest('tr').find('.service-quantity').val()) || 0;
        let price = parseFloat($(this).closest('tr').find('.service-price').val()) || 0;
        $(this).closest('tr').find('.service-total-amount').val(parseFloat(quantity * price).toFixed(2));
        calculateServiceTotal();
    });

    $(document).on('change', '#services_discount_type, #services_discount_amount, #services_vat_type, #services_vat_amount, #parts_discount_type, #parts_discount_amount, #parts_vat_type, #parts_vat_amount', function() {
        calculateGrandTotal();
    });

    function initializeMaterialSelects(row) {
        row.find('.material-category').select2({
            allowClear: true,
            placeholder: 'Select category',
            width: '100%',
            ajax: {
                url: "{{ route('product-category-list') }}",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        searchQuery: params.term,
                        page: params.page || 1,
                        _token: "{{ csrf_token() }}"
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            templateResult: function(data) {
                if (data.loading) {
                    return data.text;
                }
                var $result = $('<span></span>');
                $result.text(data.text);
                return $result;
            }
        });

        row.find('.material-product').select2({
            allowClear: true,
            placeholder: 'Select product',
            width: '100%',
            ajax: {
                url: "{{ route('product-list') }}",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        searchQuery: params.term,
                        page: params.page || 1,  
                        _token: "{{ csrf_token() }}",
                        category: function () {
                            return row.find('.material-category option:selected').val()
                        }
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: $.map(data.items, function(item) {
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
            templateResult: function(data) {
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
                let qty = parseFloat(selectedElement.parent().parent().find('input.material-quantity')) || 1;

                selectedElement.parent().parent().find('input.material-price').val(parseFloat(selectedProductPrice * qty).toFixed(2));
                selectedElement.parent().parent().find('input.material-amount').val(parseFloat(selectedProductPrice * qty).toFixed(2));
            }
        });
    }

    $('tr[data-material-id]').each(function() {
        initializeMaterialSelects($(this));
    });

    $(document).on('change', '.material-category', function() {
        const row = $(this).closest('tr');
        const productSelect = row.find('.material-product');
        productSelect.val(null).trigger('change');
    });

    $(document).on('input', '.material-quantity, .material-price', function() {
        const row = $(this).closest('tr');
        const quantity = parseFloat(row.find('.material-quantity').val()) || 0;
        const price = parseFloat(row.find('.material-price').val()) || 0;
        const amount = quantity * price;
        row.find('.material-amount').val(amount.toFixed(2));
        calculateTotal();
    });

    function calculateTotal() {
        let total = 0;
        $('.material-amount').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        
        $('#material-total').text(convertIntoAmount.format(total.toFixed(2)));
        calculateGrandTotal();
    }

    function calculateServiceTotal() {
        let total = 0;
        $('.service-total-amount').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        
        $('#service-total').text(convertIntoAmount.format(total.toFixed(2)));
        calculateGrandTotal();
    }

    function calculateServicesTotal() {

        let serviceTotal = 0;
        $('.service-total-amount').each(function() {
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

    function calculatePartsTotal() {

        let materialTotal = 0;
        $('.material-amount').each(function() {
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

    function calculateGrandTotal() {
        let servicesTotal = calculateServicesTotal();
        let partsTotal = calculatePartsTotal();
        let grandTotal = servicesTotal + partsTotal;

        $('#grand-total-display').html('<strong>$' + convertIntoAmount.format(grandTotal.toFixed(2)) + '</strong>');
        $('#grand_total').val(grandTotal.toFixed(2));
    }

    $('#confirmCustomerLocation').on('click', function() {
        $('#customerMapModal').modal('hide');
    });

    calculateGrandTotal();

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

    $('#customerForm').validate({
        ignore: [],
        rules: {
            customer: { required: true },
            title: { required: true },
            description: { required: true },
            'engineers[]': { 
                required: true
            },
            "dms_attachment[]": {
                imageFiles: true
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
        errorPlacement: function(error, element) {
            if (element.attr('id') === 'customer_phone_number' || element.attr('id') === 'technicians' || element.attr('id') === 'engineers' || element.attr('id') === 'dms_days' || element.attr('id') === 'dms_hours' || element.attr('id') === 'dms_minutes') {
                error.insertAfter(element.parent());
            } else {
                error.appendTo(element.parent());
            }
        },
        submitHandler: function (form) {
            if (customerIti.isValidNumber()) {
                $('#customer_dial_code').val(customerIti.s.dialCode);
            }
            form.submit();
        }
    });

    $(document).on('change', '#dms_job_status', function() {
        $( '#dms_job_status_hidden' ).val( $(this).val() );
    });
    $('.service-price').change();

    // Requisition functionality
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
        
        $('.requisition-total').each(function() {
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
                    data: function(params) {
                        return {
                            searchQuery: params.term,
                            page: params.page || 1,  
                            _token: "{{ csrf_token() }}"
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data.items, function(item) {
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
                templateResult: function(data) {
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
                    data: function(params) {
                        return {
                            searchQuery: params.term,
                            page: params.page || 1,
                            _token: "{{ csrf_token() }}",
                            expertises: '',
                            roles: ['vendor']
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data.items, function(item) {
                                return { id: item.id, text: item.text };
                            }),
                            pagination: { more: data.pagination.more }
                        };
                    },
                    cache: true
                },
                templateResult: function(data) {
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

    $('.requisition-product').each(function() {
        if ($(this).is('select')) {
            initializeRequisitionProducts($(this));
        }
    });
    
    $('.requisition-type').each(function() {
        initializeRequisitionType($(this));
    });

    $('.requisition-vendor').each(function() {
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
        if ($('#requisition-items tr').length > 1) {
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
        }
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