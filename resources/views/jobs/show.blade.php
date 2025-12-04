@extends( 'layouts.app', [ 'title' => $title, 'rightSideTitle' => $job->code ] )

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ $subTitle }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('jobs.index') }}">Jobs</a></li>
                        <li class="breadcrumb-item active">Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Job Information</h4>
                        <div>
                            <a href="{{ route('jobs.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Job Basic Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Job Details</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Job Code:</strong></td>
                                    <td>{{ $job->code }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Title:</strong></td>
                                    <td>{{ $job->title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($job->technicians()->count() == 0 && $job->status == 'PENDING' && in_array(auth()->user()->id, $job->engineers()->pluck('technician_id')->toArray()) && in_array('engineer', auth()->user()->roles()->pluck('name')->toArray()))
                                            <span class="badge bg-danger text-white">Techinicans to Assign</span> <br>
                                        @endif
                                        @php
                                            if ($job->status == 'PENDING') {
                                                if ($job->technicians()->count() > 0) {
                                                    echo '<span class="badge bg-warning text-dark">Assigned</span>';
                                                } else {
                                                    echo '<span class="badge bg-warning text-dark">Pending</span>';
                                                }
                                            } else if ($job->status == 'INPROGRESS') {
                                                echo '<span class="badge bg-primary">In Progress</span>';
                                            } else if ($job->status == 'ONHOLD') {
                                                echo '<span class="badge bg-secondary">On Hold</span>';
                                            } else if ($job->status == 'COMPLETED') {
                                                echo '<span class="badge bg-success-2">Job Completed</span>';
                                            } else if ($job->status == 'CANCELLED') {
                                                echo '<span class="badge bg-danger">Cancelled</span>';
                                            } else {
                                                echo '<span class="badge bg-light text-dark">Unknown</span>';
                                            }

                                            if ($job->reopened) {
                                                echo '</br><span class="badge bg-light text-dark">Reopened</span>';
                                            }
                                        @endphp
                                    </td>
                                </tr>
                                @if($job->status == 'CANCELLED')
                                    <tr>
                                        <td><strong>Cancellation Reason:</strong></td>
                                        <td>{{ $job->cancellation_note }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cancellation Fees:</strong></td>
                                        <td>{{ number_format($job->cancellation_amount, 2) }}</td>
                                    </tr>
                                @endif
                                @if($job->status == 'ONHOLD' && $job->hold_note)
                                    <tr>
                                        <td><strong>Hold Reason:</strong></td>
                                        <td>{{ $job->hold_note }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>Opening Date:</strong></td>
                                    <td>{{ $job->opening_date ? \Carbon\Carbon::parse($job->opening_date)->format('d-m-Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Visiting Date:</strong></td>
                                    <td>{{ $job->visiting_date ? \Carbon\Carbon::parse($job->visiting_date)->format('d-m-Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Visited Date:</strong></td>
                                    <td>{{ $job->visited_date ? \Carbon\Carbon::parse($job->visited_date)->format('d-m-Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Started Date:</strong></td>
                                    <td>{{ $job->opened_date ? \Carbon\Carbon::parse($job->opened_date)->format('d-m-Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Completed Date:</strong></td>
                                    <td>{{ $job->completed_at ? \Carbon\Carbon::parse($job->completed_at)->format('d-m-Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Approved By Engineer:</strong></td>
                                    <td>{{ $job->approved_by_engineer ? 'Yes' : 'No' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Approved By Billing Coordinator:</strong></td>
                                    <td>{{ $job->approved_by_billing_department ? 'Yes' : 'No' }}</td>
                                </tr>
                                @if($job->reopened)
                                <tr>
                                    <td><strong>Reopened Date:</strong></td>
                                    <td>{{ $job->reopened_date ? \Carbon\Carbon::parse($job->reopened_date)->format('d-m-Y H:i') : 'N/A' }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Deposit Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Requires Deposit:</strong></td>
                                    <td>
                                        @if($job->requires_deposit)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($job->requires_deposit)
                                <tr>
                                    <td><strong>Deposit Type:</strong></td>
                                    <td>{{ ucfirst(strtolower($job->deposit_type)) }}</td>
                                </tr>
                                <tr>
                                    <td><strong> @if($job->deposit_type == 'FIX') Deposit Amount: @else Deposit Percentage of Total Amount: @endif </strong></td>
                                    <td> @if($job->deposit_type == 'FIX') $ @endif {{ number_format($job->deposit_amount, 2) }} @if($job->deposit_type == 'PERCENT') % @endif </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Description and Summary -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">Summary & Description</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>Summary:</h6>
                                    <p class="text-muted">{{ $job->summary ?? 'No summary provided' }}</p>
                                </div>

                                <div class="col-md-12">
                                    <h6>Description:</h6>
                                    <p class="text-muted">{{ $job->description ?? 'No description provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
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
                    </div>

                    <!-- Customer Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">Customer Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Customer:</strong></td>
                                            <td>{{ $job->customer ? $job->customer->name : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Contact Name:</strong></td>
                                            <td>{{ $job->contact_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $job->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $job->contact_dial_code ? '+' . $job->contact_dial_code . ' ' . $job->contact_phone_number : $job->contact_phone_number }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Billing Name:</strong></td>
                                            <td>{{ $job->billing_name }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Assigner:</strong></td>
                                            <td>{{ $job->assigner ? $job->assigner->name : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $job->created_at->format('d-m-Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($job->signature()->count() > 0)
                    <!-- Customer Signatures -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">Customer Signature</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    @foreach ($job->signature()->get() as $signature)
                                        <a href="{{ asset('storage/signatures/' . $signature->signature) }}" target="_blank">
                                            <img src="{{ asset('storage/signatures/' . $signature->signature) }}" style="height:50px;">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Expertises -->
                    <div class="row mb-4">
                        <div class="col-12 alrt-df">
                            <h5 class="text-primary mb-3">Expertise Required</h5>
                            @if($job->expertise && $job->expertise->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($job->expertise as $index => $expertise)
                                                @if(isset($expertise->expertise->id))
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $expertise->expertise->name }}</td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> No expertise selected for this job.
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Expertises -->

                    <!-- Technicians -->
                    <div class="row mb-4">
                        <div class="col-12 alrt-df">
                            <h5 class="text-primary mb-3">Assigned Engineers</h5>
                            @if($job->engineers && $job->engineers->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($job->engineers as $index => $engineer)
                                                @if(isset($engineer->engineer->id))
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $engineer->engineer->name }}</td>
                                                    <td>{{ $engineer->engineer->email }}</td>
                                                    <td>{{ $engineer->engineer->dial_code ? '+' . $engineer->engineer->dial_code . ' ' . $engineer->engineer->phone_number : $engineer->engineer->phone_number }}</td>
                                                    <td>
                                                        @if($engineer->engineer->status)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            <h5 class="text-primary mb-3">Assigned Technicians</h5>
                            @if($job->technicians && $job->technicians->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Time Spent at Job</th>
                                                <th>Assigned By</th>
                                                <th>Assigned At</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($job->technicians as $index => $technician)
                                                @if(isset($technician->technician->id))
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $technician->technician->name }}</td>
                                                    <td>{{ $technician->technician->email }}</td>
                                                    <td>{{ $technician->technician->dial_code ? '+' . $technician->technician->dial_code . ' ' . $technician->technician->phone_number : $technician->technician->phone_number }}</td>
                                                    <td>
                                                        {{ \App\Helpers\Helper::calculateJobTotalTimeSpent($job->id, $technician->technician->id) }}
                                                    </td>
                                                    <td>
                                                        {{ isset($technician->adb->id) ? ($technician->adb->name) : 'N/A' }}
                                                    </td>
                                                    <td>
                                                        {{ isset($technician->created_at) ? (date('d-m-Y H:i', strtotime($technician->created_at))) : 'N/A' }}
                                                    </td>
                                                    <td>
                                                        @if($technician->technician->status)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="row mb-4">
                        <div class="col-12 alrt-df">
                            <h5 class="text-primary mb-3">Job Services</h5>
                            @if($job->services && $job->services->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $totalServiceAmount = 0; @endphp
                                            @foreach($job->services as $index => $service)
                                                @php $totalServiceAmount += $service->total_amount; @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $service->title }}</td>
                                                    <td>{{ $service->description ?? 'N/A' }}</td>
                                                    <td>{{ $service->quantity }}</td>
                                                    <td>${{ number_format($service->price, 2) }}</td>
                                                    <td>${{ number_format($service->total_amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-primary">
                                                <th colspan="5" class="text-end">Services Sub Total:</th>
                                                <th>${{ number_format($totalServiceAmount, 2) }}</th>
                                            </tr>
                                            <tr class="table-primary">
                                                <th colspan="5" class="text-end">Services Discount ({{ $job->services_discount_type }}):</th>
                                                <th>${{ number_format($job->services_discount_amount, 2) }}</th>
                                            </tr>
                                            <tr class="table-primary">
                                                <th colspan="5" class="text-end">Services VAT ({{ $job->services_vat_type }}):</th>
                                                <th>${{ number_format($job->services_vat_amount, 2) }}</th>
                                            </tr>
                                            <tr class="table-primary">
                                                <th colspan="5" class="text-end">Services Grand Total :</th>
                                                <th>${{ number_format(($totalServiceAmount - $job->services_discount_amount) + $job->services_vat_amount, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> No services added to this job.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Requisitions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">Job Requisitions</h5>
                            @if($job->singleRequistion)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Type</th>
                                                <th>Product/Item</th>
                                                <th>Description</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($job->singleRequistion->items as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->type }}</td>
                                                    <td>
                                                        @if($item->type == 'INVENTORY')
                                                            {{ $item->product ? $item->product->name : 'N/A' }}
                                                        @else
                                                            {{ $item->product_id }}
                                                            @if($item->vendor)
                                                                <br><small class="text-muted">Vendor: {{ $item->vendor->name ?? 'N/A' }}</small>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->description ?? 'N/A' }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>${{ number_format($item->amount, 2) }}</td>
                                                    <td>${{ number_format($item->total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-primary">
                                                <th colspan="6" class="text-end">Requisition Total:</th>
                                                <th>${{ number_format($job->singleRequistion->items->sum('total'), 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info p-2">
                                    <i class="fa fa-info-circle me-3"></i> No requisitions found for this job.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Job Inspection Statuses -->
                    <div class="row mb-4">
                        <div class="col-12 alrt-df">
                            <h5 class="text-primary mb-3">Job Inspection Statuses</h5>
                            @if($job->inspectionLogs && $job->inspectionLogs->count() > 0)
                                <!-- Summary of Latest Status by Department -->
                                @if($expectedDepartments && $expectedDepartments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Department</th>
                                                    <th>Latest Status</th>
                                                    <th>Last Inspected By</th>
                                                    <th>Last Inspection Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $latestInspections = $job->inspectionLogs->groupBy('department_id')->map(function($inspections) {
                                                        return $inspections->sortByDesc('created_at')->first();
                                                    });
                                                    $departmentInspections = $job->inspectionLogs->groupBy('department_id');
                                                @endphp
                                                @foreach($expectedDepartments as $index => $department)
                                                    @php
                                                        $latestInspection = $latestInspections->get($department->id);
                                                        $departmentInspectionCount = $departmentInspections->get($department->id) ? $departmentInspections->get($department->id)->count() : 0;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $department->name }}</td>
                                                        <td>
                                                            @if($latestInspection)
                                                                @if($latestInspection->status == 'APPROVED')
                                                                    <span class="badge bg-success">Approved</span>
                                                                @elseif($latestInspection->status == 'REJECTED')
                                                                    <span class="badge bg-danger">Rejected</span>
                                                                @else
                                                                    <span class="badge bg-secondary">{{ $latestInspection->status }}</span>
                                                                @endif
                                                            @else
                                                                <span class="badge bg-warning">Pending</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($latestInspection && $latestInspection->inspectedBy)
                                                                {{ $latestInspection->inspectedBy->name }}
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($latestInspection)
                                                                {{ $latestInspection->created_at->format('d-m-Y H:i') }}
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($departmentInspectionCount > 0)
                                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#inspectionHistoryModal{{ $department->id }}">
                                                                    <i class="fa fa-history"></i> View History ({{ $departmentInspectionCount }})
                                                                </button>
                                                            @else
                                                                <span class="text-muted">No inspections</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <!-- Fallback: Show all inspections if no expected departments -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Department</th>
                                                    <th>Inspected By</th>
                                                    <th>Status</th>
                                                    <th>Details</th>
                                                    <th>Date & Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($job->inspectionLogs->sortBy('created_at') as $index => $inspection)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $inspection->department ? $inspection->department->name : 'N/A' }}</td>
                                                        <td>{{ $inspection->inspectedBy ? $inspection->inspectedBy->name : 'N/A' }}</td>
                                                        <td>
                                                            @if($inspection->status == 'APPROVED')
                                                                <span class="badge bg-success">Approved</span>
                                                            @elseif($inspection->status == 'REJECTED')
                                                                <span class="badge bg-danger">Rejected</span>
                                                            @else
                                                                <span class="badge bg-secondary">{{ $inspection->status }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $inspection->description ?? 'No details provided' }}</td>
                                                        <td>{{ $inspection->created_at->format('d-m-Y H:i') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> No inspection records found for this job.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Inspection History Modals -->
                    @if($expectedDepartments && $expectedDepartments->count() > 0)
                        @php
                            $departmentInspections = $job->inspectionLogs->groupBy('department_id');
                        @endphp
                        @foreach($expectedDepartments as $department)
                            @php
                                $inspections = $departmentInspections->get($department->id);
                            @endphp
                            @if($inspections && $inspections->count() > 0)
                                <div class="modal fade" id="inspectionHistoryModal{{ $department->id }}" tabindex="-1" aria-labelledby="inspectionHistoryModalLabel{{ $department->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="inspectionHistoryModalLabel{{ $department->id }}">
                                                    Inspection History - {{ $department->name }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Inspected By</th>
                                                                <th>Status</th>
                                                                <th>Details</th>
                                                                <th>Date & Time</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($inspections->sortBy('created_at') as $index => $inspection)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ $inspection->inspectedBy ? $inspection->inspectedBy->name : 'N/A' }}</td>
                                                                    <td>
                                                                        @if($inspection->status == 'APPROVED')
                                                                            <span class="badge bg-success">Approved</span>
                                                                        @elseif($inspection->status == 'REJECTED')
                                                                            <span class="badge bg-danger">Rejected</span>
                                                                        @else
                                                                            <span class="badge bg-secondary">{{ $inspection->status }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $inspection->description ?? 'No details provided' }}</td>
                                                                    <td>{{ $inspection->created_at->format('d-m-Y H:i') }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif

                    <!-- Invoice Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Invoice Management</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="me-3">
                                                    @if($job->is_invoice_generated)
                                                        <i class="fa fa-check-circle text-success" style="font-size: 24px;"></i>
                                                    @else
                                                        <i class="fa fa-times-circle text-warning" style="font-size: 24px;"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <strong>Invoice Status:</strong>
                                                    @if($job->is_invoice_generated)
                                                        <span class="badge bg-success">Generated</span>
                                                        <br><small class="text-muted">Invoice #: {{ $job->invoice_number }}</small>
                                                        <br><small class="text-muted">Generated on: {{ $job->invoice_generated_at ? \Carbon\Carbon::parse($job->invoice_generated_at)->format('d-m-Y H:i') : 'N/A' }}</small>
                                                    @else
                                                        <span class="badge bg-warning">Not Generated</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex gap-2">
                                                @if(!$job->is_invoice_generated)
                                                    <a href="{{ route('jobs.generate-invoice', encrypt($job->id)) }}" class="btn btn-primary">
                                                        <i class="fa fa-file-pdf-o"></i> Generate Invoice
                                                    </a>
                                                @else
                                                    <a href="{{ route('jobs.download-invoice', encrypt($job->id)) }}" class="btn btn-success">
                                                        <i class="fa fa-download"></i> Download Invoice
                                                    </a>
                                                    <a href="{{ route('jobs.generate-invoice', encrypt($job->id)) }}" class="btn btn-warning">
                                                        <i class="fa fa-refresh"></i> Regenerate Invoice
                                                    </a>
                                                @endif
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
</div>
@endsection 