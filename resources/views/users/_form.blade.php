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

@if (!isset($user) || isset($profile))
<div class="form-group">
    <label for="password" class="col-xs-2 control-label">Password</label>
    <div class="col-xs-10">
        {!! Form::password('password', [
            'id' => 'password',
            'class' => 'form-control',
            'style' => 'font-style:italic;',
            'placeholder' => 'leave blank if you don\'t want to change the password',
        ]) !!}
    </div>
</div>
@endif

@if (!isset($user) || auth()->id() != $user->id)
    <div class="form-group">
        <label class="col-xs-2 control-label">Role</label>
        <div class="col-xs-10">
            <div class="radio icheck">
                <label>
                    {!! Form::radio('role', App\User::ROLE_SYSADMIN, null, [
                        'required' => true,
                    ]) !!}
                    Sysadmin
                </label>
            </div>

            <div class="radio icheck">
                <label>
                    {!! Form::radio('role', App\User::ROLE_ADMIN, null, [
                        'required' => true,
                    ]) !!}
                    Admin
                </label>
            </div>

            <div class="radio icheck">
                <label>
                    {!! Form::radio('role', App\User::ROLE_SUPERADMIN, null, [
                        'required' => true,
                    ]) !!}
                    Superadmin
                </label>
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
                    <td>
                        @if ($permission->resource_type === $permission::RESOURCE_TYPE_DEVICE_SECTION)
                            <i class="fa fa-server"></i>

                            <a href="{{ route('device.index', $permission->resource->id) }}" target="_blank">
                                {{ $permission->resource->title }}
                            </a>
                        @elseif ($permission->resource_type === $permission::RESOURCE_TYPE_DEVICE)
                            <i class="fa fa-server"></i>

                            <a href="{{ route('device.index', $permission->resource->section_id) }}" target="_blank">
                                {{ $permission->resource->section->title }}
                            </a>

                            &gt;

                            <a href="{{ route('device.show', ['type' => $permission->resource->section_id, 'id' => $permission->resource->id]) }}" target="_blank">
                                {{ $permission->resource->present()->humanIdField }}
                            </a>
                        @elseif ($permission->resource_type === $permission::RESOURCE_TYPE_IP_CATEGORY)
                            <i class="fa fa-ellipsis-h"></i>

                            <a href="{{ route('ip.index', $permission->resource->id) }}" target="_blank">
                                {{ $permission->resource->title }}
                            </a>
                        @elseif ($permission->resource_type === $permission::RESOURCE_TYPE_IP_SUBNET)
                            <i class="fa fa-ellipsis-h"></i>

                            <a href="{{ route('ip.index', $permission->resource->category_id) }}" target="_blank">
                                {{ $permission->resource->category->title }}
                            </a>

                            &gt;

                            <a href="{{ route('ip.subnet', str_replace('/', '-', $permission->resource->subnet)) }}" target="_blank">
                                {{ $permission->resource->subnet }}
                            </a>
                        @endif

                        <input type="hidden" name="permissions[{{ $i }}][type]" value="{{ $permission->resource_type }}">
                        <input type="hidden" name="permissions[{{ $i }}][id]" value="{{ $permission->resource_id }}">
                    </td>

                    <td>
                        <div class="checkbox icheck pull-left" style="margin-right: 10px;">
                            <label>
                                {!! Form::checkbox("permissions[{$i}][level][]", $permission::GRANT_TYPE_READ, in_array($permission::GRANT_TYPE_READ, $permission->grant_type)) !!}
                                Read
                            </label>
                        </div>

                        <div class="checkbox icheck pull-left" style="margin-right: 10px;">
                            <label>
                                {!! Form::checkbox("permissions[{$i}][level][]", $permission::GRANT_TYPE_WRITE, in_array($permission::GRANT_TYPE_WRITE, $permission->grant_type)) !!}
                                Write
                            </label>
                        </div>

                        <div class="checkbox icheck pull-left" style="margin-right: 10px;">
                            <label>
                                {!! Form::checkbox("permissions[{$i}][level][]", $permission::GRANT_TYPE_DELETE, in_array($permission::GRANT_TYPE_DELETE, $permission->grant_type)) !!}
                                Delete
                            </label>
                        </div>

                        @if (in_array($permission->resource_type, [$permission::RESOURCE_TYPE_DEVICE_SECTION, $permission::RESOURCE_TYPE_IP_CATEGORY]))
                        <div class="checkbox icheck pull-left" style="margin-right: 10px;">
                            <label>
                                {!! Form::checkbox("permissions[{$i}][level][]", $permission::GRANT_TYPE_CREATE, in_array($permission::GRANT_TYPE_CREATE, $permission->grant_type)) !!}
                                Create
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
                        <a class="btn btn-primary" href="#addSectionModal" data-toggle="modal">
                            <i class="fa fa-server"></i>
                            Add Device Section
                        </a>

                        <a class="btn btn-primary" href="#addDeviceModal" data-toggle="modal">
                            <i class="fa fa-server"></i>
                            Add Device
                        </a>

                        <a class="btn btn-primary" href="#addIpSectionModal" data-toggle="modal">
                            <i class="fa fa-ellipsis-h"></i>
                            Add IP Category
                        </a>

                        <a class="btn btn-primary" href="#addIpSubnetModal" data-toggle="modal">
                           <i class="fa fa-ellipsis-h"></i>
                            Add IP Subnet
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
                <h4>Section</h4>

                <select id="device-section-select" class="form-control">
                    @foreach($deviceSections as $section)
                        <option value="{{ $section->id }}">{{ $section->title }}</option>
                    @endforeach
                </select>

                <h4>Device</h4>

                <select id="device-select" class="form-control"></select>
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
                <h4>Section</h4>
                <select id="section-select" class="form-control">
                    @foreach($deviceSections as $section)
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

