@foreach ($deviceSection->fields as $field)
    {!! $field->render() !!}
@endforeach

<div class="form-group">
    <div class="col-lg-10 col-lg-offset-2">
        <button type="submit" class="btn btn-primary">{{ isset($device) ? 'Save' : 'Add' }}</button>
    </div>
</div>
