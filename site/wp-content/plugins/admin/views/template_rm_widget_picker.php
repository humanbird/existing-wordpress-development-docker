<?php
if (!defined('WPINC')) {
    die('Closed');
}
?>
<ul class="rm-widget-selector-view">
    <li title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_HTMLH"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('HTMLH')">

        <div class="rm-difl rm-widget-icon rm-widget-heading"><i class="fa fa-header" aria-hidden="true"></i></div>
        <div class="rm-difl rm-widget-head"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("WIDGET_TYPE_HEADING"); ?></a>
            
        </div>
    </li>
    <li title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_HTMLP"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('HTMLP')">
        <div class="rm-difl rm-widget-icon rm-widget-paragraph"><i class="fa fa-paragraph" aria-hidden="true"></i></div> 
        <div class="rm-difl rm-widget-head"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("WIDGET_TYPE_PARAGRAPH"); ?></a>
        </div>
    </li>
    <li title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Divider"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('Divider')">
        <div class="rm-difl rm-widget-icon rm-widget-divider"><i class="fa fa-arrows-h" aria-hidden="true"></i></div> 
        <div class="rm-difl rm-widget-head"> <a href="javascript:void(0)"><?php echo RM_UI_Strings::get("WIDGET_TYPE_DIVIDER"); ?></a>
   
        </div>
    </li>
            
    <li title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Spacing"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('Spacing')">
        <div class="rm-difl rm-widget-icon rm-widget-spacing"><i class="material-icons">&#xE256;</i></div> 
        <div class="rm-difl rm-widget-head"> <a href="javascript:void(0)"><?php echo RM_UI_Strings::get("WIDGET_TYPE_SPACING"); ?></a>
 
        </div>
    </li>  
    <li title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_RICHTEXT"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('RichText')">
        <div class="rm-difl rm-widget-icon rm-widget-richtext"><i class="material-icons">&#xE165;</i></div> 
        <div class="rm-difl rm-widget-head"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("WIDGET_TYPE_RICHTEXT"); ?></a>
  

        </div>
    </li>  
    
    <li title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_TIMER"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('Timer')">
        <div class="rm-difl rm-widget-icon rm-widget-richtext"><i class="material-icons">&#xE425;</i></div> 
        <div class="rm-difl rm-widget-head"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("WIDGET_TYPE_TIMER"); ?></a>
        </div>
    </li> 
    
    <li class="rm_button_like_links" onclick="add_new_widget_to_page('Link')" title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_LINK"); ?>">

        <div class="rm-difl rm-widget-icon rm-widget-add-link">
            <i class="material-icons">&#xE157;</i>
        </div>
        <div class="rm-difl rm-widget-head"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("WIDGET_TYPE_LINK"); ?></a>
            
        </div>
    </li>
    
    <li class="rm_button_like_links" onclick="add_new_widget_to_page('YouTubeV')" title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_YOUTUBE"); ?>">
        <div class="rm-difl rm-widget-icon rm-widget-youtube"><i class="fa fa-youtube-play" aria-hidden="true"></i></div> 
        <div class="rm-difl rm-widget-head"><a href="javascript:void(0)"><?php _e('YouTube Video', 'registrationmagic-addon'); ?></a>
        </div>
    </li> 
    
    <li class="rm_button_like_links" onclick="add_new_widget_to_page('Iframe')" title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_IFRAME"); ?>">
        <div class="rm-difl rm-widget-icon rm-widget-iframe-embed"><i class="material-icons">&#xE86F;</i></div> 
        <div class="rm-difl rm-widget-head"><a href="javascript:void(0)"><?php _e('iFrame Embed', 'registrationmagic-addon'); ?></a>
  

        </div>
    </li> 
    
    <li class="rm_button_like_links" onclick="add_new_widget_to_page('ImageV')" title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_IMAGEV"); ?>">
        <div class="rm-difl rm-widget-icon rm-widget-add-image"><i class="material-icons">&#xE439;</i></div> 
        <div class="rm-difl rm-widget-head"><a href="javascript:void(0)"><?php _e('Image', 'registrationmagic-addon'); ?></a>
        </div>
    </li>
    
    <li class="rm_button_like_links" onclick="add_new_widget_to_page('PriceV')" title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_PRICEV"); ?>">
        <div class="rm-difl rm-widget-icon rm-widget-add-image"><i class="material-icons">&#xE263;</i></div> 
        <div class="rm-difl rm-widget-head"><a href="javascript:void(0)"><?php _e('Price', 'registrationmagic-addon'); ?></a>
        </div>
    </li>
    
    <li class="rm_button_like_links rm-widget-lg" onclick="add_new_widget_to_page('SubCountV')" title="<?php _e('If you have set form limits, you can display the limit status using this widget', 'registrationmagic-addon'); ?>">
        <div class="rm-difl rm-widget-icon rm-widget-add-image"><i class="material-icons">&#xE439;</i></div> 
        <div class="rm-difl rm-widget-head"><a href="javascript:void(0)"> <?php _e('Submission Countdown', 'registrationmagic-addon'); ?></a>
        </div>
    </li>
    
    <li class="rm_button_like_links" onclick="add_new_widget_to_page('MapV')" title="<?php _e('Display a location with marker using GoogleMaps', 'registrationmagic-addon'); ?>">
        <div class="rm-difl rm-widget-icon rm-widget-map"><i class="material-icons">&#xE0C8;</i></div> 
        <div class="rm-difl rm-widget-head"><a href="javascript:void(0)"><?php _e('Map', 'registrationmagic-addon'); ?></a>
        </div>
    </li>  
    
    <li class="rm_button_like_links" onclick="add_new_widget_to_page('Form_Chart')" title="<?php _e('Display stats about the form using graphs and charts', 'registrationmagic-addon'); ?>">
        <div class="rm-difl rm-widget-icon rm-widget-form-date-chart"><i class="material-icons">&#xE6C4;</i></div> 
        <div class="rm-difl rm-widget-head"> <a href="javascript:void(0)"><?php _e('Form Data Chart', 'registrationmagic-addon'); ?></a>
 
        </div>
    </li> 
    
     <li class="rm_button_like_links" onclick="add_new_widget_to_page('FormData')" title="<?php _e('Display various properties of the form', 'registrationmagic-addon'); ?>">
        <div class="rm-difl rm-widget-icon rm-widget-form-data"><i class="material-icons">&#xE85D;</i></div> 
        <div class="rm-difl rm-widget-head"> <a href="javascript:void(0)"><?php _e('Form Meta-Data', 'registrationmagic-addon'); ?></a>
   
        </div>
     </li>
     
     <li class="rm_button_like_links" onclick="add_new_widget_to_page('Feed')" title="<?php _e('Display latest registrations/ submissions data in your form', 'registrationmagic-addon') ?>">
        <div class="rm-difl rm-widget-icon rm-widget-registration-feed"><i class="material-icons">&#xE0E5;</i></div> 
        <div class="rm-difl rm-widget-head"><a href="javascript:void(0)"><?php echo $data->form->form_type==0 ? __('Submission Feed', 'registrationmagic-addon') : __('Registration Feed', 'registrationmagic-addon'); ?></a>
        </div>
    </li>
  
</ul>