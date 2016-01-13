@extends('layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li class="active"><span>Users</span></li>
            </ol>

            <h1>Users <small>List All Users</small></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">Users</h2>

                    <div class="filter-block pull-right">
                        <a href="{{ route('users.create') }}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus-circle fa-lg"></i> Add user
                        </a>
                    </div>
                </header>

                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th><span>User ID</span></th>
                                <th><span>Name</span></th>
                                <th><span>Register date</span></th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td>
                                        #{{ $user->id }}
                                    </td>
                                    <td>
                                        {{ $user->name }}
                                        @if ($user->id == auth()->id())
                                            <span style="color: #03a9f4; font-size: 16px;">- it's you!</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $user->created_at->format('d/m/Y') }}
                                    </td>
                                    <td style="width: 1%; white-space: nowrap;">
                                        <a href="{{ route('users.edit', $user->id) }}" class="table-link">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>

                                        @if ($user->id != auth()->id())
                                        {!! Form::open(['route' => ['users.destroy', $user->id], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
                                        <a href="#" class="table-link danger" onclick="if (confirm('Are you sure you want to delete this user?')) $(this).closest('form').submit();">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                        {!! Form::close() !!}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {!! $users->render() !!}
                </div>
            </div>
        </div>
    </div>
@stop
