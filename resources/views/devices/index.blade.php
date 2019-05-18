@extends('layout')

@section('content')
    <section class="content-header">
        <h1>Devices <small>{{ $deviceSection->title }}</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><span>Devices</span></li>
            @if (empty($categoryLabel))
                <li class="active"><span>{{ $deviceSection->title }}</span></li>
            @else
                <li><a href="{{ route('devices.index', $type) }}"><span>{{ $deviceSection->title }}</span></a></li>
                <li class="active"><span>{{ $categoryLabel }}</span></li>
            @endif
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">All {{ $deviceSection->title }}</h3>

                <div class="filter-block pull-right">
                    <?php $ithColumn = 0; ?>
                    @foreach ($deviceSection->fields as $field)
                        @if ($field->showInDeviceList())
                            @if ($field->showFilter())
                                <div class="multi-select pull-left" data-opener-id="{{ $field->getInputName() }}" data-ith="{{ $ithColumn }}">
                                    <a href="#" class="btn btn-default btn-sm" data-filter-name="{{ $field->getName() }}">
                                        <i class="fa fa-square-o"></i>
                                        {{ $field->getName() }}
                                        <i class="fa fa-chevron-down"></i>
                                    </a>

                                    <div class="multi-choices" data-auto-close="{{ $field->getInputName() }}">
                                        @foreach ($field->getNiceOptions() as $choice)
                                            <label>
                                                <input type="checkbox" value="{{ $choice['label'] }}"{{ !isset($filters[$field->getInputName()]) || in_array($choice['label'], $filters[$field->getInputName()]) ? ' checked' : '' }}>
                                                <span class="label label-{{ $choice['type'] }}">
                                                {{ $choice['label'] }}
                                            </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <?php $ithColumn++; ?>
                        @endif
                    @endforeach

                    @can('create', $deviceSection)
                        @if (empty($categoryLabel))
                            <a href="{{ route('devices.create', $deviceSection->id) }}" class="btn btn-primary btn-sm pull-left">
                        @else
                            <a href="{{ route('devices.create', ['type' => $deviceSection->id, 'category' => $category]) }}" class="btn btn-primary pull-left">
                                @endif
                                <i class="fa fa-plus-circle fa-lg"></i> Add device
                            </a>
                        @endcan
                </div>
            </div>

            <div class="box-body">
                <table class="table table-responsive table-hover table-striped{{ $devices->count() ? ' datatable' : '' }}">
                    <thead>
                    <tr>
                        @foreach ($deviceSection->fields as $field)
                            @if ($field->showInDeviceList())
                                <th><span>{{ $field->getName() }}</span></th>
                            @endif
                        @endforeach

                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($devices as $device)
                        @can('view', $device)
                        <tr>
                            @foreach ($deviceSection->fields as $field)
                                @if ($field->showInDeviceList())
                                    <td>
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
                                    </td>
                                @endif
                            @endforeach

                            <td style="width: 1%; white-space: nowrap;">
                                <a href="{{ route('devices.show', ['type' => $device->section_id, 'id' => $device->id]) }}" class="table-link" title="View">
                                    <span class="fa-stack">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>

                                @can('superadmin')
                                <a href="{{ route('devices.share', ['type' => $device->section_id, 'id' => $device->id]) }}" class="table-link share-device" title="Share" data-human-id="{{ $device->present()->humanIdField($deviceSection) }}">
                                    <span class="fa-stack">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-share-alt fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>
                                @endcan

                                @can('edit', $device)
                                <a href="{{ route('devices.edit', ['type' => $device->section_id, 'id' => $device->id]) }}" class="table-link" title="Edit">
                                    <span class="fa-stack">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>
                                @endcan

                                @can('delete', $device)
                                {!! Form::open(['route' => ['devices.destroy', $device->id], 'method' => 'DELETE', 'style' => 'display: inline;']) !!}
                                <a href="#" class="table-link danger" onclick="if (confirm('Are you sure you want to delete this device?')) $(this).closest('form').submit();" title="Delete">
                                    <span class="fa-stack">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>
                                {!! Form::close() !!}
                                @endcan
                            </td>
                        </tr>
                        @endcan
                    @empty
                        <tr>
                            <td colspan="{{ $colspan }}" style="text-align:center;">
                                There are currently no devices added. How about

                                @if (empty($categoryLabel))
                                    <a href="{{ route('devices.create', $deviceSection->id) }}">creating one</a>
                                @else
                                    <a href="{{ route('devices.create', ['type' => $deviceSection->id, 'category' => $category]) }}">creating one</a>
                                @endif

                                now?
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <style type="text/css">
        @media (max-width: 760px) {
            <?php $i = 1; ?>
            @foreach ($deviceSection->fields as $field)
                @if ($field->showInDeviceList())
                    table.table-responsive > tbody > tr > td:nth-of-type({{ $i++ }}):before { content: "{{ $field->getName() }}"; }
                @endif
            @endforeach

            table.table-responsive > tbody > tr > td:nth-of-type({{ $i++ }}):before { content: ""; }
        }
    </style>

    <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Share Device</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <h4>User</h4>

                        <select id="user-select" class="form-control">
                            @foreach(App\User::whereRole(App\User::ROLE_SYSADMIN)->orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <h4>Permission Level</h4>

                        <br>

                        <label>
                            {!! Form::radio('permission', App\Permission::GRANT_TYPE_READ, null, [
                                'class' => 'form-control icheck',
                                'required' => true,
                            ]) !!}
                            View
                        </label>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <label>
                            {!! Form::radio('permission', App\Permission::GRANT_TYPE_WRITE, null, [
                                'class' => 'form-control icheck',
                                'required' => true,
                            ]) !!}
                            View &amp; Edit
                        </label>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <label>
                            {!! Form::radio('permission', App\Permission::GRANT_TYPE_FULL, null, [
                                'class' => 'form-control icheck',
                                'required' => true,
                            ]) !!}
                            View, Edit &amp; Delete
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <span class="pull-left more-info hidden" style="margin-top: 5px;"></span>

                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary do-share-device">Share</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer')
    <script>
        $(document).ready(function() {
            var url, devId;

            $('a.share-device').click(function(e) {
                e.preventDefault();

                url = $(this).attr('href');
                devId = $(this).data('human-id');

                $('#shareModal .modal-title').html('Share device <u>' + devId + '</u>');

                $('#shareModal').modal('show');
            });

            $('.do-share-device').click(function(e) {
                e.preventDefault();

                var $modal = $('#shareModal'),
                    $moreinfo = $modal.find('.modal-footer .more-info'),
                    params = {
                        user_id: $('#shareModal #user-select').val(),
                        grant_type: $('#shareModal [name=permission]:checked').val(),
                    };

                $modal.find('button').attr('disabled', true);

                $moreinfo.html('<i class="fa fa-spinner fa-spin"></i> Please Wait...')
                    .removeClass('hidden');

                $.post(url, params, function(r) {
                    $modal.find('button').removeAttr('disabled');

                    if (r.error) {
                        $moreinfo.html('<span style="color:darkred;">' + r.error + '</span>');
                    }

                    if (r.success) {
                        $moreinfo.addClass('hidden');
                        $('#shareModal').modal('hide');

                        var notification = new NotificationFx({
                            message : '<span class="icon fa fa-bullhorn fa-2x"></span><p>Device has been shared</p>',
                            layout : 'bar',
                            effect : 'slidetop',
                            type : 'success' // notice, warning or error
                        });

                        // show the notification
                        notification.show();
                    }
                }).fail(function() {
                    $modal.find('button').removeAttr('disabled');

                    $moreinfo.html('<span style="color:darkred;">Could not share device</span>');
                });
            });
        });
    </script>
@append
