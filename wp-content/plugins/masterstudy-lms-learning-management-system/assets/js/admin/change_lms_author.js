(function ($) {

    $(document).ready(function () {

        var author_block = '.lms_author.column-lms_author';
        var $author_block = $(author_block);

        if (!$author_block.length) return false;

        var $btn = $author_block.find('.button.action');

        $btn.on('click', function (e) {
            e.preventDefault();

            //if (!confirm('Do you really want to change the author of the course?')) return false;

            var $current_block = $(this).closest(author_block);
            var $select = $current_block.find('select');
            var post_id = $select.attr('data-post');
            var author_id = $select.val();

            $.ajax({
                url: stm_lms_ajaxurl + '?action=stm_lms_change_lms_author&post_id=' + post_id + '&author_id=' + author_id,
                context: this,
                data: {},
                beforeSend: function () {
                    $current_block.addClass('loading');
                },
                complete: function (data) {
                    $current_block.removeClass('loading');
                    $select.val(data.responseJSON);
                }
            });

        });

    });

})(jQuery);