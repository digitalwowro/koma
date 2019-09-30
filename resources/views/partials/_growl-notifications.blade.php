<script>
    @foreach (['success', 'notice', 'warning', 'error'] as $type)
    @if (session()->has($type) && is_string(session($type)))
    $.growl.{{ $type === 'success' ? 'notice' : $type }}({
        message: '{{ addslashes(session($type)) }}'
    });
    @endif
    @endforeach
</script>
