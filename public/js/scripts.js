/**
 * CLIPBOARD FUNCTIONALITY
 */

/*!
 * clipboard.js v1.5.5
 * https://zenorocha.github.io/clipboard.js
 *
 * Licensed MIT © Zeno Rocha
 */
!function(t){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=t();else if("function"==typeof define&&define.amd)define([],t);else{var e;e="undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this,e.Clipboard=t()}}(function(){var t,e,n;return function t(e,n,r){function o(a,c){if(!n[a]){if(!e[a]){var s="function"==typeof require&&require;if(!c&&s)return s(a,!0);if(i)return i(a,!0);var u=new Error("Cannot find module '"+a+"'");throw u.code="MODULE_NOT_FOUND",u}var l=n[a]={exports:{}};e[a][0].call(l.exports,function(t){var n=e[a][1][t];return o(n?n:t)},l,l.exports,t,e,n,r)}return n[a].exports}for(var i="function"==typeof require&&require,a=0;a<r.length;a++)o(r[a]);return o}({1:[function(t,e,n){var r=t("matches-selector");e.exports=function(t,e,n){for(var o=n?t:t.parentNode;o&&o!==document;){if(r(o,e))return o;o=o.parentNode}}},{"matches-selector":2}],2:[function(t,e,n){function r(t,e){if(i)return i.call(t,e);for(var n=t.parentNode.querySelectorAll(e),r=0;r<n.length;++r)if(n[r]==t)return!0;return!1}var o=Element.prototype,i=o.matchesSelector||o.webkitMatchesSelector||o.mozMatchesSelector||o.msMatchesSelector||o.oMatchesSelector;e.exports=r},{}],3:[function(t,e,n){function r(t,e,n,r){var i=o.apply(this,arguments);return t.addEventListener(n,i),{destroy:function(){t.removeEventListener(n,i)}}}function o(t,e,n,r){return function(n){n.delegateTarget=i(n.target,e,!0),n.delegateTarget&&r.call(t,n)}}var i=t("closest");e.exports=r},{closest:1}],4:[function(t,e,n){n.node=function(t){return void 0!==t&&t instanceof HTMLElement&&1===t.nodeType},n.nodeList=function(t){var e=Object.prototype.toString.call(t);return void 0!==t&&("[object NodeList]"===e||"[object HTMLCollection]"===e)&&"length"in t&&(0===t.length||n.node(t[0]))},n.string=function(t){return"string"==typeof t||t instanceof String},n.function=function(t){var e=Object.prototype.toString.call(t);return"[object Function]"===e}},{}],5:[function(t,e,n){function r(t,e,n){if(!t&&!e&&!n)throw new Error("Missing required arguments");if(!c.string(e))throw new TypeError("Second argument must be a String");if(!c.function(n))throw new TypeError("Third argument must be a Function");if(c.node(t))return o(t,e,n);if(c.nodeList(t))return i(t,e,n);if(c.string(t))return a(t,e,n);throw new TypeError("First argument must be a String, HTMLElement, HTMLCollection, or NodeList")}function o(t,e,n){return t.addEventListener(e,n),{destroy:function(){t.removeEventListener(e,n)}}}function i(t,e,n){return Array.prototype.forEach.call(t,function(t){t.addEventListener(e,n)}),{destroy:function(){Array.prototype.forEach.call(t,function(t){t.removeEventListener(e,n)})}}}function a(t,e,n){return s(document.body,t,e,n)}var c=t("./is"),s=t("delegate");e.exports=r},{"./is":4,delegate:3}],6:[function(t,e,n){function r(t){var e;if("INPUT"===t.nodeName||"TEXTAREA"===t.nodeName)t.focus(),t.setSelectionRange(0,t.value.length),e=t.value;else{t.hasAttribute("contenteditable")&&t.focus();var n=window.getSelection(),r=document.createRange();r.selectNodeContents(t),n.removeAllRanges(),n.addRange(r),e=n.toString()}return e}e.exports=r},{}],7:[function(t,e,n){function r(){}r.prototype={on:function(t,e,n){var r=this.e||(this.e={});return(r[t]||(r[t]=[])).push({fn:e,ctx:n}),this},once:function(t,e,n){function r(){o.off(t,r),e.apply(n,arguments)}var o=this;return r._=e,this.on(t,r,n)},emit:function(t){var e=[].slice.call(arguments,1),n=((this.e||(this.e={}))[t]||[]).slice(),r=0,o=n.length;for(r;o>r;r++)n[r].fn.apply(n[r].ctx,e);return this},off:function(t,e){var n=this.e||(this.e={}),r=n[t],o=[];if(r&&e)for(var i=0,a=r.length;a>i;i++)r[i].fn!==e&&r[i].fn._!==e&&o.push(r[i]);return o.length?n[t]=o:delete n[t],this}},e.exports=r},{}],8:[function(t,e,n){"use strict";function r(t){return t&&t.__esModule?t:{"default":t}}function o(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}n.__esModule=!0;var i=function(){function t(t,e){for(var n=0;n<e.length;n++){var r=e[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(t,r.key,r)}}return function(e,n,r){return n&&t(e.prototype,n),r&&t(e,r),e}}(),a=t("select"),c=r(a),s=function(){function t(e){o(this,t),this.resolveOptions(e),this.initSelection()}return t.prototype.resolveOptions=function t(){var e=arguments.length<=0||void 0===arguments[0]?{}:arguments[0];this.action=e.action,this.emitter=e.emitter,this.target=e.target,this.text=e.text,this.trigger=e.trigger,this.selectedText=""},t.prototype.initSelection=function t(){if(this.text&&this.target)throw new Error('Multiple attributes declared, use either "target" or "text"');if(this.text)this.selectFake();else{if(!this.target)throw new Error('Missing required attributes, use either "target" or "text"');this.selectTarget()}},t.prototype.selectFake=function t(){var e=this;this.removeFake(),this.fakeHandler=document.body.addEventListener("click",function(){return e.removeFake()}),this.fakeElem=document.createElement("textarea"),this.fakeElem.style.position="absolute",this.fakeElem.style.left="-9999px",this.fakeElem.style.top=(window.pageYOffset||document.documentElement.scrollTop)+"px",this.fakeElem.setAttribute("readonly",""),this.fakeElem.value=this.text,document.body.appendChild(this.fakeElem),this.selectedText=c.default(this.fakeElem),this.copyText()},t.prototype.removeFake=function t(){this.fakeHandler&&(document.body.removeEventListener("click"),this.fakeHandler=null),this.fakeElem&&(document.body.removeChild(this.fakeElem),this.fakeElem=null)},t.prototype.selectTarget=function t(){this.selectedText=c.default(this.target),this.copyText()},t.prototype.copyText=function t(){var e=void 0;try{e=document.execCommand(this.action)}catch(n){e=!1}this.handleResult(e)},t.prototype.handleResult=function t(e){e?this.emitter.emit("success",{action:this.action,text:this.selectedText,trigger:this.trigger,clearSelection:this.clearSelection.bind(this)}):this.emitter.emit("error",{action:this.action,trigger:this.trigger,clearSelection:this.clearSelection.bind(this)})},t.prototype.clearSelection=function t(){this.target&&this.target.blur(),window.getSelection().removeAllRanges()},t.prototype.destroy=function t(){this.removeFake()},i(t,[{key:"action",set:function t(){var e=arguments.length<=0||void 0===arguments[0]?"copy":arguments[0];if(this._action=e,"copy"!==this._action&&"cut"!==this._action)throw new Error('Invalid "action" value, use either "copy" or "cut"')},get:function t(){return this._action}},{key:"target",set:function t(e){if(void 0!==e){if(!e||"object"!=typeof e||1!==e.nodeType)throw new Error('Invalid "target" value, use a valid Element');this._target=e}},get:function t(){return this._target}}]),t}();n.default=s,e.exports=n.default},{select:6}],9:[function(t,e,n){"use strict";function r(t){return t&&t.__esModule?t:{"default":t}}function o(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function i(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function, not "+typeof e);t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,enumerable:!1,writable:!0,configurable:!0}}),e&&(Object.setPrototypeOf?Object.setPrototypeOf(t,e):t.__proto__=e)}function a(t,e){var n="data-clipboard-"+t;if(e.hasAttribute(n))return e.getAttribute(n)}n.__esModule=!0;var c=t("./clipboard-action"),s=r(c),u=t("tiny-emitter"),l=r(u),f=t("good-listener"),d=r(f),h=function(t){function e(n,r){o(this,e),t.call(this),this.resolveOptions(r),this.listenClick(n)}return i(e,t),e.prototype.resolveOptions=function t(){var e=arguments.length<=0||void 0===arguments[0]?{}:arguments[0];this.action="function"==typeof e.action?e.action:this.defaultAction,this.target="function"==typeof e.target?e.target:this.defaultTarget,this.text="function"==typeof e.text?e.text:this.defaultText},e.prototype.listenClick=function t(e){var n=this;this.listener=d.default(e,"click",function(t){return n.onClick(t)})},e.prototype.onClick=function t(e){var n=e.delegateTarget||e.currentTarget;this.clipboardAction&&(this.clipboardAction=null),this.clipboardAction=new s.default({action:this.action(n),target:this.target(n),text:this.text(n),trigger:n,emitter:this})},e.prototype.defaultAction=function t(e){return a("action",e)},e.prototype.defaultTarget=function t(e){var n=a("target",e);return n?document.querySelector(n):void 0},e.prototype.defaultText=function t(e){return a("text",e)},e.prototype.destroy=function t(){this.listener.destroy(),this.clipboardAction&&(this.clipboardAction.destroy(),this.clipboardAction=null)},e}(l.default);n.default=h,e.exports=n.default},{"./clipboard-action":8,"good-listener":5,"tiny-emitter":7}]},{},[9])(9)});

