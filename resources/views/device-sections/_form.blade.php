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
                    <th>Field Name</th>
                    <th>Field Type</th>
                    <th>Field Options</th>
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
                    <td colspan="3" style="text-align: center;">
                        <a href="#" id="add-new-field">
                            <i class="fa fa-plus"></i>
                            Add a new field
                        </a>
                    </td>
                </tr>
                <tr class="hidden">
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
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="form-group">
    <div class="col-lg-10 col-lg-offset-2">
        <button type="submit" class="btn btn-primary">{{ isset($deviceSection) ? 'Save' : 'Add' }}</button>
    </div>
</div>

@section('footer')
    <script>
        $(document).ready(function() {
            function refreshTr($tr) {
                var params = {
                    type: $tr.find('select.field-type').val(),
                    index: $tr.prevAll().length + 1
                };

                $tr.find('td.field-options').html('<center><img src="{{ asset('img/loading.gif') }}"></center>');

                $.post('{{ route('device-sections.get-options') }}', params, function(r) {
                    $tr.find('td.field-options').html(r);
                });
            }

            $('#add-new-field').click(function(e) {
                e.preventDefault();

                var $this = $(this),
                    $table = $this.closest('table'),
                    template = $table.find('tfoot tr.hidden').html();

                $table.find('tbody').append('<tr>' + template + '</tr>');

                var $tr = $table.find('tbody tr:last'),
                    nth = $tr.parent().find('tr').length;

                $tr.find('.field-name').attr('name', 'fields[' + nth + '][name]');
                $tr.find('.field-type').attr('name', 'fields[' + nth + '][type]');
                refreshTr($tr);
            });

            $('table.table-field-options').on('change', 'select.field-type', function() {
                refreshTr($(this).closest('tr'));
            });
        });
    </script>
@append
