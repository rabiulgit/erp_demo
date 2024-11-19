@extends('layouts.admin')

@section('page-title')
    {{__('Manage Leave')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Manage Leave')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create leave')
            <a href="#" data-size="lg" data-url="{{ route('leave.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" data-title="{{__('Create Leave')}}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                @if(\Auth::user()->type!='Employee')
                                    <th>{{__('Employee')}}</th>
                                @endif
                                <th>{{__('Leave Type')}}</th>
                                <th>{{__('Applied On')}}</th>
                                <th>{{__('Start Date')}}</th>
                                <th>{{__('End Date')}}</th>
                                <th>{{__('Total Days')}}</th>
                                <th>{{__('Leave Reason')}}</th>
                                <th>Attachment</th>
                                <th>{{__('status')}}</th>
                                @can('edit leave')
                                    <th width="200px">{{__('Action')}}</th>
                                @endcan
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($leaves as $leave)
                                <tr>
                                    @if(\Auth::user()->type!='Employee')
                                        <td>{{ !empty($leave->employees)?$leave->employees->name:'' }}</td>
                                    @endif
                                    <td>{{ !empty($leave->leaveType)?$leave->leaveType->title:'' }}</td>
                                    <td>{{ \Auth::user()->dateFormat($leave->applied_on )}}</td>
                                    <td>{{ \Auth::user()->dateFormat($leave->start_date ) }}</td>
                                    <td>{{ \Auth::user()->dateFormat($leave->end_date )  }}</td>
                                    <td>{{ $leave->total_leave_days }}</td>
                                    <td>{{ $leave->leave_reason }}</td>
                                    <td>
                                        @if(!empty($leave->attachment))
                                            <a href="{{ url('storage/leave/'.$leave->attachment) }}" class="btn btn-primary btn-sm" download>
                                                Download
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($isAdmin == 1)
                                            @if($leave->statusChecked == 0)
                                                <div class="status_badge badge bg-warning p-2 px-3 rounded">Pending</div>
                                            @else
                                                @if ($leave->leaveStatus == 0)
                                                    <div class="status_badge badge bg-danger p-2 px-3 rounded">Rejected</div>
                                                @else
                                                    <div class="status_badge badge bg-success p-2 px-3 rounded">Approved</div>
                                                @endif
                                            @endif
                                        @else
                                            @if($leave->status=="Pending")
                                                <div class="status_badge badge bg-warning p-2 px-3 rounded">{{ $leave->status }}</div>
                                            @elseif($leave->status=="Approved")
                                                <div class="status_badge badge bg-success p-2 px-3 rounded">{{ $leave->status }}</div>
                                            @else
                                                <div class="status_badge badge bg-danger p-2 px-3 rounded">{{ $leave->status }}</div>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-btn bg-warning ms-2">
                                            <a href="#" data-url="{{ URL::to('leave/'.$leave->id.'/action') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('View Leave')}}" class="mx-3 btn btn-sm  align-items-center" data-bs-toggle="tooltip" title="{{__('View Leave')}}" data-original-title="{{__('View Leave')}}">
                                                <i class="ti ti-caret-right text-white"></i>
                                            </a>
                                        </div>
                                        @if (\Auth::user()->type == 'company')
                                            <div class="action-btn bg-primary ms-2">
                                                <a href="#" data-url="{{ URL::to('leave/'.$leave->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Leave')}}" class="mx-3 btn btn-sm  align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}"><i class="ti ti-pencil text-white"></i></a>
                                            </div>
                                            <div class="action-btn bg-danger ms-2">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['leave.destroy', $leave->id],'id'=>'delete-form-'.$leave->id]) !!}
                                                <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$leave->id}}').submit();">
                                                    <i class="ti ti-trash text-white"></i>
                                                </a>
                                                {!! Form::close() !!}
                                            </div>
                                        @else
                                            @if($leave->isChecked == 0)
                                                @can('edit leave')
                                                    <div class="action-btn bg-primary ms-2">
                                                        <a href="#" data-url="{{ URL::to('leave/'.$leave->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Leave')}}" class="mx-3 btn btn-sm  align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}"><i class="ti ti-pencil text-white"></i></a>
                                                    </div>
                                                @endcan

                                                @can('delete leave')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['leave.destroy', $leave->id],'id'=>'delete-form-'.$leave->id]) !!}
                                                        <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$leave->id}}').submit();">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endcan
                                            @endif
                                        @endif

                                        @if ($isAdmin == 1)
                                            @if ($leave->statusChecked == 0)
                                                <div class="action-btn bg-success ms-2">
                                                    <a href="{{ route('leave.approve', $leave->id) }}"
                                                        class="btn btn-sm mx-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M5 12l5 5l10 -10" />
                                                          </svg></a>
                                                </div>
                                                <div class="action-btn bg-danger ms-2">
                                                    <a href="{{ route('leave.reject', $leave->id) }}"
                                                        class="btn btn-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M18 6l-12 12" />
                                                            <path d="M6 6l12 12" />
                                                          </svg>
                                                    </a>
                                                </div>
                                            @endif
                                        @endif
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
        $(document).on('change', '#employee_id', function () {
            var employee_id = $(this).val();

            $.ajax({
                url: '{{route('leave.jsoncount')}}',
                type: 'POST',
                data: {
                    "employee_id": employee_id, "_token": "{{ csrf_token() }}",
                },
                success: function (data) {

                    $('#leave_type_id').empty();
                    $('#leave_type_id').append('<option value="">{{__('Select Leave Type')}}</option>');

                    $.each(data, function (key, value) {

                        if (parseInt(value.total_leave) >= parseInt(value.days)) {
                            $('#leave_type_id').append('<option value="' + value.id + '" disabled>' + value.title + '&nbsp(' + value.total_leave + '/' + value.days + ')</option>');
                        } else {
                            $('#leave_type_id').append('<option value="' + value.id + '">' + value.title + '&nbsp(' + value.total_leave + '/' + value.days + ')</option>');
                        }
                    });

                }
            });
        });

    </script>
@endpush
