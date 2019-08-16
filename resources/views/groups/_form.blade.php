<div class="form-group">
    <label for="title" class="col-lg-2 control-label">Group Name</label>

    <div class="col-lg-10">
        {!! Form::text('name', null, [
            'class' => 'form-control',
            'id' => 'name',
            'placeholder' => 'Name',
            'required' => true,
        ]) !!}
    </div>
</div>

<div class="form-group">
    <div class="col-lg-10 col-lg-offset-2">
        <button type="submit" class="btn btn-primary">{{ isset($group) ? 'Save' : 'Add' }}</button>
        <a href="{{ route('groups.index') }}" class="btn btn-default">Cancel</a>
    </div>
</div>
