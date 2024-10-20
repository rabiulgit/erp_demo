@extends('layouts.admin')
@section('page-title')
    {{$lead->name}}
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/plugins/dropzone.min.css')}}">
@endpush
@push('script-page')
    <script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
    <script src="{{asset('assets/js/plugins/dropzone-amd-module.min.js')}}"></script>
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#lead-sidenav',
            offset: 300
        })
        Dropzone.autoDiscover = false;
        Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("#dropzonewidget", {
            maxFiles: 20,
            // maxFilesize: 20,
            parallelUploads: 1,
            filename: false,
            // acceptedFiles: ".jpeg,.jpg,.png,.pdf,.doc,.txt",
            url: "{{route('leads.file.upload',$lead->id)}}",
            success: function (file, response) {
                if (response.is_success) {
                    dropzoneBtn(file, response);
                } else {
                    myDropzone.removeFile(file);
                    show_toastr('error', response.error, 'error');
                }
            },
            error: function (file, response) {
                myDropzone.removeFile(file);
                if (response.error) {
                    show_toastr('error', response.error, 'error');
                } else {
                    show_toastr('error', response, 'error');
                }
            }
        });
        myDropzone.on("sending", function (file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("lead_id", {{$lead->id}});
        });

        function dropzoneBtn(file, response) {
            var download = document.createElement('a');
            download.setAttribute('href', response.download);
            download.setAttribute('class', "badge bg-info mx-1");
            download.setAttribute('data-toggle', "tooltip");
            download.setAttribute('data-original-title', "{{__('Download')}}");
            download.innerHTML = "<i class='ti ti-download'></i>";

            var del = document.createElement('a');
            del.setAttribute('href', response.delete);
            del.setAttribute('class', "badge bg-danger mx-1");
            del.setAttribute('data-toggle', "tooltip");
            del.setAttribute('data-original-title', "{{__('Delete')}}");
            del.innerHTML = "<i class='ti ti-trash'></i>";

            del.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (confirm("Are you sure ?")) {
                    var btn = $(this);
                    $.ajax({
                        url: btn.attr('href'),
                        data: {_token: $('meta[name="csrf-token"]').attr('content')},
                        type: 'DELETE',
                        success: function (response) {
                            if (response.is_success) {
                                btn.closest('.dz-image-preview').remove();
                            } else {
                                show_toastr('error', response.error, 'error');
                            }
                        },
                        error: function (response) {
                            response = response.responseJSON;
                            if (response.is_success) {
                                show_toastr('error', response.error, 'error');
                            } else {
                                show_toastr('error', response, 'error');
                            }
                        }
                    })
                }
            });

            var html = document.createElement('div');
            html.appendChild(download);
            @if(Auth::user()->type != 'client')
            @can('edit lead')
            html.appendChild(del);
            @endcan
            @endif

            file.previewTemplate.appendChild(html);
        }

        @foreach($lead->files as $file)
        @if (file_exists(storage_path('lead_files/'.$file->file_path)))
        // Create the mock file:
        var mockFile = {name: "{{$file->file_name}}", size: {{\File::size(storage_path('lead_files/'.$file->file_path))}}};
        // Call the default addedfile event handler
        myDropzone.emit("addedfile", mockFile);
        // And optionally show the thumbnail of the file:
        myDropzone.emit("thumbnail", mockFile, "{{asset(Storage::url('lead_files/'.$file->file_path))}}");
        myDropzone.emit("complete", mockFile);

        dropzoneBtn(mockFile, {download: "{{route('leads.file.download',[$lead->id,$file->id])}}", delete: "{{route('leads.file.delete',[$lead->id,$file->id])}}"});
        @endif
        @endforeach

        @can('edit lead')
        $('.summernote-simple').on('summernote.blur', function () {

            $.ajax({
                url: "{{route('leads.note.store',$lead->id)}}",
                data: {_token: $('meta[name="csrf-token"]').attr('content'), notes: $(this).val()},
                type: 'POST',
                success: function (response) {
                    if (response.is_success) {
                        // show_toastr('Success', response.success,'success');
                    } else {
                        show_toastr('error', response.error, 'error');
                    }
                },
                error: function (response) {
                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('error', response.error, 'error');
                    } else {
                        show_toastr('error', response, 'error');
                    }
                }
            })
        });
        @else
        $('.summernote-simple').summernote('disable');
        @endcan

    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('leads.index')}}">{{__('Lead')}}</a></li>
    <li class="breadcrumb-item"> {{$lead->name}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        @can('edit lead')
            <a href="#" data-url="{{ URL::to('leads/'.$lead->id.'/po-wo') }}" data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title="{{__('PO/WO Acknowledgement')}}" class="btn btn-sm btn-success">
                <i class="ti ti-crown"></i>
            </a>
        @endcan
        
            {{-- @if(!empty($deal))
                <a href="@can('View Deal') @if($deal->is_active) {{route('deals.show',$deal->id)}} @else # @endif @else # @endcan" data-size="lg" data-bs-toggle="tooltip" title=" {{__('Already Converted To Deal')}}" class="btn btn-sm btn-primary">
                    <i class="ti ti-exchange"></i>
                </a>
            @else
                <a href="#" data-size="lg" data-url="{{ URL::to('leads/'.$lead->id.'/show_convert') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Convert ['.$lead->subject.'] To Deal')}}" class="btn btn-sm btn-primary">
                    <i class="ti ti-exchange"></i>
                </a>
            @endif --}}

        <a href="#" data-url="{{ URL::to('leads/'.$lead->id.'/labels') }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{__('Label')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-bookmark"></i>
        </a>
        <a href="#" data-size="lg" data-url="{{ route('leads.edit',$lead->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-pencil"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="lead-sidenav">

                            @if(Auth::user()->type != 'client')
                                <a href="#general" class="list-group-item list-group-item-action border-0">{{__('General')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if(Auth::user()->type != 'client')
                                <a href="#users_products" class="list-group-item list-group-item-action border-0">{{__('Users').' | '.__('Products')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if(Auth::user()->type != 'client')
                                <a href="#sources_emails" class="list-group-item list-group-item-action border-0">{{__('Sources').' | '.__('Emails')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                                                                                    
                            @if(Auth::user()->type != 'client')
                                <a href="#mom_form" class="list-group-item list-group-item-action border-0">{{__('Meeting of Minutes')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if(Auth::user()->type != 'client')
                                <a href="#cad" class="list-group-item list-group-item-action border-0">{{__('CAD')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if(Auth::user()->type != 'client')
                                <a href="#boq" class="list-group-item list-group-item-action border-0">{{__('BOQ')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if(Auth::user()->type != 'client')
                                <a href="#survey" class="list-group-item list-group-item-action border-0">{{__('Survey')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if(Auth::user()->type != 'client')
                                <a href="#pricing" class="list-group-item list-group-item-action border-0">{{__('Request for Price')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if(Auth::user()->type != 'client')
                                <a href="#discussion_note" class="list-group-item list-group-item-action border-0">{{__('Discussion').' | '.__('Notes')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if(Auth::user()->type != 'client')
                                <a href="#files" class="list-group-item list-group-item-action border-0">{{__('Files')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if(Auth::user()->type != 'client')
                                <a href="#calls" class="list-group-item list-group-item-action border-0">{{__('Calls')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if(Auth::user()->type != 'client')
                                <a href="#activity" class="list-group-item list-group-item-action border-0">{{__('Activity')}}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <?php
                    $products = $lead->products();
                    $sources = $lead->sources();
                    $calls = $lead->calls;
                    $emails = $lead->emails;
                    ?>
                    <div id="general" class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 col-sm-4 mt-4">
                                    <div class="d-flex align-items-start">
                                        <div class="theme-avtar bg-primary">
                                            <i class="ti ti-mail"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{__('Email')}}</p>
                                            <h5 class="mb-0 text-primary">{{!empty($lead->email)?$lead->email:''}}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-4 mt-4">
                                    <div class="d-flex align-items-start">
                                        <div class="theme-avtar bg-warning">
                                            <i class="ti ti-phone"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{__('Phone')}}</p>
                                            <h5 class="mb-0 text-warning">{{!empty($lead->phone)?$lead->phone:''}}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-4 mt-4">
                                    <div class="d-flex align-items-start">
                                        <div class="theme-avtar bg-info">
                                            <i class="ti ti-test-pipe"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{__('Pipeline')}}</p>
                                            <h5 class="mb-0 text-info">{{$lead->pipeline->name}}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-4 mt-4">
                                    <div class="d-flex align-items-start">
                                        <div class="theme-avtar bg-primary">
                                            <i class="ti ti-server"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{__('Stage')}}</p>
                                            <h5 class="mb-0 text-primary">{{$lead->stage->name}}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-4 mt-4">
                                    <div class="d-flex align-items-start">
                                        <div class="theme-avtar bg-warning">
                                            <i class="ti ti-calendar"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{__('Created')}}</p>
                                            <h5 class="mb-0 text-warning">{{\Auth::user()->dateFormat($lead->created_at)}}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-4 mt-4">
                                    <div class="d-flex align-items-start">
                                        <div class="theme-avtar bg-info">
                                            <i class="ti ti-chart-bar"></i>
                                        </div>
                                        <div class="ms-2">
                                            <h3 class="mb-0 text-info">{{$precentage}}%</h3>
                                            <div class="progress mb-0">
                                                <div class="progress-bar bg-info" style="width: {{$precentage}}%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-4 mt-4">
                                    <div class="d-flex align-items-start">
                                        <div class="theme-avtar bg-success">
                                            <i class="ti ti-crown"></i>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted text-sm mb-0">{{__('PO/WO Acknowledgement')}}</p>
                                            <h5 class="mb-0 text-primary">{{$lead->po_wo_status ? $lead->po_wo_status : "No"}}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center justify-content-between">
                                        <div class="col-auto mb-3 mb-sm-0">
                                            <small class="text-muted">{{__('Total CAD')}}</small>
                                            <h3 class="m-0">{{count($lead->leadCads)}}</h3>
                                        </div>
                                        <div class="col-auto">
                                            <div class="theme-avtar bg-info">
                                                <i class="ti ti-layout"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center justify-content-between">
                                        <div class="col-auto mb-3 mb-sm-0">
                                            <small class="text-muted">{{__('Total BOQ')}}</small>
                                            <h3 class="m-0">{{count($lead->leadBoqs)}}</h3>
                                        </div>
                                        <div class="col-auto">
                                            <div class="theme-avtar bg-primary">
                                                <i class="ti ti-server"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center justify-content-between">
                                        <div class="col-auto mb-3 mb-sm-0">
                                            <small class="text-muted">{{__('Price Revision')}}</small>
                                            <h3 class="m-0">{{ $lead->leadPrices()->where('price', '!=', null)->count() }}</h3>
                                        </div>
                                        <div class="col-auto">
                                            <div class="theme-avtar bg-warning">
                                                <i class="ti ti-test-pipe"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center justify-content-between">
                                        <div class="col-auto mb-3 mb-sm-0">
                                            <small class="text-muted">{{__('Product')}}</small>
                                            <h3 class="m-0">{{count($products)}}</h3>
                                        </div>
                                        <div class="col-auto">
                                            <div class="theme-avtar bg-info">
                                                <i class="ti ti-shopping-cart"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center justify-content-between">
                                        <div class="col-auto mb-3 mb-sm-0">
                                            <small class="text-muted">{{__('Source')}}</small>
                                            <h3 class="m-0">{{count($sources)}}</h3>
                                        </div>
                                        <div class="col-auto">
                                            <div class="theme-avtar bg-primary">
                                                <i class="ti ti-social"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center justify-content-between">
                                        <div class="col-auto mb-3 mb-sm-0">
                                            <small class="text-muted">{{__('Files')}}</small>
                                            <h3 class="m-0">{{count($lead->files)}}</h3>
                                        </div>
                                        <div class="col-auto">
                                            <div class="theme-avtar bg-warning">
                                                <i class="ti ti-file"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="users_products">
                        <div class="row">
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('Users')}}</h5>
                                            <div class="float-end">
                                                <a  data-size="md" data-url="{{ route('leads.users.edit',$lead->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add User')}}" class="btn btn-sm btn-primary ">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                <tr>
                                                    <th>{{__('Name')}}</th>
                                                    <th>{{__('Action')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($lead->users as $user)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div>
                                                                    <img @if($user->avatar) src="{{asset('/storage/uploads/avatar/'.$user->avatar)}}" @else src="{{asset('/storage/uploads/avatar/avatar.png')}}" @endif class="wid-30 rounded-circle me-3" alt="avatar image">
                                                                </div>
                                                                <p class="mb-0">{{$user->name}}</p>
                                                            </div>
                                                        </td>
                                                        @can('edit lead')
                                                            <td>
                                                                <div class="action-btn bg-danger ms-2">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['leads.users.destroy', $lead->id,$user->id],'id'=>'delete-form-'.$lead->id]) !!}
                                                                    <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                                    {!! Form::close() !!}
                                                                </div>
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('Products')}}</h5>
                                            <div class="float-end">
                                                <a data-size="md" data-url="{{ route('leads.products.edit',$lead->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Product')}}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                <tr>
                                                    <th>{{__('Name')}}</th>
                                                    <th>{{__('Price')}}</th>
                                                    <th>{{__('Action')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($lead->products() as $product)
                                                    <tr>
                                                        <td>
                                                            {{$product->name}}
                                                        </td>
                                                        <td>
                                                            {{\Auth::user()->priceFormat($product->sale_price)}}
                                                        </td>
                                                        @can('edit lead')
                                                            <td>
                                                                <div class="action-btn bg-danger ms-2">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['leads.products.destroy', $lead->id,$product->id]]) !!}
                                                                    <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                                    {!! Form::close() !!}
                                                                </div>
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="sources_emails">
                        <div class="row">
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('Sources')}}</h5>
                                            <div class="float-end">
                                                <a data-size="md" data-url="{{ route('leads.sources.edit',$lead->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Source')}}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                <tr>
                                                    <th>{{__('Name')}}</th>
                                                    <th>{{__('Action')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($sources as $source)
                                                    <tr>
                                                        <td>{{$source->name}} </td>
                                                        @can('edit lead')
                                                            <td>
                                                                <div class="action-btn bg-danger ms-2">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['leads.sources.destroy', $lead->id,$source->id],'id'=>'delete-form-'.$lead->id]) !!}
                                                                        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>
                                                                    {!! Form::close() !!}
                                                                </div>
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('Emails')}}</h5>
                                                <div class="float-end">
                                                    <a data-size="md" data-url="{{ route('leads.emails.create',$lead->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create Email')}}" class="btn btn-sm btn-primary">
                                                        <i class="ti ti-plus text-white"></i>
                                                    </a>
                                                </div>
                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush mt-2">
                                            @if(!$emails->isEmpty())
                                                @foreach($emails as $email)
                                                    <li class="list-group-item px-0">
                                                        <div class="d-block d-sm-flex align-items-start">
                                                            <img src="{{asset('/storage/uploads/avatar/avatar.png')}}"
                                                                 class="img-fluid wid-40 me-3 mb-2 mb-sm-0" alt="image">
                                                            <div class="w-100">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div class="mb-3 mb-sm-0">
                                                                        <h6 class="mb-0">{{$email->subject}}</h6>
                                                                        <span class="text-muted text-sm">{{$email->to}}</span>
                                                                    </div>
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        {{$email->created_at->diffForHumans()}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @else
                                                <li class="text-center">
                                                    {{__(' No Emails Available.!')}}
                                                </li>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="mom_form">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('Meeting of Minutes')}}</h5>
                                            <div class="float-end">
                                                <a  data-size="lg" data-url="{{ route('leads.mom.create',$lead->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Meeting of Minutes')}}" class="btn btn-sm btn-primary ">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                <tr>
                                                    <th>{{__('#')}}</th>
                                                    <th>{{__('Topic')}}</th>
                                                    <th>{{__('Date')}}</th>
                                                    <th>{{__('Action')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $count=1;
                                                    @endphp
                                                @foreach($lead->leadMoms->reverse() as $mom)
                                                    <tr>
                                                        <td>{{$count++}}</td>
                                                        <td>{{$mom->topic}}</td>
                                                        <td>{{ \Carbon\Carbon::parse($mom->created_at)->format('d F, Y') }}</td>
                                                        <td>
                                                            <div class="action-btn bg-primary ms-2">
                                                                <a data-size="lg" data-url="{{ route('leads.mom.show',$mom->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Meeting of Minutes Detail')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                                    <i class="ti ti-eye text-white"></i>
                                                                </a>
                                                            </div>
                                                            @can('edit lead')
                                                                <div class="action-btn bg-danger ms-2">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['leads.mom.destroy',$mom->id],'id'=>'delete-form-'.$mom->id]) !!}
                                                                    <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                                    {!! Form::close() !!}
                                                                </div>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="cad">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('CAD')}}</h5>
                                            <div class="float-end">
                                                <a data-size="md" data-url="{{ route('leads.cad.create',$lead->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add CAD Design')}}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{__('Uploaded By')}}</th>
                                                    <th>{{__('Date')}}</th>
                                                    <th>{{__('File')}}</th>
                                                    <th>{{__('Description')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($lead->leadCads->reverse() as $cad)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $cad->user->name}}</td>
                                                        <td>{{ \Carbon\Carbon::parse($mom->created_at)->format('d F, Y') }}</td>
                                                        <td>
                                                            <a href="{{ url('storage/cad/'.$cad->file) }}" class="btn btn-primary btn-sm" download>
                                                                <i class="ti ti-download text-white"></i>
                                                            </a>
                                                        </td>
                                                        <td>{{ $cad->description ? $cad->description : '-' }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="boq">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('BOQ')}}</h5>
                                            <div class="float-end">
                                                <a data-size="md" data-url="{{ route('leads.boq.create',$lead->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add BOQ')}}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{__('Uploaded By')}}</th>
                                                    <th>{{__('Date')}}</th>
                                                    <th>{{__('File')}}</th>
                                                    <th>{{__('Description')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($lead->leadBoqs->reverse() as $boq)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $boq->user->name}}</td>
                                                        <td>{{ \Carbon\Carbon::parse($boq->created_at)->format('d F, Y') }}</td>
                                                        <td>
                                                            <a href="{{ url('storage/boq/'.$boq->file) }}" class="btn btn-primary btn-sm" download>
                                                                <i class="ti ti-download text-white"></i>
                                                            </a>
                                                        </td>
                                                        <td>{{ $boq->description ? $boq->description : '-' }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="survey">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('Site Survey')}}</h5>
                                            <div class="float-end">
                                                <a  data-size="lg" data-url="{{ route('leads.survey.checklist',$lead->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Checklist')}}" class="btn btn-sm btn-primary ">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                <tr>
                                                    <th>{{__('#')}}</th>
                                                    <th>{{__('Created By')}}</th>
                                                    <th>{{__('Checklist')}}</th>
                                                    <th>{{__('Date')}}</th>
                                                    <th>{{__('Total Report')}}</th>
                                                    <th>{{__('Action')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($lead->leadSurveys->reverse() as $survey)
                                                    <tr>
                                                        <td>{{$loop->iteration}}</td>
                                                        <td>{{$survey->user->name}}</td>
                                                        <td>{{$survey->checklist}}</td>
                                                        <td>{{ \Carbon\Carbon::parse($survey->created_at)->format('d F, Y') }}</td>
                                                        <td>{{count(json_decode($survey->report))}}</td>
                                                        <td>
                                                            <div class="action-btn bg-primary ms-2">
                                                                <a data-size="md" data-url="{{ route('leads.surveyReport.show',$survey->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{$survey->checklist}} Reports" class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                                    <i class="ti ti-eye text-white"></i>
                                                                </a>
                                                            </div>
                                                            @can('edit lead')
                                                                <div class="action-btn bg-success ms-2">
                                                                    <a data-size="md" data-url="{{ route('leads.checklist.add',$survey->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="Add Report for {{$survey->checklist}}" class="mx-3 btn btn-sm d-inline-flex align-items-center">
                                                                        <i class="ti ti-plus text-white"></i>
                                                                    </a>
                                                                </div>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="pricing">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('Request for Price')}}</h5>
                                            <div class="float-end">
                                                <a data-size="md" data-url="{{ route('leads.price.create',$lead->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Request for Price')}}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{__('Requested By')}}</th>
                                                    <th>{{__('Date')}}</th>
                                                    <th>{{__('CAD')}}</th>
                                                    <th>{{__('BOQ')}}</th>
                                                    <th>{{__('Price')}}</th>
                                                    <th>{{__('Description')}}</th>
                                                    @if ($isProcurement == 1)
                                                        <th>{{__('Action')}}</th>
                                                    @endif
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($lead->leadPrices->reverse() as $price)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $price->user->name}}</td>
                                                        <td>{{ \Carbon\Carbon::parse($price->created_at)->format('d F, Y') }}</td>
                                                        <td>
                                                            @if ($price->cad)
                                                                <a href="{{ url('storage/cad/'.$price->cad) }}" class="btn btn-primary btn-sm" download>
                                                                    <i class="ti ti-download text-white"></i>
                                                                </a>
                                                            @else
                                                                -
                                                            @endif
                                                            
                                                        </td>
                                                        <td>
                                                            @if ($price->boq)
                                                                <a href="{{ url('storage/boq/'.$price->boq) }}" class="btn btn-primary btn-sm" download>
                                                                    <i class="ti ti-download text-white"></i>
                                                                </a>
                                                            @else
                                                                -
                                                            @endif
                                                            
                                                        </td>
                                                        <td>
                                                            @if($price->price)
                                                                <div class="status_badge badge bg-success p-2 px-3 rounded">৳{{ $price->price }}</div>
                                                            @else
                                                                <div class="status_badge badge bg-warning p-2 px-3 rounded">Pending</div>
                                                            @endif
                                                        </td>
                                                        <td>{{ $price->description ? $price->description : '-' }}</td>
                                                        @if ($isProcurement == 1)
                                                            @if (!$price->price)
                                                            <td>
                                                                <a data-size="md" data-url="{{ route('leads.price.procurement',$price->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Price')}}" class="btn btn-sm btn-primary text-light">
                                                                    Add Price
                                                                </a>
                                                            </td>
                                                            @endif
                                                        @endif
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="discussion_note">
                        <div class="row">
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('Discussion')}}</h5>
                                            <div class="float-end">
                                                <a data-size="lg" data-url="{{ route('leads.discussions.create',$lead->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Message')}}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush mt-2">
                                            @if(!$lead->discussions->isEmpty())
                                                @foreach($lead->discussions as $discussion)
                                                    <li class="list-group-item px-0">
                                                        <div class="d-block d-sm-flex align-items-start">
                                                            <img src="@if($discussion->user->avatar) {{asset('/storage/uploads/avatar/'.$discussion->user->avatar)}} @else {{asset('/storage/uploads/avatar/avatar.png')}} @endif"
                                                                 class="img-fluid wid-40 me-3 mb-2 mb-sm-0" alt="image">
                                                            <div class="w-100">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div class="mb-3 mb-sm-0">
                                                                        <h6 class="mb-0"> {{$discussion->comment}}</h6>
                                                                        <span class="text-muted text-sm">{{$discussion->user->name}}</span>
                                                                    </div>
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        {{$discussion->created_at->diffForHumans()}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @else
                                                <li class="text-center">
                                                    {{__(' No Data Available.!')}}
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5>{{__('Notes')}}</h5>
                                            @php
                                                $settings = \App\Models\Utility::settings();
                                            @endphp
                                            @if($settings['ai_chatgpt_enable'] == 'on')
                                                <div class="float-end">
                                                    <a href="#" data-size="md" class="btn btn-primary btn-icon btn-sm" data-ajax-popup-over="true" id="grammarCheck" data-url="{{ route('grammar',['grammar']) }}"
                                                       data-bs-placement="top" data-title="{{ __('Grammar check with AI') }}">
                                                        <i class="ti ti-rotate"></i> <span>{{__('Grammar check with AI')}}</span>
                                                    </a>
                                                    <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['lead']) }}"
                                                       data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
                                                        <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <textarea class="summernote-simple" name="note">{!! $lead->notes !!}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="files" class="card">
                        <div class="card-header ">
                            <h5>{{__('Files')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12 dropzone top-5-scroll browse-file" id="dropzonewidget"></div>
                        </div>
                    </div>
                    <div id="calls" class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5>{{__('Calls')}}</h5>

                                <div class="float-end">
                                    <a data-size="lg" data-url="{{ route('leads.calls.create',$lead->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Add Call')}}" class="btn btn-sm btn-primary">
                                        <i class="ti ti-plus text-white"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                    <tr>
                                        <th width="">{{__('Subject')}}</th>
                                        <th>{{__('Call Type')}}</th>
                                        <th>{{__('Duration')}}</th>
                                        <th>{{__('User')}}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($calls as $call)
                                        <tr>
                                            <td>{{ $call->subject }}</td>
                                            <td>{{ ucfirst($call->call_type) }}</td>
                                            <td>{{ $call->duration }}</td>
                                            <td>{{ isset($call->getLeadCallUser) ? $call->getLeadCallUser->name : '-' }}</td>
                                            <td>
                                                    <div class="action-btn bg-info ms-2">
                                                        <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-url="{{ URL::to('leads/'.$lead->id.'/call/'.$call->id.'/edit') }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Role Edit')}}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['leads.calls.destroy', $lead->id,$call->id],'id'=>'delete-form-'.$lead->id]) !!}
                                                        <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>

                                                        {!! Form::close() !!}
                                                    </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="activity" class="card">
                        <div class="card-header">
                            <h5>{{__('Activity')}}</h5>
                        </div>
                        <div class="card-body ">

                            <div class="row leads-scroll" >
                                <ul class="event-cards list-group list-group-flush mt-3 w-100">
                                    @if(!$lead->activities->isEmpty())
                                        @foreach($lead->activities as $activity)
                                            <li class="list-group-item card mb-3">
                                                <div class="row align-items-center justify-content-between">
                                                    <div class="col-auto mb-3 mb-sm-0">
                                                        <div class="d-flex align-items-center">
                                                            <div class="theme-avtar bg-primary">
                                                                <i class="ti {{ $activity->logIcon() }}"></i>
                                                            </div>
                                                            <div class="ms-3">
                                                                <span class="text-dark text-sm">{{ __($activity->log_type) }}</span>
                                                                <h6 class="m-0">{!! $activity->getLeadRemark() !!}</h6>
                                                                <small class="text-muted">{{$activity->created_at->diffForHumans()}}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">

                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @else
                                        No activity found yet.
                                    @endif
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
