@extends('layouts.admin')
@section('page-title')
    {{__('Last Login')}}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                       <table class="table datatable">                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Last Login')}}</th>
                                <th>{{__('Role')}}</th>
                            </tr>
                            </thead>
                            <tbody class="font-style">
                            @foreach ($users as $user)
                                <tr>
                                    @if($user->type=='Employee')
                                        <td>{{ $user->id }}</td>
                                    @else
                                        <td>--</td>
                                    @endif
                                    <td>{{ $user->name }}</td>
                                    <td>{{$user->last_login}}</td>
                                    <td>{{$user->type}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


