Array.prototype.diff = function(a) {
    return this.filter(function(i) {return a.indexOf(i) < 0;});
};

function chosenSortable(query, options) {
    var selected;
    $(query).on('chosen:ready', function (evt) {
        selected = $(evt.target).val() ? $(evt.target).val() : [];
        $(".chosen-choices").sortable({
            handle: "span",
            start: function (event, ui) {
                ui.item.startPosition = ui.item.index();
            },
            stop: function (event, ui) {
                var select = $(evt.target), el;
                $(':selected', select).each(function (i) {
                    if (i == ui.item.startPosition) el = $(this);
                });

                var newEl = el.clone();
                if (el.is(':selected')) {
                    newEl.attr("selected", "selected");
                }
                el.remove();

                if (ui.item.index() === 0)
                    select.prepend(newEl);
                else
                    $(':selected', select).each(function (i) {
                        if (i == ui.item.index() - 1) el = $(this).after(newEl);
                    });
                select.trigger('chosen:updated');
            }
        });
    }).on('change', function (evt) {
        if($(evt.target).val())
            $(':selected', evt.target).eq(selected.diff($(evt.target).val())).each(function () {
                $(this).attr("selected", "selected").appendTo(evt.target);
                $(evt.target).trigger('chosen:updated');
            });

        selected = $(evt.target).val() ? $(evt.target).val() : [];

    }).chosen(options);
}