<ul id="ip-list">
    <li class="{{ isset($device) && $device->ips->count() ? 'hidden' : '' }}" style="font-size:.9em; color:grey; font-style: italic;">- no IP address currently assigned -</li>

    @if (isset($device))
        @foreach ($device->ips as $ip)
        <li>
            {{ $ip->ip }}
            <a href="#" data-action="unassign-ip" data-unassign-id="{{ $ip->id }}"><i class="fa fa-trash-o"></i></a>
            <input type="hidden" name="ips[]" value="{{ $ip->isCustom() ? $ip->ip : $ip->id }}">
        </li>
        @endforeach
    @endif
</ul>

<a href="#assign-now" data-toggle="modal" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Assign from existing class</a>
<a href="#add-custom" data-toggle="modal" class="btn btn-default btn-xs"><i class="fa fa-plus"></i> Add custom</a>

<div class="modal fade" id="assign-now" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Assign IP Addresses To Device</h4>
            </div>
            <div class="modal-body" style="position:relative;">
                <div class="row" style="margin-top:15px;">
                    <div class="col-xs-6">
                        <label>Search By Subnet</label>
                        <select id="subnet-select" style="width:100%;">
                        @foreach(App\IpAddress::getSubnetsFor() as $subnet)
                            <option value="{{ $subnet->subnet }}">{{ $subnet->category->title }}: {{ $subnet->subnet }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="col-xs-6">
                        <label>IP Addresses To Assign</label>

                        <select id="ip-select" multiple style="width:100%;">
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" data-action="assign-ip">Add</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add-custom" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add Custom IP Addresses To Device</h4>
            </div>
            <div class="modal-body" style="position:relative;">
                <div class="row" style="margin-top:15px;">
                    <div class="col-xs-12">
                        <label>IP Addresses To Add</label>
                        <input type="text" id="custom-ip" class="form-control">
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" data-action="add-custom">Add</button>
            </div>
        </div>
    </div>
</div>

@section('footer')
    <script>
        $(document).ready(function() {
            $('#ip-select').select2();

            $('#subnet-select').select2().change(function() {
                var val     = $(this).val() ? $(this).val().replace('/', '-') : $(this).val(),
                    url     = '{{ route('ip.subnet-list', '_SUB_') }}'.replace('_SUB_', val),
                    $select = $('#ip-select');

                $select.find('option').remove();
                $select.trigger('change');

                $.get(url, function(r) {
                    $.each(r, function(i, ip) {
                        $select.append('<option value="' + ip.id + '">' + ip.ip + '</option>');
                    });

                    $select.trigger('change');
                });
            }).trigger('change');

            $('[data-action="assign-ip"]').click(function() {
                var $list = $('#ip-list');

                $('#ip-select option:selected').each(function() {
                    var val  = $(this).val(),
                        text = $(this).text();

                    $list.append(
                        '<li>' +
                            text +
                            ' <a href="#" data-action="unassign-ip" data-unassign-id="' + val + '"><i class="fa fa-trash-o"></i></a>' +
                            '<input type="hidden" name="ips[]" value="' + val + '">' +
                        '</li>'
                    );
                });

                if ($list.find('li').length > 1) {
                    $list.find('li:first').addClass('hidden');
                }

                $('#assign-now').modal('hide');

                return false;
            });

            $('#custom-ip').keydown(function(e) {
                if (e.keyCode == 13) {
                    $('[data-action="add-custom"]').click();

                    e.preventDefault();
                }
            });

            $('[data-action="add-custom"]').click(function() {
                var $list = $('#ip-list'),
                    ip = $('#custom-ip').val();

                $list.append(
                        '<li>' +
                            ip +
                            ' <a href="#" data-action="unassign-ip" data-unassign-id="' + ip + '"><i class="fa fa-trash-o"></i></a>' +
                            '<input type="hidden" name="ips[]" value="' + ip + '">' +
                        '</li>');

                $list.find('li:first').addClass('hidden');

                $('#add-custom').modal('hide');

                return false;
            });

            $('#ip-list').on('click', '[data-action="unassign-ip"]', function() {
                var $list = $('#ip-list');

                $(this).closest('li').remove();

                if ($list.find('li').length == 1) {
                    $list.find('li').removeClass('hidden');
                }

                return false;
            });
        });
    </script>
@append
