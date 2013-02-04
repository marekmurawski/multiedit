<?php
if (!defined('IN_CMS')) {
	exit();
}
?>
<p>
Here you can quickly edit multiple pages at once. The list of pages consists of the selected root page and children of it.
Each field is updated upon leaving it.
</p>
<?php if (AuthUser::hasPermission('multiedit_parts') ): ?>
<p>
You can also edit page parts with <strong>ace, codemirror, markdown, textile or -none-</strong> filter. Page parts with other filters are listed below.
</p>
<?php endif; ?>
<p>
    To <strong>rename page part</strong> click it's label and type new name.
</p>
<?php if (AuthUser::hasPermission('multiedit_advanced') ): ?>
<p>
    You can edit <strong>extended page fields</strong>, which are generally provided by some plugins. This can have <strong>unexpected</strong> consequences, so do it with care.
</p>
<?php endif; ?>

