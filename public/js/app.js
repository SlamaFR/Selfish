new ClipboardJS('.copy');
feather.replace();

const routes = {
    "upload.delete": "/{mediaCode}/delete",
    "upload.toggle-visibility": "/{mediaCode}/toggle-visibility",
    "mode.toggle": "/mode/{mode}",
    "user.external.sharex": "/user/external/sharex",
    "user.regenerate-token": "/user/regenerate-token",
    "admin.user.regenerate-token": "/admin/user/{userId}/regenerate-token",
    "admin.user.delete": "/admin/user/{userId}/delete",
    "admin.user.promote": "/admin/user/{userId}/promote",
    "admin.user.demote": "/admin/user/{userId}/demote",
    "admin.quotas.recalculate": "/admin/recalculate-quotas",
    "admin.clean-up": "/admin/clean-up",
    "admin.maintenance.toggle": "/admin/toggle-maintenance",
};

let toastElem = $('.toast');
let toast = new bootstrap.Toast(toastElem.get(0), {
    delay: 4000
});
let deleteModal = new bootstrap.Modal($('#delete-modal').get(0));

var mediaSelection = 0;

$('div.alert').not('.alert-important').delay(3000).slideUp(350);

function route(routeName, parameters = []) {
    let route = routes[routeName];
    for (let paremeter of parameters) {
        route = route.replace(/{.*}/, paremeter);
    }
    return baseUrl + route;
}

function dispatchToast(title, message) {
    if (toast._element !== undefined) {
        if (toastElem.is(':visible')) {
            function e() {
                $('.toast-header strong').text(title);
                $('.toast-body').html(message);
                clearTimeout(toast._timeout);
                toast.show();
                toastElem.get(0).removeEventListener('hidden.bs.toast', e);
            }
            toastElem.get(0).addEventListener('hidden.bs.toast', e);
            toast.hide();
            clearTimeout(toast._timeout);
        } else {
            $('.toast-header strong').text(title);
            $('.toast-body').html(message);
            clearTimeout(toast._timeout);
            toast.show();
        }
    }
}

function go(url) {
    location.href = url;
}

async function toggleDarkMode() {
    let currentMode = $("body").attr('data-theme');
    let nextMode = currentMode === 'light' ? 'dark' : 'light';
    let res;

    await $.post(route("mode.toggle", [nextMode]), {
        "_token": csrf_token
    }).done(function (response) {
        res = response;
        $("body").attr('data-theme', nextMode);
        $('.btn-outline-' + nextMode)
            .removeClass('btn-outline-' + nextMode)
            .addClass('btn-outline-' + currentMode);
        $('.btn-' + nextMode)
            .removeClass('btn-' + nextMode)
            .addClass('btn-' + currentMode);
    });
    return res;
}

$('.hover-to-see').hover(function () {
    $(this).attr('type', 'text');
}, function () {
    $(this).attr('type', 'password');
});

$('.media-row').contextmenu(function () {
    let elem = $(this);
    if (elem.hasClass('table-danger')) {
        elem.removeClass('table-danger');
        mediaSelection--;
    } else {
        elem.addClass('table-danger');
        mediaSelection++;
    }

    $('#delete-btn').attr('disabled', mediaSelection == 0);
    return false;
});

$('#disk_max-quota_default').click(function () {
    $('#disk_custom-max-quota').attr('disabled', true);
    $('#disk_custom-max-quota_unit').attr('disabled', true);
});

$('#disk_max-quota_custom').click(function () {
    $('#disk_custom-max-quota').attr('disabled', false);
    $('#disk_custom-max-quota_unit').attr('disabled', false);
});

$('#app_captcha_disabled').click(function () {
    $('[name="key_captcha_site"]').attr('disabled', true);
    $('[name="key_captcha_private"]').attr('disabled', true);
});

$('#app_captcha_enabled').click(function () {
    $('[name="key_captcha_site"]').attr('disabled', false);
    $('[name="key_captcha_private"]').attr('disabled', false);
});

$('[data-action="regenerate-token"]').click(function () {
    let elem = $(this),
        id = $(this).data('id');
    $.post(id === undefined ? route("user.regenerate-token") : route("admin.user.regenerate-token", [id]), {
        _token: csrf_token
    }).done(function (response) {
        $('#personnal-token').val(response.token);
        navigator.clipboard.writeText(response.token);
        dispatchToast(response.title, response.message);
    }).fail(function (response) {
        dispatchToast(response.responseJSON.title, response.responseJSON.message);
    });
});

$('[data-action="toggle-visibility"]').click(function () {
    let elem = $(this),
        id = elem.parent().data('id');

    $.post(route("upload.toggle-visibility", [id]), {
        "_token": csrf_token
    }).done(function (response) {
        elem.parent().data('visible', response.visible);
        elem.html(feather.icons[response.btnIcon].toSvg());
        $('[data-state="' + id + '"]').html(feather.icons[response.stateIcon].toSvg({
            class: response.stateColor
        }));
        dispatchToast(response.title, response.message);
    }).fail(function (response) {
        dispatchToast(response.responseJSON.title, response.responseJSON.message);
    });
});

$('[data-action="download-sharex"]').click(function () {
    go(route("user.external.sharex"));
});

