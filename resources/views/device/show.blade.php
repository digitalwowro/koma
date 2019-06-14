@extends('layout')

@section('content')
    <section class="content-header">
        <h1>Devices<small>View {{ str_singular($deviceSection->title) }}</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><span>Devices</span></li>
            <li><a href="{{ route('device.index', $deviceSection->id) }}"><span>{{ $deviceSection->title }}</span></a></li>
            <li class="active"><span>View {{ str_singular($deviceSection->title) }}</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">{{ str_singular($deviceSection->title) }} Details</h3>
            </div>

            <div class="box-body">
                @foreach ($device->section->fields as $field)
                    <div class="row">
                        <label for="title" class="col-lg-2 control-label">{{ $field->getName() }}</label>
                        <div class="col-lg-10">
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
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Associated IP Addresses</h3>
            </div>

            <div class="box-body">
                <ul>
                    @forelse ($device->ips as $ip)
                        <li>{{ $ip->ip }}</li>
                    @empty
                        <li style="font-size:.9em; color:grey; font-style: italic;">there are no IP Addresses associated to this device</li>
                    @endforelse
                </ul>
            </div>
        </div>

        @can('admin')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Shared with</h3>
            </div>

            <div class="box-body">
                <ul>
                    @forelse ($device->sharedWith() as $share)
                        <li>
                            <a href="{{ route('users.edit', $share->user_id) }}">{{ $share->user->name }}</a>

                            ({!! $share->present()->grantThrough !!})
                        </li>
                    @empty
                        <li>Device is not shared</li>
                    @endforelse
                </ul>
            </div>
        </div>
        @endcan

        <a href="{{ route('device.index', $deviceSection->id) }}" class="btn btn-primary">Go Back</a>
    </section>
@stop
