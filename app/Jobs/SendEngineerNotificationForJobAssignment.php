<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Models\NotificationTemplate;
use App\Helpers\Helper;
use App\Models\Job;
use App\Models\User;

class SendEngineerNotificationForJobAssignment implements ShouldQueue
{
    use Queueable;

    public $jobId;

    public $engineers = [];

    /**
     * Create a new job instance.
     */
    public function __construct($jobId = null, $engineers = [])
    {
        $this->jobId = $jobId;
        $this->engineers = $engineers;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!empty($this->jobId) && !empty($this->engineers)) {
            $template = NotificationTemplate::where('event', 'have-to-assign-technician')->first();
            $job = Job::find($this->jobId);

            if ($template && $job) {
                foreach (User::whereIn('id', $this->engineers)->get() as $user) {
                    $title = Helper::notificationTemplateReplaceVariable( $template->title, $job, $user );
                    $body = Helper::notificationTemplateReplaceVariable( $template->body, $job, $user );

                    Mail::send([], [], function ( $message ) use ( $user, $title, $body ) {
                        $message->to( $user->email, $user->name )
                            ->subject( $title )
                            ->html( $body, 'text/html' );
                    });
                }
            }
        }
    }
}
