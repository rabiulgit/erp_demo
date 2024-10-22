@extends('layouts.admin')
@section('page-title')
{{__('Manage Attendance List')}}
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
<li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
<li class="breadcrumb-item">{{__('Attendance')}}</li>
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
                    {{ Form::open(array('route' => array('device-attendanceemployee.lists'),'method'=>'get','id'=>'attendanceemployee_filter')) }}
                    <div class="row align-items-center justify-content-end">
                        <div class="col-xl-10">
                            <div class="row">
                                <div class="col-3">
                                    <label class="form-label">{{__('Type')}}</label> <br>

                                    <div class="form-check form-check-inline form-group">
                                        <input type="radio" id="monthly" value="monthly" name="type" class="form-check-input" {{isset($_GET['type']) && $_GET['type']=='monthly' ?'checked':'checked'}}>
                                        <label class="form-check-label" for="monthly">{{__('Monthly')}}</label>
                                    </div>
                                    <div class="form-check form-check-inline form-group">
                                        <input type="radio" id="daily" value="daily" name="type" class="form-check-input" {{isset($_GET['type']) && $_GET['type']=='daily' ?'checked':''}}>
                                        <label class="form-check-label" for="daily">{{__('Daily')}}</label>
                                    </div>

                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 month">
                                    <div class="btn-box">
                                        {{Form::label('month',__('Month'),['class'=>'form-label'])}}
                                        {{Form::month('month',isset($_GET['month'])?$_GET['month']:date('Y-m'),array('class'=>'month-btn form-control month-btn'))}}
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 date">
                                    <div class="btn-box">
                                        {{ Form::label('date', __('Date'),['class'=>'form-label'])}}
                                        {{ Form::date('date',isset($_GET['date'])?$_GET['date']:'', array('class' => 'form-control month-btn')) }}
                                    </div>
                                </div>
                                @if(\Auth::user()->type != 'Employee')
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                    <div class="btn-box">
                                        {{ Form::label('employees', __('Employees'),['class'=>'form-label'])}}
                                        {{ Form::select('employee_id', $employees->prepend('Select Employee', ''), isset($_GET['employees']) ? $_GET['employees'] : '', ['class' => 'form-control select']) }}
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                    <div class="btn-box">
                                        {{ Form::label('department', __('Department'),['class'=>'form-label'])}}
                                        {{ Form::select('department', $department->prepend('Select Department', ''), isset($_GET['department']) ? $_GET['department'] : '', ['class' => 'form-control select']) }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto mt-4">
                                <div class="row">
                                    <div class="col-auto">
                                        <a href="#" class="btn btn-sm btn-primary" onclick="document.getElementById('attendanceemployee_filter').submit(); return false;" data-bs-toggle="tooltip" title="{{__('Apply')}}" data-original-title="{{__('apply')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>
                                        <a href="{{route('device-attendanceemployee.lists')}}" class="btn btn-sm btn-danger " data-bs-toggle="tooltip" title="{{ __('Reset') }}" data-original-title="{{__('Reset')}}">
                                            <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off "></i></span>
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
                                    <th>{{('SL')}}</th>
                                    <th>{{('Employee Id')}}</th>
                                    @if(\Auth::user()->type!='Employee')
                                    <th>{{__('Employee')}}</th>
                                    @endif
                                    <th>{{__('Date')}}</th>
                                    <th>{{__('Clock In')}}</th>
                                    <th>{{__('Clock Out')}}</th>
                                    <th>{{__('Late')}}</th>
                                    <th>{{__('Early Leaving')}}</th>
                                    <th>{{__('Overtime')}}</th>
                                    <th>{{__('Status')}}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($attendanceEmployee as $key => $attendance)

                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{ $attendance->employee_id}}</td>
                                    @if(\Auth::user()->type!='Employee')
                                    <td>{{$attendance->name ?? ""}}</td>
                                    @endif
                                    <td>{{ \Auth::user()->dateFormat($attendance->date) }}</td>
                                    <td>{{ ($attendance->clock_in !='00:00:00') ?\Auth::user()->timeFormat( $attendance->clock_in):'00:00' }} </td>
                                    <td>{{ ($attendance->clock_out !='00:00:00') ?\Auth::user()->timeFormat( $attendance->clock_out):'00:00' }}</td>
                                    <td>{{ $attendance->late ?? "" }}</td>
                                    <td>{{ $attendance->early_leaving ?? "" }}</td>
                                    <td>{{ $attendance->overtime ?? "" }}</td>
                                    <td>
                                        @php
                                        // Formatting the dates
                                        $start_date = \Auth::user()->dateFormat($attendance->l_start_date);
                                        $end_date = \Auth::user()->dateFormat($attendance->l_end_date);
                                        $check_date = \Auth::user()->dateFormat($attendance->date);
                                        $meeting_date = \Auth::user()->dateFormat($attendance->m_date);

                                        // Decode the JSON string into a PHP array
                                        $json_decode = $attendance->emp_ids ? json_decode($attendance->emp_ids, true) : [1,1];

                                        // Check if the current date is within the leave dates
                                        if ($start_date <= $check_date && $end_date>= $check_date) {
                                            $attendance->status = "Leave";
                                            }
                                            // Check if the employee ID is in the meeting list and the dates match
                                            else if (in_array($attendance->emp_id, $json_decode ) && $check_date == $meeting_date) {
                                            $attendance->status = "Present-Meeting"; // Set status to Present-Meeting
                                            }
                                            @endphp

                                            <span class="badge 
                                                 {{$attendance->status == 'Present' ? 'bg-success' : ($attendance->status == 'Leave' ? 'bg-warning' : ($attendance->status == 'Present-Meeting' ? 'bg-info' : 'bg-danger'))}}">
                                                {{$attendance->status == 'Present' ? 'Present' : ($attendance->status == 'Leave' ? 'Leave' : ($attendance->status == 'Present-Meeting' ? 'Present-M' : 'Absent'))}}
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