{{ Form::open(array('route' => array('leads.procurementPrice.store', $reqId), 'method' => 'POST', 'enctype' => 'multipart/form-data')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            {!! Form::label('price', __('Price'), ['class' => 'form-label']) !!}
            {!! Form::number('price', null, ['class' => 'form-control', 'required' => 'required', 'placeholder'=>'Enter Price', 'step' => 'any']) !!}
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>

{{Form::close()}}
