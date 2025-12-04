<div class="collapse mt-3" id="filterPanel">
    <div class="card mb-3">
        <div class="card-body" style="border: 1px solid #8080805c;border-radius: 11px;">
            <div class="row">

                @if(Request::route()->getName() == 'users.index')
                    <div class="col-md-3">
                        <label for="filter-name" class="form-label"> Search </label>
                        <input type="text" id="filter-name" class="form-control" placeholder="Name, Email or Phone Number">
                    </div>

                    <div class="col-md-3">
                        <label for="filter-role" class="form-label">Role</label>
                        <select id="filter-role" class="form-select" multiple>
                            @forelse (\Spatie\Permission\Models\Role::whereNotIn('name', App\Http\Controllers\UserController::$excludeRoles)->get() as $item)
                                <option value="{{ $item->id }}"> {{ $item->title }} </option>
                            @empty                                
                            @endforelse
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filter-department" class="form-label"> Department </label>
                        <select id="filter-department" class="form-select" multiple>
                            @forelse (\App\Models\Department::get() as $item)
                                <option value="{{ $item->id }}"> {{ $item->name }} </option>
                            @empty                                
                            @endforelse
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filter-status" class="form-label">Status</label>
                        <select id="filter-status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                @endif

                @if(Request::route()->getName() == 'engineers.index' || Request::route()->getName() == 'technicians.index')
                    <div class="col-md-3">
                        <label for="filter-name" class="form-label"> Search </label>
                        <input type="text" id="filter-name" class="form-control" placeholder="Name, Email or Phone Number">
                    </div>

                    <div class="col-md-3">
                        <label for="filter-expertise" class="form-label"> Expertise </label>
                        <select id="filter-expertise" class="form-select" multiple>
                            @forelse (\App\Models\Expertise::get() as $item)
                                <option value="{{ $item->id }}"> {{ $item->name }} </option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="filter-department" class="form-label"> Department </label>
                        <select id="filter-department" class="form-select" multiple>
                            @forelse (\App\Models\Department::get() as $item)
                                <option value="{{ $item->id }}"> {{ $item->name }} </option>
                            @empty                                
                            @endforelse
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filter-status" class="form-label">Status</label>
                        <select id="filter-status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                @endif

                @if(Request::route()->getName() == 'co-ordinators.index')
                    <div class="col-md-4">
                        <label for="filter-name" class="form-label"> Search </label>
                        <input type="text" id="filter-name" class="form-control" placeholder="Name, Email or Phone Number">
                    </div>

                    <div class="col-md-4">
                        <label for="filter-department" class="form-label"> Department </label>
                        <select id="filter-department" class="form-select" multiple>
                            @forelse (\App\Models\Department::get() as $item)
                                <option value="{{ $item->id }}"> {{ $item->name }} </option>
                            @empty                                
                            @endforelse
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="filter-status" class="form-label">Status</label>
                        <select id="filter-status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                @endif

                @if(Request::route()->getName() == 'customers.index')
                    <div class="col-md-8">
                        <label for="filter-name" class="form-label"> Search </label>
                        <input type="text" id="filter-name" class="form-control" placeholder="Name, Email, Phone Number or Adress">
                    </div>

                    <div class="col-md-4">
                        <label for="filter-status" class="form-label">Status</label>
                        <select id="filter-status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-4 mt-2">
                        <label for="filter-country" class="form-label"> Country </label>
                        <select id="filter-country" class="form-select">
                        </select>
                    </div>
                    <div class="col-md-4 mt-2">
                        <label for="filter-state" class="form-label"> State </label>
                        <select id="filter-state" class="form-select">
                        </select>
                    </div>
                    <div class="col-md-4 mt-3">
                        <label for="filter-city" class="form-city"> City </label>
                        <select id="filter-city" class="form-select">
                        </select>
                    </div>
                @endif

                <div class="col-12 d-flex justify-content-end mt-3">
                    <button type="button" id="btn-search" class="btn btn-primary me-2">
                        <i class="fa fa-search"></i> Search
                    </button>
                    <button type="button" id="btn-clear" class="btn btn-secondary">
                        <i class="fa fa-times"></i> Clear
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>