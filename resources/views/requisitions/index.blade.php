@extends('layouts.app', ['title' => $title, 'subTitle' => $subTitle,'datatable' => true, 'select2' => true,'rightButton' => [
        'title'  => 'Add New Requisition',
        'link' => route('requisitions.create')
    ]])

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
<style>
    #requisitions-table tbody td .badge.bg-warning {
        background: #D8C03B;
        border-color: #D8C03B;
        padding: 5px 15px;
        border-radius: 7px;
        font-size: 16px;
        margin-right: 10px;
    }
    #requisitions-table tbody td .btn.btn-sm.btn-success.open-approve-popup {
        padding: 5px 15px;
        border-radius: 7px;
        font-size: 16px;
        margin-right: 10px;
        border-color: #00cc99;
    }
    #requisitions-table tbody td .btn.btn-sm.btn-danger.open-reject-popup {
        background: #888;
        border-color: #888;
        border-radius: 10px;
        padding: 10px 15px;
        font-size: 18px;
        line-height: 18px;
        box-shadow: none;
    }
    #requisitions-table tbody td .btn.btn-sm.btn-danger.open-reject-popup .fa.fa-times {
        color: #fff;
        margin-right: 4px;
        vertical-align: bottom;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="row">
            @include('filters.work-management')
        </div>
        <div class="card">
            <div class="card-header">
                @if(auth()->user()->can('requisitions.create'))
                    {{-- <a href="{{ route('requisitions.create') }}" class="btn btn-primary float-end">
                        <i class="fa fa-plus"></i> Add New Requisition
                    </a> --}}
                @endif

                <button class="btn btn-outline-secondary me-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel" aria-expanded="false" aria-controls="filterPanel">
                    <i class="fa fa-filter"></i> Filter
                </button>
            </div>
            <div class="card-body">
                <table id="requisitions-table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Job Code</th>
                            <th>Requisition Code</th>
                            <th>Total</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script>
$(document).ready(function() {

    var dataTable = $('#requisitions-table').DataTable({
        pageLength : 10,
        searching: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route(Request::route()->getName()) }}",
            data: {
                filter_status : function () {
                    return $('#filter-status').val();
                },
                filter_job : function () {
                    return $('#filter-job').val()
                },
                filter_addedby : function () {
                    return $('#filter-addedby').val()
                },
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'job_code', name: 'job_code', orderable: false, searchable: false},
            {data: 'code', name: 'code', orderable: false, searchable: false},
            {data: 'total', name: 'total', orderable: false, searchable: false},
            {data: 'added_by_name', name: 'added_by_name', orderable: false, searchable: false},
            {data: 'created_at', name: 'created_at', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });

    $('#filter-job').select2({
        allowClear: true,
        placeholder: 'Select job',
        width: '100%',
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

    $('#filter-addedby').select2({
        placeholder: 'Select added by',
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
                    roles: '*'
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

    $(document).on('click', '.open-approve-popup', function () {
        var route = $(this).data('route');

        Swal.fire({
            title: 'Approve Requisition',
            input: 'textarea',
            inputPlaceholder: 'Enter your remark here...',
            showCancelButton: true,
            confirmButtonText: 'Approve',
            cancelButtonText: 'Cancel',
            preConfirm: (remark) => {
                return $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        remark: remark,
                        status: 'APPROVED'
                    }
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Approved!', result.value.message, 'success');
                dataTable.ajax.reload();
            }
        });
    });

    $(document).on('click', '.open-reject-popup', function () {
        var route = $(this).data('route');

        Swal.fire({
            title: 'Reject Requisition',
            input: 'textarea',
            inputPlaceholder: 'Enter your remark here...',
            showCancelButton: true,
            confirmButtonText: 'Reject',
            cancelButtonText: 'Cancel',
            preConfirm: (remark) => {
                return $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        remark: remark,
                        status: 'REJECTED'
                    }
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Rejected!', result.value.message, 'success');
                dataTable.ajax.reload();
            }
        });
    });

    $('#filter-status').select2({
        placeholder: 'Select status',
        width: '100%'
    });

    $('#btn-search').on('click', function () {
        dataTable.ajax.reload();
    });

    $('#btn-clear').on('click', function () {
        $('#filter-status').val(null).trigger('change');
        $('#filter-job').val(null).trigger('change');
        $('#filter-addedby').val(null).trigger('change');

        dataTable.ajax.reload();
    });

    $(document).on('click', '#deleteRow', function() {
        var route = $(this).data('row-route');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#286c99',
            cancelButtonColor: '#888',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: route,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                response.success,
                                'success'
                            );
                            dataTable.draw();
                        } else {
                            Swal.fire(
                                'Error!',
                                response.error,
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Something went wrong!';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }
                        Swal.fire(
                            'Error!',
                            errorMessage,
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
@endpush 