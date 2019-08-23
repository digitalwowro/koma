<div class="form-group">
    <label for="subnet" class="col-lg-2 control-label">Subnet</label>

    <div class="col-lg-10">
        {!! Form::text('subnet', null, [
            'class' => 'form-control',
            'id' => 'subnet',
            'placeholder' => '127.0.0.1/32',
            'required' => empty($ip),
            'disabled' => isset($ip),
        ]) !!}
    </div>
</div>

<div class="form-group">
    <label for="name" class="col-lg-2 control-label">Name (optional)</label>

    <div class="col-lg-10">
        {!! Form::text('name', $name ?? null, [
            'class' => 'form-control',
            'id' => 'name',
            'placeholder' => 'My first IP subnet',
        ]) !!}
    </div>
</div>

<div class="form-group">
    <label for="reserved" class="col-lg-2 control-label">Reserved IPs</label>

    <div class="col-lg-10">
        @if (isset($allInSubnet, $allReserved))
        {!! Form::select('reserved[]', $allInSubnet, $allReserved, [
            'class' => 'form-control select2',
            'id' => 'reserved',
            'multiple' => true,
        ]) !!}
        @else
            <span style="display: block; line-height: 30px; color: gray; font-style: italic;">Please save this subnet first before adding reserved IPs</span>
        @endif
    </div>
</div>

<div class="form-group">
    <div class="col-lg-10 col-lg-offset-2">
        <button type="submit" class="btn btn-primary">{{ isset($ip) ? 'Save' : 'Add' }}</button>
        <a href="{{ route('ip.index',  $category) }}" class="btn btn-default">Cancel</a>
    </div>
</div>

@section('footer')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@append
