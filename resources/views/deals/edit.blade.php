
{{ Form::model($deal, array('route' => array('deals.update', $deal->id), 'method' => 'PUT')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if($settings['ai_chatgpt_enable'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['deal']) }}"
               data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
            </a>
        </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="col-6 form-group">
            {{ Form::label('name', __('Project Name'),['class'=>'form-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('price', __('Price'),['class'=>'form-label']) }}
            {{ Form::number('price', null, array('class' => 'form-control')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('pipeline_id', __('Pipeline'),['class'=>'form-label']) }}
            {{ Form::select('pipeline_id', $pipelines,null, array('class' => 'form-control ','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('stage_id', __('Stage'),['class'=>'form-label']) }}
            {{ Form::select('stage_id', [''=>__('Select Stage')],null, array('class' => 'form-control ','required'=>'required')) }}
        </div>
        <div class="col-12">
            <h6 class="text-primary">End User Details</h6>
        </div>
        <div class="col-6 form-group">
            {{ Form::label('client_name', __('Name'),['class'=>'form-label']) }}
            {{ Form::text('client_name', optional($dealDetails)->client_name ?? null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('email', __('Email'),['class'=>'form-label']) }}
            {{ Form::email('email', optional($dealDetails)->email ?? null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('phone', __('Phone'),['class'=>'form-label']) }}
            {{ Form::text('phone', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('address', __('Address'),['class'=>'form-label']) }}
            {{ Form::textarea('address', optional($dealDetails)->address ?? null, array('class' => 'form-control','required'=>'required', 'rows' => 4)) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('informations', __('More Information'),['class'=>'form-label']) }}
            {{ Form::textarea('informations', optional($dealDetails)->informations ?? null, array('class' => 'summernote-simple','required'=>'required', 'rows' => 4)) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
</div>
{{Form::close()}}

<script>
    var stage_id = '{{$deal->stage_id}}';

    $(document).ready(function () {
        $("#commonModal select[name=pipeline_id]").trigger('change');
    });

    $(document).on("change", "#commonModal select[name=pipeline_id]", function () {
        $.ajax({
            url: '{{route('stages.json')}}',
            data: {pipeline_id: $(this).val(), _token: $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            success: function (data) {
                $('#stage_id').empty();
                $("#stage_id").append('<option value="" selected="selected">{{__('Select Stage')}}</option>');
                $.each(data, function (key, data) {
                    var select = '';
                    if (key == '{{ $deal->stage_id }}') {
                        select = 'selected';
                    }
                    $("#stage_id").append('<option value="' + key + '" ' + select + '>' + data + '</option>');
                });
                $("#stage_id").val(stage_id);
                $('#stage_id').select2({
                    placeholder: "{{__('Select Stage')}}"
                });
            }
        })
    });
</script>
