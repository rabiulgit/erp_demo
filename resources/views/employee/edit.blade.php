@extends('layouts.admin')
@section('page-title')
    {{ __('Edit Employee') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('employee.index') }}">{{ __('Employee') }}</a></li>
    <li class="breadcrumb-item">{{ $employee->employee_id }}</li>
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            {{ Form::model($employee, ['route' => ['employee.update', $employee->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
            @csrf
        </div>
    </div>
    <div class="row">
        {{-- Personal Details Start --}}
        <div class="col-md-6 ">
            <div class="card emp_details">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('Personal Detail') }}</h6>
                </div>
                <div class="card-body employee-detail-edit-body">

                    <div class="row">
                        <div class="form-group col-md-6">
                            {!! Form::label('name', __('Name'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('email', __('Email'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                            {!! Form::text('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('phone', __('Phone'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                            {!! Form::number('phone', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group col-md-6">

                            {!! Form::label('dob', __('Date of Birth'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                            {!! Form::date('dob', null, ['class' => 'form-control']) !!}

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('religion', __('Religion'), ['class' => 'form-label']) !!}
                                {!! Form::text('religion', old('religion'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter Religion',
                                ]) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('nationality', __('Nationality'), ['class' => 'form-label']) !!}
                                {!! Form::text('nationality', old('nationality'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter Nationality',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('nid', __('NID'), ['class' => 'form-label']) !!}
                                {!! Form::text('nid', old('NID'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter NID',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('passport', __('Passport'), ['class' => 'form-label']) !!}
                                {!! Form::text('passport', old('passport'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter Passport No.',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('bg', __('Blood Group'), ['class' => 'form-label']) !!}
                                {!! Form::text('bg', old('bg'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter Blood Group',
                                ]) !!}
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('gender', __('Gender'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                            <div class="d-flex radio-check mt-2">
                                <div class="form-check form-check-inline form-group">
                                    <input type="radio" id="g_male" value="Male" name="gender"
                                        class="form-check-input" {{ $employee->gender == 'Male' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="g_male">{{ __('Male') }}</label>
                                </div>
                                <div class="form-check form-check-inline form-group">
                                    <input type="radio" id="g_female" value="Female" name="gender"
                                        class="form-check-input" {{ $employee->gender == 'Female' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="g_female">{{ __('Female') }}</label>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('address', __('Address'), ['class' => 'form-label']) !!}
                        {!! Form::textarea('address', null, ['class' => 'form-control', 'rows' => 2]) !!}
                    </div>

                </div>
            </div>
        </div>
        {{-- Personal Details End --}}

        {{-- Family Start --}}
        <div class="col-md-6">
            <div class="card em-card">
                <div class="card-header">
                    <h5>{{ __('Family Detail') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            {!! Form::label('father_name', __("Father's Name"), ['class' => 'form-label']) !!}
                            {!! Form::text('father_name', old('father_name'), [
                                'class' => 'form-control',
                                'placeholder' => "Enter Father's Name",
                            ]) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('father_occupation', __("Father's Occupation"), ['class' => 'form-label']) !!}
                            {!! Form::text('father_occupation', old('father_occupation'), [
                                'class' => 'form-control',
                                'placeholder' => "Enter Father's Occupation",
                            ]) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('mother_name', __("Mother's Name"), ['class' => 'form-label']) !!}
                            {!! Form::text('mother_name', old('mother_name'), [
                                'class' => 'form-control',
                                'placeholder' => "Enter Mother's Name",
                            ]) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('mother_occupation', __("Mother's Occupation"), ['class' => 'form-label']) !!}
                            {!! Form::text('mother_occupation', old('mother_occupation'), [
                                'class' => 'form-control',
                                'placeholder' => "Enter Mother's Occupation",
                            ]) !!}
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('marital_status', __('Marital Status'), ['class' => 'form-label']) !!}
                                {!! Form::text('marital_status', old('marital_status'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter Marital Status',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-6">

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('spouse_name', __('Spouse Name'), ['class' => 'form-label']) !!}
                                {!! Form::text('spouse_name', old('spouse_name'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter Spouse Name',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('spouse_occupation', __('Spouse Occupation'), ['class' => 'form-label']) !!}
                                {!! Form::text('spouse_occupation', old('spouse_occupation'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter Spouse Occupation',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Family End --}}


        {{-- Document Start --}}
        <div class="col-md-6 ">
            <div class="card emp_details">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('Document') }}</h6>
                </div>
                <div class="card-body employee-detail-edit-body">
                    @php
                        $employeedoc = $employee->documents()->pluck('document_value', __('document_id'));
                    @endphp

                    @foreach ($documents as $key => $document)
                        <div class="row">
                            <div class="form-group col-12">
                                <div class="float-left col-4">
                                    <label for="document" class="float-left pt-1 form-label">{{ $document->name }}
                                        @if ($document->is_required == 1)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                </div>
                                <div class="float-right col-4">
                                    <input type="hidden" name="emp_doc_id[{{ $document->id }}]" id=""
                                        value="{{ $document->id }}">
                                    <div class="choose-file form-group">
                                        <label for="document[{{ $document->id }}]">
                                            <input
                                                class="form-control @if (!empty($employeedoc[$document->id])) float-left @endif @error('document') is-invalid @enderror border-0"
                                                @if ($document->is_required == 1 && empty($employeedoc[$document->id])) required @endif
                                                name="document[{{ $document->id }}]"
                                                onchange="document.getElementById('{{ 'blah' . $key }}').src = window.URL.createObjectURL(this.files[0])"
                                                type="file" data-filename="{{ $document->id . '_filename' }}">
                                        </label>
                                        <p class="{{ $document->id . '_filename' }}"></p>

                                        @php
                                            $logo = \App\Models\Utility::get_file('uploads/document/');
                                        @endphp

                                        {{--                                            <img id="{{'blah'.$key}}" src=""  width="25%" /> --}}
                                        <img id="{{ 'blah' . $key }}"
                                            src="{{ isset($employeedoc[$document->id]) && !empty($employeedoc[$document->id]) ? $logo . '/' . $employeedoc[$document->id] : '' }}"
                                            width="25%" />

                                    </div>


                                    {{--                                        @if (!empty($employeedoc[$document->id])) --}}
                                    {{--                                            <br> <span class="text-xs"><a href="{{ (!empty($employeedoc[$document->id])?asset(Storage::url('uploads/document')).'/'.$employeedoc[$document->id]:'') }}" target="_blank">{{ (!empty($employeedoc[$document->id])?$employeedoc[$document->id]:'') }}</a> --}}
                                    {{--                                                    </span> --}}
                                    {{--                                        @endif --}}
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        {{-- Document End --}}

        {{-- Bank Account Start --}}
        <div class="col-md-6">
            <div class="card emp_details">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('Bank Account Detail') }}</h6>
                </div>
                <div class="card-body employee-detail-edit-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            {!! Form::label('account_holder_name', __('Account Holder Name'), ['class' => 'form-label']) !!}
                            {!! Form::text('account_holder_name', null, ['class' => 'form-control']) !!}

                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('account_number', __('Account Number'), ['class' => 'form-label']) !!}
                            {!! Form::number('account_number', null, ['class' => 'form-control']) !!}

                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('bank_name', __('Bank Name'), ['class' => 'form-label']) !!}
                            {!! Form::text('bank_name', null, ['class' => 'form-control']) !!}

                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('bank_identifier_code', __('Bank Identifier Code'), ['class' => 'form-label']) !!}
                            {!! Form::text('bank_identifier_code', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('branch_location', __('Branch Location'), ['class' => 'form-label']) !!}
                            {!! Form::text('branch_location', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('tax_payer_id', __('Tax Payer Id'), ['class' => 'form-label']) !!}
                            {!! Form::text('tax_payer_id', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Bank Account End --}}

        @if (\Auth::user()->type != 'Employee')
            {{-- Company Details Start --}}
            <div class="col-md-6 ">
                <div class="card emp_details">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('Company Details') }}</h6>
                    </div>
                    <div class="card-body employee-detail-edit-body">
                        <div class="row">
                            @csrf
                            <div class="form-group col-md-12">
                                {!! Form::label('employee_id', __('Employee ID'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                {!! Form::text('employee_id', $employee->employee_id, ['class' => 'form-control']) !!}
                            </div>

                            <div class="form-group col-md-6">
                                {{ Form::label('branch_id', __('Branch'), ['class' => 'form-label']) }}
                                {{ Form::select('branch_id', $branches, null, ['class' => 'form-control select', 'id' => 'branch_id']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('department_id', __('Department'), ['class' => 'form-label']) }}
                                {{ Form::select('department_id', $departments, null, ['class' => 'form-control select', 'id' => 'department_id']) }}

                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('designation_id', __('Designation'), ['class' => 'form-label']) }}
                                <select class="select form-control " id="designation_id" name="designation_id"></select>

                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('company_doj', 'Company Date Of Joining', ['class' => 'form-label']) !!}
                                {!! Form::date('company_doj', null, ['class' => 'form-control']) !!}
                            </div>

                            <div class="form-group col-md-6">
                                {!! Form::label('team_id', __('Assign to team'), ['class' => '  form-label']) !!}
                                {{ Form::select('team_id', $teams, null, ['class' => 'form-control select', 'id' => 'team_id', 'placeholder' => 'Assign a Team']) }}
                            </div>

                            <div class="form-group col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="team_lead" name="team_lead"
                                        value="1"
                                        {{ !empty($employee['team_lead']) && $employee['team_lead'] == '1' ? 'checked' : '' }} />
                                    <label class="form-check-label f-w-600 pl-1"
                                        for="team_lead">{{ __('Team Leader') }}</label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            {{-- Company Details End --}}


            {{-- Equipment Allocation Start --}}
            <div class="col-md-6">
                <div class="card emp_details">
                    <div class="card-header">
                        <h5>{{ __('Equipment Allocation') }}</h5>
                    </div>
                    <div class="card-body employee-detail-edit-body">
                        <div class="row">

                            <div class="form-group col-md-6">
                                {!! Form::label('mobile_name', __('Mobile Name/Model'), ['class' => 'form-label']) !!}
                                {!! Form::text('mobile_name', old('mobile_name'), [
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('date_receive_mobile', __('Date of receive'), ['class' => 'form-label']) !!}
                                {!! Form::date('date_receive_mobile', old('date_receive_mobile'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter date of receive',
                                ]) !!}

                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('sim_no', __('Sim Card No.'), ['class' => 'form-label']) !!}
                                {!! Form::text('sim_no', old('sim_no'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter Sim Card No.',
                                ]) !!}

                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('date_receive_sim', __('Date of receive'), ['class' => 'form-label']) !!}
                                {!! Form::date('date_receive_sim', old('date_receive_sim'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter date of receive',
                                ]) !!}

                            </div>
                            <div class="form-group col-md-12">
                                {!! Form::label('laptop_name', __('Laptop name/model'), ['class' => 'form-label']) !!}
                                {!! Form::text('laptop_name', old('laptop_name'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter Laptop name/model',
                                ]) !!}
                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('product_code', __('Product ID/Code'), ['class' => 'form-label']) !!}
                                {!! Form::text('product_code', old('product_code'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter Product Id/Code',
                                ]) !!}

                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('date_receive_laptop', __('Date of receive'), ['class' => 'form-label']) !!}
                                {!! Form::date('date_receive_laptop', old('date_receive_laptop'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter date of receive',
                                ]) !!}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Equipment Allocation End --}}
        @endif
        {{-- Emergency Contact Start --}}
        <div class="col-md-6">
            <div class="card em-card">
                <div class="card-header">
                    <h5>{{ __('Emergency Contact') }}</h5>
                </div>
                <div class="card-body employee-detail-create-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            {!! Form::label('emergency_contact_name', __('Name'), ['class' => 'form-label']) !!}
                            {!! Form::text('emergency_contact_name', old('emergency_contact_name'), [
                                'class' => 'form-control',
                                'placeholder' => 'Enter emergency contact name',
                            ]) !!}

                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('emergency_contact_phone', __('Phone'), ['class' => 'form-label']) !!}
                            {!! Form::text('emergency_contact_phone', old('emergency_contact_phone'), [
                                'class' => 'form-control',
                                'placeholder' => 'Enter emergency contact phone',
                            ]) !!}

                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('emergency_contact_relation', __('Relation'), ['class' => 'form-label']) !!}
                            {!! Form::text('emergency_contact_relation', old('emergency_contact_relation'), [
                                'class' => 'form-control',
                                'placeholder' => 'Enter emergency contact relation',
                            ]) !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Emergency Contact End --}}
    </div>

    <div class="row">
        <div class="col-12">
            <input type="submit" value="{{ __('Update') }}" class="btn btn-primary float-end">
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@push('script-page')
    <script type="text/javascript">
        $(document).on('change', '#branch_id', function() {
            var branch_id = $(this).val();
            getDepartment(branch_id);
        });

        function getDepartment(branch_id) {
            var data = {
                "branch_id": branch_id,
                "_token": "{{ csrf_token() }}",
            }

            $.ajax({
                url: '{{ route('employee.getdepartment') }}',
                method: 'POST',
                data: data,
                success: function(data) {
                    $('#department_id').empty();
                    $('#department_id').append(
                        '<option value="" disabled>{{ __('Select any Department') }}</option>');

                    $.each(data, function(key, value) {
                        $('#department_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                    $('#department_id').val('');
                }
            });
        }
    </script>
    <script type="text/javascript">
        function getDesignation(did) {
            $.ajax({
                url: '{{ route('employee.json') }}',
                type: 'POST',
                data: {
                    "department_id": did,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#designation_id').empty();
                    $('#designation_id').append(
                        '<option value="">{{ __('Select any Designation') }}</option>');
                    $.each(data, function(key, value) {
                        var select = '';
                        if (key == '{{ $employee->designation_id }}') {
                            select = 'selected';
                        }

                        $('#designation_id').append('<option value="' + key + '"  ' + select + '>' +
                            value + '</option>');
                    });
                }
            });
        }

        $(document).ready(function() {
            var d_id = $('#department_id').val();
            var designation_id = '{{ $employee->designation_id }}';
            getDesignation(d_id);
        });

        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
        });
    </script>
@endpush
