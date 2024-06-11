<?php
if (!defined('WPINC')) {
    die('Closed');
}
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

add_thickbox();
$allowed_c_fields = RM_Utilities::get_allowed_conditional_fields();
$primary_fields = array();
?>
<div class="rm-form-builder-navbar">
    <div class="rm-form-builder-navbar-wrap rm-box-border-bottom">
        <div class="rm-form-builder-top-box rm-dd-builder-box">
            <div class="rm-form-builder-title rm-form-builder-action"><div class="rm-dd-page-logo rm-di-flex rm-box-center"><img src="<?php echo esc_url(RM_IMG_URL.'svg/rm-logo.svg');?>" width="150px"><div class="rm-dd-page-title rm-di-flex rm-box-center"> <?php _e('Fields Manager','registrationmagic-addon'); ?><span class="material-icons rm-video-link-icon">smart_display</span></div></div></div>
            <!-- <div class="rm-form-builder-views rm-form-builder-action">
                  <ul>
                    <?php
                    $design_link_class = $design_link_tooltip = "";
                    if($data->theme == 'classic') {
                        $design_link_class = "class='rm_deactivated'";
                        $design_link_tooltip = __('Form design customization is not applicable for Classic theme. To enable please change theme in Global Settings >> General Settings.', 'registrationmagic-addon');
                    }
                    ?>
                    <li title="<?php echo $design_link_tooltip; ?>"><a <?php echo $design_link_class; ?> href="?page=rm_form_sett_view&rdrto=rm_field_manage&rm_form_id=<?php echo $data->form_id; ?>"><span class="material-icons"> palette </span><?php _e('Design','registrationmagic-addon'); ?></a></li>
                    <li><a id="rm_form_preview_action" class="thickbox rm_form_preview_btn" href="<?php echo esc_url(add_query_arg(array('form_prev' => '1','form_id' => $data->form_id), get_permalink($data->prev_page))); ?>&TB_iframe=true&width=900&height=600"><span class="material-icons"> preview </span><?php _e('Preview','registrationmagic-addon'); ?></a></li>
                    <li title=""><a href="#" onclick="formPageReorder(this)"><span class="material-icons"> auto_stories </span><?php _e('Reorder Pages','registrationmagic-addon'); ?></a></li>
                </ul>
            </div>-->
            <div class="rm-form-builder-form-toggle rm-form-builder-action">
                <div class="rm-fs-toggle rm-di-flex">
                    <div class="rm-fs-toggle-head rm-box-white-bg rm-d-flex rm-box-center rm-box-border rm-fs-panel-trigger" data-panel="main">
                        <div class="rm-fs-toggle-head-wrap">
                            <div class="rm-fs-toggle-title"><?php _e('Selected Form', 'registrationmagic-addon'); ?></div>
                            <div class="rm-fs-form-selected"><?php
                                foreach ($data->forms as $form_id => $form)
                                    if ($data->form_id == $form_id)
                                        echo "<span >" . esc_html($form) . "</span>";
                                ?>
                            </div>
                        </div>
                        <span class="material-icons rm-fs-toggle-open"> expand_more </span>
                        <span class="material-icons rm-fs-toggle-close"> close </span>
                    </div>
                </div>
                
                <!----Pannel--->

                <div class="rm-fs-panel rm-fs-panel-from-top js-cd-panel-main">
                    <div class="rm-fs-panel-overlay"></div>
                    <div class="rm-fs-panel-container rm-box-white-bg rm-box-border">       
                        <div class="rm-fs-panel-content">

                            <div class="rm-fs-panel-data">

                                <div class="rm-fs-search-forms">
                                    <input type="search" id="rm-fs-search-forms" name="rm-fs-search" placeholder="Search Forms">
                                </div>

                                <div class="rm-fs-recent-forms">
                                    <div class="rm-fs-forms-title"><?php _e('Recent Forms', 'registrationmagic-addon'); ?></div>
                                    <ul id="rm-fs-recent-forms">
                                        <?php foreach ($data->recent_forms as $recent_form) { ?>
                                            <li>
                                                <a href="?page=rm_field_manage&rm_form_id=<?php echo esc_attr($recent_form['id']); ?>"><?php echo esc_html($recent_form['name']); ?>
                                                <span class="rm-fs-form-desc"><?php echo esc_html($recent_form['desc']); ?></span>
                                                </a>
                                                
                                            </li>
                                        <?php
                                        }
                                        /* foreach ($data->forms as $form_id => $form)
                                          if ($data->form_id == $form_id)
                                          echo "<li>" . esc_html($form) . "</li>";
                                          else
                                          echo "<li>" . esc_html($form) . "</li>";
                                         */
                                        ?>
                                    </ul>
                                </div>

                                <div class="rm-fs-popular-forms">
                                    <div class="rm-fs-forms-title"><?php _e('Popular Forms', 'registrationmagic-addon'); ?></div>
                                    <ul id="rm-fs-popular-forms">
                                     <?php foreach ($data->popular_forms as $popular_form) { ?>
                                            <li>
                                                <a href="?page=rm_field_manage&rm_form_id=<?php echo esc_attr($popular_form['id']); ?>"><?php echo esc_html($popular_form['name']); ?>
                                                <span class="rm-fs-form-desc"><?php echo esc_html($popular_form['desc']); ?></span>
                                                </a>
                                                
                                            </li>
                                            <?php
                                        }
                                        /* foreach ($data->forms as $form_id => $form)
                                          if ($data->form_id == $form_id)
                                          echo "<div>" . esc_html($form) . "</div>";
                                          else
                                          echo "<div>" . esc_html($form) . "</div>";
                                         */
                                        ?>
                                    </ul> 
                                </div> 

                                <div class="rm-fs-search-result" style="display:none;">
                                    <div class="rm-fs-search-result-head"><span>0</span> <?php _e('result(s) found', 'registrationmagic-addon'); ?> <a href="javascript:void(0)"><?php _e('Reset', 'registrationmagic-addon'); ?></a></div>
                                    <div class="rm-fs-forms-title"><?php _e('Search Result', 'registrationmagic-addon'); ?></div>
                                    <ul id="rm-fs-search-forms">
<?php foreach ($data->forms as $id => $form_name) { ?>
                                            <li>
                                                <a href="?page=rm_field_manage&rm_form_id=<?php echo esc_attr($id); ?>"><?php echo esc_html($form_name); ?></a>
                                            </li>
<?php } ?>
                                    </ul>
                                </div> 

                            </div>
                        </div> 
                    </div> 
                </div> 
                
                <!----Pannel--->
                
            </div>
        </div>
    </div>
</div>



