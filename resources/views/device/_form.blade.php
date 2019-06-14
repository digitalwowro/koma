@foreach ($deviceSection->fields as $field)
    {!! $field->render(isset($device->data[$field->getInputName()]) ? $device->data[$field->getInputName()] : null) !!}
@endforeach
