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
    if (($('#aceeditorME' + target).length < 1) && ($('#ace-live-settings').length > 0)) {
        // in backend use height specified in MultiEdit settings
        if ($('#partheight').length > 0) {
            setupEditor('ME' + target, $('#' + target + '-toolbar'), $('#' + target), {
                'editorheight': $('#partheight').val()
            });
            // hide standard textareas
            $('#' + target).parents('td').find('textarea.partedit').hide();
        } else {
            setupEditor('ME' + target, $('#' + target + '-toolbar'), $('#' + target));
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
    // alert(target);
    $(this).siblings().removeClass('active');
    $(this).addClass('active');

    $('#' + target).parents('td').find('.partedit_container.visible').removeClass('visible');

    // removing Aces
    $('#'+target).parent().children('.ace_editor, .ace_options').remove();
    // showing textareas
    $('#' + target).parents('td').find('textarea').show();
    // displaying container
    $('#' + target + '-container').addClass('visible');

    me_createCookie('MEfet', $(this).attr('data-part-name'));

    if (e.ctrlKey) {
        $('.me_pt_' + $(this).attr('data-part-name')).trigger('contextmenu');
    }

});