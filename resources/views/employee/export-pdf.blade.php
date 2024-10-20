@php
    $logo = \App\Models\Utility::get_file('uploads/logo');
    $company_logo = \App\Models\Utility::GetLogo();
@endphp
@extends('layouts.admin')
@section('page-title')
    {{ __('All Employees') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Employee') }}</li>
@endsection
<style>
    table {
        width: 100%;
        table-layout: fixed;
        /* Fixed layout ensures the table fits in the available space */
        border-collapse: collapse;
        font-size: 11px;
        color: black;
    }

    th,
    td {
        border: 1px solid black;
        padding: 2px;
        text-align: center;
        word-break: break-word;
    }

    /* Prevent the table from being too wide */
    th,
    td {
        max-width: 100%;
    }

    /* Prevent page breaks inside table rows */
    tr {
        page-break-inside: avoid !important;
    }

    /* Add this to ensure the content fits */
    #printableArea {
        max-width: 100%;
        overflow: hidden;
    }
</style>

@section('content')
    <div class="d-flex justify-content-end mb-4">
        <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()">
            <i class="ti ti-download"></i> {{ __('Download as PDF') }}
        </a>
    </div>


    <div id="printableArea" class="container mt-5 mb-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="col-md-6 d-flex align-items-center ms-5">
                <img src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png') }}"
                    alt="Company Logo" width="80px" class="img-fluid">
            </div>
            <div class="col-md-2">

            </div>
            <!-- Company Info -->
            <div class="col-md-4 ml-auto text-right">
                <h6 class="mb-2"><strong>{{ \Utility::getValByName('company_name') }}</strong></h6>
                <p class="mb-1">{{ \Utility::getValByName('company_address') }},
                    {{ \Utility::getValByName('company_city') }}</p>
            </div>
        </div>

        <!-- Payslip Table -->
        <h4 class="text-primary text-center mb-4">{{ __('All Employee List') }}</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th style="width: 5%">{{ __('SN') }}</th>
                        <th style="width: 20%" class="text-start">{{ __('Name') }}</th>
                        <th style="width: 15%">{{ __('Emp ID') }}</th>
                        <th style="width: 20%">{{ __('Email') }}</th>
                        <th style="width: 15%">{{ __('Phone') }}</th>
                        <th style="width: 15%">{{ __('Designation') }}</th>
                        <th style="width: 10%">{{ __('DOJ') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sn = 1;
                    @endphp
                    @foreach ($datas as $data)
                        <tr>
                            <td>{{$sn++}}</td>
                            <td class="text-start">{{ $data->name }}</td>
                            <td>{{ $data->employee_id }}</td>
                            <td>{{ $data->email }}</td>
                            <td>{{ $data->phone }}</td>
                            <td>{{ $data->designation_id }}</td>
                            <td>{{ $data->company_doj }}</td>
                    @endforeach

                </tbody>
            </table>
        </div>

    </div>
@endsection
<!-- Include HTML2PDF JS library -->
<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>
    function saveAsPDF() {
        var element = document.getElementById('printableArea');
        var opt = {
            margin: 0.1,
            filename: 'employee_list_' + new Date().toISOString().split('T')[0] + '.pdf',
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 2, // Lower the scale to fit better
                windowWidth: element.scrollWidth, // Fit content width
                windowHeight: element.scrollHeight // Fit content height
            },
            jsPDF: {
                unit: 'in',
                format: 'letter',
                orientation: 'landscape' // Keep landscape orientation
            }
        };

        html2pdf().set(opt).from(element).save();
    }
</script>
