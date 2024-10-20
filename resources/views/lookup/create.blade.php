{{-- {{ Form::model(array('route' => array('lookupStore'), 'method' => 'POST')) }} --}}

<form method="POST" action="{{route('lookupStore')}}" accept-charset="UTF-8">
    <input name="_token" type="hidden" value="{{csrf_token()}}">
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('lookupname', __('Name'),['class'=>'form-label']) }}
            {{ Form::text('lookupname',null, array('class' => 'form-control ','required'=>'required')) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('lookupvalue', __('Value'),['class'=>'form-label']) }}
            {{ Form::text('lookupvalue',null, array('class' => 'form-control ','required'=>'required')) }}
        </div>

    </div>
</div>
<div class="modal-footer">
    {{-- <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal"> --}}
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}
