<div class="box box-primary">
    <div class="box-body">
        <button type="submit" class="btn btn-primary">{{ isset($device) ? 'Save' : 'Add' }}</button>
        <a href="{{ route('devices.index', $deviceSection->id) }}" class="btn btn-default">Cancel</a>
    </div>
</div>
