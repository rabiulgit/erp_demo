@extends('layouts.admin')
@section('page-title')
    {{__('Manage Vat Rate')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Vats')}}</li>
@endsection
@section('action-btn')
    
    <div class="float-end">
        {{-- @can('create constant vat') --}}
        {{-- too hot to handle
            <a href="#" data-url="{{ route('vats.create') }}" data-ajax-popup="true" data-title="{{__('Create Vat Rate')}}" data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        --}}
        {{-- @endcan --}}
    </div>
    
@endsection

@section('content')
    <div class="row">
        <div class="col-3">
            @include('layouts.account_setup')
        </div>
        <div class="col-9">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th> {{__('Vat Name')}}</th>
                                <th> {{__('Rate %')}}</th>
                                <th width="10%"> {{__('Action')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($vats as $vat)
                                <tr class="font-style">
                                    <td>{{ $vat->name }}</td>
                                    <td>{{ $vat->rate }}</td>
                                    <td class="Action">
                                        <span>
                                        {{-- @can('edit constant vat') --}}
                                                <div class="action-btn bg-primary ms-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="{{ route('vats.edit',$vat->id) }}" data-ajax-popup="true" data-title="{{__('Edit Vat Rate')}}" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                                </div>
                                            {{--
                                            @endcan
                                            @can('delete constant vat')
                                            --}}
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['vats.destroy', $vat->id],'id'=>'delete-form-'.$vat->id]) !!}
                                                        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$vat->id}}').submit();">
                                                <i class="ti ti-trash text-white"></i>
                                            </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            {{-- @endcan --}}
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
