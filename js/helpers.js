var showpageparts;
var showcollapsed;

function me_createCookie(name, value, days)
{
    if (days)
    {
        var date = new Date( );
        date.setTime(date.getTime( ) + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString( );
    }
    else {
        var expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}
function me_readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++)
    {
        var c = ca[i];
        while (c.charAt(0) === ' ')
            c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0)
            return c.substring(nameEQ.length, c.length);
    }
    return null;
}
function me_eraseCookie(name) {
    me_createCookie(name, "", -1);
}

$(document).delegate(".multiedit-slugifier", 'click', function() {
    id = $(this).attr('rel').split('-', 2)[1];
    source = $('#title-' + id)
    target = $('#slug-' + id);

    oldval = target.val();
    if (oldval != toSlug(source.val())) {
        target.val(toSlug(source.val()));
        target.trigger("change");
        target.trigger("keyup");
    }
});

$(document).delegate(".multiedit-breadcrumber", 'click', function() {
    id = $(this).attr('rel').split('-', 2)[1];
    source = $('#title-' + id)
    target = $('#breadcrumb-' + id);
    oldval = target.val();
    if (oldval != source.val()) {
        target.val(source.val());
        target.trigger("change");
    }
});


$(document).delegate(".multiedit-countchars", 'keyup', function() {
    field = $(this);
    len = field.val().length;
    counterObject = $('#' + field.attr('id') + '-cnt');
    counterObject.html(len);
    if (field.attr('id').substr(0, 4) == 'desc') {
        if ((100 > len) || (len > 180)) {
            counterObject.removeClass('green');
            counterObject.addClass('red');
        }
        else if ((129 < len) && (len < 161)) {
            counterObject.removeClass('red');
            counterObject.addClass('green');
        } else {
            counterObject.removeClass('red');
            counterObject.removeClass('green');
        }
    }
    ;
});


$(document).delegate(".multiedit-counttags", 'keyup', function() {
    field = $(this); // this needs impovement for abc,,,,,sda,,,dsa case
    tmp = field.val();
    tmp = tmp.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
    tmp = tmp.replace(/^,*/, '').replace(/,*$/, '');
    if (tmp.length > 0) {
        len = tmp.split(',').length;
        $('#' + field.attr('id') + '-cnt').html(len);
    }
    else {
        $('#' + field.attr('id') + '-cnt').html('0');
    }
});

$(document).delegate(".multiedit-slugfield", 'keyup', function() {
    field = $(this);
    $('#' + field.attr('id') + '-title').html(field.val());
});

/**
 * Leftclick on part tab
 * Defaults to standard textarea editing field
 * If Ace is installed and checked to be used - embeds Ace editor
 */
$(document).delegate('.part_label_tab', 'click', function(e) {
    target = $(this).attr('data-target');
    shortID = $(this).attr('data-short-id');

    $(this).siblings().removeClass('active');
    $(this).addClass('active');

    $('#' + target).parents('td').find('.partedit_container.visible').removeClass('visible');

    $('#' + target + '-container').addClass('visible');
    $('#' + target).addClass('visible');

    me_createCookie('MEfet', $(this).attr('data-part-name'));

    if (e.ctrlKey) {
        $('.me_pt_' + $(this).attr('data-part-name')).trigger('click');
    }

    /**
     * setup Ace
     */
    if (($('#aceeditor' + 'ME' + shortID).length < 1) && ($('#ace-live-settings').length > 0)) {
        // in backend use height specified in MultiEdit settings
        if ($('#partheight').length > 0) {
            setupEditor('ME' + shortID, $('#' + target + '-toolbar'), $('#' + target), {
                'editorheight': $('#partheight').val(),
                'theme' : 'monokai'
            });
            // hide standard textareas
            $('#' + target).parents('td').find('textarea.partedit').hide();
        } else {
            setupEditor('ME' + shortID, $('#' + target + '-toolbar'), $('#' + target), {
                'theme' : 'monokai'
            });
            // hide standard textareas
            $('#' + target).parents('td').find('textarea.partedit').hide();
        }
    }

});

/**
 * Rightclick on part tab
 * Defaults to standard textarea editing field
 */
