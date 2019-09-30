<div class="box-body" style="font-size: 16px; line-height: 160%;">
    @if (!empty($center))
        <div class="text-center">
    @endif

    <strong>This is your recovery phrase. It will be displayed only once and it is the only way of recovering your data!</strong>

    <p>As this system is using a "zero knowledge" approach, losing your password and recovery phrase will render all your data permanently unusable! Anyone with access to your recovery phrase could access your data. Store it securely!</p>

    <div class="well">
        <code>{{ implode(' ', str_split($recoveryString, 4)) }}</code>
    </div>

    @if (!empty($center))
        </div>
    @endif
</div>
