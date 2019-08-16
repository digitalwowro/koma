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

    @if (isset($user))
        <div class="form-group">
            <label for="password_confirmed" class="col-xs-2 control-label">Password Confirmation</label>
            <div class="col-xs-10">
                {!! Form::password('password_confirmed', [
                    'id' => 'password_confirmed',
                    'class' => 'form-control',
                    'style' => 'font-style:italic;',
                    'placeholder' => 'leave blank if you don\'t want to change the password',
                ]) !!}
            </div>
        </div>
    @endif
@endif

@if (!isset($user) || auth()->id() != $user->id)
    <div class="form-group">
        <label class="col-xs-2 control-label">Role</label>
        <div class="col-xs-10">
            <div class="radio icheck">
                <label>
                    {!! Form::radio('role', App\User::ROLE_USER, null, [
                        'required' => true,
                    ]) !!}
                    User
                </label>
            </div>

            <div class="radio icheck">
                <label>
                    {!! Form::radio('role', App\User::ROLE_ADMIN, null, [
                        'required' => true,
                    ]) !!}
                    Administrator
                </label>
            </div>
        </div>
    </div>
@endif

<div class="form-group">
    <label class="col-xs-2 control-label">Permission Groups</label>
    <div class="col-xs-10">
        {!! Form::select('groups[]', $groups, isset($user) ? $user->groups->pluck('id') : [], [
            'class' => 'form-control select2',
            'multiple' => true,
        ]) !!}
    </div>
</div>

@section('footer')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@append
