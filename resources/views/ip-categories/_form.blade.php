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
    <div class="col-lg-10 col-lg-offset-2">
        <button type="submit" class="btn btn-primary">{{ isset($ipCategory) ? 'Save' : 'Add' }}</button>
        <button type="reset" class="btn btn-default">Reset</button>
        <a href="{{ route('ip-categories.index') }}" class="btn btn-default">Cancel</a>
    </div>
</div>
