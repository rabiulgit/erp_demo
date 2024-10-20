@extends('layouts.admin')
@section('page-title')
    {{ __('Approval Setup') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Approval Setup') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">

            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-hrm-tab" data-bs-toggle="pill" href="#hrm" role="tab"
                        aria-controls="pills-home" aria-selected="true"><b>HRM</b></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-crm-tab" data-bs-toggle="pill" href="#crm" role="tab"
                        aria-controls="pills-profile" aria-selected="false"><b>CRM</b></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-project-tab" data-bs-toggle="pill" href="#project" role="tab"
                        aria-controls="pills-contact" aria-selected="false"><b>Project</b></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-account-tab" data-bs-toggle="pill" href="#account" role="tab"
                        aria-controls="pills-contact" aria-selected="false"><b>Account</b></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-account-tab" data-bs-toggle="pill" href="#pos" role="tab"
                        aria-controls="pills-contact" aria-selected="false"><b>POS</b></a>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="hrm" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="col-md-12">
                        <ul class="nav nav-pills justify-content-center mb-4" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-hrm-tab" data-bs-toggle="pill" href="#leave"
                                    role="tab" aria-controls="pills-home" aria-selected="true">{{ __('Leave') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-crm-tab" data-bs-toggle="pill" href="#payroll" role="tab"
                                    aria-controls="pills-profile" aria-selected="false">{{ __('Payroll') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-crm-tab" data-bs-toggle="pill" href="#recruitment" role="tab"
                                    aria-controls="pills-profile" aria-selected="false">{{ __('Recruitment') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-crm-tab" data-bs-toggle="pill" href="#promotion" role="tab"
                                    aria-controls="pills-profile" aria-selected="false">{{ __('Promotion') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-crm-tab" data-bs-toggle="pill" href="#termination" role="tab"
                                    aria-controls="pills-profile" aria-selected="false">{{ __('Termination') }}</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="leave" role="tabpanel"
                                aria-labelledby="pills-home-tab">
                                <div class="col-md-12">
                                    @include('approvalSetup.components.leave')
                                </div>
                            </div>

                            <div class="tab-pane fade" id="payroll" role="tabpanel" aria-labelledby="pills-home-tab">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        @include('approvalSetup.components.payroll')
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="recruitment" role="tabpanel" aria-labelledby="pills-home-tab">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        @include('approvalSetup.components.recruitment')
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="promotion" role="tabpanel" aria-labelledby="pills-home-tab">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        @include('approvalSetup.components.promotion')
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="termination" role="tabpanel" aria-labelledby="pills-home-tab">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        @include('approvalSetup.components.termination')
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="crm" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="col-md-12">
                        <h5 class="text-center">Coming Soon!</h5>
                    </div>
                </div>

                <div class="tab-pane fade" id="project" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="col-md-12">
                        <h5 class="text-center">Coming Soon!</h5>
                    </div>
                </div>

                <div class="tab-pane fade" id="account" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="col-md-12">
                        <h5 class="text-center">Coming Soon!</h5>
                    </div>
                </div>

                <div class="tab-pane fade" id="pos" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="col-md-12">
                        <h5 class="text-center">Coming Soon!</h5>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .timeline:before {
            width: 0px;
        }

        .timeline-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            position: relative;
        }

        .timeline-item .actions {
            position: absolute;
            right: -50px;
        }

        .timeline-content {
            background-color: #f9f9f9;
            padding: 12px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .timeline-content h3 {
            margin-top: 0;
            font-size: 18px;
        }

        .timeline-item h6 {
            font-size: 28px;
        }

        .timeline-arrow {
            font-size: 24px;
            margin: 0;
            padding: 0;
            line-height: 1;
        }

        .timeline-item:nth-child(even) .timeline-arrow {
            transform: rotate(180deg);
        }
    </style>
@endsection
