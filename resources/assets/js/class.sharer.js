var sharerUtil = {
    options: {},
    $button: null,
    $modal: null,
    devId: null,
    id: null,
    permissions: [],

    init: function(options) {
        var that = this;

        this.options = $.extend(this.options, options);

        this.$modal = $('#shareModal');

        $('a.share-item').click(function(e) {
            var $this = $(this);

            e.preventDefault();

            that.devId = $this.data('human-id');
            that.id = $this.data('id');

            // (re)set modal state
            that.$modal.find('.modal-title').html('Share ' + that.options.type + ' <u>' + that.devId + '</u>');

            that.$modal.find('.modal-body:not(.loader)').addClass('hidden');
            that.$modal.find('.modal-body.loader').removeClass('hidden error');

            that.$modal.find('.do-share-item').addClass('hidden');

            that.$modal.modal('show');

            that.loadPermissions();
        });

        this.$modal.on('ifToggled', 'table tbody input[type=checkbox]', function() {
            var $tr = $(this).closest('tr'),
                id = parseInt($tr.data('id')),
                newPermissions = [];

            $.each($tr.find('input[type=checkbox]:checked'), function (i, item) {
                newPermissions.push(parseInt(item.value));
            });

            that.permissions[id].permissions = newPermissions;
        });

        this.$modal.find('.btn-add-permission').click(function() {
            var userId = that.$modal.find('select.user-add').val(),
                permissions = [],
                $tr = that.$modal.find('table tfoot tr');

            $.each($tr.find('input[type=checkbox]:checked'), function (i, item) {
                permissions.push(parseInt(item.value));
            });

            var user = null;

            $.each(that.options.users, function (i, item) {
                if (parseInt(item.id) === parseInt(userId)) {
                    user = item;
                    return false;
                }
            });

            if (!user) {
                alert('Please select a user');

                return false;
            }

            that.permissions.push({
                id: user.id,
                name: user.name,
                email: user.email,
                avatar: user.avatar,
                permissions: permissions,
            });

            // reset
            $tr.find('input[type=checkbox]').iCheck('uncheck');

            // redraw
            that.renderPermissions();
        });

        this.$modal.on('click', '.btn-remove-permission', function() {
            var $tr = $(this).closest('tr'),
                id = $tr.data('id');

            that.permissions.splice(id, 1);

            that.renderPermissions();
        });

        this.$modal.find('.do-share-item').click(function(e) {
            e.preventDefault();

            var $moreinfo = that.$modal.find('.modal-footer .more-info'),
                url = that.options.postRoute,
                params = {
                    type: that.options.type.split(' ').pop(),
                    id: that.id,
                    permissions: that.permissions,
                };

            that.$modal.find('button').attr('disabled', true);

            $moreinfo.html('<i class="fa fa-spinner fa-spin"></i> Please Wait...')
                .removeClass('hidden');

            $.post(url, params, function(r) {
                that.$modal.find('button').removeAttr('disabled');

                if (r.success) {
                    $moreinfo.addClass('hidden');

                    that.$modal.modal('hide');

                    $.growl.notice({message: ucfirst(that.options.type) + ' has been shared'});
                } else if (r.error) {
                    $moreinfo.html('<span style="color:darkred;">' + r.error + '</span>');
                } else {
                    $moreinfo.html('<span style="color:darkred;">Could not share ' + that.options.type + '</span>');
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
    },

    checkboxes: function (permissions) {
        var result = '<div class="form-group"><label> ' +
            '<input class="form-control icheck" name="permission" type="checkbox" value="1"' + (permissions.includes(1) ? ' checked' : '') + '>' +
            ' Read ' +
            '</label>' +
            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

        result += '<label> ' +
            '<input class="form-control icheck" name="permission" type="checkbox" value="2"' + (permissions.includes(2) ? ' checked' : '') + '>' +
            ' Edit ' +
            '</label>' +
            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

        result += '<label> ' +
            '<input class="form-control icheck" name="permission" type="checkbox" value="4"' + (permissions.includes(4) ? ' checked' : '') + '>' +
            ' Delete ' +
            '</label>';

        if (this.options.createPermission) {
            result += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                '<label> ' +
                '<input class="form-control icheck" name="permission" type="checkbox" value="8"' + (permissions.includes(8) ? ' checked' : '') + '>' +
                ' Create ' +
                '</label>';
        }

        result += '</div>';

        return result;
    },

    renderPermissions: function () {
        var that = this,
            html = '',
            $body = this.$modal.find('.modal-body:not(.loader)'),
            usersWithPermission = [];

        $.each(this.permissions, function (i, data) {
            html += '<tr data-id="' + i + '">';
            html += '<td><img src="' + data.avatar + '" width="40" height="40"></td>';
            html += '<td>' + data.name + '</td>';
            html += '<td>' + data.email + '</td>';
            html += '<td>' + that.checkboxes(data.permissions) + '</td>';
            html += '<td><i class="fa fa-remove btn-remove-permission" title="Remove permission"></i></td>';
            html += '</tr>';

            usersWithPermission.push(parseInt(data.id));
        });

        if (!html) {
            html = '<tr><td colspan="5" class="not-shared">- this ' + this.options.type + ' is not shared with anyone -</td></tr>';
        }

        $body.find('table > tbody').html(html);

        if (typeof bindIcheck === 'function') {
            bindIcheck($body.find('table > tbody .icheck'));
        }

        // refresh user list
        var $userSelect = this.$modal.find('select.user-add'),
            oldVal = parseInt($userSelect.val()),
            hasVal = false,
            userOptions = '<option value="">- select user -</option>';

        $.each(this.options.users, function (i, user) {
            if (!usersWithPermission.includes(parseInt(user.id))) {
                var option = document.createElement('option');

                option.value = user.id;
                option.innerHTML = user.name;

                userOptions += option.outerHTML;

                if (oldVal === parseInt(user.id)) {
                    hasVal = true;
                }
            }
        });

        $userSelect.html(userOptions);

        if (hasVal) {
            $userSelect.val(oldVal);
        }
    },

    loadPermissionsError: function () {
        this.$modal.find('.modal-body.loader')
            .addClass('error')
            .html('<i class="fa fa-warning"></i> Error loading permissions');
    },

    loadPermissions: function () {
        var that = this,
            $body = this.$modal.find('.modal-body:not(.loader)'),
            params = {
                type: this.options.type.split(' ').pop(),
                id: this.id,
            };

        $.post(this.options.permissionsRoute, params, function (r) {
            that.permissions = r;
            that.renderPermissions();

            $body.removeClass('hidden');
            that.$modal.find('.modal-body.loader').addClass('hidden');
            that.$modal.find('.do-share-item').removeClass('hidden');
        }).fail(function () {
            that.loadPermissionsError();
        });
    },
};
