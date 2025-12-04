<?php

namespace App\Helpers;

use App\Jobs\CommonNotificationDispatcher;
use \Illuminate\Support\Facades\DB;
use App\Models\TimeSpentOnJob;
use Illuminate\Http\Request;
use App\Models\Expertise;
use App\Models\Country;
use App\Models\JobLog;
use App\Models\State;
use App\Models\City;
use App\Models\User;
use App\Models\Job;
use Carbon\Carbon;

class Helper {
    
    public static $defaulDialCode = 'bb';

    public static function title ($title = '') {
        if (!empty($title)) {
            return $title;
        } else if ($name = DB::table('settings')->first()?->name) {
            return $name;
        } else {
            return env('APP_NAME', '');
        }
    }

    public static function logo () {
        if ($name = DB::table('settings')->first()?->logo) {
            return url("settings-media/{$name}");
        } else {
            return url('assets/images/logo.png');
        }
    }

    public static function favicon () {
        if ($name = DB::table('settings')->first()?->favicon) {
            return url("settings-media/{$name}");
        } else {
            return url('assets/images/favicon.ico');
        }
    }

    public static function bgcolor ($bg = null) {
        if (!empty($bg)) {
            return $bg;
        } else if ($color = DB::table('settings')->first()?->theme_color) {
            return $color;
        } else {
            return '#3a082f';
        }
    }

