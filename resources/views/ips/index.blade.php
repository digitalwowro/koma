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
                        <a class="btn btn-primary pull-right" data-toggle="modal" href="#myModal">
                            <i class="fa fa-plus-circle fa-lg"></i> Add Subnet
                        </a>
                    </div>
                    @endcan
                </header>

                <div class="main-box-body clearfix">
                    <div class="table-responsive">
                        <table class="table table-responsive table-hover table-striped{{ $subnets->count() ? ' datatable' : '' }}">
                            <thead>
                            <tr>
                                <th>Subnet</th>
                                <th>Free IPs</th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($subnets as $subnet)
                                <tr>
                                    <td style="font-size: 1.15em; font-weight:300;">
                                        <a href="{{ route('ip.subnet', ['subnet' => str_replace('/', '-', $subnet->subnet)]) }}">
                                            {{ $subnet->subnet }}
                                        </a>
                                    </td>
                                    <td style="font-size: 1.15em; font-weight:300;">
                                        {{ App\IpAddress::getFreeForSubnet($subnet->subnet) }} / {{ $subnet->count }}
                                    </td>
                                    <td style="width: 1%; white-space: nowrap;">
                                        <a href="{{ route('ip.subnet', ['subnet' => str_replace('/', '-', $subnet->subnet)]) }}" class="table-link">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>

                                        @can('admin')
                                        {!! Form::open(['route' => ['ip.destroy', str_replace('/', '-', $subnet->subnet)], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
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
                                    <td colspan="3" style="text-align:center;">
                                        There are currently no Subnets added. How about <a data-toggle="modal" href="#myModal">creating one</a> now?
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
            <?php $i = 1; ?>
             {{--@foreach ($fields as $field)
                @if ($field->showInDeviceList())
                    table.table-responsive > tbody > tr > td:nth-of-type({{ $i++ }}):before { content: "{{ $field->getName() }}"; }
                @endif
            @endforeach--}}

            table.table-responsive > tbody > tr > td:nth-of-type({{ $i++ }}):before { content: ""; }
        }
    </style>
@stop
