<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Models\NotificationTemplate;
use App\Models\Notification;
use App\Helpers\Helper;
use App\Models\User;
use App\Models\Job;

class CommonNotificationDispatcher implements ShouldQueue
{
    use Queueable;

    public $event;
    public $user_id_arr;
    public $job_id;

    /**
     * Create a new job instance.
     */
    public function __construct($event, $user_id_arr, $job_id)
    {
        $this->event = $event;
        $this->user_id_arr = $user_id_arr;
        $this->job_id = $job_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {


        $template = NotificationTemplate::active()->where( 'event', $this->event )->first();
        if ( !empty($user_id_arr) && !empty($template) ) {
            $notification_type = $template->type;
            $job_row = Job::find( $this->job_id );
            $users = User::whereIn( 'id', $user_id_arr )->get();

            foreach ( $users as $user ) {
                $title = Helper::notificationTemplateReplaceVariable( $template->title, $job_row, $user );
                $body = Helper::notificationTemplateReplaceVariable( $template->body, $job_row, $user );

                if ( !empty($notification_type) && in_array( 'push-notification', $notification_type ) ) {
                    $device_ids = $user->deviceTokens->pluck( 'token' )->toArray();
                    $data = array( 'title' => $title, 'description' => $body );
                    if ( !empty($device_ids) ) {
                        Helper::sendPushNotification( $device_ids, $data );
                        Notification::create( [ 'user_id' => $user->id, 'title' => $title, 'message' => $body, 'type' => 'info' ] );
                    }
                }
                if ( !empty($notification_type) && in_array( 'email', $notification_type ) ) {
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
