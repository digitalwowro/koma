<tr>
    <td style="width:1px; white-space:nowrap; text-align: center;">
        <i class="fa fa-reorder"></i>
    </td>
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
    <td class="field-options">
        {!! $field->renderOptions($i) !!}
    </td>
    <td style="width:1px; white-space:nowrap; text-align: center;">
        <a href="#" title="Delete this field" class="delete-field">
            <i class="fa fa-trash-o"></i>
        </a>
    </td>
</tr>
