<div class="modal fade" id="passwordGenerateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Password Generator</h4>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <input type="text" class="form-control" id="passwordGoesHere">

                    <span class="input-group-btn">
                        <button class="btn btn-primary do-generate-password">Generate</button>
                    </span>
                </div>

                <br>

                <div class="well">
                    <div class="form-inline">
                        <div class="form-group">
                            <label for="passLength">Length</label>
                            <input id="passLength" type="number" min="8" max="32" value="12" class="form-control">
                            <label>characters (8-32)</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Alpha Characters</label>
                            <div class="radio">
                                <input id="a1" type="radio" name="alphaCharacters" value="both" checked>
                                <label for="a1">Both (aBcD)</label>
                            </div>
                            <div class="radio">
                                <input id="a2" type="radio" name="alphaCharacters" value="lowercase">
                                <label for="a2">Lowercase (abc)</label>
                            </div>
                            <div class="radio">
                                <input id="a3" type="radio" name="alphaCharacters" value="uppercase">
                                <label for="a3">Uppercase (ABC)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Non Alpha Characters</label>
                            <div class="radio">
                                <input id="na1" type="radio" name="nonAlphaCharacters" value="both">
                                <label for="na1">Both (1@3$)</label>
                            </div>
                            <div class="radio">
                                <input id="na2" type="radio" name="nonAlphaCharacters" value="numbers" checked>
                                <label for="na2">Numbers (123)</label>
                            </div>
                            <div class="radio">
                                <input id="na3" type="radio" name="nonAlphaCharacters" value="symbols">
                                <label for="na3">Symbols (@#$)</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary do-use-password">Use Password</button>
            </div>
        </div>
    </div>
</div>

@section('footer')
<script>
    $(document).ready(function() {
        var $sourceField;

        $('[data-action="password-generator"]').click(function (e) {
            $('#passwordGenerateModal').modal('show');

            $sourceField = $(this).closest('.input-group').find('input');

            generate();

            e.preventDefault();
        });

        $('[data-action="password-mask"]').click(function (e) {
            var $field = $(this).closest('.input-group').find('input');

            if ($field.attr('type') === 'password') {
                $field.attr('type', 'text');
            } else {
                $field.attr('type', 'password');
            }

            e.preventDefault();
        });

        function generate() {
            var length = $('#passLength').val(),
                alpha = $('[name=alphaCharacters]:checked').val(),
                nonAlpha = $('[name=nonAlphaCharacters]:checked').val(),
                chars = '',
                result = '',
                charSet = [
                    ["0123456789"],
                    ["abcdefghijklmnopqrstuvwxyz"],
                    ["ABCDEFGHIJKLMNOPQRSTUVWXYZ"],
                    ["!\"#$%&'()*+,-./:;<=>?@[\\]^_`{|}~"],
                ];

            if (['both', 'numbers'].includes(nonAlpha)) {
                chars = chars + charSet[0];
            }

            if (['both', 'lowercase'].includes(alpha)) {
                chars = chars + charSet[1];
            }

            if (['both', 'uppercase'].includes(alpha)) {
                chars = chars + charSet[2];
            }

            if (['both', 'symbols'].includes(nonAlpha)) {
                chars = chars + charSet[3];
            }

            for (var i = 0; i < length; i++) {
                result += chars[Math.floor(Math.random() * chars.length)];
            }

            $('#passwordGoesHere').val(result);

            return false;
        }

        generate();

        $('.do-generate-password').click(generate);

        $('.do-use-password').click(function() {
            if ($sourceField.length) {
                $sourceField.val($('#passwordGoesHere').val());
            }

            $('#passwordGenerateModal').modal('hide');
        });
    });
</script>
@append
