<div class="form-group">
    <label for="title" class="col-lg-2 control-label">Title</label>

    <div class="col-lg-10">
        {!! Form::text('title', null, [
            'class'       => 'form-control',
            'id'          => 'title',
            'placeholder' => 'Title',
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
        <table class="table table-striped table-hover table-bordered">
            <thead>
                <tr>
                    <th>Field Name</th>
                    <th>Field Type</th>
                    <th>Field Options</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($deviceSection))
                    @foreach ($deviceSection->fields as $field)
                        <tr>
                            <td>
                                {{ $field['name'] }}
                            </td>
                            <td>
                                {{ $field['type'] }}
                            </td>
                            <td>
                                {{ $field['options'] }}
                            </td>
                        </tr>
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
                        <input type="text" class="form-control">
                    </td>
                    <td>
                        <select class="form-control">
                            <option>A</option>
                        </select>
                    </td>
                    <td>
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
            $('#add-new-field').click(function(e) {
                e.preventDefault();

                var $this = $(this),
                    $table = $this.closest('table'),
                    template = $table.find('tfoot tr.hidden').html();

                $table.find('tbody').append('<tr>' + template + '</tr>');
            });
        });
    </script>
@append