function removeMedia(id, elem) {
    $.post(route("upload.delete", [id]), {
        "_token": csrf_token
    }).done(function (response) {
        children = $("#files-table").children().length;
        if (children == 1 || children == mediaSelection) {
            location.reload();
        } else {
            elem.parent().parent().parent().fadeOut(350, function () {
                $(this).remove();
            });
            updateNavbarQuota(response);
        }
        mediaSelection = Math.max(mediaSelection - 1, 0);
        $('#delete-btn').attr('disabled', mediaSelection == 0);
    }).fail(function (response) {
        dispatchToast(response.responseJSON.title, response.responseJSON.message);
    });
}

function updateNavbarQuota(response) {
    if (!response.unlimited_quota) {
        $('#navbar_quota_caption').text(response.new_quota + " / " + response.max_quota);
        let bar = $('#navbar_quota_progress');
        bar.css('width', response.new_usage * 100 + "%");
        bar.removeClass();
        bar.addClass('progress-bar');
        if (response.new_usage < .6) {
            bar.addClass('bg-success');
        } else if (response.new_usage < .85) {
            bar.addClass('bg-warning');
        } else {
            bar.addClass('bg-danger');
        }
    }
}

$('[data-action="delete-media"]').click(function () {
    let elem = $(this),
        id = elem.parent().data('id');
    removeMedia(id, elem);
});

$('[data-action="delete-media-confirm"]').click(function () {
    let elem = $(this),
        id = elem.parent().data('id');
    let button = $('.btn-confirm');
    deleteModal.show();
    button.on('click', function () {
        removeMedia(id, elem);
        button.off('click');
        go(baseUrl);
    });
});

$('[data-action="delete-media-selection"]').click(function () {
    $('.table-danger').each(function () {
        let elem = $(this);
        let deleteBtn = elem.find('[data-action="delete-media"]');
        removeMedia(deleteBtn.parent().data('id'), deleteBtn);
    });
});

$('[data-action="set-display-default"]').click(function () {
    $('[id$=".default"]').prop('checked', true);
});

$('[data-action="toggle-dark-mode-text"]').click(async function () {
    let newMode = await toggleDarkMode();
    $(this).html(feather.icons[newMode.next_mode_icon].toSvg() + newMode.next_mode_name);
});

$('[data-action="toggle-dark-mode"]').click(function () {
    let newMode = toggleDarkMode();
    $(this).html(feather.icons[newMode.next_mode_icon].toSvg());
});

$('[data-action="toggle-admin"]').click(function () {
    let elem = $(this),
        id = elem.parent().data('id'),
        admin = elem.data('admin');
    $.post(route(admin ? "admin.user.demote" : "admin.user.promote", [id]), {
        _token: csrf_token
    }).done(function (response) {
        elem.data('admin', !admin)
            .html(feather.icons[admin ? 'shield' : 'shield-off'].toSvg());
        $('[data-state="' + id + '"]')
            .removeClass(admin ? 'text-success' : 'text-danger')
            .addClass(admin ? 'text-danger' : 'text-success')
            .html(feather.icons[admin ? 'x-circle' : 'check-circle'].toSvg());
        dispatchToast(response.title, response.message);
    }).fail(function (response) {
        dispatchToast(response.responseJSON.title, response.responseJSON.message);
    });
});

$('[data-action="delete-user"]').click(function () {
    let elem = $(this),
        id = elem.parent().data('id');
    let button = $('.btn-confirm');
    deleteModal.show();
    button.on('click', function () {
        button.off('click');
        $.post(route("admin.user.delete", [id]), {
            _token: csrf_token
        }).done(function (response) {
            elem.parent().parent().parent().fadeOut(350, function () {
                $(this).remove();
            });
            dispatchToast(response.title, response.message);
            $('#user-count').text(response.count);
        }).fail(function (response) {
            dispatchToast(response.responseJSON.title, response.responseJSON.message);
        })
    });
});

$('[data-action="recalculate-quotas"]').click(function () {
    $.post(route("admin.quotas.recalculate"), {
        _token: csrf_token
    }).done(function (response) {
        dispatchToast(response.title, response.message);
        $('#total-usage').text(response.total_usage);
        updateNavbarQuota(response);
    }).fail(function (response) {
        dispatchToast(response.responseJSON.title, response.responseJSON.message);
    });
});

$('[data-action="clean-up"]').click(function () {
    $.post(route("admin.clean-up"), {
        _token: csrf_token
    }).done(function (response) {
        $('#file-count').text(response.new_file_count);
        dispatchToast(response.title, response.message);
    }).fail(function (response) {
        dispatchToast(response.responseJSON.title, response.responseJSON.message);
    });
});

$('[data-action="toggle-maintenance"]').click(function () {
    let elem = $(this);
    $.post(route("admin.maintenance.toggle"), {
        _token: csrf_token
    }).done(function (response) {
        if (response.maintenance) {
            elem.addClass("active");
            $('.navbar').css('border-bottom', '2px solid #dc3545');
        } else {
            elem.removeClass("active");
            $('.navbar').css('border-bottom', 'none');
        }
        dispatchToast(response.title, response.message);
    }).fail(function (response) {
        dispatchToast(response.responseJSON.title, response.responseJSON.message);
    });
});
