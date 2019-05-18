@extends('layout')

@section('content')
    <section class="content-header">
        <h1>Subnets <small>{{ $ipCategory->title }}</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><span>IP Addresses</span></li>
            <li class="active"><span>{{ $ipCategory->title }}</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">All {{ $ipCategory->title }} Subnets</h3>

                @can('admin')
                <div class="filter-block pull-right">
                    <a class="btn btn-primary pull-right" data-toggle="modal" href="#myModal">
                        <i class="fa fa-plus-circle fa-lg"></i> Add Subnet
                    </a>
                </div>
                @endcan
            </div>

            <div class="box-body">
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
    </section>

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
