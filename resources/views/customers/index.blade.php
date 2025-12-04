@extends('layouts.app',['title' => $title, 'subTitle' => $subTitle,'datatable' => true, 'select2' => true, 'rightButton' => [
        'title'  => 'Add New Customer',
        'link' => route('customers.create')
    ]])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="row">
            @include('filters.user-management')
        </div>
        <div class="card">
            <div class="card-header">
                @if(auth()->user()->can('customers.create'))
                {{-- <a href="{{ route('customers.create') }}" class="btn btn-primary float-end">
                    <i class="fa fa-plus"></i> Add New Customer
                </a> --}}
                @endif


                <button class="btn btn-outline-secondary me-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel" aria-expanded="false" aria-controls="filterPanel">
                    <i class="fa fa-filter"></i> Filter
                </button>
            </div>
            <div class="card-body">
                <table id="datatables-reponsive" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function () {

    let dataTable = $('#datatables-reponsive').DataTable({
        pageLength : 10,
        searching: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route(Request::route()->getName()) }}",
            type: "GET",
            data: {
                filter_status:function() {
                    return $("#filter-status").val();
                },
                filter_country:function() {
                    return $("#filter-country").val();
                },
                filter_state:function() {
                    return $("#filter-state").val();
                },
                filter_city:function() {
                    return $("#filter-city").val();
                },
                filter_name: function () {
                    return $('#filter-name').val();
                }
            }
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name' },
            { data: 'email' },
            { data: 'alternate_phone_number' },
            { data: 'status' },
            { data: 'action', orderable: false, searchable: false }
        ],
    });

        $('#filter-status').select2({
            placeholder: 'Select status',
            width: '100%'
        });

        $('#filter-country').select2({
            allowClear: true,
            placeholder: 'Select country',
            width: '100%',
            ajax: {
                url: "{{ route('country-list') }}",
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
            }
        });

        $('#filter-state').select2({
            allowClear: true,
            placeholder: 'Select state',
            width: '100%',
            ajax: {
                url: "{{ route('state-list') }}",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        searchQuery: params.term,
                        page: params.page || 1,
                        country_id: $('#filter-country').val(),
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
            }
        });

        $('#filter-city').select2({
            allowClear: true,
            placeholder: 'Select city',
            width: '100%',
            ajax: {
                url: "{{ route('city-list') }}",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        searchQuery: params.term,
                        page: params.page || 1,
                        state_id: $('#filter-state').val(),
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
            }
        });

        $('#btn-search').on('click', function () {
            dataTable.ajax.reload();
        });

        $('#btn-clear').on('click', function () {
            $('#filter-status').val(null).trigger('change');
            $('#filter-country').val(null).trigger('change');
            $('#filter-state').val(null).trigger('change');
            $('#filter-city').val(null).trigger('change');
            $('#filter-name').val(null);

            dataTable.ajax.reload();
        });

    $(document).on('click', '#deleteRow', function () {
        let url = $(this).data('row-route');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
           confirmButtonColor: '#286c99',
            cancelButtonColor: '#888',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Deleted!', response.success, 'success');
                            dataTable.ajax.reload();
                        } else if (response.error) {
                            Swal.fire('Error', response.error, 'error');
                        }
                    },
                    error: function (xhr) {
                        let msg = 'An error occurred.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            msg = xhr.responseJSON.error;
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush 