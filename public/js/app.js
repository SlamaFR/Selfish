new ClipboardJS('.copy');

let toastElem = $('.toast');
let modalElem = $('.modal');
let toast = new bootstrap.Toast(toastElem.get(0), {
    delay: 5000
});
let modal = new bootstrap.Modal(modalElem.get(0));

$('div.alert').not('.alert-important').delay(3000).slideUp(350);

function isVisible(elem) {
    return elem.parent().data('visible');
}

function getId(elem) {
    return elem.parent().data('id');
}

function getName(elem) {
    return elem.parent().data('name');
}

function remove(id, elem) {
    $.post(baseUrl + "/" + id + "/delete", {
        "_token": csrf_token
    }).done(function () {
        if ($("#files-table").children().length == 1) {
            location.reload();
        } else {
            elem.parent().parent().parent().fadeOut(350, function() {
                $(this).remove();
            });
        }
    });
}

function toggleDarkMode() {
    let currentMode = $("body").attr('data-theme');
    let nextMode = currentMode === 'light' ? 'dark' : 'light';

    $("body").attr('data-theme', nextMode);
    $('.btn-outline-' + nextMode)
        .removeClass('btn-outline-' + nextMode)
        .addClass('btn-outline-' + currentMode);
    $('.btn-' + nextMode)
        .removeClass('btn-' + nextMode)
        .addClass('btn-' + currentMode);

    $.post('/mode/' + nextMode, {"_token": csrf_token});
    return nextMode;
}

$('.hover-to-see').hover(function () {
    $(this).attr('type', 'text');
}, function () {
    $(this).attr('type', 'password');
});

$('#regenerate-token').click(function () {
    $.post(baseUrl + '/user/regenerate-token', {_token: csrf_token}).done(function (token) {
        $('#personnal-token').val(token);
        navigator.clipboard.writeText(token);
        $('.toast-body').html("Your personnal access token has been regenerated and copied to clipboard.");
        if (toast._element !== undefined) {
            if (toastElem.is(':visible')) clearTimeout(toast._timeout);
            toast.show();
        }
    })
});

$('[data-action="toggle-visibility"]').click(function () {
    let elem = $(this),
        id = getId(elem),
        visible = !isVisible(elem);

    $.post(baseUrl + "/" + id + "/toggle-visibility", {
        "_token": csrf_token
    }).done(function () {
        elem.parent().data('visible', visible);
        elem.html(feather.icons[visible ? 'eye-off' : 'eye'].toSvg());
        $('[data-state="' + id + '"]').html(feather.icons[visible ? 'check-circle' : 'x-circle'].toSvg({
            class: visible ? "text-success" : "text-danger"
        }));
        $('.toast-body').html("Media <strong>" + getName(elem) + "</strong> is now " + (visible ? "visible." : "invisible."));
        if (toast._element !== undefined) {
            if (toastElem.is(':visible')) clearTimeout(toast._timeout);
            toast.show();
        }
    });
});

$('[data-action="delete"]').click(function () {
    let elem = $(this),
        id = getId(elem);
    remove(id, elem);
});

$('[data-action="delete-confirm"]').click(function () {
    let elem = $(this),
        id = getId(elem);
    let button = $('.btn-confirm');
    modal.show();
    button.on('click', function () {
        remove(id, elem);
        button.off('click');
        location.href = baseUrl;
    })
});

$('[data-action="set-display-default"]').click(function () {
    $('[id$=default]').prop('checked', true);
});

$('[data-action="toggle-dark-mode-text"]').click(function (e) {
    let newMode = toggleDarkMode();
    $(this).html(feather.icons[newMode === 'dark' ? 'sun' : 'moon'].toSvg() + (newMode === 'dark' ? 'Light mode' : 'Dark mode'));
});

$('[data-action="toggle-dark-mode"]').click(function () {
    let newMode = toggleDarkMode();
    $(this).html(feather.icons[newMode === 'dark' ? 'sun' : 'moon'].toSvg());
});
