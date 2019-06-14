<div class="form-group">
    <label for="title" class="col-lg-2 control-label">Title</label>

    <div class="col-lg-10">
        {!! Form::text('title', null, [
            'class'       => 'form-control',
            'id'          => 'title',
            'placeholder' => 'Title',
            'required'    => true,
        ]) !!}
    </div>
</div>

<div class="form-group">
    <label for="title" class="col-lg-2 control-label">
        Icon
        <br>
        <a href="https://fontawesome.com/v4.7.0/icons/" target="_blank" style="font-style:italic; opacity:.6;">See complete list of icons here</a>
    </label>

    <div class="col-lg-10">
        {!! Form::text('icon', null, [
            'class'       => 'form-control',
            'id'          => 'icon',
            'placeholder' => 'Enter icon name',
        ]) !!}
    </div>
</div>

<div class="form-group">
    <label for="title" class="col-lg-2 control-label">Categories</label>

    <div class="col-lg-10">
        <table class="table table-striped table-hover table-bordered table-categories">
            <thead>
            <tr>
                <th style="text-align: center;">#</th>
                <th>Name</th>
                <th>Del</th>
            </tr>
            </thead>
            <tbody>
                @if (isset($deviceSection) && !empty($deviceSection->categories))
                    @foreach ($deviceSection->categories as $key => $label)
                        <tr>
                            <td style="width:1px; white-space:nowrap; text-align: center;">
                                <i class="fa fa-reorder"></i>
                            </td>

                            <td>
                                {{ $label }}

                                <input type="hidden" name="categories[]" value="{{ $label }}">
                                <input type="hidden" name="categoryid[]" value="{{ $key }}">
                            </td>

                            <td style="width:1px; white-space:nowrap; text-align: center;">
                                <a href="#" title="Delete this category" class="delete-category">
                                    <i class="fa fa-trash-o"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 20px 0;">
                            No categories have been defined
                        </td>
                    </tr>
                @endif
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: center;">
                        <a href="#" id="add-new-category">
                            <i class="fa fa-plus"></i>
                            Add a new category
                        </a>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="form-group">
    <label for="title" class="col-lg-2 control-label">Fields</label>

    <div class="col-lg-10">
        <table class="table table-striped table-hover table-bordered table-field-options">
            <thead>
                <tr>
                    <th style="text-align: center;">#</th>
                    <th>Field Name</th>
                    <th>Field Type</th>
                    <th>Field Options</th>
                    <th>Del</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($deviceSection))
                    @foreach ($deviceSection->fields as $i => $field)
                        @include('partials._field-options', compact('field'))
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: center;">
                        <a href="#" id="add-new-field">
                            <i class="fa fa-plus"></i>
                            Add a new field
                        </a>
                    </td>
                </tr>
                <tr class="hidden">
                    <td style="width:1px; white-space:nowrap; text-align: center;">
                        <i class="fa fa-reorder"></i>
                    </td>
                    <td>
                        <input type="hidden" class="field-key">
                        <input type="text" class="field-name form-control">
                    </td>
                    <td>
                        <select class="form-control field-type">
                            @foreach (App\Fields\Factory::getFieldTypes() as $fieldType)
                            <option value="{{ $fieldType }}">{{ $fieldType }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="field-options">
                        ...
                    </td>
                    <td style="width:1px; white-space:nowrap; text-align: center;">
                        <a href="#" title="Delete this field" class="delete-field">
                            <i class="fa fa-trash-o"></i>
                        </a>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="form-group">
    <div class="col-lg-10 col-lg-offset-2">
        <button type="submit" class="btn btn-primary">{{ isset($deviceSection) ? 'Save' : 'Add' }}</button>
        <a href="{{ route('device-section.index') }}" class="btn btn-default">Cancel</a>
    </div>
</div>

@section('footer')
    <script>
        $(document).ready(function() {
            {{------------------
            --   CATEGORIES   --
            ------------------}}
            $('table.table-categories tbody').sortable({
                handle: '.fa-reorder',
                distance: 15,
                items: 'tr',
                forcePlaceholderSize: true,
                placeholder: 'ui-state-highlight',
                start: function(event, ui) {
                    ui.placeholder.html('<td style="height:30px; background-color:#d1eeff;" colspan="5">&nbsp;</td>');
                }
            });

            $('#add-new-category').click(function(e) {
                e.preventDefault();

                var $this = $(this),
                    $tbody = $this.closest('table').find('tbody'),
                    name = prompt('Enter category name'),
                    chars = '0123456789abcdefghijklmnopqrstuvwxyz',
                    key = '';

                for (var i = 0; i < 8; i++) {
                    key += chars[Math.floor(Math.random() * chars.length)];
                }

                if (name) {
                    name = name.replace('"', '&quot;');

                    var $tr = $tbody.find('tr'),
                        html = '<tr>' +
                        '<td style="width:1px; white-space:nowrap; text-align: center;">' +
                            '<i class="fa fa-reorder"></i>' +
                        '</td>' +

                        '<td>' +
                            name +

                            '<input type="hidden" name="categories[]" value="' + name + '">' +
                            '<input type="hidden" name="categoryid[]" value="' + key + '">' +
                        '</td>' +

                        '<td style="width:1px; white-space:nowrap; text-align: center;">' +
                            '<a href="#" title="Delete this category" class="delete-category">' +
                            '<i class="fa fa-trash-o"></i>' +
                            '</a>' +
                        '</td>' +
                    '</tr>';

                    if ($tr.length === 1 && $tr.find('td').length === 1) {
                        $tbody.html(html);
                    } else {
                        $tbody.append(html);
                    }
                }
            });

            $('table.table-categories').on('click', '.delete-category', function(e) {
                var $this = $(this),
                    $tbody = $this.closest('tbody');

                $this.closest('tr').remove();

                if (!$tbody.find('tr').length) {
                    $tbody.html('' +
                        '<tr>' +
                            '<td colspan="3" style="text-align: center; padding: 20px 0;">' +
                                'No categories have been defined' +
                            '</td>' +
                        '</tr>'
                    );
                }

                e.preventDefault();
            });

            {{--------------
            --   FIELDS   --
            --------------}}
            function refreshTr($tr) {
                var params = {
                    type: $tr.find('select.field-type').val(),
                    index: $tr.prevAll().length
                };

                $tr.find('td.field-options').html('<center><img src="{{ asset('img/loading.gif') }}"></center>');

                $.post('{{ route('device-section.get-options') }}', params, function(r) {
                    $tr.find('td.field-options').html(r);
                    bindIcheck();
                });
            }

            function makeid() {
                var text = "";
                var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

                for( var i=0; i < 24; i++ )
                    text += possible.charAt(Math.floor(Math.random() * possible.length));

                return text;
            }

            function refreshAllIndexes() {
                $('table.table-field-options tbody tr').each(function(i) {
                    var $this = $(this),
                        $key = $this.find('.field-key');

                    if ($key.val() == '') {
                        $key.val(makeid());
                    }

                    $key.attr('name', 'fields[' + i + '][key]');
                    $this.find('.field-name').attr('name', 'fields[' + i + '][name]');
                    $this.find('.field-type').attr('name', 'fields[' + i + '][type]');
                    $this.find('.field-options').find('[name^="fields\["]').each(function() {
                        var name = $(this).attr('name'),
                            new_name = name.replace(/^fields\[\d+\]/, 'fields[' + i + ']');

                        $(this).attr('name', new_name);
                    });
                });
            }

            $('#add-new-field').click(function(e) {
                e.preventDefault();

                var $this = $(this),
                    $table = $this.closest('table'),
                    template = $table.find('tfoot tr.hidden').html();

                $table.find('tbody').append('<tr>' + template + '</tr>');

                var $tr = $table.find('tbody tr:last');

                refreshAllIndexes();
                refreshTr($tr);
            });

            $('table.table-field-options').on('change', 'select.field-type', function() {
                refreshTr($(this).closest('tr'));
            });

            $('table.table-field-options tbody').sortable({
                handle: '.fa-reorder',
                distance: 15,
                items: 'tr',
                forcePlaceholderSize: true,
                placeholder: 'ui-state-highlight',
                start: function(event, ui) {
                    ui.placeholder.html('<td style="height:30px; background-color:#d1eeff;" colspan="5">&nbsp;</td>');
                },
                stop: function() {
                    refreshAllIndexes();
                }
            });

            $('table.table-field-options').on('click', '.delete-field', function(e) {
                if (confirm('Are you sure you want to delete this field?')) {
                    $(this).closest('tr').remove();
                    refreshAllIndexes();
                }

                e.preventDefault();
            });
    });
    </script>
@append
