<?php
/* Security measure */
if (!defined('IN_CMS')) { exit(); }
?>

<?php foreach ($items as $k): ?>
<?php if (!isset($innerOnly)): ?>
	<div class="multiedit-item<?php if (isset($isRoot)&&$isRoot==true) {echo " multiedit-item-root";} ?>" id="multipage_item-<?php echo $k->id; ?>">
<?php endif; ?>
		<div class="header">
		<div class="actions">
			<span class="reload-item" rel="multipage_item-<?php echo $k->id; ?>"><img src="<?php echo PLUGINS_URI.'multiedit/icons/arrow-circle-135-left.png'; ?>"/></span>
			<span class="hide-item" rel="multipage_item-<?php echo $k->id; ?>"><img src="<?php echo PLUGINS_URI.'multiedit/icons/minus-button.png'; ?>"/></span>
			<a href="/admin/page/edit/<?php echo $k->id; ?>" target="_blank"><img src="<?php echo PLUGINS_URI.'multiedit/icons/blue-document--pencil.png'; ?>"/></a>
		</div>			
		<div class="page-id"><?php echo $k->id; ?></div>
		<?php echo URL_PUBLIC; ?><?php echo $parentUri; if (strlen($parentUri)>0) {echo '/';} ?><div class="titleslug" id="slug-<?php echo $k->id; ?>-title"><?php echo $k->slug; ?></div>

		</div>
		<table border="0">

			<tr>
				<td colspan="8"></td>
			</tr>			
			<tr>
				<td class="label">Title</td>
				<td>
					<input type="text" class="multiedit-field countchars" id="title-<?php echo $k->id; ?>" name="title-<?php echo $k->id; ?>" value="<?php echo $k->title; ?>"/>
					<img id="title-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>
				<td class="counter">
					<div id="title-<?php echo $k->id; ?>-cnt"></div>
				</td>
				<td class="label">Keywords</td>
				<td>
					<input type="text" class="multiedit-field countchars" id="keywords-<?php echo $k->id; ?>" name="keywords-<?php echo $k->id; ?>" value="<?php echo $k->keywords; ?>"/>
					<img id="keywords-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>
				<td class="counter">
					<div id="keywords-<?php echo $k->id; ?>-cnt"></div>
				</td>
				<td class="label">Created</td>
				<td class="timecolumn">
					<input type="text" class="multiedit-field" id="created_on-<?php echo $k->id; ?>" name="created_on-<?php echo $k->id; ?>" value="<?php echo $k->created_on; ?>"/>
					<img id="created_on-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>
			</tr>		
			<tr>
				<td class="label">B-crumb</td>
				<td>
					<input type="text" class="multiedit-field" id="breadcrumb-<?php echo $k->id; ?>" name="breadcrumb-<?php echo $k->id; ?>" value="<?php echo $k->breadcrumb; ?>"/>
					<img id="breadcrumb-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>
				<td class="counter">
					<div><span class="breadcrumber" rel="slug-<?php echo $k->id; ?>"><img src="<?php echo PLUGINS_URI.'multiedit/icons/arrow-curve-180.png'; ?>"/></span></div>
				</td>
				<td class="label">Description</td>
				<td>
					<input type="text" class="multiedit-field countchars" id="description-<?php echo $k->id; ?>" name="description-<?php echo $k->id; ?>" value="<?php echo $k->description; ?>"/>
					<img id="description-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>
				<td class="counter">
					<div id="description-<?php echo $k->id; ?>-cnt"></div>
				</td>
				<td class="label">Published</td>
				<td class="timecolumn">
					<input type="text" class="multiedit-field" id="published_on-<?php echo $k->id; ?>" name="published_on-<?php echo $k->id; ?>" value="<?php echo $k->published_on; ?>"/>
					<img id="published_on-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>
			</tr>
			<tr>
				<td class="label">Slug</td>
				<td>
					<input type="text" class="multiedit-field slugfield" id="slug-<?php echo $k->id; ?>" name="slug-<?php echo $k->id; ?>" value="<?php echo $k->slug; ?>"/>
					<img id="slug-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>
				<td class="counter">
					<div><span class="slugifier" rel="slug-<?php echo $k->id; ?>"><img src="<?php echo PLUGINS_URI.'multiedit/icons/arrow-curve-180.png'; ?>"/></span></div>
				</td>
				<td class="label">Tags</td>
				<td>
					<input type="text" class="multiedit-field counttags" id="tags-<?php echo $k->id; ?>" name="tags-<?php echo $k->id; ?>" value="<?php echo implode(', ', $k->tags()); ?>"/>
					<img id="tags-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>
				<td class="counter">
					<div id="tags-<?php echo $k->id; ?>-cnt"></div>
				</td>


				<td class="label">Valid until</td>
				<td class="timecolumn">
					<input type="text" class="multiedit-field" id="valid_until-<?php echo $k->id; ?>" name="valid_until-<?php echo $k->id; ?>" value="<?php echo $k->valid_until; ?>"/>
					<img id="valid_until-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>

			</tr>
			<tr>
				<td class="label">
					Layout
				</td>
				<td>
					<select id="layout_id-<?php echo $k->id; ?>" name="layout_id-<?php echo $k->id; ?>" class="multiedit-select multiedit-field" >
						<option value="0">&#8212; <?php echo __('inherit'); ?> &#8212;</option>
					<?php foreach ($layouts as $layout): ?>
						<option value="<?php echo $layout->id; ?>"<?php echo $layout->id == $k->layout_id ? ' selected="selected"': ''; ?>><?php echo $layout->name; ?></option>
					<?php endforeach; ?>
					</select>
				</td>
				<td>
					
				</td>
				<td class="label">
					Status
				</td>				
				<td>
					<select id="status_id-<?php echo $k->id; ?>" class="multiedit-select multiedit-field" id="status_id-<?php echo $k->id; ?>" name="status_id-<?php echo $k->id; ?>">
						<option value="<?php echo Page::STATUS_DRAFT; ?>"<?php echo $k->status_id == Page::STATUS_DRAFT ? ' selected="selected"': ''; ?>><?php echo __('Draft'); ?></option>
						<option value="<?php echo Page::STATUS_PREVIEW; ?>"<?php echo $k->status_id == Page::STATUS_PREVIEW ? ' selected="selected"': ''; ?>><?php echo __('Preview'); ?></option>
						<option value="<?php echo Page::STATUS_PUBLISHED; ?>"<?php echo $k->status_id == Page::STATUS_PUBLISHED ? ' selected="selected"': ''; ?>><?php echo __('Published'); ?></option>
						<option value="<?php echo Page::STATUS_HIDDEN; ?>"<?php echo $k->status_id == Page::STATUS_HIDDEN ? ' selected="selected"': ''; ?>><?php echo __('Hidden'); ?></option>
						<option value="<?php echo Page::STATUS_ARCHIVED; ?>"<?php echo $k->status_id == Page::STATUS_ARCHIVED ? ' selected="selected"': ''; ?>><?php echo __('Archived'); ?></option>
					</select>

				</td>
				<td></td>
				<td class="label">Updated on</td>
				<td id="updated_on-<?php echo $k->id; ?>">
					<?php echo $k->updated_on; ?>
				</td>
			</tr>
	<?php if ($showpageparts=='1'): ?>
			<?php
			$parts = PagePart::findByPageId($k->id);
			foreach ($parts as $part) {
				if (empty($part->filter_id)) {
			?>
					<tr>
						      <td class="label"><b><?php echo $part->name; ?></b></td>
						      <td colspan="7" class="textareacontainer"><textarea class="multiedit-field" name="part-<?php echo $k->id; ?>_partname_<?php echo $part->name; ?>"><?php echo $part->content_html; ?></textarea></td>
					</tr>
			<?php 
				}; //empty filter value
			}; //foreach ?>
			<tr>
				<td class="label">
				</td>
				<td colspan="6">
					<?php foreach ($parts as $part): ?>
					<?php 
						if (!empty($part->filter_id)) {
						echo '<div class="filteredparts">' . $part->name . ' [<em>' . $part->filter_id . '</em>]</div>'; 
						}
					?>
					<?php endforeach; ?>
				</td>
			</tr>
	<?php endif; // showpageparts ?>

		</table>
<?php if (!isset($innerOnly)): ?>
	</div>
<?php endif; ?>
<?php endforeach; ?>
