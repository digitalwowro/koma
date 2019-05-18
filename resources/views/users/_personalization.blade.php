<div class="form-group">
    <label for="devices_per_page" class="col-xs-2 control-label">Default devices per page</label>
    <div class="col-xs-10">
        {!! Form::select('devices_per_page', [10 => 10, 25 => 25, 50 => 50, 100 => 100], $user->profile['devices_per_page'] ?? null, [
            'id' => 'devices_per_page',
            'class' => 'form-control',
        ]) !!}
    </div>
</div>

<div class="form-group">
    <label for="devices_per_page" class="col-xs-2 control-label">Favorite device sections</label>
    <div class="col-xs-10">
        <div class="checkbox icheck">
            <label>
                {!! Form::checkbox('device_sections_customize', 1, isset(auth()->user()->profile['device_sections']), [
                    'id' => 'device_sections_customize',
                ]) !!}

                Show/hide device sections from sidebar
            </label>
        </div>

        <br>

        <div id="device_sections_list" class="well well-inline{{ isset(auth()->user()->profile['device_sections']) ? '' : ' hidden' }}">
            <p>Select device sections you want to see in sidebar</p>

            @foreach ($deviceSections as $deviceSection)
                @can('list', $deviceSection)
                    <div class="checkbox icheck">
                        <label>
                            {!! Form::checkbox("device_sections[]", $deviceSection->id, auth()->user()->deviceSectionVisible($deviceSection->id)) !!}

                            {{ $deviceSection->title }}
                        </label>
                    </div>
                @endcan
            @endforeach
        </div>
    </div>
</div>

@section('footer')
    <script>
        $('#device_sections_customize').on('ifToggled', function() {
            $('#device_sections_list').toggleClass('hidden', !$(this).is(':checked'));
        });
    </script>
@append
