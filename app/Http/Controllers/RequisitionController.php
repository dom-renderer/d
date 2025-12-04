<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\RequisitionItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Requisition;
use App\Helpers\Helper;
use App\Models\Job;

class RequisitionController extends Controller
{
    protected $title = 'Requisitions';
    protected $view = 'requisitions.';

    public function __construct()
    {
        $this->middleware('permission:requisitions.index')->only(['index', 'ajax']);
        $this->middleware('permission:requisitions.create')->only(['create']);
        $this->middleware('permission:requisitions.store')->only(['store']);
        $this->middleware('permission:requisitions.edit')->only(['edit']);
        $this->middleware('permission:requisitions.update')->only(['update']);
        $this->middleware('permission:requisitions.show')->only(['show']);
        $this->middleware('permission:requisitions.destroy')->only(['destroy']);
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->ajax();
        }

        $title = $this->title;
        $subTitle = 'List';
        return view($this->view . 'index', compact('title', 'subTitle'));
    }

    public function ajax()
    {
        $currentUserRoles = auth()->user()->roles()->pluck('name')->toArray();

        $query = Requisition::with(['job' => fn ($builder) => $builder->withTrashed(), 'addedBy' => fn ($builder) => $builder->withTrashed()])
        ->when(!(in_array('admin', $currentUserRoles) || in_array('job-coordinator', $currentUserRoles)), function ($queryBuilder) {
            $queryBuilder->whereHas('job.engineers', function ($innerQueryBuilder) {
                $innerQueryBuilder->where('technician_id', auth()->user()->id);
            });
        });

        if (request()->filled('filter_status')) {
            $query->where('status', request('filter_status'));
        }

        if (request()->filled('filter_job')) {
            $query->where('job_id', request('filter_job'));
        }

        if (request()->filled('filter_addedby')) {
            $query->where('added_by', request('filter_addedby'));
        }

        return datatables()
            ->eloquent($query)
            ->addColumn('job_code', function ($row) {
                return !$row->job->deleted_at ? $row->job->code : ($row->job->code .  '<br><span class="badge bg-danger">Job Removed</span>');
            })
            ->addColumn('added_by_name', function ($row) {
                return $row->addedBy ? $row->addedBy->name : '-';
            })
            ->editColumn('status', function ($row) {
                switch ($row->status) {
                    case 'PENDING':
                        return '<span class="badge bg-warning">Pending</span>';
                    case 'APPROVED':
                        return '<span class="badge bg-success">Approved</span>';
                    case 'REJECTED':
                        return '<span class="badge bg-danger">Rejected</span>';
                    default:
                        return '<span class="badge bg-secondary">Unknown</span>';
                }
            })
            ->editColumn('total', function ($row) {
                return Helper::number_format($row->items()->sum('total'), 2);
            })
            ->editColumn('created_at', function ($row) {
                return date('d-m-Y H:i', strtotime($row->created_at));
            })
            ->addColumn('action', function ($row) {
                $html = '';
                
                if (auth()->user()->can('requisitions.edit') && !$row->job->deleted_at) {
                    $html .= '<a href="' . route('requisitions.edit', encrypt($row->id)) . '" class="btn btn-sm btn-primary"> <i class="fa fa-edit"> </i> </a>&nbsp;';
                }

                if (auth()->user()->can('requisitions.destroy')) {
                    $html .= '<button type="button" class="btn btn-sm btn-danger" id="deleteRow" data-row-route="' . route('requisitions.destroy', $row->id) . '"> <i class="fa fa-trash"> </i> </button>&nbsp;';
                }

                if (auth()->user()->can('requisitions.show')) {
                    $html .= '<a href="' . route('requisitions.show', encrypt($row->id)) . '" class="btn btn-sm btn-secondary"> <i class="fa fa-eye"> </i> </a>&nbsp;';
                }

                if (auth()->user()->can('requisitions.show')) {
                    $html .= '<a href="' . route('get-po-invoice', ($row->id)) . '" class="btn btn-sm btn-secondary"> <i class="fa fa-file"> </i> </a>&nbsp;';
                }

                if (auth()->user()->can('requisitions.approve-reject') && $row->status === 'PENDING' && !$row->job->deleted_at) {
                    // $html .= '<button data-route="' . route('requisitions.approve-reject', encrypt($row->id)) . '" class="btn btn-sm btn-success open-approve-popup"> <i class="fa fa-check"> </i> Approve</button>&nbsp;';
                    // $html .= '<button data-route="' . route('requisitions.approve-reject', encrypt($row->id)) . '" class="btn btn-sm btn-danger open-reject-popup"> <i class="fa fa-times"> </i> Reject</button>';
                }

                return $html;
            })
            ->rawColumns(['job_code', 'type', 'status', 'action'])
            ->addIndexColumn()
            ->toJson();
    }

    public function create()
    {
        $title = $this->title;
        $subTitle = 'Add';
        return view($this->view . 'create', compact('title', 'subTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:job,id',
            'requisition' => 'required|array|min:1',
            'requisition.*.type' => 'required|in:INVENTORY,VENDOR',
            'requisition.*.product' => 'required',
            'requisition.*.description' => 'nullable|string',
            'requisition.*.amount' => 'required|numeric|min:0',
            'requisition.*.quantity' => 'required|numeric|min:1',
            'requisition.*.total' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        
        try {

            $requisitionEloquent = new Requisition();
            $requisitionEloquent->job_id = $request->job_id;
            $requisitionEloquent->code = Helper::requisitionCode();
            $requisitionEloquent->added_by = auth()->user()->id;
            $requisitionEloquent->status = 'PENDING';
            $requisitionEloquent->save();

            foreach ($request->input('requisition') as $item) {
                $requisitionItem = new RequisitionItem();
                $requisitionItem->requisition_id = $requisitionEloquent->id;
                $requisitionItem->type = $item['type'];
                
                if ($item['type'] === 'INVENTORY') {
                    $requisitionItem->product_id = $item['product'];
                    $requisitionItem->vendor_id = null;
                } else {
                    $requisitionItem->product_name = $item['product'];
                    $requisitionItem->vendor_id = $item['vendor'] ?? null;
                }

                $requisitionItem->description = $item['description'] ?? null;
                $requisitionItem->amount = $item['amount'];
                $requisitionItem->quantity = $item['quantity'];
                $requisitionItem->total = $item['total'];
                $requisitionItem->save();
            }

            DB::commit();
            return redirect()->route('requisitions.index')->with('success', 'Requisition created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create requisition.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $requisition = Requisition::with(['job' => fn ($builder) => $builder->withTrashed(), 'addedBy' => fn ($builder) => $builder->withTrashed(), 'items.product'])
                ->findOrFail(decrypt($id));
            
            $title = $this->title;
            $subTitle = 'Details';
            
            return view($this->view . 'show', compact('requisition', 'title', 'subTitle'));
        } catch (\Exception $e) {
            return redirect()->route('requisitions.index')->with('error', 'Requisition not found.');
        }
    }

    public function edit($id)
    {
        try {
            $requisition = Requisition::with(['job', 'items.product'])->findOrFail(decrypt($id));
            
            $title = $this->title;
            $subTitle = 'Edit';
            return view($this->view . 'edit', compact('requisition', 'title', 'subTitle'));
        } catch (\Exception $e) {
            return redirect()->route('requisitions.index')->with('error', 'Requisition not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $requisition = Requisition::findOrFail(decrypt($id));

            $request->validate([
                'job_id' => 'required|exists:job,id',
                'requisition' => 'required|array|min:1',
                'requisition.*.type' => 'required|in:INVENTORY,VENDOR',
                'requisition.*.product' => 'required',
                'requisition.*.description' => 'nullable|string',
                'requisition.*.amount' => 'required|numeric|min:0',
                'requisition.*.status' => 'required|in:PENDING,APPROVED,REJECTED',
                'requisition.*.rejection_note' => 'nullable|required_if:requisition.*.status,REJECTED|string',
                'requisition.*.quantity' => 'required|numeric|min:1',
                'requisition.*.total' => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            $requisition->job_id = $request->job_id;
            $requisition->save();

            $existingItemIds = $requisition->items->pluck('id')->toArray();
            $updatedItemIds = [];
            $change_status_arr = array();

            foreach ($request->input('requisition') as $item) {
                if (isset($item['id']) && !empty($item['id'])) {

                    $requisitionItem = RequisitionItem::find($item['id']);
                    if ($requisitionItem && $requisitionItem->requisition_id == $requisition->id) {
                        if ( $item['status'] != 'PENDING' && $item['status'] != $requisitionItem->status ) {
                            $change_status_arr[] = $item['status'];
                        }
                        $requisitionItem->type = $item['type'];
                        
                        if ($item['type'] === 'INVENTORY') {
                            $requisitionItem->product_id = $item['product'];
                            $requisitionItem->vendor_id = null;
                        } else {
                            $requisitionItem->product_name = $item['product'];
                            $requisitionItem->vendor_id = $item['vendor'] ?? null;
                        }

                        $requisitionItem->description = $item['description'] ?? null;
                        $requisitionItem->amount = $item['amount'];
                        $requisitionItem->quantity = $item['quantity'];
                        $requisitionItem->total = $item['total'];
                        $requisitionItem->status = $item['status'] ?? 'PENDING';
                        $requisitionItem->rejection_note = ($item['status'] === 'REJECTED') ? ($item['rejection_note'] ?? null) : null;
                        $requisitionItem->save();

                        $updatedItemIds[] = $requisitionItem->id;
                    }
                } else {
                    if ( $item['status'] != 'PENDING' ) {
                        $change_status_arr[] = $item['status'];
                    }
                    $requisitionItem = new RequisitionItem();
                    $requisitionItem->requisition_id = $requisition->id;
                    $requisitionItem->type = $item['type'];
                    
                    if ($item['type'] === 'INVENTORY') {
                        $requisitionItem->product_id = $item['product'];
                            $requisitionItem->vendor_id = null;
                    } else {
                        $requisitionItem->product_name = $item['product'];
                        $requisitionItem->vendor_id = $item['vendor'] ?? null;
                    }

                    $requisitionItem->description = $item['description'] ?? null;
                    $requisitionItem->amount = $item['amount'];
                    $requisitionItem->quantity = $item['quantity'];
                    $requisitionItem->total = $item['total'];
                    $requisitionItem->status = $item['status'] ?? 'PENDING';
                    $requisitionItem->rejection_note = ($item['status'] === 'REJECTED') ? ($item['rejection_note'] ?? null) : null;
                    $requisitionItem->save();

                    $updatedItemIds[] = $requisitionItem->id;
                }
            }

            if ( !empty($change_status_arr) ) {
                $job = Job::findOrFail( $request->job_id );
                $notification_user = $job->technicians->pluck( 'technician_id' )->toArray();
                if ( !empty($notification_user) ) {
                    if ( in_array( 'APPROVED', $change_status_arr ) ) {
                        Helper::sendNotificationUser( 'item-request-approved', $notification_user, $request->job_id );
                    }
                    if ( in_array( 'REJECTED', $change_status_arr ) ) {
                        Helper::sendNotificationUser( 'item-request-rejected', $notification_user, $request->job_id );
                    }
                }
            }

            $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
            if (!empty($itemsToDelete)) {
                RequisitionItem::whereIn('id', $itemsToDelete)->delete();
            }

            DB::commit();
            return redirect()->route('requisitions.index')->with('success', 'Requisition updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update requisition: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $requisition = Requisition::findOrFail($id);

            DB::beginTransaction();
            
            $requisition->items()->delete();
            
            $requisition->delete();
            
            DB::commit();
            return response()->json(['success' => 'Requisition deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something Went Wrong: ' . $e->getMessage()], 500);
        }
    }

    public function approveReject(Request $request, $id) {
        $request->validate([
            'status' => 'required|in:APPROVED,REJECTED',
            'remarks' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();

        try {
            $requisition = Requisition::findOrFail(decrypt($id));

            if ($requisition->status != $request->status) {
                if ($request->status == 'APPROVED') {
                    $requisition->status = 'APPROVED';
                } elseif ($request->status == 'REJECTED') {
                    $requisition->status = 'REJECTED';
                } else {
                    return response()->json(['error' => 'Invalid status provided.'], 400);
                }
            }

            if ($request->status == 'REJECTED') {
                $requisition->rejected_reason = $request->remark;
                $requisition->rejected_by = auth()->user()->id;
                $requisition->rejected_at = now();
                $requisition->status = 'REJECTED';
            } else if ($request->status == 'APPROVED') {
                $requisition->approved_reason = $request->remark;
                $requisition->approved_by = auth()->user()->id;
                $requisition->approved_at = now();
                $requisition->status = 'APPROVED';
            }

            $requisition->save();

            DB::commit();
            return response()->json(['success' => 'Requisition status updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something Went Wrong: ' . $e->getMessage()], 500);
        }
    }

    public function po($id) {
        try {
            $requistion = Requisition::find($id);

            $job = Job::with([
                'customer', 
                'assigner', 
                'technicians.technician', 
                'engineers.technician',
                'materials.product.category',
                'services',
                'expertise.expertise',
                'requisitions.items.product',
                'requisitions.items.vendor',
                'invoiceGeneratedBy'
            ])->findOrFail(($requistion->job_id));

            if (!$job->is_invoice_generated) {
                return redirect()->back()->with('error', 'Invoice has not been generated for this job.');
            }

            $setting = \App\Models\Setting::first();

            $pdf = PDF::loadView('invoices.po-invoice', compact('job', 'setting'));
            
            return $pdf->download('PO-' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to download invoice: ' . $e->getMessage());
        }
    }
}