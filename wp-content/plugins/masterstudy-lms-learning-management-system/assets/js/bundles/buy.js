(function ($) {

    $(document).ready(function () {
        var $buy = $('.stm_lms_buy_bundle');

        if (!$buy.length) return true;

        $buy.on('click', function (e) {

            var $this = $(this);
            var $btn = $this.find('.btn');

            if ($btn.attr('href') !== '#') {
                location.href = $btn.attr('href');
                return false;
            }

            console.log('fired');

            $.ajax({
                url: stm_lms_ajaxurl,
                dataType: 'json',
                context: this,
                data: {
                    action: 'stm_lms_add_bundle_to_cart',
                    item_id: $this.data('bundle'),
                },
                beforeSend: function () {
                    $(this).addClass('loading');
                },
                complete: function (data) {

                    data = data['responseJSON'];
                    $(this).removeClass('loading');

                    $(this).find('span').text(data['text']);

                    if (data['cart_url']) {
                        if (data['redirect']) window.location = data['cart_url'];
                        $btn.attr('href', data['cart_url']);
                    }

                }
            });
        });

    });

})(jQuery);