@extends('layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><span>IP Addresses</span></li>
                <li><a href="{{ route('ip.index', $ipCategory->id) }}"><span>{{ $ipCategory->title }}</span></a></li>
                <li class="active"><span>View subnet: {{ $subnet }}</span></li>
            </ol>

            <h1>IP Addresses <small>in subnet {{ $subnet }}</small></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">All IP Addresses In Subnet</h2>
                </header>

                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table table-responsive table-hover table-striped">
                            <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Status</th>
                                @foreach ($ipFields as $ipField)
                                <th>
                                    {{ $ipField->title }}
                                </th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($ips as $ip)
                                <tr>
                                    <td>
                                        {{ $ip->ip }}
                                    </td>
                                    <td>
                                        @if ($ip->assigned())
                                            assigned to <a href="{{ route('devices.edit', ['type' => $ip->device->section_id, 'id' => $ip->device->id]) }}">{{ $ip->device->present()->humanIdField }}</a>
                                        @else
                                            <span class="label label-danger">Unassigned</span>
                                        @endif
                                    </td>
                                    @foreach ($ipFields as $ipField)
                                    <td>
                                        {!! $ip->getFieldValue($ipField) !!}
                                    </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 2 + count($ipFields) }}" style="text-align:center;">
                                        There are currently no Subnets added. How about <a href="{{ route('ip.create', $ipCategory->id) }}">creating one</a> now?
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

    @include('ips._add-subnet-modal')

    <style type="text/css">
        @media (max-width: 760px) {
            table.table-responsive > tbody > tr > td:nth-of-type(1):before { content: ""; }
        }
    </style>
@stop
