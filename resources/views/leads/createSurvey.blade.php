{{ Form::open(array('route' => array('leads.checklist.store', $leadId), 'method' => 'POST')) }}
<div class="modal-body">
    <div class="row">
        <div id="checklist-group">
            <div class="form-group">
                {!! Form::label('checklist', __('Checklist 1'), ['class' => 'form-label']) !!}
                {!! Form::textarea('checklist[]', null, ['class' => 'form-control', 'rows' => 3 , 'required' => 'required', 'placeholder'=>'Checklist']) !!}
            </div>
        </div>
        <div class="form-group text-center">
            <a class="btn btn-primary btn-sm text-light" onclick="addChecklist()">Add Checklist</a>
        </div>
    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>

{{Form::close()}}

<script>
    var checklistCount = 1;

    function addChecklist() {
        checklistCount++;
        var checklistGroup = document.getElementById('checklist-group');
        var newFormGroup = document.createElement('div');
        newFormGroup.className = 'form-group';
        var label = document.createElement('label');
        label.className = 'form-label';
        label.innerHTML = 'Checklist ' + checklistCount;
        var textarea = document.createElement('textarea');
        textarea.className = 'form-control';
        textarea.setAttribute('rows', '3');
        textarea.setAttribute('required', 'required');
        textarea.setAttribute('placeholder', 'Checklist');
        textarea.setAttribute('name', 'checklist[]');
        var removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn btn-danger btn-sm mx-2';
        removeButton.innerHTML = '<i class="ti ti-trash text-white"></i>';
        removeButton.onclick = function() {
            removeChecklist(newFormGroup);
        };
        newFormGroup.appendChild(label);
        newFormGroup.appendChild(removeButton);
        newFormGroup.appendChild(textarea);
        checklistGroup.appendChild(newFormGroup);
        updateLabels();
    }

    function removeChecklist(element) {
        element.parentNode.removeChild(element);
        updateLabels();
    }

    function updateLabels() {
        var checklists = document.querySelectorAll('#checklist-group .form-group');
        checklists.forEach(function(checklist, index) {
            var label = checklist.querySelector('label');
            label.innerHTML = 'Checklist ' + (index + 1);
        });
    }
</script>

<style>
    .btn-sm{
        padding: 0.15rem 0.3rem !important;
    }
</style>