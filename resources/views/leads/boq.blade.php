{{ Form::open(array('route' => array('leads.boq.store', $leadId), 'method' => 'POST', 'enctype' => 'multipart/form-data')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            {{ Form::label('file', __('Upload File'), ['class' => 'form-label']) }}
            {{ Form::file('file', ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="col-12 form-group">
            {!! Form::label('description', __('Description'), ['class' => 'form-label']) !!}
            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3 , 'placeholder'=>'Enter Description (If any)']) !!}
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>

{{Form::close()}}
