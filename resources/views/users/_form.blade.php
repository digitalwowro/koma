<div class="form-group">
    <label for="name" class="col-xs-2 control-label">Name</label>
    <div class="col-xs-10">
        {!! Form::text('name', null, [
            'id' => 'name',
            'class' => 'form-control',
            'placeholder' => 'Name',
        ]) !!}
    </div>
</div>

<div class="form-group">
    <label for="email" class="col-xs-2 control-label">Email</label>
    <div class="col-xs-10">
        {!! Form::email('email', (! isset($user) || filter_var($user->email, FILTER_VALIDATE_EMAIL) ? null : ''), [
            'id' => 'email',
            'class' => 'form-control',
            'placeholder' => 'Email',
            'required' => true,
        ]) !!}
    </div>
</div>

@if (!isset($user) || isset($profile))
<div class="form-group">
    <label for="password" class="col-xs-2 control-label">Password</label>
    <div class="col-xs-10">
        {!! Form::password('password', [
            'id' => 'password',
            'class' => 'form-control',
            'style' => 'font-style:italic;',
            'placeholder' => 'leave blank if you don\'t want to change the password',
        ]) !!}
    </div>
</div>
@endif

@if (!isset($user) || auth()->id() != $user->id)
    <div class="form-group">
        <label class="col-xs-2 control-label">Role</label>
        <div class="col-xs-10">
            <div class="radio icheck">
                <label>
                    {!! Form::radio('role', App\User::ROLE_SYSADMIN, null, [
                        'required' => true,
                    ]) !!}
                    Sysadmin
                </label>
            </div>

            <div class="radio icheck">
                <label>
                    {!! Form::radio('role', App\User::ROLE_ADMIN, null, [
                        'required' => true,
                    ]) !!}
                    Admin
                </label>
            </div>
        </div>
    </div>
@endif
