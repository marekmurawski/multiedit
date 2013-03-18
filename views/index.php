<?php
/* Security measure */
if ( !defined( 'IN_CMS' ) ) {
    exit();
}
?>
<?php if ( Plugin::isEnabled( 'ace' ) ): ?>
    <script type="text/javascript" charset="utf-8" src="<?php echo PLUGINS_URI; ?>ace/ace_editor.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo PLUGINS_URI; ?>ace/build/src-min/ace.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo PLUGINS_URI; ?>ace/js/jquery.cookie.js"></script>
<?php endif; ?>
<div id="multiedit-wrapper">
    <div id="multiedit-header">
        <?php
        echo $pagesList;
        ?>
    </div>
    <img id="multiedit-list-preloader" src="<?php echo PLUGINS_URI . 'multiedit/icons/progress-big.gif'; ?>"/>
    <div id="multiedit-list">
        <?php
        echo $rootItem;
        ?>
        <?php
        echo $itemsList;
        ?>
    </div>
</div>
<script>

    var aceEmbedLimit = 40;

    var setMEcookie = function() {
        if ($('#showrow1').is(':checked')) {
            showrow1 = '1';
        } else {
            showrow1 = '0';
        }
        if ($('#showrow2').is(':checked')) {
            showrow2 = '1';
        } else {
            showrow2 = '0';
        }
        if ($('#showrow3').is(':checked')) {
            showrow3 = '1';
        } else {
            showrow3 = '0';
        }
        if ($('#showrow4').is(':checked')) {
            showrow4 = '1';
        } else {
            showrow4 = '0';
        }
        if ($('#showpageparts').is(':checked')) {
            showpageparts = '1';
        } else {
            showpageparts = '0';
        }
        if ($('#useace').is(':checked')) {
            useace = '1';
        } else {
            useace = '0';
        }
        pagepartheight = $('#partheight').val();

        var theCookie = showrow1 + '|' + showrow2 + '|' + showrow3 + '|' + showrow4 + '|' + showpageparts + '|' + useace + '|' + pagepartheight;
        me_createCookie('MEdit', theCookie);
    };

    $(document).ready(function() { // @todo: change counters to be initially PHP processed - faster

        $(".multiedit-countchars").trigger('keyup');
        $(".multiedit-counttags").trigger('keyup');

        /**
         * Use Embed Ace Syntax highlighter
         * if less than 20 items is displayed
         */
        if (($('.multiedit-item').length < aceEmbedLimit) && $('#useace').is(':checked')) {
            $('.part_label_tab.active').trigger('click');
        }
    });

    $(".multiedit-items-select").live('change', function() {

        setMEcookie();
        if ($(this).val() == '0')
            return false;
        $('#multiedit-list').fadeOut('fast', function() {
            $('#multiedit-list-preloader').addClass('preloading');
            var request = $.ajax({
                url: "<?php echo get_url( 'plugin/multiedit/getsubpages/' ); ?>"
                        + $('#multiedit-pageslist').val() + '/' +
                        $("#multiedit-pageslist-sorting").val() + '/' +
                        $("#multiedit-pageslist-order").val() + '/' +
                        showpageparts + '/'
                        ,
                type: 'get',
                success: function(data) {
                    $('#multiedit-list').html(data);

                    $(".multiedit-countchars").trigger('keyup');
                    $(".multiedit-counttags").trigger('keyup');
                    $('#multiedit-list-preloader').removeClass('preloading');
                    $('#multiedit-list').fadeIn('fast');
                    /**
                     * Use Embed Ace Syntax highlighter
                     * if less than 20 items is displayed
                     */
                    if (($('.multiedit-item').length < aceEmbedLimit) && $('#useace').is(':checked')) {
                        $('.part_label_tab.active').trigger('click');
                    }
                },
                error: function(data) {
                    alert(dump(data));
                }
            })
        })

    });

    $('.rename_page_part').live('click', function() {

        var oldname = $(this).attr('oldname');
        var pageid = $(this).attr('rel');
        var newname = window.prompt('<?php echo __( 'New page part name for "' ); ?>' + oldname + '"', oldname);

        reloadButton = $(this).parents('div.multiedit-item').find('.reload-item');
        if (newname === null) { /* showMessageBox ('Cancelled page part name change','error'); */
            return false;
        }
        if (newname.trim().length === 0) {
            showMessageBox('No name specified', 'error');
            return false;
        }
        if (newname.trim() === $(this).html().trim()) {
            showMessageBox('Same name specified', 'error');
            return false;
        }
        $.ajax({
            url: "<?php echo get_url( 'plugin/multiedit/rename_page_part' ); ?>",
            type: 'POST',
            data: {
                'page_id': pageid,
                'old_name': oldname,
                'new_name': newname
            },
            dataType: 'json',
            success: function(data) {
                reloadButton.trigger('click');
                showMessageBox(data.message, data.status);
            },
            error: function(data) {
                reloadButton.trigger('click');
                showMessageBox(data.message, data.status);

            }
        });
    });

    $('.delete_page_part').live('click', function() {

        var name = $(this).attr('data-name');
        var pageid = $(this).attr('data-page-id');
        if (window.confirm('<?php echo __( 'Are you sure?' ); ?>') === false)
            return false;

        reloadButton = reloadButton = $('#reload-item' + pageid);
        $.ajax({
            url: "<?php echo get_url( 'plugin/multiedit/delete_page_part' ); ?>",
            type: 'POST',
            data: {
                'page_id': pageid,
                'name': name
            },
            dataType: 'json',
            success: function(data) {
                reloadButton.trigger('click');

            },
            error: function(data) {
                reloadButton.trigger('click');

            }
        });
    });


    $(document).delegate(".add_page_part", 'click', function() {
        var pageid = $(this).attr('rel');
        var newname = window.prompt('New page part name ');
        reloadButton = $('#reload-item' + pageid);
        if (newname === null) { /* showMessageBox ('Cancelled page part name change','error'); */
            return false;
        }
        $.ajax({
            url: "<?php echo get_url( 'plugin/multiedit/add_page_part' ); ?>",
            type: 'POST',
            data: {
                'page_id': pageid,
                'name': newname
            },
            dataType: 'json',
            success: function(data) {
                // reloadButton.hide();
                reloadButton.trigger('click');
                // showMessageBox(data.message, data.status);
            },
            error: function(data) {
                //reloadButton.hide();
                reloadButton.trigger('click');
                // showMessageBox(data.message, data.status);
            }
        });
    });

    $('.multiedit-delete-field').live('click', function() {

        var fieldname = $(this).attr('data-field-name');
        var confirm = window.confirm('<?php echo __( 'Are you ABSOLUTELY sure you want to delete field' ); ?>' +
                '\n' + '\n === ' + fieldname + ' === ??? \n' + '\n' +
                '<?php echo __( 'Deleting this field will permanently erase ALL data in this field in ALL pages!' ); ?>');
//var newname = window.prompt

        if (confirm !== true) {
            showMessageBox('Cancelled field deletion', 'OK');
            return false;
        }
        else {
            $.ajax({
                url: "<?php echo get_url( 'plugin/multiedit/field_delete' ); ?>",
                type: 'POST',
                data: {
                    'field_name': fieldname
                },
                dataType: 'json',
                success: function(data) {
                    //reloadButton.trigger('click');
                    showMessageBox(data.message, data.status);
                    if (data.status === 'OK')
                        $("#reload-list").trigger('click');
                },
                error: function(data) {
                    //reloadButton.trigger('click');
                    showMessageBox(data.message, data.status);
                }
            })
        }
    });

    $('#multiedit-add-field').live('click', function() {

        var template_id = $('#multiedit-add-field-template').val();
        var newname = window.prompt('<?php echo __( 'Specify new field name ' ); ?>');

        $.ajax({
            url: "<?php echo get_url( 'plugin/multiedit/field_add' ); ?>",
            type: 'POST',
            data: {
                'template_id': template_id,
                'name': newname
            },
            dataType: 'json',
            success: function(data) {
                //reloadButton.trigger('click');
                showMessageBox(data.message, data.status);
                if (data.status === 'OK')
                    $("#reload-list").trigger('click');
            },
            error: function(data) {
                //reloadButton.trigger('click');
                showMessageBox(data.message, data.status);
            }
        })
    });

    $('.multiedit-rename-field').live('click', function() {

        var fieldname = $(this).attr('data-field-name');
        var newname = window.prompt('<?php echo __( 'Specify new name for field ' ); ?>' + fieldname);

        if (newname.trim()) {
            $.ajax({
                url: "<?php echo get_url( 'plugin/multiedit/field_rename' ); ?>",
                type: 'POST',
                data: {
                    'field_name': fieldname,
                    'field_new_name': newname
                },
                dataType: 'json',
                success: function(data) {
                    //reloadButton.trigger('click');
                    showMessageBox(data.message, data.status);
                    if (data.status === 'OK')
                        $("#reload-list").trigger('click');
                },
                error: function(data) {
                    //reloadButton.trigger('click');
                    showMessageBox(data.message, data.status);
                }
            });
        }
    });

    $("#reload-list").live('click', function() {
        $('#multiedit-pageslist').trigger('change');
    });


    $("#partheight").live('change', function() {
        $height = $(this).val();
        setMEcookie();
        $('.partedit').css('height', $height + "px");
        $('.ace_editor').css('height', $height + "px");
        $('.ace_resize_btn').trigger('click');
    });


    $(".reload-item").live('click', function() {
        if ($(this).hasClass('full')) {
            showfull = '/1';
        } else
            showfull = '/0';
        id = $(this).attr('rel').split('-', 2)[1];
        target = $('#' + $(this).attr('rel'));
        target.fadeTo('fast', 0.3, function() {
            $.get("<?php echo get_url( 'plugin/multiedit/getonepage/' ); ?>" + id + '/1/1/0' + showfull,
                    function(data) {
                        target.html(data);
                        $(".multiedit-countchars").trigger('keyup');
                        $(".multiedit-counttags").trigger('keyup');

                        target.fadeTo('fast', 1);

                        // trigger click to activate Ace
                        target.find('.part_label_tab.active').trigger('click');

                        //showMessageBox ('Reloaded item ' + id,'OK');
                    });
        });
    });

    $(".multiedit-item .header").live('click', function(e) {

        if (e.ctrlKey) {
            target = $(this).parent();
            target.fadeOut('normal', function() {
                target.remove();
            });
            return false;
        }

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

    $(".multiedit-field").live('change', function() {
        field = $(this);
        progressIndicator = $('#' + field.attr('id') + '-loader');
        progressIndicator.addClass('visible');
        $.ajax({
            url: "<?php echo get_url( 'plugin/multiedit/setvalue/' ); ?>",
            type: 'post',
            data: {
                item: field.attr('name'),
                value: field.val()
            },
            success: function(data) {
                if (data.status == 'OK') {
                    field.removeClass('error');
                    field.addClass('success');

                    showMessageBox(data.message, data.status);
                    setTimeout(function() {
                        progressIndicator.removeClass('visible');
                    }, 300);
                    if (data.hasOwnProperty('datetime') && data.hasOwnProperty('identifier')) {
                        $('#updated_on-' + data.identifier).html(data.datetime).addClass('wasmodified');
                    }
                    if (data.hasOwnProperty('reloaditem') && data.hasOwnProperty('identifier')) {
                        $('#reload-item' + data.identifier).trigger('click');
                    }
                    // change status if page has expired
                    if (data.hasOwnProperty('setstatus') && data.hasOwnProperty('identifier')) {
                        $('#status_id-' + data.identifier).val(data.setstatus);
                        indicator = $('#status-indicator-' + data.identifier);
                        indicator.removeClass('status-1 status-10 status-100 status-101 status-200');
                        indicator.addClass('status-' + data.setstatus);
                    }
                    // status change management @todo DRY status change
                    if (field.hasClass('status-select')) {
                        indicator = $('#' + field.attr('rel'));
                        indicator.removeClass('status-1 status-10 status-100 status-101 status-200');
                        indicator.addClass('status-' + field.val());
                    }

                } else {
                    field.removeClass('success');
                    field.addClass('error');

                    field.val(data.oldvalue);
                    showMessageBox(data.message, data.status);
                    setTimeout(function() {
                        progressIndicator.removeClass('visible');
                    }, 300);
                    $(".slugfield").trigger('keyup');

                }
            },
            error: function(data) {
                showMessageBox(dump(data));
            },
            dataType: 'json'
        });
    });



</script>

