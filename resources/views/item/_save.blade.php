<div class="box box-primary">
    <div class="box-body">
        <button type="submit" class="btn btn-primary">{{ isset($item) ? 'Save' : 'Add' }}</button>
        <a href="{{ route('item.index', $category->id) }}" class="btn btn-default">Cancel</a>
    </div>
</div>
