<div class="row">
    <div class="col-lg-12">
        <div class="main-box clearfix">
            <div class="form-group">
                <div class="col-lg-12" style="padding-top:20px; padding-bottom:20px; padding-left:170px;">
                    <button type="submit" class="btn btn-primary">{{ isset($device) ? 'Save' : 'Add' }}</button>
                    <a href="{{ route('devices.index', $deviceSection->id) }}" class="btn btn-default">Cancel</a>
                </div>
            </div>

        </div>
    </div>
</div>
