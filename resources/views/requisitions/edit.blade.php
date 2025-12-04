@extends('layouts.app', ['title' => $title, 'subTitle' => $subTitle])

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
<style>
    label.error {
        color: red;
    }
    .table tbody, .table td, .table tfoot, .table th, .table thead, .table tr {
        border-color: var(--bs-border-color);
        border: none;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('requisitions.update', encrypt($requisition->id)) }}" id="requisitionForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="job_id" class="form-label">Job <span class="text-danger">*</span></label>
                                <select name="job_id" id="job_id" class="form-select" required>
                                    <option value="{{ $requisition->job_id }}" selected>{{ $requisition->job->code }}</option>
                                </select>
                                @error('job_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row ovfl-mn">
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
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="multi-items">
                                @foreach($requisition->items as $index => $item)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="row-type-container">
                                        <select class="row-type" id="row-type-{{ $index }}" name="requisition[{{ $index }}][type]" required> 
                                            <option value="INVENTORY" {{ $item->type == 'INVENTORY' ? 'selected' : '' }}> Inventory </option>
                                            <option value="VENDOR" {{ $item->type == 'VENDOR' ? 'selected' : '' }}> Vendor </option>
                                        </select>
                                        <input type="hidden" name="requisition[{{ $index }}][id]" value="{{ $item->id }}">
                                    </td>
                                    <td class="row-product-container">
                                        @if($item->type == 'INVENTORY')
                                            <select class="row-product" id="row-product-{{ $index }}" name="requisition[{{ $index }}][product]" required>
                                                @if($item->product)
                                                    <option value="{{ $item->product_id }}" selected>{{ $item->product->name }}</option>
                                                @endif
                                            </select>
                                        @else
                                        <select class="row-vendor" id="row-vendor-{{ $index }}" name="requisition[{{ $index }}][vendor]" required>
                                            @if(isset($item->vendor->id))
                                                <option value="{{ $item->vendor_id }}" required> {{ $item->vendor->name }} </option>
                                            @endif
                                        </select> <br/> <br/>
                                            <input class="row-product form-control" placeholder="Product Name" id="row-product-{{ $index }}" name="requisition[{{ $index }}][product]" value="{{ $item->product_name ?? '' }}" required>
                                        @endif
                                    </td>
                                    <td class="row-description-container">
                                        <textarea class="row-description form-control" id="row-description-{{ $index }}" name="requisition[{{ $index }}][description]" placeholder="Description">{{ $item->description }}</textarea>
                                    </td>
                                    <td class="row-quantity-container">
                                        <input type="number" min="1" class="row-quantity form-control" id="row-quantity-{{ $index }}" name="requisition[{{ $index }}][quantity]" value="{{ $item->quantity }}" required>
                                    </td>
                                    <td class="row-amount-container">
                                        <input type="number" min="0" step="0.01" class="row-amount form-control" id="row-amount-{{ $index }}" name="requisition[{{ $index }}][amount]" value="{{ $item->amount }}" required>
                                    </td>
                                    <td class="row-total-container">
                                        <input type="number" min="0" class="row-total form-control" id="row-total-{{ $index }}" name="requisition[{{ $index }}][total]" value="{{ $item->total }}" readonly>
                                    </td>
                                    <td class="row-status-container">
                                        <select class="row-status form-select" id="row-status-{{ $index }}" 
                                                name="requisition[{{ $index }}][status]" required>
                                            <option value="PENDING" {{ $item->status == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                            <option value="APPROVED" {{ $item->status == 'APPROVED' ? 'selected' : '' }}>APPROVED</option>
                                            <option value="REJECTED" {{ $item->status == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                                        </select>
                                        <div class="rejection-note-container mt-2" style="display: {{ $item->status == 'REJECTED' ? 'block' : 'none' }};">
                                            <textarea class="form-control rejection-note" id="row-rejection-note-{{ $index }}" 
                                                      name="requisition[{{ $index }}][rejection_note]" placeholder="Rejection reason..." 
                                                      rows="2" {{ $item->status == 'REJECTED' ? 'required' : '' }}>{{ $item->rejection_note ?? '' }}</textarea>
                                        </div>
                                    </td>
                                    <td>
                                        @if($index == 0)
                                            <button class="btn add-row" type="button"><img src="{{ url('settings-media/dms-minus.svg') }}" alt="" /></button>
                                        @else
                                            <button class="btn add-row" type="button"><img src="{{ url('settings-media/dms-minus.svg') }}" alt="" /></button>
                                            <button class="btn remove-row" type="button"> <img src="{{ url('settings-media/plus-sms.svg') }}" alt="" /></button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6"> Total </td>
                                    <td colspan="2" id="grand-total-column">
                                        {{ number_format($requisition->items->sum('total'), 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Update Requisition</button>
                            <a href="{{ route('requisitions.index') }}" class="btn btn-secondary ms-3">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-validate.min.js') }}"></script>
<script>
$(document).ready(function() {

    let itemRow = `
        <tr>
            <td> 0 </td>
            <td class="row-type-container">
                <select class="row-type" id="row-type-0" name="requisition[0][type]" required>
                    <option value="INVENTORY"> Inventory </option>
                    <option value="VENDOR"> Vendor </option>
                </select>
            </td>
            <td class="row-product-container">
                <select class="row-product" id="row-product-0" name="requisition[0][product]" required>
                </select>
            </td>
            <td class="row-description-container">
                <textarea class="row-description form-control" id="row-description-0" name="requisition[0][description]" placeholder="Description"></textarea>
            </td>
            <td class="row-quantity-container">
                <input type="number" min="1" class="row-quantity form-control" id="row-quantity-0" name="requisition[0][quantity]" value="1" required>
            </td>
            <td class="row-amount-container">
                <input type="number" min="0" step="0.01" class="row-amount form-control" id="row-amount-0" name="requisition[0][amount]" required>
            </td>
            <td class="row-total-container">
                <input type="number" min="0" class="row-total form-control" id="row-total-0" name="requisition[0][total]" readonly>
            </td>
            <td class="row-status-container">
                <select class="row-status form-select" id="row-status-0" name="requisition[0][status]" required>
                    <option value="PENDING" selected>PENDING</option>
                    <option value="APPROVED">APPROVED</option>
                    <option value="REJECTED">REJECTED</option>
                </select>
                <div class="rejection-note-container mt-2" style="display: none;">
                    <textarea class="form-control rejection-note" id="row-rejection-note-0" name="requisition[0][rejection_note]" placeholder="Rejection reason..." rows="2"></textarea>
                </div>
            </td>
            <td>
                <button class="btn  add-row" type="button"><img src="{{ url('settings-media/dms-minus.svg') }}" alt="" /></button>
                <button class="btn   remove-row" type="button"><img src="{{ url('settings-media/plus-sms.svg') }}" alt="" /></button>
            </td>
        </tr>
    `;

    let calculateMaterialTotal = () => {
        var total = 0;
        
        $('.row-total').each(function() {
            var val = parseFloat($(this).val()) || 0;
            total += val;
        });

        $('#grand-total-column').html(convertIntoAmount.format(total.toFixed(2)));
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
                    let qty = parseFloat(selectedElement.parent().parent().find('input.row-quantity').val()) || 1;

                    selectedElement.parent().parent().find('input.row-amount').val(parseFloat(selectedProductPrice).toFixed(2));
                    selectedElement.parent().parent().find('input.row-total').val(parseFloat(selectedProductPrice * qty).toFixed(2));
                }

                calculateMaterialTotal();
            });
        }
    }

    let initializeType = (element = null) => {
        if (element) {
            $(element).select2({
                placeholder: 'Select type',
                width: '100%',
            }).on('change', function () {
                let nextId = parseInt($(this).attr('id').replace('row-type-', '')) || 0;
                
                if (nextId >= 0) {
                    if ($('option:selected', this).val() == 'INVENTORY') {
                        $(this).parent().next().html(`
                            <select class="row-product" id="row-product-${nextId}" name="requisition[${nextId}][product]" required>
                            </select>
                        `);

                        initializeProducts(`#row-product-${nextId}`)
                    } else {
                        $(this).parent().next().html(`
                            <select class="row-vendor" id="row-vendor-${nextId}" name="requisition[${nextId}][vendor]" required>
                            </select> <br/> <br/>

                            <input class="row-product form-control" placeholder="Product Name" id="row-product-${nextId}" name="requisition[${nextId}][product]" required>
                        `);

                        initializeVendor(`#row-vendor-${nextId}`)
                    }
                }

            });
        }
    }

    let initializeVendor = (element = null) => {
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
                            expertises: function () {
                                return $('#expertise').val();
                            },
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

    $(document).on('input', '.row-amount, .row-quantity', calculateMaterialTotal);
    $(document).on('click', '.add-row, .remove-row', calculateMaterialTotal);

    $('.row-product').each(function() {
        if ($(this).is('select')) {
            initializeProducts($(this));
        }
    });
    
    $('.row-type').each(function() {
        initializeType($(this));
    });

    $('.row-vendor').each(function() {
        initializeVendor($(this));
    });

    $(document).on('click', '.add-row', function () {
        var rowCount = $('#multi-items tr').length;
        
        var row = $(itemRow);

        row.find('.row-type').attr('id', `row-type-${rowCount}`).attr('name', `requisition[${rowCount}][type]`);
        row.find('.row-product').attr('id', `row-product-${rowCount}`).attr('name', `requisition[${rowCount}][product]`);
        row.find('.row-description').attr('id', `row-description-${rowCount}`).attr('name', `requisition[${rowCount}][description]`);
        row.find('.row-quantity').attr('id', `row-quantity-${rowCount}`).attr('name', `requisition[${rowCount}][quantity]`);
        row.find('.row-amount').attr('id', `row-amount-${rowCount}`).attr('name', `requisition[${rowCount}][amount]`);
        row.find('.row-total').attr('id', `row-total-${rowCount}`).attr('name', `requisition[${rowCount}][total]`);
        row.find('.row-status').attr('id', `row-status-${rowCount}`).attr('name', `requisition[${rowCount}][status]`);
        row.find('.rejection-note').attr('id', `row-rejection-note-${rowCount}`).attr('name', `requisition[${rowCount}][rejection_note]`);
        row.find('td:eq(0)').text(rowCount + 1);

        initializeProducts(row.find('.row-product'));
        initializeType(row.find('.row-type'));

        $('#multi-items').append(row);

        $(`#row-type-${rowCount}`).rules("add", { required: true });
        $(`#row-product-${rowCount}`).rules("add", { required: true });
        $(`#row-quantity-${rowCount}`).rules("add", { required: true, number: true, min: 1 });
        $(`#row-amount-${rowCount}`).rules("add", { required: true, number: true, min: 0 });
    });

    $(document).on('change', '.row-quantity', function () {
        let quantity = parseFloat($(this).val()) || 0;
        let price = parseFloat($(this).parent().next().find('input.row-amount').val()) || 0;

        $(this).parent().next().next().find('input.row-total').val(parseFloat(quantity * price).toFixed(2));
        calculateMaterialTotal();
    });

    $(document).on('change', '.row-amount', function () {
        let price = parseFloat($(this).val()) || 0;
        let quantity = parseFloat($(this).parent().prev().find('input.row-quantity').val()) || 0;

        $(this).parent().next().find('input.row-total').val(parseFloat(quantity * price).toFixed(2));
        calculateMaterialTotal();
    });

    $(document).on('click', '.remove-row', function () {
        if ($('#multi-items tr').length > 1) {
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

    $('#job_id').select2({
        placeholder: 'Select job',
        width: '100%',
        // theme: 'classic',
        ajax: {
            url: "{{ route('job-list') }}",
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
        }
    });

    // Handle status change to show/hide rejection note
    $(document).on('change', '.row-status', function () {
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

    $('#requisitionForm').validate({
        rules: {
            job_id: { required: true }
        },
        errorPlacement: function(error, element) {
            error.appendTo(element.parent());
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
});
</script>
@endpush 