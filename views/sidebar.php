<?php
/* Security measure */
if (!defined('IN_CMS')) { exit(); }
?>
<p class="button"><a href="<?php echo get_url('plugin/multiedit/documentation'); ?>"><img src="<?php echo URL_PUBLIC; ?>wolf/plugins/multiedit/icons/help-32.png" align="middle" /><?php echo __('Documentation'); ?></a></p>

<div class="box">
<h2><?php echo __('MultiEdit Pages');?></h2>
<?php
echo $sidebarContents;
?>
</div>
<div class="box" id="multiedit-messages">
<h2><?php echo __('Messages');?></h2>
<div id="multiedit-messagebox">
</div>
</div>
