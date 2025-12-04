@extends('layouts.app', ['title' => 'Dashboard', 'subTitle' => 'Dashboard', 'datepicker' => true])

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.min.css') }}">
<style>
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 1.25rem;
        color: #5a5c69;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        text-decoration: none;
        display: block;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #4e73df;
        transition: width 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #d1d3e2;
    }

    .stat-card:hover::before {
        width: 6px;
    }

    .stat-card.new-jobs::before {
        background: #4e73df;
    }

    .stat-card.pending-jobs::before {
        background: #f6c23e;
    }

    .stat-card.upcoming-services::before {
        background: #36b9cc;
    }

    .stat-card.open-jobs::before {
        background: #1cc88a;
    }

    .stat-card.emergency-callouts::before {
        background: #e74a3b;
    }

    .stat-card.workshop-jobs::before {
        background: #858796;
    }

    .stat-card-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stat-icon {
        font-size: 2rem;
        color: #d1d3e2;
        flex-shrink: 0;
        width: 50px;
        text-align: center;
    }

    .stat-card.new-jobs .stat-icon {
        color: #4e73df;
    }

    .stat-card.pending-jobs .stat-icon {
        color: #f6c23e;
    }

    .stat-card.upcoming-services .stat-icon {
        color: #36b9cc;
    }

    .stat-card.open-jobs .stat-icon {
        color: #1cc88a;
    }

    .stat-card.emergency-callouts .stat-icon {
        color: #e74a3b;
    }

    .stat-card.workshop-jobs .stat-icon {
        color: #858796;
    }

    .stat-info {
        flex: 1;
    }

    .stat-count {
        font-size: 1.75rem;
        font-weight: 700;
        color: #5a5c69;
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #858796;
        letter-spacing: 0.5px;
    }

    .loading-skeleton {
        background: linear-gradient(90deg, #f8f9fc 25%, #e3e6f0 50%, #f8f9fc 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 4px;
        height: 1.75rem;
        width: 60px;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }
</style>
@endpush

@section('content')

@if(in_array('admin', auth()->user()->roles()->pluck('name')->toArray()) || 
in_array('billing-coordinator', auth()->user()->roles()->pluck('name')->toArray()))
    <div class="dashboard-stats">
        <a href="#" class="stat-card new-jobs" data-filter="new-jobs">
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="fa fa-briefcase"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-count" id="new-jobs-count">
                        <div class="loading-skeleton"></div>
                    </div>
                    <div class="stat-title">New Jobs</div>
                </div>
            </div>
        </a>

        <a href="#" class="stat-card pending-jobs" data-filter="pending-jobs">
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="fa fa-clock-o"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-count" id="pending-jobs-count">
                        <div class="loading-skeleton"></div>
                    </div>
                    <div class="stat-title">Pending Jobs</div>
                </div>
            </div>
        </a>

        <a href="#" class="stat-card upcoming-services" data-filter="upcoming-services">
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="fa fa-calendar"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-count" id="upcoming-services-count">
                        <div class="loading-skeleton"></div>
                    </div>
                    <div class="stat-title">Upcoming Services</div>
                </div>
            </div>
        </a>

        <a href="#" class="stat-card open-jobs" data-filter="open-jobs">
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="fa fa-folder-open"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-count" id="open-jobs-count">
                        <div class="loading-skeleton"></div>
                    </div>
                    <div class="stat-title">Open Jobs</div>
                </div>
            </div>
        </a>

        <a href="#" class="stat-card emergency-callouts" data-filter="emergency-callouts">
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-count" id="emergency-callouts-count">
                        <div class="loading-skeleton"></div>
                    </div>
                    <div class="stat-title">Emergency Call Outs</div>
                </div>
            </div>
        </a>

        <a href="#" class="stat-card workshop-jobs" data-filter="workshop-jobs">
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="fa fa-wrench"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-count" id="workshop-jobs-count">
                        <div class="loading-skeleton"></div>
                    </div>
                    <div class="stat-title">Workshop Jobs</div>
                </div>
            </div>
        </a>
    </div>
@endif

<hr style="margin: 2rem 0; border-color: #e3e6f0;">

@if(in_array('admin', auth()->user()->roles()->pluck('name')->toArray()) || 
in_array('job-coordinator', auth()->user()->roles()->pluck('name')->toArray()))
<div class="filtered-stats-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0" style="color: #5a5c69; font-weight: 600;">Filtered Statistics</h5>
        <div class="d-flex gap-2 align-items-center">
            <input type="text" id="daterange-picker" class="form-control" placeholder="Select Date Range" value="{{ now()->startOfMonth()->format('Y-m-d') }} - {{ date('Y-m-d') }}" readonly>
        </div>
    </div>

    <div class="dashboard-stats">
        <div class="stat-card jobs-assigned-card">
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="fa fa-plus-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-count" id="jobs-assigned-count">
                        <div class="loading-skeleton"></div>
                    </div>
                    <div class="stat-title">Jobs Assigned</div>
                </div>
            </div>
        </div>

        <div class="stat-card tech-availability-card">
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-count" id="tech-availability-count">
                        <div class="loading-skeleton"></div>
                    </div>
                    <div class="stat-title">Pending Tasks</div>
                </div>
            </div>
        </div>

        <div class="stat-card jobs-cancelled-card">
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="fa fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-count" id="jobs-cancelled-count">
                        <div class="loading-skeleton"></div>
                    </div>
                    <div class="stat-title">Jobs Cancelled</div>
                </div>
            </div>
        </div>

        <div class="stat-card reschedules-card">
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="fa fa-refresh"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-count" id="reschedules-count">
                        <div class="loading-skeleton"></div>
                    </div>
                    <div class="stat-title">Reschedules</div>
                </div>
            </div>
        </div>

        <div class="stat-card priority-jobs-card">
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="fa fa-star"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-count" id="priority-jobs-count">
                        <div class="loading-skeleton"></div>
                    </div>
                    <div class="stat-title">Priority Jobs</div>
                </div>
            </div>
        </div>

        <div class="stat-card daily-job-load-card">
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i class="fa fa-tasks"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-count" id="daily-job-load-count">
                        <div class="loading-skeleton"></div>
                    </div>
                    <div class="stat-title">Avg Jobs/Technician</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<style>
    .filtered-stats-section {
        margin-top: 2rem;
    }

    .jobs-assigned-card::before {
        background: #5a67d8;
    }

    .jobs-assigned-card .stat-icon {
        color: #5a67d8;
    }

    .tech-availability-card::before {
        background: #48bb78;
    }

    .tech-availability-card .stat-icon {
        color: #48bb78;
    }

    .jobs-cancelled-card::before {
        background: #f56565;
    }

    .jobs-cancelled-card .stat-icon {
        color: #f56565;
    }

    .reschedules-card::before {
        background: #ed8936;
    }

    .reschedules-card .stat-icon {
        color: #ed8936;
    }

    .priority-jobs-card::before {
        background: #ecc94b;
    }

    .priority-jobs-card .stat-icon {
        color: #ecc94b;
    }

    .daily-job-load-card::before {
        background: #9f7aea;
    }

    .daily-job-load-card .stat-icon {
        color: #9f7aea;
    }
</style>
@endsection 

@push('js')
<script src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>
<script>
    $(document).ready(function () {

        function loadJobStats() {
            $.ajax({
                url: '{{ route("dashboard.job-stats") }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#new-jobs-count').html(data.newJobs);
                    $('#pending-jobs-count').html(data.pendingJobs);
                    $('#upcoming-services-count').html(data.upcomingServices);
                    $('#open-jobs-count').html(data.openJobs);
                    $('#emergency-callouts-count').html(data.emergencyCallouts);
                    $('#workshop-jobs-count').html(data.workshopJobs);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading job stats:', error);
                    $('.stat-count').html('0');
                }
            });
        }

        loadJobStats();

        $('.stat-card').on('click', function(e) {
            e.preventDefault();
            var filter = $(this).data('filter');
            var url = '{{ route("jobs.index") }}?filter=' + filter;
            window.location.href = url;
        });

        function loadFilteredStats() {
            var filterType = $('#daterange-picker').val();
            var params = {
                filter_type: filterType
            };

            if (filterType === 'custom') {
                var dateRangeValue = $('#daterange-picker').val();
                if (dateRangeValue) {
                    var dates = dateRangeValue.split(' - ');
                    params.start_date = dates[0];
                    params.end_date = dates[1];
                } else {
                    return;
                }
            }

            $.ajax({
                url: '{{ route("dashboard.filtered-stats") }}',
                type: 'GET',
                data: params,
                dataType: 'json',
                success: function(data) {
                    $('#jobs-assigned-count').html(data.jobsAssigned);
                    $('#tech-availability-count').html(data.technicianAvailability);
                    $('#jobs-cancelled-count').html(data.jobsCancelled);
                    $('#reschedules-count').html(data.reschedules);
                    
                    var priorityText = data.priorityJobs.count;
                    if (data.priorityJobs.avgResolutionTime > 0) {
                        priorityText += '<br><small style="font-size: 0.65rem; opacity: 0.8;">' + data.priorityJobs.avgResolutionTime + 'h avg</small>';
                    }
                    $('#priority-jobs-count').html(priorityText);
                    
                    $('#daily-job-load-count').html(data.dailyJobLoad);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading filtered stats:', error);
                    $('#jobs-assigned-count, #tech-availability-count, #jobs-cancelled-count, #reschedules-count, #priority-jobs-count, #daily-job-load-count').html('0');
                }
            });
        }

        loadFilteredStats();

        $('#date-filter').on('change', function() {
            if ($(this).val() === 'custom') {
                $('#custom-date-range').removeClass('d-none');
            } else {
                $('#custom-date-range').addClass('d-none');
                loadFilteredStats();
            }
        });

        $('#daterange-picker').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
                cancelLabel: 'Clear'
            },
            maxDate: moment(),
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });

        $('#daterange-picker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            loadFilteredStats();
        });

        $('#daterange-picker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });
</script>
@endpush
