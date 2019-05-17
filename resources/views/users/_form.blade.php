<div class="form-group">
    <label for="name" class="col-xs-2 control-label">Name</label>
    <div class="col-xs-10">
        {!! Form::text('name', null, [
            'id' => 'name',
            'class' => 'form-control',
            'placeholder' => 'Name',
        ]) !!}
    </div>
</div>

<div class="form-group">
    <label for="email" class="col-xs-2 control-label">Email</label>
    <div class="col-xs-10">
        {!! Form::email('email', (! isset($user) || filter_var($user->email, FILTER_VALIDATE_EMAIL) ? null : ''), [
            'id' => 'email',
            'class' => 'form-control',
            'placeholder' => 'Email',
            'required' => true,
        ]) !!}
    </div>
</div>

<div class="form-group">
    <label for="password" class="col-xs-2 control-label">Password</label>
    <div class="col-xs-10">
        {!! Form::password('password', [
            'id' => 'password',
            'class' => 'form-control',
            'style' => 'font-style:italic;',
            'placeholder' => 'leave blank if you don\'t want to assign or change the password',
        ]) !!}
    </div>
</div>

@if (!isset($user) || auth()->id() != $user->id)
    <div class="col-xs-12">
        <div class="form-group">
            <label class="col-xs-2 control-label">Role</label>
            <div class="col-xs-10">
                <div class="radio">
                    {!! Form::radio('role', App\User::ROLE_SYSADMIN, null, [
                        'id' => 'role-sysadmin',
                        'class' => 'form-control',
                        'required' => true,
                    ]) !!}
                    <label for="role-sysadmin">
                        Sysadmin
                    </label>
                </div>

                <div class="radio">
                    {!! Form::radio('role', App\User::ROLE_ADMIN, null, [
                        'id' => 'role-admin',
                        'class' => 'form-control',
                        'required' => true,
                    ]) !!}
                    <label for="role-admin">
                        Admin
                    </label>
                </div>

                <div class="radio">
                    {!! Form::radio('role', App\User::ROLE_SUPERADMIN, null, [
                        'id' => 'role-superadmin',
                        'class' => 'form-control',
                        'required' => true,
                    ]) !!}
                    <label for="role-superadmin">
                        Superadmin
                    </label>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="form-group" id="permissions-section" style="display:{{ isset($user) && auth()->id() != $user->id && !$user->isAdmin() && !$user->isSuperAdmin() ? '' : 'none' }};">
    <label for="input1" class="col-xs-2 control-label">Permissions</label>
    <div class="col-xs-10">
        <table class="table table-condensed table-permissions">
            <thead>
                <tr>
                    <th>Resource</th>
                    <th>Permission</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr class="empty{{ isset($user) && count($user->permissions) ? ' hidden' : '' }}">
                    <td colspan="3" style="text-align: center;">This user currently has no permissions granted.</td>
                </tr>
                @if (isset($user))
                @foreach ($user->permissions as $i => $permission)
                @if ($permission->preloadResource())
                <tr>
                    @if ($permission->resource_type === $permission::RESOURCE_TYPE_DEVICES_FULL)
                    <td>
                        <i class="fa fa-star"></i>

                        Global

                        <input type="hidden" name="permissions[{{ $i }}][type]" value="global">
                        <input type="hidden" name="permissions[{{ $i }}][id]" value="">
                    </td>
                    @elseif ($permission->resource_type === $permission::RESOURCE_TYPE_DEVICES_SECTION)
                    <td>
                        <i class="fa fa-server"></i>

                        <a href="{{ route('devices.index', $permission->resource->id) }}" target="_blank">
                            {{ $permission->resource->title }}
                        </a>

                        <input type="hidden" name="permissions[{{ $i }}][type]" value="section">
                        <input type="hidden" name="permissions[{{ $i }}][id]" value="{{ $permission->resource_id }}">
                    </td>
                    @elseif ($permission->resource_type === $permission::RESOURCE_TYPE_DEVICES_DEVICE)
                    <td>
                        <i class="fa fa-server"></i>

                        <a href="{{ route('devices.index', $permission->resource->section_id) }}" target="_blank">
                            {{ $permission->resource->section->title }}
                        </a>

                        &gt;

                        <a href="{{ route('devices.show', ['type' => $permission->resource->section_id, 'id' => $permission->resource->id]) }}" target="_blank">
                            {{ $permission->resource->present()->humanIdField }}
                        </a>

                        <input type="hidden" name="permissions[{{ $i }}][type]" value="device">
                        <input type="hidden" name="permissions[{{ $i }}][id]" value="{{ $permission->resource_id }}">
                    @else
                    <td></td>
                    @endif

                    <td>
                        <div class="radio pull-left" style="margin-right: 10px;">
                            {!! Form::radio("permissions[{$i}][level]", $permission::GRANT_TYPE_READ, $permission->grant_type === $permission::GRANT_TYPE_READ, [
                                'id' => "grant-{$i}-view",
                                'class' => 'form-control',
                                'required' => true,
                            ]) !!}
                            <label for="grant-{{ $i }}-view">
                                @if ($permission->resource_type == $permission::RESOURCE_TYPE_DEVICES_DEVICE)
                                    View
                                @else
                                    View all
                                @endif
                            </label>
                        </div>

                        <div class="radio pull-left" style="margin-right: 10px;">
                            {!! Form::radio("permissions[{$i}][level]", $permission::GRANT_TYPE_WRITE, $permission->grant_type === $permission::GRANT_TYPE_WRITE, [
                                'id' => "grant-{$i}-edit",
                                'class' => 'form-control',
                                'required' => true,
                            ]) !!}
                            <label for="grant-{{ $i }}-edit">
                                @if ($permission->resource_type == $permission::RESOURCE_TYPE_DEVICES_DEVICE)
                                    View &amp; Edit
                                @else
                                    View &amp; Edit all
                                @endif
                            </label>
                        </div>

                        <div class="radio pull-left" style="margin-right: 10px;">
                            {!! Form::radio("permissions[{$i}][level]", $permission::GRANT_TYPE_FULL, $permission->grant_type === $permission::GRANT_TYPE_FULL, [
                                'id' => "grant-{$i}-full",
                                'class' => 'form-control',
                                'required' => true,
                            ]) !!}
                            <label for="grant-{{ $i }}-full">
                                @if ($permission->resource_type === $permission::RESOURCE_TYPE_DEVICES_DEVICE)
                                    View, Edit &amp; Delete
                                @else
                                    View, Edit &amp; Delete all
                                @endif
                            </label>
                        </div>

                        @if ($permission->resource_type === $permission::RESOURCE_TYPE_DEVICES_SECTION)
                        <div class="radio pull-left" style="margin-right: 10px;">
                            {!! Form::radio("permissions[{$i}][level]", $permission::GRANT_TYPE_CREATE, $permission->grant_type === $permission::GRANT_TYPE_CREATE, [
                                'id' => "grant-{$i}-create",
                                'class' => 'form-control',
                                'required' => true,
                            ]) !!}
                            <label for="grant-{{ $i }}-create">
                                Create
                            </label>
                        </div>

                        <div class="radio pull-left" style="margin-right: 10px;">
                            {!! Form::radio("permissions[{$i}][level]", $permission::GRANT_TYPE_READ_CREATE, $permission->grant_type === $permission::GRANT_TYPE_READ_CREATE, [
                                'id' => "grant-{$i}-viewcreate",
                                'class' => 'form-control',
                                'required' => true,
                            ]) !!}
                            <label for="grant-{{ $i }}-viewcreate">
                                View all &amp; Create
                            </label>
                        </div>

                        <div class="radio pull-left" style="margin-right: 10px;">
                            {!! Form::radio("permissions[{$i}][level]", $permission::GRANT_TYPE_OWNER, $permission->grant_type === $permission::GRANT_TYPE_OWNER, [
                                'id' => "grant-{$i}-owner",
                                'class' => 'form-control',
                                'required' => true,
                            ]) !!}
                            <label for="grant-{{ $i }}-owner">
                                Owner
                            </label>
                        </div>
                        @endif
                    </td>

                    <td>
                        <a href="#" style="color:red;" class="delete-this-permission" title="Delete">
                            <i class="fa fa-trash-o"></i>
                        </a>
                    </td>
                </tr>
                @endif
                @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right;">
                        <a class="btn btn-primary btn-xs add-global-access">
                            <i class="fa fa-star"></i>
                            Add Global Access
                        </a>

                        <a class="btn btn-primary btn-xs" href="#addSectionModal" data-toggle="modal">
                            <i class="fa fa-server"></i>
                            Add Device Section
                        </a>

                        <a class="btn btn-primary btn-xs" href="#addDeviceModal" data-toggle="modal">
                            <i class="fa fa-server"></i>
                            Add Device
                        </a>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="modal fade" id="addDeviceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add Permission for Device</h4>
            </div>
            <div class="modal-body">
                <label for="device-section-select">Section</label>
                <select id="device-section-select" style="width:100%;">
                    @foreach(App\DeviceSection::orderBy('title')->get() as $section)
                        <option value="{{ $section->id }}">{{ $section->title }}</option>
                    @endforeach
                </select>

                <label for="device-select">Device</label>
                <select id="device-select" style="width:100%;">
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary do-add-device">Add</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addSectionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add Permission for Section</h4>
            </div>
            <div class="modal-body">
                <label for="section-select">Section</label>
                <select id="section-select" style="width:100%;">
                    @foreach(App\DeviceSection::orderBy('title')->get() as $section)
                        <option value="{{ $section->id }}">{{ $section->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary do-add-section">Add</button>
            </div>
        </div>
    </div>
</div>

@section('footer')
<script>
    $(document).ready(function() {
        var $table = $('table.table-permissions');
        var deviceSections = {!! App\Device::all()->map(function($item) { $item->title = @reset($item->data); return $item; })->keyBy('id')->groupBy('section_id')->map(function($item) { return $item->pluck('title', 'id'); })->toJson() !!};

        function getSectionTitle(section_id) {
            var sections = {!! App\DeviceSection::all()->pluck('title', 'id')->toJson() !!};
            return sections[section_id];
        }

        function getDeviceTitle(device_id) {
            var devices = {!! App\Device::all()->keyBy('id')->map(function($item) { return @reset($item->data); })->toJson() !!};
            return devices[device_id];
        }

        function addPerm(type, section_id, device_id) {
            var to_add = '<tr><td>',
                nextId = $table.find('tbody tr:not(.empty)').length
                    ? (parseInt($table.find('tbody tr:last [name^=permissions]').first().attr('name').split('[')[1].replace(/[^0-9]/g, '')) + 1)
                    : 0;

            if (type === 'global') {
                to_add = to_add + '<i class="fa fa-star"></i>' +
                    '' +
                    'Global' +
                    '' +
                    '<input type="hidden" name="permissions[' + nextId + '][type]" value="global">' +
                    '<input type="hidden" name="permissions[' + nextId + '][id]" value="">';
            } else if (type === 'section') {
                to_add = to_add + '<i class="fa fa-server"></i>' +
                    '' +
                    '<a href="' + '{{ route('devices.index', '_SID_') }}'.replace('_SID_', section_id) + '" target="_blank"> ' +
                        getSectionTitle(section_id) +
                    '</a>' +

                    '<input type="hidden" name="permissions[' + nextId + '][type]" value="section">' +
                    '<input type="hidden" name="permissions[' + nextId + '][id]" value="' + section_id + '">';
            } else if (type === 'device') {
                to_add = to_add + '<i class="fa fa-server"></i>' +
                    '' +
                    '<a href="' + '{{ route('devices.index', '_SID_') }}'.replace('_SID_', section_id) + '" target="_blank"> ' +
                        getSectionTitle(section_id) +
                    '</a>' +
                    ' ' +
                    '&gt;' +
                    ' ' +
                    '<a href="' + '{{ route('devices.show', ['type' => '_SID_', 'id' => '_DID_']) }}'.replace('_SID_', section_id).replace('_DID_', device_id) + '" target="_blank"> ' +
                        getDeviceTitle(device_id) +
                    '</a>' +
                    '' +
                    '<input type="hidden" name="permissions[' + nextId + '][type]" value="device">' +
                    '<input type="hidden" name="permissions[' + nextId + '][id]" value="' + device_id + '">';
            }

            to_add = to_add +
                    '</td><td>' +
                        '<div class="radio pull-left" style="margin-right: 10px;">' +
                            '<input type="radio" name="permissions[' + nextId + '][level]" id="grant-' + nextId + '-view" class="form-control" value="{{ \App\Permission::GRANT_TYPE_READ }}" required checked>' +

                            '<label for="grant-' + nextId + '-view">' +
                                (type === 'device' ? 'View' : 'View all') +
                            '</label>' +
                        '</div>' +

                        '<div class="radio pull-left" style="margin-right: 10px;">' +
                            '<input type="radio" name="permissions[' + nextId + '][level]" id="grant-' + nextId + '-edit" class="form-control" value="{{ \App\Permission::GRANT_TYPE_WRITE }}" required>' +

                            '<label for="grant-' + nextId + '-edit">' +
                                (type === 'device' ? 'View &amp; Edit' : 'View &amp; Edit all') +
                            '</label>' +
                        '</div>' +

                        '<div class="radio pull-left" style="margin-right: 10px;">' +
                            '<input type="radio" name="permissions[' + nextId + '][level]" id="grant-' + nextId + '-full" class="form-control" value="{{ \App\Permission::GRANT_TYPE_FULL }}" required>' +

                            '<label for="grant-' + nextId + '-full">' +
                                (type === 'device' ? 'View, Edit &amp; Delete' : 'View, Edit &amp; Delete all') +
                            '</label>' +
                        '</div>' +

                        '<div class="radio pull-left' + (type === 'section' ? '' : ' hidden') + '" style="margin-right: 10px;">' +
                            '<input type="radio" name="permissions[' + nextId + '][level]" id="grant-' + nextId + '-create" class="form-control" value="{{ \App\Permission::GRANT_TYPE_CREATE }}" required>' +

                            '<label for="grant-' + nextId + '-create">' +
                                'Create' +
                            '</label>' +
                        '</div>' +

                        '<div class="radio pull-left' + (type === 'section' ? '' : ' hidden') + '" style="margin-right: 10px;">' +
                            '<input type="radio" name="permissions[' + nextId + '][level]" id="grant-' + nextId + '-viewcreate" class="form-control" value="{{ \App\Permission::GRANT_TYPE_READ_CREATE }}" required>' +

                            '<label for="grant-' + nextId + '-viewcreate">' +
                                'View all &amp; Create' +
                            '</label>' +
                        '</div>' +

                        '<div class="radio pull-left' + (type === 'section' ? '' : ' hidden') + '" style="margin-right: 10px;">' +
                            '<input type="radio" name="permissions[' + nextId + '][level]" id="grant-' + nextId + '-owner" class="form-control" value="{{ \App\Permission::GRANT_TYPE_OWNER }}" required>' +

                            '<label for="grant-' + nextId + '-owner">' +
                                'Owner' +
                            '</label>' +
                        '</div>' +
                    '</td>' +
                    '<td>' +
                        '<a href="#" style="color:red;" class="delete-this-permission" title="Delete">' +
                            '<i class="fa fa-trash-o"></i>' +
                        '</a>' +
                    '</td>' +
                '</tr>';

            $table.find('tbody').append(to_add);
            $table.find('tr.empty').addClass('hidden');
        }

        $table.on('click', '.delete-this-permission', function(e) {
            $(this).closest('tr').remove();

            if (!$table.find('tbody tr:not(.empty)').length) {
                $table.find('tr.empty').removeClass('hidden');
            }

            e.preventDefault();
        });

        $('.add-global-access').click(function(e) {
            addPerm('global');

            e.preventDefault();
        });

        /**
         * Add section
         */
        $('.do-add-section').click(function(e) {
            addPerm('section', $('#section-select').val());

            $('#addSectionModal').modal('hide');

            e.preventDefault();
        });

        /**
         * Add device
         */
        $('.do-add-device').click(function(e) {
            addPerm('device', $('#device-section-select').val(), $('#device-select').val());

            $('#addDeviceModal').modal('hide');

            e.preventDefault();
        });

        $('#device-section-select').change(function() {
            var html = '',
                items = deviceSections[$(this).val()];

            for (var i in items) {
                html += '<option value="' + i + '">' + items[i] + '</option>';
            }

            $('#device-select').html(html);
        }).change();

        $('input[type=radio][name=role]').click(function() {
            if ($(this).val() == '{{ App\User::ROLE_SYSADMIN }}') {
                $('#permissions-section').show();
            } else {
                $('#permissions-section').hide();
            }
        });
    });
</script>
@append
