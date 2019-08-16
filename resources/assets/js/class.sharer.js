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
            var selected = that.$modal.find('select.user-add').val(),
                selectedId = parseInt(selected.substr(2)),
                permissions = [],
                $tr = that.$modal.find('table tfoot tr');

            $.each($tr.find('input[type=checkbox]:checked'), function (i, item) {
                permissions.push(parseInt(item.value));
            });

            if (!selected || !selectedId) {
                alert('Please select a user or a group');

                return false;
            } else if (selected.substr(0, 2) === 'u_') {
                $.each(that.options.users, function (i, user) {
                    if (parseInt(user.id) === selectedId) {
                        that.permissions.push({
                            user_id: user.id,
                            name: user.name,
                            email: user.email,
                            avatar: user.avatar,
                            permissions: permissions,
                        });

                        return false;
                    }
                });
            } else if (selected.substr(0, 2) === 'g_') {
                $.each(that.options.groups, function (i, group) {
                    if (parseInt(group.id) === selectedId) {
                        that.permissions.push({
                            group_id: group.id,
                            name: group.name,
                            email: group.email,
                            avatar: group.avatar,
                            permissions: permissions,
                        });

                        return false;
                    }
                });
            }

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

                    $.growl.notice({message: that.ucfirst(that.options.type) + ' has been shared'});
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
            usersWithPermission = [],
            groupsWithPermission = [];

        $.each(this.permissions, function (i, data) {
            html += '<tr data-id="' + i + '">';
            html += '<td><img src="' + data.avatar + '" width="40" height="40"></td>';
            html += '<td>' + data.name + '</td>';

            if (data.user_id) {
                html += '<td>' + data.email + '</td>';
            } else {
                html += '<td><i style="color: grey;">' + data.email + '</i></td>';
            }

            html += '<td>' + that.checkboxes(data.permissions) + '</td>';
            html += '<td><i class="fa fa-remove btn-remove-permission" title="Remove permission"></i></td>';
            html += '</tr>';

            if (data.user_id) {
                usersWithPermission.push(parseInt(data.user_id));
            } else if (data.group_id) {
                groupsWithPermission.push(parseInt(data.group_id));
            }
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
            userOptions = '<option value="">- select user or group -</option>',
            userList = '',
            groupList = '';

        $.each(this.options.users, function (i, user) {
            if (usersWithPermission.includes(parseInt(user.id))) {
                return;
            }

            var option = document.createElement('option');

            option.value = 'u_' + user.id;
            option.innerHTML = user.name;

            userList += option.outerHTML;

            if (oldVal === parseInt(user.id)) {
                hasVal = true;
            }
        });

        $.each(this.options.groups, function (i, group) {
            if (groupsWithPermission.includes(parseInt(group.id))) {
                return;
            }

            var option = document.createElement('option');

            option.value = 'g_' + group.id;
            option.innerHTML = group.name;

            groupList += option.outerHTML;

            if (oldVal === parseInt(group.id)) {
                hasVal = true;
            }
        });

        if (userList.length) {
            userOptions += '<optgroup label="Users">' + userList + '</optgroup>';
        }

        if (groupList.length) {
            userOptions += '<optgroup label="Groups">' + groupList + '</optgroup>';
        }

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