$('.copy-this').click(function(e) {
    e.preventDefault();
});

var clipboard = new Clipboard('.copy-this');

clipboard.on('success', function(e) {
    var notification = new NotificationFx({
        message : '<span class="icon fa fa-bullhorn fa-2x"></span><p>Text copied to clipboard.</p>',
        layout : 'bar',
        effect : 'slidetop',
        type : 'success' // notice, warning or error
    });

    // show the notification
    notification.show();
});

clipboard.on('error', function(e) {
    var notification = new NotificationFx({
        message : '<span class="icon fa fa-bullhorn fa-2x"></span><p>Error copying to clipboard. Please try using a better web browser.</p>',
        layout : 'bar',
        effect : 'slidetop',
        type : 'error' // notice, warning or error
    });

    // show the notification
    notification.show();
});

$(function($) {
    setTimeout(function() {
        $('#content-wrapper > .row').css({
            opacity: 1
        });
    }, 200);

    $('#sidebar-nav,#nav-col-submenu').on('click', '.dropdown-toggle', function (e) {
        e.preventDefault();

        var $item = $(this).parent();

        if (!$item.hasClass('open')) {
            $item.parent().find('.open .submenu').slideUp('fast');
            $item.parent().find('.open').toggleClass('open');
        }

        $item.toggleClass('open');

        if ($item.hasClass('open')) {
            $item.children('.submenu').slideDown('fast');
        }
        else {
            $item.children('.submenu').slideUp('fast');
        }
    });

    $('body').on('mouseenter', '#page-wrapper.nav-small #sidebar-nav .dropdown-toggle', function (e) {
        if ($( document ).width() >= 992) {
            var $item = $(this).parent();

            if ($('body').hasClass('fixed-leftmenu')) {
                var topPosition = $item.position().top;

                if ((topPosition + 4*$(this).outerHeight()) >= $(window).height()) {
                    topPosition -= 6*$(this).outerHeight();
                }

                $('#nav-col-submenu').html($item.children('.submenu').clone());
                $('#nav-col-submenu > .submenu').css({'top' : topPosition});
            }

            $item.addClass('open');
            $item.children('.submenu').slideDown('fast');
        }
    });

    $('body').on('mouseleave', '#page-wrapper.nav-small #sidebar-nav > .nav-pills > li', function (e) {
        if ($( document ).width() >= 992) {
            var $item = $(this);

            if ($item.hasClass('open')) {
                $item.find('.open .submenu').slideUp('fast');
                $item.find('.open').removeClass('open');
                $item.children('.submenu').slideUp('fast');
            }

            $item.removeClass('open');
        }
    });
    $('body').on('mouseenter', '#page-wrapper.nav-small #sidebar-nav a:not(.dropdown-toggle)', function (e) {
        if ($('body').hasClass('fixed-leftmenu')) {
            $('#nav-col-submenu').html('');
        }
    });
    $('body').on('mouseleave', '#page-wrapper.nav-small #nav-col', function (e) {
        if ($('body').hasClass('fixed-leftmenu')) {
            $('#nav-col-submenu').html('');
        }
    });

    $('#make-small-nav').click(function (e) {
        $('#page-wrapper').toggleClass('nav-small');
    });

    $(window).smartresize(function(){
        if ($( document ).width() <= 991) {
            $('#page-wrapper').removeClass('nav-small');
        }
    });

    $('.mobile-search').click(function(e) {
        e.preventDefault();

        $('.mobile-search').addClass('active');
        $('.mobile-search form input.form-control').focus();
    });
    $(document).mouseup(function (e) {
        var container = $('.mobile-search');

        if (!container.is(e.target) // if the target of the click isn't the container...
            && container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            container.removeClass('active');
        }
    });

    $('.fixed-leftmenu #col-left').nanoScroller({
        alwaysVisible: false,
        iOSNativeScrolling: false,
        preventPageScrolling: true,
        contentClass: 'col-left-nano-content'
    });

    // build all tooltips from data-attributes
    $("[data-toggle='tooltip']").each(function (index, el) {
        $(el).tooltip({
            placement: $(this).data("placement") || 'top'
        });
    });
});

