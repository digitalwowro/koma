var sharerUtil = {
    options: {},
    $button: null,
    $modal: null,
    url: null,
    devId: null,

    init: function(options) {
        var that = this;

        this.options = $.extend(this.options, options);

        this.$modal = $('#shareModal');

        $('a.share-item').click(function(e) {
            e.preventDefault();

            that.url = $(this).attr('href');
            that.devId = $(this).data('human-id');

            that.$modal.find('.modal-title').html('Share ' + that.options.type + ' <u>' + that.devId + '</u>');

            that.$modal.modal('show');
        });

        this.$modal.find('.do-share-item').click(function(e) {
            e.preventDefault();

            var $moreinfo = that.$modal.find('.modal-footer .more-info'),
                grantType = [];

            $('[name=permission]:checked').each(function(i, el) {
                grantType.push(el.value);
            });

            var params = {
                user_id: that.$modal.find('#user-select').val(),
                grant_type: grantType,
            };

            that.$modal.find('button').attr('disabled', true);

            $moreinfo.html('<i class="fa fa-spinner fa-spin"></i> Please Wait...')
                .removeClass('hidden');

            $.post(that.url, params, function(r) {
                that.$modal.find('button').removeAttr('disabled');

                if (r.error) {
                    $moreinfo.html('<span style="color:darkred;">' + r.error + '</span>');
                }

                if (r.success) {
                    $moreinfo.addClass('hidden');

                    that.$modal.modal('hide');

                    $.growl.notice({message: ucfirst(that.options.type) + ' has been shared'});
                }
            }).fail(function() {
                that.$modal.find('button').removeAttr('disabled');

                $moreinfo.html('<span style="color:darkred;">Could not share ' + that.options.type + '</span>');
            });
        });

        return this;
    },

    ucfirst: function (string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
};
