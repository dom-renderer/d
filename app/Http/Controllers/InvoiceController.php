<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Job;

class InvoiceController extends Controller
{
    public function generateInvoice($jobId)
    {
        try {
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
            ])->findOrFail(decrypt($jobId));

            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($job->id, 6, '0', STR_PAD_LEFT);

            $job->update([
                'is_invoice_generated' => true,
                'invoice_number' => $invoiceNumber,
                'invoice_generated_at' => now(),
                'invoice_generated_by' => auth()->user()->id
            ]);

            $setting = \App\Models\Setting::first();

            $pdf = PDF::loadView('invoices.job-invoice', compact('job', 'setting'));
            
            return $pdf->download('Invoice-' . $invoiceNumber . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }

    public function downloadInvoice($jobId)
    {
        try {
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
            ])->findOrFail(decrypt($jobId));

            if (!$job->is_invoice_generated) {
                return redirect()->back()->with('error', 'Invoice has not been generated for this job.');
            }

            $setting = \App\Models\Setting::first();

            $pdf = PDF::loadView('invoices.job-invoice', compact('job', 'setting'));
            
            return $pdf->download('Invoice-' . $job->invoice_number . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to download invoice: ' . $e->getMessage());
        }
    }
}