<div class="rmagic rm-field-manager-main">
    <div id="rm-form-builder" class="rm-form-builder-main">
        <?php foreach ($data->ordered_form_pages as $fp_no) {//for ($i = 1; $i <= $data->total_page; $i++)
                $k = $fp_no;
                $fpage = $data->form_pages[$fp_no];
                $i = $k + 1;
        ?>
        <div id="rm-form-page-id-<?php echo $i; ?>" class="rm-form-builder-box">
            <div  class="rm-form-page-sort"><?php echo __('Page', 'registrationmagic-addon'); ?><!-- <?php echo __('Page', 'registrationmagic-addon').' '.$i; ?>--></div> 
            <div class="rm-form-page-actions rm-field-actions-item-wrap">
                <div class="rm-field-action-item rm-form-page-action"><a href="#" onclick="formPageReorder(this)"><span class="material-icons">reorder</span></a></div>                
                <div class="rm-form-page-edit  rm-field-action-item rm-form-page-action" title="Edit Page"><a onclick="rename_form_page(<?php echo $i; ?>,'<?php echo $fpage; ?>')" href="javascript:void(0)"><span class="material-icons">settings</span></a></div>
                <div class="rm-form-page-delete rm-field-action-item rm-form-page-action" title="Delete Page">
                    <?php if ($i == 1) { ?>
                    <a class="rm_deactivated" href="javascript:void(0)">
                    <?php } else { ?>
                    <a onclick="delete_page_from_page(<?php echo $i; ?>)" href="javascript:void(0)">
                    <?php } ?>
                    <span class="material-icons">delete</span>
                    </a>
                </div>
            </div>
            
            <div class="rm-form-page-name"><?php echo $fpage; ?></div>
            <ul class="rm_sortable_form_rows" id="rm-field-sortable">
                   
            <?php $is_privacy_added = 0; foreach($data->rows_data as $row_order => $row) { ?>
            <?php if($row->page_no != $i) continue; ?>
            <li class="rm-fields-box-wrap" id="<?php echo esc_attr($row->row_id); ?>">
            <div class="rm-fields-row-wrap">
                    <div class="rm-fields-row-title"><?php echo esc_html($row->heading); ?></div>
                    <div class="rm-fields-row-subtitle"><?php echo wp_kses_post(nl2br($row->subheading)); ?></div>
                </div>  
                
            <div class="rm-fields-row <?php echo 'rm-fields-' . str_replace(':', '-', $row->columns); ?>" >
                
                <div class="rm-field-move rm_sortable_handle">
                    <span class="rm-drag-sortable-handle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20">
                    <path d="M11 18c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm-2-8c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm6 4c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                    </svg>
                    </span>
                </div>
                <div class="rm-field-row-actions rm-field-actions-item-wrap">
                    <div class="rm-field-row-setting rm-field-action-item rm-field-row-action" title="Row Setting" ><a onclick="CallModalBox(this)" data-row-id="<?php echo $row->row_id; ?>" data-action="update_row" data-row-columns="<?php echo $row->columns; ?>" data-row-class="<?php echo $row->class; ?>" data-row-gutter="<?php echo $row->gutter; ?>" data-row-bmargin="<?php echo $row->bmargin; ?>" data-row-width="<?php echo $row->width; ?>" data-row-heading="<?php echo $row->heading; ?>" data-row-subheading="<?php echo $row->subheading; ?>" data-page-no="<?php echo $i; ?>"><span class="material-icons">settings</span></a></div>
                    <div class="rm-field-row-duplicate rm-field-action-item rm-field-row-action" title="Duplicate Row"><a href="?page=rm_field_manage&rm_form_id=<?php echo $data->form_id; ?>&rm_row_id=<?php echo $row->row_id; ?>&rm_form_page_no=<?php echo $row->page_no; ?>&rm_action=duplicate_row" class="rm-row-duplicate-icon"><span class="material-icons">content_copy</span></a></div>
                                        <div class="rm-field-row-delete rm-field-action-item rm-field-row-action" title="Delete Row"><a onclick="CallRowDeleteBox(this)" data-form-id="<?php echo $data->form_id; ?>" data-row-id="<?php echo $row->row_id; ?>"><span class="material-icons">delete</span></a></div>
                </div>
                <?php foreach ($row->fields as $field_order => $field) { ?>
                <?php if (!empty($field)) {
                        $is_privacy_added = 0;
                        if($field->field_type=='Privacy') {
                            $is_privacy_added = 1;
                        }
                        $f_options = maybe_unserialize($field->field_options);
                        if (isset($f_options->field_is_multiline) && $f_options->field_is_multiline == 1) {
                            $field->field_type = $field->field_type . '_M';
                        }
                        if($field->is_field_primary) {
                            array_push($primary_fields, $field->field_type);
                        }
                ?>
                <div class="rm-form-field<?php echo ($row->columns == '2:1' && $field_order == 0) ? ' rm-2-col' : ' rm-1-col'; ?>" rm-grid-id="<?php echo $field_order + 1; ?>" id="rm-col-<?php echo esc_attr($field->field_id);?>">
                    <div class="rm-col-area" id="<?php echo esc_attr($field->field_id);?>">
                        <span class="rm-field-draggable-handle">
                            <svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 0 24 24" width="15px" fill="#2271B1"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 18c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm-2-8c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm6 4c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                        </span>
                        <div class="rm-field-box-name"><?php if($field->is_field_primary && $field->field_type == 'Email') echo 'Account ' . $data->field_types[$field->field_type]; else echo $data->field_types[$field->field_type]; ?></div>
                        <div class="rm-field-box-label"><?php echo $field->field_label; ?></div>
                        <div class="rm-field-actions rm-field-actions-item-wrap" data-title="<?php if($field->is_field_primary && $field->field_type == 'Email') echo 'Account Email'; else echo $field->field_type; ?>">
                            <!--<div class="rm-field-analytics rm-field-action-item rm-field-action"><a href="<?php echo esc_attr('?page=rm_analytics_show_field&rm_form_id='.$data->form_id); ?>"><span class="material-icons">pie_chart</span></a></div> -->
                            <div class="rm-field-rules rm-field-action-item rm-field-action"><?php
                                if (empty($field->is_field_primary) && in_array($field->field_type, $allowed_c_fields)):
                                    $c_count = '';
                                    if (isset($f_options->conditions) && isset($f_options->conditions['rules']) && count($f_options->conditions['rules']) > 0) {
                                        $c_count = '' . count($f_options->conditions['rules']) . '';
                                    }
                                ?>
                                <a href="javascript:void(0)" onClick="showConditionFormModal(<?php echo $field->field_id; ?>)"><span class="material-icons">rule</span><span class="rm-conditions-badge"><?php echo $c_count; ?></span></a>
                                <?php endif; ?></div>
                            <div class="rm-field-setting rm-field-action-item rm-field-action" title="Field Setting"><a onclick="edit_field_in_page('<?php echo $field->field_type; ?>',<?php echo $field->field_id; ?>,<?php echo $i; ?>)" href="javascript:void(0)"><span class="material-icons">settings</span></a></div>
                            <div class="rm-field-delete rm-field-action-item rm-field-action" title="Delete Field">
                                <?php if ($field->is_field_primary == 1 && (!empty($field->is_deletion_allowed)) || in_array(strtolower($field->field_type), array('username','password'))): ?>
                                <a data-form-id="<?php echo $data->form_id; ?>" data-field-id="<?php echo $field->field_id; ?>" data-field-type="<?php echo $field->field_type; ?>" data-row-id="<?php echo $row->row_id; ?>" data-order="<?php echo $field_order; ?>" onclick="CallFieldDeleteBox(this)"><span class="material-icons">delete</span></a>
                                <?php elseif ($field->is_field_primary == 1 && empty($field->is_deletion_allowed)) : ?>
                                <a href="javascript:void(0)" class="rm_deactivated" onclick="CallFieldDeleteBox(this)"><span class="material-icons">delete</span></a>
                                <?php else: ?>
                                <a data-form-id="<?php echo $data->form_id; ?>" data-field-id="<?php echo $field->field_id; ?>" data-field-type="<?php echo $field->field_type; ?>" data-row-id="<?php echo $row->row_id; ?>" data-order="<?php echo $field_order; ?>" onclick="CallFieldDeleteBox(this)"><span class="material-icons">delete</span></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } else { ?>
                <div class="rm-col-draggable rm-form-field<?php echo ($row->columns == '2:1' && $field_order == 0) ? ' rm-2-col' : ' rm-1-col'; ?>" id="rm-col-<?php echo esc_attr($row->row_id.'-'.$field_order);?>">
                    <div class="rm-col-area rm-col-free" id="<?php echo 'col-row-'.$row->row_id.'-'.$field_order;?>">
                        <div class="rm-insert-field">
                        <button class="rm-insert-field-button">
                            <a href="#rm-field-selector" onclick="CallFieldModalBox(this)" data-page-no="<?php echo $i; ?>" data-row-id="<?php echo $row->row_id; ?>" data-order="<?php echo $field_order; ?>"><?php _e('Add Field','registrationmagic-addon'); ?> <span class="material-icons">add_box</span></a>
                        </button>
                        </div>
                    </div>
                </div>
                <?php } } ?>
                </div>
            </li>
            <?php } ?>
                            
            </ul>
            
            <!-- Begin: New Field -->
            <div class="rm-insert-row">
                <button class="rm-insert-row-button">
                  <a href="#rm-field-selector" onclick="CallFieldModalBox(this)" data-page-no="<?php echo esc_attr($i); ?>" data-row-id="0" data-order="0"><?php _e('Add Field', 'registrationmagic-addon'); ?> <span class="material-icons">add_box</span></a>
                </button>
            </div>
            
             <!-- Ends: New Field -->
            
            <!-- Begin: Submit Field -->
            <?php
            $submit_label = ($data->form_options->form_submit_btn_label) ? $data->form_options->form_submit_btn_label : __('Submit', 'registrationmagic-addon');
            $prev_label = ($data->form_options->form_prev_btn_label) ? $data->form_options->form_prev_btn_label : RM_UI_Strings::get('LABEL_PREV_FORM_PAGE');
            $next_label = ($data->form_options->form_next_btn_label) ? $data->form_options->form_next_btn_label : __('Next', 'registrationmagic-addon');
            $btn_align = ($data->form_options->form_btn_align) ? $data->form_options->form_btn_align : "center";
            $ralign_check_state = $lalign_check_state = $calign_check_state = "";
            if ($btn_align === "right")
                $ralign_check_state = "checked";
            else if ($btn_align === "left")
                $lalign_check_state = "checked";
            else
                $calign_check_state = "checked";

            $hideprev_check_style = "";
            if (count($data->ordered_form_pages) > 1) {
                $prev_btn_style = $data->form_options->no_prev_button ? 'style="display:none"' : "";
                $hideprev_check_state = $data->form_options->no_prev_button ? 'checked' : "";
            } else {
                $prev_btn_style = 'style="display:none"';
                $hideprev_check_style = 'style="visibility:hidden"';
                $hideprev_check_state = $data->form_options->no_prev_button ? 'checked' : "";
            }
            ?>
            <div class="rm-field-submit-field-holder">
                <div class="rm-field-submit-field">
                    <div class="rm-field-submit-field-btn-container rm-field-btn-align-<?php echo $btn_align; ?>">
                        &#8203;<!-- Zero width space character is added to workaround webkit bug where clicking outside the div enables editing of the content. -->
                        <div class="rm-field-prev-btn rm_field_btn" title="<?php _e('Click to edit button label', 'registrationmagic-addon'); ?>" contenteditable="true" spellcheck="false" <?php echo $prev_btn_style; ?>><?php echo htmlentities(stripslashes((string)$prev_label)); ?></div>
                        &#8203;
                        <?php if($fp_no != end($data->ordered_form_pages)) { ?>
                        <div class="rm-field-next-btn rm_field_btn" title="<?php _e('Click to edit button label', 'registrationmagic-addon'); ?>" contenteditable="true" spellcheck="false"><?php echo htmlentities(stripslashes((string)$next_label)); ?></div>
                        &#8203;
                        <?php } ?>
                        <?php if($fp_no == end($data->ordered_form_pages)) { ?>
                        <div class="rm-field-sub-btn rm_field_btn" id="rm_field_sub_button" title="<?php _e('Click to edit button label', 'registrationmagic-addon'); ?>" contenteditable="true" spellcheck="false"><?php echo htmlentities(stripslashes((string)$submit_label)); ?></div>
                        &#8203;
                        <?php } ?>
                    </div>
                    <div class="rm-field-submit-field-options">
                        <div class="rm-field-submit-field-option-row rm-field-submit-hide-prev" <?php echo $hideprev_check_style; ?>>
                            <input type="checkbox" id="rm_field_hide_prev_button" name="rm_field_hide_prev_button" <?php echo $hideprev_check_state; ?>>
                            <!--                                            <label class="rm-label-normalized" for="rm_field_hide_prev_button">-->
                            <?php echo RM_UI_Strings::get("LABEL_HIDE_PREV_FIELDMAN"); ?>
                            <!--                                            </label>-->
                        </div>
                            <div class="rm-field-submit-field-option-row rm-field-submit-alignment">
                                <div class="rm-field-submit-field-wrap">
                                    <input type="radio" name="rm_field_submit_field_align" value="left" id="rm_field_submit_field_align_left" <?php echo $lalign_check_state; ?> ><label for="rm_field_submit_field_align_left"><span class="material-icons"> arrow_back</span></label>
                                    <input type="radio" name="rm_field_submit_field_align" value="center" id="rm_field_submit_field_align_center" <?php echo $calign_check_state; ?> ><label for="rm_field_submit_field_align_center" class="rm_submit_field_align_center"><span class="material-icons"> vertical_align_center </span></label>
                                    <input type="radio" name="rm_field_submit_field_align" value="right" id="rm_field_submit_field_align_right" <?php echo $ralign_check_state; ?> ><label for="rm_field_submit_field_align_right"><span class="material-icons"> arrow_forward </span></label>
        <!--                        <input type="radio" name="rm_field_submit_field_align" value="left" id="rm_field_submit_field_align_left" <?php // echo $lalign_check_state;   ?> ><label for="rm_field_submit_field_align_left"><?php //echo RM_UI_Strings::get('LABEL_LEFT');  ?></label>
                                   <input type="radio" name="rm_field_submit_field_align" value="center" id="rm_field_submit_field_align_center" <?php // echo $calign_check_state;   ?> ><label for="rm_field_submit_field_align_center"><?php //echo RM_UI_Strings::get('LABEL_CENTER');  ?></label>
                                   <input type="radio" name="rm_field_submit_field_align" value="right" id="rm_field_submit_field_align_right" <?php //echo $ralign_check_state;   ?> ><label for="rm_field_submit_field_align_right"><?php //echo RM_UI_Strings::get('LABEL_RIGHT');  ?></label>-->
                                </div>
                            </div>
                        <div class="rm-field-submit-field-option-row rm-field-submit-ajax-loader" style="visibility: hidden">
                            <?php _e('Updating...', 'registrationmagic-addon'); ?>
                        </div>
                    </div>
                </div>

                <div class="rm-field-submit-field-hint"><?php _e('Click on buttons to edit label', 'registrationmagic-addon'); ?></div>
            </div>
            <!-- End: Submit Field -->
            
            <!--
            <div class="rm-form-buttons-row">
                <div class="rm-form-buttons-holder">
                    <div class="rm-form-button-field rm-submit-field-btn"> Submit </div>
                    <div class="rm-form-buttons-setting"><a href="javascript:void(0)" onclick="CallFormButtonSettings(this)"><span class="material-icons">settings</span></a></div>
                </div>
            </div>
            -->
            
        </div>
        <?php } ?>
        <!--- Insert Page 2 --->
        <div class="rm-insert-new-form-page">
            <button class="rm-insert-new-form-page-button" onclick="add_new_page_to_form()">
                <a href="javascript:void(0)"><?php _e('Add Page','registrationmagic-addon'); ?> <span class="material-icons">add_box</span></a>
            </button>
        </div>
        <!--- Page 2 End --->
    </div>
    <!-- Row Setting Popup -->
    <div id="rm-field-row-setting-modal" class="rm-modal-view" style="display: none;">
        <form method="post" action="" id="rm-row-add-edit-form">
        <div class="rm-modal-overlay rm-field-popup-overlay-fade-in"></div>
        <div class="rm_field_row_setting_wrap rm-select-row-setting rm-field-popup-out">
            <div class="rm-modal-titlebar rm-new-form-popup-header">
                <div class="rm-modal-title">
                    <?php _e('Row Properties','registrationmagic-addon'); ?>
                </div>
                <span class="rm-modal-close">×</span>
            </div>
            <div class="rm-modal-container">
                <div class="rm-field-row-wrap">
                    
                    <div class="rmrow">
                      <div class="rm-field-columns-head">
                                <div class="rm-field-column-label"><?php _e('Heading (Optional)','registrationmagic-addon'); ?></div>
                                <input type="text" placeholder="Heading" id="rm-row-heading" class="rm-form-column-control" name="heading" value="">
                                <div class="rm-form-column-help-text"><?php _e('Heading text for the fields in this row. Rendered on frontend with larger font size.','registrationmagic-addon'); ?></div>
                        </div>
                        
                        <div class="rm-field-columns-head rm-mt-2">
                                <div class="rm-field-column-label"><?php _e('Sub-heading (Optional)','registrationmagic-addon'); ?></div>
                                <textarea placeholder="<?php _e('Sub Heading', 'custom-registration-form-builder-with-submission-manager'); ?>" name="subheading" cols="3" class="rm-form-column-control" id="rm-row-subheading"></textarea>
                                <div class="rm-form-column-help-text"><?php _e('Subtitle for the fields in this row. Rendered on frontend with muted body font.','registrationmagic-addon'); ?></div>
                        </div> 
                        
                    </div>
                    
                    <div class="rmrow">
                        <div class="rm-fields-columns-wrap">
                            <div class="rm-fields-columns">
                                <h3><?php _e('Field Columns','registrationmagic-addon'); ?></h3>
                                <ul>
                                    <li>
                                        <div class="rm-fields-column">
                                            <span style="width: 100%"></span>
                                        </div>
                                        <label> 
                                            <input type="radio" class="rm-field-radio" value="1" name="columns" data-allowed-fields="1" onclick="rmColumnSelector(this)" /><?php _e('Single','registrationmagic-addon'); ?></label>
                                    </li>
                                    <li>
                                        <div class="rm-fields-column">
                                            <span style="width: 50%"></span>
                                            <span style="width: 50%"></span>
                                        </div>
                                        <label>
                                            <input type="radio" class="rm-field-radio" value="1:1" name="columns" data-allowed-fields="2" onclick="rmColumnSelector(this)" /><?php _e('1:1','registrationmagic-addon'); ?></label>
                                    </li>
                                    <li>
                                        <div class="rm-fields-column">
                                            <span style="width: 75%"></span>
                                            <span style="width: 25%"></span>
                                        </div>
                                        <label>
                                            <input type="radio" class="rm-field-radio" value="2:1" name="columns" data-allowed-fields="2" onclick="rmColumnSelector(this)" /><?php _e('2:1','registrationmagic-addon'); ?></label>
                                    </li>
                                    <li>
                                        <div class="rm-fields-column">
                                            <span style="width: 33%"></span>
                                            <span style="width: 33%"></span>
                                            <span style="width: 33%"></span>
                                        </div>
                                        <label>
                                            <input type="radio" class="rm-field-radio" value="1:1:1" name="columns" data-allowed-fields="3" onclick="rmColumnSelector(this)" /><?php _e('1:1:1','registrationmagic-addon'); ?></label>
                                    </li>
                                    <li>
                                        <div class="rm-fields-column">
                                            <span style="width: 25%"></span>
                                            <span style="width: 25%"></span>
                                            <span style="width: 25%"></span>
                                            <span style="width: 25%"></span>
                                        </div>
                                        <label>
                                            <input type="radio" class="rm-field-radio" value="1:1:1:1" name="columns" data-allowed-fields="4" onclick="rmColumnSelector(this)"/><?php _e('1:1:1:1','registrationmagic-addon'); ?></label>
                                    </li>
                                </ul>
                            </div>
                            <div class="rm-alert rm-alert-warning rm-mt-3" id="rm_row_col_edit_notice" style="display:none;"><?php esc_html_e('The number of columns cannot be less than the number of fields in this row.','registrationmagic-addon'); ?></div>
                        </div>
                    </div>
                    <div class="rmrow">
                        <h3><?php _e('Optional styling:','registrationmagic-addon'); ?></h3>
                    </div>
                    <div class="rmrow">
                        <div class="rm-field-columns-setting">
                            <div class="rm-field-columns">
                                <div class="rm-field-column-head"><?php _e('CSS Class','registrationmagic-addon'); ?></div>
                                <input type="text" placeholder="E.g. row" id="rm-row-css-class" class="rm-form-column-control" name="class" value="">
                                <div class="rm-form-column-help-text"><?php _e('Add additional CSS class to the row for custom styling.','registrationmagic-addon'); ?></div>
                            </div>
                            <div class="rm-field-columns">
                                <div class="rm-field-column-head"><?php _e('Gutter','registrationmagic-addon'); ?></div>
                                <input type="number" placeholder="E.g. 24" id="rm-column-gutter" class="rm-form-column-control" name="gutter" value=""  min="0" max="30"> 
                                <span>px</span>
                                <div class="rm-form-column-help-text"><?php _e('Define spacing between columns in this row in px. Does not applies to single column layouts.','registrationmagic-addon'); ?></div>
                            </div>
                            <div class="rm-field-columns">
                                <div class="rm-field-column-head"><?php _e('Bottom Margin','registrationmagic-addon'); ?></div>
                                <input type="number" placeholder="E.g. 48" id="rm-column-bmargin" class="rm-form-column-control" name="bmargin" value="" min="0"> 
                                <span>px</span>
                                <div class="rm-form-column-help-text"><?php _e('Add additional vertical spacing between this row, and the the row just below, in px.','registrationmagic-addon'); ?></div>
                            </div>
                            <div class="rm-field-columns">
                                <div class="rm-field-column-head"><?php _e('Max Width','registrationmagic-addon'); ?></div>
                                <input type="number" placeholder="E.g. 800" id="rm-column-width" class="rm-form-column-control" name="width" value=""  min="0" max="1500"> 
                                <span>px</span>
                                <div class="rm-form-column-help-text"><?php _e('Define maximum width for this row in px. Leave empty for full-width (justified).','registrationmagic-addon'); ?></div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="form-id" value="<?php echo $data->form_id; ?>">
                    <input type="hidden" name="page-no" value="1">
                    <input type="hidden" name="rm_row_id" value="-1">
                    <input type="hidden" name="rm_action" value="add_row">
                    <div class="rm-form-builder-modal-footer rm-justify-content-between rm-pt-2 rm-pb-3">
                        <div class="rm-cancel-row-setting"><a href="javascript:void(0)" class="button rm-bg-white button-secondary rm-modal-close">← &nbsp;<?php _e('Cancel','registrationmagic-addon'); ?></a></div>
                        <div class="rm-save-row-setting"><input type="submit" value="Save" class="button button-primary"></div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    <!-- Row Setting Popup End -->  
    <!-- Row Delete Popup -->
    <div id="rm-field-row-delete-modal" class="rm-modal-view" style="display: none;">
        <div class="rm-modal-overlay rm-field-popup-overlay-fade-in"></div>
        <div class="rm_field_row_setting_wrap rm-select-row-setting rm-field-popup-out">
            <div class="rm-modal-titlebar rm-new-form-popup-header">
                <div class="rm-modal-title">
                    <?php _e('Delete Row','registrationmagic-addon'); ?>
                </div>
                <span class="rm-modal-close">×</span>
            </div>
            <div class="rm-modal-container">
                <div class="rmrow rm-my-4">
                    <div class="rm-delete-row-info-icon rm-mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="62" height="62" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                            <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                            <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                            </svg>
                    </div>
                    
                    <div class="rm-delete-row-info-text">
                        <?php _e('Are you sure you want to delete this row?','registrationmagic-addon'); ?>
                    </div>
                </div>
     
                <div class="rm-form-builder-modal-footer rm-justify-content-between rm-pt-2 rm-pb-3">
                    <div class="rm-cancel-delete-action"><a class="button rm-bg-white button-secondary rm-modal-close" href="javascript:void(0)">← &nbsp;<?php _e('Cancel','registrationmagic-addon'); ?></a></div>
                    <div class="rm-confirm-delete-action"><a class="rm-btn-danger button-primary" id="rm-delete-row-link"><?php _e('Delete','registrationmagic-addon'); ?></a></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row Delete Popup End -->
    <!-- Field Delete Popup -->
    <div id="rm-field-delete-modal" class="rm-modal-view" style="display: none;">
        <div class="rm-modal-overlay rm-field-popup-overlay-fade-in"></div>
        <div class="rm_field_row_setting_wrap rm-select-row-setting rm-field-popup-out">
            <div class="rm-modal-titlebar rm-new-form-popup-header">
                <div class="rm-modal-title" id="rm-field-delete-modal-title">
                    <?php _e('Delete Field','registrationmagic-addon'); ?>
                </div>
                <span class="rm-modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                </span>
            </div>
            <div class="rm-modal-container">
                <div class="rmrow rm-my-4">
                     <div class="rm-delete-row-info-icon rm-mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="62" height="62" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                            <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                            <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                            </svg>
                    </div>
                    
                    <div class="rm-delete-row-info-text" id="rm-field-delete-modal-info">
                        <?php _e('Are you sure you want to delete this field?','registrationmagic-addon'); ?>
                    </div>
                </div>

                <div class="rm-form-builder-modal-footer rm-justify-content-between rm-pt-2 rm-pb-3">
                    <div class="rm-cancel-delete-action"><a class="button rm-bg-white button-secondary rm-modal-close" href="javascript:void(0)">← &nbsp;<?php _e('Cancel','registrationmagic-addon'); ?></a></div>
                    <div class="rm-confirm-delete-action"><a class="rm-btn-danger button-primary" id="rm-delete-field-link" href="javascript:void(0)"><?php _e('Delete','registrationmagic-addon'); ?></a></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Field Delete Popup End -->

    <!--- Field Selector PopUp -->
    <div id="rm-field-selector" class="rm-modal-view" style="display:none">
        <div class="rm-modal-overlay"></div> 

        <div class="rm-modal-wrap">
            <div class="rm-modal-titlebar">
                <div class="rm-modal-title"> <?php _e('Choose a field type','registrationmagic-addon'); ?></div>
                <span  class="rm-modal-close">&times;</span>
            </div>
            <div class="rm-modal-container">
            <div class="rmrow">
                <div class="rm-field-selector">
                    <?php require RM_ADMIN_DIR."views/template_rm_field_picker.php"; ?>
                </div>
            </div>
            </div>
        </div>
    </div>
    <!---End Field Selector PopUp -->
    
    <!--- Widget Selector PopUp -->
    <div id="rm-widget-selector" class="rm-modal-view" style="display:none">
        <div class="rm-modal-overlay"></div> 

        <div class="rm-modal-wrap">
            <div class="rm-modal-titlebar">
                <div class="rm-modal-title"><?php _e('MagicWidgets', 'registrationmagic-addon'); ?></div>
                <span  class="rm-modal-close">&times;</span>
            </div>
            <div class="rm-modal-container">
                <div class="rmrow">
                    <div class="rm-widget-selector">
                        <?php require RM_ADMIN_DIR . "views/template_rm_widget_picker.php"; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!---End Widget Selector PopUp -->
    
    
    <!-- User Name Row Delete Popup -->
    <div id="rm-username-delete-row" class="rm-modal-view" style="display: none;">
        <div class="rm-modal-overlay rm-field-popup-overlay-fade-in"></div>
        <div class="rm_field_row_setting_wrap rm-select-row-setting rm-field-popup-out">
            <div class="rm-modal-titlebar rm-new-form-popup-header">
                <div class="rm-modal-title">
                    <?php _e('Delete Row','registrationmagic-addon'); ?>
                </div>
                <span class="rm-modal-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                </span>
            </div>
            <div class="rm-modal-container">
                <div class="rmrow rm-my-4">
                    <div class="rm-delete-row-info-icon rm-mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="62" height="62" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                            <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                            <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                            </svg>
                    </div>
                    
                    <div class="rm-delete-row-info-text rm-text-dark">
                    <?php _e('There are fields inside this row. Please delete them first.','registrationmagic-addon'); ?>
                    </div>
                    
                </div>
          
                <!--
                <div class="rm-form-builder-modal-footer">
                    <div class="rm-confirm-delete-action"><a id="rm-delete-row-link">Yes, Remove Username</a></div>
                    <div class="rm-cancel-delete-action"><a id="rm-delete-row-link">No, Keep Username</a></div>
                </div>
                -->
            </div>
        </div>
    </div>
        
    <!-- Row Delete Popup End -->
    
    
    
    
    <!-- Form Button Setting Popup Popup -->

