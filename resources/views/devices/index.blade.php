@extends('layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><span>Devices</span></li>
                @if (empty($categoryLabel))
                    <li class="active"><span>{{ $deviceSection->title }}</span></li>
                @else
                    <li><a href="{{ route('devices.index', $type) }}"><span>{{ $deviceSection->title }}</span></a></li>
                    <li class="active"><span>{{ $categoryLabel }}</span></li>
                @endif
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
                        <?php $ithColumn = 0; ?>
                        @foreach ($deviceSection->fields as $field)
                            @if ($field->showInDeviceList())
                            @if ($field->showFilter())
                            <div class="multi-select pull-left" data-opener-id="{{ $field->getInputName() }}" data-ith="{{ $ithColumn }}">
                                <a href="#" class="btn btn-default" data-filter-name="{{ $field->getName() }}">
                                    <i class="fa fa-square-o"></i>
                                    {{ $field->getName() }}
                                    <i class="fa fa-chevron-down"></i>
                                </a>

                                <div class="multi-choices" data-auto-close="{{ $field->getInputName() }}">
                                    @foreach ($field->getNiceOptions() as $choice)
                                        <label>
                                            <input type="checkbox" value="{{ $choice['label'] }}"{{ ! isset($filters[$field->getInputName()]) || in_array($choice['label'], $filters[$field->getInputName()]) ? ' checked' : '' }}>
                                            <span class="label label-{{ $choice['type'] }}">
                                                {{ $choice['label'] }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            <?php $ithColumn++; ?>
                            @endif
                        @endforeach

                        @can('edit', $deviceSection)
                            @if (empty($categoryLabel))
                                <a href="{{ route('devices.create', $deviceSection->id) }}" class="btn btn-primary pull-left">
                            @else
                                <a href="{{ route('devices.create', ['type' => $deviceSection->id, 'category' => $category]) }}" class="btn btn-primary pull-left">
                            @endif
                            <i class="fa fa-plus-circle fa-lg"></i> Add device
                        </a>
                        @endcan
                    </div>
                </header>

                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table table-responsive table-hover table-striped{{ $devices->count() ? ' datatable' : '' }}">
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
                                @can('view', $device)
                                <tr>
                                    @foreach ($deviceSection->fields as $field)
                                        @if ($field->showInDeviceList())
                                            <td>
                                                @if (method_exists($field, 'customDeviceListContent'))
                                                    {!! $field->customDeviceListContent($device) !!}
                                                @elseif (isset($device->data[$field->getInputName()]))
                                                    @if (is_array($device->data[$field->getInputName()]))
                                                        {!! urlify(implode(', ', $device->data[$field->getInputName()])) !!}
                                                    @else
                                                        {!! urlify($device->data[$field->getInputName()]) !!}
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach

                                    <td style="width: 1%; white-space: nowrap;">
                                        <a href="{{ route('devices.show', ['type' => $device->section_id, 'id' => $device->id]) }}" class="table-link">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>

                                        @can('edit', $device)
                                        <a href="{{ route('devices.edit', ['type' => $device->section_id, 'id' => $device->id]) }}" class="table-link">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                        @endcan

                                        @can('delete', $device)
                                        {!! Form::open(['route' => ['devices.destroy', $device->id], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
                                        <a href="#" class="table-link danger" onclick="if (confirm('Are you sure you want to delete this device?')) $(this).closest('form').submit();">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                        {!! Form::close() !!}
                                        @endcan
                                    </td>
                                </tr>
                                @endcan
                            @empty
                                <tr>
                                    <td colspan="{{ $colspan }}" style="text-align:center;">
                                        There are currently no devices added. How about

                                        @if (empty($categoryLabel))
                                            <a href="{{ route('devices.create', $deviceSection->id) }}">creating one</a>
                                        @else
                                            <a href="{{ route('devices.create', ['type' => $deviceSection->id, 'category' => $category]) }}">creating one</a>
                                        @endif

                                        now?
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
    <style type="text/css">
        @media (max-width: 760px) {
            <?php $i = 1; ?>
            @foreach ($deviceSection->fields as $field)
                @if ($field->showInDeviceList())
                    table.table-responsive > tbody > tr > td:nth-of-type({{ $i++ }}):before { content: "{{ $field->getName() }}"; }
                @endif
            @endforeach

            table.table-responsive > tbody > tr > td:nth-of-type({{ $i++ }}):before { content: ""; }
        }
    </style>
@stop
