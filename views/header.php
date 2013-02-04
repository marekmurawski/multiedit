<?php
/* Security measure */
if ( !defined( 'IN_CMS' ) ) {
    exit();
}
?>
<img alt="<?php echo __( "Reload list of pages" ); ?>" title="<?php echo __( "Reload list of pages" ); ?>" id="reload-list" src="<?php echo PLUGINS_URI . 'multiedit/icons/refresh.png'; ?>"/>

<label for="rootpage"><?php echo __( 'Show subpages of' ) ?>: </label>
<select name="rootpage" id="multiedit-pageslist" class="multiedit-items-select">
    <option value="-1"><?php echo __( 'All pages as flat list' ); ?></option>
    <option value="0">---------------------------</option>
    <option value="1" selected><?php echo $rootPage->breadcrumb; ?></option>
    <?php foreach ( $pagesList as $k ): ?>
        <option value="<?php echo $k['id'] ?>"><?php echo $k['label'] . ' (' . $k['count'] . ')' ?></option>
    <?php endforeach; ?>
</select>
<label for="sorting"> <?php echo __( 'sort by' ) ?> </label>
<select name="sorting" id="multiedit-pageslist-sorting" class="multiedit-items-select">
    <option value="-default-" selected><?php echo __( 'Default' ); ?></option>
    <option value="id"><?php echo __( 'ID' ); ?></option>
    <option value="title"><?php echo __( 'Title' ); ?></option>
    <option value="breadcrumb"><?php echo __( 'Breadcrumb' ); ?></option>
    <option value="slug"><?php echo __( 'Slug' ); ?></option>
    <option value="keywords"><?php echo __( 'Keywords' ); ?></option>
    <option value="description"><?php echo __( 'Description' ); ?></option>
    <option value="created_on"><?php echo __( 'Created on' ); ?></option>
    <option value="published_on"><?php echo __( 'Published on' ); ?></option>
    <option value="valid_until"><?php echo __( 'Valid until' ); ?></option>
</select>
<select name="order" id="multiedit-pageslist-order" class="multiedit-items-select">
    <option value="asc"><?php echo __( 'Ascending' ); ?></option>
    <option value="desc"><?php echo __( 'Descending' ); ?></option>
</select>

<div class="clear"></div>

<?php
$show_row_1 = (!isset( $_COOKIE['r1'] ) || $_COOKIE['r1'] == '1') ? ' checked="checked"' : '';
$show_row_2 = (!isset( $_COOKIE['r2'] ) || $_COOKIE['r2'] == '1') ? ' checked="checked"' : '';
$show_row_3 = (!isset( $_COOKIE['r3'] ) || $_COOKIE['r3'] == '1') ? ' checked="checked"' : '';
$show_row_4 = (!isset( $_COOKIE['r4'] ) || $_COOKIE['r4'] == '1') ? ' checked="checked"' : '';
$showpageparts = (!isset( $_COOKIE['shpp'] ) || $_COOKIE['shpp'] == '1') ? ' checked="checked"' : '';
$autosizepageparts = (isset( $_COOKIE['aspp'] ) && $_COOKIE['aspp'] == '1') ? ' checked="checked"' : '';
?>
<table>
    <tr>
        <td style="width: 50%">
            <?php if ( AuthUser::hasPermission( 'multiedit_basic' ) ): ?>
                <input type="checkbox" class="multiedit-items-select" name="showrow1" id="showrow1" value="1" <?php echo $show_row_1; ?>/>
                <label for="showrow1"><img alt="<?php echo __( 'Show title and description' ); ?>" title="<?php echo __( 'Show title, description and created date' ); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/row1.png'; ?>"/> <?php echo __( 'Show title, description and created date' ); ?></label>
                <div class="clear"></div>
                <input type="checkbox" class="multiedit-items-select" name="showrow2" id="showrow2" value="1" <?php echo $show_row_2; ?>/>
                <label for="showrow2"><img alt="<?php echo __( 'Show breadcrumb and keywords' ); ?>" title="<?php echo __( 'Show breadcrumb, keywords and published date' ); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/row2.png'; ?>"/> <?php echo __( 'Show breadcrumb, keywords and published date' ); ?></label>
                <div class="clear"></div>
                <input type="checkbox" class="multiedit-items-select" name="showrow3" id="showrow3" value="1" <?php echo $show_row_3; ?>/>
                <label for="showrow3"><img alt="<?php echo __( 'Show extended properties' ); ?>" title="<?php echo __( 'Show slug, layout, status, tags and valid until date' ); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/row3.png'; ?>"/> <?php echo __( 'Show slug, layout, status, tags and valid until date' ); ?></label>
                <div class="clear"></div>
            <?php endif; ?>
            <?php if ( AuthUser::hasPermission( 'multiedit_advanced' ) ): ?>
                <input type="checkbox" class="multiedit-items-select" name="showrow4" id="showrow4" value="1" <?php echo $show_row_4; ?>/>
                <label for="showrow4"><img alt="<?php echo __( 'Show extended properties' ); ?>" title="<?php echo __( 'Show extended properties' ); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/row4.png'; ?>"/> <?php echo __( 'Show extended properties' ); ?></label>
            <?php endif; ?>
        </td>
        <td style="width: 50%">
            <?php if ( AuthUser::hasPermission( 'multiedit_parts' ) ): ?>
                <div class="clear"></div>
                <input type="checkbox" class="multiedit-items-select" name="showpageparts" id="showpageparts" value="1" <?php echo $showpageparts; ?>/>
                <label for="showpageparts"><img alt="<?php echo __( 'Load page parts' ); ?>" title="<?php echo __( 'Load page parts' ); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/snippet.png'; ?>"/> <?php echo __( 'Load page parts' ); ?></label>
                <div class="clear"></div>
                <input type="checkbox" class="multiedit-items-select secondary" name="autosizepageparts" id="autosizepageparts" value="1" <?php echo $autosizepageparts; ?>/>
                <label for="autosizepageparts"><img alt="<?php echo __( 'Auto-size page parts' ); ?>" title="<?php echo __( 'Auto-size page parts' ); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/stretch-ver.png'; ?>"/> <?php echo __( 'Auto-size page parts' ); ?></label>
            <?php endif; ?>
        </td>
    </tr>
</table>

