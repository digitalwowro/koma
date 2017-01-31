<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add Subnet</h4>
            </div>
            {!! Form::open(['route' => ['ip.store', $ipCategory->id], 'role' => 'form', 'method' => 'POST']) !!}
            <div class="modal-body">
                <div class="form-group">
                    <label for="subnetInput">Subnet</label>
                    <input type="text" name="subnet" class="form-control" id="subnetInput" placeholder="127.0.0.1/32">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
