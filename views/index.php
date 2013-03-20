<?php
/* Security measure */
if ( !defined( 'IN_CMS' ) ) {
    exit();
}
?>
<h1>MultiEdit</h1>
<?php if ( Plugin::isEnabled( 'ace' ) ): ?>
    <script type="text/javascript" charset="utf-8" src="<?php echo PLUGINS_URI; ?>ace/ace_editor.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo PLUGINS_URI; ?>ace/build/src-min/ace.js"></script>
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

        showrow1 = ($('#showrow1').is(':checked')) ? '1' : '0';
        showrow2 = ($('#showrow2').is(':checked')) ? '1' : '0';
        showrow3 = ($('#showrow3').is(':checked')) ? '1' : '0';
        showrow4 = ($('#showrow4').is(':checked')) ? '1' : '0';
        showpageparts = ($('#showpageparts').is(':checked')) ? '1' : '0';
        useace = ($('#useace').is(':checked')) ? '1' : '0';
        pagepartheight = parseInt($('#partheight').val());

        var theCookie = showrow1 + '|' + showrow2 + '|' + showrow3 + '|' + showrow4 + '|' + showpageparts + '|' + useace + '|' + pagepartheight;
        me_createCookie('MEdit', theCookie);
    };

    $(document).ready(function() { // @todo: change counters to be initially PHP processed - faster

        $(".multiedit-countchars").trigger('keyup');
        $(".multiedit-counttags").trigger('keyup');

        /**
         * Use Embed Ace Syntax highlighter
         * if less than {aceEmbedLimit} items is displayed
         */
        if (($('.multiedit-item').length < aceEmbedLimit) && $('#useace').is(':checked')) {
            $('.part_label_tab.active').trigger('click');
        }
    });

$(document).delegate(".multiedit-header-field",'change', function() {

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
                global: false,
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
            });
        });

    });




$(document).delegate("#partheight",'change', function() {
        $height = $(this).val();
        setMEcookie();
        $('.partedit').css('height', $height + "px");
        // Ace-backend specific
        $('.ace_editor').css('height', $height + "px");
        $('.ace_resize_tb').trigger('click');
    });




$(document).delegate('.multiedit-delete-field','click', function() {

        var fieldname = $(this).attr('data-field-name');
        var confirm = window.confirm('<?php echo __( 'Are you ABSOLUTELY sure you want to delete field' ); ?>' +
                '\n' + '\n === ' + fieldname + ' === ??? \n' + '\n' +
                '<?php echo __( 'Deleting this field will permanently erase ALL data in this field in ALL pages!' ); ?>');
        if (confirm !== true) {
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
                    mmShowMessage(data);
                    if (data.status === 'OK')
                        $("#reload-list").trigger('click');
                },
                error: function(data) {
                    //reloadButton.trigger('click');
                    mmShowMessage(data);
                }
            })
        }
    });

$(document).delegate('#multiedit-add-field','click', function() {

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
                mmShowMessage(data);
                if (data.status === 'OK')
                    $("#reload-list").trigger('click');
            },
            error: function(data) {
                //reloadButton.trigger('click');
                mmShowMessage(data);
            }
        })
    });

    $(document).delegate('.multiedit-rename-field', 'click', function() {

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
                    mmShowMessage(data);
                    if (data.status === 'OK')
                        $("#reload-list").trigger('click');
                },
                error: function(data) {
                    //reloadButton.trigger('click');
                    mmShowMessage(data);
                }
            });
        }
    });

    $("#reload-list").live('click', function() {
        $('#multiedit-pageslist').trigger('change');
    });

    $(document).delegate(".multiedit-field", 'change', function() {
        field = $(this);
        progressIndicator = $('#' + field.attr('id') + '-loader');
        progressIndicator.addClass('visible');
        $.ajax({
            url: "<?php echo get_url( 'plugin/multiedit/setvalue/' ); ?>",
            type: 'post',
            dataType: 'json',
            data: {
                item: field.attr('name'),
                value: field.val()
            },
            success: function(data) {
                if (data.status == 'OK') {
                    field.removeClass('error');
                    field.addClass('success');

                    mmShowMessage(data);
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
                    mmShowMessage(data);
                    setTimeout(function() {
                        progressIndicator.removeClass('visible');
                    }, 300);
                    $(".slugfield").trigger('keyup');

                }
            },
        });
    });



</script>

