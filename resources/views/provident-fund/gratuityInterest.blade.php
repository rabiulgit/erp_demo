@extends('layouts.admin')
@section('page-title')
    {{ __('Gratuity Interest') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Gratuity Interest') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <a href="{{ route('employee.export') }}" data-bs-toggle="tooltip" title="{{ __('Export') }}"
            class="btn btn-sm btn-primary">
            <i class="ti ti-file-export"></i>
        </a>
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
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Total Interest</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gratuityInterests as $index => $gratuityInterest)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $gratuityInterest->name }}</td>
                                        <td>{{$gratuityInterest->from_date}}</td>
                                        <td>{{$gratuityInterest->to_date}}</td>
                                        <td>{{ \Auth::user()->priceFormat($gratuityInterest->total_gi) }}</td>
                                        <td>
                                            @if ($gratuityInterest->total_gi > 0)
                                                <div class="action-btn bg-primary">
                                                    <a href="{{ route('gratuity.employeeInterest', \Illuminate\Support\Facades\Crypt::encrypt($gratuityInterest->employee_id)) }}"
                                                        class="mx-1 btn btn-sm align-items-center">
                                                        <i class="ti ti-eye text-white"></i>
                                                    </a>
                                                </div>
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
    </div>
@endsection
