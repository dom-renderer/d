<div class="collapse mt-3" id="filterPanel">
    <div class="card mb-3">
        <div class="card-body" style="border: 1px solid #8080805c;border-radius: 11px;">
            <div class="row">

                @if(Request::route()->getName() == 'requisitions.index')
                    <!-- <div class="col-md-3">
                        <label for="filter-status">Status</label>
                        <select id="filter-status" class="form-select">
                            <option value="">All Status</option>
                            <option value="PENDING">Pending</option>
                            <option value="APPROVED">Approved</option>
                            <option value="REJECTED">Rejected</option>
                        </select>
                    </div> -->
                    <div class="col-md-3">
                        <label for="filter-job">Job</label>
                        <select id="filter-job" class="form-select">
                            <option value="">All Jobs</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-addedby">Created By</label>
                        <select id="filter-addedby" class="form-select">
                            <option value="">All Users</option>
                        </select>
                    </div>
                @endif

                @if(Request::route()->getName() == 'jobs.index')
                    <div class="col-md-3">
                        <label for="filter-status">Status</label>
                        <select id="filter-status" class="form-select">
                            <option value="">All Status</option>
                            <option value="PENDING">Pending</option>
                            <option value="INPROGRESS">In Progress</option>
                            <option value="COMPLETED">Completed</option>
                            <option value="CANCELLED">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-customer">Customer</label>
                        <select id="filter-customer" class="form-select">
                            <option value="">All Customers</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-addedby">Created By</label>
                        <select id="filter-addedby" class="form-select">
                            <option value="">All Users</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-technicians">Technicians</label>
                        <select id="filter-technicians" class="form-select">
                            <option value="">All Users</option>
                        </select>
                    </div>
                    <div class="col-md-3 mt-2">
                        <label for="filter-engineers">Engineers</label>
                        <select id="filter-engineers" class="form-select">
                            <option value="">All Users</option>
                        </select>
                    </div>
                    <div class="col-md-3 mt-2">
                        <label for="filter-deposit-requirement">Deposit Required</label>
                        <select id="filter-deposit-requirement" class="form-select">
                            <option value="" selected> --- Select --- </option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-md-3 mt-2">
                        <label for="filter-invoice-generation">Invoice Generated</label>
                        <select id="filter-invoice-generation" class="form-select">
                            <option value="" selected> --- Select --- </option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-md-3 mt-2">
                        <label for="filter-expertise">Expertise</label>
                        <select id="filter-expertise" class="form-select">
                            <option value="">All Expertise</option>
                        </select>
                    </div>
                    <div class="col-md-3 mt-2">
                        <label for="filter-visiting_date_from"> Visiting Date (From) </label>
                        <input type="text" id="visiting_date_from" class="form-control" readonly>
                    </div>
                    <div class="col-md-3 mt-2">
                        <label for="filter-visiting_date_to"> Visiting Date (To) </label>
                        <input type="text" id="visiting_date_to" class="form-control" readonly>
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