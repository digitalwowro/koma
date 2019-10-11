@if (count($deviceSection->categories))
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Category</h3>
        </div>

        <div class="box-body">
            {!! Form::select('category_id', $deviceSection->present()->categorySelector, request()->input('category'), [
                'class' => 'form-control',
            ]) !!}
        </div>
    </div>
@endif
