<div class="form-group">
    <div class="col-lg-12" style="margin-top:20px; margin-bottom:20px; margin-left:170px;">
        <button type="submit" class="btn btn-primary">{{ isset($device) ? 'Save' : 'Add' }}</button>
        <button type="reset" class="btn btn-default">Reset</button>
        <a href="{{ route('devices.index', $deviceSection->id) }}" class="btn btn-default">Cancel</a>
    </div>
</div>
