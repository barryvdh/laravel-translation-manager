<script>
    $.fn.editableform.buttons = '<button type="submit" class="btn btn-sm btn-info editable-submit"><span class="la la-check"></span></button>' +
        '<button type="button" class="btn btn-danger btn-sm editable-cancel"><span class="la la-remove"></span></button>';
    $.fn.editable.defaults.mode = 'inline';

    jQuery(document).ready(function ($) {

        $.ajaxSetup({
            beforeSend: function (xhr, settings) {
                settings.data += "&_token={{csrf_token()}}";
            }
        });

        $('.editable').editable().on('hidden', function (e, reason) {
            var locale = $(this).data('locale');
            if (reason === 'save') {
                $(this).removeClass('status-0').addClass('font-weight-bold');
            }
            if (reason === 'save' || reason === 'nochange') {
                var $next = $(this).closest('tr').next().find('.editable.locale-' + locale);
                setTimeout(function () {
                    $next.editable('show');
                }, 300);
            }
        });

        $('.group-select').on('change', function () {
            var group = $(this).val();
            if (group) {
                window.location.href = '<?php echo action($controller . '@getView') ?>/' + $(this).val();
            } else {
                window.location.href = '<?php echo action($controller . '@getIndex') ?>';
            }
        });

        $("a.delete-key").click(function (event) {
            event.preventDefault();
            var row = $(this).closest('tr');
            var url = $(this).attr('href');
            var id = row.attr('id');
            $.post(url, {id: id}, function () {
                row.remove();
            });
        });

        $('.form-import').on('ajax:success', function (e, data) {
            $('div.success-import strong.counter').text(data.counter);
            $('div.success-import').slideDown();
            window.location.reload();
        });

        $('.form-find').on('ajax:success', function (e, data) {
            $('div.success-find strong.counter').text(data.counter);
            $('div.success-find').slideDown();
            window.location.reload();
        });

        $('.form-publish').on('ajax:success', function (e, data) {
            $('div.success-publish').slideDown();
        });

        $('.form-publish-all').on('ajax:success', function (e, data) {
            $('div.success-publish-all').slideDown();
        });

    })
</script>
