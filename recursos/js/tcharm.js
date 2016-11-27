jQuery.fn.extend({
    tcharm: function (options) {

        if (options == 'hide') {
            this.css('display', 'none');
            this.attr('tcharm-display', '0');
        }
        else if (options == 'show') {
            this.css('display', 'none');
            this.attr('tcharm-display', '0');
        }
        else {

            var defaults = {
                position: 'right',
                trigger_tag: '.tcharm-trigger',
                trigger_tag_event: 'click',
                trigger_close: '.tcharm-close',
                trigger_close_event: 'click',
                display: false,
                top: '0px',
                bottom: '0px',
                left: '0px',
                right: '0px'
            };

            var options = $.extend({}, defaults, options);

            this.each(function () {
                var elem = jQuery(this);

                elem.css('position', 'fixed');
                elem.css('z-index', '999999');
                elem.css('right', options.right);
                elem.css('top', options.top);
                elem.css('bottom', options.bottom);
                elem.css('left', options.left);

                if (options.display == true) {
                    elem.css('display', 'block');
                    elem.attr('tcharm-display', '1');
                }
                else {
                    elem.css('display', 'none');
                    elem.attr('tcharm-display', '0');
                }

                switch (options.position) {
                    case 'right':
                    {
                        elem.css('left', 'auto');
                        break;
                    }
                    case 'left':
                    {
                        elem.css('right', 'auto');
                        break;
                    }
                    case 'top':
                    {
                        elem.css('bottom', 'auto');
                        break;
                    }
                    case 'bottom':
                    {
                        elem.css('top', 'auto');
                        break;
                    }
                    default:
                    {
                        elem.css('left', 'auto');
                        break;
                    }
                }

                //suscribo los eventos
                $(options.trigger_tag).on(options.trigger_tag_event, function (e) {
                    e.preventDefault();
                    if (elem.attr('tcharm-display') == '1') {
                        elem.fadeOut(300);
                        elem.attr('tcharm-display', '0');
                    }
                    else if (elem.attr('tcharm-display') == '0') {
                        elem.fadeIn(300);
                        elem.attr('tcharm-display', '1');
                    }
                });

                $(options.trigger_close).on(options.trigger_close_event, function (e) {
                    e.preventDefault();
                    elem.fadeOut(300);
                    elem.attr('tcharm-display', '0');
                });

            });
        }
    }

});