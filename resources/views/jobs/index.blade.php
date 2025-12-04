@extends('layouts.app',['title' => $title, 'subTitle' => $subTitle,'datatable' => true, 'select2' => true, 'datepicker' => true,'rightButton' => [
        'title'  => 'Add New Job',
        'link' => route('jobs.create')
    ]])

@section('content')

<div class="row">
    <div class="col-12">
        <div class="row">
            @include('filters.work-management')
        </div>
        <div class="card">
            <div class="card-header">
                {{-- Filters --}}
                @if(auth()->user()->can('jobs.create'))
                {{-- <a href="{{ route('jobs.create') }}" class="btn btn-primary float-end">
                    <i class="fa fa-plus"></i> Add New Job 
                </a> --}}
                @endif
                {{-- Filters --}}

                <button class="btn btn-outline-secondary me-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel" aria-expanded="false" aria-controls="filterPanel">
                    <i class="fa fa-filter"></i> Filter
                </button>
                
            </div>
            <div class="card-body">
                <table id="datatables-reponsive" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Time Spent</th>
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
                "url": "{{ route(Request::route()->getName()) }}",
                "type": "GET",
                "data" : {
                    filter_status:function() {
                        return $("#filter-status").val();
                    },
                    filter_customer:function() {
                        return $("#filter-customer").val();
                    },
                    filter_technicians:function() {
                        return $("#filter-technicians").val();
                    },
                    filter_engineers:function() {
                        return $("#filter-engineers").val();
                    },
                    filter_depreq:function() {
                        return $("#filter-deposit-requirement").val();
                    },
                    filter_invgen:function() {
                        return $("#filter-invoice-generation").val();
                    },
                    filter_addedby:function() {
                        return $("#filter-addedby").val();
                    },
                    filter_expertise:function() {
                        return $("#filter-expertise").val();
                    },
                    filter_visiting_date_from:function() {
                        return $("#visiting_date_from").val();
                    },
                    filter_visiting_date_to:function() {
                        return $("#visiting_date_to").val();
                    }
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'code', orderable: false, searchable: false },
                { data: 'title', orderable: false, searchable: false },
                { data: 'status', orderable: false, searchable: false },
                { data: 'time_spent', orderable: false, searchable: false },
                { data: 'action', orderable: false, searchable: false }
            ]
        });

        $('#filter-customer').select2({
            placeholder: 'Select customer',
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
                        roles: ['customer']
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

        $('#filter-technicians').select2({
            placeholder: 'Select technicians',
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
                        roles: ['technician']
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

        $('#filter-engineers').select2({
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
        });

        $('#filter-expertise').select2({
            placeholder: 'Select expertise',
            allowClear: true,
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

        $('#visiting_date_from, #visiting_date_to').datepicker({
            format: "{{ DATE_FORMAT_DATEPICKER_DISPLAY }}",
            dateFormat: "{{ DATE_FORMAT_DATEPICKER_FORMAT }}",
            autoclose: true,
            todayHighlight: true
        });

        $('#btn-search').on('click', function () {
            dataTable.ajax.reload();
        });

        $('#btn-clear').on('click', function () {
            $('#filter-status').val(null).trigger('change');
            $('#filter-customer').val(null).trigger('change');
            $('#filter-technicians').val(null).trigger('change');
            $('#filter-engineers').val(null).trigger('change');
            $('#filter-addedby').val(null).trigger('change');
            $('#filter-expertise').val(null).trigger('change');
            $('#filter-deposit-requirement').val(null).trigger('change');
            $('#filter-invoice-generation').val(null).trigger('change');
            $('#visiting_date_from').val('');
            $('#visiting_date_to').val('');

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
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
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

        $(document).on('click', '.reschedule-btn', function () {
            let url = $(this).data('url');
            Swal.fire({
                title: 'Reschedule Job',
                html:
                    '<input id="swal-datepicker" class="form-control mb-3" placeholder="Select date">' +
                    '<textarea id="swal-reason" class="form-control" placeholder="Reason"></textarea>',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                didOpen: () => {
                    $('#swal-datepicker').datepicker({
                        format: 'dd-mm-yyyy', 
                        autoclose: true,
                        dateFormat: 'dd-mm-yy',
                        todayHighlight: true
                    });
                },
                preConfirm: () => {
                    const date = $('#swal-datepicker').val();
                    const reason = $('#swal-reason').val();
                    if (!date || !reason) {
                        Swal.showValidationMessage('Please select a date and enter a reason');
                        return false;
                    }
                    return { date, reason };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            reschedule_date: result.value.date,
                            reason: result.value.reason
                        },
                        success: function (response) {
                            if (response.status) {
                                Swal.fire('Success', response.message, 'success');
                                $('#datatables-reponsive').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function (xhr) {
                            Swal.fire('Error', 'Failed to reschedule job', 'error');
                        }
                    });
                }
            });
        });

        $(document).on('change', '.change-status', function () {
            let select = $(this);
            let url = select.data('url');
            let newStatus = select.val();
            let oldStatus = select.data('old') || select.find('option:selected').val();
            
            if (newStatus === oldStatus) return;

            Swal.fire({
                title: 'Are you sure?',
                text: `Change status from ${oldStatus} to ${newStatus}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (newStatus === 'CANCELLED' && oldStatus !== 'CANCELLED') {
                        Swal.fire({
                            title: 'Cancel Job',
                            html:
                                '<input id="cancel-amount" type="number" class="form-control mb-3" placeholder="Amount">' +
                                '<textarea id="cancel-note" class="form-control" placeholder="Description note"></textarea>',
                            showCancelButton: true,
                            confirmButtonText: 'Submit',
                            preConfirm: () => {
                                const amount = $('#cancel-amount').val();
                                const note = $('#cancel-note').val();
                                if (!amount || !note) {
                                    Swal.showValidationMessage('Amount and Note are required');
                                    return false;
                                }
                                return { amount, note };
                            }
                        }).then((cancelResult) => {
                            if (cancelResult.isConfirmed) {
                                updateStatus(url, newStatus, cancelResult.value.amount, cancelResult.value.note, null, select, oldStatus);
                            } else {
                                select.val(oldStatus);
                            }
                        });
                    } else if (newStatus === 'ONHOLD' && oldStatus !== 'ONHOLD') {
                        Swal.fire({
                            title: 'Hold Job',
                            html:
                                '<textarea id="hold-note" class="form-control" placeholder="Reason for holding the job" rows="4"></textarea>',
                            showCancelButton: true,
                            confirmButtonText: 'Submit',
                            preConfirm: () => {
                                const note = $('#hold-note').val();
                                if (!note) {
                                    Swal.showValidationMessage('Hold note is required');
                                    return false;
                                }
                                return { note };
                            }
                        }).then((holdResult) => {
                            if (holdResult.isConfirmed) {
                                updateStatus(url, newStatus, null, null, holdResult.value.note, select, oldStatus);
                            } else {
                                select.val(oldStatus);
                            }
                        });
                    } else {
                        updateStatus(url, newStatus, null, null, null, select, oldStatus);
                    }
                } else {
                    select.val(oldStatus);
                }
            });
        });

        function updateStatus(url, status, amount, cancelNote, holdNote, select, oldStatus) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status,
                    cancel_amount: amount,
                    cancel_note: cancelNote,
                    hold_note: holdNote
                },
                success: function (response) {
                    if (response.status) {
                        Swal.fire('Success', response.message, 'success');
                        select.data('old', status);
                        $('#datatables-reponsive').DataTable().ajax.reload(null, false);
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Failed to update status', 'error');
                    select.val(oldStatus);
                }
            });
        }

        $(document).on('click', '.approve-btn-placeholder', function() {
            let jobId = $(this).data('job-id');
            Swal.fire({
                title: 'Department Approval',
                html: `<select id="swal-status" class="form-select">
                            <option value="APPROVED">Approve</option>
                            <option value="REJECTED">Reject</option>
                       </select>
                       <textarea id="swal-description" class="form-control mt-2" placeholder="Enter remarks (optional)"></textarea>`,
                showCancelButton: true,
                confirmButtonText: 'Submit',
                preConfirm: () => {
                    return {
                        status: $('#swal-status').val(),
                        description: $('#swal-description').val()
                    }
                }
            }).then((result) => {
                if(result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('jobs/approve') }}" + '/' + jobId,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: result.value.status,
                            description: result.value.description
                        },
                        success: function(res) {
                            if(res.status) {
                                Swal.fire('Success', res.message, 'success');
                                dataTable.ajax.reload();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Something went wrong!', 'error');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.dms_send_workshop_btn', function() {
            var $this = $(this);
            var job_id = $this.data('job_id');

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to send to the workshop?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('jobs.ajax.common') }}",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: '{{ csrf_token() }}',
                            dms_action: 'job_send_to_workshop',
                            job_id
                        },
                        success: function (response) {
                            if (response.status) {
                                $this
                                    .removeClass(['btn-primary', 'dms_send_workshop_btn'])
                                    .addClass(['btn-success', 'dms_sent_workshop_btn'])
                                    .text('Sent to Workshop');
                                Swal.fire('Success', response.message, 'success');
                            } else {
                                Swal.fire('Error', response.message || 'Something went wrong!', 'error');
                            }
                        },
                        error: function (xhr, status, error) {
                            var message = xhr.responseJSON?.message || 'Something went wrong!';
                            Swal.fire('Error', message, 'error');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.dms_approve_by_e', function() {
            var $this = $(this);
            var job_id = $this.data('jid');
            var theJobType = $this.data('thetype');

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to approve this job?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('jobs.ajax.common') }}",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: '{{ csrf_token() }}',
                            approve_type: theJobType,
                            dms_action: 'approve_by_engineer',
                            job_id
                        },
                        success: function (response) {
                            if (response.status) {
                                $this.remove();
                                Swal.fire('Success', response.message, 'success');
                            } else {
                                Swal.fire('Error', response.message || 'Something went wrong!', 'error');
                            }
                        },
                        error: function (xhr, status, error) {
                            var message = xhr.responseJSON?.message || 'Something went wrong!';
                            Swal.fire('Error', message, 'error');
                        }
                    });
                }
            });
        });

    });
</script>
@endpush