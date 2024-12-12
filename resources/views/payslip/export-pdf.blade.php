@php
    $logo = \App\Models\Utility::get_file('uploads/logo');
    $company_logo = \App\Models\Utility::GetLogo();
@endphp
@extends('layouts.admin')
@section('page-title')
    {{ __('Payslip') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('payslip') }}</li>
@endsection
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid black;
        padding: 8px;
        text-align: center;
    }

    /* Prevent page breaks inside table rows */
    tr {
        page-break-inside: avoid !important;
    }

    /* Optionally ensure the table doesn't break unnecessarily */
    table,
    h4 {
        page-break-after: auto;
        page-break-before: auto;
    }
</style>

@section('content')
    <div class="d-flex justify-content-end mb-4">
        <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()">
            <i class="ti ti-download"></i> {{ __('Download as PDF') }}
        </a>
    </div>

    <div id="printableArea" class="container mt-5 mb-4">
        <!-- Header -->
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
                <p class="mb-1"><strong>Salary Month: </strong> {{ $payslipData[0]['salary_month'] }}</p>
            </div>
        </div>



        <!-- Payslip Table -->
        <h4 class="text-primary text-center mb-4">{{ __('Salary Sheet') }}</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>SN</th>
                        <th>Employee ID</th>
                        <th class="text-start">{{ __('Employee Name') }}</th>
                        <th>{{ __('Basic Salary') }}</th>
                        <th>{{ __('Net Salary') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sn = 1;
                        $totalBasicSalary = 0;
                        $totalNetSalary = 0;
                    @endphp

                    @foreach ($payslipData as $data)
                        @php
                            // Convert salary fields to numeric values
                            $basicSalary = (float) $data['basic_salary'];
                            $netSalary = (float) $data['net_salary'];

                            // Accumulate totals
                            $totalBasicSalary += $basicSalary;
                            $totalNetSalary += $netSalary;
                        @endphp
                        <tr>
                            <td>{{ $sn++ }}</td>
                            <td>{{ $data['employee_id'] }}</td>
                            <td class="text-start">{{ $data['employee_name'] }}</td>
                            <td>{{ \Auth::user()->priceFormat($basicSalary) }}</td>
                            <td>{{ \Auth::user()->priceFormat($netSalary) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">{{ __('Total') }}</th>
                        <th>{{ \Auth::user()->priceFormat($totalBasicSalary) }}</th>
                        <th>{{ \Auth::user()->priceFormat($totalNetSalary) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Signature Section -->
        <div class="row mt-2">
            <div class="col-md-4 text-center mt-3">
                <p></p>
                <div style="border-bottom: 1px solid black; width: 60%; margin: 0 auto;"></div>
                <p class="mt-3"><strong>Managing Director</strong></p>
            </div>
            <div class="col-md-4 text-center mt-3">
                <p></p>
                <div style="border-bottom: 1px solid black; width: 60%; margin: 0 auto;"></div>
                <p class="mt-3"><strong>CEO</strong></p>
            </div>
            <div class="col-md-4 text-center mt-3">
                <p></p>
                <div style="border-bottom: 1px solid black; width: 60%; margin: 0 auto;"></div>
                <p class="mt-3"><strong>Finance</strong></p>
            </div>
        </div>

    </div>
@endsection
<!-- Include HTML2PDF JS library -->
<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>
    function saveAsPDF() {
        var element = document.getElementById('printableArea');
        var opt = {
            margin: 0.2,
            filename: 'salary_sheet_' + new Date().toISOString().split('T')[0] + '.pdf',
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 2
            },
            jsPDF: {
                unit: 'in',
                format: 'letter',
                orientation: 'portrait'
            }
        };

        html2pdf().set(opt).from(element).save();
    }
</script>
