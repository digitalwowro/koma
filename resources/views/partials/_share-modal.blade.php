<div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Share Device</h4>
            </div>

            <div class="modal-body loader">
                <i class="fa fa-spin fa-spinner"></i>
                Loading permissions...
            </div>

            <div class="modal-body">
                <div>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <td colspan="5">
                                    <h4>Users who have access</h4>
                                </td>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <h4>Share with...</h4>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <img src="{{ gravatar('', 40) }}" width="40" height="40">
                                </td>

                                <td colspan="2">
                                    <select class="form-control user-add"></select>
                                </td>

                                <td>
                                    <div class="form-group">
                                        <label>
                                            {!! Form::checkbox('permission', App\Permission::GRANT_TYPE_READ, null, [
                                                'class' => 'form-control icheck',
                                            ]) !!}
                                            Read
                                        </label>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <label>
                                            {!! Form::checkbox('permission', App\Permission::GRANT_TYPE_EDIT, null, [
                                                'class' => 'form-control icheck',
                                            ]) !!}
                                            Edit
                                        </label>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <label>
                                            {!! Form::checkbox('permission', App\Permission::GRANT_TYPE_DELETE, null, [
                                                'class' => 'form-control icheck',
                                            ]) !!}
                                            Delete
                                        </label>

                                        @if ($create_permissions)
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <label>
                                                {!! Form::checkbox('permission', App\Permission::GRANT_TYPE_CREATE, null, [
                                                    'class' => 'form-control icheck',
                                                    'required' => true,
                                                ]) !!}
                                                Create
                                            </label>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <a class="btn btn-primary btn-add-permission pull-right">
                                        <i class="fa fa-plus"></i>
                                        Add
                                    </a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <span class="pull-left more-info hidden" style="margin-top: 5px;"></span>

                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary do-share-item">Save</button>
            </div>
        </div>
    </div>
</div>

@section('footer')
    <script>
        $.sharer = sharerUtil.init({
            type: '{{ $resource_type }}',
            createPermission: {{ empty($create_permissions) ? 'false' : 'true' }},
            permissionsRoute: '{{ route('share.with') }}',
            postRoute: '{{ route('share.post') }}',
            users: {!! $users !!},
        });
    </script>
@append
