@extends('layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><span>IP Addresses</span></li>
                <li class="active"><span>{{ $ipCategory->title }}</span></li>
            </ol>

            <h1>Subnets <small>{{ $ipCategory->title }}</small></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">All {{ $ipCategory->title }} Subnets</h2>

                    @can('admin')
                    <div class="filter-block pull-right">
                        <a href="{{ route('ip.create', $ipCategory->id) }}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus-circle fa-lg"></i> Add Subnet
                        </a>
                    </div>
                    @endcan
                </header>

                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table table-responsive table-hover table-striped">
                            <thead>
                            <tr>
                                @foreach ($fields as $field)
                                    @if ($field->showInDeviceList())
                                        <th><span>{{ $field->getName() }}</span></th>
                                    @endif
                                @endforeach

                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($ips as $ip)
                                <tr>
                                    @foreach ($fields as $field)
                                        @if ($field->showInDeviceList())
                                            <td>
                                                @if (method_exists($field, 'customDeviceListContent'))
                                                    {!! $field->customDeviceListContent($ip) !!}
                                                @elseif (isset($ip->data[$field->getInputName()]))
                                                    @if (is_array($ip->data[$field->getInputName()]))
                                                        {!! urlify(implode(', ', $ip->data[$field->getInputName()])) !!}
                                                    @else
                                                        {!! urlify($ip->data[$field->getInputName()]) !!}
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach

                                    <td style="width: 1%; white-space: nowrap;">
                                        <a href="{{ route('ip.show', ['category' => $ip->category_id, 'id' => $ip->id]) }}" class="table-link">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>

                                        @can('admin')
                                        <a href="{{ route('ip.edit', ['category' => $ip->category_id, 'id' => $ip->id]) }}" class="table-link">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                        {!! Form::open(['route' => ['ip.destroy', $ip->id], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
                                        <a href="#" class="table-link danger" onclick="if (confirm('Are you sure you want to delete this subnet?')) $(this).closest('form').submit();">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                        {!! Form::close() !!}
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $colspan }}" style="text-align:center;">
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
    <style type="text/css">
        @media (max-width: 760px) {
            <?php $i = 1; ?>
            @foreach ($fields as $field)
                @if ($field->showInDeviceList())
                    table.table-responsive > tbody > tr > td:nth-of-type({{ $i++ }}):before { content: "{{ $field->getName() }}"; }
                @endif
            @endforeach

            table.table-responsive > tbody > tr > td:nth-of-type({{ $i++ }}):before { content: ""; }
        }
    </style>
@stop
