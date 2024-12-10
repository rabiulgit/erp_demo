{{ Form::open(['url' => 'employee-causes', 'method' => 'post']) }}
<div class="modal-body">
    {{-- end for ai module --}}
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('typee', __('Employee Cause Type'), ['class' => 'form-label']) }}
                <select name="type" id="type" class="form-control select" required>
                    <option value="">{{ __('Select Cause Type') }}</option>
                    <option value="late">{{ __('late Cause') }}</option>
                    <option value="early_leave">{{ __('Early Leave Cause') }}</option>
                    <option value="other">{{ __('Other Cause') }}</option>

                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                {{ Form::date('date', null, ['class' => 'form-control ']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('time', __('Time'), ['class' => 'form-label']) }}
                {{ Form::time('time', null, ['class' => 'form-control timepicker']) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('note', __('Note'), ['class' => 'form-label']) }}
                {{ Form::textarea('note', null, ['class' => 'form-control', 'placeholder' => __('Enter cause')]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
