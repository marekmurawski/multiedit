<?php
/* Security measure */
if (!defined('IN_CMS')) { exit(); }
?>

<label for="rootpage"><?php echo __('Show subpages of')?>: </label>
<select name="rootpage" id="multiedit-pageslist" class="multiedit-items-select">
			<option value="-1"><?php echo __('All pages as flat list'); ?></option>
			<option value="0">---------------------------</option>
			<option value="1" selected><?php echo $rootPage->breadcrumb; ?></option>
			<?php foreach($pagesList as $k): ?>
			<option value="<?php echo $k['id'] ?>"><?php echo $k['label'] . ' (' . $k['count'] . ')' ?></option>
			<?php endforeach; ?>
</select>
<label for="sorting"> <?php echo __('sort by')?> </label>
<select name="sorting" id="multiedit-pageslist-sorting" class="multiedit-items-select">
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
<select name="order" id="multiedit-pageslist-order" class="multiedit-items-select">
			<option value="asc"><?php echo __('Ascending'); ?></option>
			<option value="desc"><?php echo __('Descending'); ?></option>
</select>
<label for="showpageparts"><img alt="<?php echo __('Load page parts'); ?>" title="<?php echo __('Load page parts'); ?>" src="<?php echo PLUGINS_URI.'multiedit/icons/page-parts.png'; ?>"/></label>
<input type="checkbox" class="multiedit-items-select" name="showpageparts" id="showpageparts" value="1" checked="1"/>
<label for="showcollapsed"><img alt="<?php echo __('Load items in collapsed state'); ?>" title="<?php echo __('Load items in collapsed state'); ?>" src="<?php echo PLUGINS_URI.'multiedit/icons/collapse.png'; ?>"/></label>
<input type="checkbox" class="multiedit-items-select" name="showcollapsed" id="showcollapsed" value="1"/>
<img alt="<?php echo __("Reload list of pages"); ?>" title="<?php echo __("Reload list of pages"); ?>" id="reload-list" src="<?php echo PLUGINS_URI.'multiedit/icons/arrow-circle-135-left.png'; ?>"/>