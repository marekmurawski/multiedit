<?php
/* Security measure */
if ( !defined( 'IN_CMS' ) ) {
    exit();
}
?>

<?php
// initialize variables
//$behaviors = Behavior::findAll();

if ( !isset( $force_full_view ) )
    $force_full_view     = false;
if ( !isset( $isRoot ) )
    $isRoot              = false;
$show_line_1         = ((MultieditController::$cookie['showrow1'] || $force_full_view ) && AuthUser::hasPermission( 'multiedit_basic' )) ? true : false;
$show_line_2         = ((MultieditController::$cookie['showrow2'] || $force_full_view ) && AuthUser::hasPermission( 'multiedit_basic' )) ? true : false;
$show_line_3         = ((MultieditController::$cookie['showrow3'] || $force_full_view ) && AuthUser::hasPermission( 'multiedit_basic' )) ? true : false;
$show_line_4         = ((MultieditController::$cookie['showrow4'] || $force_full_view ) && AuthUser::hasPermission( 'multiedit_advanced' )) ? true : false;
$showpageparts       = ((MultieditController::$cookie['showpageparts'] || $force_full_view ) && AuthUser::hasPermission( 'multiedit_parts' )) ? true : false;
$page_part_tab_title = __( 'Left click - :editor', array( ':editor' => Plugin::isEnabled( 'ace' ) ? 'Ace Syntax Highlighter' : __( 'Default editor' ) ) ) . PHP_EOL .
            __( 'Right click - Default editor' );
if ( !$is_frontend )
    $page_part_tab_title .= PHP_EOL .
                __( 'Hold CTRL to switch all editors to this part' );
?>

