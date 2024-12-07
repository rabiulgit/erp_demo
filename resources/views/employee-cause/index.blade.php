@extends('layouts.admin')

@section('page-title')
    {{ __('Manage employeeCauses') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('employeeCauses') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">

        <a href="#" data-url="{{ route('employee-causes.create') }}" data-size="lg" data-ajax-popup="true"
            data-title="{{ __('Create New employeeCauses') }}" data-bs-toggle="tooltip" title="{{ __('Create employeeCauses') }}"
            class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Cause Type') }}</th>
                                    <th>{{ __('employee Date') }}</th>
                                    <th>{{ __('In Time') }}</th>
                                    <th>{{ __('employee Note') }}</th>
                                    <th width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="font-style">
                                @foreach ($EmployeeCauses ?? [] as $row)
                                    <tr>
                                        <td>{{ $row?->employee?->name }}</td>
                                        <td>{{ $row?->type ?? "N/A" }}</td>
                                        <td>{{ \Auth::user()?->dateFormat($row->date) }}</td>
                                        <td>{{ \Auth::user()?->timeFormat($row->time) }}</td>
                                        <td>{{ $row->note ?? 'N/A' }}</td>
                                        <td>
                                            <div class="action-btn bg-primary ms-2">
                                                <a href="#"
                                                    data-url="{{ URL::to('employee-causes/' . $row->id . '/edit') }}"
                                                    data-size="lg" data-ajax-popup="true"
                                                    data-title="{{ __('Edit employeeCauses') }}"
                                                    class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip"
                                                    title="{{ __('Edit') }}"
                                                    data-original-title="{{ __('Edit') }}"><i
                                                        class="ti ti-pencil text-white"></i></a>
                                            </div>
                                            <div class="action-btn bg-danger ms-2">
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['employee-causes.destroy', $row->id],
                                                    'id' => 'delete-form-' . $row->id,
                                                ]) !!}
                                                <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para"
                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                    data-original-title="{{ __('Delete') }}"
                                                    data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                    data-confirm-yes="document.getElementById('delete-form-{{ $row->id }}').submit();"><i
                                                        class="ti ti-trash text-white"></i></a>
                                                {!! Form::close() !!}
                                            </div>
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
@endsection

@push('script-page')
    <script>
        $(document).ready(function() {
            var b_id = $('#branch_id').val();
            getDepartment(b_id);
        });
        $(document).on('change', 'select[name=branch_id]', function() {

            var branch_id = $(this).val();
            getDepartment(branch_id);
        });

        function getDepartment(bid) {

            $.ajax({
                url: '',
                type: 'POST',
                data: {
                    "branch_id": bid,
                    "_token": "{{ csrf_token() }}",
                },

                success: function(data) {
                    console.log(data);
                    $('#department_id').empty();

                    $("#department_div").html('');
                    $('#department_div').append(
                        '<select class="form-control" id="department_id" name="department_id[]"  multiple></select>'
                    );

                    $('#department_id').append('<option value="">{{ __('Select Department') }}</option>');

                    $('#department_id').append('<option value="0"> {{ __('All Department') }} </option>');
                    $.each(data, function(key, value) {
                        console.log(key, value);
                        $('#department_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                    var multipleCancelButton = new Choices('#department_id', {
                        removeItemButton: true,
                    });


                }

            });
        }

        $(document).on('change', '#department_id', function() {
            var department_id = $(this).val();
            getEmployee(department_id);
        });

        function getEmployee(did) {

            $.ajax({
                url: '',
                type: 'POST',
                data: {
                    "department_id": did,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    console.log(data);
                    $('#employee_id').empty();

                    $("#employee_div").html('');
                    $('#employee_div').append(
                        '<select class="form-control" id="employee_id" name="employee_id[]"  multiple></select>'
                    );


                    $('#employee_id').append('<option value="">{{ __('Select Employee') }}</option>');
                    $('#employee_id').append('<option value="0"> {{ __('All Employee') }} </option>');

                    $.each(data, function(key, value) {
                        $('#employee_id').append('<option value="' + key + '">' + value + '</option>');
                    });

                    var multipleCancelButton = new Choices('#employee_id', {
                        removeItemButton: true,
                    });
                }
            });
        }
    </script>
@endpush
