@if (count($deviceSection->categories))
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <header class="main-box-header clearfix">
                    <h2 class="pull-left">Category</h2>
                </header>

                <div class="main-box-body clearfix">
                    {!! Form::select('category_id', ['' => '- uncategorized -'] + $deviceSection->categories, request()->input('category'), [
                        'class' => 'form-control',
                    ]) !!}
                </div>
            </div>
        </div>
    </div>
@endif
