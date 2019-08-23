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

                @can('create', $ipCategory)
                <div class="filter-block pull-right">
                    <a class="btn btn-primary pull-right" href="{{ route('ip.create', $ipCategory->id) }}">
                        <i class="fa fa-plus-circle fa-lg"></i> Add Subnet
                    </a>
                </div>
                @endcan
            </div>

            <div class="box-body">
                <table class="table table-responsive table-hover table-striped{{ $subnets->count() ? ' datatable' : '' }}">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Subnet</th>
                        <th>Free IPs</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $displayed = 0 @endphp
                    @foreach ($subnets as $subnet)
                        @php $displayed++ @endphp
                        <tr>
                            <td>
                                {{ $subnet->data['name'] ?? '' }}
                            </td>
                            <td>
                                <a href="{{ route('ip.subnet', ['subnet' => str_replace('/', '-', $subnet->subnet)]) }}">
                                    {{ $subnet->subnet }}
                                </a>
                            </td>
                            <td>
                                {{ App\IpAddress::getFreeForSubnet($subnet->subnet) }} / {{ $subnet->count }}
                            </td>
                            <td style="width: 1%; white-space: nowrap;">
                                <a href="{{ route('ip.subnet', ['subnet' => str_replace('/', '-', $subnet->subnet)]) }}" class="table-link">
                                    <span class="fa-stack">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>

                                @can('share', $subnet)
                                    <a href="#" class="table-link share-item" title="Share" data-id="{{ $subnet->id }}" data-human-id="{{ $subnet->subnet }}">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-share-alt fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </a>
                                @endcan

                                @can('edit', $subnet)
                                    <a href="{{ route('ip.edit', ['category' => $ipCategory->id, 'id' => $subnet->id]) }}" class="table-link" title="Edit">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </a>
                                @endcan

                                @can('delete', $subnet)
                                    {!! Form::open(['route' => ['ip.destroy', $subnet->id], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
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
                    @endforeach

                    @if (!$displayed)
                        <tr class="norows">
                            <td colspan="3" style="text-align:center;">
                                There are currently no Subnets added. How about <a href="{{ route('ip.create', $ipCategory->id) }}">creating one</a> now?
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @include('partials._share-modal', [
        'resource_type' => 'IP subnet',
        'create_permissions' => false,
    ])
@stop
