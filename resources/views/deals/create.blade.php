<link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
<script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>

{{ Form::open(array('url' => 'deals')) }}
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
            {{ Form::number('price', 0, array('class' => 'form-control','min'=> 0)) }}
        </div>
        <div class="col-12">
            <h6 class="text-primary">End User Details</h6>
        </div>
        <div class="col-6 form-group">
            {{ Form::label('client_name', __('Name'),['class'=>'form-label']) }}
            {{ Form::text('client_name', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('email', __('Email'),['class'=>'form-label']) }}
            {{ Form::email('email', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('phone', __('Phone'),['class'=>'form-label']) }}
            {{ Form::text('phone', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('company_code', __('Company Code'),['class'=>'form-label']) }}
            {{ Form::text('company_code', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('address', __('Address'),['class'=>'form-label']) }}
            {{ Form::textarea('address', null, array('class' => 'form-control','required'=>'required', 'rows' => 4)) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('informations', __('More Information'),['class'=>'form-label']) }}
            {{ Form::textarea('informations', null, array('class' => 'summernote-simple','required'=>'required', 'rows' => 4)) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{Form::close()}}
 