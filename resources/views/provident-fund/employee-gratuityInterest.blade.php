@extends('layouts.admin')
@section('page-title')
    {{ __('Gratuity Interest') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('gratuity.interest') }}">{{ __('Gratuity Interest') }}</a></li>
    <li class="breadcrumb-item">{{$employeeName}}</li>
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
                                    <th>Total Fund</th>
                                    <th>Interest Percentage</th>
                                    <th>Interest Amount</th>
                                    <th>Interest Month</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gratuityInterests as $index => $interest)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ \Auth::user()->priceFormat($interest->total_gratuity) }}</td>
                                        <td>{{ $interest->interest_value }}%</td>
                                        <td>{{ \Auth::user()->priceFormat($interest->interest_amount) }}</td>
                                        <td>{{ date('F, Y', strtotime($interest->interest_month)) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($interest->created_at)->format('d-m-Y')}}</td>
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
