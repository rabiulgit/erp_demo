@extends('layouts.admin')

@section('page-title')
    {{ __('Create Employee') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ url('employee') }}">{{ __('Employee') }}</a></li>
    <li class="breadcrumb-item">{{ __('Create Employee') }}</li>
@endsection


@section('content')
    <div class="row">
        <div class="">
            <div class="">
                <div class="row">

                </div>
                {{ Form::open(['route' => ['employee.store'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                <div class="row">
                    <div class="col-md-6">
                        <div class="card em-card">
                            <div class="card-header">
                                <h5>{{ __('Personal Detail') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {!! Form::label('name', __('Name'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                        {!! Form::text('name', old('name'), [
                                            'class' => 'form-control',
                                            'required' => 'required',
                                            'placeholder' => 'Enter employee name',
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('phone', __('Phone'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                        {!! Form::text('phone', old('phone'), ['class' => 'form-control', 'placeholder' => 'Enter employee phone']) !!}
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('dob', __('Date of Birth'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                            {{ Form::date('dob', null, ['class' => 'form-control ', 'required' => 'required', 'autocomplete' => 'off', 'placeholder' => 'Select Date of Birth']) }}
                                        </div>
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('gender', __('Gender'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                            <div class="d-flex radio-check">
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="g_male" value="Male" name="gender"
                                                        class="form-check-input">
                                                    <label class="form-check-label "
                                                        for="g_male">{{ __('Male') }}</label>
                                                </div>
                                                <div class="custom-control custom-radio ms-1 custom-control-inline">
                                                    <input type="radio" id="g_female" value="Female" name="gender"
                                                        class="form-check-input">
                                                    <label class="form-check-label "
                                                        for="g_female">{{ __('Female') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('email', __('Email'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                        {!! Form::email('email', old('email'), [
                                            'class' => 'form-control',
                                            'required' => 'required',
                                            'placeholder' => 'Enter employee email',
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('password', __('Password'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                        {!! Form::password('password', [
                                            'class' => 'form-control',
                                            'required' => 'required',
                                            'placeholder' => 'Enter employee new password',
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('address', __('Address'), ['class' => 'form-label']) !!}
                                    {!! Form::textarea('address', old('address'), [
                                        'class' => 'form-control',
                                        'rows' => 2,
                                        'placeholder' => 'Enter employee address',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

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

                    <div class="col-md-6">
                        <div class="card em-card">
                            <div class="card-header">
                                <h5>{{ __('Company Detail') }}</h5>
                            </div>
                            <div class="card-body employee-detail-create-body">
                                <div class="row">
                                    @csrf
                                    <div class="form-group ">
                                        {!! Form::label('employee_id', __('Employee ID'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                        {!! Form::text('employee_id', old('employee_id'), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter Employee ID',
                                        ]) !!}
                                    </div>

                                    <div class="form-group col-md-6">
                                        {{ Form::label('branch_id', __('Select Branch'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            {{ Form::select('branch_id', $branches, null, ['class' => 'form-control select2', 'placeholder' => 'Select Branch']) }}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        {{ Form::label('department_id', __('Select Department'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            {{ Form::select('department_id', $departments, null, ['class' => 'form-control select2', 'id' => 'department_id', 'placeholder' => 'Select Department']) }}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        {{ Form::label('designation_id', __('Select Designation'), ['class' => 'form-label']) }}

                                        <div class="form-icon-user">
                                            {{--  <div class="designation_div">
                                            <select class="form-control  designation_id" name="designation_id"
                                                id="choices-multiple" placeholder="Select Designation">
                                            </select>
                                        </div>  --}}
                                            {{ Form::select('designation_id', $designations, null, ['class' => 'form-control select2', 'id' => 'designation_id', 'placeholder' => 'Select Designation']) }}

                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('company_doj', __('Company Date Of Joining'), ['class' => '  form-label']) !!}
                                        {{ Form::date('company_doj', null, ['class' => 'form-control ', 'autocomplete' => 'off', 'placeholder' => 'Select company date of joining']) }}
                                    </div>

                                    <div class="form-group col-md-6">
                                        {!! Form::label('team_id', __('Assign to team'), ['class' => '  form-label']) !!}
                                        {{ Form::select('team_id', $teams, null, ['class' => 'form-control select', 'id' => 'team_id', 'placeholder' => 'Assign a Team']) }}
                                    </div>

                                    <div class="form-group col-md-6 d-flex align-items-end">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" id="team_lead" name="team_lead"
                                                value="1" />
                                            <label class="form-check-label f-w-600 pl-1"
                                                for="team_lead">{{ __('Team Leader') }}</label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Equipment Allocation Start --}}
                    <div class="col-md-6">
                        <div class="card em-card">
                            <div class="card-header">
                                <h5>{{ __('Equipment Allocation') }}</h5>
                            </div>
                            <div class="card-body employee-detail-create-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {!! Form::label('mobile_name', __('Mobile Name/Model'), ['class' => 'form-label']) !!}
                                        {!! Form::text('mobile_name', old('mobile_name'), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter Mobile Name/Model',
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
                    {{-- Equipment Allocation End--}}

                    <div class="col-md-6">
                        <div class="card em-card">
                            <div class="card-header">
                                <h5>{{ __('Bank Account Detail') }}</h5>
                            </div>
                            <div class="card-body employee-detail-create-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {!! Form::label('account_holder_name', __('Account Holder Name'), ['class' => 'form-label']) !!}
                                        {!! Form::text('account_holder_name', old('account_holder_name'), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter account holder name',
                                        ]) !!}

                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('account_number', __('Account Number'), ['class' => 'form-label']) !!}
                                        {!! Form::number('account_number', old('account_number'), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter account number',
                                        ]) !!}

                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('bank_name', __('Bank Name'), ['class' => 'form-label']) !!}
                                        {!! Form::text('bank_name', old('bank_name'), ['class' => 'form-control', 'placeholder' => 'Enter bank name']) !!}

                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('bank_identifier_code', __('Bank Identifier Code'), ['class' => 'form-label']) !!}
                                        {!! Form::text('bank_identifier_code', old('bank_identifier_code'), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter bank identifier code',
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('branch_location', __('Branch Location'), ['class' => 'form-label']) !!}
                                        {!! Form::text('branch_location', old('branch_location'), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter branch location',
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('tax_payer_id', __('Tax Payer Id'), ['class' => 'form-label']) !!}
                                        {!! Form::text('tax_payer_id', old('tax_payer_id'), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter tax payer id',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

                    <div class="col-md-6">
                        <div class="card em-card">
                            <div class="card-header">
                                <h5>{{ __('Document') }}</h6>
                            </div>
                            <div class="card-body employee-detail-create-body">
                                @foreach ($documents as $key => $document)
                                    <div class="row">
                                        <div class="form-group col-12 d-flex">
                                            <div class="float-left col-4">
                                                <label for="document"
                                                    class="float-left pt-1 form-label">{{ $document->name }} @if ($document->is_required == 1)
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <div class="float-right col-8">
                                                <input type="hidden" name="emp_doc_id[{{ $document->id }}]"
                                                    id="" value="{{ $document->id }}">
                                                <div class="choose-files">
                                                    <label for="document[{{ $document->id }}]">
                                                        <div class=" bg-primary document "> <i
                                                                class="ti ti-upload "></i>{{ __('Choose file here') }}
                                                        </div>
                                                        <input type="file"
                                                            class="form-control file  d-none @error('document') is-invalid @enderror"
                                                            @if ($document->is_required == 1) required @endif
                                                            name="document[{{ $document->id }}]"
                                                            id="document[{{ $document->id }}]"
                                                            data-filename="{{ $document->id . '_filename' }}"
                                                            onchange="document.getElementById('{{ 'blah' . $key }}').src = window.URL.createObjectURL(this.files[0])">
                                                    </label>
                                                    <img id="{{ 'blah' . $key }}" src="" width="50%" />

                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>







                </div>

            </div>

            <div class="float-end">
                <button type="submit" class="btn  btn-primary">{{ 'Create' }}</button>
            </div>
            </form>
        </div>
    </div>
@endsection

@push('script-page')
    <script>
        $('input[type="file"]').change(function(e) {
            var file = e.target.files[0].name;
            var file_name = $(this).attr('data-filename');
            $('.' + file_name).append(file);
        });
    </script>
    <script>
        $(document).ready(function() {
            var d_id = $('.department_id').val();
            getDesignation(d_id);
        });

        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
        });

        function getDesignation(did) {

            $.ajax({
                url: '{{ route('employee.json') }}',
                type: 'POST',
                data: {
                    "department_id": did,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {

                    $('.designation_id').empty();
                    var emp_selct = ` <select class="form-control  designation_id" name="designation_id" id="choices-multiple"
                                            placeholder="Select Designation" >
                                            </select>`;
                    $('.designation_div').html(emp_selct);

                    $('.designation_id').append('<option value="0"> {{ __('All') }} </option>');
                    $.each(data, function(key, value) {
                        $('.designation_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                    new Choices('#choices-multiple', {
                        removeItemButton: true,
                    });


                }
            });
        }
    </script>
@endpush
