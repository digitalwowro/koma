<div class="form-group">
    <div class="col-lg-offset-2 col-xs-10">
        <button type="submit" class="btn btn-success">{{ isset($user) ? 'Save' : 'Add' }}</button>
        <button class="btn btn-default" onclick="window.history.back(); return false;">Cancel</button>
    </div>
</div>
