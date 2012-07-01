<?php
/* Security measure */
if (!defined('IN_CMS')) { exit(); }
?>

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
