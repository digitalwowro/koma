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
                    <a class="btn btn-primary pull-right" data-toggle="modal" href="#addSubnetModal">
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
                    @php $displayed = 0 @endphp
                    @foreach ($subnets as $subnet)
                        @php $displayed++ @endphp
                        <tr>
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
                                    <a href="{{ route('ip.share', ['category' => $subnet->category_id, 'id' => $subnet->id]) }}" class="table-link share-item" title="Share" data-human-id="{{ $subnet->subnet }}">
                                        <span class="fa-stack">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-share-alt fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </a>
                                @endcan

                                @can('owner', $subnet)
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
                        <tr>
                            <td colspan="3" style="text-align:center;">
                                There are currently no Subnets added. How about <a data-toggle="modal" href="#addSubnetModal">creating one</a> now?
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @include('ips._add-subnet-modal')
    @include('partials._share-modal', [
        'resource_type' => App\Permission::RESOURCE_TYPE_IP_SUBNET,
        'create_permissions' => false,
    ])
@stop

@section('footer')
    <script>
        $.sharer = sharerUtil.init({
            type: 'IP subnet',
        });
    </script>
@append
