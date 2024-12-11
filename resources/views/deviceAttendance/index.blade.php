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
                                    <td>{{ $attendance->clock_in != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_in) : '00:00' }}</td>
                                    <td>{{ $attendance->clock_out != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_out) : '00:00' }}</td>
                                    <td>{{ $attendance->late ?? '' }}</td>
                                    <td>{{ $attendance->early_leaving ?? '' }}</td>

                                    @php
                                        $status = $attendance->status;
                                        $dayName = date('l', strtotime($attendance?->date));
                                        $attendanceDate = $attendance?->date;
                                        $employee = $attendance?->employee;

                                        $isHoliday = function () use ($holidays, $attendanceDate) {
                                            return $holidays->contains(function ($holiday) use ($attendanceDate) {
                                                return \Carbon\Carbon::parse($attendanceDate)->between($holiday->date, $holiday->end_date);
                                            });
                                        };

                                        $isWeekend = function () use ($dayName) {
                                            return in_array($dayName, ['Friday', 'Saturday']);
                                        };

                                        $hasMeeting = function () use ($employee, $attendanceDate) {
                                            return $employee?->meetings?->contains('date', $attendanceDate) ?? false;
                                        };

                                        $isOnLeave = function () use ($employee, $attendanceDate) {
                                            return $employee?->leaves?->first(function ($leave) use ($attendanceDate) {
                                                return $leave->start_date <= $attendanceDate && $leave->end_date >= $attendanceDate;
                                            });
                                        };

                                        if ($isHoliday()) {
                                            $status = 'GH';
                                        } elseif ($isWeekend()) {
                                            $status = 'Off';
                                        } elseif ($isOnLeave()) {
                                            $status = 'Leave';
                                        } elseif ($hasMeeting()) {
                                            if ($attendance->status === 'Absent') {
                                                $status = 'M';
                                            } elseif ($attendance->status === 'Present') {
                                                if ($attendance->late === '00:00:00' && $attendance->early_leaving === '00:00:00') {
                                                    $status = 'PM';
                                                } elseif ($attendance->late !== '00:00:00' && $attendance->early_leaving === '00:00:00') {
                                                    $status = 'PLM';
                                                } elseif ($attendance->late === '00:00:00' && $attendance->early_leaving !== '00:00:00') {
                                                    $status = 'PELM';
                                                } elseif ($attendance->late !== '00:00:00' && $attendance->early_leaving !== '00:00:00') {
                                                    $status = 'PLELM';
                                                }
                                            }
                                        } else {
                                            if ($attendance->status === 'Present') {
                                                if ($attendance->late === '00:00:00' && $attendance->early_leaving === '00:00:00') {
                                                    $status = 'P';
                                                } elseif ($attendance->late !== '00:00:00' && $attendance->early_leaving === '00:00:00') {
                                                    $status = 'PL';
                                                } elseif ($attendance->late === '00:00:00' && $attendance->early_leaving !== '00:00:00') {
                                                    $status = 'PEL';
                                                } elseif ($attendance->late !== '00:00:00' && $attendance->early_leaving !== '00:00:00') {
                                                    $status = 'PLEL';
                                                }
                                            }
                                        }

                                        $badgeStyles = [
                                            'M'       => ['class' => 'bg-primary', 'label' => 'Meeting'],
                                            'P'       => ['class' => 'bg-success', 'label' => 'Present'],
                                            'PM'      => ['class' => 'bg-primary', 'label' => 'Present/M'],
                                            'PLM'     => ['class' => 'bg-warning', 'label' => 'Present/L/M'],
                                            'PELM'    => ['class' => 'bg-warning', 'label' => 'Present/EL/M'],
                                            'PLELM'   => ['class' => 'bg-warning', 'label' => 'Present/L/EL/M'],
                                            'GH'      => ['class' => 'bg-info',    'label' => 'G/Holiday'],
                                            'Off'     => ['class' => 'bg-info',     'label'=> 'Holiday'],
                                            'Leave'   => ['class' => 'bg-danger',   'label'=> 'Leave'],
                                            'Absent'  => ['class' => 'bg-danger',   'label'=> 'Absent'],
                                            'PL'      => ['class' => 'bg-warning', 'label' => 'Present/L'],
                                            'PEL'     => ['class' => 'bg-warning', 'label' => 'Present/EL'],
                                            'PLEL'    => ['class' => 'bg-warning', 'label' => 'Present/L/EL'],
                                        ];


                                        $badgeClass = $badgeStyles[$status]['class'] ?? 'bg-secondary';
                                        $badgeLabel = $badgeStyles[$status]['label'] ?? 'Unknown';
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