<?php foreach ( $items as $k ): ?>
    <?php if ( !isset( $innerOnly ) ): ?>
        <div class="multiedit-item<?php
        if ( isset( $isRoot ) && $isRoot == true ) {
            echo " multiedit-item-root";
        }
        ?>" id="multipage_item-<?php echo $k->id; ?>">
            <?php endif; ?>
        <div class="actions">
            <?php if ( $is_frontend ) {
                echo '<input type="number" id="partheight" value="' . MultiEditController::$cookie['pagepartheight'] . '" min="40" max="1000" step="10" />';
            } ?>
            <span class="reload-item" id="reload-item<?php echo $k->id; ?>" rel="multipage_item-<?php echo $k->id; ?>" data-is-frontend="<?php echo ($is_frontend) ? '1' : '0'; ?>"><img alt="<?php echo __( 'Refresh item' ); ?>" title="<?php echo __( 'Refresh item' ); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/refresh.png'; ?>"/></span>
            <span class="reload-item full" rel="multipage_item-<?php echo $k->id; ?>" data-is-frontend="<?php echo ($is_frontend) ? '1' : '0'; ?>"><img alt="<?php echo __( 'Full view' ); ?>" title="<?php echo __( 'Full view' ); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/zoom.png'; ?>"/></span>
            <a class="edit-item" href="<?php echo URL_PUBLIC . ADMIN_DIR . '/page/edit/' . $k->id; ?>" target="multiedit_tab"><img alt="<?php echo __( 'Edit in default editor' ); ?>" title="<?php echo __( 'Edit in default editor' ); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/pencil.png'; ?>"/></a>
        </div>
        <div class="header">
            <div id="status-indicator-<?php echo $k->id; ?>" class="status-indicator status-<?php echo $k->status_id; ?>"></div>
            <div class="page-id"><?php echo $k->id; ?></div>
            <?php
            echo URL_PUBLIC;
            if ( $parentUri !== false ) {
                echo $parentUri;
                if ( strlen( $parentUri ) > 0 ) {
                    echo '/';
                };
            } else {
                $listUri = $k->getUri();
                echo (isset( $k->parent_id )) ? mb_substr( $listUri, 0, -mb_strlen( strrchr( $listUri, "/" ) ) ) : '';
                if ( strpos( $listUri, '/' ) !== false ) {
                    echo '/';
                };
            }
            ?><div class="titleslug" id="slug-<?php echo $k->id; ?>-title"><?php echo $k->slug; ?></div>
        </div>
        <?php
        if ( $is_frontend ) {
            echo '<div style="display:none" id="multiedit-controller-url" data-url="' . get_url( 'plugin/multiedit' ) . '"></div>';
        }
        ?>
        <table border="0">

    <?php if ( $show_line_1 ): ?>
                <tr>
                    <td class="fieldlabel">Title</td>
                    <td>
                        <input type="text" class="multiedit-field multiedit-countchars" id="title-<?php echo $k->id; ?>" name="title-<?php echo $k->id; ?>" value="<?php echo $k->title; ?>"/>
                        <img id="title-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI . 'multiedit/icons/progress.gif'; ?>">
                    </td>
                    <td class="counter">
                        <div id="title-<?php echo $k->id; ?>-cnt"></div>
                    </td>
                    <td class="fieldlabel">Description</td>
                    <td>
                        <input type="text" class="multiedit-field multiedit-countchars" id="description-<?php echo $k->id; ?>" name="description-<?php echo $k->id; ?>" value="<?php echo $k->description; ?>"/>
                        <img id="description-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI . 'multiedit/icons/progress.gif'; ?>">
                    </td>
                    <td class="counter">
                        <div id="description-<?php echo $k->id; ?>-cnt"></div>
                    </td>
                    <td class="fieldlabel">Created</td>
                    <td class="timecolumn">
                        <input type="text" class="multiedit-field" id="created_on-<?php echo $k->id; ?>" name="created_on-<?php echo $k->id; ?>" value="<?php echo $k->created_on; ?>"/>
                        <img id="created_on-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI . 'multiedit/icons/progress.gif'; ?>">
                    </td>
                </tr>
    <?php endif; //$show_line_1     ?>
    <?php if ( $show_line_2 ): ?>
                <tr>
                    <td class="fieldlabel">B-crumb</td>
                    <td>
                        <input type="text" class="multiedit-field" id="breadcrumb-<?php echo $k->id; ?>" name="breadcrumb-<?php echo $k->id; ?>" value="<?php echo $k->breadcrumb; ?>"/>
                        <img id="breadcrumb-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI . 'multiedit/icons/progress.gif'; ?>">
                    </td>
                    <td class="counter">
                        <?php if ( $show_line_1 ): ?>
                            <div><span class="multiedit-breadcrumber" rel="slug-<?php echo $k->id; ?>"><img src="<?php echo PLUGINS_URI . 'multiedit/icons/arrow-top-left.png'; ?>" alt="<?php echo __( 'Copy breadcrumb from title' ); ?>" title="<?php echo __( 'Copy breadcrumb from title' ); ?>" /></span></div>
        <?php endif; ?>
                    </td>
                    <td class="fieldlabel">Keywords</td>
                    <td>
                        <input type="text" class="multiedit-field multiedit-countchars" id="keywords-<?php echo $k->id; ?>" name="keywords-<?php echo $k->id; ?>" value="<?php echo $k->keywords; ?>"/>
                        <img id="keywords-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI . 'multiedit/icons/progress.gif'; ?>">
                    </td>
                    <td class="counter">
                        <div id="keywords-<?php echo $k->id; ?>-cnt"></div>
                    </td>
                    <td class="fieldlabel">Published</td>
                    <td class="timecolumn">
                        <input type="text" class="multiedit-field" id="published_on-<?php echo $k->id; ?>" name="published_on-<?php echo $k->id; ?>" value="<?php echo $k->published_on; ?>"/>
                        <img id="published_on-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI . 'multiedit/icons/progress.gif'; ?>">
                    </td>
                </tr>
    <?php endif; //$show_line_2     ?>
                    <?php if ( $show_line_3 ): ?>
                <tr>
                    <td class="fieldlabel">
                        <?php if ( $k->id != 1 ): //root page slug protection     ?>
                            Slug
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ( $k->id != 1 ): //root page slug protection      ?>
                            <input type="text" class="multiedit-field multiedit-slugfield" id="slug-<?php echo $k->id; ?>" name="slug-<?php echo $k->id; ?>" value="<?php echo $k->slug; ?>"/>
                            <img id="slug-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI . 'multiedit/icons/progress.gif'; ?>">
                        <?php endif; ?>
                    </td>
                    <td class="counter">
                        <?php if ( $k->id != 1 ): //root page slug protection      ?>
                            <?php if ( $show_line_1 ): ?>
                                <div><span class="multiedit-slugifier" rel="slug-<?php echo $k->id; ?>"><img src="<?php echo PLUGINS_URI . 'multiedit/icons/arrow-top-left.png'; ?>" alt="<?php echo __( 'Make slug from title' ); ?>" title="<?php echo __( 'Make slug from title' ); ?>"/></span></div>
            <?php endif; ?>
        <?php endif; ?>
                    </td>
                    <td rowspan="3" class="fieldlabel">Tags</td>
                    <td rowspan="3">
                        <textarea class="multiedit-field multiedit-counttags multiedit-field-tags" id="tags-<?php echo $k->id; ?>" name="tags-<?php echo $k->id; ?>"><?php echo implode( ', ', $k->tags() ); ?></textarea>
                        <img id="tags-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI . 'multiedit/icons/progress.gif'; ?>">
                    </td>
                    <td rowspan="3" class="counter">
                        <div id="tags-<?php echo $k->id; ?>-cnt"></div>
                    </td>


                    <td class="fieldlabel">Valid until</td>
                    <td class="timecolumn">
                        <input type="text" class="multiedit-field" id="valid_until-<?php echo $k->id; ?>" name="valid_until-<?php echo $k->id; ?>" value="<?php echo $k->valid_until; ?>"/>
                        <img id="valid_until-<?php echo $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI . 'multiedit/icons/progress.gif'; ?>">
                    </td>

                </tr>
                <tr>
                    <td class="fieldlabel">
                        Layout
                    </td>
                    <td>
                        <select id="layout_id-<?php echo $k->id; ?>" name="layout_id-<?php echo $k->id; ?>" class="multiedit-select multiedit-field" >
                            <option value="0">&#8212; <?php echo __( 'inherit' ); ?> &#8212;</option>
                            <?php foreach ( $layouts as $layout ): ?>
                                <option value="<?php echo $layout->id; ?>"<?php echo $layout->id == $k->layout_id ? ' selected="selected"' : ''; ?>><?php echo $layout->name; ?></option>
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
                        <?php if ( $k->id != 1 ): //root page status protection     ?>
                            Status
                        <?php endif; ?>
                    </td>
                    <td>
        <?php if ( $k->id != 1 ): //root page status protection       ?>
                            <select id="status_id-<?php echo $k->id; ?>" class="multiedit-select multiedit-field status-select" rel="status-indicator-<?php echo $k->id; ?>" id="status_id-<?php echo $k->id; ?>" name="status_id-<?php echo $k->id; ?>">
                                <option class="status-<?php echo Page::STATUS_DRAFT; ?>" value="<?php echo Page::STATUS_DRAFT; ?>"<?php echo $k->status_id == Page::STATUS_DRAFT ? ' selected="selected"' : ''; ?>><?php echo __( 'Draft' ); ?></option>
                                <option class="status-<?php echo Page::STATUS_PREVIEW; ?>" value="<?php echo Page::STATUS_PREVIEW; ?>"<?php echo $k->status_id == Page::STATUS_PREVIEW ? ' selected="selected"' : ''; ?>><?php echo __( 'Preview' ); ?></option>
                                <option class="status-<?php echo Page::STATUS_PUBLISHED; ?>" value="<?php echo Page::STATUS_PUBLISHED; ?>"<?php echo $k->status_id == Page::STATUS_PUBLISHED ? ' selected="selected"' : ''; ?>><?php echo __( 'Published' ); ?></option>
                                <option class="status-<?php echo Page::STATUS_HIDDEN; ?>" value="<?php echo Page::STATUS_HIDDEN; ?>"<?php echo $k->status_id == Page::STATUS_HIDDEN ? ' selected="selected"' : ''; ?>><?php echo __( 'Hidden' ); ?></option>
                                <option class="status-<?php echo Page::STATUS_ARCHIVED; ?>" value="<?php echo Page::STATUS_ARCHIVED; ?>"<?php echo $k->status_id == Page::STATUS_ARCHIVED ? ' selected="selected"' : ''; ?>><?php echo __( 'Archived' ); ?></option>
                            </select>
        <?php endif; ?>
                    </td>
                    <td></td>
                </tr>
            <?php endif; //$show_line_3     ?>

            <?php
            if ( $show_line_4 ):
                $cnt              = 1; // helper counter for layouting
                $total_ext_fields = count( $extended_fields );
                $warning          = '&lArr; ' . __( 'Plugin fields. Use with caution!' );
                ?>
                <tr class="extended_fields_row">
        <?php foreach ( $extended_fields as $ext_field ): ?>
                        <td class="fieldlabel"><span title="<?php echo __( 'Extended field' ) . ' [' . $ext_field . ']'; ?>"><?php echo Inflector::humanize( $ext_field ); ?></span></td>
                        <td>
                            <input type="text" class="multiedit-field" id="<?php echo $ext_field . '-' . $k->id; ?>" name="<?php echo $ext_field . '-' . $k->id; ?>" value="<?php echo $k->{$ext_field}; ?>"/>
                            <img id="<?php echo $ext_field . '-' . $k->id; ?>-loader" class="loader" src="<?php echo PLUGINS_URI . 'multiedit/icons/progress.gif'; ?>"/>
                        </td>
                        <td class="counter">
            <?php if ( $k->id == 1 ): // editing possible only in root page      ?>
                                <span class="multiedit-delete-field" data-field-name="<?php echo $ext_field; ?>">
                                    <img src="<?php echo PLUGINS_URI . 'multiedit/icons/cross.png'; ?>" alt="<?php echo __( 'Delete this field' ); ?>" title="<?php echo __( 'Delete this field' ); ?>"/>
                                </span>
                                <span class="multiedit-rename-field" data-field-name="<?php echo $ext_field; ?>">
                                    <img src="<?php echo PLUGINS_URI . 'multiedit/icons/pencil.png'; ?>" alt="<?php echo __( 'Rename this field' ); ?>" title="<?php echo __( 'Rename this field' ); ?>"/>
                                </span>
                        <?php endif; ?>
                        </td>

                        <?php
                        if ( ($cnt % 2) === 0 && $cnt !== $total_ext_fields ) {
                            echo '<td class="warning" colspan="2">' . $warning . '</td></tr><tr class="extended_fields_row">';
                            $warning = '';
                        }
                        $cnt++;
                    endforeach; //extended fields
                    // fill up remaining cells
                    if ( $total_ext_fields % 2 === 1 )
                        echo '<td class="fieldlabel"></td><td></td><td></td><td class="warning" colspan="2">' . $warning . '</td>';
                    else
                        echo '<td class="warning" colspan="2">' . $warning . '</td>';
                    ?>
                </tr>
            <?php endif; //$show_line_4   ?>
            <?php if ( $showpageparts ): ?>
                <?php
                $active_frontend_tab_name = (isset( $_COOKIE['MEfet'] )) ? $_COOKIE['MEfet'] : false;
                ?>
                <tr class="page_part_row">
                    <td colspan="8">
                        <?php
                        $parts                    = PagePart::findByPageId( $k->id );
                        $active_tab               = !$is_frontend;
                        ?>

                        <?php foreach ( $parts as $part ): ?>
                            <?php
                            if ( $is_frontend )
                                $active_tab = ($part->name === $active_frontend_tab_name);
                            ?>
                            <div class="partedit_container<?php echo ($active_tab) ? ' visible' : ''; ?>" id="part-<?php echo $k->id; ?>_partname_<?php echo $part->name; ?>-container">

                                <span class="fieldlabel">
            <?php echo __( 'Filter' ); ?>
                                </span>
                                <span class="fieldlabel">
                                    <select class="multiedit-field" name="partfilter-<?php echo $k->id; ?>_partname_<?php echo $part->name; ?>" title="" class="full">
                                        <option value="" <?php echo $part->filter_id == '' ? ' selected="selected"' : ''; ?>>&#8212; <?php echo __( 'none' ); ?> &#8212;</option>
                                        <?php foreach ( $filters as $id => $fname ): ?>
                                            <option value="<?php echo $fname; ?>" <?php echo ($fname === $part->filter_id) ? ' selected="selected"' : ''; ?>><?php echo $fname; ?></option>
            <?php endforeach; ?>
                                    </select>
                                </span>
                                <span class="partedit_toolbar" id="part-<?php echo $k->id; ?>_partname_<?php echo $part->name; ?>-toolbar">
                                </span>


                                <textarea class="multiedit-field partedit"
                                          name="part-<?php echo $k->id; ?>_partname_<?php echo $part->name; ?>"
                                          id="part-<?php echo $k->id; ?>_partname_<?php echo $part->name; ?>"
                                          style="height: <?php echo MultieditController::$cookie['pagepartheight']; ?>px;"><?php echo htmlentities( $part->content, ENT_COMPAT, 'UTF-8' ); ?></textarea>
                            <?php $active_tab = false; ?>
                            </div>
        <?php endforeach; ?>
                    </td>
                </tr>
                <tr class="page_part_row">
                    <td colspan="6">
                        <?php
                        $active_tab = !$is_frontend;
                        foreach ( $parts as $part ):
                            if ( $is_frontend )
                                $active_tab   = ($part->name === $active_frontend_tab_name);
                            ?>
                            <div
                                data-part-name="<?php echo $part->name; ?>"
                                class="me_pt_<?php echo $part->name; ?> part_label_tab<?php echo ($active_tab) ? ' active' : ''; ?>"
                                data-target="part-<?php echo $k->id; ?>_partname_<?php echo $part->name; ?>"
                                data-short-id="p<?php echo $k->id; ?>_<?php echo $part->name; ?>"
                                title = "<?php echo $page_part_tab_title ?>"
                                >
                                <span class="me_tablabel"><?php echo $part->name; ?></span><br/>
                                <?php
                                $filter_class = (!in_array( $part->filter_id, $filters ) && !empty( $part->filter_id )) ? ' class="me_tabfilter" style="color: red" title="' . __( 'Plugin for this filter seems to be disabled!' ) . '"' : ' class="me_tabfilter"';
                                echo '<span ' . $filter_class . '>';
                                echo (empty( $part->filter_id )) ? '&#8212; ' . __( 'none' ) . ' &#8212;' : $part->filter_id;
                                echo '</span>';
                                ?>

                                <img class="rename_page_part" oldname="<?php echo $part->name; ?>" rel="<?php echo $k->id; ?>" alt="<?php echo __( 'Rename page part' ); ?>" title="<?php echo __( 'Rename page part' ); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/pencil.png'; ?>"/>
                                <img class="delete_page_part" data-name="<?php echo $part->name; ?>" data-page-id="<?php echo $k->id; ?>" alt="<?php echo __( 'Delete page part' ); ?>" title="<?php echo __( 'Delete page part' ); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/minus.png'; ?>"/>
                            </div>
                            <?php
                            $active_tab   = false;
                        endforeach;
                        ?>

                        <img class="add_page_part" rel="<?php echo $k->id; ?>" alt="<?php echo __( 'Add page part' ); ?>" title="<?php echo __( 'Add page part' ); ?>" src="<?php echo PLUGINS_URI . 'multiedit/icons/plus.png'; ?>"/>

                    </td>
                </tr>
        <?php endif; // showparts    ?>
        </table>
    <?php if ( !isset( $innerOnly ) ): ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
