/**
 * Settings
 */

// automatically detects "markdown" and "textile" filters
var multiedit_ace_autodetect = true;

// editor theme
var multiedit_ace_theme = 'tomorrow_night_bright';

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
    sr = $('#title-' + id)
    tgt = $('#slug-' + id);

    ov = tgt.val();
    if (ov != toSlug(sr.val())) {
        tgt.val(toSlug(sr.val()));
        tgt.trigger("change");
        tgt.trigger("keyup");
    }
});

$(document).delegate(".multiedit-breadcrumber", 'click', function() {
    id = $(this).attr('rel').split('-', 2)[1];
    sr = $('#title-' + id)
    tgt = $('#breadcrumb-' + id);
    ov = tgt.val();
    if (ov != sr.val()) {
        tgt.val(sr.val());
        tgt.trigger("change");
    }
});


$(document).delegate(".multiedit-countchars", 'keyup', function() {
    field = $(this);
    len = field.val().length;
    cntObj = $('#' + field.attr('id') + '-cnt');
    cntObj.html(len);
    if (field.attr('id').substr(0, 4) == 'desc') {
        if ((100 > len) || (len > 180)) {
            cntObj.removeClass('green');
            cntObj.addClass('red');
        }
        else if ((129 < len) && (len < 161)) {
            cntObj.removeClass('red');
            cntObj.addClass('green');
        } else {
            cntObj.removeClass('red');
            cntObj.removeClass('green');
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
    tgt = $(this).attr('data-target');
    shID = $(this).attr('data-short-id');
    fSel = $(this).attr('data-filter-select');

    $(this).siblings().removeClass('active');
    $(this).addClass('active');

    $('#' + tgt).parents('td').find('.partedit_container.visible').removeClass('visible');

    $('#' + tgt + '-container').addClass('visible');
    $('#' + tgt).addClass('visible');

    me_createCookie('MEfet', $(this).attr('data-part-name'));

    if (e.ctrlKey) {
        $('.me_pt_' + $(this).attr('data-part-name')).trigger('click');
    }

    /**
     * setup Ace
     */
    if (($('#aceeditor' + 'ME' + shID).length < 1) && ($('#ace-live-settings').length > 0)) {
        // in backend use height specified in MultiEdit settings
        $options = {
            'editorheight': $('#partheight').val(),
            'theme': multiedit_ace_theme
        };

        // markdown and textile autodetection
        if (multiedit_ace_autodetect) {
            selectBoxMode = $('#' + fSel).val();
            if ((selectBoxMode === 'textile') || (selectBoxMode === 'markdown'))
                $.extend($options, {
                    mode: selectBoxMode
                });
        }
        setupEditor('ME' + shID, $('#' + tgt + '-toolbar'), $('#' + tgt), $options);
        // hide standard textareas
        $('#' + tgt).parents('td').find('textarea.partedit').hide();
    }

});

/**
 * Rightclick on part tab
 * Defaults to standard textarea editing field
 */
$(document).delegate('.part_label_tab', 'contextmenu', function(e) {
    e.preventDefault();
    tgt = $(this).attr('data-target');
    $(this).siblings().removeClass('active');
    $(this).addClass('active');

    $('#' + tgt).parents('td').find('.partedit_container.visible').removeClass('visible');

    // removing Aces
    $('#' + tgt).parent().children('.ace_editor, .ace_options').remove();
    // showing textareas
    $('#' + tgt).parents('td').find('textarea').show();
    // displaying container
    $('#' + tgt + '-container').addClass('visible');

    me_createCookie('MEfet', $(this).attr('data-part-name'));

    if (e.ctrlKey) {
        $('.me_pt_' + $(this).attr('data-part-name')).trigger('contextmenu');
    }

});



$(document).delegate('.multiedit-item .reload-item', 'click', function() {
    var meUrl = $('#multiedit-controller-url').attr('data-url');
    showfull = ($(this).hasClass('full')) ? '1' : '0';
    isFE = ($(this).attr('data-is-frontend') == '1') ? '1' : '0';

    id = $(this).attr('rel').split('-', 2)[1];
    tgt = $('#' + $(this).attr('rel'));
    tgt.fadeTo('fast', 0.3, function() {

        $.ajax({
            url: meUrl + '/getoneitem',
            type: 'POST',
            global: false,
            data: {
                'page_id': id,
                'force_full_view': showfull,
                'frontend': isFE
            },
            success: function(data) {
                tgt.html(data);
                $(".multiedit-countchars").trigger('keyup');
                $(".multiedit-counttags").trigger('keyup');
                tgt.fadeTo('fast', 1);
                // trigger click to activate Ace
                tgt.find('.part_label_tab.active').trigger('click');
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
    relBtn = $('#reload-item' + pageid);
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
            relBtn.trigger('click');
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

    relBtn = relBtn = $('#reload-item' + pageid);
    $.ajax({
        url: meUrl + '/delete_page_part',
        type: 'POST',
        data: {
            'page_id': pageid,
            'name': name
        },
        dataType: 'json',
        success: function(data) {
            relBtn.trigger('click');
            mmShowMessage(data);
        }
    });
});


$(document).delegate('.rename_page_part', 'click', function() {
    var meUrl = $('#multiedit-controller-url').attr('data-url');
    var oldname = $(this).attr('oldname');
    var pageid = $(this).attr('rel');
    var newname = window.prompt('New page part name for ' + oldname, oldname);

    relBtn = $(this).parents('div.multiedit-item').find('.reload-item');
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
            relBtn.trigger('click');
            mmShowMessage(data);
        }
    });
});


$(document).delegate(".multiedit-item .header", 'click', function(e) {

    isFE = ($(this).parents('div.multiedit-item').find('#multiedit-controller-url').length > 0);

    if (e.ctrlKey && !isFE) {
        tgt = $(this).parent();
        tgt.slideUp('normal', function() {
            tgt.remove();
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

$(document).delegate(".multiedit-item .header", 'contextmenu', function(e) {
    e.preventDefault();
    isFE = ($(this).parents('div.multiedit-item').find('#multiedit-controller-url').length > 0);
    if (isFE) {
        var state = $(this).data('state');
        if (state) {
            $('#multiedit-wrapper').css('opacity','1');
        } else {
            $('#multiedit-wrapper').css('opacity','.33');
        }
        $(this).data("state", !state);
    }
});


