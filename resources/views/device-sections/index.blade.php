@extends('layout')

@section('content')
    <section class="content-header">
        <h1>Device Sections <small>List All Device Sections</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li class="active"><span>Device Sections</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Sections</h3>

                <div class="filter-block pull-right">
                    <a href="{{ route('device-sections.create') }}" class="btn btn-primary pull-right">
                        <i class="fa fa-plus-circle fa-lg"></i> Add section
                    </a>
                </div>
            </div>

            <div class="box-body">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Number of fields</th>
                        @can('admin')
                        <th>Created By</th>
                        @endcan
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                        @forelse ($deviceSections as $deviceSection)
                        <tr>
                            <td>
                                {!! $deviceSection->present()->icon !!}
                                <a href="{{ route('devices.index', $deviceSection->id) }}">{{ $deviceSection->title }}</a>
                            </td>

                            <td>
                                {{ count($deviceSection->fields) }}
                            </td>

                            @can('admin')
                            <td>
                                @if ($deviceSection->creator)
                                    <a href="{{ route('users.edit', $deviceSection->creator->id) }}">
                                        {{ $deviceSection->creator->name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            @endcan

                            <td style="width: 1%; white-space: nowrap;">
                                <a href="{{ route('device-sections.edit', $deviceSection->id) }}" class="table-link">
                                    <span class="fa-stack">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>
                                {!! Form::open(['route' => ['device-sections.destroy', $deviceSection->id], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
                                <a href="#" class="table-link danger" onclick="if (confirm('Are you sure you want to delete this device section?')) $(this).closest('form').submit();">
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
                                <td colspan="{{ auth()->user()->can('admin') ? 4 : 3 }}" style="text-align:center;">
                                    There are currently no device sections added. How about <a href="{{ route('device-sections.create') }}">creating one</a> now?
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {!! $deviceSections->render() !!}
            </div>
        </div>
    </section>
@stop
