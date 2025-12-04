<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $job->invoice_number ?? 'N/A' }}</title>
    <style>
        @page {
            margin: 20px 25px 20px 25px;
        }
        
        body {
            margin: 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }
        
        .container {
            width: 100%!important;
        }
        
        /* Header Styles */
        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid #06429d;
            padding-bottom: 15px;
        }
        
        .header-content {
            display: table;
            width: 100%;
        }
        
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        
        .company-logo {
            max-width: 150px;
            max-height: 60px;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #06429d;
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 10px;
            color: #666;
            line-height: 1.6;
        }
        
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #06429d;
            margin-bottom: 5px;
        }
        
        .invoice-meta {
            font-size: 10px;
            color: #666;
        }
        
        /* Info Boxes */
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-boxes {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-box {
            display: table-cell;
            width: 48%;
            padding: 12px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }
        
        .info-box:first-child {
            margin-right: 4%;
        }
        
        .info-box-title {
            font-size: 12px;
            font-weight: bold;
            color: #06429d;
            margin-bottom: 8px;
            border-bottom: 1px solid #06429d;
            padding-bottom: 4px;
        }
        
        .info-row {
            margin-bottom: 4px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        
        .info-value {
            display: inline-block;
        }
        
        /* Badge Styles */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 3px;
            color: #fff;
        }
        
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-danger { background-color: #dc3545; }
        .badge-primary { background-color: #007bff; }
        .badge-secondary { background-color: #6c757d; }
        .badge-info { background-color: #17a2b8; }
        
        /* Section Titles */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #06429d;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #06429d;
        }
        
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table.data-table {
            border: 1px solid #dee2e6;
        }
        
        table.data-table th {
            background-color: #06429d;
            color: #fff;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }
        
        table.data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #dee2e6;
            font-size: 10px;
        }
        
        table.data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        table.data-table tfoot td {
            font-weight: bold;
            background-color: #e9ecef;
            border-top: 2px solid #06429d;
        }
        
        table.simple-table td {
            padding: 4px 0;
            font-size: 10px;
        }
        
        table.simple-table td:first-child {
            font-weight: bold;
            width: 150px;
        }
        
        /* Summary Box */
        .summary-box {
            background: #f8f9fa;
            padding: 12px;
            border: 1px solid #dee2e6;
            margin-bottom: 15px;
        }
        
        .summary-title {
            font-weight: bold;
            color: #06429d;
            margin-bottom: 5px;
        }
        
        .summary-text {
            font-size: 10px;
            color: #666;
            line-height: 1.6;
        }
        
        /* Financial Summary */
        .financial-summary {
            width: 50%;
            margin-left: auto;
            margin-top: 20px;
        }
        
        .financial-row {
            display: table;
            width: 100%;
            padding: 5px 0;
        }
        
        .financial-label {
            display: table-cell;
            text-align: right;
            padding-right: 15px;
            font-weight: bold;
        }
        
        .financial-value {
            display: table-cell;
            text-align: right;
            width: 120px;
        }
        
        .financial-total {
            border-top: 2px solid #06429d;
            margin-top: 10px;
            padding-top: 10px;
            font-size: 13px;
            font-weight: bold;
            color: #06429d;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
            font-size: 9px;
            color: #666;
            text-align: center;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        /* Text Utilities */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-muted { color: #6c757d; }
        .mb-10 { margin-bottom: 10px; }
        .mb-15 { margin-bottom: 15px; }
        .mt-20 { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header Section --}}
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    @if(!empty($setting->logo) && file_exists(public_path('settings-media/' . $setting->logo)))
                        <img src="{{ public_path('settings-media/' . $setting->logo) }}" alt="Company Logo" class="company-logo">
                    @endif
                    <!-- <div class="company-name">{{ $setting->name ?? 'DMS' }}</div> -->
                    <div class="company-details">
                        @if(!empty($setting->address))
                            {{ $setting->address }}<br>
                        @endif
                        @if(!empty($setting->email))
                            Email: {{ $setting->email }}<br>
                        @endif
                        @if(!empty($setting->phone))
                            Phone: {{ $setting->phone }}
                        @endif
                    </div>
                </div>
                <div class="header-right">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-meta">
                        <strong>Invoice #:</strong> {{ $job->invoice_number ?? 'N/A' }}<br>
                        <strong>Job Code:</strong> {{ $job->code }}<br>
                        <strong>Date:</strong> {{ $job->invoice_generated_at ? \Carbon\Carbon::parse($job->invoice_generated_at)->format('d-m-Y') : date('d-m-Y') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer and Company Info --}}
        <div class="info-boxes">
            <div class="info-box">
                <div class="info-box-title">Bill To</div>
                <table class="simple-table">
                    <tr>
                        <td>Customer:</td>
                        <td>{{ $job->customer ? $job->customer->name : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Contact Name:</td>
                        <td>{{ $job->contact_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td>{{ $job->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Phone:</td>
                        <td>{{ $job->contact_dial_code ? '+' . $job->contact_dial_code . ' ' . $job->contact_phone_number : ($job->contact_phone_number ?? 'N/A') }}</td>
                    </tr>
                    <tr>
                        <td>Billing Name:</td>
                        <td>{{ $job->billing_name ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <div class="info-box">
                <div class="info-box-title">Job Information</div>
                <table class="simple-table">
                    <tr>
                        <td>Job Title:</td>
                        <td>{{ $job->title }}</td>
                    </tr>
                    <tr>
                        <td>Status:</td>
                        <td>
                            @php
                                $statusBadge = '';
                                if ($job->status == 'PENDING') {
                                    $statusBadge = '<span class="badge badge-warning">Pending</span>';
                                } elseif ($job->status == 'INPROGRESS') {
                                    $statusBadge = '<span class="badge badge-primary">In Progress</span>';
                                } elseif ($job->status == 'ONHOLD') {
                                    $statusBadge = '<span class="badge badge-secondary">On Hold</span>';
                                } elseif ($job->status == 'COMPLETED') {
                                    $statusBadge = '<span class="badge badge-success">Completed</span>';
                                } elseif ($job->status == 'CANCELLED') {
                                    $statusBadge = '<span class="badge badge-danger">Cancelled</span>';
                                }
                                echo $statusBadge;
                            @endphp
                        </td>
                    </tr>
                    <tr>
                        <td>Opening Date:</td>
                        <td>{{ $job->opening_date ? \Carbon\Carbon::parse($job->opening_date)->format('d-m-Y H:i') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Visiting Date:</td>
                        <td>{{ $job->visiting_date ? \Carbon\Carbon::parse($job->visiting_date)->format('d-m-Y H:i') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Completed Date:</td>
                        <td>{{ $job->completed_at ? \Carbon\Carbon::parse($job->completed_at)->format('d-m-Y H:i') : 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Job Summary --}}
        @if($job->summary || $job->description)
        <div class="summary-box">
            @if($job->summary)
            <div class="summary-title">Summary:</div>
            <div class="summary-text mb-10">{{ $job->summary }}</div>
            @endif
            
            @if($job->description)
            <div class="summary-title">Description:</div>
            <div class="summary-text">{{ $job->description }}</div>
            @endif
        </div>
        @endif

        {{-- Assigned Personnel --}}
        @if(($job->engineers && $job->engineers->count() > 0) || ($job->technicians && $job->technicians->count() > 0))
        <div class="section-title">Assigned Personnel</div>
        
        @if($job->engineers && $job->engineers->count() > 0)
        <strong style="font-size: 11px; display: block; margin: 10px 0 5px 0;">Engineers:</strong>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="30%">Name</th>
                    <th width="35%">Email</th>
                    <th width="30%">Phone</th>
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
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        @endif

        @if($job->technicians && $job->technicians->count() > 0)
        <strong style="font-size: 11px; display: block; margin: 10px 0 5px 0;">Technicians:</strong>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="25%">Name</th>
                    <th width="25%">Email</th>
                    <th width="20%">Phone</th>
                    <th width="15%">Time Spent</th>
                    <th width="10%">Assigned At</th>
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
                        <td>{{ \App\Helpers\Helper::calculateJobTotalTimeSpent($job->id, $technician->technician->id) }}</td>
                        <td>{{ isset($technician->created_at) ? date('d-m-Y', strtotime($technician->created_at)) : 'N/A' }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        @endif
        @endif

        {{-- Services Section --}}
        @if($job->services && $job->services->count() > 0)
        <div class="section-title">Services</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="30%">Title</th>
                    <th width="30%">Description</th>
                    <th width="10%">Quantity</th>
                    <th width="12%">Unit Price</th>
                    <th width="13%">Total Amount</th>
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
                        <td class="text-center">{{ $service->quantity }}</td>
                        <td class="text-right">${{ number_format($service->price, 2) }}</td>
                        <td class="text-right">${{ number_format($service->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right">Services Sub Total:</td>
                    <td class="text-right">${{ number_format($totalServiceAmount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right">Services Discount ({{ $job->services_discount_type }}):</td>
                    <td class="text-right">-${{ number_format($job->services_discount_amount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right">Services VAT ({{ $job->services_vat_type }}):</td>
                    <td class="text-right">${{ number_format($job->services_vat_amount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right"><strong>Services Grand Total:</strong></td>
                    <td class="text-right"><strong>${{ number_format(($totalServiceAmount - $job->services_discount_amount) + $job->services_vat_amount, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
        @endif

        {{-- Requisitions Section --}}
        @if($job->singleRequistion && $job->singleRequistion->items && $job->singleRequistion->items->count() > 0)
        <div class="section-title">Requisitions</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="10%">Type</th>
                    <th width="30%">Product/Item</th>
                    <th width="30%">Description</th>
                    <th width="10%">Quantity</th>
                    <th width="12%">Unit Price</th>
                    <th width="13%">Total Amount</th>
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
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">${{ number_format($item->amount, 2) }}</td>
                        <td class="text-right">${{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right"><strong>Requisition Total:</strong></td>
                    <td class="text-right"><strong>${{ number_format($job->singleRequistion->items->sum('total'), 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
        @endif

        {{-- Deposit Information --}}
        @if($job->requires_deposit)
        <div class="section-title">Deposit Information</div>
        <div class="info-box" style="width: 100%; margin-bottom: 15px;">
            <table class="simple-table">
                <tr>
                    <td>Deposit Required:</td>
                    <td><span class="badge badge-success">Yes</span></td>
                </tr>
                <tr>
                    <td>Deposit Type:</td>
                    <td>{{ ucfirst(strtolower($job->deposit_type)) }}</td>
                </tr>
                <tr>
                    <td>Deposit Amount:</td>
                    <td>
                        @if($job->deposit_type == 'FIX')
                            ${{ number_format($job->deposit_amount, 2) }}
                        @else
                            {{ number_format($job->deposit_amount, 2) }}%
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        @endif

        {{-- Cancellation Information --}}
        @if($job->status == 'CANCELLED')
        <div class="section-title">Cancellation Information</div>
        <div class="info-box" style="width: 100%; margin-bottom: 15px;">
            <table class="simple-table">
                <tr>
                    <td>Cancellation Reason:</td>
                    <td>{{ $job->cancellation_note ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Cancellation Fees:</td>
                    <td>${{ number_format($job->cancellation_amount, 2) }}</td>
                </tr>
            </table>
        </div>
        @endif

        {{-- Financial Summary --}}
        <div class="financial-summary">
            @php
                $servicesTotal = 0;
                if($job->services && $job->services->count() > 0) {
                    $totalServiceAmount = $job->services->sum('total_amount');
                    $servicesTotal = ($totalServiceAmount - $job->services_discount_amount) + $job->services_vat_amount;
                }
                
                $requisitionsTotal = 0;
                if($job->singleRequistion && $job->singleRequistion->items) {
                    $requisitionsTotal = $job->singleRequistion->items->sum('total');
                }
                
                $depositAmount = 0;
                if($job->requires_deposit && $job->deposit_type == 'FIX') {
                    $depositAmount = $job->deposit_amount;
                } elseif($job->requires_deposit && $job->deposit_type == 'PERCENT') {
                    $depositAmount = ($servicesTotal + $requisitionsTotal) * ($job->deposit_amount / 100);
                }
                
                $cancellationFees = 0;
                if($job->status == 'CANCELLED') {
                    $cancellationFees = $job->cancellation_amount ?? 0;
                }
                
                $grandTotal = $servicesTotal + $requisitionsTotal + $cancellationFees;
            @endphp
            
            @if($servicesTotal > 0)
            <div class="financial-row">
                <div class="financial-label">Services Total:</div>
                <div class="financial-value">${{ number_format($servicesTotal, 2) }}</div>
            </div>
            @endif
            
            @if($requisitionsTotal > 0)
            <div class="financial-row">
                <div class="financial-label">Requisitions Total:</div>
                <div class="financial-value">${{ number_format($requisitionsTotal, 2) }}</div>
            </div>
            @endif
            
            @if($depositAmount > 0)
            <div class="financial-row">
                <div class="financial-label">Deposit Amount:</div>
                <div class="financial-value">${{ number_format($depositAmount, 2) }}</div>
            </div>
            @endif
            
            @if($cancellationFees > 0)
            <div class="financial-row">
                <div class="financial-label">Cancellation Fees:</div>
                <div class="financial-value">${{ number_format($cancellationFees, 2) }}</div>
            </div>
            @endif
            
            <div class="financial-row financial-total">
                <div class="financial-label">GRAND TOTAL:</div>
                <div class="financial-value">${{ number_format($grandTotal, 2) }}</div>
            </div>
        </div>

    </div>
</body>
</html>
