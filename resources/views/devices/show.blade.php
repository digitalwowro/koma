@extends('layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><span>Devices</span></li>
                <li><a href="{{ route('devices.index', $deviceSection->id) }}"><span>{{ $deviceSection->title }}</span></a></li>
                <li class="active"><span>View {{ str_singular($deviceSection->title) }}</span></li>
            </ol>

            <h1>Devices<small>View {{ str_singular($deviceSection->title) }}</small></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box">
                <header class="main-box-header">
                    <h2 class="pull-left">{{ str_singular($deviceSection->title) }} Details</h2>
                </header>

                <div class="main-box-body">
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

            <div class="main-box">
                <header class="main-box-header">
                    <h2 class="pull-left">Associated IP Addresses</h2>
                </header>

                <div class="main-box-body">
                    <ul>
                        @forelse ($device->ips as $ip)
                            <li>{{ $ip->ip }}</li>
                        @empty
                            <li style="font-size:.9em; color:grey; font-style: italic;">there are no IP Addresses associated to this device</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="main-box">
                <header class="main-box-header">
                    <h2 class="pull-left">Shared with</h2>
                </header>

                <div class="main-box-body">
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

            <div class="main-box">
                <div class="main-box-body" style="padding-top: 20px;">
                    <a href="{{ route('devices.index', $deviceSection->id) }}" class="btn btn-default btn-xs">Go Back</a>
                </div>
            </div>
        </div>
    </div>
@stop
