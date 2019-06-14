@extends('layout')

@section('content')
    <section class="content-header">
        <h1>IP Addresses <small>in subnet {{ $subnet }}</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><span>IP Addresses</span></li>
            <li><a href="{{ route('ip.index', $ipCategory->id) }}"><span>{{ $ipCategory->title }}</span></a></li>
            <li class="active"><span>View subnet: {{ $subnet }}</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">All IP Addresses In Subnet</h3>
            </div>

            <div class="box-body">
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
                                    assigned to <a href="{{ route('device.edit', ['type' => $ip->device->section_id, 'id' => $ip->device->id]) }}">{{ $ip->device->present()->humanIdField }}</a>
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
    </section>

    @include('ips._add-subnet-modal')
@stop
