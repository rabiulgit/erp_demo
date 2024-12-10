{{Form::model($EmployeeCause,array('route' => array('employee-causes.update', $EmployeeCause->id), 'method' => 'PUT')) }}
<div class="modal-body">
    {{-- end for ai module--}}
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('type', __('Employee Cause Type'), ['class' => 'form-label']) }}
                <select name="type" id="type" class="form-control select" required>
                    <option value="">{{ __('Select Cause Type') }}</option>
                    <option value="late" {{ $EmployeeCause->type == 'late' ? 'selected' : '' }}>
                        {{ __('Late Cause') }}
                    </option>
                    <option value="early_leave" {{ $EmployeeCause->type == 'early_leave' ? 'selected' : '' }}>
                        {{ __('Early Leave Cause') }}
                    </option>
                    <option value="other" {{ $EmployeeCause->type == 'other' ? 'selected' : '' }}>
                        {{ __('Other Cause') }}
                    </option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('date',__('Date'),['class'=>'form-label'])}}
                {{Form::date('date',null,array('class'=>'form-control '))}}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('time',__('Time'),['class'=>'form-label'])}}
                {{Form::time('time',null,array('class'=>'form-control timepicker'))}}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('note',__('Note'),['class'=>'form-label'])}}
                {{Form::textarea('note',null,array('class'=>'form-control','placeholder'=>__('Enter Late Note')))}}
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
</div>
{{Form::close()}}