<div id="rm-form-button-settings-modal" class="rm-modal-view" style="display: none;">
        <div class="rm-modal-overlay rm-field-popup-overlay-fade-in"></div>

        <div class="rm_field_row_setting_wrap rm-select-row-setting rm-field-popup-out">
            <div class="rm-modal-titlebar rm-new-form-popup-header">
                <div class="rm-modal-title">
                 <?php _e('Styling & Buttons','registrationmagic-addon'); ?>
                </div>
                <span class="rm-modal-close">×</span>
            </div>
            <div class="rm-modal-container">
                

                <div class="rmrow">

                  <label for="rm-field-custom-submit-text" class="rm-label"><?php _e('Button text','registrationmagic-addon'); ?></label>
                    <input type="text" placeholder="Enter text" id="rm-field-custom-submit-text" class="rm-form-control" value="Submit">

                      </div>

                    <div class="rmrow">
                         <label for="rm-field-custom-submit-text" class="rm-label"><?php _e('Button align','registrationmagic-addon'); ?></label>

                         <div class="rm-field-submit-field-option-row rm-field-submit-alignment">
                            <input type="radio" name="rm_field_submit_field_align" value="left" id="rm_field_submit_field_align_left"><label for="rm_field_submit_field_align_left"><?php _e('Left','registrationmagic-addon'); ?></label>
                            <input type="radio" name="rm_field_submit_field_align" value="center" id="rm_field_submit_field_align_center"><label for="rm_field_submit_field_align_center"><?php _e('Center','registrationmagic-addon'); ?></label>
                            <input type="radio" name="rm_field_submit_field_align" value="right" id="rm_field_submit_field_align_right" checked=""><label for="rm_field_submit_field_align_right"><?php _e('Right','registrationmagic-addon'); ?></label>
                        </div>
                    </div>
                
   

                <div class="rm-form-builder-modal-footer">
                    <div class="rm-discard-setting"><a href="javascript:void(0)">← &nbsp;<?php _e('Cancel','registrationmagic-addon'); ?></a></div> 
                    <div class="rm-save-setting"><button type="button" class="rm-save-setting-button"><?php _e('Save','registrationmagic-addon'); ?></button></div>

                </div>

            </div>
        </div>
    </div>


