@extends('layout')

@section('content')
    <section class="content-header">
        <h1>IP Addresses <small>in subnet {{ $subnet->subnet }}</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><span>IP Addresses</span></li>
            <li><a href="{{ route('subnet.index', $ipCategory->id) }}"><span>{{ $ipCategory->title }}</span></a></li>
            <li class="active"><span>View subnet: {{ $subnet->subnet }}</span></li>
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
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($ips as $ipData)
                        <tr>
                            <td style="font-size: 16px;">
                                {{ $ipData['ip'] }}
                            </td>
                            <td>
                                @if (isset($ipData['reserved']) && $ipData['reserved'] === true)
                                    <span class="label label-danger">Reserved</span>
                                @elseif (!empty($ipData['device_id']) && !empty($ipData['device']))
                                    <span class="label label-warning">assigned</span> to <a href="{{ route('item.edit', $ipData['device']->id) }}">{{ $ipData['device']->present()->humanIdField }}</a>
                                @elseif (!empty($ipData['device_id']))
                                    <span class="label label-warning">assigned</span> to <i class="fa fa-question-circle"></i>
                                @else
                                    <span class="label label-success">Unassigned</span>
                                @endif
                            </td>
                            @foreach ($ipFields as $ipField)
                            <td>
                                @if (!empty($ipData['device']))
                                    {!! $ipData['device']->ipFieldValue($ipField) !!}
                                @else
                                    -
                                @endif
                            </td>
                            @endforeach
                            <td>
                                @if (!empty($ipData['device_id']))
                                @can('update', $subnet)
                                    {!! Form::open(['route' => ['subnet.unassign', $subnet->id], 'method' => 'POST', 'style' => 'display: inline;']) !!}
                                        {!! Form::hidden('ip', $ipData['ip']) !!}

                                        <a href="#" class="table-link danger" onclick="if (confirm('Are you sure you want to unassign this IP address?')) $(this).closest('form').submit(); return false;" title="Unassign">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-unlink fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                    {!! Form::close() !!}
                                @endcan
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="pull-right">
                    {!! $ips->render() !!}
                </div>
            </div>
        </div>

        @if ($notes = $subnet->notes)
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Notes</h3>
            </div>

            <div class="box-body">
                {!! xss_safe_newline_to_br($notes) !!}
            </div>
        </div>
        @endif

        @can('owner', $subnet)
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Shared with</h3>
                </div>

                <div class="box-body">
                    <ul>
                        @forelse ($subnet->sharedWith() as $share)
                            <li>
                                {!! $share->present()->sharedWith !!}

                                ({!! $share->present()->grantThrough !!})
                            </li>
                        @empty
                            <li>IP Subnet is not shared</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endcan

        <a href="{{ route('subnet.index', $ipCategory->id) }}" class="btn btn-primary">Go Back</a>
    </section>
@stop
