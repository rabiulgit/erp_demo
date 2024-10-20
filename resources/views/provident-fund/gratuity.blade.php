@extends('layouts.admin')
@section('page-title')
    {{__('Gratuity')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Gratuity')}}</li>
@endsection

@push('script-page')
<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>

    function saveAsPDF() {
        document.getElementById('printableArea').style.display = 'block';

        var element = document.getElementById('printableArea');
        var opt = {
            margin: 0.3,
            filename: "Gratuity Report",
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
        <a href="{{ route('gratuity.export') }}" data-bs-toggle="tooltip" title="{{ __('Export') }}"
            class="btn btn-sm btn-primary">
            <i class="ti ti-file-export"></i>
        </a>

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
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Total Fund</th>
                                <th>Total Interest</th>
                                <th>Total Gratuity</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($gratuities as $gratuity)
                                <tr>
                                    <td>{{$gratuity->employee_id}}</td>
                                    <td>{{$gratuity->name}}</td>
                                    <td>{{$gratuity->from_date}}</td>
                                    <td>{{$gratuity->to_date}}</td>
                                    <td>{{\Auth::user()->priceFormat($gratuity->total_gf)}}</td>
                                    <td>{{\Auth::user()->priceFormat($gratuity->total_gi)}}</td>
                                    <td>{{\Auth::user()->priceFormat($gratuity->total_gi+$gratuity->total_gf)}}</td>
                                    <td>
                                        @if ($gratuity->total_gf > 0)
                                        <div class="action-btn bg-primary">
                                            <a href="{{ route('gratuity.employee', \Illuminate\Support\Facades\Crypt::encrypt($gratuity->employee_id)) }}"
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
        <h4 class="mb-2">Gratuity Report</h4>
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
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Total Fund</th>
                                <th>Total Interest</th>
                                <th>Total Gratuity</th>
                            </tr>
                            @php
                                $total_fund=0;
                                $total_interest=0;
                            @endphp
                            @foreach ($gratuities as $gratuity)
                            @php
                                $total_fund += $gratuity->total_gf;
                                $total_interest += $gratuity->total_gi;
                            @endphp
                                <tr>
                                    <td>{{$gratuity->employee_id}}</td>
                                    <td>{{$gratuity->name}}</td>
                                    <td>{{$gratuity->from_date}}</td>
                                    <td>{{$gratuity->to_date}}</td>
                                    <td>{{\Auth::user()->priceFormat($gratuity->total_gf)}}</td>
                                    <td>{{\Auth::user()->priceFormat($gratuity->total_gi)}}</td>
                                    <td>{{\Auth::user()->priceFormat($gratuity->total_gi+$gratuity->total_gf)}}</td>
                                </tr>
                            @endforeach
                            <tr class="font-weight-bold" style="background-color: rgba(81, 69, 157, 0.1)">
                                <th></th>
                                <th>Total:</th>
                                <th></th>
                                <th></th>
                                <th>{{ \Auth::user()->priceFormat($total_fund) }}</th>
                                <th>{{ \Auth::user()->priceFormat($total_interest) }}</th>
                                <th>{{ \Auth::user()->priceFormat($total_fund+$total_interest) }}</th>
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
