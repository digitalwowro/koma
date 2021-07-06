<div class="form-group">
    <label for="devices_per_page" class="col-xs-2 control-label">Default devices per page</label>
    <div class="col-xs-10">
        {!! Form::select('devices_per_page', [10 => 10, 25 => 25, 50 => 50, 100 => 100], $user->profile['devices_per_page'] ?? null, [
            'id' => 'devices_per_page',
            'class' => 'form-control',
        ]) !!}
    </div>
</div>
