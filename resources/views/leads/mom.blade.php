{{ Form::open(array('route' => array('leads.mom.store', $lead->id), 'method' => 'POST')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12 form-group">
            {{ Form::label('attendee', __('List of Attendee'),['class'=>'form-label']) }}
            {{ Form::select('attendee[]', $users,false, array('class' => 'form-control select2', 'required' => 'required', 'id'=>'choices-multiple3','multiple'=>'')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('topic', __('Topic discussed'),['class'=>'form-label']) }}
            {{ Form::text('topic', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('place_time', __('Place and Time '),['class'=>'form-label']) }}
            {{ Form::text('place_time', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('next_plan', __('Next action plan (If any)'),['class'=>'form-label']) }}
            {{ Form::select('next_plan', $departments,false, array('class' => 'form-control select2', 'id' => 'next_plan' , 'placeholder' => 'Select Department')) }}
        </div>
        <div id="interaction-group">
            <div class="form-group">
                {!! Form::label('interaction', __('Client Interaction 1'), ['class' => 'form-label']) !!}
                {!! Form::textarea('interaction[]', null, ['class' => 'form-control', 'rows' => 3 , 'required' => 'required', 'placeholder'=>'Client Interaction']) !!}
            </div>
        </div>
        <div class="form-group text-center">
            <a class="btn btn-primary btn-sm text-light" onclick="addInteraction()">Add Interaction</a>
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>

{{Form::close()}}

<script>
    var interactionCount = 1;

    function addInteraction() {
        interactionCount++;
        var interactionGroup = document.getElementById('interaction-group');
        var newFormGroup = document.createElement('div');
        newFormGroup.className = 'form-group';
        var label = document.createElement('label');
        label.className = 'form-label';
        label.innerHTML = 'Client Interaction ' + interactionCount;
        var textarea = document.createElement('textarea');
        textarea.className = 'form-control';
        textarea.setAttribute('rows', '3');
        textarea.setAttribute('required', 'required');
        textarea.setAttribute('placeholder', 'Client Interaction');
        textarea.setAttribute('name', 'interaction[]');
        var removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn btn-danger btn-sm mx-2';
        removeButton.innerHTML = '<i class="ti ti-trash text-white"></i>';
        removeButton.onclick = function() {
            removeInteraction(newFormGroup);
        };
        newFormGroup.appendChild(label);
        newFormGroup.appendChild(removeButton);
        newFormGroup.appendChild(textarea);
        interactionGroup.appendChild(newFormGroup);
        updateLabels();
    }

    function removeInteraction(element) {
        element.parentNode.removeChild(element);
        updateLabels();
    }

    function updateLabels() {
        var interactions = document.querySelectorAll('#interaction-group .form-group');
        interactions.forEach(function(interaction, index) {
            var label = interaction.querySelector('label');
            label.innerHTML = 'Client Interaction ' + (index + 1);
        });
    }
</script>

<style>
    .btn-sm{
        padding: 0.15rem 0.3rem !important;
    }
</style>