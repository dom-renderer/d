<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TimeSpentOnJob;
use Illuminate\Http\Request;
use App\Models\JobSignature;
use App\Models\Notification;
use Illuminate\Support\Str;
use App\Models\Requisition;
use App\Models\DeviceToken;
use App\Models\JobService;
use App\Helpers\Helper;
use App\Models\JobLog;
use App\Models\User;
use App\Models\Job;
use Carbon\Carbon;

class JobController extends \App\Http\Controllers\Controller
{
    public $successStatus = 200;

    public function before_login( Request $request ) {
        if ( !empty($request->dms_action) ) {
            if ( $request->dms_action == 'login' ) {
                $validator = Validator::make($request->all(), [
                    'phone_number' => 'required|exists:users,phone_number',
                    'password' => 'required'
                ]);

                if ($validator->fails()) { 
                    $errorString = implode(",",$validator->messages()->all());
                    return response()->json(['status' => false, 'message' => $errorString], 401);
                }

                if (Auth::attempt($request->only( 'phone_number', 'password' ))) {

                    $user = Auth::user();
                    $roles = $user->roles->pluck('id', 'name')->toArray();

                    if (empty($roles)) {
                        return response()->json(['status' => false, 'message' => 'User is not valid for the login.'], 401);
                    } else if (!isset($roles['technician'])) {
                        return response()->json(['status' => false, 'message' => 'User is not valid for the login!'], 401);
                    }

                    if ($user->status != 1) {
                        return response()->json(['status' => false, 'message' => 'Your account is disabled by administrator!'], 401);
                    } else {
                        $success = [
                            'token' => $user->createToken('DMS')->accessToken,
                            'id' => $user->id,
                            'email' => $user->email,
                            'name' => $user->name,
                            'dial_code' => $user->dial_code,
                            'phone_number' => $user->phone_number,
                            'profile' => $user->userprofile,
                            'currency' => $user->currencyr,
                            'address_line_1' => $user->address_line_1,
                            'address_line_2' => $user->address_line_2,
                            'city' => $user->city,
                            'state' => $user->state,
                            'country' => $user->country,
                            'pincode' => $user->pincode,
                            'latitude' => $user->latitude,
                            'longitude' => $user->longitude,
                            'location_url' => $user->location_url,
                            'roles' => collect($roles)->map(function ($value, $key) {
                                return ['id' => $value, 'name' => $key];
                            })->values()
                        ];

                        return response()->json(['status' => true, 'message' => 'Succeed', 'data' => $success]);
                    }
                } else {
                    return response()->json(['status' => false, 'message' => 'Password is incorrect!'], 401);
                }
            }
        }
        return response()->json( [ 'status' => false, 'message' => 'Something went wrong, please try again' ], 401 );
    }

