@extends('layouts.admin')
@section('page-title')
    {{ __('Gratuity') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('gratuity.index') }}">{{ __('Gratuity') }}</a></li>
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
                                    <th>Gratuity Amount</th>
                                    <th>Gratuity Month</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gratuities as $index => $gratuity)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ \Auth::user()->priceFormat($gratuity->amount) }}</td>
                                        <td>{{ date('F, Y', strtotime($gratuity->gratuity_month)) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($gratuity->created_at)->format('d-m-Y')}}</td>
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
