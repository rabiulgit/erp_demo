@extends('layouts.admin')
@section('page-title')
    {{__('Manage Job')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Job')}}</li>
@endsection
@push('script-page')


    <script>
        function copyToClipboard(element) {

            var copyText = element.id;
            navigator.clipboard.writeText(copyText);
            // document.addEventListener('copy', function (e) {
            //     e.clipboardData.setData('text/plain', copyText);
            //     e.preventDefault();
            // }, true);
            //
            // document.execCommand('copy');
            show_toastr('success', 'Url copied to clipboard', 'success');
        }
    </script>


@endpush


@section('action-btn')
    <div class="float-end">
        @can('create job')
            <a href="{{ route('job.create') }}" class="btn btn-sm btn-primary"  data-bs-toggle="tooltip" title="{{__('Create')}}" data-title="{{__('Create New Job')}}">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-primary">
                                    <i class="ti ti-cast"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{__('Total')}}</small>
                                    <h6 class="m-0">{{__('Jobs')}}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <h4 class="m-0">{{$data['total']}}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-info">
                                    <i class="ti ti-cast"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{__('Active')}}</small>
                                    <h6 class="m-0">{{__('Jobs')}}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <h4 class="m-0">{{$data['active']}}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-warning">
                                    <i class="ti ti-cast"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{__('Inactive')}}</small>
                                    <h6 class="m-0">{{__('Jobs')}}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <h4 class="m-0">{{$data['in_active']}}</h4>
                        </div>
                    </div>
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
                                <th>{{__('Branch')}}</th>
                                <th>{{__('Title')}}</th>
                                <th>{{__('Start Date')}}</th>
                                <th>{{__('End Date')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Approvals')}}</th>
                                <th>{{__('Created At')}}</th>
                                @if( Gate::check('edit job') ||Gate::check('delete job') ||Gate::check('show job'))
                                    <th width="200px">{{__('Action')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody class="font-style">
                            @foreach ($jobs as $job)
                                <tr>
                                    <td>{{ !empty($job->branches)?$job->branches->name:__('All') }}</td>
                                    <td>{{$job->title}}</td>
                                    <td>{{\Auth::user()->dateFormat($job->start_date)}}</td>
                                    <td>{{\Auth::user()->dateFormat($job->end_date)}}</td>
                                    <td>
                                        @if($job->status=='active')
                                            <span class="status_badge badge bg-success p-2 px-3 rounded">{{App\Models\Job::$status[$job->status]}}</span>
                                        @else
                                            <span class="status_badge badge bg-danger p-2 px-3 rounded">{{App\Models\Job::$status[$job->status]}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($job->approvals=="Pending")
                                            <div class="status_badge badge bg-warning p-2 px-3 rounded">{{ $job->approvals }}</div>
                                        @elseif($job->approvals=="Approved")
                                            <div class="status_badge badge bg-success p-2 px-3 rounded">{{ $job->approvals }}</div>
                                        @else
                                            <div class="status_badge badge bg-danger p-2 px-3 rounded">{{ $job->approvals }}</div>
                                        @endif
                                    </td>
                                    <td>{{ \Auth::user()->dateFormat($job->created_at) }}</td>
                                    @if( Gate::check('edit job') ||Gate::check('delete job') || Gate::check('show job'))
                                        <td>

                                            @if($job->status!='in_active')
                                                <div class="action-btn bg-warning ms-2">
                                                    <a href="#" id="{{ route('job.requirement',[$job->code,!empty($job)?$job->createdBy->lang:'en']) }}" class="mx-3 btn btn-sm align-items-center"  onclick="copyToClipboard(this)" data-bs-toggle="tooltip" title="{{__('Copy')}}" data-original-title="{{__('Click to copy')}}"><i class="ti ti-link text-white"></i></a>
                                                </div>
                                            @endif
                                            @can('show job')
                                                <div class="action-btn bg-info ms-2">
                                                    <a href="{{ route('job.show',$job->id) }}" data-title="{{__('Job Detail')}}" title="{{__('View')}}"  class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" data-original-title="{{__('View Detail')}}">
                                                        <i class="ti ti-eye text-white"></i></a>
                                                </div>
                                            @endcan
                                            @can('edit job')
                                                <div class="action-btn bg-primary ms-2">
                                                    <a href="{{ route('job.edit',$job->id) }}" data-title="{{__('Edit Job')}}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                        <i class="ti ti-pencil text-white"></i></a>
                                                </div>
                                            @endcan
                                            @can('delete job')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['job.destroy', $job->id],'id'=>'delete-form-'.$job->id]) !!}

                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$job->id}}').submit();">
                                                        <i class="ti ti-trash text-white"></i></a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan

                                            @if ($isAdmin == 1)
                                                @if ($job->statusChecked == 0)
                                                    <div class="action-btn bg-success ms-2">
                                                        <a href="{{ route('job.approve', $job->id) }}"
                                                            class="btn btn-sm mx-3">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M5 12l5 5l10 -10" />
                                                            </svg></a>
                                                    </div>
                                                    <div class="action-btn bg-danger ms-2">
                                                        <a href="{{ route('job.reject', $job->id) }}"
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
                                    @endif
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
