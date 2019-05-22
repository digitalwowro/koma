<div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Share Device</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <h4>User</h4>

                    <select id="user-select" class="form-control">
                        @foreach(App\User::whereRole(App\User::ROLE_SYSADMIN)->orderBy('name')->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <h4>Permission Level</h4>

                    <br>

                    @php
                        $permission = (new App\Permission([
                            'resource_type' => $resource_type,
                        ]));
                    @endphp

                    <label>
                        {!! Form::radio('permission', App\Permission::GRANT_TYPE_READ, null, [
                            'class' => 'form-control icheck',
                            'required' => true,
                        ]) !!}
                        {{ $permission->present()->actionVerb(App\Permission::GRANT_TYPE_READ) }}
                    </label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <label>
                        {!! Form::radio('permission', App\Permission::GRANT_TYPE_WRITE, null, [
                            'class' => 'form-control icheck',
                            'required' => true,
                        ]) !!}
                        {{ $permission->present()->actionVerb(App\Permission::GRANT_TYPE_WRITE) }}
                    </label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <label>
                        {!! Form::radio('permission', App\Permission::GRANT_TYPE_FULL, null, [
                            'class' => 'form-control icheck',
                            'required' => true,
                        ]) !!}
                        {{ $permission->present()->actionVerb(App\Permission::GRANT_TYPE_FULL) }}
                    </label>

                    @if ($create_permissions)
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <label>
                            {!! Form::radio('permission', App\Permission::GRANT_TYPE_CREATE, null, [
                                'class' => 'form-control icheck',
                                'required' => true,
                            ]) !!}
                            {{ $permission->present()->actionVerb(App\Permission::GRANT_TYPE_CREATE) }}
                        </label>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <label>
                            {!! Form::radio('permission', App\Permission::GRANT_TYPE_READ_CREATE, null, [
                                'class' => 'form-control icheck',
                                'required' => true,
                            ]) !!}
                            {{ $permission->present()->actionVerb(App\Permission::GRANT_TYPE_READ_CREATE) }}
                        </label>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <span class="pull-left more-info hidden" style="margin-top: 5px;"></span>

                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary do-share-item">Share</button>
            </div>
        </div>
    </div>
</div>
