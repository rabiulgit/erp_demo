@extends('layouts.admin')
@section('page-title')
    {{ __('Tax Report') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Tax Report') }}</li>
@endsection

@push('script-page')
<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>

    function saveAsPDF() {
        document.getElementById('printableArea').style.display = 'block';

        var element = document.getElementById('printableArea');
        var opt = {
            margin: 0.3,
            filename: "Tax Report-March 2024",
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 5,
                dpi: 72,
                letterRendering: true
            },
            jsPDF: {
                unit: 'in',
                format: 'A4'
            }
        };
        html2pdf().set(opt).from(element).save();

        setTimeout(() => {
            document.getElementById('printableArea').style.display = 'none';
        }, 100);

    }

    $(document).ready(function () {
        var filename = $('#filename').val();
        $('#report-dataTable').DataTable({
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'pdf',
                    title: filename
                },
                {
                    extend: 'excel',
                    title: filename
                }, {
                    extend: 'csv',
                    title: filename
                }
            ]
        });
    });
</script>
@endpush

@section('action-btn')
    <div class="float-end">
        <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()"data-bs-toggle="tooltip" title="{{__('Download')}}" data-original-title="{{__('Download')}}">
            <span class="btn-inner--icon"><i class="ti ti-download"></i></span>
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
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Basic Salary</th>
                                    <th>Allowance</th>
                                    <th>Tax</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($taxReport as $tax)
                                    <tr>
                                        <td>{{ $tax->employee_id }}</td>
                                        <td>{{ $tax->name }}</td>
                                        <td>{{ \Auth::user()->priceFormat($tax->basic_salary) }}</td>
                                        <td>{{ \Auth::user()->priceFormat($tax->allowance) }}</td>
                                        <td>{{ \Auth::user()->priceFormat($tax->tax) }}</td>
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

    @php
        $logo = \App\Models\Utility::get_file('uploads/logo');
        $company_logo = \App\Models\Utility::GetLogo();
    @endphp

    <div id="printableArea" class="mt-2">

        <div class="invoice-print">
            <div class="row text-sm">
                <div class="col-md-6">
                    <img src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png') }}"
                        width="120px;">
                </div>
                <div class="col-md-6 text-end">
                    <address>
                        <strong>{{ \Utility::getValByName('company_name') }} </strong><br>
                        {{ \Utility::getValByName('company_address') }} ,
                        {{ \Utility::getValByName('company_city') }},<br>
                        {{ \Utility::getValByName('company_state') }}-{{ \Utility::getValByName('company_zipcode') }}<br>
                    </address>
                </div>
            </div>

            <div class="row text-center">
                <h4 class="mb-2">Tax Report</h4>
                <h5>March, 2024</h5>
            </div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card-body table-border-style">

                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    <tr class="font-weight-bold">
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Basic Salary</th>
                                        <th>Allowance</th>
                                        <th>Tax</th>
                                    </tr>
                                    @php
                                        $total_salary=0;
                                        $total_allowance=0;
                                        $total_tax=0;
                                    @endphp
                                    @foreach ($taxReport as $tax)
                                    @php
                                        $total_salary += $tax->basic_salary;
                                        $total_allowance += $tax->allowance;
                                        $total_tax += $tax->tax;
                                    @endphp
                                        <tr>
                                            <td>{{ $tax->employee_id }}</td>
                                            <td>{{ $tax->name }}</td>
                                            <td>{{ \Auth::user()->priceFormat($tax->basic_salary) }}</td>
                                            <td>{{ \Auth::user()->priceFormat($tax->allowance) }}</td>
                                            <td>{{ \Auth::user()->priceFormat($tax->tax) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="font-weight-bold" style="background-color: rgba(81, 69, 157, 0.1)">
                                        <th></th>
                                        <th>Total:</th>
                                        <th>{{ \Auth::user()->priceFormat($total_salary) }}</th>
                                        <th>{{ \Auth::user()->priceFormat($total_allowance) }}</th>
                                        <th>{{ \Auth::user()->priceFormat($total_tax) }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <style>
        #printableArea {
            display: none;
        }

        #printableArea .table.table-sm td,
        #printableArea .table.table-sm th {
            padding: 0.5rem 0rem;
        }
    </style>

@endsection
