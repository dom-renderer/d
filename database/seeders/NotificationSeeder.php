<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notifications = [
            [
                'event'  => 'job-created',
                'title'  => 'New job created: {job_code} for {customer_name}',
                'body'   => '',
                'status' => 'ACTIVE',
                'type'   => [ "email", "push-notification" ],
                'roles'  => ['job-coordinator']
            ],
            [
                'event'  => 'job-assigned',
                'title'  => 'New job assigned: {job_code} - {job_title}',
                'body'   => '',
                'status' => 'ACTIVE',
                'type'   => [ "push-notification" ],
                'roles'  => ['technician']
            ],
            [
                'event'  => 'job-started',
                'title'  => 'Technician {technician_name} started job {job_code}',
                'body'   => '',
                'status' => 'ACTIVE',
                'type'   => [ "email", "push-notification" ],
                'roles'  => ['technician']
            ],
            [
                'event'  => 'job-paused',
                'title'  => 'Job {job_code} paused - reason: {job_hold_note}',
                'body'   => '',
                'status' => 'ACTIVE',
                'type'   => [ "email", "push-notification" ],
                'roles'  => ['technician']
            ],
            [
                'event'  => 'job-completed',
                'title'  => 'Job {job_code} marked complete by {technician_name}',
                'body'   => '',
                'status' => 'ACTIVE',
                'type'   => [ "email", "push-notification" ],
                'roles'  => ['technician']
            ],
            [
                'event'  => 'job-rescheduled',
                'title'  => 'Job {job_code} rescheduled to {job_reschedule_date}',
                'body'   => '',
                'status' => 'ACTIVE',
                'type'   => [ "email" ],
                'roles'  => ['technician', 'job-coordinator']
            ],
            [
                'event'  => 'po-created',
                'title'  => 'Purchase Order created for Job {job_code}',
                'body'   => '',
                'status' => 'ACTIVE',
                'type'   => [ "email" ],
                'roles'  => ['inventory-procurement']
            ],
            [
                'event'  => 'invoice-approved',
                'title'  => 'Invoice for Job {job_code} approved by {engineer_name}',
                'body'   => '',
                'status' => 'ACTIVE',
                'type'   => [ "email" ],
                'roles'  => ['billing-coordinator']
            ],
            [
                'event'  => 'invoice-sent',
                'title'  => 'Your invoice for Job {job_code} is ready',
                'body'   => '',
                'status' => 'ACTIVE',
                'type'   => [ "email" ],
                'roles'  => ['billing-coordinator']
            ],
            [
                'event'  => 'offline-data-pending',
                'title'  => '{offline_job_pending_count} job actions pending sync',
                'body'   => '',
                'status' => 'ACTIVE',
                'type'   => [ "push-notification" ],
                'roles'  => ['technician']
            ],
            [
                'event'  => 'sync-success',
                'title'  => 'Offline job data synced',
                'body'   => '',
                'status' => 'ACTIVE',
                'type'   => [ "push-notification" ],
                'roles'  => ['technician']
            ],
            [
                'event'  => 'sync-error',
                'title'  => 'Sync error for Technician {technician_name} on Job {job_code}',
                'body'   => '',
                'status' => 'ACTIVE',
                'type'   => [ "push-notification" ],
                'roles'  => ['admin']
            ],
            [
                'event'  => 'have-to-assign-technician',
                'title'  => '{job_code} - {job_title}',
                'body'   => '',
                'status' => 'ACTIVE',
                'type'   => [ "email" ],
                'roles'  => ['engineer']
            ],
            [
                'event' => 'item-request-created',
                'title'  => '{job_code} - {job_title}',
                'body'   => '',
                'status' => 'ACTIVE',
                'type'   => [ "email" ],
                'roles'  => ['engineer', 'billing-coordinator', 'job-coordinator']
            ]
        ];

        foreach ( $notifications as $notification ) {
            NotificationTemplate::firstOrCreate(
                [ 'event' => $notification['event'] ],
                $notification
            );
        }
    }
}
