<?php
/* Security measure */
if (!defined('IN_CMS')) { exit(); }
?>

<?php
// initialize variables
//$behaviors = Behavior::findAll();

if (!isset($force))  $force = false;
if (!isset($isRoot)) $isRoot = false;

$show_line_1 = (((!isset($_COOKIE['r1']) || $_COOKIE['r1']=='1') || $force || $is_frontend) && AuthUser::hasPermission('multiedit_basic')) ? true : false;
$show_line_2 = (((!isset($_COOKIE['r2']) || $_COOKIE['r2']=='1') || $force || $is_frontend) && AuthUser::hasPermission('multiedit_basic')) ? true : false;
$show_line_3 = (((!isset($_COOKIE['r3']) || $_COOKIE['r3']=='1') || $force || $is_frontend) && AuthUser::hasPermission('multiedit_basic')) ? true : false;
$show_line_4 = (((!isset($_COOKIE['r4']) || $_COOKIE['r4']=='1') || $force || $is_frontend) && AuthUser::hasPermission('multiedit_advanced')) ? true : false;
$showpageparts = (((!isset($_COOKIE['shpp']) || $_COOKIE['shpp']=='1') || $force || $is_frontend) && AuthUser::hasPermission('multiedit_parts')) ? true : false;
$editable_filters = MultieditController::$editableFilters;
?>

<?php foreach ($items as $k): ?>
<?php if (!isset($innerOnly)): ?>
	<div class="multiedit-item<?php if (isset($isRoot)&&$isRoot==true) {echo " multiedit-item-root";} ?>" id="multipage_item-<?php echo $k->id; ?>">
<?php endif; ?>
		<div class="actions">
<?php if (!$is_frontend): ?>
                        <span class="reload-item" id="reload-item<?php echo $k->id; ?>" rel="multipage_item-<?php echo $k->id; ?>"><img alt="<?php echo __('Refresh item'); ?>" title="<?php echo __('Refresh item'); ?>" src="<?php echo PLUGINS_URI.'multiedit/icons/refresh.png'; ?>"/></span>
                        <span class="reload-item full" rel="multipage_item-<?php echo $k->id; ?>"><img alt="<?php echo __('Full view'); ?>" title="<?php echo __('Full view'); ?>" src="<?php echo PLUGINS_URI.'multiedit/icons/zoom.png'; ?>"/></span>
			<!-- <span class="hide-item" rel="multipage_item-<?php echo $k->id; ?>"><img alt="<?php echo __("Remove from list (doesn't delete the page)"); ?>" title="<?php echo __("Remove from list (doesn't delete the page)"); ?>" src="<?php echo PLUGINS_URI.'multiedit/icons/minus.png'; ?>"/></span> -->
