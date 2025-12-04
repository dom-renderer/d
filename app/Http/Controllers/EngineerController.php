<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Models\DepartmentUser;
use App\Models\ExpertiseUser;
use Illuminate\Http\Request;
use App\Models\User;

class EngineerController extends Controller
{
    protected $title = 'Engineers';
    protected $view = 'engineers.';

    public function __construct()
    {
        $this->middleware('permission:engineers.index')->only(['index', 'ajax']);
        $this->middleware('permission:engineers.create')->only(['create']);
        $this->middleware('permission:engineers.store')->only(['store']);
        $this->middleware('permission:engineers.edit')->only(['edit']);
        $this->middleware('permission:engineers.update')->only(['update']);
        $this->middleware('permission:engineers.show')->only(['show']);
        $this->middleware('permission:engineers.destroy')->only(['destroy']);
    }

    public function index()
    {
        if (request()->ajax()) {
            return $this->ajax();
        }
        $title = $this->title;
        $subTitle = 'Manage engineers here';
        return view($this->view . 'index', compact('title', 'subTitle'));
    }

    public function ajax()
    {
        $engineerRole = Role::where('name', 'engineer')->first();
        $query = User::query()->whereHas('roles', function ($q) use ($engineerRole) {
            $q->where('id', $engineerRole->id);
        });

        if (request()->filled('filter_status')) {
            $query->where('status', request('filter_status'));
        }

        if (request()->filled('filter_expertise')) {
            $query->whereHas('expertise', fn ($builder) => $builder->whereIn('expertise_id', explode(',', request('filter_expertise'))));
        }

        if (request()->filled('filter_department')) {
            $query->whereHas('department', fn ($builder) => $builder->whereIn('department_id', explode(',', request('filter_department'))));
        }

        if (request()->filled('filter_name')) {
            $query->where(function ($builder) {
                $filter = request('filter_name');
                $likeFilter = "%{$filter}%";

                $builder->where('name', 'LIKE', $likeFilter)
                    ->orWhere('email', 'LIKE', $likeFilter)
                    ->orWhere('phone_number', 'LIKE', $likeFilter)
                    ->orWhereRaw("CONCAT(dial_code, phone_number) LIKE ?", [$likeFilter])
                    ->orWhereRaw("CONCAT(dial_code, ' ', phone_number) LIKE ?", [$likeFilter])
                    ->orWhereRaw("CONCAT('+', dial_code, ' ', phone_number) LIKE ?", [$likeFilter])
                    ->orWhereRaw("CONCAT('+', dial_code, ' ', phone_number) LIKE ?", ["%+{$filter}%"]);
            });
        }

        return datatables()
            ->eloquent($query)
            ->editColumn('phone_number', function ($row) {
                return '+' . $row->dial_code . ' ' . $row->phone_number;
            })
            ->editColumn('status', function ($row) {
                return $row->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('action', function ($row) {
                $html = '';
                if (auth()->user()->can('engineers.edit')) {
                    $html .= '<a href="' . route('engineers.edit', encrypt($row->id)) . '" class="btn btn-sm btn-primary"> <i class="fa fa-edit"> </i> </a>&nbsp;';
                }
                if (auth()->user()->can('engineers.destroy')) {
                    $html .= '<button type="button" class="btn btn-sm btn-danger" id="deleteRow" data-row-route="' . route('engineers.destroy', $row->id) . '"> <i class="fa fa-trash"> </i> </button>&nbsp;';
                }
                if (auth()->user()->can('engineers.show')) {
                    $html .= '<a href="' . route('engineers.show', encrypt($row->id)) . '" class="btn btn-sm btn-secondary"> <i class="fa fa-eye"> </i> </a>';
                }
                return $html;
            })
            ->rawColumns(['status', 'action'])
            ->addIndexColumn()
            ->toJson();
    }

    public function create()
    {
        $title = $this->title;
        $subTitle = 'Add New Engineer';
        return view($this->view . 'create', compact('title', 'subTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'dial_code' => 'required|string|max:10',
            'phone_number' => ['required', 'regex:/^[0-9]+$/', 'max:15', 'unique:users,phone_number'],
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
            'password' => 'required|string|min:6',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->only(['name', 'email', 'dial_code', 'phone_number', 'status']);
            $data['password'] = $request->password;
            $data['added_by'] = optional(auth()->user())->id;
            if ($request->hasFile('profile')) {
                $file = $request->file('profile');
                $filename = uniqid('profile_') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/users/profile'), $filename);
                $data['profile'] = $filename;
            }
            $user = User::create($data);
            $engineerRole = Role::where('name', 'engineer')->first();
            $user->roles()->attach($engineerRole->id);

            if ($request->has('departments')) {
                foreach ($request->departments as $department) {
                    DepartmentUser::create([
                        'user_id' => $user->id,
                        'department_id' => $department
                    ]);
                }
            }

            if ($request->has('expertise')) {
                foreach ($request->expertise as $expertise) {
                    ExpertiseUser::create([
                        'user_id' => $user->id,
                        'expertise_id' => $expertise
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('engineers.index')->with('success', 'Engineer created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('engineers.index')->with('error', 'Something Went Wrong.');
        }
    }

    public function show($id)
    {
        $engineer = User::findOrFail(decrypt($id));
        $currentDepartments = DepartmentUser::with('department')->where('user_id', $engineer->id)->get();
        $currentExpertise = ExpertiseUser::with('expertise')->where('user_id', $engineer->id)->get();

        $title = $this->title;
        $subTitle = 'Engineer Details';
        return view($this->view . 'view', compact('title', 'subTitle', 'engineer', 'currentDepartments', 'currentExpertise'));
    }

    public function edit($id)
    {
        $engineer = User::findOrFail(decrypt($id));
        $currentDepartments = DepartmentUser::with('department')->where('user_id', $engineer->id)->get();
        $currentExpertise = ExpertiseUser::with('expertise')->where('user_id', $engineer->id)->get();

        $title = $this->title;
        $subTitle = 'Edit Engineer';
        return view($this->view . 'edit', compact('title', 'subTitle', 'engineer', 'currentDepartments', 'currentExpertise'));
    }

    public function update(Request $request, $id)
    {
        $engineer = User::findOrFail(decrypt($id));

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $engineer->id,
            'dial_code' => 'required|string|max:10',
            'phone_number' => ['required', 'regex:/^[0-9]+$/', 'max:15', 'unique:users,phone_number,' . $engineer->id],            
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
            'password' => 'nullable|string|min:6',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->only(['name', 'email', 'dial_code', 'phone_number', 'status']);

            if ($request->filled('password')) {
                $data['password'] = $request->password;
            }
            
            if ($request->hasFile('profile')) {
                $file = $request->file('profile');
                $filename = uniqid('profile_') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/users/profile'), $filename);
                $data['profile'] = $filename;
            }

            $deptsToKeep = $expsToKeeps =  [];

            if ($request->has('departments')) {
                foreach ($request->departments as $department) {
                    $deptsToKeep [] = DepartmentUser::updateOrCreate([
                        'user_id' => $engineer->id,
                        'department_id' => $department
                    ])->id;
                }
            }

            if ($request->has('expertise')) {
                foreach ($request->expertise as $expertise) {
                    $expsToKeeps [] = ExpertiseUser::updateOrCreate([
                        'user_id' => $engineer->id,
                        'expertise_id' => $expertise
                    ])->id;
                }
            }

            $engineer->update($data);

            if (!empty($deptsToKeep)) {
                DepartmentUser::whereNotIn('id', $deptsToKeep)->where('user_id', $engineer->id)->delete();
            } else {
                DepartmentUser::where('user_id', $engineer->id)->delete();
            }

            if (!empty($expsToKeeps)) {
                ExpertiseUser::whereNotIn('id', $expsToKeeps)->where('user_id', $engineer->id)->delete();
            } else {
                ExpertiseUser::where('user_id', $engineer->id)->delete();
            }

            DB::commit();
            return redirect()->route('engineers.index')->with('success', 'Engineer updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('engineers.index')->with('error', 'Something Went Wrong.');
        }
    }

    public function destroy($id)
    {
        $engineer = User::findOrFail($id);
        $engineer->delete();
        return response()->json(['success' => 'Engineer deleted successfully.']);
    }
} 