    public function after_login( Request $request )
    {
        if ( !empty($request->dms_action) ) {
            $user_data = Auth::user();
            if ( $request->dms_action == 'update_profile' ) {
                $validator = Validator::make($request->all(), [
                    'password'      => 'nullable|min:6|confirmed',
                    'profile_name'  => 'nullable|string|max:255',
                    'email'         => 'nullable|email|unique:users,email,' . Auth::id(),
                    'profile_img'   => 'nullable',
                ]);

                if ( $validator->fails() ) {
                    $errorString = implode(",",$validator->messages()->all());
                    return response()->json( [ 'status' => false, 'message' => $errorString ], 401 );
                }
                $user = Auth::user();
                if ( $request->filled( 'password' ) ) {
                    $user->password = $request->password;
                }
                if ( $request->filled( 'profile_name' ) ) {
                    $user->name = $request->profile_name;
                }
                if ( $request->filled( 'email' ) ) {
                    $user->email = $request->email;
                }
                if ( $request->hasFile( 'profile_img' ) ) {
                    if ( !empty($user->profile) ) {
                        $oldPath = public_path('storage/users/profile/' . $user->profile);
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }

                    $file = $request->file('profile_img');
                    $filename = uniqid('profile_') . '.' . $file->getClientOriginalExtension();
                    $uploadPath = public_path('storage/users/profile');
                    $file->move($uploadPath, $filename);
                    $user->profile = $filename;
                } else if ( $request->filled( 'profile_img' ) && !Str::startsWith( $request->profile_img, 'http' ) ) {
                    if ( !empty($user->profile) ) {
                        $oldPath = public_path('storage/users/profile/' . $user->profile);
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }

                    $image = $request->profile_img;

                    if ( preg_match( '/^data:image\/(\w+);base64,/', $image, $type ) ) {
                        $image = substr( $image, strpos($image, ',') + 1 );
                        $type = strtolower( $type[1] );

                        $image = str_replace( ' ', '+', $image );
                        $imageData = base64_decode( $image );

                        if ( $imageData === false ) {
                            return response()->json( [ 'status' => false, 'message' => 'Invalid base64 image' ], 400 );
                        }

                        $filename = uniqid( 'profile_' ) . '.' . $type;
                        $uploadPath = public_path( 'storage/users/profile' );
                        file_put_contents( $uploadPath . '/' . $filename, $imageData );

                        $user->profile = $filename;
                    }
                }

                $user->save();
                return response()->json([
                    'status' => true,
                    'message' => 'Profile updated successfully',
                    'data' => array(
                        'id' => $user->id,
                        'email' => $user->email,
                        'dial_code' => $user->dial_code,
                        'phone_number' => $user->phone_number,
                        'profile' => $user->userprofile,
                        'currency' => $user->currencyr,
                        'address_line_1' => $user->address_line_1,
                        'address_line_2' => $user->address_line_2,
                        'city' => $user->city,
                        'state' => $user->state,
                        'country' => $user->country,
                        'pincode' => $user->pincode,
                        'latitude' => $user->latitude,
                        'longitude' => $user->longitude,
                        'location_url' => $user->location_url
                    ),
                ]);

            } else if ( $request->dms_action == 'reset_password' ) {
                $validator = Validator::make($request->all(), [
                    'phone_number' => 'required|exists:users,phone_number',
                    'password'     => 'required|min:6|confirmed',
                ]);

                if ( $validator->fails() ) { 
                    $errorString = implode(",",$validator->messages()->all());
                    return response()->json( [ 'status' => false, 'message' => $errorString ], 401 );
                }

                try {
                    $user = User::where('phone_number', $request->phone_number)->first();

                    if (!$user) {
                        return response()->json(['status' => false, 'message' => 'User not found'], 404);
                    }

                    $user->password = $request->password;
                    $user->save();

                    return response()->json([
                        'status' => true,
                        'message' => 'Password reset successfully. Please login with new password.'
                    ]);
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Something went wrong!'], 500);
                }
            } else if ( $request->dms_action == 'get_notification' ) {
                $perPage = $request->input('per_page', 20);

                $notifications = Notification::where( 'user_id', Auth::id() )
                    ->orderBy( 'created_at', 'desc' )
                    ->paginate( $perPage );

                $notifications->getCollection()->transform(function ( $notification ) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'type' => $notification->type,
                        'is_read' => $notification->read_at ? true : false,
                        'created_at' => $notification->created_at,
                    ];
                });
                return response()->json([
                    'status' => true,
                    'message' => 'get notification successfully',
                    'notification' => $notifications,
                ]);
            } else if ( $request->dms_action == 'get_dashboard_data' ) {
                $today = date('Y-m-d');
                $today_total_jobs = Job::whereHas( 'technicians', fn ($builder) => $builder->where( 'technician_id', $user_data->id ) )->whereDate( 'visiting_date', $today )->count();
                $today_total_completed_jobs = Job::whereHas( 'technicians', fn ($builder) => $builder->where( 'technician_id', $user_data->id ) )->whereDate( 'visiting_date', $today )
                        ->where( 'status', 'COMPLETED' )
                        ->count();
                $upcoming_jobs = Job::with( 'customer' )
                    ->whereHas( 'technicians', fn ($builder) => $builder->where( 'technician_id', $user_data->id ) )
                    ->whereDate( 'visiting_date', $today )
                    ->where( 'status', '!=', 'COMPLETED' )
                    ->orderBy( 'visiting_date', 'asc' )
                    ->get()->map(fn ($job) => $job->toAPIArray());
                return response()->json([
                    'status' => true,
                    'message' => 'get dashbaord data successfully',
                    'data' => array(
                        'today_total_jobs' => $today_total_jobs,
                        'today_total_completed_jobs' => $today_total_completed_jobs,
                        'upcoming_jobs' => $upcoming_jobs,
                    ),
                ]);
            } else if ( $request->dms_action == 'get_job_list' ) {
                $perPage = $request->input( 'per_page', 20 );

                $job_data = Job::with( 'customer' )->whereHas( 'engineers' )->whereHas( 'technicians', fn ($builder) => $builder->where( 'technician_id', $user_data->id ) );

                if ( !empty($request->status) && strtoupper( $request->status ) != 'ALL' ) {
                    $status = strtoupper( str_replace( ' ', '', $request->status ) );
                    $job_data->where( 'status', $status );
                }

                if ( !empty($request->search) ) {
                    $job_data->where( 'title', 'LIKE', "%{$request->search}%" );
                }

                if ( !empty($request->customer_id) ) {
                    $job_data->where( 'customer_id', $request->customer_id );
                }

                $job_data = $job_data->orderBy( 'visiting_date', 'asc' )->paginate( $perPage );
                $job_data->getCollection()->transform(fn ( $job ) => $job->toAPIArray() );

                $customerIds = Job::whereHas( 'engineers' )->whereHas( 'technicians', fn($q) => $q->where( 'technician_id', $user_data->id ) )
                    ->pluck( 'customer_id' )
                    ->unique();
                $customer_data = User::select( 'name', 'id' )->whereIn( 'id', $customerIds )->pluck( 'name', 'id' );

                return response()->json([
                    'status' => true,
                    'message' => 'get job list successfully',
                    'job_data' => $job_data,
                    'customer_data' => $customer_data,
                ]);
            } else if ( $request->dms_action == 'get_job_details' ) {
                $validator = Validator::make($request->all(), [
                    'job_id' => 'required|exists:job,id',
                ]);

                if ($validator->fails()) { 
                    $errorString = implode(",",$validator->messages()->all());
                    return response()->json(['status' => false, 'message' => $errorString], 401);
                }

                $job_row = Job::with([ 'customer', 'services', 'singleRequistion.items', 'singleRequistion.items.product', 'singleRequistion.items.product.category', 'singleRequistion.items.vendor' ])
                    ->whereHas( 'technicians', fn ($builder) => $builder->where( 'technician_id', $user_data->id ) )
                    ->where( 'id', $request->job_id )
                    ->first();

                if (isset($job_row->id)) {
                    $job_row = $job_row->toAPIArray( 'job_details' );
                }
                
                if ( !empty($job_row) ) {
                    return response()->json([
                        'status' => true,
                        'message' => 'get job details successfully',
                        'job_details' => $job_row,
                    ]);
                }
            } else if ( $request->dms_action == 'add_job_image' ) {
                $validator = Validator::make($request->all(), [
                    'attachment'   => 'required',
                    'attachment.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
                if ( $validator->fails() ) {
                    $errorString = implode(",",$validator->messages()->all());
                    return response()->json( [ 'status' => false, 'message' => $errorString ], 401 );
                }
                $job_row = Job::whereHas( 'technicians', fn ($builder) => $builder->where( 'technician_id', $user_data->id ) )
                    ->where( 'id', $request->job_id )
                    ->first();
                
                if ( !empty($job_row) ) {
                    $attachment_arr = !empty($job_row->attachment) ? json_decode( $job_row->attachment, true ) : array();
                    $folder = 'job_attachment';
                    if ( $request->hasFile( 'attachment' ) ) {
                        $file = $request->file('attachment');
                        $filename = Str::random( 20 ) . '.' . $file->getClientOriginalExtension();

                        $file->storeAs( $folder, $filename, 'public' );
                        $attachment_arr[] = $filename;
                    } else if ( $request->filled( 'attachment' ) && !Str::startsWith( $request->attachment, 'http' ) ) {
                        $image = $request->attachment;
    
                        if ( preg_match( '/^data:image\/(\w+);base64,/', $image, $type ) ) {
                            $image = substr( $image, strpos($image, ',') + 1 );
                            $type = strtolower( $type[1] );

                            $image = str_replace( ' ', '+', $image );
                            $imageData = base64_decode( $image );
    
                            if ( $imageData === false ) {
                                return response()->json( [ 'status' => false, 'message' => 'Invalid base64 image' ], 400 );
                            }
    
                            $filename = Str::random( 20 ) . '.' . $type;
                            $uploadPath = public_path( 'storage/' . $folder );
                            file_put_contents( $uploadPath . '/' . $filename, $imageData );
    
                            $attachment_arr[] = $filename;
                        }
                    }
                    $job_row->attachment = json_encode( $attachment_arr );
                    $job_row->save();
                    return response()->json([
                        'status' => true,
                        'message' => 'Job images add successfully',
                        'attachments' => $job_row->jobattachment,
                    ]);
                }
            } else if ( $request->dms_action == 'delete_job_image' ) {
                $validator = Validator::make($request->all(), [
                    'job_id'   => 'required|exists:job,id',
                    'image'    => 'required|string',
                ]);
                
                if ( $validator->fails() ) {
                    $errorString = implode(",",$validator->messages()->all());
                    return response()->json( [ 'status' => false, 'message' => $errorString ], 401 );
                }
                
                $job_row = Job::whereHas( 'technicians', fn ($builder) => $builder->where( 'technician_id', $user_data->id ) )
                    ->where( 'id', $request->job_id )
                    ->first();
                
                if ( !empty($job_row) ) {
                    $attachment_arr = !empty($job_row->attachment) ? json_decode( $job_row->attachment, true ) : array();
                    
                    $key = array_search( $request->image, $attachment_arr );
                    
                    if ( $key !== false ) {

                        unset( $attachment_arr[$key] );
                        
                        $filePath = public_path('storage/job_attachment/' . $request->image);
                        if ( file_exists($filePath) ) {
                            @unlink($filePath);
                        }
                        
                        $attachment_arr = array_values( $attachment_arr );
                        $job_row->attachment = json_encode( $attachment_arr );
                        $job_row->save();
                        
                        return response()->json([
                            'status' => true,
                            'message' => 'Job image deleted successfully',
                            'attachments' => $job_row->jobattachment,
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Image not found in job attachments',
                        ], 404);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Job not found or you do not have access to this job',
                    ], 404);
                }
                
            } else if ( $request->dms_action == 'job_send_to_workshop' ) {
                $validator = Validator::make($request->all(), [
                    'job_id' => 'required|exists:job,id',
                ]);
                if ( $validator->fails() ) {
                    $errorString = implode(",",$validator->messages()->all());
                    return response()->json( [ 'status' => false, 'message' => $errorString ], 401 );
                }

                $id = $request->job_id;
                $job_row = Job::find( $id );
                if ( !empty($job_row) ) {
                    $job_row->in_workshop = 1;
                    $job_row->save();
                    return response()->json( [ 'status' => true, 'message' => 'Job send to workshop successfully.' ] );
                }
            } else if ( $request->dms_action == 'job_start' || $request->dms_action == 'job_end' ) {
                $validator = Validator::make($request->all(), [
                    'job_id' => 'required|exists:job,id',
                ]);

                if ($validator->fails()) { 
                    return response()->json(['status' => false, 'message' => implode(",", $validator->messages()->all())], 401);
                }

                $job = Job::find( $request->job_id );

                if ( in_array($job->status, ['COMPLETED', 'CANCELLED']) ) {
                    return response()->json(['status' => false, 'message' => 'Either job is Cancelled or Completed']);
                }

                $attendance = TimeSpentOnJob::where('job_id', $job->id)
                    ->where( 'technician_id', $user_data->id )
                    ->whereNull( 'punch_out_at' )
                    ->first();

                if ( $attendance ) {
                    if ( $request->dms_action == 'job_end' ) {
                        $attendance->update([
                            'punch_out_at' => now(),
                            'status' => 'PUNCHED_OUT',
                        ]);
                        $totalTime = $this->calculateTotalTime( $job->id, $user_data->id );
                        return response()->json( [ 'status' => true, 'message' => 'Job End successfully.', 'total_time' => $totalTime ] );
                    } else {
                        return response()->json( [ 'status' => true, 'message' => 'You are already start Job.' ] );
                    }
                } else {
                    if ( $request->dms_action == 'job_start' ) {
                        TimeSpentOnJob::create([
                            'job_id' => $job->id,
                            'date' => date('Y-m-d'),
                            'technician_id' => $user_data->id,
                            'punch_in_at' => now(),
                            'status' => 'PUNCHED_IN',
                        ]);
                        if ( $job->status == 'PENDING' ) {
                            $job->status = 'INPROGRESS';
                            $job->save();

                            $notification_user = User::active()->whereHas('roles', function ($q) {
                                                    $q->where( 'name', 'admin' );
                                                })->pluck( 'id' )->toArray();
                            if ( User::find( $job->assigner_id )->hasRole( 'job-coordinator' ) ) {
                                $notification_user[] = $job->assigner_id;
                            }
                            Helper::sendNotificationUser( 'job-started', $notification_user, $job->id );
                        }
                        return response()->json( [ 'status' => true, 'message' => 'Job start successfully.' ] );
                    } else {
                        return response()->json( [ 'status' => false, 'message' => 'No active Job found.' ] );
                    }
                }
            } else if ( $request->dms_action == 'job_change_status' ) {
                $status_arr = Helper::getJobStatusList();
                $statuses = !empty($status_arr) ? implode( ',', $status_arr ) : '';

                $validator = Validator::make($request->all(), [
                    'job_id' => 'required|exists:job,id',
                    'status' => 'required|string|in:' . $statuses,
                    'note' => 'nullable|required_if:status,ONHOLD,CANCELLED|string',
                    'cancel_amount' => 'nullable|required_if:status,CANCELLED|numeric|min:0',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => implode(",", $validator->messages()->all())], 401);
                }

                $job = Job::find( $request->job_id );

                if ( $job->status != 'ONHOLD' && $request->status == 'ONHOLD' ) {
                    if ( User::find( $job->assigner_id )->hasRole( 'job-coordinator' ) ) {
                        $notification_user = array( $job->assigner_id );
                        Helper::sendNotificationUser( 'job-paused', $notification_user, $job->id );
                    }
                    $job->hold_note = $request->note;
                }
                if ( $job->status != 'CANCELLED' && $request->status == 'CANCELLED' ) {
                    $job->cancellation_amount = $request->cancel_amount;
                    $job->cancellation_note = $request->note;
                }
                $job->status = $request->status;
                $job->save();

                return response()->json( [ 'status' => true, 'message' => 'Job status changed successfully.' ] );
            } else if ( $request->dms_action == 'get_requisitions' ) {

                $requisitionRow = Requisition::with(['job', 'items', 'items.product', 'items.category', 'items.vendor'])
                // ->whereHas('job.technicians', fn ($queryBuilder) => $queryBuilder->where('technician_id', auth()->user()->id))
                ->get()
                ->toArray();
                    
                if ( !empty($requisitionRow) ) {
                    return response()->json([
                        'status' => true,
                        'message' => 'All requisition fetched successfully',
                        'job_details' => $requisitionRow,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'No requisitions found',
                        'job_details' => null
                    ]);
                }

            } else if ( $request->dms_action == 'get_categories' ) {
                
                $categories = \App\Models\Category::where('status', 1)->get();

                return response()->json([
                    'status' => true,
                    'message' => 'All categories fetched successfully',
                    'job_details' => $categories,
                ]);

            } else if ( $request->dms_action == 'get_products' ) {

                $products = \App\Models\Product::when(request()->has('category_id'), function ($builder) {
                    $builder->where('category_id', request('category_id'));
                })->where('status', 1)->get();

                return response()->json([
                    'status' => true,
                    'message' => 'All products fetched successfully',
                    'job_details' => $products,
                ]);

            } else if ( $request->dms_action == 'get_customers' ) {

                $customers = User::whereHas('roles', function ($builder) {
                    $builder->where('name', 'customer');
                })->where('status', 1)->get();

                return response()->json([
                    'status' => true,
                    'message' => 'All customers fetched successfully',
                    'job_details' => $customers,
                ]);

            } else if ( $request->dms_action == 'job_timeline' ) {

                $logs = JobLog::with('user')->where('job_id', $request->job_id)->get();

                return response()->json([
                    'status' => true,
                    'message' => 'All timeline fetched successfully',
                    'job_details' => $logs,
                ]);

            } else if ( $request->dms_action == 'get_vendors' ) {

                $vendors = User::whereHas('roles', fn ($builder) => $builder->where('id', 7))->where('status', 1)->get();

                return response()->json([
                    'status' => true,
                    'message' => 'All vendors fetched successfully',
                    'job_details' => $vendors,
                ]);

            } else if ( $request->dms_action == 'get_sub_contractors' ) {

                $subContractors = User::whereHas('roles', fn ($builder) => $builder->where('id', 6))->where('status', 1)->get();

                return response()->json([
                    'status' => true,
                    'message' => 'All sub-contractors fetched successfully',
                    'job_details' => $subContractors,
                ]);

            } else if ( $request->dms_action == 'get_job_requisition' ) {

                $validator = Validator::make($request->all(), [
                    'job_id' => 'required|exists:job,id',
                ]);

                if ($validator->fails()) { 
                    $errorString = implode(",",$validator->messages()->all());
                    return response()->json(['status' => false, 'message' => $errorString], 401);
                }

                $job_row = Job::with( ['singleRequistion.items', 'singleRequistion.items.product.category', 'singleRequistion.items.product', 'singleRequistion.items.vendor'] )
                    ->where( 'id', $request->job_id )
                    ->first()
                    ->toAPIArray( 'requisition_details' );
                    
                if ( !empty($job_row) ) {
                    return response()->json([
                        'status' => true,
                        'message' => 'get job details successfully',
                        'job_details' => $job_row,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'No requisition found',
                        'job_details' => [],
                    ]);
                }

            } else if ( $request->dms_action == 'get_profile' ) {

                $user = auth()->user();

                return response()->json([
                    'status' => true,
                    'message' => 'Profile fetched successfully',
                    'data' => array(
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'dial_code' => $user->dial_code,
                        'phone_number' => $user->phone_number,
                        'profile' => $user->userprofile,
                        'currency' => $user->currencyr,
                        'address_line_1' => $user->address_line_1,
                        'address_line_2' => $user->address_line_2,
                        'city' => $user->city,
                        'state' => $user->state,
                        'country' => $user->country,
                        'pincode' => $user->pincode,
                        'latitude' => $user->latitude,
                        'longitude' => $user->longitude,
                        'location_url' => $user->location_url
                    ),
                ]);

            } else if ( $request->dms_action == 'add_requisition_item' ) {

                $validator = Validator::make($request->all(), [
                    'job_id' => 'required|exists:job,id',
                    'requisitions_id' => 'nullable|exists:requisitions,id',
                    'items' => 'required|array',
                    'items.*.id' => 'nullable|exists:requisition_items,id',
                    'items.*.type' => 'required|in:INVENTORY,VENDOR',
                    'items.*.vendor_id' => 'required_if:items.*.type,VENDOR',
                    'items.*.product_id' => 'nullable|exists:products,id',
                    'items.*.product_name' => 'required_if:items.*.type,VENDOR|string',
                    'items.*.description' => 'nullable|string',
                    'items.*.quantity' => 'required|numeric',
                    'items.*.amount' => 'required|numeric',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => implode(",", $validator->messages()->all())], 401);
                }

