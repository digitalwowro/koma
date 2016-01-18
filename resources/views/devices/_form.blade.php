@foreach ($deviceSection->fields as $field)
    {!! $field->render(isset($device->data[$field->getInputName()]) ? $device->data[$field->getInputName()] : null) !!}
@endforeach

<div class="form-group">
    <div class="col-lg-10 col-lg-offset-2">
        <button type="submit" class="btn btn-primary">{{ isset($device) ? 'Save' : 'Add' }}</button>
        <button type="reset" class="btn btn-default">Reset</button>
        <a href="{{ route('devices.index', $deviceSection->id) }}" class="btn btn-default">Cancel</a>
    </div>
</div>
