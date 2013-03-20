<?php

if ( !defined('IN_CMS') ) {
    exit();
}
?>
<p>
    Here you can quickly edit multiple pages at once. The list of pages consists of the selected root page and children of it.
    Each field is updated upon leaving it.
</p>
<?php if ( AuthUser::hasPermission('multiedit_parts') ): ?>
    <p>
        You can <b>edit page part</b> contents. It's also possible to <b>rename, delete and add</b> page parts.
    </p>
<?php endif; ?>
<?php if ( AuthUser::hasPermission('multiedit_advanced') ): ?>
    <p>
        You can edit <strong>extended page fields</strong>, which are generally provided by some plugins. This can have <strong>unexpected</strong> consequences, so do it with care.
    </p>
<?php endif; ?>

