@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Attendance List') }}
@endsection
@push('script-page')
    <script>
        $('input[name="type"]:radio').on('change', function(e) {
            var type = $(this).val();

            if (type == 'monthly') {
                $('.month').addClass('d-block');
                $('.month').removeClass('d-none');
                $('.date').addClass('d-none');
                $('.date').removeClass('d-block');
            } else {
                $('.date').addClass('d-block');
                $('.date').removeClass('d-none');
                $('.month').addClass('d-none');
                $('.month').removeClass('d-block');
            }
        });

        $('input[name="type"]:radio:checked').trigger('change');
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Attendance') }}</li>
@endsection
@section('content')
    <div class="row">

        <div class="col-sm-12">
            @if (session('status'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {!! session('status') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class=" mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['device-attendanceemployee.lists'], 'method' => 'get', 'id' => 'attendanceemployee_filter']) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-10">
                                <div class="row">
                                    <div class="col-3">
                                        <label class="form-label">{{ __('Type') }}</label> <br>

                                        <div class="form-check form-check-inline form-group">
                                            <input type="radio" id="monthly" value="monthly" name="type"
                                                class="form-check-input"
                                                {{ isset($_GET['type']) && $_GET['type'] == 'monthly' ? 'checked' : 'checked' }}>
                                            <label class="form-check-label" for="monthly">{{ __('Monthly') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline form-group">
                                            <input type="radio" id="daily" value="daily" name="type"
                                                class="form-check-input"
                                                {{ isset($_GET['type']) && $_GET['type'] == 'daily' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="daily">{{ __('Daily') }}</label>
                                        </div>

                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 month">
                                        <div class="btn-box">
                                            {{ Form::label('month', __('Month'), ['class' => 'form-label']) }}
                                            {{ Form::month('month', isset($_GET['month']) ? $_GET['month'] : date('Y-m'), ['class' => 'month-btn form-control month-btn']) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 date">
                                        <div class="btn-box">
                                            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                                            {{ Form::date('date', isset($_GET['date']) ? $_GET['date'] : '', ['class' => 'form-control month-btn']) }}
                                        </div>
                                    </div>
                                    @if (\Auth::user()->type != 'Employee')
                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('employees', __('Employees'), ['class' => 'form-label']) }}
                                                {{ Form::select('employee_id', $employees->prepend('Select Employee', ''), isset($_GET['employee_id']) ? $_GET['employee_id'] : '', ['class' => 'form-control select']) }}
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('department', __('Department'), ['class' => 'form-label']) }}
                                                {{ Form::select('department', $department->prepend('Select Department', ''), isset($_GET['department']) ? $_GET['department'] : '', ['class' => 'form-control select']) }}
                                            </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto mt-4">
                                <div class="row">
                                    <div class="col-auto">
                                        <a href="#" class="btn btn-sm btn-primary"
                                            onclick="document.getElementById('attendanceemployee_filter').submit(); return false;"
                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                            data-original-title="{{ __('apply') }}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>
                                        <a href="{{ route('device-attendanceemployee.lists') }}"
                                            class="btn btn-sm btn-danger " data-bs-toggle="tooltip"
                                            title="{{ __('Reset') }}" data-original-title="{{ __('Reset') }}">
                                            <span class="btn-inner--icon"><i
                                                    class="ti ti-trash-off text-white-off "></i></span>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ 'SL' }}</th>
                                    <th>{{ 'Employee Id' }}</th>
                                    @if (\Auth::user()->type != 'Employee')
                                        <th>{{ __('Employee') }}</th>
                                    @endif
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Clock In') }}</th>
                                    <th>{{ __('Clock Out') }}</th>
                                    <th>{{ __('Late') }}</th>
                                    <th>{{ __('Early Leaving') }}</th>
                                    {{-- <th>{{ __('Overtime') }}</th> --}}
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($attendanceEmployee as $key => $attendance)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $attendance->employee_id }}</td>
                                        @if (\Auth::user()->type != 'Employee')
                                            <td>{{ $attendance?->employee?->name ?? '' }}</td>
                                        @endif
                                        <td>{{ \Auth::user()->dateFormat($attendance->date) }}</td>
                                        <td>{{ $attendance->clock_in != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_in) : '00:00' }}
                                        </td>
                                        <td>{{ $attendance->clock_out != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_out) : '00:00' }}
                                        </td>
                                        <td>{{ $attendance->late ?? '' }}</td>
                                        <td>{{ $attendance->early_leaving ?? '' }}</td>
                                        {{-- <td>{{ $attendance->overtime ?? '' }}</td> --}}
                                        {{-- @dd($attendance); --}}
                                        @php

                                            $dayName = date('l', strtotime($attendance?->date));
                                            $attendanceDate = $attendance?->date;
                                            $employee = $attendance?->employee;
                                            $status = $attendance->status; // Default status

                                            // Check for General Holidays
                                            $checkDate = \Carbon\Carbon::parse($attendance->date);

                                            $isHoliday = $holidays->contains(function ($holiday) use ($checkDate) {
                                                return $checkDate->between($holiday->date, $holiday->end_date);
                                            });

                                            if ($isHoliday) {

                                                $status = 'GH';
                                            }
                                            // Check for Weekends
                                            elseif ($dayName == 'Friday' || $dayName == 'Saturday') {
                                                $status = 'Off';
                                            }
                                            // Check for Meetings
                                            elseif (!$employee?->meetings->isEmpty()) {
                                                $meetingDates = $employee?->meetings->pluck('date')->toArray();
                                                if (in_array($attendanceDate, $meetingDates)) {
                                                    $status = 'Meeting';
                                                }
                                            }

                                            // Check for Meetings
                                            elseif (!$employee?->meetings->isEmpty() && $attendance->status == 'Present') {
                                                $meetingDates = $employee?->meetings->pluck('date')->toArray();
                                                if (in_array($attendanceDate, $meetingDates)) {
                                                    $status = 'P_Meeting';
                                                }
                                            }
                                            elseif (!$employee?->meetings->isEmpty() && $attendance->status == 'Absent') {
                                                $meetingDates = $employee?->meetings->pluck('date')->toArray();
                                                if (in_array($attendanceDate, $meetingDates)) {
                                                    $status = 'Meeting';
                                                }
                                            }
                                            elseif($attendance->status == 'Present' && $attendance->late !== "00:00:00" && $attendance->early_leaving === "00:00:00"){
                                                $status = 'Late';
                                            }
                                            elseif($attendance->status == 'Present' && $attendance->late === "00:00:00" && $attendance->early_leaving !== "00:00:00"){
                                                $status = 'Early_leaving';
                                            }
                                            elseif($attendance->status == 'Present' && $attendance->late !== "00:00:00" && $attendance->early_leaving != "00:00:00"){
                                                $status = 'late_early_leave';
                                            }



                                            // Check for Leave
                                            elseif (!$employee?->leaves->isEmpty()) {
                                                $leave = $employee?->leaves->firstWhere(function ($leave) use (
                                                    $attendanceDate,
                                                ) {
                                                    return $leave->start_date <= $attendanceDate &&
                                                        $leave->end_date >= $attendanceDate;
                                                });
                                                if ($leave) {
                                                    $status = 'Leave';
                                                }
                                            }

                                            // Define badge classes and labels
                                            $badgeStyles = [
                                                'Meeting' => ['class' => 'bg-primary', 'label' => 'Present/M'],
                                                'Present' => ['class' => 'bg-success', 'label' => 'Present'],
                                                'GH' => ['class' => 'bg-info', 'label' => 'G/Holiday'],
                                                'Off' => ['class' => 'bg-info', 'label' => 'Holiday'],
                                                'Leave' => ['class' => 'bg-danger', 'label' => 'Leave'],
                                                'Absent' => ['class' => 'bg-danger', 'label' => 'Absent'],
                                                'Late' => ['class' => 'bg-warning', 'label' => 'Present/L'],
                                                'Early_leaving' => ['class' => 'bg-warning', 'label' => 'Present/EL'],
                                                'late_early_leave' => ['class' => 'bg-warning', 'label' => 'Present/L/EL'],
                                            ];

                                            $badgeClass = $badgeStyles[$status]['class'];
                                            $badgeLabel = $badgeStyles[$status]['label'];
                                        @endphp

                                        <td>
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $badgeLabel }}
                                            </span>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script>
        $(document).ready(function() {
            $('.daterangepicker').daterangepicker({
                format: 'yyyy-mm-dd',
                locale: {
                    format: 'YYYY-MM-DD'
                },
            });
        });
    </script>
@endpush
