<div class="box-body" style="font-size: 16px; line-height: 160%;">
    <span style="color:red; font-weight: bold;">
        Warning!
    </span>

    If you forget your new password, there is no way to get your data back!
    <br>
    As a safety feature in case that happens, it is recommended to write down and store in a safe place this recovery string.
    <br>
    This is the only way of getting your data back in case you forget your password. This code will only be displayed once.
    <br><br>

    <div class="well">
        <code>{{ implode(' ', str_split($recoveryString, 4)) }}</code>
    </div>
</div>
