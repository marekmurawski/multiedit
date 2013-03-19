
<link href="<?php echo PLUGINS_URI; ?>multiedit/multiedit.css" media="screen" rel="stylesheet" type="text/css" />
<script type="text/javascript" charset="utf-8" src="<?php echo PLUGINS_URI; ?>multiedit/js/helpers.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo PLUGINS_URI; ?>ace/ace_editor.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo PLUGINS_URI; ?>ace/build/src-min/ace.js"></script>
<script>

    $(document).delegate(".multiedit-field", 'change', function() {
        field = $(this);
        progressIndicator = $('#' + field.attr('id') + '-loader');
        progressIndicator.addClass('visible');
        $.ajax({
            url: "<?php echo URL_PUBLIC . ADMIN_DIR; ?>/plugin/multiedit/setvalue",
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
                    }, 300)
                    if (data.hasOwnProperty('datetime') && data.hasOwnProperty('identifier')) {
                        $('#updated_on-' + data.identifier).html(data.datetime).addClass('wasmodified');
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
                    }, 300)
                    $(".multiedit-slugfield").trigger('keyup');

                }
            },
            error: function(data) {
                showMessageBox(dump(data));
            },
            dataType: 'json'
        })
    });


    $(document).delegate("#multiedit-fe-show", 'click', function() {
        $(this).hide();
        document.cookie = 'MEfe=1; path=/';
        target = $('#multipage_item-' +<?php echo $page_id; ?>);
        target.fadeOut('fast', function() {
            $.ajax({
                url: "<?php echo URL_PUBLIC . ADMIN_DIR; ?>/plugin/multiedit/getoneitem/",
                type: 'POST',
                data: {
                    page_id : "<?php echo $page_id; ?>",
                    frontend : "1",
                },
                success: function(data) {
                    target.html(data);
                    $(".multiedit-countchars").trigger('keyup');
                    $(".multiedit-counttags").trigger('keyup');
                    target.fadeIn('fast');
                    $('#multiedit-list').show();
                    $("#multiedit-fe-hide").fadeIn('slow');
                    $('.part_label_tab.active').trigger('click');
                },
                error: function(data) {
                    alert(dump(data));
                }
            })
        })
    })



    $(document).delegate("#multiedit-fe-hide", 'click', function() {
        $(this).hide();
        me_eraseCookie('MEfe');
        target = $('#multipage_item-' +<?php echo $page_id; ?>);
        target.hide();
        $("#multiedit-fe-show").fadeIn('slow');
    });

    $(document).ready(function() {
        if (me_readCookie("MEfe") === '1') {
            //alert("hello again");
            $("#multiedit-fe-show").trigger('click');
        }

    });

</script>
<div id="multiedit-wrapper" class="frontend">
    <?php
    /**
     * Notify ACE plugin to include it's hidden settings div
     */
    if ( Plugin::isEnabled( 'ace' ) ) {
        Observer::notify( 'view_backend_list_plugin', 'ace' );
    }
    ?>
    <div id="multiedit-fe-hide"></div>

    <div id="multiedit-list" style="display: none;">
        <div class="multiedit-item-root multiedit-item" id="multipage_item-<?php echo $page_id; ?>" style="box-shadow: 0px 0px 16px 4px rgba(0,0,0,0.3);">
        </div>
    </div>
    <div id="multiedit-fe-show"></div>
</div>