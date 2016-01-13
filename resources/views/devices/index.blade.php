@extends('layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><span>Devices</span></li>
                <li class="active"><span>{{ $deviceSection->title }}</span></li>
            </ol>

            <h1>Devices <small>{{ $deviceSection->title }}</small></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">All {{ $deviceSection->title }}</h2>

                    <div class="filter-block pull-right">
                        <a href="{{ route('devices.create', $deviceSection->id) }}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus-circle fa-lg"></i> Add device
                        </a>
                    </div>
                </header>

                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                @foreach ($deviceSection->fields as $field)
                                    @if ($field->showInDeviceList())
                                        <th><span>{{ $field->getName() }}</span></th>
                                    @endif
                                @endforeach

                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($devices as $device)
                                <tr>
                                    @foreach ($deviceSection->fields as $field)
                                        @if ($field->showInDeviceList())
                                            <td>{{ $device->data[$field->getName()] }}</td>
                                        @endif
                                    @endforeach

                                    <td style="width: 1%; white-space: nowrap;">
                                        <a href="{{ route('devices.edit', $device->id) }}" class="table-link">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                        {!! Form::open(['route' => ['devices.destroy', $device->id], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
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
                                    <td colspan="{{ $colspan }}" style="text-align:center;">
                                        There are currently no devices added. How about <a href="{{ route('devices.create', $deviceSection->id) }}">creating one</a> now?
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
