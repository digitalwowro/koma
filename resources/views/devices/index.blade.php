@extends('layout')

@section('content')
    <div class="row">
        <div class="col-lg-12">
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

            <h1>Devices <small>{{ $deviceSection->title }}</small></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">All {{ $deviceSection->title }}</h2>

                    <div class="filter-block pull-right">
                        <?php $ithColumn = 0; ?>
                        @foreach ($deviceSection->fields as $field)
                            @if ($field->showInDeviceList())
                            @if ($field->showFilter())
                            <div class="multi-select pull-left" data-opener-id="{{ $field->getInputName() }}" data-ith="{{ $ithColumn }}">
                                <a href="#" class="btn btn-default" data-filter-name="{{ $field->getName() }}">
                                    <i class="fa fa-square-o"></i>
                                    {{ $field->getName() }}
                                    <i class="fa fa-chevron-down"></i>
                                </a>

                                <div class="multi-choices" data-auto-close="{{ $field->getInputName() }}">
                                    @foreach ($field->getNiceOptions() as $choice)
                                        <label>
                                            <input type="checkbox" value="{{ $choice['label'] }}"{{ ! isset($filters[$field->getInputName()]) || in_array($choice['label'], $filters[$field->getInputName()]) ? ' checked' : '' }}>
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

                        @can('edit', $deviceSection)
                            @if (empty($categoryLabel))
                                <a href="{{ route('devices.create', $deviceSection->id) }}" class="btn btn-primary pull-left">
                            @else
                                <a href="{{ route('devices.create', ['type' => $deviceSection->id, 'category' => $category]) }}" class="btn btn-primary pull-left">
                            @endif
                            <i class="fa fa-plus-circle fa-lg"></i> Add device
                        </a>
                        @endcan
                    </div>
                </header>

                <div class="main-box-body clearfix">
                    <div class="table-responsive">
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
                                        <a href="{{ route('devices.share', ['type' => $device->section_id, 'id' => $device->id]) }}" class="table-link share-device" title="Share">
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
            </div>
        </div>
    </div>

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
                        <label for="user-select">User</label>
                        <select id="user-select" style="width:100%;">
                            @foreach(App\User::whereRole(App\User::ROLE_SYSADMIN)->orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Permission Level</label>

                        <br>

                        <div class="radio radio-inline" style="margin-top: 10px;">
                            {!! Form::radio('permission', App\Permission::GRANT_TYPE_READ, null, [
                                'id' => "grant-view",
                                'class' => 'form-control',
                                'required' => true,
                            ]) !!}
                            <label for="grant-view">
                                View
                            </label>
                        </div>

                        <div class="radio radio-inline" style="margin-top: 10px;">
                            {!! Form::radio('permission', App\Permission::GRANT_TYPE_WRITE, null, [
                                'id' => "grant-edit",
                                'class' => 'form-control',
                                'required' => true,
                            ]) !!}
                            <label for="grant-edit">
                                View &amp; Edit
                            </label>
                        </div>

                        <div class="radio radio-inline" style="margin-top: 10px;">
                            {!! Form::radio('permission', App\Permission::GRANT_TYPE_FULL, null, [
                                'id' => "grant-full",
                                'class' => 'form-control',
                                'required' => true,
                            ]) !!}
                            <label for="grant-full">
                                View, Edit &amp; Delete
                            </label>
                        </div>
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
                devId = $.trim($(this).closest('tr').find('td:first').text());

                $('#shareModal .modal-title').html('Share device ' + devId);

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
