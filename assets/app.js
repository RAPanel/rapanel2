/**
 * Created by semyonchick on 10.09.2015.
 */
$(function () {
    $('[data-toggle="tooltip"]').tooltip();

    $('[href^="/rapanel/table/view?id="]').click('click', function(){
        $(this).attr('target', '_blank').blur();
    });

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
                    if(to) a.attr('href').title('Скрыть');
                    else a.attr('href').title('Отобразить');

                }
            });
            return false;
        });

    $( ".sortableTable tbody" ).sortable({
        helper: function(e, tr)
        {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index)
            {
                // Set helper cell sizes to match the original sizes
                $(this).width($originals.eq(index).outerWidth());
            });
            return $helper;
        },
        stop: function(e, ui){
            var el = ui.item;
            $.get('move', {id:el.data('key'), prev: el.prev('[data-key]').data('key'), next: el.next('[data-key]').data('key')});
        }
    });
});