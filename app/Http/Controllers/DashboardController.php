<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Interior;
use App\Models\Invoice;
use App\Models\Dealer;
use App\Models\Job;
use App\Models\JobRescheduleRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function getJobStats()
    {
        $newJobs = Job::where('status', 'PENDING')->count();

        $pendingJobs = Job::where('status', 'PENDING')
            ->whereDoesntHave('technicians')
            ->whereDoesntHave('engineers')
            ->count();

        $upcomingServices = Job::where('status', 'PENDING')
            ->whereDate('visiting_date', '>', Carbon::today())
            ->count();

        $openJobs = Job::whereIn('status', ['PENDING'])->count();

        $emergencyCallouts = 0;

        $workshopJobs = Job::where('in_workshop', 1)->count();

        return response()->json([
            'newJobs' => $newJobs,
            'pendingJobs' => $pendingJobs,
            'upcomingServices' => $upcomingServices,
            'openJobs' => $openJobs,
            'emergencyCallouts' => $emergencyCallouts,
            'workshopJobs' => $workshopJobs,
        ]);
    }

    public function getFilteredStats(Request $request)
    {
        $startDate = null;
        $endDate = null;

        $filterType = explode(' - ', $request->filter_type);

        $startDate = isset($filterType[0]) ? Carbon::parse($filterType[0])->startOfDay() : Carbon::today()->startOfDay();
        $endDate = isset($filterType[1]) ? Carbon::parse($filterType[1])->endOfDay() : Carbon::today()->endOfDay();

        $jobsAssigned = Job::whereBetween('created_at', [$startDate, $endDate])->count();

        $technicianAvailability = Job::where('status', 'PENDING')
            ->whereDoesntHave('technicians')
            ->whereDoesntHave('engineers')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $jobsCancelled = Job::where('status', 'CANCELLED')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        $reschedules = JobRescheduleRequest::whereBetween('created_at', [$startDate, $endDate])->count();

        $priorityJobsCount = Job::where('priority', 'HIGH')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $avgResolutionTime = Job::where('priority', 'HIGH')
            ->where('status', 'COMPLETED')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('completed_at')
            ->whereNotNull('created_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as avg_hours')
            ->value('avg_hours');

        $avgResolutionTime = $avgResolutionTime ? round($avgResolutionTime, 1) : 0;

        $activeJobs = Job::whereIn('status', ['PENDING', 'INPROGRESS'])->whereBetween('created_at', [$startDate, $endDate])->count();
        

        $technicianCount = User::whereHas('roles', function($q) {
            $q->where('name', 'technician');
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

        $dailyJobLoad = $technicianCount > 0 ? round($activeJobs / $technicianCount, 1) : 0;

        return response()->json([
            'jobsAssigned' => $jobsAssigned,
            'technicianAvailability' => $technicianAvailability,
            'jobsCancelled' => $jobsCancelled,
            'reschedules' => $reschedules,
            'priorityJobs' => [
                'count' => $priorityJobsCount,
                'avgResolutionTime' => $avgResolutionTime
            ],
            'dailyJobLoad' => $dailyJobLoad,
        ]);
    }
}
