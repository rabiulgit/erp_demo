@extends('layouts.admin')
@section('page-title')
    {{__('Manage Lookup Data')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Lookup Data')}}</li>
@endsection
@section('content')
    <div class="row">
    <div class="col-xl-12">
            <div class="card">
            <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <a href="#" data-url="{{ route('lookupCreate') }}" style="margin:10px;"
                            data-size="md" data-ajax-popup="true" data-title="{{__('Create New Lookup')}}" data-toggle="tooltip" data-original-title="{{__('Lookup Data')}}" class="btn btn-sm btn-primary">
                            Create
                        </a>
                    <table class="table datatable">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Value</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($rows as $row)
                                <tr>
                                    <td class="Id"><b>{{$row->id}}</b></td>
                                    <td class="Id" style="color:blue;"><b>{{$row->name}}</b></td>
                                    <td class="Id">{{$row->value}}</td>
                                    <td class="Id">{{$row->created_by}}</td>
                                    <td class="Id">{{($row->created_at)? date('d-m-Y', strtotime($row->created_at)):''}}</td>
                                    <td class="Id">{{($row->updated_at)? date('d-m-Y', strtotime($row->updated_at)):''}}</td>

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