                \DB::beginTransaction();

                try {
                    $add_new_item = 0;
                    $requisition = Requisition::updateOrCreate(
                        ['id' => $request->requisitions_id],
                        [
                            'job_id'   => $request->job_id,
                            'code'     => Helper::requisitionCode(),
                            'added_by' => auth()->user()->id,
                        ]
                    );
                    if ( empty($request->requisitions_id) ) {
                        $add_new_item = 1;
                    }

                    $receivedItemIds = [];

                    if ($request->has('items')) {
                        foreach ($request->items as $item) {
                            if ( empty($item['id']) ) {
                                $add_new_item = 1;
                            }

                            if (isset($item['vendor_id']) && User::where('id', $item['vendor_id'])->doesntExist()) {
                                $item['vendor_id'] = User::updateOrCreate([
                                    'name' => $item['vendor_id']
                                ])->id;
                            }

                            $total = $item['amount'] * $item['quantity'];
                            $requisitionItem = \App\Models\RequisitionItem::updateOrCreate(
                                ['id' => $item['id'] ?? null],
                                [
                                    'requisition_id' => $requisition->id,
                                    'vendor_id'      => $item['vendor_id'] ?? null,
                                    'type'           => $item['type'] ?? 'INVENTORY',
                                    'product_id'     => $item['product_id'] ?? null,
                                    'product_name'   => $item['product_name'] ?? null,
                                    'description'    => $item['description'] ?? null,
                                    'amount'         => $item['amount'] ?? 0,
                                    'quantity'       => $item['quantity'] ?? 0,
                                    'total'          => $total,
                                ]
                            );

                            $receivedItemIds[] = $requisitionItem->id;
                        }
                    }

                    if ( $add_new_item == 1 ) {
                        $job_data = Job::with('engineers')->find( $request->job_id );
                        $notification_user = array();
                        if ( !empty($job_data->engineers) ) {
                            $notification_user = $job_data->engineers->pluck('technician_id')->toArray();
                        }
                        if ( User::find( $job_data->assigner_id )->hasRole( 'job-coordinator' ) ) {
                            $notification_user[] = $job_data->assigner_id;
                        }
                        Helper::sendNotificationUser( 'item-request-created', $notification_user, $request->job_id );
                    }

                    \DB::commit();

                    return response()->json([
                        'status' => true,
                        'message' => 'Requisition Updated'
                    ]);

                } catch (\Exception $e) {
                    \DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Something went wrong, please try again',
                        'exception' => $e->getMessage()
                    ]);
                }

            } else if ( $request->dms_action == 'edit_requisition_item' ) {

            $validator = Validator::make($request->all(), [
                'job_id' => 'required|exists:job,id',
                'requisitions_id' => 'nullable|exists:requisitions,id',
                'id' => 'nullable|exists:requisition_items,id',
                'type' => 'required|in:INVENTORY,VENDOR',
                'vendor_id' => 'required_if:items.*.type,VENDOR',
                'product_id' => 'nullable|exists:products,id',
                'product_name' => 'required_if:items.*.type,VENDOR|string',
                'description' => 'nullable|string',
                'quantity' => 'required|numeric',
                'amount' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => implode(",", $validator->messages()->all())], 401);
            }

            \DB::beginTransaction();

            try {
                $vndr = $request->vendor_id;
                if (User::where('id', $vndr)->doesntExist()) {
                    $vndr = User::updateOrCreate([
                        'name' => $vndr
                    ])->id;
                }

                $total = ($request->amount ?? 0) * ($request->quantity ?? 0);
                $requisitionItem = \App\Models\RequisitionItem::where('requisition_id', $request->requisitions_id)
                ->where('id', $request->id)
                ->update([
                    'requisition_id' => $request->requisitions_id,
                    'vendor_id'      => $vndr,
                    'type'           => $request->type ?? 'INVENTORY',
                    'product_id'     => $request->product_id ?? null,
                    'product_name'   => $request->product_name ?? null,
                    'description'    => $request->description ?? null,
                    'amount'         => $request->amount ?? 0,
                    'quantity'       => $request->quantity ?? 0,
                    'total'          => $total,
                ]);

                $job_data = Job::with('engineers')->find( $request->job_id );
                $notification_user = array();
                if ( !empty($job_data->engineers) ) {
                    $notification_user = $job_data->engineers->pluck('technician_id')->toArray();
                }
                if ( User::find( $job_data->assigner_id )->hasRole( 'job-coordinator' ) ) {
                    $notification_user[] = $job_data->assigner_id;
                }
                Helper::sendNotificationUser( 'item-request-created', $notification_user, $request->job_id );

                \DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Item updated successfully'
                ]);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong, please try again',
                    'exception' => $e->getMessage()
                ]);
            }

            } else if ( $request->dms_action == 'delete_requisition_item' ) {

            $validator = Validator::make($request->all(), [
                'job_id' => 'required|exists:job,id',
                'requisitions_id' => 'nullable|exists:requisitions,id',
                'id' => 'nullable|exists:requisition_items,id'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => implode(",", $validator->messages()->all())], 401);
            }

            \DB::beginTransaction();

            try {
                \App\Models\RequisitionItem::where('requisition_id', $request->requisitions_id)
                ->where('id', $request->id)
                ->delete();
                
            \DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Item removed successfully'
            ]);

            } catch (\Exception $e) {
                    \DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong, please try again',
                    'exception' => $e->getMessage()
                ]);
            }

            } else if ( $request->dms_action == 'get_job_note_list' ) {
                $validator = Validator::make($request->all(), [
                    'job_id' => 'required|exists:job,id',
                ]);

                if ( $validator->fails() ) { 
                    $errorString = implode(",",$validator->messages()->all());
                    return response()->json(['status' => false, 'message' => $errorString], 401);
                }

                $job_logs = JobLog::with('user')->where( 'type', 'work_note' )
                ->where('job_id', $request->job_id)->get()->map(function ($log) {
                                return [
                                    'id'          => $log->id,
                                    'title'       => $log->title,
                                    'description' => $log->description,
                                    'created_at'  => $log->created_at,
                                    'user'        => $log->user
                                ];
                            });
                if ( !empty($job_logs) ) {
                    return response()->json([
                        'status' => true,
                        'message' => 'get Job work note successfully',
                        'job_note' => $job_logs,
                    ]);
                }
            } else if ( $request->dms_action == 'add_job_note' ) {
                $validator = Validator::make($request->all(), [
                    'job_id'      => 'required|exists:job,id',
                    'title'       => 'required|string|max:255',
                    'description' => 'nullable|string',
                ]);

                if ( $validator->fails() ) { 
                    $errorString = implode(",",$validator->messages()->all());
                    return response()->json(['status' => false, 'message' => $errorString], 401);
                }
                $job_logs = JobLog::create([
                    'job_id'      => $request->job_id,
                    'user_id'     => $user_data->id,
                    'title'       => $request->title,
                    'description' => $request->description,
                    'type'        => 'work_note',
                ]);
                if ( !empty($job_logs) ) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Job work note created successfully',
                        'job_note' => $job_logs,
                    ]);
                }
            } else if ( $request->dms_action == 'complete_job' ) {
                $validator = Validator::make($request->all(), [
                    'job_id'      => 'required|exists:job,id',
                    'customer_signature'   => 'required',
                    'additional_note'   => 'nullable',
                ]);
                if ($validator->fails()) { 
                    $errorString = implode(",",$validator->messages()->all());
                    return response()->json(['status' => false, 'message' => $errorString], 401);
                }
                $job_data = Job::find( $request->job_id );
                if ( $job_data->status == 'COMPLETED' ) {
                    return response()->json([
                        'status'   => false,
                        'message'  => 'Job is already completed.',
                    ]);
                }
                $job_data->status = 'COMPLETED';
                $job_data->completed_at = now();
                $job_data->save();

                $signatureFilename = null;

                $folder = 'signatures';
                if ( $request->hasFile( 'customer_signature' ) ) {
                    $file = $request->file( 'customer_signature' );
                    $signatureFilename = Str::random(20) . '.' . $file->getClientOriginalExtension();
                    $file->storeAs($folder, $signatureFilename, 'public');

                } elseif ($request->filled('customer_signature') && !Str::startsWith($request->customer_signature, 'http')) {
                    $image = $request->customer_signature;

                    if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
                        $image = substr($image, strpos($image, ',') + 1);
                        $type = strtolower($type[1]);

                        $image = str_replace(' ', '+', $image);
                        $imageData = base64_decode($image);

                        if ($imageData === false) {
                            return response()->json([
                                'status'  => false,
                                'message' => 'Invalid base64 image'
                            ], 400);
                        }

                        $signatureFilename = Str::random(20) . '.' . $type;
                        $uploadPath = public_path('storage/' . $folder);
                        if (!file_exists($uploadPath)) {
                            mkdir($uploadPath, 0777, true);
                        }
                        file_put_contents( $uploadPath . '/' . $signatureFilename, $imageData );
                    }
                }
                JobSignature::create([
                    'job_id'    => $request->job_id,
                    'user_id'   => $job_data->customer_id,
                    'signature' => $signatureFilename,
                ]);

                $title = 'Job Completed';
                $desc = !empty($request->additional_note) ? $request->additional_note : null;
                Helper::addJobLog( $request->job_id, $user_data->id, $title, $desc );

                $notification_user = array();
                if ( User::find( $job_data->assigner_id )->hasRole( 'job-coordinator' ) ) {
                    $notification_user[] = $job_data->assigner_id;
                }
                Helper::sendNotificationUser( 'job-completed', $notification_user, $job_data->id );

                $notification_user2 = User::active()->whereHas('roles', function ($q) {
                                                    $q->where( 'name', 'admin' );
                                                })->pluck( 'id' )->toArray();
                Helper::sendNotificationUser( 'signature-captured', $notification_user2, $job_data->id );

                return response()->json([
                    'status'   => true,
                    'message'  => 'Job completed successfully',
                ]);
            } else if ( $request->dms_action == 'update_job_services' ) {
                $validator = Validator::make($request->all(), [
                    'job_id'                        => 'required|exists:job,id',
                    'services_data'                 => 'nullable|array',
                    'services_data.*.title'         => 'required_with:services_data|string|max:255',
                    'services_data.*.description'   => 'nullable|string',
                    'services_data.*.quantity'      => 'required_with:services_data|integer|min:1',
                    'services_data.*.price'         => 'required_with:services_data|numeric|min:0',
                ]);
                if ($validator->fails()) {
                    $errorString = implode(",",$validator->messages()->all());
                    return response()->json(['status' => false, 'message' => $errorString], 401);
                }

                JobService::where( 'job_id', $request->job_id )->delete();
                if ( !empty($request->services_data) ) {
                    foreach ($request->services_data as $service) {
                        JobService::create([
                            'job_id'     => $request->job_id,
                            'title'      => $service['title'],
                            'description'=> $service['description'] ?? null,
                            'quantity'   => $service['quantity'],
                            'price'      => $service['price'],
                            'total_amount' => $service['total_amount']
                        ]);
                    }
                }

                if ( !empty($request->additional_note) ) {
                    $title = 'Job Services Update';
                    Helper::addJobLog( $request->job_id, $user_data->id, $title, $request->additional_note );
                }

                $job_row = Job::with( [ 'customer', 'services', 'singleRequistion.items', 'singleRequistion.items.product', 'singleRequistion.items.product.category', 'singleRequistion.items.vendor' ] )
                    ->whereHas( 'technicians', fn ($builder) => $builder->where( 'technician_id', $user_data->id ) )
                    ->where( 'id', $request->job_id )
                    ->first()->toAPIArray( 'job_details' );

                return response()->json([
                    'status'  => true,
                    'message' => 'Job updated successfully',
                    'job_details' => $job_row,
                ]);
            } else {
                return response()->json( [ 'status' => false, 'message' => 'Action is wrong.' ], 401 );
            }
        }
        return response()->json( [ 'status' => false, 'message' => 'Something went wrong, please try again' ], 401 );
    }

    public function jobs(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $query = Job::with(['customer', 'assigner', 'technicians.technician', 'materials.product', 'expertise.expertise', 'requisitions.product', 'requisitions.addedby', 'requisitions.approvedby', 'requisitions.rejectedby'])
        ->whereHas('technicians', fn ($builder) => $builder->where('technician_id', auth()->user()->id))
        ->when(in_array($request->status, ['PENDING','INPROGRESS','COMPLETED','CANCELLED']), fn ($builder) => $builder->where('status', request('status')))
        ->when($request->filled('from'), fn ($builder) => $builder->where(DB::raw("DATE_FORMAT(visiting_date, '%Y-%m-%d')"), '>=', date('Y-m-d', strtotime($request->from))))
        ->when($request->filled('to'), fn ($builder) => $builder->where(DB::raw("DATE_FORMAT(visiting_date, '%Y-%m-%d')"), '<=', date('Y-m-d', strtotime($request->to))))
        ->when(!$request->filled('from') && !$request->filled('to'),fn ($builder) => $builder->where(DB::raw("DATE_FORMAT(visiting_date, '%Y-%m-%d')"), date('Y-m-d')));

        $total = $query->count();
        $jobs = $query->skip(($page - 1) * $perPage)
                       ->take($perPage)
                       ->get();

        return response()->json([
            'data' => $jobs,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
            ],
        ]);
    }

    public function punchIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:job,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) { 
            return response()->json(['status' => false, 'message' => implode(",", $validator->messages()->all())], 401);
        }

        $job = Job::find($request->job_id);

        if (!$job || in_array($job->status, ['COMPLETED', 'CANCELLED'])) {
            return response()->json(['status' => false, 'message' => 'Either job not found or is completed']);
        }

        $technicianId = $request->user_id;

        $existingPunch = TimeSpentOnJob::where('job_id', $job->id)
            ->where('technician_id', $technicianId)
            ->whereNull('punch_out_at')
            ->first();

        if ($existingPunch) {
            return response()->json(['status' => false, 'message' => 'You are already punched in.']);
        }

        $attendance = TimeSpentOnJob::create([
            'job_id' => $job->id,
            'date' => date('Y-m-d'),
            'technician_id' => $technicianId,
            'punch_in_at' => now(),
            'status' => 'PUNCHED_IN',
        ]);

        if ($job->status == 'PENDING') {
            $job->status = 'INPROGRESS';
            $job->save();
        }

        $totalTime = $this->calculateTotalTime($job->id, $technicianId);

        return response()->json(['status' => true, 'message' => 'Punched in successfully.', 'data' => $attendance, 'total_time_spent' => $totalTime]);
    }

    public function punchOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:job,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) { 
            return response()->json(['status' => false, 'message' => implode(",", $validator->messages()->all())], 401);
        }

        $technicianId = $request->user_id;
        $job = Job::find($request->job_id);

        if (!$job) {
            return response()->json(['status' => false, 'message' => 'Either job not found or is completed']);
        }

        $attendance = TimeSpentOnJob::where('job_id', $job->id)
            ->where('technician_id', $technicianId)
            ->whereNull('punch_out_at')
            ->latest('punch_in_at')
            ->first();

        if (!$attendance) {
            return response()->json(['status' => false, 'message' => 'No active punch-in found.']);
        }

        $attendance->update([
            'punch_out_at' => now(),
            'status' => 'PUNCHED_OUT',
        ]);

        $totalTime = $this->calculateTotalTime($job->id, $technicianId);

        return response()->json(['status' => true, 'message' => 'Punched out successfully.', 'data' => $attendance, 'total_time_spent' => $totalTime]);
    }

    private function calculateTotalTime($jobId, $technicianId)
    {
        $records = TimeSpentOnJob::where('job_id', $jobId)
            ->where('technician_id', $technicianId)
            ->get();

        $totalSeconds = 0;

        foreach ($records as $record) {
            $punchIn = Carbon::parse($record->punch_in_at);
            $punchOut = $record->punch_out_at ? Carbon::parse($record->punch_out_at) : now();
            $totalSeconds += $punchIn->diffInSeconds($punchOut);
        }

        return gmdate("H:i:s", $totalSeconds);
    }

    public function deviceToken(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'token' => 'required'
        ]);

        if ($validator->fails()) { 
            $errorString = implode(",",$validator->messages()->all());
            return response()->json(['error'=>$errorString], 401);
        }

        if (DeviceToken::where('token', $request->token)->exists()) {
            if (DeviceToken::where(function ($builder) {
                return $builder->whereNull('user_id')->orWhere('user_id', '');
            })->where('token', $request->token)->exists()) {
    
                DeviceToken::where(function ($builder) {
                    return $builder->whereNull('user_id')->orWhere('user_id', '');
                })->where('token', $request->token)->update([
                    'user_id' => $request->user_id
                ]);
    
            } else {
                DeviceToken::updateOrCreate([
                    'token' => $request->token
                ],[
                    'user_id' => $request->user_id,
                    'token' => $request->token
                ]);
            }
        } else {
            DeviceToken::updateOrCreate([
                'user_id' => $request->user_id,
                'token' => $request->token
            ]);
        }

        return response()->json(['success' => "Device token saved successfully."], $this->successStatus);
    }

    public function removeDeviceToken(Request $request) {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'token' => 'required'
        ]);

        if ($validator->fails()) { 
            $errorString = implode(",",$validator->messages()->all());
            return response()->json(['error'=>$errorString], 401);
        }

        DeviceToken::where('user_id', $request->user_id)->where('token', $request->token)->update([
            'user_id' => null
        ]);
        
        return response()->json(['success' => "Device token removed from user successfully."], $this->successStatus);
    }
}