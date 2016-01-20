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
                        <table class="table table-responsive table-hover table-striped">
                            <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Device</th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($ips as $ip)
                                <tr>
                                    <td>
                                        {{ $ip->ip }}
                                    </td>
                                    <td>
                                        {!! $ip->device ? $ip->device->title : '- unassigned - <a href="#" data-action="assign" data-assign-id="' . $ip->id . '">assign now?</a>' !!}
                                    </td>
                                    <td style="width: 1%; white-space: nowrap;">
                                        <a href="{{ route('ip.show', ['category' => $ip->category_id, 'id' => $ip->id]) }}" class="table-link">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                        <a href="#" class="table-link" data-action="assign" data-assign-id="{{ $ip->id }}">
                                            <span class="fa-stack">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-check-square-o fa-stack-1x fa-inverse"></i>
                                            </span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align:center;">
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

    <div class="modal fade" id="assign-now" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Assign IP To Device</h4>
                </div>
                {!! Form::open(['route' => ['ip.assign', $ipCategory->id], 'role' => 'form', 'method' => 'POST']) !!}
                    <input type="hidden" class="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="subnetInput">Device</label>
                            <input type="text" name="subnet" class="form-control" id="subnetInput" placeholder="127.0.0.1/32">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary">Add</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

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

@section('footer')
    <script>
        $(document).ready(function() {
            $('[data-action="assign"]').click(function() {
                $('#assign-now .id').val($(this).data('assign-id'));
                $('#assign-now').modal('show');

                return false;
            });
        });
    </script>
@append