<!-- Form Button Setting Popup End -->
    
    
    
    
    
        <!--- Field Conditions PopUp -->
        <?php 
            // Including field condition template
            include RM_ADMIN_DIR."views/template_rm_field_conditions.php";  
         ?>
        <!--- End Field Conditions PopUp -->
        
        
        
            <!-- Reorder Field  Popup -->
    <div id="rm-field-reorder-modal" class="rm-modal-view" style="display: none;">
        <div class="rm-modal-overlay rm-field-popup-overlay-fade-in"></div>
        <div class="rm_field_row_setting_wrap rm-select-row-setting rm-field-popup-out">
            <div class="rm-modal-titlebar rm-new-form-popup-header">
                <div class="rm-modal-title" id="rm-field-delete-modal-title">
                    <?php _e('Rearrange Form Pages','registrationmagic-addon'); ?>
                </div>
                <span class="rm-modal-close" onclick="reset_page_order();">×</span>
            </div>
            <div class="rm-modal-container">
                <div class="rm-reoder-modal-box-wrap">
                    <ul id="rm-form-page-sortable">
                        <?php foreach ($data->ordered_form_pages as $fp_no) { //for ($i = 1; $i <= $data->total_page; $i++)
                                $k = $fp_no;
                                $fpage = $data->form_pages[$fp_no];
                                $i = $k + 1;
                        ?>
                        <li class="ui-state-default rm-form-page-sortable-box" id="<?php echo $k; ?>"><span><?php echo $fpage; ?></span></li>
                        <?php } ?>
                    </ul>


                </div>
                <div class="rmrow">
          
                </div>
                <div class="rm-form-builder-modal-footer">
                    <div class="rm-cancel-delete-action"><a href="#" onclick="reset_page_order();">← &nbsp;<?php _e('Cancel','registrationmagic-addon'); ?></a></div>
                    <div class="rm-confirm-reorder-bt"><a id="#" href="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>"><?php _e('Done','registrationmagic-addon'); ?></a></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Reorder Field  PopupEnd -->
        
        
        