    public function getCountries(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;
    
        $query = Country::query();
    
        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }
    
        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });

        return response()->json([
            'items' => $response->reverse()->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getStatesByCountry(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;
    
        $query = State::query()
        ->where('country_id', request('country_id'));
    
        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }
    
        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });

        return response()->json([
            'items' => $response->reverse()->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getCitiesByState(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;
    
        $query = City::query()
        ->where('state_id', $request->state_id);
    
        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }
    
        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });

        return response()->json([
            'items' => $response->reverse()->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getUsers(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $roles = $request->input('roles', null);
        $expertises = $request->input('expertises', null);
        $departments = $request->input('departments', null);
        $page = $request->input('page', 1);
        $addNewOption = $request->input('addNewOption', 0);
        $includeUserData = $request->input('includeUserData', false);
        $limit = 10;
    
        $query = User::query();
    
        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }

        if (!empty($roles)) {
            if (is_string($roles) && $roles == '*') {
                $query->whereHas('roles');
            } else {
                $roles = is_string($roles) ? explode(',', $roles) : (is_array($roles) ? $roles : []);
                $query->whereHas('roles', fn  ($builder) => $builder->whereIn('name', $roles));
            }
        }

        if (!empty($expertises)) {
            $expertises = is_string($expertises) ? explode(',', $expertises) : (is_array($expertises) ? $expertises : []);
            $query->whereHas('expertise', fn  ($builder) => $builder->whereIn('expertise_id', $expertises));
        }

        if (!empty($departments)) {
            $departments = is_string($departments) ? explode(',', $departments) : (is_array($departments) ? $departments : []);
            $query->whereHas('department', fn  ($builder) => $builder->whereIn('department_id', $departments));
        }
        
        // Handle new DMS time format (days:hours:minutes) with timestamp
        if (!empty($request->visiting_date) && (!empty($request->dms_days) || !empty($request->dms_hours) || !empty($request->dms_minutes))) {
            $startDate = \Carbon\Carbon::parse($request->visiting_date);
            $endDate = (clone $startDate);

            // Add time based on DMS format
            $days = (int) ($request->dms_days ?? 0);
            $hours = (int) ($request->dms_hours ?? 0);
            $minutes = (int) ($request->dms_minutes ?? 0);

            if ($days > 0) {
                $endDate->addDays($days);
            }
            if ($hours > 0) {
                $endDate->addHours($hours);
            }
            if ($minutes > 0) {
                $endDate->addMinutes($minutes);
            }

            $query->whereDoesntHave('jobs', function ($q) use ($startDate, $endDate, $request) {
                $q->where(function ($sub) use ($startDate, $endDate) {
                    $sub->where('visiting_date', '<', $endDate)
                        ->where(function ($timeSub) use ($startDate) {
                            $timeSub->whereNull('time_to_complete')
                                    ->orWhere('time_to_complete', '>', $startDate);
                        });
                });

                if (!empty($request->job_id)) {
                    $q->where('id', '!=', $request->job_id);
                }
            });
        }

        $data = $query->orderBy('name', 'ASC')->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) use ( $includeUserData, $request ) {
            // Handle new DMS time format (days:hours:minutes) in response mapping
            if (!empty($request->visiting_date) && (!empty($request->dms_days) || !empty($request->dms_hours) || !empty($request->dms_minutes))) {
                $startDate = \Carbon\Carbon::parse($request->visiting_date);
                $endDate = (clone $startDate);

                // Add time based on DMS format
                $days = (int) ($request->dms_days ?? 0);
                $hours = (int) ($request->dms_hours ?? 0);
                $minutes = (int) ($request->dms_minutes ?? 0);

                if ($days > 0) {
                    $endDate->addDays($days);
                }
                if ($hours > 0) {
                    $endDate->addHours($hours);
                }
                if ($minutes > 0) {
                    $endDate->addMinutes($minutes);
                }
            }

            $familiarity_client = '';
            if (!empty($request->customer_id)) {
                $job_exists = Job::where( 'customer_id', $request->customer_id )
                    ->when( !empty( $request->job_id ), function ($q) use ( $request ) {
                        $q->where( 'id', '!=', $request->job_id );
                    })
                    ->whereHas('technicians', function ($q) use ( $item ) {
                        $q->where( 'technician_id', $item->id );
                    })
                    ->exists();

                if ( $job_exists ) {
                    $familiarity_client = ' (Familiar)';
                }
            }

            $result = [
                'id' => $item->id,
                'text' => $item->name . $familiarity_client,
            ];
            
            if ($includeUserData) {
                $result['user'] = $item;
                $result['alternate_dial_code_iso'] = Helper::getIso2ByDialCode($item->alternate_dial_code);
            }
            
            return $result;
        });

        if ($addNewOption === '1' && $page == 1) {
            if ($response->count() > 0) {
                $response->push([
                    'id' => 'ADD_NEW_USER',
                    'text' => 'Add Customer'
                ])->unique();
            } else {
                $response->push([
                    'id' => 'ADD_NEW_USER',
                    'text' => 'Add Customer'
                ]);
            }
        }

        return response()->json([
            'items' => $response->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getCategories(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $except = $request->input('except', null);
        $limit = 10;

        $query = \App\Models\Category::query();
        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }

        if (!empty($except)) {
            $query = $query->where('id', '!=', $except);
        }

        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });
        return response()->json([
            'items' => $response->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getProducts(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $except = $request->input('except', null);
        $category = $request->input('category', null);
        $categoryId = $request->input('category_id', null);
        $limit = 10;

        $query = \App\Models\Product::query();
        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }

        if (!empty($except)) {
            $query = $query->where('id', '!=', $except);
        }

        if ($request->has('category')) {
            $query = $query->where('category_id', $category);
        }

        if ($request->has('category_id') && !empty($categoryId)) {
            $query = $query->where('category_id', $categoryId);
        }

        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name . ' - ' . $item->sku,
                'sku' => $item->sku,
                'price' => $item->amount
            ];
        });
        return response()->json([
            'items' => $response->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getProductCategories(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;

        $query = \App\Models\Category::query();
        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }
        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });
        return response()->json([
            'items' => $response->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getDepartments(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;

        $query = \App\Models\Department::query();
        
        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }

        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });
        return response()->json([
            'items' => $response->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getExpertise(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;

        $query = \App\Models\Expertise::query();

        if (!empty($queryString)) {
            $query->where('name', 'LIKE', "%{$queryString}%");
        }

        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });
        return response()->json([
            'items' => $response->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function getJobs(Request $request)
    {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;

        $query = Job::query();

        if ($request->has('exceptRequistionCreated') && $request->exceptRequistionCreated) {
            $query->whereDoesntHave('singleRequistion');
        }

        if (!empty($queryString)) {
            $query->where('code', 'LIKE', "%{$queryString}%");
        }

        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->code
            ];
        });
        return response()->json([
            'items' => $response->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public function notificationTemplates(Request $request) {
        $queryString = trim($request->searchQuery);
        $page = $request->input('page', 1);
        $limit = 10;

        $query = \App\Models\NotificationTemplate::active();

        if (!empty($queryString)) {
            $query->where('title', 'LIKE', "%{$queryString}%");
        }

        $data = $query->paginate($limit, ['*'], 'page', $page);
        $response = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->title . ' - ' . str_replace(',', ' | ', ucwords($item->type_display))
            ];
        });
        return response()->json([
            'items' => $response->values(),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }

    public static function getIso2ByDialCode($dialCode = null) {
        if (empty(trim($dialCode))) {
            $dialCode = '91';
        }

        $dialCode = trim(str_replace('+', '', $dialCode));
        return strtolower(Country::select('iso2')->where('phonecode', "+{$dialCode}")->orWhere('phonecode', $dialCode)->first()->iso2 ?? 'in');
    }

    public static function jobCode() {
        $orderNo = 0;
        
        if (\App\Models\Job::withTrashed()->orderBy('id', 'DESC')->first() !== null) {
            $orderNo = \App\Models\Job::withTrashed()->orderBy('id', 'DESC')->first()->id;
        }

        $orderNo += 1;
        $orderNo = sprintf('%07d', $orderNo);
        $orderNo = "JOB-{$orderNo}";

        return $orderNo;
    }

    public static function requisitionCode() {
        $orderNo = 0;
        
        if (\App\Models\Requisition::withTrashed()->orderBy('id', 'DESC')->first() !== null) {
            $orderNo = \App\Models\Requisition::withTrashed()->orderBy('id', 'DESC')->first()->id;
        }

        $orderNo += 1;
        $orderNo = sprintf('%07d', $orderNo);
        $orderNo = "REQ-{$orderNo}";

        return $orderNo;
    }

    public static function activateNavigation (...$routes) {
        if (!empty($routes)) {
            return in_array(Request::route()->getName(), $routes) ? 'active' : '';
        }

        return '';
    }

    public static function calculateTotalTimePerEmployee($jobId)
    {
        $records = TimeSpentOnJob::with('user')
            ->where('job_id', $jobId)
            ->get()
            ->groupBy('technician_id');

        $result = [];

        foreach ($records as $technicianId => $punches) {
            $totalSeconds = 0;

            foreach ($punches as $punch) {
                $punchIn = Carbon::parse($punch->punch_in_at);
                $punchOut = $punch->punch_out_at ? Carbon::parse($punch->punch_out_at) : now();
                $totalSeconds += $punchIn->diffInSeconds($punchOut);
            }

            $result[$technicianId] = [
                'technician_id' => $technicianId,
                'technician_name' => $punch->user->name ?? '',
                'technician_email' => $punch->user->email ?? '',
                'total_time_spent' => self::formatTime($totalSeconds),
            ];
        }

        return $result;
    }

    private static function formatTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%02dh %02dm %02ds', $hours, $minutes, $remainingSeconds);
    }

    public static function number_format($number)
    {
        return '$' . number_format($number, 2);
    }

    public static function getJobStatusList() {
        return [ 'PENDING', 'INPROGRESS', 'ONHOLD', 'COMPLETED', 'CANCELLED' ];
    }

    public static function calculateJobTotalTimeSpent( $jobId, $technicianId = null )
    {
        $records = TimeSpentOnJob::where('job_id', $jobId)
        ->when(!empty($technicianId), function ($builder) use ($technicianId) {
            $builder->where('technician_id', $technicianId);
        })
        ->get();

        $totalSeconds = 0;

        foreach ( $records as $record ) {
            $punchIn = Carbon::parse( $record->punch_in_at );
            $punchOut = $record->punch_out_at ? Carbon::parse( $record->punch_out_at ) : now();
            $totalSeconds += $punchIn->diffInSeconds( $punchOut );
        }

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);

        return $hours . 'h ' . $minutes . 'm';
    }

    public static function addJobLog( $job_id, $user_id, $title, $desc )
    {
        if ( !empty($job_id) ) {
            JobLog::create([
                'job_id'      => $job_id,
                'user_id'     => $user_id,
                'title'       => $title,
                'description' => $desc,
                'type'        => 'job_logs',
            ]);
        }
    }

    public static function sendPushNotification( $device_ids, $data )
    {
        $keyFilePath = storage_path('app/firebase.json');
        $client = new \Google\Client();
        $client->setAuthConfig($keyFilePath);
        $client->setScopes(['https://www.googleapis.com/auth/firebase.messaging']);
        $tokenArray = $client->fetchAccessTokenWithAssertion();
        if (isset($tokenArray['error'])) {
            return false;
        }
        $accessToken = $tokenArray['access_token'];

        foreach ($device_ids as $did) {
            $notification = json_encode([
                "message" => [
                    "token" => $did, 
                    "notification" => [
                        "body" => $data['description'],
                        "title" => $data['title'],
                    ],
                    "android" => [
                        "priority" => "HIGH",
                    ],
                ]
            ]);
            $headers = array(
                'Authorization: Bearer '.$accessToken,
                'Content-Type: application/json'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/dms-app-b5205/messages:send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $notification);
            curl_exec($ch);
        }

        return true;
    }

    public static function sendNotificationUser( $event, $user_id_arr, $job_id )
    {
        CommonNotificationDispatcher::dispatch($event, $user_id_arr, $job_id);
    }

    public static function notificationTemplateReplaceVariable( $content, $job_row, $user )
    {
        $technician_name = '';
        $technicians_ids = $job_row->technicians->pluck( 'technician_id' )->toArray();
        if ( !empty($technicians_ids) ) {
            $technicians_data = User::select( 'name' )->whereIn( 'id', $technicians_ids )->pluck( 'name' )->toArray();
            $technician_name = !empty($technicians_data) ? implode( ', ', $technicians_data ) : '';
        }

        $expertise_name = '';
        $expertise_ids = $job_row->expertise->pluck( 'expertise_id' )->toArray();
        if ( !empty($expertise_ids) ) {
            $expertise_data = Expertise::select( 'name' )->active()->whereIn( 'id', $expertise_ids )->pluck( 'name' )->toArray();
            $expertise_name = !empty($expertise_data) ? implode( ', ', $expertise_data ) : '';
        }

        $engineer_name = '';
        $engineer_ids = $job_row->engineers->pluck( 'engineer_id' )->toArray();
        if ( !empty($engineer_ids) ) {
            $engineer_data = User::select( 'name' )->whereIn( 'id', $engineer_ids )->pluck( 'name' )->toArray();
            $engineer_name = !empty($engineer_data) ? implode( ', ', $engineer_data ) : '';
        }

        $variable_arr = [
            '{user_name}'       => $user->name,
            '{user_email}'      => $user->email,
            '{job_title}'       => $job_row->title ?? '',
            '{job_code}'        => $job_row->code ?? '',
            '{job_status}'      => $job_row->status ?? '',
            '{job_reschedule_date}' => $job_row->visiting_date ?? '',
            '{customer_name}'   => $job_row->customer->name ?? '',
            '{customer_email}'  => $job_row->customer->email ?? '',
            '{technician_name}' => $technician_name,
            '{expertise_name}'  => $expertise_name,
            '{current_date}'    => now()->format( 'Y-m-d' ),
            '{current_time}'    => now()->format( 'H:i:s' ),
            '{job_hold_note}'   => $job_row->hold_note ?? '',
            '{engineer_name}'   => $engineer_name,
        ];
        return str_replace( array_keys( $variable_arr ), array_values( $variable_arr ), $content );
    }

}