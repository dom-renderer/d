<?php

namespace App\Http\Controllers;

use App\Models\JobInspectionOrdering;
use App\Models\JobRescheduleRequest;
use App\Models\JobInspectedLog;
use Illuminate\Support\Facades\DB;
use App\Models\JobTechnician;
use App\Models\JobExpertise;
use Illuminate\Http\Request;
use App\Models\JobMaterial;
use App\Models\JobService;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Helpers\Helper;
use App\Models\User;
use App\Models\Job;
use App\Models\Department;
use App\Models\DeviceToken;
use App\Models\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class JobController extends Controller
{
    protected $title = 'Jobs';
    protected $view = 'jobs.';

    public function __construct()
    {
        $this->middleware('permission:jobs.index')->only(['index', 'ajax']);
        $this->middleware('permission:jobs.create')->only(['create']);
        $this->middleware('permission:jobs.store')->only(['store']);
        $this->middleware('permission:jobs.edit')->only(['edit']);
        $this->middleware('permission:jobs.update')->only(['update']);
        $this->middleware('permission:jobs.show')->only(['show']);
        $this->middleware('permission:jobs.destroy')->only(['destroy']);
        $this->middleware('permission:jobs.reschedule')->only(['reschedule']);
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->ajax();
        }
        $title = $this->title;
        $subTitle = 'Manage jobs here';
        return view($this->view . 'index', compact('title', 'subTitle'));
    }

    public function ajax()
    {
        $currentUserRoles = auth()->user()->roles()->pluck('name')->toArray();

        $query = Job::with(['technicians', 'engineers'])->when(!(in_array('admin', $currentUserRoles) || in_array('job-coordinator', $currentUserRoles) || in_array('billing-coordinator', $currentUserRoles)), function ($queryBuilder) {
            $queryBuilder->whereHas('engineers', function ($innerQueryBuilder) {
                $innerQueryBuilder->where('technician_id', auth()->user()->id);
            });
        });

        if (request()->filled('filter_status')) {
            $query->where('status', request('filter_status'));
        }

        if (request()->filled('filter_customer')) {
            $query->where('customer_id', request('filter_customer'));
        }

        if (request()->filled('filter_technicians')) {
            $query->whereHas('technicians', function ($q) {
                $q->whereIn('technician_id', explode(',', request('filter_technicians')));
            });
        }

        if (request()->filled('filter_engineers')) {
            $query->whereHas('engineers', function ($q) {
                $q->whereIn('technician_id', explode(',', request('filter_engineers')));
            });
        }

        if (request()->filled('filter_depreq')) {
            $query->where('requires_deposit', request('filter_depreq') === '1');
        }

        if (request()->filled('filter_invgen')) {
            $query->where('is_invoice_generated', request('filter_invgen') === '1');
        }

        if (request()->filled('filter_addedby')) {
            $query->where('assigner_id', request('filter_addedby'));
        }

        if (request()->filled('filter_expertise')) {
            $query->whereHas('expertise', function ($q) {
                $q->whereIn('expertise_id', explode(',', request('filter_expertise')));
            });
        }

        if (request()->filled('filter_visiting_date_from')) {
            $query->where('visiting_date', '>=', date('Y-m-d', strtotime(request('filter_visiting_date_from'))));
        }

        if (request()->filled('filter_visiting_date_to')) {
            $query->where('visiting_date', '<=', date('Y-m-d', strtotime(request('filter_visiting_date_to'))));
        }

        if (request()->filled('filter')) {
            $dashboardFilter = request('filter');
            
            switch ($dashboardFilter) {
                case 'new-jobs':
                    $query->where('status', 'PENDING');
                    break;
                    
                case 'pending-jobs':
                    $query->where('status', 'PENDING')
                        ->whereDoesntHave('technicians')
                        ->whereDoesntHave('engineers');
                    break;
                    
                case 'upcoming-services':
                    $query->where('status', 'PENDING')
                        ->whereDate('visiting_date', '>', \Carbon\Carbon::today());
                    break;
                    
                case 'open-jobs':
                    $query->whereIn('status', ['PENDING']);
                    break;
                    
                case 'emergency-callouts':
                    $query->where('priority', 'CRITICAL');
                    break;
                    
                case 'workshop-jobs':
                    $query->where('in_workshop', 1);
                    break;
            }
        }
        
        $query->orderBy( 'id', 'DESC' );

        return datatables()
            ->eloquent($query)
            ->editColumn('status', function ($row) use ($currentUserRoles) {

                $finalHtml = '';

                    if ($row->status == 'PENDING') {
                        if ($row->technicians()->count() > 0) {
                            $finalHtml .= '<span class="badge bg-warning text-dark">Assigned</span>';
                        } else {
                            $finalHtml .= '<span class="badge bg-warning text-dark">Pending</span>';
                        }

                        if ($row->technicians()->count() == 0 && in_array(auth()->user()->id, $row->engineers()->pluck('technician_id')->toArray()) && in_array('engineer', auth()->user()->roles()->pluck('name')->toArray())) {
                            $finalHtml .= '<br><span class="badge bg-danger text-white">Techinicans to Assign</span>';
                        }

                    } else if ($row->status == 'INPROGRESS') {
                        $finalHtml .= '<span class="badge bg-primary">In Progress</span>';
                    } else if ($row->status == 'ONHOLD') {
                        $finalHtml .= '<span class="badge bg-secondary">On Hold</span>';
                    } else if ($row->status == 'COMPLETED') {

                        $finalHtml .= '<span class="badge bg-success-2">Job Completed</span>';

                        if ($row->approved_by_billing_department == 1) {
                            $finalHtml .= '<br><span class="badge bg-success-2"> Approved By Billing Coordinator </span>';
                        } else {
                            $finalHtml .= '<br><span class="badge bg-warning"> Need to Approved By Billing Coordinator </span>';
                        }
                        
                        if ($row->approved_by_engineer == 1) {
                            $finalHtml .= '<br><span class="badge bg-success-2"> Approved By Engineer </span>';
                        } else {
                            $finalHtml .= '<br><span class="badge bg-warning"> Need to Approved By Engineer </span>';
                        }

                    } else if ($row->status == 'CANCELLED') {
                        $finalHtml .= '<span class="badge bg-danger">Cancelled</span>';
                    } else {
                        $finalHtml .= '<span class="badge bg-light text-dark">Unknown</span>';
                    }

                    if ($row->reopened) {
                        $finalHtml .= '</br><span class="badge bg-info text-dark">Reopened</span>';
                    }

                    if ($row->in_workshop == 1) {
                        $finalHtml .= '<br/><span class="badge bg-success-2"> Sent to Workshop </span> <br/>';
                    }

                $html = $finalHtml . '<br>';

                if (in_array('admin', $currentUserRoles)) {
                    if ($row->status != 'COMPLETED') {
                        $statuses = Helper::getJobStatusList();
                        $html .= '<select class="form-select change-status" data-old="' . $row->status . '" data-id="' . $row->id . '" data-url="' . route('jobs.change-status', $row->id) . '">';
                        foreach ($statuses as $status) {
                            $selected = $row->status === $status ? 'selected' : '';
                            $html .= "<option value='{$status}' {$selected}>{$status}</option>";
                        }
                        $html .= '</select>';
                    }
                }

                return $html;
            })
            ->addColumn('time_spent', function ($row) {
                return Helper::calculateJobTotalTimeSpent( $row->id );
            })
            ->addColumn('action', function ($row) use ($currentUserRoles)  {
                $html = '';

                if (auth()->user()->can('jobs.edit')) {
                    if (in_array($row->status, ['PENDING', 'INPROGRESS'])) {
                        $html .= '<a href="' . route('jobs.edit', encrypt($row->id)) . '" class="btn btn-sm btn-primary"> <i class="fa fa-edit"> </i> </a>&nbsp;';
                    }
                }

                if (auth()->user()->can('jobs.destroy')) {
                    $html .= '<button type="button" class="btn btn-sm btn-danger" id="deleteRow" data-row-route="' . route('jobs.destroy', $row->id) . '"> <i class="fa fa-trash"> </i> </button>&nbsp;';
                }

                if (auth()->user()->can('jobs.show')) {
                    $html .= '<a href="' . route('jobs.show', encrypt($row->id)) . '" class="btn btn-sm btn-secondary"> <i class="fa fa-eye"> </i> </a>&nbsp;';
                }

                if (auth()->user()->can('jobs.reschedule')) {
                    if (in_array($row->status, ['PENDING', 'INPROGRESS'])) {
                        $html .= '<button type="button" class="btn btn-sm btn-warning reschedule-btn" 
                                    title="Reschedule"
                                    data-id="' . $row->id . '" 
                                    data-url="' . route('jobs.reschedule', $row->id) . '">
                                    <i class="fa fa-calendar"></i> 
                                </button>&nbsp;';
                    }
                }

                if ( $row->in_workshop != 1 && in_array($row->status, ['INPROGRESS']) ) {
                    $html .= '<button type="button" title="Send to Workshop" class="btn btn-sm btn-primary rounded-3 dms_send_workshop_btn" data-job_id="' . encrypt( $row->id ) . '">
                                <i class="fa fa-tools"></i>
                            </button>&nbsp;';
                }

                if ($row->technicians()->count() == 0 && $row->status == 'PENDING' && in_array(auth()->user()->id, $row->engineers()->pluck('technician_id')->toArray()) && in_array('engineer', auth()->user()->roles()->pluck('name')->toArray())) {
                    $html .= '<a href="' . route('jobs.edit', encrypt($row->id)) . '#engineers" class="btn btn-sm btn-danger text-white" style="background:#f1535d;padding:5px 15px;border-radius:10px;"> Assign Technician </a>&nbsp;';
                }

                if ( in_array('engineer', auth()->user()->roles()->pluck('name')->toArray()) && $row->in_workshop != 1 && $row->status == 'COMPLETED' && $row->approved_by_engineer != 1 ) {
                    $html .= '<button type="button" title="Approve" class="btn btn-sm btn-primary rounded-3 dms_approve_by_e" data-thetype="engineer" data-jid="' . encrypt( $row->id ) . '">
                                <i class="fa fa-check"></i> Approve
                            </button>&nbsp;';
                }

                if ( in_array('billing-coordinator', auth()->user()->roles()->pluck('name')->toArray()) && $row->in_workshop != 1 && $row->status == 'COMPLETED' && $row->approved_by_engineer == 1 && $row->approved_by_billing_department != 1 ) {
                    $html .= '<button type="button" title="Approve" class="btn btn-sm btn-primary rounded-3 dms_approve_by_e" data-thetype="billingcoordinator" data-jid="' . encrypt( $row->id ) . '">
                                <i class="fa fa-check"></i> Approve
                            </button>&nbsp;';
                }

                return $html;
            })
            ->rawColumns( [ 'status', 'time_spant', 'action' ] )
            ->addIndexColumn()
            ->toJson();
    }

    public function create()
    {
        $title = $this->title;
        $subTitle = 'Add New Job';
        $rightSideFilter = '';
        $job_status_arr = Helper::getJobStatusList();
        if ( !empty($job_status_arr) ) {
            $rightSideFilter .= '<select class="form-select dms_change_status_select" id="dms_job_status">';
            
            if (!in_array('admin', auth()->user()->roles()->pluck('name')->toArray())) {
                $rightSideFilter .= "<option value='PENDING'>PENDING</option>";
            } else {
                foreach ( $job_status_arr as $job_status ) {
                    $rightSideFilter .= "<option value='{$job_status}'>{$job_status}</option>";
                }
            }
            $rightSideFilter .= "</select>";
        }
        return view( $this->view . 'create', compact( 'title', 'subTitle', 'rightSideFilter' ) );
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer' => 'required|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_alternate_phone_number' => 'required|string|max:20',
            'customer_billing_name' => 'required|string|max:255',
            'customer_address_line_1' => 'required|string',
            'opening_date' => 'required',
            'visiting_date' => 'required',
            // 'expected_date' => 'required',
            'description' => 'required|string',
            'dms_job_priority' => 'required|string',
            'dms_attachment' => 'nullable|array',
            'dms_attachment.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'dms_job_status' => 'nullable|string',
            'summary' => 'nullable|string',
            'requires_deposit' => 'boolean',
            'deposit_type' => 'required_if:requires_deposit,1|in:FIX,PERCENT',
            'deposit_amount' => 'required_if:requires_deposit,1|min:0|max:100',
            'material' => 'nullable|array',
            'material.*.product' => 'required_with:material|exists:products,id',
            'material.*.quantity' => 'required_with:material|numeric|min:1',
            'material.*.price' => 'required_with:material|numeric|min:0',
            'material.*.amount' => 'required_with:material|numeric|min:0',
            'service' => 'nullable|array',
            'service.*.title' => 'required_with:service|string|max:255',
            'service.*.description' => 'nullable|string',
            'service.*.quantity' => 'required_with:service|numeric|min:1',
            'service.*.price' => 'required_with:service|numeric|min:0',
            'service.*.total_amount' => 'required_with:service|numeric|min:0',
            'services_discount_type' => 'nullable|in:PERCENT,FIX',
            'services_discount_amount' => 'nullable|numeric|min:0',
            'services_vat_type' => 'nullable|in:PERCENT,FIX',
            'services_vat_amount' => 'nullable|numeric|min:0',
            'parts_discount_type' => 'nullable|in:PERCENT,FIX',
            'parts_discount_amount' => 'nullable|numeric|min:0',
            'parts_vat_type' => 'nullable|in:PERCENT,FIX',
            'parts_vat_amount' => 'nullable|numeric|min:0',
            'requisition' => 'nullable|array',
            'requisition.*.type' => 'required_with:requisition|in:INVENTORY,VENDOR',
            'requisition.*.product' => 'required_with:requisition',
            'requisition.*.vendor' => 'required_if:requisition.*.type,VENDOR|exists:users,id',
            'requisition.*.description' => 'nullable|string',
            'requisition.*.quantity' => 'required_with:requisition|numeric|min:1',
            'requisition.*.amount' => 'required_with:requisition|numeric|min:0',
            'requisition.*.total' => 'required_with:requisition|numeric|min:0',
            'requisition.*.status' => 'required_with:requisition|in:PENDING,APPROVED,REJECTED',
            'requisition.*.rejection_note' => 'nullable|required_if:requisition.*.status,REJECTED|string',
        ]);

        DB::beginTransaction();

        try {
            $allCurrentCheckingDepartment = JobInspectionOrdering::select('department_id')->orderBy('ordering')->pluck('department_id')->toArray();
            $allCurrentCheckingDepartment = implode(',', $allCurrentCheckingDepartment);

            $filesData = [];

            if ($request->hasFile('dms_attachment')) {
                $folder = 'job_attachment';

                $fullPath = storage_path( "app/{$folder}" );
                if ( !File::exists( $fullPath ) ) {
                    File::makeDirectory( $fullPath, 0755, true );
                }

                foreach ( $request->file('dms_attachment') as $file ) {
                    $filename = Str::random( 20 ) . '.' . $file->getClientOriginalExtension();

                    $file->storeAs( $folder, $filename, 'public' );
                    $filesData[] = $filename;
                }
            }

            // Calculate time_to_complete timestamp
            $timeToComplete = null;
            if ($request->visiting_date && ($request->dms_days || $request->dms_hours || $request->dms_minutes)) {
                $visitingDate = \Carbon\Carbon::createFromFormat('d-m-Y h:i', $request->visiting_date);
                $visitingDate->addDays((int) ($request->dms_days ?? 0));
                $visitingDate->addHours((int) ($request->dms_hours ?? 0));
                $visitingDate->addMinutes((int) ($request->dms_minutes ?? 0));
                $timeToComplete = $visitingDate->format('Y-m-d H:i:s');
            }

            $jobData = [
                'code' => Helper::jobCode(),
                'customer_id' => $request->customer,
                'assigner_id' => auth()->user()->id,
                'title' => $request->title,
                
                'contact_name' => $request->customer_name,
                'contact_dial_code' => $request->customer_alternate_dial_code,
                'contact_phone_number' => $request->customer_alternate_phone_number,
                'billing_name' => $request->customer_billing_name,
                'email' => $request->customer_email,
                'address_line_1' => $request->customer_address_line_1,
                'address_line_2' => $request->customer_address_line_2,
                'latitude' => $request->customer_latitude,
                'longitude' => $request->customer_longitude,
                'location_url' => $request->customer_location_url,
                'location_id' => $request->location_id,
                
                'description' => $request->description,
                'summary' => $request->summary,
                'before_attachment' => !empty($filesData) ? json_encode( $filesData ) : null,
                'priority' => $request->dms_job_priority,
                'opening_date' => \Carbon\Carbon::createFromFormat( 'd-m-Y h:i', $request->opening_date )->format( 'Y-m-d H:i:s' ),
                'visiting_date' => \Carbon\Carbon::createFromFormat( 'd-m-Y h:i', $request->visiting_date )->format( 'Y-m-d H:i:s' ),
                // 'expected_date' => \Carbon\Carbon::createFromFormat( 'd-m-Y h:i', $request->expected_date )->format( 'Y-m-d H:i:s' ),
                'status' => $request->dms_job_status,
                'requires_deposit' => $request->requires_deposit ?? false,
                'deposit_type' => $request->requires_deposit ? $request->deposit_type : 'FIX',
                'deposit_amount' => $request->requires_deposit ? $request->deposit_amount : 0,
                'services_discount_type' => $request->services_discount_type ?? 'FIX',
                'services_discount_amount' => $request->services_discount_amount ?? 0,
                'services_vat_type' => $request->services_vat_type ?? 'PERCENT',
                'services_vat_amount' => $request->services_vat_amount ?? 0,
                'parts_discount_type' => $request->parts_discount_type ?? 'FIX',
                'parts_discount_amount' => $request->parts_discount_amount ?? 0,
                'parts_vat_type' => $request->parts_vat_type ?? 'PERCENT',
                'parts_vat_amount' => $request->parts_vat_amount ?? 0,
                'grand_total' => $request->grand_total ?? 0,
                'department_checking_order' => $allCurrentCheckingDepartment,
                'time_to_complete' => $timeToComplete
            ];

            $job = Job::create($jobData);

            if ($request->has('technicians') && is_array($request->technicians)) {
                foreach ($request->technicians as $technicianId) {
                    JobTechnician::create([
                        'job_id' => $job->id,
                        'technician_id' => $technicianId
                    ]);
                }
            }

            $tempEngineersForNotification = [];
            if ($request->has('engineers') && is_array($request->engineers)) {
                foreach ($request->engineers as $engineerId) {
                    $tempEngineersForNotification[] = JobTechnician::create([
                        'job_id' => $job->id,
                        'technician_id' => $engineerId,
                        'type' => 1
                    ])->id;
                }
            }

            if ($request->has('expertise') && is_array($request->expertise)) {
                foreach ($request->expertise as $expId) {
                    JobExpertise::create([
                        'job_id' => $job->id,
                        'expertise_id' => $expId
                    ]);
                }
            }

            // if ($request->has('material') && is_array($request->material)) {
            //     foreach ($request->material as $material) {
            //         if (!empty($material['product']) && !empty($material['quantity'])) {
            //             JobMaterial::create([
            //                 'job_id' => $job->id,
            //                 'product_id' => $material['product'],
            //                 'description' => $material['description'] ?? null,
            //                 'quantity' => $material['quantity'],
            //                 'amount' => $material['price'],
            //                 'total' => $material['amount']
            //             ]);
            //         }
            //     }
            // }

            if ($request->has('service') && is_array($request->service)) {
                foreach ($request->service as $service) {
                    if (!empty($service['title']) && !empty($service['quantity'])) {
                        JobService::create([
                            'job_id' => $job->id,
                            'title' => $service['title'],
                            'description' => $service['description'] ?? null,
                            'quantity' => $service['quantity'],
                            'price' => $service['price'],
                            'total_amount' => $service['total_amount']
                        ]);
                    }
                }
            }

            if (!empty($tempEngineersForNotification)) {
                \App\Jobs\SendEngineerNotificationForJobAssignment::dispatch($job->id, $tempEngineersForNotification);
            }

            // Handle requisitions
            if ($request->has('requisition') && is_array($request->requisition)) {
                // Create requisition for this job
                $requisition = Requisition::create([
                    'job_id'    => $job->id,
                    'code'      => Helper::requisitionCode(),
                    'added_by'  => auth()->user()->id
                ]);

                foreach ($request->requisition as $requisitionData) {
                    if (!empty($requisitionData['product']) && !empty($requisitionData['quantity'])) {
                        $itemData = [
                            'requisition_id' => $requisition->id,
                            'type' => $requisitionData['type'],
                            'description' => $requisitionData['description'] ?? null,
                            'quantity' => $requisitionData['quantity'],
                            'amount' => $requisitionData['amount'],
                            'total' => $requisitionData['total'],
                            'status' => $requisitionData['status'],
                            'rejection_note' => ($requisitionData['status'] === 'REJECTED') ? ($requisitionData['rejection_note'] ?? null) : null
                        ];

                        if ($requisitionData['type'] === 'INVENTORY') {
                            $itemData['product_id'] = $requisitionData['product'];
                            $itemData['product_name'] = null;
                            $itemData['vendor_id'] = null;
                        } else {
                            $itemData['product_id'] = null;
                            $itemData['product_name'] = $requisitionData['product'];
                            $itemData['vendor_id'] = $requisitionData['vendor'] ?? null;
                        }

                        RequisitionItem::create($itemData);
                    }
                }
            }

            $notification_user = !empty($request->engineers) ? $request->engineers : array();
            if ( User::find( $job->assigner_id )->hasRole( 'job-coordinator' ) ) {
                $notification_user[] = $job->assigner_id;
            }

            Helper::sendNotificationUser( 'job-created', $notification_user, $job->id );

            if ( !empty($request->technicians) ) {
                Helper::sendNotificationUser( 'job-assigned', $request->technicians, $job->id );
            }

            DB::commit();
            return redirect()->route('jobs.index')->with('success', 'Job created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('jobs.index')->with('error', 'Something Went Wrong: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $job = Job::findOrFail(decrypt($id));
            
            $expectedDepartments = [];
            if ($job->department_checking_order) {
                $departmentIds = explode(',', $job->department_checking_order);
                $expectedDepartments = Department::whereIn('id', $departmentIds)->orderByRaw('FIELD(id, ' . implode(',', $departmentIds) . ')')->get();
            }
            
            $title = $job->title;
            $subTitle = 'Job Details';
            $times = Helper::calculateTotalTimePerEmployee($job->id);
            
            return view($this->view . 'show', compact('job', 'title', 'subTitle', 'times', 'expectedDepartments'));
        } catch (\Exception $e) {
            return redirect()->route('jobs.index')->with('error', 'Job not found.');
        }
    }

    public function edit($id)
    {
        try {
            $job = Job::with( [ 'customer', 'technicians', 'materials.product.category', 'services', 'requisitions.items.product', 'requisitions.items.vendor' ] )->findOrFail(decrypt($id));
            
            if (!in_array($job->status, ['PENDING', 'INPROGRESS'])) {
                return redirect()->route('jobs.index')->with('error', 'Job cannot be edited in current status.');
            }
            
            $title = $job->title;
            $subTitle = 'Edit Job';
            $dial_code_iso = Helper::getIso2ByDialCode( $job->contact_dial_code );
            if ( empty($dial_code_iso) ) {
                $dial_code_iso = Helper::$defaulDialCode;
            }

            // Convert time_to_complete timestamp back to days, hours, minutes
            $extractdTime = ['00', '00', '00']; // Default values
            if ($job->time_to_complete && $job->visiting_date) {
                $visitingDate = \Carbon\Carbon::parse($job->visiting_date);
                $timeToComplete = \Carbon\Carbon::parse($job->time_to_complete);
                
                $diffInMinutes = $visitingDate->diffInMinutes($timeToComplete);
                $days = intval($diffInMinutes / (24 * 60));
                $hours = intval(($diffInMinutes % (24 * 60)) / 60);
                $minutes = $diffInMinutes % 60;
                
                $extractdTime = [
                    sprintf('%02d', $days),
                    sprintf('%02d', $hours),
                    sprintf('%02d', $minutes)
                ];
            }
            $job_status_arr = Helper::getJobStatusList();
            $rightSideFilter = '';
            if ( !empty($job_status_arr) ) {
                $rightSideFilter .= '<select class="form-select dms_change_status_select" id="dms_job_status">';
                if (!in_array('admin', auth()->user()->roles()->pluck('name')->toArray())) {
                        $rightSideFilter .= "<option value='{$job->status}' selected>{$job->status}</option>";
                } else {
                    foreach ( $job_status_arr as $job_status ) {
                        $selected = $job->status === $job_status ? 'selected' : '';
                        $rightSideFilter .= "<option value='{$job_status}' {$selected}>{$job_status}</option>";
                    }
                }
                $rightSideFilter .= "</select>";
            }
            return view( $this->view . 'edit', compact( 'job', 'title', 'subTitle', 'dial_code_iso', 'rightSideFilter', 'extractdTime' ) );
        } catch (\Exception $e) {
            return redirect()->route('jobs.index')->with('error', 'Job not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $job = Job::findOrFail(decrypt($id));
            
            if (!in_array($job->status, ['PENDING', 'INPROGRESS'])) {
                return redirect()->route('jobs.index')->with('error', 'Job cannot be edited in current status.');
            }
            $old_technicians = $job->technicians->pluck( 'technician_id' )->toArray();

            $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'customer_alternate_dial_code' => 'required|string|max:10',
                'customer_alternate_phone_number' => 'required|string|max:20',
                'customer_billing_name' => 'required|string|max:255',
                'customer_address_line_1' => 'required|string',
                'customer_address_line_2' => 'nullable|string',
                'customer_latitude' => 'nullable|numeric',
                'customer_longitude' => 'nullable|numeric',
                'customer_location_url' => 'nullable|string',
                'title' => 'required|string|max:255',
                'opening_date' => 'required',
                'visiting_date' => 'required',
                // 'expected_date' => 'required',
                'description' => 'required|string',
                'dms_job_priority' => 'required|string',
                'dms_attachment' => 'nullable|array',
                'dms_attachment.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:20480',
                'dms_job_status' => 'nullable|string',
                'summary' => 'nullable|string',
                'requires_deposit' => 'boolean',
                'deposit_type' => 'required_if:requires_deposit,1|in:FIX,PERCENT',
                'deposit_amount' => 'required_if:requires_deposit,1|min:0|max:100',
                /* 'material' => 'nullable|array',
                'material.*.product' => 'required_with:material|exists:products,id',
                'material.*.quantity' => 'required_with:material|numeric|min:1',
                'material.*.price' => 'required_with:material|numeric|min:0',
                'material.*.amount' => 'required_with:material|numeric|min:0', */
                'service' => 'nullable|array',
                'service.*.title' => 'required_with:service|string|max:255',
                'service.*.description' => 'nullable|string',
                'service.*.quantity' => 'required_with:service|numeric|min:1',
                'service.*.price' => 'required_with:service|numeric|min:0',
                'service.*.total_amount' => 'required_with:service|numeric|min:0',
                'services_discount_type' => 'nullable|in:PERCENT,FIX',
                'services_discount_amount' => 'nullable|numeric|min:0',
                'services_vat_type' => 'nullable|in:PERCENT,FIX',
                'services_vat_amount' => 'nullable|numeric|min:0',
                'parts_discount_type' => 'nullable|in:PERCENT,FIX',
                'parts_discount_amount' => 'nullable|numeric|min:0',
                'parts_vat_type' => 'nullable|in:PERCENT,FIX',
                'parts_vat_amount' => 'nullable|numeric|min:0',
                'requisition' => 'nullable|array',
                'requisition.*.type' => 'required_with:requisition|in:INVENTORY,VENDOR',
                'requisition.*.product' => 'required_with:requisition',
                'requisition.*.vendor' => 'required_if:requisition.*.type,VENDOR|exists:users,id',
                'requisition.*.description' => 'nullable|string',
                'requisition.*.quantity' => 'required_with:requisition|numeric|min:1',
                'requisition.*.amount' => 'required_with:requisition|numeric|min:0',
                'requisition.*.total' => 'required_with:requisition|numeric|min:0',
                'requisition.*.status' => 'required_with:requisition|in:PENDING,APPROVED,REJECTED',
                'requisition.*.rejection_note' => 'nullable|required_if:requisition.*.status,REJECTED|string',
            ]);

            DB::beginTransaction();

            $filesData = [];

            if ( $request->hasFile('dms_attachment') ) {
                $folder = 'job_attachment';

                $fullPath = storage_path( "app/{$folder}" );
                if ( !File::exists( $fullPath ) ) {
                    File::makeDirectory( $fullPath, 0755, true );
                }

                foreach ( $request->file('dms_attachment') as $file ) {
                    $filename = Str::random( 20 ) . '.' . $file->getClientOriginalExtension();

                    $file->storeAs( $folder, $filename, 'public' );
                    $filesData[] = $filename;
                }

                if (!empty($request->dms_old_attachment)) {
                    $oldFiles = json_decode($request->dms_old_attachment, true);
                    if ( !empty($oldFiles) && is_array($oldFiles) ) {
                        foreach ($oldFiles as $oldFile) {
                            $oldFilePath = $folder . '/' . $oldFile;
                            if ( Storage::disk( 'public' )->exists( $oldFilePath ) ) {
                                Storage::disk( 'public' )->delete( $oldFilePath );
                            }
                        }
                    }
                }
            }
            $dms_old_attachment = !empty($request->dms_old_attachment) ? $request->dms_old_attachment : null;
            $attachment = !empty($filesData) ? json_encode( $filesData ) : $dms_old_attachment;

            // Calculate time_to_complete timestamp
            $timeToComplete = null;
            if ($request->visiting_date && ($request->dms_days || $request->dms_hours || $request->dms_minutes)) {
                $visitingDate = \Carbon\Carbon::createFromFormat('d-m-Y h:i', $request->visiting_date);
                $visitingDate->addDays((int) ($request->dms_days ?? 0));
                $visitingDate->addHours((int) ($request->dms_hours ?? 0));
                $visitingDate->addMinutes((int) ($request->dms_minutes ?? 0));
                $timeToComplete = $visitingDate->format('Y-m-d H:i:s');
            }

            $job->update([
                'title' => $request->title,
                'contact_name' => $request->customer_name,
                'contact_dial_code' => $request->customer_alternate_dial_code,
                'contact_phone_number' => $request->customer_alternate_phone_number,
                'billing_name' => $request->customer_billing_name,
                'email' => $request->customer_email,
                'address_line_1' => $request->customer_address_line_1,
                'address_line_2' => $request->customer_address_line_2,
                'latitude' => $request->customer_latitude,
                'longitude' => $request->customer_longitude,
                'location_url' => $request->customer_location_url,
                'description' => $request->description,
                'summary' => $request->summary,
                'before_attachment' => $attachment,
                'priority' => $request->dms_job_priority,
                'opening_date' => \Carbon\Carbon::createFromFormat( 'd-m-Y h:i', $request->opening_date )->format( 'Y-m-d H:i:s' ),
                'visiting_date' => \Carbon\Carbon::createFromFormat( 'd-m-Y h:i', $request->visiting_date )->format( 'Y-m-d H:i:s' ),
                // 'expected_date' => \Carbon\Carbon::createFromFormat( 'd-m-Y h:i', $request->expected_date )->format( 'Y-m-d H:i:s' ),
                'requires_deposit' => $request->requires_deposit ?? false,
                'deposit_type' => $request->requires_deposit ? $request->deposit_type : 'FIX',
                'deposit_amount' => $request->requires_deposit ? $request->deposit_amount : 0,
                'services_discount_type' => $request->services_discount_type ?? 'FIX',
                'services_discount_amount' => $request->services_discount_amount ?? 0,
                'services_vat_type' => $request->services_vat_type ?? 'PERCENT',
                'services_vat_amount' => $request->services_vat_amount ?? 0,
                'parts_discount_type' => $request->parts_discount_type ?? 'FIX',
                'parts_discount_amount' => $request->parts_discount_amount ?? 0,
                'parts_vat_type' => $request->parts_vat_type ?? 'PERCENT',
                'parts_vat_amount' => $request->parts_vat_amount ?? 0,
                'grand_total' => $request->grand_total ?? 0,
                'time_to_complete' => $timeToComplete,
                'status' => $request->dms_job_status,
            ]);

            $techs = $engs = [];

            $new_technicians = array();
            if ($request->has('technicians') && is_array($request->technicians)) {
                foreach ($request->technicians as $technicianId) {
                    $techs[] = JobTechnician::updateOrCreate([
                        'job_id' => $job->id,
                        'technician_id' => $technicianId,
                        'type' => 0
                    ])->id;
                    if ( !in_array( $technicianId, $old_technicians ) ) {
                        $new_technicians[] = $technicianId;
                        $tUser = User::find($technicianId);

                        if (isset($tUser->id)) {
                            Helper::addJobLog($id, auth()->user()->id, 'Technician Assigned', 'A new techician (' . ($tUser->name) . ') has been assigned to job');
                        }
                    }
                }
            }

            if ($request->has('engineers') && is_array($request->engineers)) {
                foreach ($request->engineers as $enginerId) {
                    $engs[] = JobTechnician::updateOrCreate([
                        'job_id' => $job->id,
                        'technician_id' => $enginerId,
                        'type' => 1
                    ])->id;
                }
            }

            if (!empty($engs)) {
                \App\Jobs\SendEngineerNotificationForJobAssignment::dispatch($job->id, $engs);
            }

            if (!empty($techs)) {
                JobTechnician::where('job_id', $job->id)->whereNotIn('id', $techs)->where('type', 0)->delete();
            } else {
                JobTechnician::where('job_id', $job->id)->where('type', 0)->delete();
            }

            if (!empty($engs)) {
                JobTechnician::where('job_id', $job->id)->whereNotIn('id', $engs)->where('type', 1)->delete();
            } else {
                JobTechnician::where('job_id', $job->id)->where('type', 1)->delete();
            }

            $exps = [];
            if ($request->has('expertise') && is_array($request->expertise)) {
                foreach ($request->expertise as $expId) {
                    $exps[] = JobExpertise::updateOrCreate([
                        'job_id' => $job->id,
                        'expertise_id' => $expId
                    ])->id;
                }
            }

            if (!empty($exps)) {
                JobExpertise::where('job_id', $job->id)->whereNotIn('id', $exps)->delete();
            } else {
                JobExpertise::where('job_id', $job->id)->delete();
            }

            /* $existingMaterialIds = [];
            if ($request->has('material') && is_array($request->material)) {
                foreach ($request->material as $materialId => $material) {
                    if (!empty($material['product']) && !empty($material['quantity'])) {
                        if (is_numeric($materialId)) {
                            JobMaterial::where('id', $materialId)->where('job_id', $job->id)->update([
                                'product_id' => $material['product'],
                                'description' => $material['description'] ?? null,
                                'quantity' => $material['quantity'],
                                'amount' => $material['price'],
                                'total' => $material['amount']
                            ]);
                            $existingMaterialIds[] = $materialId;
                        } else {
                            JobMaterial::create([
                                'job_id' => $job->id,
                                'product_id' => $material['product'],
                                'description' => $material['description'] ?? null,
                                'quantity' => $material['quantity'],
                                'amount' => $material['price'],
                                'total' => $material['amount']
                            ]);
                        }
                    }
                }
            }
            
            JobMaterial::where('job_id', $job->id)->whereNotIn('id', $existingMaterialIds)->delete(); */

            $existingServiceIds = [];
            if ($request->has('service') && is_array($request->service)) {
                foreach ($request->service as $serviceId => $service) {
                    if (!empty($service['title']) && !empty($service['quantity'])) {
                        if (is_numeric($serviceId)) {
                            JobService::where('id', $serviceId)->where('job_id', $job->id)->update([
                                'title' => $service['title'],
                                'description' => $service['description'] ?? null,
                                'quantity' => $service['quantity'],
                                'price' => $service['price'],
                                'total_amount' => $service['total_amount']
                            ]);
                            $existingServiceIds[] = $serviceId;
                        } else {
                            JobService::create([
                                'job_id' => $job->id,
                                'title' => $service['title'],
                                'description' => $service['description'] ?? null,
                                'quantity' => $service['quantity'],
                                'price' => $service['price'],
                                'total_amount' => $service['total_amount']
                            ]);
                        }
                    }
                }
            }
            
            JobService::where('job_id', $job->id)->whereNotIn('id', $existingServiceIds)->delete();

            // Handle requisitions
            if ($request->has('requisition') && is_array($request->requisition)) {
                // Get or create requisition for this job
                $requisition = Requisition::firstOrCreate(
                    [ 'job_id' => $job->id ],
                    [
                        'added_by' => auth()->user()->id,
                        'code' => Helper::requisitionCode(),
                    ]
                );

                $existingRequisitionItemIds = [];
                foreach ($request->requisition as $itemId => $requisitionData) {
                    if (!empty($requisitionData['product']) && !empty($requisitionData['quantity'])) {
                        $itemData = [
                            'type' => $requisitionData['type'],
                            'description' => $requisitionData['description'] ?? null,
                            'quantity' => $requisitionData['quantity'],
                            'amount' => $requisitionData['amount'],
                            'total' => $requisitionData['total'],
                            'status' => $requisitionData['status'],
                            'rejection_note' => ($requisitionData['status'] === 'REJECTED') ? ($requisitionData['rejection_note'] ?? null) : null
                        ];

                        if ($requisitionData['type'] === 'INVENTORY') {
                            $itemData['product_id'] = $requisitionData['product'];
                            $itemData['product_name'] = null;
                            $itemData['vendor_id'] = null;
                        } else {
                            $itemData['product_id'] = null;
                            $itemData['product_name'] = $requisitionData['product'];
                            $itemData['vendor_id'] = $requisitionData['vendor'] ?? null;
                        }
                        
                        if (isset($requisitionData['id']) && is_numeric($requisitionData['id'])) {
                            // Update existing item
                            RequisitionItem::where('id', $requisitionData['id'])
                                ->where('requisition_id', $requisition->id)
                                ->update($itemData);
                            $existingRequisitionItemIds[] = $requisitionData['id'];
                        } else {
                            // Create new item
                            $newItem = RequisitionItem::create(array_merge($itemData, [
                                'requisition_id' => $requisition->id
                            ]));
                            $existingRequisitionItemIds[] = $newItem->id;
                        }
                    }
                }

                RequisitionItem::where('requisition_id', $requisition->id)
                    ->whereNotIn('id', $existingRequisitionItemIds)
                    ->delete();
            }

            if ( !empty($new_technicians) ) {
                Helper::sendNotificationUser( 'job-assigned', $new_technicians, $job->id );
            }

            DB::commit();
            return redirect()->route('jobs.index')->with('success', 'Job updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('jobs.index')->with('error', 'Something Went Wrong: ' . $e->getMessage());
        }
    }

    public function reschedule(Request $request, Job $job)
    {
        $request->validate([
            'reschedule_date' => 'required|date',
            'reason' => 'required|string|max:255'
        ]);

        DB::beginTransaction();

        try {
            JobRescheduleRequest::create([
                'job_id' => $job->id,
                'rescheduled_at' => date('Y-m-d H:i:s', strtotime($request->reschedule_date)),
                'reschedule_reason' => $request->reason
            ]);

            $job->visiting_date = date('Y-m-d H:i:s', strtotime($request->reschedule_date));
            $job->save();

            $notification_user = $job->technicians->pluck( 'technician_id' )->toArray();
            
            if ( User::find( $job->assigner_id )->hasRole( 'job-coordinator' ) ) {
                $notification_user[] = $job->assigner_id;
            }

            foreach ($job->technicians()->get() as $tech) {
                $notification_user[] = $tech->technician_id;
            }

            Helper::sendNotificationUser( 'job-rescheduled', $notification_user, $job->id );

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Job rescheduled successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Something went wrong!']);
        }
    }

    public function changeStatus(Request $request, Job $job)
    {
        $request->validate([
            'status' => 'required|string',
            'cancel_amount' => 'nullable|required_if:status,CANCELLED|numeric|min:0',
            'cancel_note' => 'nullable|required_if:status,CANCELLED|string|max:255',
            'hold_note' => 'nullable|required_if:status,ONHOLD|string|max:500'
        ]);

        DB::beginTransaction();

        try {

            $job->status = $request->status;

            if ($request->status === 'CANCELLED') {
                $job->cancellation_amount = $request->cancel_amount;
                $job->cancellation_note = $request->cancel_note;
            }

            if ($request->status === 'ONHOLD') {
                $job->hold_note = $request->hold_note;
            }

            $job->save();

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Job status changed successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Something went wrong!']);
        }
    }

    public function approve(Request $request, Job $job)
    {
        $request->validate([
            'status' => 'required|in:APPROVED,REJECTED',
            'description' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            $userDepartment = auth()->user()->department->first();
            if (!$userDepartment) {
                return response()->json(['status' => false, 'message' => 'User does not belong to any department.']);
            }

            $departmentId = $userDepartment->department_id;

            $departmentOrder = explode(',', $job->department_checking_order);
            
            $currentDepartmentIndex = array_search($departmentId, $departmentOrder);
            
            if ($currentDepartmentIndex === false) {
                return response()->json(['status' => false, 'message' => 'Department not found in job inspection order.']);
            }

            $currentInspectionLog = JobInspectedLog::where('job_id', $job->id)
                ->where('department_id', $departmentId)
                ->latest()
                ->first();

            if ($currentInspectionLog && $currentInspectionLog->status != 'REJECTED') {
                for ($i = 0; $i < $currentDepartmentIndex; $i++) {
                    $prevDepartmentId = $departmentOrder[$i];
                    $prevInspectionLog = JobInspectedLog::where('job_id', $job->id)
                        ->where('department_id', $prevDepartmentId)
                        ->latest()
                        ->first();

                    if (!$prevInspectionLog || $prevInspectionLog->status !== 'APPROVED') {
                        return response()->json(['status' => false, 'message' => 'Previous departments must approve first.']);
                    }
                }
            }

            JobInspectedLog::create([
                'job_id' => $job->id,
                'department_id' => $departmentId,
                'inspected_by' => auth()->user()->id,
                'status' => $request->status,
                'description' => $request->description
            ]);

            if ($request->status === 'APPROVED' && $currentDepartmentIndex === count($departmentOrder) - 1) {
                $job->status = 'COMPLETED';
                $job->save();
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Job inspection updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    public function getCurrentInspectionStatus(Job $job)
    {
        try {
            $userDepartment = auth()->user()->department->first();
            if (!$userDepartment) {
                return response()->json(['status' => false, 'message' => 'User does not belong to any department.']);
            }

            $departmentId = $userDepartment->department_id;
            $departmentOrder = explode(',', $job->department_checking_order);
            $currentDepartmentIndex = array_search($departmentId, $departmentOrder);

            if ($currentDepartmentIndex === false) {
                return response()->json(['status' => false, 'message' => 'Department not found in job inspection order.']);
            }

            $canApprove = true;
            for ($i = 0; $i < $currentDepartmentIndex; $i++) {
                $prevDepartmentId = $departmentOrder[$i];
                $prevInspectionLog = JobInspectedLog::where('job_id', $job->id)
                    ->where('department_id', $prevDepartmentId)
                    ->latest()
                    ->first();

                if (!$prevInspectionLog || $prevInspectionLog->status !== 'APPROVED') {
                    $canApprove = false;
                    break;
                }
            }

            $currentInspectionLog = JobInspectedLog::where('job_id', $job->id)
                ->where('department_id', $departmentId)
                ->latest()
                ->first();

            return response()->json([
                'status' => true,
                'can_approve' => $canApprove,
                'current_status' => $currentInspectionLog ? $currentInspectionLog->status : null,
                'department_name' => Department::find($departmentId)->name ?? 'Unknown'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $job = Job::findOrFail($id);

            DB::beginTransaction();
            
            $job->delete();
            
            DB::commit();
            return response()->json(['success' => 'Job deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something Went Wrong: ' . $e->getMessage()], 500);
        }
    }

    public function ajax_common( Request $request )
    {
        $res = array( 'status' => false, 'message' => 'Something went wrong, please try again' );
        $status = 401;
        $dms_action = !empty($request->dms_action) ? $request->dms_action : '';
        if ( $dms_action == 'job_send_to_workshop' ) {
            $request->validate([
                'job_id' => 'required|string'
            ]);
            $id = decrypt( $request->job_id );
            $job_row = Job::find( $id );
            if ( !empty($job_row) ) {
                $job_row->in_workshop = 1;
                $job_row->save();
                $res = array( 'status' => true, 'message' => 'Job send to workshop successfully.' );
                $status = 200;
            } else {
                $res = array( 'status' => false, 'message' => 'Job not found!' );
                $status = 200;
            }
        } else if ($dms_action == 'approve_by_engineer') {
            $request->validate([
                'job_id' => 'required|string'
            ]);
            $id = decrypt( $request->job_id );
            $job_row = Job::find( $id );
            if ( !empty($job_row) ) {

                if ($request->approve_type == 'engineer') {
                    $job_row->approved_by_engineer = 1;
                    $job_row->approved_by_engineer_id = auth()->user()->id;
                    $job_row->save();
                    $res = array( 'status' => true, 'message' => 'Job approved successfully.' );
                    $status = 200;

                    Helper::addJobLog($id, auth()->user()->id, 'Approved By Engineer', 'Job has been reviwed and approved by an engineer');

                } else {
                    $job_row->approved_by_billing_department = 1;
                    $job_row->approved_by_billing_department_id = auth()->user()->id;
                    $job_row->save();
                    $res = array( 'status' => true, 'message' => 'Job approved successfully.' );
                    $status = 200;

                    Helper::addJobLog($id, auth()->user()->id, 'Approved By Billing Co-ordinator', 'Job has been reviwed and approved by an billing co-ordinator');
                }

            } else {
                $res = array( 'status' => false, 'message' => 'Job not found!' );
                $status = 200;
            }            
        }

        return response()->json( $res, $status );
    }

}