</div>







<script>
    var field_order_in_row = -1;
    var row_id_for_field = -1;
    var curr_form_page_for_field = 1;
    function CallModalBox(ele) {
        var fields_in_this_row = 0;
        jQuery('li#' + jQuery(ele).data('row-id')).find('div.rm-field-box-name').each(function(index) {
            fields_in_this_row++;
        });
        if(fields_in_this_row > 0) {
            if(fields_in_this_row > 1) {
                jQuery('div#rm_row_col_edit_notice').show();
            } else {
                jQuery('div#rm_row_col_edit_notice').hide();
            }
            jQuery('input[name=columns]').each(function(index) {
                if(jQuery(this).data('allowed-fields') < fields_in_this_row) {
                    jQuery(this).attr("disabled", true);
                } else {
                    jQuery(this).attr("disabled", false);
                }
                
                if(jQuery(ele).data('row-columns') != '') {
                    if(jQuery(this).val() == jQuery(ele).data('row-columns')) {
                        jQuery(this).prop("checked", true);
                    } else {
                        jQuery(this).prop("checked", false);
                    }
                } else {
                    var column_counter = '1';
                    for(i=1;i<fields_in_this_row;i++) {
                        column_counter += ':1';
                    }
                    if(jQuery(this).val() == column_counter) {
                        jQuery(this).prop("checked", true);
                    } else {
                        jQuery(this).prop("checked", false);
                    }
                }
            });
        } else {
            jQuery('div#rm_row_col_edit_notice').hide();
            jQuery('input[name=columns]').each(function(index) {
                jQuery(this).removeAttr("disabled");
            });
        }
        jQuery("#rm-field-row-setting-modal").toggle();
        jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').removeClass('rm-field-popup-out');
        jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').addClass('rm-field-popup-in');
        
        jQuery('.rmagic .rm-modal-view').addClass('rm-form-popup-show').removeClass('rm-form-popup-hide');

        jQuery('.rm-modal-overlay').removeClass('rm-field-popup-overlay-fade-out');
        jQuery('.rm-modal-overlay').addClass('rm-field-popup-overlay-fade-in');
        
        jQuery('input[name=columns][value="' + jQuery(ele).data('row-columns') + '"]').attr('checked',true);
        jQuery('input[name=class]').val(jQuery(ele).data('row-class'));
        jQuery('input[name=gutter]').val(jQuery(ele).data('row-gutter'));
        jQuery('input[name=bmargin]').val(jQuery(ele).data('row-bmargin'));
        jQuery('input[name=width]').val(jQuery(ele).data('row-width'));
        jQuery('input[name=heading]').val(jQuery(ele).data('row-heading'));
        jQuery('textarea[name=subheading]').val(jQuery(ele).data('row-subheading'));
        jQuery('input[name=page-no]').val(jQuery(ele).data('page-no'));
        jQuery('input[name=rm_row_id]').val(jQuery(ele).data('row-id'));
        jQuery('input[name=rm_action]').val(jQuery(ele).data('action'));
    }
    
    function CallFieldModalBox(ele) {
        jQuery(jQuery(ele).attr('href')).toggle();
        field_order_in_row = jQuery(ele).data('order');
        row_id_for_field = jQuery(ele).data('row-id');
        curr_form_page_for_field = jQuery(ele).data('page-no');
        jQuery('.rmagic .rm-modal-view').addClass('rm-form-popup-show').removeClass('rm-form-popup-hide');
        jQuery('.rmagic .rm-modal-wrap').removeClass('rm-field-popup-out');
        jQuery('.rmagic .rm-modal-wrap').addClass('rm-field-popup-in');
    }
    
    
    function CallFormButtonSettings(ele) {
    jQuery("#rm-form-button-settings-modal").toggle();
          if(jQuery(ele).attr('href')=='#rm-form-button-settings-modal'){
            jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').removeClass('rm-field-popup-out');
            jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').addClass('rm-field-popup-in');

            jQuery('.rm-modal-overlay').removeClass('rm-field-popup-overlay-fade-out');
            jQuery('.rm-modal-overlay').addClass('rm-field-popup-overlay-fade-in');
          }
    }
    
    
    function CallRowDeleteBox(ele) {
        var fieldCheck = false;
        jQuery('li#' + jQuery(ele).data('row-id')).find('div.rm-field-actions').each(function(index) {
            if(jQuery(this).data('title') == 'Username' || jQuery(this).data('title') == 'UserPassword' || jQuery(this).data('title') == 'Account Email') { fieldCheck = true; }
        });
        if(fieldCheck) {
            jQuery("#rm-username-delete-row").toggle();
        
            jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').removeClass('rm-field-popup-out');
            jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').addClass('rm-field-popup-in');
            jQuery('.rmagic .rm-modal-view').addClass('rm-form-popup-show').removeClass('rm-form-popup-hide'); 

            jQuery('.rm-modal-overlay').removeClass('rm-field-popup-overlay-fade-out');
            jQuery('.rm-modal-overlay').addClass('rm-field-popup-overlay-fade-in');
        } else {
            jQuery("#rm-field-row-delete-modal").toggle();
            jQuery('.rmagic .rm-modal-view').addClass('rm-form-popup-show').removeClass('rm-form-popup-hide');
            jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').removeClass('rm-field-popup-out');
            jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').addClass('rm-field-popup-in');

            jQuery('.rm-modal-overlay').removeClass('rm-field-popup-overlay-fade-out');
            jQuery('.rm-modal-overlay').addClass('rm-field-popup-overlay-fade-in');

            jQuery('#rm-delete-row-link').attr('href', '?page=rm_field_manage&rm_form_id=' + jQuery(ele).data('form-id') + '&rm_row_id=' + jQuery(ele).data('row-id') + '&rm_action=delete_row');
        }
    }


    function CallFieldDeleteBox(ele) {
        if(jQuery(ele).hasClass('rm-premium-option')) {
            jQuery('.rm-premium-option-popup').toggle();
        } else {
            if(jQuery(ele).data('field-type').toLowerCase() == 'username') {
                jQuery('#rm-field-delete-modal-title').text('<?php _e('Delete Username Field','registrationmagic-addon'); ?>');
                jQuery('#rm-field-delete-modal-info').text('<?php _e('After removing the Username field, Email field will be used as login ID. Do you wish to proceed? ','registrationmagic-addon'); ?>');
            }
            if(jQuery(ele).data('field-type').toLowerCase() == 'userpassword') {
                jQuery('#rm-field-delete-modal-title').text('<?php _e('Delete Password Field','registrationmagic-addon'); ?>');
                jQuery('#rm-field-delete-modal-info').text('<?php _e('After removing the Password Field, passwords will be automatically generated and emailed to the user on successful registration. Do you wish to proceed?','registrationmagic-addon'); ?>');
            }
            jQuery("#rm-field-delete-modal").toggle();
            jQuery('.rmagic .rm-modal-view').addClass('rm-form-popup-show').removeClass('rm-form-popup-hide');
            jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').removeClass('rm-field-popup-out');
            jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').addClass('rm-field-popup-in');

            jQuery('.rm-modal-overlay').removeClass('rm-field-popup-overlay-fade-out');
            jQuery('.rm-modal-overlay').addClass('rm-field-popup-overlay-fade-in');

            jQuery('#rm-delete-field-link').attr('href', '?page=rm_field_manage&rm_form_id=' + jQuery(ele).data('form-id') + '&rm_field_id=' + jQuery(ele).data('field-id') + '&rm_row_id=' + jQuery(ele).data('row-id') + '&rm_order_in_row=' + jQuery(ele).data('order') + '&rm_action=delete');
        }
    }
    
    
    function CallUserNameDeleteRow(ele) {
        jQuery("#rm-username-delete-row").toggle();
        
        jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').removeClass('rm-field-popup-out');
        jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').addClass('rm-field-popup-in');

        jQuery('.rm-modal-overlay').removeClass('rm-field-popup-overlay-fade-out');
        jQuery('.rm-modal-overlay').addClass('rm-field-popup-overlay-fade-in');

        jQuery('#rm-delete-field-link').attr('href', '?page=rm_field_manage&rm_form_id=' + jQuery(ele).data('form-id') + '&rm_field_id=' + jQuery(ele).data('field-id') + '&rm_row_id=' + jQuery(ele).data('row-id') + '&rm_order_in_row=' + jQuery(ele).data('order') + '&rm_action=delete');
    }
    
    function formPageReorder(ele) {
        jQuery("#rm-field-reorder-modal").toggle();
        
        jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').removeClass('rm-field-popup-out');
        jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').addClass('rm-field-popup-in');

        jQuery('.rm-modal-overlay').removeClass('rm-field-popup-overlay-fade-out');
        jQuery('.rm-modal-overlay').addClass('rm-field-popup-overlay-fade-in');
    }


    jQuery(document).ready(function () {
        jQuery('.rm-modal-close, .rm-modal-overlay').click(function () {
            //setTimeout(function () {
                //jQuery(this).parents('.rm-modal-view').hide();
               jQuery('.rm-modal-view').hide();
            //}, 400);
             
             jQuery('.rmagic .rm-modal-view').addClass('rm-form-popup-hide').removeClass('rm-form-popup-show'); 
             
            jQuery('#rm-field-delete-modal-title').text('<?php _e('Delete Field','registrationmagic-addon'); ?>');
            jQuery('#rm-field-delete-modal-info').text('<?php _e('Are you sure you want to delete this field?','registrationmagic-addon'); ?>');
        });

        jQuery('a.rm-row-duplicate-icon').click(function() {
            jQuery('a.rm-row-duplicate-icon').addClass('rm_deactivated');
        });

        jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting .rm-modal-close, #rm-field-row-setting-modal .rm-modal-overlay').on('click', function () {
            jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').removeClass('rm-field-popup-in');
            jQuery('.rmagic .rm_field_row_setting_wrap.rm-select-row-setting').addClass('rm-field-popup-out');

            jQuery('.rm-modal-overlay').removeClass('rm-field-popup-overlay-fade-in');
            jQuery('.rm-modal-overlay').addClass('rm-field-popup-overlay-fade-in');
        });

        jQuery("body").addClass("registrationmagic-form-builder");
        
        rm_init_submit_field();
    });


    function rmColumnSelector(checkbox) {
        var checkboxes = document.getElementsByName('check')
        checkboxes.forEach((item) => {
            if (item !== checkbox)
                item.checked = false
        });
    }

    function edit_field_in_page(field_type, field_id, page_no) {
        if (field_type == undefined || field_id == undefined)
            return;
        var curr_form_page = page_no;// = (jQuery("#rm_form_page_tabs").tabs("option", "active")) + 1;
        if (["HTMLP", "HTMLH", "Divider", "Spacing", "RichText", "Timer", "Link", "YouTubeV", "Iframe", "ImageV", "PriceV", "SubCountV", "MapV", "Form_Chart", "FormData", "Feed"].indexOf(field_type) >= 0)
            var loc = "?page=rm_field_add_widget&rm_form_id=<?php echo $data->form_id; ?>&rm_form_page_no=" + curr_form_page + "&rm_field_type";
        else
            var loc = "?page=rm_field_add&rm_form_id=<?php echo $data->form_id; ?>&rm_form_page_no=" + curr_form_page + "&rm_field_type";
        loc += ('=' + field_type);
        loc += "&rm_field_id=" + field_id;
        window.location = loc;
    }
    
    function add_new_field_to_page(field_type) {
        var curr_form_page = get_current_form_page();
        var loc = "?page=rm_field_add&rm_form_id=<?php echo $data->form_id; ?>&rm_form_page_no=" + curr_form_page + "&rm_row_id=" + row_id_for_field + "&rm_order_in_row=" + field_order_in_row + "&rm_field_type";
        if (field_type !== undefined)
            loc += ('=' + field_type);
        window.location = loc;
    }

    function add_user_field_to_page(field_type) 
    {
        var curr_form_page = get_current_form_page();//(jQuery("#rm_form_page_tabs").tabs("option", "active")) + 1;
        var loc = "?page=rm_field_manage&rm_form_id=<?php echo esc_attr($data->form_id); ?>&rm_form_page_no=" + curr_form_page + "&rm_row_id=" + row_id_for_field + "&rm_order_in_row=" + field_order_in_row + "&rm_field_type";
        if (field_type !== undefined)
            loc += ('=' + field_type);
        window.location = loc;
    }
    
    function add_new_widget_to_page(widget_type) {
        var curr_form_page = get_current_form_page();//(jQuery("#rm_form_page_tabs").tabs("option", "active")) + 1;
        var loc = "?page=rm_field_add_widget&rm_form_id=<?php echo $data->form_id; ?>&rm_form_page_no=" + curr_form_page + "&rm_row_id=" + row_id_for_field + "&rm_order_in_row=" + field_order_in_row + "&rm_field_type";
        if (widget_type !== undefined)
            loc += ('=' + widget_type);
        window.location = loc;
    }
    
    function add_user_field_to_page(field_type) {
        var extra_param = '';
        var curr_form_page = get_current_form_page();//(jQuery("#rm_form_page_tabs").tabs("option", "active")) + 1;
        var loc = "?page=rm_field_manage&rm_form_id=<?php echo $data->form_id; ?>&rm_form_page_no=" + curr_form_page + "&rm_row_id=" + row_id_for_field + "&rm_order_in_row=" + field_order_in_row + "&rm_field_type";
        if (field_type !== undefined)
            loc += ('=' + field_type + extra_param);
        window.location = loc;
    }
    
    function rm_init_submit_field() {
        jQuery(".rm_field_btn").on("keydown", function (e) {
            if (e.keyCode === 13 || e.keyCode === 27) {
                jQuery(this).blur();
                window.getSelection().removeAllRanges();
            }
        })

        var last_label;

        jQuery(".rm_field_btn").on("focus", function (e) {
            var temp = jQuery(this).text().trim();
            if (temp.length)
                last_label = temp;
        })

        jQuery(".rm_field_btn").on("blur", function (e) {
            var temp = jQuery(this).text().trim();
            if (temp.length <= 0) {
                jQuery(this).text(last_label);
            } else {
                if(jQuery(this).hasClass('rm-field-prev-btn')) {
                    jQuery('.rm-field-prev-btn').text(temp);
                }
                if(jQuery(this).hasClass('rm-field-next-btn')) {
                    jQuery('.rm-field-next-btn').text(temp);
                }
                rm_update_submit_field();
            }
        })

        jQuery("input[name='rm_field_submit_field_align']").change(function (e) {
            var $btn_container = jQuery(".rm-field-submit-field-btn-container");
            $btn_container.removeClass("rm-field-btn-align-left rm-field-btn-align-center rm-field-btn-align-right");
            $btn_container.addClass("rm-field-btn-align-" + jQuery(this).val());
            rm_update_submit_field();
        })

        jQuery("input[name='rm_field_hide_prev_button']").change(function (e) {
            if (jQuery(this).prop("checked")) {
                jQuery(".rm-field-prev-btn").hide();
                jQuery("input[name='rm_field_hide_prev_button']").prop('checked', true);
            } else {
                jQuery(".rm-field-prev-btn").show();
                jQuery("input[name='rm_field_hide_prev_button']").prop('checked', false);
            }
            rm_update_submit_field();
        })
    }

    function rm_update_submit_field() {
        var data = {
            'submit_btn_label': jQuery("#rm_field_sub_button").text().trim(),
            'next_btn_label': jQuery(".rm-field-next-btn").first().text().trim(),
            'prev_btn_label': jQuery(".rm-field-prev-btn").first().text().trim(),
            'btn_align': jQuery("[name='rm_field_submit_field_align']:checked").val(),
            'hide_prev_btn': jQuery("#rm_field_hide_prev_button").prop('checked'),
        };

        var data = {
            'action': 'rm_update_submit_field',
            'rm_sec_nonce': '<?php echo wp_create_nonce('rm_ajax_secure'); ?>',
            'data': data,
            'form_id': <?php echo $data->form_id; ?>
        };
        jQuery(".rm-field-submit-ajax-loader").css("visibility", "visible");
        jQuery.post(ajaxurl, data, function (response) {
            jQuery(".rm-field-submit-ajax-loader").css("visibility", "hidden");
        });
    }
    
    function add_new_page_to_form() {
        var loc = "?page=rm_field_manage&rm_form_id=<?php echo $data->form_id; ?>&rm_action=add_page";
        window.location = loc;
    }
    
    function rename_form_page(page_no, page_name) {
        var new_name = prompt("Please enter new name", page_name);
 
        if (new_name !== null) {
            var curr_form_page = page_no;
            var encoded_new_name = encodeURIComponent(new_name);
           
            var params = new URLSearchParams();
            params.set('page', 'rm_field_manage');
            params.set('rm_form_id', '<?php echo $data->form_id; ?>');
            params.set('rm_form_page_no', curr_form_page);
            params.set('rm_form_page_name', encoded_new_name);
            params.set('rm_action', 'rename_page');
 
            var loc = '?' + params.toString();
           
            window.location.href = decodeURIComponent(loc);
        }
    } 
    
    function delete_page_from_page(page_no) {
        if (confirm('This will remove the page along with all the contained rows and fields! Proceed?')) {
            var curr_form_page = page_no;
            var loc = "?page=rm_field_manage&rm_form_id=<?php echo $data->form_id; ?>&rm_form_page_no=" + curr_form_page + "&rm_action=delete_page";
            window.location = loc;
        }
    }
    
    function get_current_form_page() {
        return curr_form_page_for_field;
    }

