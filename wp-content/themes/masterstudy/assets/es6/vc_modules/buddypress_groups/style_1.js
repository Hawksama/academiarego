(function ($) {
    $(document).ready(function () {

        $('.stm_buddypress_groups__sort select').on('change', function () {

            let $this = $(this);
            let $wrapper = $this.closest('.stm_buddypress_groups');
            let $list = $wrapper.find('.stm_buddypress_groups__list');

            $.ajax({
                url: ajaxurl,
                dataType: 'json',
                context: this,
                data: {
                    action: 'stm_load_buddypress_groups',
                    sort: $this.val(),
                    nonce: stm_buddypress_groups,
                },
                beforeSend: function () {
                    $wrapper.addClass('loading');
                },
                complete: function (data) {
                    $list.html(data.responseJSON.html);
                    $wrapper.removeClass('loading');
                }
            });
        });

    });
})(jQuery);