$(document).delegate('.part_label_tab', 'contextmenu', function(e) {
    e.preventDefault();
    target = $(this).attr('data-target');
    $(this).siblings().removeClass('active');
    $(this).addClass('active');

    $('#' + target).parents('td').find('.partedit_container.visible').removeClass('visible');

    // removing Aces
    $('#' + target).parent().children('.ace_editor, .ace_options').remove();
    // showing textareas
    $('#' + target).parents('td').find('textarea').show();
    // displaying container
    $('#' + target + '-container').addClass('visible');

    me_createCookie('MEfet', $(this).attr('data-part-name'));

    if (e.ctrlKey) {
        $('.me_pt_' + $(this).attr('data-part-name')).trigger('contextmenu');
    }

});



$(document).delegate('.multiedit-item .reload-item', 'click', function() {
    var meUrl = $('#multiedit-controller-url').attr('data-url');
    showfull = ($(this).hasClass('full')) ? '1' : '0';
    is_frontend = ($(this).attr('data-is-frontend') == '1') ? '1' : '0';

    id = $(this).attr('rel').split('-', 2)[1];
    target = $('#' + $(this).attr('rel'));
    target.fadeTo('fast', 0.3, function() {

        $.ajax({
            url: meUrl + '/getoneitem',
            type: 'POST',
            global: false,
            data: {
                'page_id': id,
                'force_full_view': showfull,
                'frontend': is_frontend
            },
            success: function(data) {
                target.html(data);
                $(".multiedit-countchars").trigger('keyup');
                $(".multiedit-counttags").trigger('keyup');
                target.fadeTo('fast', 1);
                // trigger click to activate Ace
                target.find('.part_label_tab.active').trigger('click');
                //mmShowMessage ('Reloaded item ' + id,'OK');
            }
        });

    });
});

/**
 * Handler for add_page_part button
 */
$(document).delegate(".add_page_part", 'click', function() {
    var meUrl = $('#multiedit-controller-url').attr('data-url');
    var pageid = $(this).attr('rel');
    var newname = window.prompt('New page part name ');
    reloadButton = $('#reload-item' + pageid);
    if (newname === null)
        return false;
    $.ajax({
        url: meUrl + '/add_page_part',
        type: 'POST',
        data: {
            'page_id': pageid,
            'name': newname
        },
        dataType: 'json',
        success: function(data) {
            reloadButton.trigger('click');
            mmShowMessage(data);
        }
    });
});

/**
 * Handler for delete_page_part button
 */
$(document).delegate('.delete_page_part', 'click', function() {
    var meUrl = $('#multiedit-controller-url').attr('data-url');
    var name = $(this).attr('data-name');
    var pageid = $(this).attr('data-page-id');
    if (window.confirm('Are you sure?') === false)
        return false;

    reloadButton = reloadButton = $('#reload-item' + pageid);
    $.ajax({
        url: meUrl + '/delete_page_part',
        type: 'POST',
        data: {
            'page_id': pageid,
            'name': name
        },
        dataType: 'json',
        success: function(data) {
            reloadButton.trigger('click');
            mmShowMessage(data);
        }
    });
});


$(document).delegate('.rename_page_part', 'click', function() {
    var meUrl = $('#multiedit-controller-url').attr('data-url');
    var oldname = $(this).attr('oldname');
    var pageid = $(this).attr('rel');
    var newname = window.prompt('New page part name for ' + oldname, oldname);

    reloadButton = $(this).parents('div.multiedit-item').find('.reload-item');
    if (newname === null)
        return false;

    $.ajax({
        url: meUrl + '/rename_page_part',
        type: 'POST',
        data: {
            'page_id': pageid,
            'old_name': oldname,
            'new_name': newname
        },
        dataType: 'json',
        success: function(data) {
            reloadButton.trigger('click');
            mmShowMessage(data);
        }
    });
});


$(document).delegate(".multiedit-item .header", 'click', function(e) {

    if (e.ctrlKey) {
        target = $(this).parent();
        target.slideUp('normal', function() {
            target.remove();
        });
        return false;
    }

    // if nothing is visible - load full item
    if (($('#showrow1').is(':checked') === false) &&
            ($('#showrow2').is(':checked') === false) &&
            ($('#showrow3').is(':checked') === false) &&
            ($('#showrow4').is(':checked') === false) &&
            ($('#showpageparts').is(':checked') === false) &&
            ($(this).parent().find('tr').length === 0)
            ) {
        $(this).parent().find('span.reload-item.full').trigger('click');
        return false;
    }

    $(this).parent().find('table').toggle();
});


