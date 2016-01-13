<div class="form-group">
    <label for="input1" class="col-xs-2 control-label">Name</label>
    <div class="col-xs-10">
        {!! Form::text('name', null, [
            'class'       => 'form-control',
            'placeholder' => 'Name',
        ]) !!}
    </div>
</div>

<div class="form-group">
    <label for="input1" class="col-xs-2 control-label">Email</label>
    <div class="col-xs-10">
        {!! Form::email('email', (! isset($user) || filter_var($user->email, FILTER_VALIDATE_EMAIL) ? null : ''), [
            'class'       => 'form-control',
            'placeholder' => 'Email',
            'required'    => true,
        ]) !!}
    </div>
</div>

<div class="form-group">
    <label for="input1" class="col-xs-2 control-label">Password</label>
    <div class="col-xs-10">
        {!! Form::password('password', [
            'id'          => 'password',
            'class'       => 'form-control',
            'style'       => 'font-style:italic;',
            'placeholder' => 'leave blank if you don\'t want to assign or change the user\'s password',
        ]) !!}
    </div>
</div>

@if ( ! isset($user) || auth()->id() != $user->id)
    <div class="col-xs-12">
        <div class="form-group">
            <label class="col-xs-2 control-label">Role</label>
            <div class="col-xs-10">
                <div class="radio">
                    {!! Form::radio('role', App\User::ROLE_ADMIN, null, [
                        'id'       => 'role-admin',
                        'class'    => 'form-control',
                        'required' => true,
                    ]) !!}
                    <label for="role-admin">
                        Admin
                    </label>
                </div>

                <div class="radio">
                    {!! Form::radio('role', App\User::ROLE_SUPERADMIN, null, [
                        'id'       => 'role-superadmin',
                        'class'    => 'form-control',
                        'required' => true,
                    ]) !!}
                    <label for="role-superadmin">
                        Superadmin
                    </label>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="form-group">
    <div class="col-lg-offset-2 col-xs-10">
        <button type="submit" class="btn btn-success">{{ isset($user) ? 'Save' : 'Add' }}</button>
        <button class="btn btn-default" onclick="window.history.back(); return false;">Cancel</button>
    </div>
</div>
