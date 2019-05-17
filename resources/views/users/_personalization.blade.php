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
        <div class="checkbox-nice">
            {!! Form::checkbox('device_sections_customize', 1, isset(auth()->user()->profile['device_sections']), [
                'id' => 'device_sections_customize',
            ]) !!}

            <label for="device_sections_customize">
                Show/hide device sections from sidebar
            </label>
        </div>

        <br>

        <div id="device_sections_list" class="well{{ isset(auth()->user()->profile['device_sections']) ? '' : ' hidden' }}">
            <p>Select device sections you want to see in sidebar</p>

            @foreach ($deviceSections as $deviceSection)
                <div class="checkbox-nice">
                    {!! Form::checkbox("device_sections[]", $deviceSection->id, auth()->user()->deviceSectionVisible($deviceSection->id), [
                        'id' => "device_section_{$deviceSection->id}",
                    ]) !!}

                    <label for="device_section_{{ $deviceSection->id }}">
                        {{ $deviceSection->title }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>

@section('footer')
    <script>
        $('#device_sections_customize').click(function() {
            $('#device_sections_list').toggleClass('hidden', !$(this).is(':checked'));
        });
    </script>
@append
