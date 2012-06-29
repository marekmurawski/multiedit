<?php
/* Security measure */
if (!defined('IN_CMS')) { exit(); }
?>

<label for="rootpage"><?php echo __('Show subpages of')?>: </label>
<select name="rootpage" id="multiedit-pageslist" class="multiedit-items-select">
			<option value="1" selected><?php echo $rootPage->title; ?></option>
			<?php foreach($pagesList as $k): ?>
			<option value="<?php echo $k['id'] ?>"><?php echo $k['label'] . ' (' . $k['count'] . ')' ?></option>
			<?php endforeach; ?>
</select>
<label for="sorting"> <?php echo __('sort by')?> </label>
<select name="sorting" id="multiedit-pageslist-sorting" class="multiedit-items-select">
			<option value="id" selected><?php echo __('ID'); ?></option>	
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
<label for="order"> <?php echo __('Show page parts'); ?> </label>
<input type="checkbox" class="multiedit-items-select" name="showpageparts" id="showpageparts" value="1" checked="1"/>
<img id="reload-list" src="<?php echo PLUGINS_URI.'multiedit/icons/arrow-circle-135-left.png'; ?>"/>