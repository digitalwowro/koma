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
        <a href="http://fontawesome.io/icons/" target="_blank" style="font-style:italic; opacity:.6;">See complete list of icons here</a>
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
                        @include('_field-options', compact('field'))
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
        <button type="reset" class="btn btn-default">Reset</button>
        <a href="{{ route('device-sections.index') }}" class="btn btn-default">Cancel</a>
    </div>
</div>

@section('footer')
    <script>
        $(document).ready(function() {
            function refreshTr($tr) {
                var params = {
                    type: $tr.find('select.field-type').val(),
                    index: $tr.prevAll().length
                };

                $tr.find('td.field-options').html('<center><img src="{{ asset('img/loading.gif') }}"></center>');

                $.post('{{ route('device-sections.get-options') }}', params, function(r) {
                    $tr.find('td.field-options').html(r);
                });
            }

            function refreshAllIndexes() {
                $('table.table-field-options tbody tr').each(function(i) {
                    var $this = $(this);

                    $this.find('.field-name').attr('name', 'fields[' + i + '][name]');
                    $this.find('.field-type').attr('name', 'fields[' + i + '][type]');
                    $this.find('.field-options').find('[name^="fields\["]').each(function() {
                        var name = $(this).attr('name');

                        $(this).attr('name', name.substr(0,7) + i + name.substr(8));
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

            $('table.table-field-options').on('click', '.delete-field', function() {
                if (confirm('Are you sure you want to delete this field?')) {
                    $(this).closest('tr').remove();
                    refreshAllIndexes();
                }
            });
    });
    </script>
@append