$.fn.removeClassPrefix = function(prefix) {
    this.each(function(i, el) {
        var classes = el.className.split(" ").filter(function(c) {
            return c.lastIndexOf(prefix, 0) !== 0;
        });
        el.className = classes.join(" ");
    });
    return this;
};

(function($,sr){
    // debouncing function from John Hann
    // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
    var debounce = function (func, threshold, execAsap) {
        var timeout;

        return function debounced () {
            var obj = this, args = arguments;
            function delayed () {
                if (!execAsap)
                    func.apply(obj, args);
                timeout = null;
            };

            if (timeout)
                clearTimeout(timeout);
            else if (execAsap)
                func.apply(obj, args);

            timeout = setTimeout(delayed, threshold || 100);
        };
    }
    // smartresize
    jQuery.fn[sr] = function(fn){	return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');

/**
 * Misc
 */
$(document).ready(function() {
    // datepickers
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
    });

    // init data tables
    if ($('.datatable').length) {
        $.tableFixed = $('.datatable').dataTable({
            sDom: 'lTfr<"clearfix">tip',
            stateSave: true,
            language: {
                infoEmpty: "No entries to show"
            },
            columnDefs: [{
                orderable: false,
                targets: -1
            }]
        });

        new $.fn.dataTable.FixedHeader($.tableFixed);
    }

    // auto-close popover elements
    $(document).mouseup(function (e) {
        $('[data-auto-close]').each(function(i, container) {
            var $container = $(container),
                auto_close_id = $container.data('auto-close');

            if ($container.is(':visible') && ! $container.is(e.target) && $container.has(e.target).length === 0) {
                var is_opener = $(e.target).parents('[data-opener-id="' + auto_close_id + '"]').length ? true : false;

                if ( ! is_opener) { // do not act on clicks on the opener element
                    $container.trigger('autoclose');
                }
            }
        });
    });

    // multi select dropdowns
    $('.multi-select')
        .on('click', '.btn', function() {
            $(this).closest('.multi-select').find('.multi-choices').toggle();

            return false;
        })
        .on('autoclose', function() {
            $(this).closest('.multi-select').find('.multi-choices').hide();
        })
        .on('changed', function() {
            var $selected = $(this).find('input[type=checkbox]:checked'),
                $btn = $(this).find('.btn'),
                choices = [],
                field_id = $(this).data('opener-id');

            if ($selected.length == 0) {
                $(this).find('input[type=checkbox]').prop('checked', true);
                $selected = $(this).find('input[type=checkbox]:checked');
            }

            $selected.each(function() {
                var text = $.trim($(this).closest('label').text());

                choices.push(text);
            });

            var tableColumn = $.tableFixed.api().column($(this).data('ith'));

            if (choices.length == 0 || choices.length == $(this).find('input[type=checkbox]').length) {
                $btn
                    .html('<i class="fa fa-square-o"></i> ' + $btn.data('filter-name') + ' <i class="fa fa-chevron-down"></i>')
                    .removeClass('btn-danger')
                    .addClass('btn-default');

                // set datatable search value
                tableColumn = tableColumn.search('');
            } else {
                $btn
                    .html('<i class="fa fa-check-square-o"></i> ' + $btn.data('filter-name') + ': ' + choices.join(', ') + ' <i class="fa fa-chevron-down"></i>')
                    .addClass('btn-danger')
                    .removeClass('btn-default');

                // set datatable search value
                tableColumn = tableColumn.search('^' + choices.join('|') + '$', true, false, false);
            }

            // redraw datatable using new search params
            tableColumn.draw();

            // save to cookie
            var cookie = {};
            try { cookie = JSON.parse(Cookies.get('device-filters')); } catch(e) {}
            cookie[field_id] = choices;
            Cookies.set('device-filters', cookie);
        })
        .on('click', 'input[type=checkbox]', function() {
            var $multi = $(this).closest('.multi-select');

            $multi.trigger('changed');
        })
        .trigger('changed')
        .find('option:not(:selected)').attr('selected', true);
});
