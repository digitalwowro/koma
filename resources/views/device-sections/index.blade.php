@extends('layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li class="active"><span>Device Sections</span></li>
            </ol>

            <h1>Device Sections <small>List All Device Sections</small></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">Sections</h2>

                    <div class="filter-block pull-right">
                        <a href="{{ route('device-sections.create') }}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus-circle fa-lg"></i> Add section
                        </a>
                    </div>
                </header>

                <div class="main-box-body clearfix">
                    <div class="table-responsive">
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
                    </div>

                    {!! $deviceSections->render() !!}
                </div>
            </div>
        </div>
    </div>
@stop