</script>

<script>
    jQuery(function($) {
        $( "#rm-field-sortable" ).sortable();
        $( "#rm-field-sortable" ).disableSelection();
        //$( "#rm-field-sortable" ).sortable({ axis: 'y' });
        $( "#rm-field-sortable" ).sortable("option", "placeholder", "rm-form-row-placeholder");
        $( "#rm-field-sortable" ).sortable("option", "opacity", 1);
        $( "#rm-field-sortable" ).on( "sortstart", function(event, ui) {
            $(ui.helper).css("box-shadow", "0 0 10px rgba(0,0,0,0.1)");
        });        
        $( "#rm-field-sortable" ).on( "sortstop", function(event, ui) {
            $(ui.item).css("box-shadow", "none");
        });
    });
    
    /*-- jQuery( function() {
    
    } ); -- */
    var old_page_ids = [];
    jQuery("#rm-form-page-sortable").find(".rm-form-page-sortable-box").each(function(){
        old_page_ids.push(this.id);
    });
    
    function reset_page_order() {
        var data = {
            action: 'rm_sort_form_pages',
            rm_sec_nonce: '<?php echo wp_create_nonce('rm_ajax_secure'); ?>',
            rm_slug: 'rm_field_set_page_order',
            data: old_page_ids,
            form_id: <?php echo $data->form_id; ?>
        };

        jQuery.post(ajaxurl, data, function (response) {
            location.reload();
        });
    }
    
    jQuery('#rm-row-add-edit-form').submit(function(e) {
        jQuery(this).find('input[type=submit]').prop('disabled', true);
    });
 jQuery(document).ready(function () {
    src = null;
    var pre_row_id;
    options = {
        revert:true,
        opacity: 1,
        scroll: false,
        iframeFix: false,
        start: function(event, ui) {
            
            pre_row_id = jQuery(this).closest('li').attr("id");
            pre_page_id = jQuery(this).closest('.rm-form-builder-box').attr('id');
                src = jQuery(this).parent();
                jQuery(this).addClass('rm-form-field-drag');
        },
        stop: function(event,ui){
            pre_row_id = null;
            pre_page_id = null;
            src= null;
            jQuery(this).removeClass('rm-form-field-drag');
        }
    };

    jQuery("#rm-form-page-sortable").sortable({
        axis: 'x,y',
        opacity: 0.7,
        update: function (event, ui) {
            var list_sortable = jQuery(this).sortable('toArray');
            //jQuery(".rm-page-tabs-sidebar").sortable('disable');
            var data = {
                action: 'rm_sort_form_pages',
                rm_sec_nonce: '<?php echo wp_create_nonce('rm_ajax_secure'); ?>',
                rm_slug: 'rm_field_set_page_order',
                data: list_sortable,
                form_id: <?php echo $data->form_id; ?>
            };

            jQuery.post(ajaxurl, data, function (response) {});
        }
    });

    jQuery(".rm-col-area").draggable(options);
    jQuery(".rm-col-area").draggable("option", "handle", ".rm-field-draggable-handle");
    jQuery(".rm-col-area").draggable("option", "cursor", "move");
    //jQuery(".rm-col-area").draggable().disableSelection();
    //jQuery(".rm-form-field").droppable().disableSelection();
    jQuery(".rm-form-field").droppable({
        iframeFix: true,
        deactivate: function( event, ui ) {
                
        },
        drop: function(event, ui) {
                var rm_row_id = jQuery(this).closest('li').attr("id");
                var row_page_id = jQuery(this).closest('.rm-form-builder-box').attr('id');
                var currentId = jQuery(this).closest('.rm-form-field').attr('id');
                if(!src) return;
                var previousId = src.attr('id');
                if(currentId !== previousId){
                    src.append(
                            jQuery('.rm-col-area', this).remove().clone().removeClass().addClass("rm-col-area").css({"left": '', "opacity": '99999',"top":''}).draggable(options)
                    );
    
                   jQuery(this).append(
                            ui.draggable.remove().clone().removeClass().addClass("rm-col-area").css({"left": '', "opacity": '999999',"top":''}).draggable(options)
                    );
                }
                //Previous Column Row 
                var colSequence = 0;
                var fieldId = [];
                pre_row_id = src.closest('li').attr("id");
                    jQuery("li#"+pre_row_id+".rm-fields-box-wrap .rm-form-field").each(function() {
                        if(jQuery(this).find('.rm-insert-field-button a').length){
                            jQuery(this).find('.rm-insert-field-button a').attr('data-order', colSequence);
                            jQuery(this).find('.rm-insert-field-button a').attr('data-row-id', pre_row_id);
                            jQuery(this).find('.rm-insert-field-button a').attr('data-page-no', pre_page_id.replace(/[^0-9]/g, ''));
                            jQuery(this).addClass('rm-col-draggable');
                            fieldId.push('');
                        }else{
                            fieldId.push(jQuery(this).find('.rm-col-area').attr('id'));
                            jQuery(this).removeClass('rm-col-draggable');
                        }
                        if(jQuery(this).find('.rm-field-actions').length){
                            var funname = 'edit_field_in_page';  
                            pre_page_id = pre_page_id.replace(/[^0-9]/g, '');
                            pre_fieldType = jQuery(this).find(".rm-field-actions").attr('data-title');
                            pre_field_id = jQuery(this).find('.rm-col-area').attr('id');
                            jQuery(this).find(".rm-field-setting a").attr("onclick",funname+"('"+pre_fieldType+"',"+pre_field_id+","+pre_page_id+")");
                            
                            jQuery(this).find('.rm-field-delete a').attr('data-order', colSequence);
                            jQuery(this).find('.rm-field-delete a').attr('data-row-id', pre_row_id);
                            jQuery(this).find('.rm-field-delete a').attr('data-field-id', pre_field_id);
                        }
                        colSequence++;
                    });
                var dataPreField = {
                    action: 'rm_sort_form_row_column',
                    'rm_slug': 'rm_field_set_row_col_order',
                    'rm_sec_nonce': rm_admin_vars.nonce,
                    data: fieldId,
                    rm_row : pre_row_id,
                    rm_page: pre_page_id.replace(/[^0-9]/g, '')
                };

                jQuery.post(ajaxurl, dataPreField, function (response) {
                });
                
                //Last Column Row  rm_row_id
                
                var colSequence = 0;
                var rowFieldId = [];
                    jQuery("li#"+rm_row_id+".rm-fields-box-wrap .rm-form-field").each(function() {
                        if(jQuery(this).find('.rm-insert-field-button a').length){
                            jQuery(this).find('.rm-insert-field-button a').attr('data-order', colSequence);
                            jQuery(this).find('.rm-insert-field-button a').attr('data-row-id', pre_row_id);
                            jQuery(this).find('.rm-insert-field-button a').attr('data-page-no', row_page_id.replace(/[^0-9]/g, ''));
                            jQuery(this).addClass('rm-col-draggable');
                            rowFieldId.push('');
                        }else{
                            rowFieldId.push(jQuery(this).find('.rm-col-area').attr('id'));
                            jQuery(this).removeClass('rm-col-draggable');
                        }
                        if(jQuery(this).find('.rm-field-actions').length){
                            var funname = 'edit_field_in_page';  
                            row_page_id = row_page_id.replace(/[^0-9]/g, '');
                            row_fieldType = jQuery(this).find(".rm-field-actions").attr('data-title');
                            row_field_id = jQuery(this).find('.rm-col-area').attr('id');
                            jQuery(this).find(".rm-field-setting a").attr("onclick",funname+"('"+row_fieldType+"',"+row_field_id+","+row_page_id+")");
                            
                            jQuery(this).find('.rm-field-delete a').attr('data-order', colSequence);
                            jQuery(this).find('.rm-field-delete a').attr('data-row-id', rm_row_id);
                            jQuery(this).find('.rm-field-delete a').attr('data-field-id', row_field_id);
                        }
                        colSequence++;
                    });
                var dataRowField = {
                    action: 'rm_sort_form_row_column',
                    'rm_slug': 'rm_field_set_row_col_order',
                    'rm_sec_nonce': rm_admin_vars.nonce,
                    data: rowFieldId,
                    rm_row : rm_row_id,
                    rm_page: row_page_id.replace(/[^0-9]/g, '')
                };

                jQuery.post(ajaxurl, dataRowField, function (response) {
                });
                src = null;
                previousId = null;
                currentId = null;
        }
    });
     
     jQuery('input#rm-fs-search-forms').on('input', function() {
        var searchTerm = jQuery(this).val().toLowerCase();
        if(searchTerm == '') {
            jQuery('div.rm-fs-recent-forms').show();
            jQuery('div.rm-fs-popular-forms').show();
            jQuery('div.rm-fs-search-result').hide();
            jQuery('ul#rm-fs-search-forms > li').each(function(index, value) {
                jQuery(this).show();
            });
        } else {
            var counter = 0;
            jQuery('div.rm-fs-recent-forms').hide();
            jQuery('div.rm-fs-popular-forms').hide();
            jQuery('div.rm-fs-search-result').show();
            jQuery('ul#rm-fs-search-forms > li').each(function(index, value) {
                var linkText = jQuery(this).children('a').text().toLowerCase();
                if(linkText.includes(searchTerm)) {
                    jQuery(this).show();
                    counter++;
                } else {
                    jQuery(this).hide();
                }
            });
            jQuery('div.rm-fs-search-result-head > span').text(counter);
        }
    });
        
    jQuery('div.rm-fs-search-result-head > a').on('click', function() {
        var input = jQuery('input#rm-fs-search-forms');
        input.val('');
        input.trigger('input');
    });

});

</script>

<style>
   .admin_page_rm_field_manage .rm-formflow-top-bar {
    margin: 100px 0px 10px 5%;
    max-width: 75%;
    margin-left: 12.5%;
   }
   
   .wp-core-ui.admin_page_rm_field_manage .notice {
       display:none
   }
   
    .wp-core-ui .rmagic::before {
       display: none;
   }
   .rm-col-area {
        cursor: pointer;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
        border-radius: 3px;
        min-height: 30px;
        border-color: transparent;
        /* box-shadow: 0 1px 0 #e6e6e6; */
        flex: 1;
    }
   .rm-form-field-drag {
        background: #fff !important;
        border: 1px solid #DAE1E7;
        z-index: 999999999999999999;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
        max-width: 500px;
    }
   
</style>

    <script>       
        jQuery(document).ready(function(){
        jQuery(".rm-fs-panel-trigger, .rm-fs-panel-overlay").click(function(){
          jQuery(".js-cd-panel-main").toggleClass("rm-fs-panel-is-visible");
           jQuery(".rm-fs-panel-trigger").toggleClass("rm-fs-panel-trigger-close");
           });
        });
        
    </script>