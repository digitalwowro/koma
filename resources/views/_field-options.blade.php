<tr>
    <td>
        <input type="text" class="field-name form-control" name="fields[{{ $i }}][name]" value="{{ $field->getName() }}">
    </td>
    <td>
        <select class="form-control field-type" name="fields[{{ $i }}][type]">
            @foreach (App\Fields\Factory::getFieldTypes() as $fieldType)
            <option value="{{ $fieldType }}"{{ $field->getType() == $fieldType ? ' selected' : '' }}>{{ $fieldType }}</option>
            @endforeach
        </select>
    </td>
    <td>
        {{ $field->getOptions() }}
    </td>
</tr>
