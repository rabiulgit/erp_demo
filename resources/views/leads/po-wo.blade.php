{{ Form::open(array('route' => array('leads.powo.store', $leadId), 'method' => 'POST')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            {!! Form::label('status', __('Status'), ['class' => 'form-label']) !!}
            {!! Form::select('status', ['' => 'Select Status', 'Received and Acknowledged' => 'Received and Acknowledged', 'No' => 'No'], null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
</div>

{{Form::close()}}
