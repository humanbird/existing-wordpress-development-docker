<?php

class RM_Floating_Widget_Addon {

        public function show_widget($parent_widget) {
            $options=new RM_Options;
            wp_enqueue_script('rm_front');
            $parent_widget->include_scripts();
            global $rm_form_diary;
            if(current_user_can('manage_options') && $options->get_value_of('hide_magic_panel_styler')!='yes'){
            ?>
            <!--noptimize-->
            <!----Color Switcher---->
            <div class="rm-white-box difl rm-color-switcher rm-rounded-corners rm-shadow-10" id="rm-color-switcher">
                <div id="rm-theme-box-toggle" style="cursor: pointer; text-align: right; color: grey" onclick="close_theme_box()">x</div>
                <div class="rm-color-switcher-note rm-grey-box dbfl rm-pad-10"><?php _e("Welcome! This sticky panel is only visible to you as site admin. You can style RegistrationMagic's front-end panel on the right side, using the options below.", 'registrationmagic-addon') ?></div>
                <div class="rm-color-switch-title dbfl rm-accent-bg rm-pad-10"><?php _e('Magic Panel Styler!', 'registrationmagic-addon') ?></div>
                <input type="text" class="dbfl rm-grey-box jscolor" placeholder="<?php _e('Panel Accent Color', 'registrationmagic-addon') ?>" id="rm-panel-accent">
                <select class="dbfl" id="rm-panel-theme">
                    <option value="Light"><?php echo RM_UI_Strings::get('LABEL_LIGHT'); ?></option>
                    <option value="Dark"><?php echo RM_UI_Strings::get('LABEL_DARK'); ?></option>
                </select>
                <button class="difl" id="rm-color-switch"><?php echo RM_UI_Strings::get('LABEL_SWITCH'); ?></button>
            </div>
            <?php
            }
            ?>

            <div class="rm-magic-popup">
                <div class="rm-popup-menu rm-border rm-white-box  dbfl" id="rm-menu" style="display:none">
                    <?php
                    if($parent_widget->user_level === 0x4){
                    ?>
                    <div class="rm-popup-item rm-popup-welcome-box  dbfl rm-border" id="rm-account-open">
                        
                        <?php 
                            $av = get_avatar_data($parent_widget->user->ID); 
                            $profile_image_url = apply_filters('rm_profile_image',$av['url'],$parent_widget->user->ID);
                        ?>
                        <img class="rm-menu-userimage difl" src="<?php echo $profile_image_url; ?>">
                        <div class="rm-menu-user-details difl">
                          
                                 <?php if(!empty($parent_widget->user->first_name) &&  !empty($parent_widget->user->last_name)): ?>
                                    <div class="dbfl"><?php echo $parent_widget->user->first_name.' '.$parent_widget->user->last_name; ?></div>
                                <?php elseif(!empty($parent_widget->user->first_name) &&  empty($parent_widget->user->last_name)): ?>
                                    <div class="dbfl"><?php echo $parent_widget->user->first_name; ?></div>
                                <?php else: ?>
                                    <div class="dbfl"> </span> <?php echo $parent_widget->user->display_name; ?></div>
                                 <?php endif; ?>
                        </div>
                    </div>
                     <?php
                    }
                        if ($parent_widget->user_level === 0x1) {
                            ?>
                    <div class="rm-popup-item dbfl" id="rm-login-open"><?php echo RM_UI_Strings::get('LABEL_LOGIN'); ?></div>
                    
                    <?php 
                    //if(!isset($rm_form_diary[$parent_widget->param->default_form])){
                    if(!empty($parent_widget->param->default_form)){
                    ?>
                    <div class="rm-popup-item dbfl" id="rm-register-open-big"><?php echo RM_UI_Strings::get('LABEL_REGISTER'); ?></div>
                    <?php }
                    //else {
                    ?>
                    <!-- <a href="#form_<?php echo $parent_widget->param->default_form;?>_1" id="rm_fab_register_redirect_link"><div class="rm-popup-item dbfl" id="rm-register-open-big"><?php echo RM_UI_Strings::get('LABEL_REGISTER'); ?></div></a> -->
                     <?php    
                    //}
                   
                        }
                        ?>
                    <?php 
                    if($parent_widget->user_level === 0x1 || $parent_widget->user_level === 0x4){
                          $show_tabs=$options->get_value_of('show_tabs');
                        $show_submission_tab=$show_tabs['submissions'];
                        $show_payment_tab=$show_tabs['payment'];
                        $show_details_tab=$show_tabs['details'];
                        if($show_submission_tab== 1)
                        {
                        ?>
                    <div class="rm-popup-item dbfl" id="rm-submissions-open"><?php echo RM_UI_Strings::get('LABEL_MY_SUBS'); ?></div>
                        <?php } 
                          if($show_payment_tab== 1)
                        {
                        ?>
                    <div class="rm-popup-item dbfl" id="rm-transactions-open"><?php echo RM_UI_Strings::get('LABEL_PAYMENTS'); ?></div>
                     <?php } 
                          if($show_details_tab== 1)
                        {
                        ?>
                    <div class="rm-popup-item dbfl" id="rm-account-open"><?php echo RM_UI_Strings::get('LABEL_MY_DETAILS'); ?></div>
                   <?php 
                        }
                        
                    //Options extended by extensions
                    echo apply_filters('rm_popup_button_menu', '');
                  
                   $fab_links=$options->get_value_of('fab_links');
                 
                   foreach($fab_links as $links)
                   {
                       if($parent_widget->widget_helper->check_link_show($links['visibility']) &&  $links['flag']=='yes')
                       {
                            if($links['type']=='page')
                            {
                                $name=get_the_title($links['link']);
                                $url=get_permalink($links['link']); ?>
                             <div class="rm-popup-item dbfl" id="rm_fab_register_external">   <a href="<?php echo $url;?>" id="rm_fab_external_redirect_link"><?php echo $name; ?></a></div>
                   <?php
                            }
                            else{  ?>
                             <div class="rm-popup-item dbfl" id="rm_fab_register_external" ><a   href="<?php echo $links['link'];?>" id="rm_fab_external_redirect_link"><?php echo $links['label']; ?></a></div>
                   
                    <?php
                            }      
                            
                       }
                       else
                           continue;
                   }
                   
                    
                    
                    ?>

 <?php }if($parent_widget->user_level !== 0x1 && !is_user_logged_in()){ ?>
                    <div class="rm-popup-item dbfl rm-popup-item-log-off" id="rm_log_off" onclick="document.getElementById('rm_floating_btn_nav_form').submit()"><?php echo RM_UI_Strings::get('LABEL_LOG_OFF'); ?></div>
                   
                    <form method="post" id="rm_floating_btn_nav_form">
                       <input type="hidden" name="rm_slug" value="rm_front_log_off">
                       <input type="hidden" name="rm_do_not_redirect" value="true">
                   </form>
                    <?php } 
                    elseif(is_user_logged_in()){
                        ?>
                    <div class="rm-popup-item dbfl rm-popup-item-log-off" id="rm_log_off"><a href="<?php echo wp_logout_url(); ?>"><?php echo RM_UI_Strings::get('LABEL_LOG_OFF'); ?></a></div>
                    <?php
                    }
?>
                    <span class="rm-magic-popup-nub"></span>
                </div>
                <div class="rm-magic-popup-button rm-accent-bg rm-shadow-10 dbfl" id="rm-popup-button">
                    <img src="<?php echo $parent_widget->widget_helper->get_fab_icon(); ?>">
                    <span class="rm-magic-popup-close-button" style="display: none;"><i class="material-icons">&#xE5CD;</i></span>
                </div>
            </div>

            <div class="rm-floating-page rm-shadow-10 dbfl" id="rm-panel-page"  style="transform: translateX(150%);">
                <div class="rm-floating-page-top rm-border rm-white-box dbfl">
                <i class="material-icons">assignment_turned_in</i>
                <?php echo RM_UI_Strings::get('LABEL_MY_SUBS'); ?></div>
                <div class="rm-floating-page-content dbfl">

                    <!----Login Panel---->
                    <div class="rm-floating-page-login dbfl" id="rm-login-panel">
                        <?php $parent_widget->widget_helper->getLoginForm(); ?>
                    </div>
                    <!--Registration Form-->
                    <div class="dbfl" id="rm-register-panel-big">
                        <?php if ($parent_widget->param->default_form > 0 && !is_user_logged_in()) {                           
                                
                            echo do_shortcode("[RM_Form force_enable_multiform='true' id='".$parent_widget->param->default_form."']");       
                        } else {
                            echo "<div class='rm-no-default-from-notification'>". RM_UI_Strings::get('NO_DEFAULT_FORM')."</div>";
                        }
                        ?>
                    </div>

                    <!----Panel page submissions area---->
                    <div class="dbfl" id="rm-submissions-panel">
                        <?php
                        if ($parent_widget->user_level !== 0x1)
                            $parent_widget->widget_helper->getSubmissions();
                        else
                            echo "<div class='rm-no-default-from-notification'>".RM_UI_Strings::get('MSG_PLEASE_LOGIN_FIRST')."</div>";
                        ?>
                    </div>

                    <!--------User Transaction Panel------->
                    <div class="dbfl" id="rm-transactions-panel">
                        <?php
                        if ($parent_widget->user_level !== 0x1)
                            $parent_widget->widget_helper->getPayments();
                        else
                            echo "<div class='rm-no-default-from-notification'>".RM_UI_Strings::get('MSG_PLEASE_LOGIN_FIRST')."</div>";
                        ?>

                    </div>

                    <!----User Account Page---->
                    <div class="dbfl" id="rm-account-panel">
                        <?php
                        if ($parent_widget->user_level !== 0x1)
                            $parent_widget->widget_helper->get_account();
                        else
                            echo "<div class='rm-no-default-from-notification'>".RM_UI_Strings::get('MSG_PLEASE_LOGIN_FIRST')."</div>";
                        ?>
                        
                    </div>
                    
                    <!----Extended panels from extensions---->
                    <?php echo apply_filters('rm_popup_button_menu_content', '')?>
                    
                </div>

                <div class="rm-floating-page-bottom rm-border rm-white-box dbfl">
                    <button class="rm-rounded-corners rm-button" id="rm-panel-close"><?php echo RM_UI_Strings::get('LABEL_FIELD_ICON_CLOSE'); ?></button>
                </div>

            </div>
            <!--/noptimize-->
            <?php
        }

}