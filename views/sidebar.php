<?php
/* Security measure */
if ( !defined('IN_CMS') ) {
    exit();
}
?>
<p class="button"><a href="<?php echo get_url('plugin/multiedit/documentation'); ?>"><img src="<?php echo URL_PUBLIC; ?>wolf/plugins/multiedit/icons/help.png" align="middle" /><?php echo __('Documentation'); ?></a></p>

<div class="box">
    <h2><?php echo __('MultiEdit') . ' - v.' . Plugin::$plugins_infos['multiedit']->version; ?></h2>
    <?php
    echo $sidebarContents;
    ?>
</div>


<div id="mm_sbox">
    <h2><?php echo __('Messages'); ?></h2>
    <div id="mmsg_wrap" class="progress init">
        <div id="mmsg_out"></div>
    </div>
    <div id="mmsg_stats"></div>
    <div style="clear: both; text-align: right;"><small><p>&copy; <a href="http://marekmurawski.pl" target="_blank" />Marek Murawski</a><br/>
    <a href="http://marekmurawski.pl/en/web/wolf-cms/plugins/multiedit.html" target="_blank" />[plugin homepage]</a></p></small></div>
</div>

<script>
    var safeJSON = function(code) {
        try {
            return $.parseJSON(code);
        } catch (e) {
            return {
                status: 'error',
                message: code
            };
        }
    };

    var mmShowMessage = function(data) {
        if (null === data) {
            $('#mmsg_wrap').attr('class', 'error');
            $('#mmsg_out').html('<?php echo __('Empty response!'); ?>');
        } else
        if (data.hasOwnProperty('status')) {
            if (data.status === 'OK') {
                $('#mmsg_wrap').attr('class', 'success');
            } else
                $('#mmsg_wrap').attr('class', 'error');
        } else
            $('#mmsg_wrap').attr('class', 'success');
        if (data.hasOwnProperty('exe_time'))
            $('#mmsg_stats').html('<?php echo __('Execution time'); ?>: <b>' + data.exe_time + '</b><br/>' + '<?php echo __('Memory usage'); ?>: <b>' + data.mem_used + '</b>');
        (data.hasOwnProperty('message')) ? $('#mmsg_out').html(data.message) : $('#mmsg_out').html(data);
    };

    $(document).ajaxSend(function() {
        $('#mmsg_wrap').attr('class', 'progress');
        $('#mmsg_stats').html('');
        $('#mmsg_out').html('<?php echo __('Sending request...'); ?>');
    });

    $(document).ajaxError(function(event, jqXHR, settings, exception) {
        var msg;
        if (msg = safeJSON(jqXHR.responseText)) {
            mmShowMessage(msg);
        } else {
            mmShowMessage(jqXHR.responseText);
        }
    });

    $(document).ready(function() {
        var top = $('#mm_sbox').offset().top - parseFloat($('#mm_sbox').css('marginTop').replace(/auto/, 0));
        $(window).scroll(function(event) {
            var y = $(this).scrollTop();
            if (y >= top) {
                $('#mm_sbox').addClass('fixed');
            } else {
                $('#mm_sbox').removeClass('fixed');
            }
        });
    });

</script>