<div class="modal fade" id="addIpSubnetModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add Permission for IP Subnet</h4>
            </div>
            <div class="modal-body">
                <h4>IP Category</h4>

                <select id="ip-subnet-select" class="form-control">
                    @foreach($ipCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                    @endforeach
                </select>

                <h4>Subnet</h4>

                <select id="subnet-select" class="form-control"></select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary do-add-subnet">Add</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addIpSectionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add Permission for IP Category</h4>
            </div>
            <div class="modal-body">
                <h4>Category</h4>
                <select id="category-select" class="form-control">
                    @foreach($ipCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary do-add-ip-category">Add</button>
            </div>
        </div>
    </div>
</div>

@section('footer')
<script>
    $(document).ready(function() {
        var $table = $('table.table-permissions');
        var deviceSections = {!! App\Device::with('section')->get()->map(function($item) { $item->title = $item->present()->humanIdField; return $item; })->keyBy('id')->groupBy('section_id')->map(function($item) { return $item->pluck('title', 'id'); })->toJson() !!};
        var subnets = {!! App\IpAddress::allSubnets()->toJson() !!};

        function getSectionTitle(section_id) {
            var sections = {!! App\DeviceSection::all()
                ->pluck('title', 'id')
                ->toJson() !!};

            return sections[section_id];
        }

        function getDeviceTitle(device_id) {
            var devices = {!! App\Device::with('section')
                ->get()
                ->keyBy('id')
                ->map(function($item) {
                    return $item->present()->humanIdField;
                })
                ->toJson() !!};

            return devices[device_id];
        }

        function getIpCategoryTitle(category_id) {
            var categories = {!! App\IpCategory::all()
                ->pluck('title', 'id')
                ->toJson() !!};

            return categories[category_id];
        }

        function getIpSubnetTitle(subnet_id) {
            var subnets = {!! App\IpAddress::selectRaw('min(id) as id, subnet')
                ->groupBy('subnet')
                ->pluck('subnet', 'id')
                ->toJson() !!};

            return subnets[subnet_id];
        }

        function addPerm(type, section_id, device_id) {
            var to_add = '<tr><td>',
                nextId = $table.find('tbody tr:not(.empty)').length
                    ? (parseInt($table.find('tbody tr:last [name^=permissions]').first().attr('name').split('[')[1].replace(/[^0-9]/g, '')) + 1)
                    : 0;

            if (type === {{ App\Permission::RESOURCE_TYPE_DEVICE_SECTION }}) {
                to_add = to_add + '<i class="fa fa-server"></i> ' +
                    '<a href="' + '{{ route('device.index', '_SID_') }}'.replace('_SID_', section_id) + '" target="_blank"> ' +
                        getSectionTitle(section_id) +
                    '</a>' +

                    '<input type="hidden" name="permissions[' + nextId + '][type]" value="{{ App\Permission::RESOURCE_TYPE_DEVICE_SECTION }}">' +
                    '<input type="hidden" name="permissions[' + nextId + '][id]" value="' + section_id + '">';
            } else if (type === {{ App\Permission::RESOURCE_TYPE_DEVICE }}) {
                to_add = to_add + '<i class="fa fa-server"></i> ' +
                    '<a href="' + '{{ route('device.index', '_SID_') }}'.replace('_SID_', section_id) + '" target="_blank"> ' +
                        getSectionTitle(section_id) +
                    '</a>' +
                    ' &gt; ' +
                    '<a href="' + '{{ route('device.show', ['type' => '_SID_', 'id' => '_DID_']) }}'.replace('_SID_', section_id).replace('_DID_', device_id) + '" target="_blank"> ' +
                        getDeviceTitle(device_id) +
                    '</a>' +
                    '<input type="hidden" name="permissions[' + nextId + '][type]" value="{{ App\Permission::RESOURCE_TYPE_DEVICE }}">' +
                    '<input type="hidden" name="permissions[' + nextId + '][id]" value="' + device_id + '">';
            } else if (type === {{ App\Permission::RESOURCE_TYPE_IP_CATEGORY }}) {
                to_add = to_add + '<i class="fa fa-ellipsis-h"></i> ' +
                    '<a href="' + '{{ route('ip.index', '_SID_') }}'.replace('_SID_', section_id) + '" target="_blank">' +
                        getIpCategoryTitle(section_id) +
                    '</a>' +
                    '<input type="hidden" name="permissions[' + nextId + '][type]" value="{{ App\Permission::RESOURCE_TYPE_IP_CATEGORY }}">' +
                    '<input type="hidden" name="permissions[' + nextId + '][id]" value="' + section_id + '">';
            } else if (type === {{ App\Permission::RESOURCE_TYPE_IP_SUBNET }}) {
                to_add = to_add + '<i class="fa fa-ellipsis-h"></i> ' +
                    '<a href="' + '{{ route('ip.index', '_SID_') }}'.replace('_SID_', section_id) + '" target="_blank">' +
                        getIpCategoryTitle(section_id) +
                    '</a>' +
                    ' &gt; ' +
                    '<a href="' + '{{ route('ip.subnet', '_SID_') }}'.replace('_SID_', getIpSubnetTitle(device_id).replace('/', '-'))  + '" target="_blank">' +
                        getIpSubnetTitle(device_id) +
                    '</a>' +
                    '<input type="hidden" name="permissions[' + nextId + '][type]" value="{{ App\Permission::RESOURCE_TYPE_IP_SUBNET }}">' +
                    '<input type="hidden" name="permissions[' + nextId + '][id]" value="' + device_id + '">';
            }

            var hasCreate = [{{ App\Permission::RESOURCE_TYPE_DEVICE_SECTION }}, {{ App\Permission::RESOURCE_TYPE_IP_CATEGORY }}].includes(parseInt(type));

            to_add = to_add +
                '</td><td>' +
                    '<div class="checkbox icheck pull-left" style="margin-right: 10px;">' +
                        '<label>' +
                            '<input type="checkbox" name="permissions[' + nextId + '][level][]" value="{{ App\Permission::GRANT_TYPE_READ }}" checked> Read' +
                        '</label>' +
                    '</div>' +

                    '<div class="checkbox icheck pull-left" style="margin-right: 10px;">' +
                        '<label>' +
                            '<input type="checkbox" name="permissions[' + nextId + '][level][]" value="{{ App\Permission::GRANT_TYPE_WRITE }}"> Write' +
                        '</label>' +
                    '</div>' +

                    '<div class="checkbox icheck pull-left" style="margin-right: 10px;">' +
                        '<label>' +
                            '<input type="checkbox" name="permissions[' + nextId + '][level][]" value="{{ App\Permission::GRANT_TYPE_DELETE }}"> Delete' +
                        '</label>' +
                    '</div>' +

                    '<div class="checkbox icheck pull-left' + (hasCreate ? '' : ' hidden') + '" style="margin-right: 10px;">' +
                        '<label>' +
                            '<input type="checkbox" name="permissions[' + nextId + '][level][]" value="{{ App\Permission::GRANT_TYPE_CREATE }}"> Create' +
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

            bindIcheck();
        }

        $table.on('click', '.delete-this-permission', function(e) {
            $(this).closest('tr').remove();

            if (!$table.find('tbody tr:not(.empty)').length) {
                $table.find('tr.empty').removeClass('hidden');
            }

            e.preventDefault();
        });

        /** Add section **/
        $('.do-add-section').click(function(e) {
            addPerm({{ App\Permission::RESOURCE_TYPE_DEVICE_SECTION }}, $('#section-select').val());

            $('#addSectionModal').modal('hide');

            e.preventDefault();
        });

        /** Add device **/
        $('.do-add-device').click(function(e) {
            addPerm({{ App\Permission::RESOURCE_TYPE_DEVICE }}, $('#device-section-select').val(), $('#device-select').val());

            $('#addDeviceModal').modal('hide');

            e.preventDefault();
        });

        /** Add IP Category **/
        $('.do-add-ip-category').click(function(e) {
            addPerm({{ App\Permission::RESOURCE_TYPE_IP_CATEGORY }}, $('#category-select').val());

            $('#addIpSectionModal').modal('hide');

            e.preventDefault();
        });

        /** Add IP Subnet **/
        $('.do-add-subnet').click(function(e) {
            addPerm({{ App\Permission::RESOURCE_TYPE_IP_SUBNET }}, $('#ip-subnet-select').val(), $('#subnet-select').val());

            $('#addIpSubnetModal').modal('hide');

            e.preventDefault();
        });

        $('#device-section-select').change(function() {
            var html = '',
                items = deviceSections[$(this).val()];

            for (var i in items) {
                html += '<option value="' + i + '">' + items[i] + '</option>';
            }

            $('#device-select').html(html);

            if (html) {
                $('#addDeviceModal .do-add-device').removeAttr('disabled');
            } else {
                $('#addDeviceModal .do-add-device').attr('disabled', true);
            }
        }).change();

        $('#ip-subnet-select').change(function() {
            var html = '',
                category = $(this).val();

            $.each(subnets, function(i, subnet) {
                if (parseInt(subnet.category_id) === parseInt(category)) {
                    html += '<option value="' + subnet.id + '">' + subnet.subnet + '</option>';
                }
            });

            $('#subnet-select').html(html);

            if (html) {
                $('#addIpSubnetModal .do-add-subnet').removeAttr('disabled');
            } else {
                $('#addIpSubnetModal .do-add-subnet').attr('disabled', true);
            }
        }).change();

        $('input[type=checkbox][name=role]').click(function() {
            if ($(this).val() == '{{ App\User::ROLE_SYSADMIN }}') {
                $('#permissions-section').show();
            } else {
                $('#permissions-section').hide();
            }
        });
    });
</script>
@append
