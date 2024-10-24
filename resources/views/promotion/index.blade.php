@extends('layouts.admin')

@section('page-title')
    {{__('Manage Promotion')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Promotion')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
    @can('create promotion')
            <a href="#" data-url="{{ route('promotion.create') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Create New Promotion')}}" data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
            <div class="card-body table-border-style">
                    <div class="table-responsive">
                    <table class="table datatable">
                            <thead>
                            <tr>
                                @role('company')
                                <th>{{__('Employee Name')}}</th>
                                @endrole
                                <th>{{__('Designation')}}</th>
                                <th>{{__('Promotion Title')}}</th>
                                <th>{{__('Promotion Date')}}</th>
                                <th>{{__('Description')}}</th>
                                <th>{{__('Status')}}</th>
                                @if(Gate::check('edit promotion') || Gate::check('delete promotion'))
                                    <th width="200px">{{__('Action')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody class="font-style">
                            @foreach ($promotions as $promotion)
                                <tr>
                                    @role('company')
                                    <td>{{ !empty($promotion->employee)?$promotion->employee->name:'' }}</td>
                                    @endrole
                                    <td>{{ !empty($promotion->designation)?$promotion->designation->name:'' }}</td>
                                    <td>{{ $promotion->promotion_title }}</td>
                                    <td>{{  \Auth::user()->dateFormat($promotion->promotion_date) }}</td>
                                    <td>{{ $promotion->description }}</td>
                                    <td>
                                        @if ($isAdmin == 1)
                                            @if($promotion->statusChecked == 0)
                                                <div class="status_badge badge bg-warning p-2 px-3 rounded">Pending</div>
                                            @else
                                                @if ($promotion->leaveStatus == 0)
                                                    <div class="status_badge badge bg-danger p-2 px-3 rounded">Rejected</div>
                                                @else
                                                    <div class="status_badge badge bg-success p-2 px-3 rounded">Approved</div>                                               
                                                @endif
                                            @endif
                                        @else
                                            @if($promotion->status=="Pending")
                                                <div class="status_badge badge bg-warning p-2 px-3 rounded">{{ $promotion->status }}</div>
                                            @elseif($promotion->status=="Approved")
                                                <div class="status_badge badge bg-success p-2 px-3 rounded">{{ $promotion->status }}</div>
                                            @else
                                                <div class="status_badge badge bg-danger p-2 px-3 rounded">{{ $promotion->status }}</div>
                                            @endif
                                        @endif
                                        
                                    </td>
                                    @if(Gate::check('edit promotion') || Gate::check('delete promotion'))
                                        <td>
                                         
                                           @can('edit promotion')
                                                <div class="action-btn bg-primary ms-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="{{ URL::to('promotion/'.$promotion->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Promotion')}}" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                                </div>
                                           @endcan

                                            @can('delete promotion')
                                                <div class="action-btn bg-danger ms-2">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['promotion.destroy', $promotion->id],'id'=>'delete-form-'.$promotion->id]) !!}
                                                        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$promotion->id}}').submit();">
                                                <i class="ti ti-trash text-white"></i>
                                                </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan

                                            @if ($isAdmin == 1)
                                                @if ($promotion->statusChecked == 0)
                                                    <div class="action-btn bg-success ms-2">
                                                        <a href="{{ route('promotion.approve', $promotion->id) }}"
                                                            class="btn btn-sm mx-3">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M5 12l5 5l10 -10" />
                                                            </svg></a>
                                                    </div>
                                                    <div class="action-btn bg-danger ms-2">
                                                        <a href="{{ route('promotion.reject', $promotion->id) }}"
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
