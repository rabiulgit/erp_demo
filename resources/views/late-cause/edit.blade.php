{{Form::model($lateCause,array('route' => array('employee-late-causes.update', $lateCause->id), 'method' => 'PUT')) }}
<div class="modal-body">
    {{-- end for ai module--}}
    <div class="row">

        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('date',__('Late Date'),['class'=>'form-label'])}}
                {{Form::date('date',null,array('class'=>'form-control '))}}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('time',__('Late Time'),['class'=>'form-label'])}}
                {{Form::time('time',null,array('class'=>'form-control timepicker'))}}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('note',__('Late Note'),['class'=>'form-label'])}}
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
