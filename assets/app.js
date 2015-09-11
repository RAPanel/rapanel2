/**
 * Created by semyonchick on 10.09.2015.
 */
$(function () {
    $('body')
        .on('click', 'td.editable', function () {
            if ($(':input', this).length) return;
            var el = $('div', this);
            var val = el.html();
            var data = el.data();
            var textarea = $('<textarea>').css({margin: '-8px 0'}).attr({rows: 1}).addClass('form-control').val(val).on('change live focusout', function () {
                if (el.data('name')) data[el.data('name')] = val = $(this).val();
                else data.translation = val = $(this).val();
                $.post('save', data);
                el.html(val);
            });
            $('div', this).html(textarea);
            textarea.focus();
            return false;
        })
        .on('click', '.changeStatus', function () {
            var a = $(this);
            var el = $('i', a);
            $.get(this.href, {}, function (data) {
                if (data) {
                    el.toggleClass('fa-toggle-on').toggleClass('fa-toggle-off');
                    var to = el.hasClass('fa-toggle-on') ? 1 : 0;
                    var from = to ? 0 : 1;
                    a.attr('href').replace('status=' + from, 'status=' + to)
                }
            });
            return false;
        });
});