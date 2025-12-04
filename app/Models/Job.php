<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $table = 'job';

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function approvedbyengineer()
    {
        return $this->belongsTo(User::class, 'approved_by_engineer_id');
    }
    
    public function approvedbybillingcoordinator()
    {
        return $this->belongsTo(User::class, 'approved_by_billing_department_id');
    }

    public function signature()
    {
        return $this->belongsTo(JobSignature::class, 'job_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigner_id');
    }

    public function technicians()
    {
        return $this->hasMany(JobTechnician::class)->where('type', 0);
    }

    public function engineers()
    {
        return $this->hasMany(JobTechnician::class)->where('type', 1);
    }

    public function materials()
    {
        return $this->hasMany(JobMaterial::class);
    }

    public function services()
    {
        return $this->hasMany(JobService::class);
    }

    public function expertise()
    {
        return $this->hasMany(JobExpertise::class);
    }

    public function requisitions()
    {
        return $this->hasMany(Requisition::class);
    }

    /*from now Job will have single requistion entry in requisition table*/
    public function singleRequistion()
    {
        return $this->hasOne(Requisition::class);
    }

    public function inspectionLogs()
    {
        return $this->hasMany(JobInspectedLog::class);
    }

    public function invoiceGeneratedBy()
    {
        return $this->belongsTo(User::class, 'invoice_generated_by');
    }

    public function toAPIArray( $details_param = '' )
    {
        $customer_profile_url = $customer_phone_number = $customer_email = '';
        if (!empty($this->customer)) {
            $customer_profile_url = !empty($this->customer->profile) ? $this->customer->userprofile : '';
            $customer_phone_number = "(+{$this->customer->dial_code})" . $this->customer->phone_number;
            $customer_email = $this->customer->email;
        }
        
        $response = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'summary' => $this->summary,
            'code' => $this->code,
            'priority' => $this->priority,
            'time_to_complete' => date('H:i:s', strtotime($this->time_to_complete)),
            'visiting_date' => $this->visiting_date,
            'opening_date' => $this->opening_date,
            'visited_date' => $this->visited_date,
            'completed_at' => $this->completed_at,
            'cancellation_note' => $this->cancellation_note,
            'billing_name' => $this->billing_name,
            'customer_name' => $this->contact_name,
            'customer_email' => $customer_email,
            'customer_phone_number' => $customer_phone_number,
            'customer_profile_url' => $customer_profile_url,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'status' => $this->status,
            'hold_note' => $this->hold_note,
            'in_workshop' => $this->in_workshop,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'services_discount_type' => $this->services_discount_type,
            'services_discount_amount' => $this->services_discount_amount,
            'services_vat_type' => $this->services_vat_type,
            'services_vat_amount' => $this->services_vat_amount,
            'is_invoice_generated' => $this->is_invoice_generated,
            'invoice_number' => $this->invoice_number,
            'invoice_generated_at' => $this->invoice_generated_at,
            'invoice_generated' => route('jobs.download-invoice', encrypt($this->id)),
            'customer' => $this->customer,
            'approved_by_engineer' => $this->approved_by_engineer,
            'approved_by_billing_department' => $this->approved_by_billing_department,
            'approved_by_engineer_id' => $this->approvedbyengineer,
            'approved_by_billing_department_id' => $this->approvedbybillingcoordinator,
            'has_technician_started_job' => TimeSpentOnJob::where('job_id', $this->id)->where('technician_id', auth()->user()->id)
            ->where(function ($innerBuilder) {
                $innerBuilder->whereNull('punch_out_at')->orWhere('punch_out_at', '');
            })->exists()
        ];

        if ( $details_param == 'job_details' ) {
            $response['job_attachment'] = $this->jobattachment;
            $response['job_before_attachment'] = $this->jobbeforeattachment;
            $response['requisition_details'] = $this->singleRequistion;
            $response[ 'service_details' ] = $this->services;
        } else if ( $details_param == 'requisition_details' ) {
            $response['requisition_details'] = Requisition::with(['items.product.category'])
            ->where('job_id', $this->id)
            ->first();

            if (empty($response['requisition_details'])) {
                $response['requisition_details'] = null;
            } else {
                $response['requisition_details'] = collect($response['requisition_details'])->map(function ($el) {

                    if (!empty($el['items'])) {
                        foreach ($el['items'] as &$item) {

                            if ($item['status'] == 'APPROVED') {
                                $item['po_link'] = route('get-po-invoice', ($el->job_id));
                            }
                        }
                    }

                    return $el;
                });
            }
        }

        return $response;
    }

    public function getJobAttachmentAttribute() {
        $attachment_url_arr = array();
        $attachment_arr = !empty($this->attachment) ? json_decode( $this->attachment, true ) : array();
        if ( !empty($attachment_arr) ) {
            foreach ( $attachment_arr as $attachment ) {
                if ( file_exists( public_path( "storage/job_attachment/{$attachment}" ) ) ) {
                    $attachment_url_arr[] = asset( "storage/job_attachment/{$attachment}" );
                }
            }
        }

        return $attachment_url_arr;
    }

    public function getJobBeforeAttachmentAttribute() {
        $attachment_url_arr = array();
        $attachment_arr = !empty($this->before_attachment) ? json_decode( $this->before_attachment, true ) : array();
        if ( !empty($attachment_arr) ) {
            foreach ( $attachment_arr as $attachment ) {
                if ( file_exists( public_path( "storage/job_attachment/{$attachment}" ) ) ) {
                    $attachment_url_arr[] = asset( "storage/job_attachment/{$attachment}" );
                }
            }
        }

        return $attachment_url_arr;
    }
}
