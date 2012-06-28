<?php
/* Security measure */
if (!defined('IN_CMS')) { exit(); }
?>

<div class="box">
<h2><?php echo __('MultiEdit Pages');?></h2>
<p>
<?php echo __('Here you can quickly edit multiple pages at once. The list of pages consists of the selected root page and children of it.')?>
</p>
<p>
<?php echo __('Each field is updated upon leaving it. Changing <em>"title", "breadcrumb", "slug", "keywords", "description" and page parts</em> will change page "updated on" time.')?>
</p>
<p>
<?php echo __('You can also edit page parts but only those <strong>without filter</strong> applied. Page parts with filters are listed below each page.')?>
</p>
</div>
<div class="box">
<h2><?php echo __('Messages');?></h2>
<div id="multiedit-messagebox">
</div>
</div>
