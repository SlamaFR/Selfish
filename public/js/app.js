function isVisible(elem) {
    return elem.parent().data('visible');
}

function getId(elem) {
    return elem.parent().data('id');
}

$('[data-action="toggle-visibility"]').click(function() {
    let elem = $(this),
        id = getId(elem),
        visible = !isVisible(elem); 

    $.post(baseUrl + "/upload/" + id + "/toggle-visibility", {"_token": csrf_token}).done(function () {
        elem.parent().data('visible', visible);
        elem.html(feather.icons[visible ? 'eye-off' : 'eye'].toSvg());
        $('[data-state="' + id + '"]').html(feather.icons[visible ? 'check-circle' : 'x-circle'].toSvg({
            class: visible ? "text-success" : "text-danger"
        }));
    });
});

$('[data-action="delete"]').click(function() {
    let elem = $(this),
        id = getId(elem); 

    $.post(baseUrl + "/upload/" + id + "/delete", {"_token": csrf_token}).done(function () {
        if ($("#files-table").children().length == 1) {
            location.reload();
        } else {
            elem.parent().parent().parent().remove();
        }
    });
});