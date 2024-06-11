<?php

/**
 * Repository of all the string resources
 * for easy translation and management. 
 *
 */

class RM_NLetter_UI_Strings
{
    public static function get($identifier)
    {
        switch($identifier)
        {
            
            case 'NAME_NLETTER':
                return __('Newsletter','registrationmagic-addon');
                
            case 'LABEL_NLETTER_INTEGRATION':
                return __('Newsletter Integration','registrationmagic-addon');
             
           case 'LABEL_NLETTER_LIST':
                return __('Send To Newsletter List', 'registrationmagic-addon');
   
            case 'HELP_ADD_FORM_MP_LIST':
                return sprintf(__("Required for connecting the form to a Newsletter list. <a target='_blank' class='rm-more' href='%s'>More</a>", 'registrationmagic-addon'),'https://registrationmagic.com/knowledgebase/newsletter-integration/#htnllist');

            case 'HELP_OPTIONS_THIRDPARTY_NL_ENABLE':
                return sprintf(__("Enable Newsletter Integration. <a target='_blank' class='rm-more' href='%s'>More</a>", 'registrationmagic-addon'),'https://registrationmagic.com/knowledgebase/newsletter-integration/#htnlintegration');
                
            case 'NL_ERROR':
                return sprintf(__("<div class='rmnotice'><ul class='rm-notice-info'><div class='rm-notice-head'>Oops!! Something went wrong.</div><li>Possible causes:-</li><li><a target='_blank' href='%s'>Newsletter</a> is not installed/active.</li></ul></div>", 'registrationmagic-addon'),'https://wordpress.org/plugins/newsletter/');
                
            case 'LABEL_FIRST_NAME':
                return __('First Name','registrationmagic-addon');
                
            case 'LABEL_LAST_NAME':
                return __('Last Name','registrationmagic-addon');
             
            case 'LABEL_GENDER':
                return __('Gender', 'registrationmagic-addon');
            
            case 'MSG_OPT_IN_DEFAULT_STATE':
                return sprintf(__("Default state of the opt in check box. <a target='_blank' class='rm-more' href='%s'>More</a>", 'registrationmagic-addon'),'https://registrationmagic.com/knowledgebase/newsletter-integration/#htnldefstate');    
             
            case 'HELP_OPT_IN_CB_TEXT':
                return sprintf(__("This text will appear with the opt-in checkbox. <a target='_blank' class='rm-more' href='%s'>More</a>", 'registrationmagic-addon'),'https://registrationmagic.com/knowledgebase/newsletter-integration/#htnloptintext');    
             
            default: 
                return __("NO STRING FOUND (rmdpx)", 'registrationmagic-addon');
        }
    }
}