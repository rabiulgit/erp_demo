@extends('layouts.admin')
@section('page-title')
    {{ __('Provident Fund') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('provident-fund.index') }}">{{ __('Provident Fund') }}</a></li>
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
                                    <th>Own Provident</th>
                                    <th>organization Provident</th>
                                    <th>Total Provident</th>
                                    <th>Provident Month</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($providentFunds as $index => $provident)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ \Auth::user()->priceFormat($provident->own_pf) }}</td>
                                        <td>{{ \Auth::user()->priceFormat($provident->organization_pf) }}</td>
                                        <td>{{ \Auth::user()->priceFormat($provident->total_pf) }}</td>
                                        <td>{{ date('F, Y', strtotime($provident->provident_month)) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($provident->created_at)->format('d-m-Y')}}</td>
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
