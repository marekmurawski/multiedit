<?php
/* Security measure */
if ( !defined('IN_CMS') ) {
    exit();
}
?>
<label for="rootpage"><?php echo __('Show subpages of') ?>: </label>
<select name="rootpage" id="multiedit-pageslist" class="multiedit-header-field">
    <option value="-1"><?php echo __('All pages as flat list'); ?></option>
    <option value="0">---------------------------</option>
    <option value="1" selected><?php echo $rootPage->breadcrumb; ?></option>
    <?php foreach ( $pagesList as $k ): ?>
        <option value="<?php echo $k['id'] ?>"><?php echo $k['label'] . ' (' . $k['count'] . ')' ?></option>
    <?php endforeach; ?>
</select>
<label for="sorting"> <?php echo __('sort by') ?> </label>
<select name="sorting" id="multiedit-pageslist-sorting" class="multiedit-header-field">
    <option value="-default-" selected><?php echo __('Default'); ?></option>
    <option value="id"><?php echo __('ID'); ?></option>
    <option value="title"><?php echo __('Title'); ?></option>
    <option value="breadcrumb"><?php echo __('Breadcrumb'); ?></option>
    <option value="slug"><?php echo __('Slug'); ?></option>
    <option value="keywords"><?php echo __('Keywords'); ?></option>
    <option value="description"><?php echo __('Description'); ?></option>
    <option value="created_on"><?php echo __('Created on'); ?></option>
    <option value="published_on"><?php echo __('Published on'); ?></option>
    <option value="valid_until"><?php echo __('Valid until'); ?></option>
</select>
<select name="order" id="multiedit-pageslist-order" class="multiedit-header-field">
    <option value="asc"><?php echo __('Ascending'); ?></option>
    <option value="desc"><?php echo __('Descending'); ?></option>
</select>

<div class="clear"></div>

<?php
$show_row_1    = (MultieditController::$cookie['showrow1']) ? ' checked="checked"' : '';
$show_row_2    = (MultieditController::$cookie['showrow2']) ? ' checked="checked"' : '';
$show_row_3    = (MultieditController::$cookie['showrow3']) ? ' checked="checked"' : '';
$show_row_4    = (MultieditController::$cookie['showrow4']) ? ' checked="checked"' : '';
$showpageparts = (MultieditController::$cookie['showpageparts']) ? ' checked="checked"' : '';
$useace        = (MultieditController::$cookie['useace']) ? ' checked="checked"' : '';

// this is used by ajax calls
echo '<div style="display: none" id="multiedit-controller-url" data-url="' . get_url('plugin/multiedit') . '">' . get_url('plugin/multiedit') . '</div>';
?>
<table border="0">
    <tr>
        <td style="width: 50%">
            <?php if ( AuthUser::hasPermission('multiedit_basic') ): ?>
                <input type="checkbox" class="multiedit-header-field" name="showrow1" id="showrow1" value="1" <?php echo $show_row_1; ?>/>
                <label for="showrow1"><img alt="<?php echo __('Show title and description'); ?>" title="<?php echo __('Show title, description and created date'); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/row1.png'; ?>"/> <?php echo __('Show title, description and created date'); ?></label>
                <div class="clear"></div>
                <input type="checkbox" class="multiedit-header-field" name="showrow2" id="showrow2" value="1" <?php echo $show_row_2; ?>/>
                <label for="showrow2"><img alt="<?php echo __('Show breadcrumb and keywords'); ?>" title="<?php echo __('Show breadcrumb, keywords and published date'); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/row2.png'; ?>"/> <?php echo __('Show breadcrumb, keywords and published date'); ?></label>
                <div class="clear"></div>
                <input type="checkbox" class="multiedit-header-field" name="showrow3" id="showrow3" value="1" <?php echo $show_row_3; ?>/>
                <label for="showrow3"><img alt="<?php echo __('Show extended properties'); ?>" title="<?php echo __('Show slug, layout, status, tags and valid until date'); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/row3.png'; ?>"/> <?php echo __('Show slug, layout, status, tags and valid until date'); ?></label>
                <div class="clear"></div>
            <?php endif; ?>
            <?php if ( AuthUser::hasPermission('multiedit_advanced') ): ?>
                <input type="checkbox" class="multiedit-header-field" name="showrow4" id="showrow4" value="1" <?php echo $show_row_4; ?>/>
                <label for="showrow4"><img alt="<?php echo __('Show extended (plugin) properties'); ?>" title="<?php echo __('Show extended (plugin) properties'); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/row4.png'; ?>"/> <?php echo __('Show extended (plugin) properties'); ?></label>
                <div class="clear"></div>
            <?php endif; ?>
            <input type="button" style="margin-top: 16px" id="reload-list" value="<?php echo __('Reload list'); ?>"/>
        </td>
        <td style="width: 50%">
            <?php if ( AuthUser::hasPermission('multiedit_parts') ): ?>
                <div class="clear"></div>
                <input type="checkbox" class="multiedit-header-field" name="showpageparts" id="showpageparts" value="1" <?php echo $showpageparts; ?>/>
                <label for="showpageparts"><img alt="<?php echo __('Load page parts'); ?>" title="<?php echo __('Load page parts'); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/snippet.png'; ?>"/> <?php echo __('Load page parts'); ?></label>

                <div class="clear"></div>
                <input type="number" id="partheight" class="secondary" style="width:50px" value="<?php echo MultiEditController::$cookie['pagepartheight']; ?>" min="40" max="1000" step="10" />
                <label for="partheight"><img alt="<?php echo __('Part editing field height'); ?>" title="<?php echo __('Part editing field height'); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/stretch-ver.png'; ?>"/> <?php echo __('Part editing field height'); ?></label>

                <?php if ( Plugin::isEnabled('ace') ): ?>
                    <div class="clear"></div>
                    <input type="checkbox" class="multiedit-header-field secondary" name="useace" id="useace" value="1" <?php echo $useace; ?>/>
                    <label for="useace"><img alt="<?php echo __('Use <b>Ace Syntax Highlighter</b>'); ?>" title="<?php echo __('Use <b>Ace Syntax Highlighter</b>'); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/stretch-ver.png'; ?>"/> <?php echo __('Use <b>Ace Syntax Highlighter</b>'); ?></label>
                <?php endif; ?>

                <div class="clear"></div>
                <p>New field
                    <select id="multiedit-add-field-template" >
                        <?php foreach ( MultieditController::$fieldTemplates[$db_driver] as $k => $fieldTemplate ): ?>
                            <option value="<?php echo $k; ?>"><?php echo $fieldTemplate['description']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="button" id="multiedit-add-field" value="<?php echo __('Add new field'); ?>"/> (DB: <b><?php echo $db_driver; ?></b>)
                </p>
            <?php endif; ?>
            <div class="clear"></div>
        </td>
    </tr>
</table>
