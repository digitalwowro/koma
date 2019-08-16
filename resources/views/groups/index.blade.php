@extends('layout')

@section('content')
    <section class="content-header">
        <h1>Groups <small>List All Groups</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li class="active"><span>Groups</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Groups</h3>

                <div class="filter-block pull-right">
                    <a href="{{ route('groups.create') }}" class="btn btn-primary pull-right">
                        <i class="fa fa-plus-circle fa-lg"></i> Add group
                    </a>
                </div>
            </div>

            <div class="box-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Group ID</th>
                        <th>Name</th>
                        <th>Created</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                        @forelse ($groups as $group)
                        <tr>
                            <td>
                                #{{ $group->id }}
                            </td>
                            <td>
                                {{ $group->name }}
                            </td>
                            <td>
                                {{ $group->created_at->format('d/m/Y') }}
                            </td>
                            <td style="width: 1%; white-space: nowrap;">
                                <a href="{{ route('groups.edit', $group->id) }}" class="table-link">
                                    <span class="fa-stack">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>

                                {!! Form::open(['route' => ['groups.destroy', $group->id], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
                                <a href="#" class="table-link danger" onclick="if (confirm('Are you sure you want to delete this group?')) $(this).closest('form').submit();">
                                    <span class="fa-stack">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>
                                {!! Form::close() !!}
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    There are no user groups created.
                                    <a href="{{ route('groups.create') }}">Click here</a> to create one now.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {!! $groups->render() !!}
            </div>
        </div>
    </section>
@stop
