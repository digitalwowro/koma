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
    <label for="title" class="col-lg-2 control-label">Field Bindings</label>

    <div class="col-lg-10">
        <table class="table table-striped table-hover table-bordered table-field-options">
            <thead>
                <tr>
                    <th>Device Type</th>
                    <th>Bind To Field</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deviceSections as $deviceSection)
                <tr>
                    <td>
                        {{ $deviceSection->title }}
                    </td>
                    <td>
                        {!! Form::select('bindings[' . $deviceSection->id . ']', $deviceSection->getFieldNames(), null, [
                            'class' => 'form-control',
                        ]) !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="form-group">
    <div class="col-lg-10 col-lg-offset-2">
        <button type="submit" class="btn btn-primary">{{ isset($field) ? 'Save' : 'Add' }}</button>
        <button type="reset" class="btn btn-default">Reset</button>
        <a href="{{ route('ip-fields.index') }}" class="btn btn-default">Cancel</a>
    </div>
</div>