<?php endif; ?>
			<a class="edit-item" href="<?php echo URL_PUBLIC.ADMIN_DIR.'/page/edit/'. $k->id; ?>" target="_blank"><img alt="<?php echo __('Edit in default editor'); ?>" title="<?php echo __('Edit in default editor'); ?>" src="<?php echo PLUGINS_URI.'multiedit/icons/pencil.png'; ?>"/></a>
		</div>
		<div class="header">
			<div id="status-indicator-<?php echo $k->id; ?>" class="status-indicator status-<?php echo $k->status_id;?>"></div>
		<div class="page-id"><?php echo $k->id; ?></div>
		<?php echo URL_PUBLIC;
        if ($parentUri!==false) {
              echo $parentUri;
              if (strlen($parentUri)>0) {echo '/';};
              }
              else {
                $listUri = $k->getUri();
                // echo '[ listuri ='.$listUri.']';
                echo (isset($k->parent_id)) ? mb_substr($listUri,0,-mb_strlen(strrchr($listUri,"/"))) : '';
                if (strpos($listUri,'/')!==false) {echo '/';};
              }

        ?><div class="titleslug" id="slug-<?php echo $k->id; ?>-title"><?php echo $k->slug; ?></div>
		</div>
		<table>

                        <?php if ($show_line_1): ?>
			<tr>
				<td class="fieldlabel">Title</td>
				<td>
					<input type="text" class="multiedit-field multiedit-countchars" id="title-<?php echo $k->id; ?>" name="title-<?php echo $k->id; ?>" value="<?php echo $k->title; ?>"/>
					<img id="title-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>
				<td class="counter">
					<div id="title-<?php echo $k->id; ?>-cnt"></div>
				</td>
				<td class="fieldlabel">Description</td>
				<td>
					<input type="text" class="multiedit-field multiedit-countchars" id="description-<?php echo $k->id; ?>" name="description-<?php echo $k->id; ?>" value="<?php echo $k->description; ?>"/>
					<img id="description-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>
				<td class="counter">
					<div id="description-<?php echo $k->id; ?>-cnt"></div>
				</td>
				<td class="fieldlabel">Created</td>
				<td class="timecolumn">
					<input type="text" class="multiedit-field" id="created_on-<?php echo $k->id; ?>" name="created_on-<?php echo $k->id; ?>" value="<?php echo $k->created_on; ?>"/>
					<img id="created_on-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>
			</tr>
                        <?php endif; //$show_line_1 ?>
                        <?php if ($show_line_2): ?>
			<tr>
				<td class="fieldlabel">B-crumb</td>
				<td>
					<input type="text" class="multiedit-field" id="breadcrumb-<?php echo $k->id; ?>" name="breadcrumb-<?php echo $k->id; ?>" value="<?php echo $k->breadcrumb; ?>"/>
					<img id="breadcrumb-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>
				<td class="counter">
                                    <?php if ($show_line_1): ?>
					<div><span class="multiedit-breadcrumber" rel="slug-<?php echo $k->id; ?>"><img src="<?php echo PLUGINS_URI.'multiedit/icons/arrow-top-left.png'; ?>" alt="<?php echo __('Copy breadcrumb from title'); ?>" title="<?php echo __('Copy breadcrumb from title'); ?>" /></span></div>
                                    <?php endif; ?>
				</td>
				<td class="fieldlabel">Keywords</td>
				<td>
					<input type="text" class="multiedit-field multiedit-countchars" id="keywords-<?php echo $k->id; ?>" name="keywords-<?php echo $k->id; ?>" value="<?php echo $k->keywords; ?>"/>
					<img id="keywords-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>
				<td class="counter">
					<div id="keywords-<?php echo $k->id; ?>-cnt"></div>
				</td>
				<td class="fieldlabel">Published</td>
				<td class="timecolumn">
					<input type="text" class="multiedit-field" id="published_on-<?php echo $k->id; ?>" name="published_on-<?php echo $k->id; ?>" value="<?php echo $k->published_on; ?>"/>
					<img id="published_on-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>
			</tr>
                        <?php endif; //$show_line_2 ?>
                        <?php if ($show_line_3): ?>
			<tr>
				<td class="fieldlabel">
					<?php if($k->id != 1): //root page slug protection ?>
					Slug
					<?php endif; ?>
				</td>
				<td>
					<?php if($k->id != 1): //root page slug protection ?>
					<input type="text" class="multiedit-field multiedit-slugfield" id="slug-<?php echo $k->id; ?>" name="slug-<?php echo $k->id; ?>" value="<?php echo $k->slug; ?>"/>
					<img id="slug-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
					<?php endif; ?>
				</td>
				<td class="counter">
					<?php if($k->id != 1): //root page slug protection ?>
                                            <?php if ($show_line_1): ?>
					<div><span class="multiedit-slugifier" rel="slug-<?php echo $k->id; ?>"><img src="<?php echo PLUGINS_URI.'multiedit/icons/arrow-top-left.png'; ?>" alt="<?php echo __('Make slug from title'); ?>" title="<?php echo __('Make slug from title'); ?>"/></span></div>
                                            <?php endif; ?>
					<?php endif; ?>
				</td>
				<td rowspan="3" class="fieldlabel">Tags</td>
				<td rowspan="3">
                    <textarea class="multiedit-field multiedit-counttags multiedit-field-tags" id="tags-<?php echo $k->id; ?>" name="tags-<?php echo $k->id; ?>"><?php echo implode(', ', $k->tags()); ?></textarea>
					<img id="tags-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>
				<td rowspan="3" class="counter">
					<div id="tags-<?php echo $k->id; ?>-cnt"></div>
				</td>


				<td class="fieldlabel">Valid until</td>
				<td class="timecolumn">
					<input type="text" class="multiedit-field" id="valid_until-<?php echo $k->id; ?>" name="valid_until-<?php echo $k->id; ?>" value="<?php echo $k->valid_until; ?>"/>
					<img id="valid_until-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>">
				</td>

			</tr>
			<tr>
				<td class="fieldlabel">
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
				<td class="fieldlabel">Updated on</td>
				<td id="updated_on-<?php echo $k->id; ?>">
					<?php echo $k->updated_on; ?>
				</td>
			</tr>
                        <tr>
				<td class="fieldlabel">
					<?php if($k->id != 1): //root page status protection ?>
					Status
					<?php endif; ?>
				</td>
				<td>
					<?php if($k->id != 1): //root page status protection ?>
					<select id="status_id-<?php echo $k->id; ?>" class="multiedit-select multiedit-field status-select" rel="status-indicator-<?php echo $k->id; ?>" id="status_id-<?php echo $k->id; ?>" name="status_id-<?php echo $k->id; ?>">
						<option class="status-<?php echo Page::STATUS_DRAFT; ?>" value="<?php echo Page::STATUS_DRAFT; ?>"<?php echo $k->status_id == Page::STATUS_DRAFT ? ' selected="selected"': ''; ?>><?php echo __('Draft'); ?></option>
						<option class="status-<?php echo Page::STATUS_PREVIEW; ?>" value="<?php echo Page::STATUS_PREVIEW; ?>"<?php echo $k->status_id == Page::STATUS_PREVIEW ? ' selected="selected"': ''; ?>><?php echo __('Preview'); ?></option>
						<option class="status-<?php echo Page::STATUS_PUBLISHED; ?>" value="<?php echo Page::STATUS_PUBLISHED; ?>"<?php echo $k->status_id == Page::STATUS_PUBLISHED ? ' selected="selected"': ''; ?>><?php echo __('Published'); ?></option>
						<option class="status-<?php echo Page::STATUS_HIDDEN; ?>" value="<?php echo Page::STATUS_HIDDEN; ?>"<?php echo $k->status_id == Page::STATUS_HIDDEN ? ' selected="selected"': ''; ?>><?php echo __('Hidden'); ?></option>
						<option class="status-<?php echo Page::STATUS_ARCHIVED; ?>" value="<?php echo Page::STATUS_ARCHIVED; ?>"<?php echo $k->status_id == Page::STATUS_ARCHIVED ? ' selected="selected"': ''; ?>><?php echo __('Archived'); ?></option>
					</select>
					<?php endif; ?>
				</td>
				<td></td>
                        </tr>
                        <?php endif; //$show_line_3 ?>

                        <?php if ($show_line_4):
                            $cnt = 1; // helper counter for layouting
                            $total_ext_fields = count($extended_fields);
                            $warning = '&lArr; ' . __('Plugin fields. Use with caution!');
                            ?>
                            <tr class="extended_fields_row">
                            <?php foreach ( $extended_fields as $ext_field ): ?>
                            <td class="fieldlabel"><span title="<?php echo __('Extended field') . ' [' . $ext_field . ']'; ?>"><?php echo Inflector::humanize($ext_field); ?></span></td>
                                    <td>
                                            <input type="text" class="multiedit-field" id="<?php echo $ext_field . '-' . $k->id; ?>" name="<?php echo $ext_field . '-' . $k->id; ?>" value="<?php echo $k->{$ext_field}; ?>"/>
                                            <img id="<?php echo $ext_field . '-' . $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress.gif'; ?>"/>
                                    </td>
                                    <td class="counter">
                                        <?php if ($k->id==1): // editing possible only in root page ?>
                                                <span class="multiedit-delete-field" data-field-name="<?php echo $ext_field; ?>">
                                                    <img src="<?php echo PLUGINS_URI.'multiedit/icons/cross.png'; ?>" alt="<?php echo __('Delete this field'); ?>" title="<?php echo __('Delete this field'); ?>"/>
                                                </span>
                                                <span class="multiedit-rename-field" data-field-name="<?php echo $ext_field; ?>">
                                                    <img src="<?php echo PLUGINS_URI.'multiedit/icons/pencil.png'; ?>" alt="<?php echo __('Rename this field'); ?>" title="<?php echo __('Rename this field'); ?>"/>
                                                </span>
                                        <?php endif; ?>
                                    </td>

                            <?php
                            if (($cnt % 2)===0 && $cnt !== $total_ext_fields) { echo '<td class="warning" colspan="2">'.$warning.'</td></tr><tr class="extended_fields_row">'; $warning=''; }
                            $cnt++;
                            endforeach; //extended fields

                            // fill up remaining cells
                            if ($total_ext_fields % 2 === 1) echo '<td class="fieldlabel"></td><td></td><td></td><td class="warning" colspan="2">'.$warning.'</td>';
                            else echo '<td class="warning" colspan="2">'.$warning.'</td>';

                            ?>
			</tr>
                        <?php endif; //$show_line_4 ?>

	<?php if ($showpageparts=='1'): ?>
			<?php
			$parts = PagePart::findByPageId($k->id);
			foreach ($parts as $part) :
                            if (empty($part->filter_id) || in_array($part->filter_id,$editable_filters)) :

                             $filter_class = (!in_array( $part->filter_id, $filters ) && !empty($part->filter_id)) ? ' class="error" title="'.__('Plugin for this filter seems to be disabled!').'"' : '';
			?>
					<tr class="page_part_row">
						      <td class="fieldlabel"><span class="rename_page_part" rel="<?php echo $k->id; ?>"><?php echo $part->name; ?></span><br/>
                                                          <?php
                                                          echo '[<em'.$filter_class.'>';
                                                          echo (empty($part->filter_id)) ? '-'. __('none') . '-' : $part->filter_id;
                                                          echo '</em>]';
                                                          ?></td>
						      <td colspan="7" class="textareacontainer"><textarea class="multiedit-field partedit" name="part-<?php echo $k->id; ?>_partname_<?php echo $part->name; ?>"><?php echo htmlentities($part->content, ENT_COMPAT, 'UTF-8'); ?></textarea></td>
					</tr>
			<?php
				endif; //editable parts
                            endforeach; //foreach  ?>
			<tr class="page_part_row">
				<td class="fieldlabel">
				</td>
				<td colspan="7">
					<?php foreach ($parts as $part): ?>
					<?php
						if (!empty($part->filter_id) && !in_array($part->filter_id,$editable_filters) ):
						echo '<div class="filteredparts"><span class="rename_page_part" rel="'. $k->id . '">' . $part->name . '</span> [<em>' . $part->filter_id . '</em>]</div>';
						endif;
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
