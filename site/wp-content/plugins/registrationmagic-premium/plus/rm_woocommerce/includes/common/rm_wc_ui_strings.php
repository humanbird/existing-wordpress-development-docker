<?php

/**
 * This class works as a repository of all the string resources used in product UI
 * for easy translation and management. 
 *
 * @author CMSHelplive
 */

class RM_WC_UI_Strings
{
    public static function get($identifier)
    {
        switch($identifier)
        {
            case 'LABEL_SHIPPING_ADDRESS':
                return __('Shipping Address','registrationmagic-addon');
                
            case 'LABEL_BILLING_ADDRESS':
                return __('Billing Address','registrationmagic-addon');
            
            case 'LABEL_ORDER':
                return __('Order','registrationmagic-addon');    
            
            case 'LABEL_ORDERS':
                return __('Orders','registrationmagic-addon');    
            
            case 'LABEL_ORDER_DETAILS':
                return __('Order details','registrationmagic-addon');      
            
            case 'LABEL_DOWNLOADS':
                return __('Downloads','registrationmagic-addon');
            
            case 'LABEL_DOWNLOAD':
                return __('Download','registrationmagic-addon');    
            
            case 'LABEL_ADDRESSES':
                return __('Addresses','registrationmagic-addon');
                
            case 'LABEL_VIEW':
                return __('View','registrationmagic-addon');
            
            case 'LABEL_NAME':
                return __('Name','registrationmagic-addon');
                
            case 'LABEL_TOTAL':
                return __('Total','registrationmagic-addon');
                
            case 'LABEL_SUBTOTAL':
                return __('Subtotal','registrationmagic-addon');
                
            case 'LABEL_SHIPPING':
                return __('Shipping','registrationmagic-addon');
                
            case 'LABEL_DISCOUNT':
                return __('Discount','registrationmagic-addon');
                
            case 'LABEL_TOTAL_DISCOUNT':
                return __('Total Discount','registrationmagic-addon');
                
            case 'LABEL_ORDER_TOTAL':
                return __('Order Total','registrationmagic-addon');
                
            case 'LABEL_COUPONS_USED':
                return __('Coupon(s) Used','registrationmagic-addon');
                
            case 'LABEL_PRODUCT_NAME':
                return __('Product Name','registrationmagic-addon');
                
            case 'LABEL_QUANTITY':
                return __('Quantity','registrationmagic-addon');
                
            case 'LABEL_COST':
                return __('Cost','registrationmagic-addon');
                
            case 'NOTICE_NO_SHIPPING_ADDRESS_USER':
                return __('User has not set up shipping address yet.','registrationmagic-addon');
                
            case 'NOTICE_NO_BILLING_ADDRESS_USER':
                return __('User has not set up billing address yet.','registrationmagic-addon');
                
            case 'LABEL_REMAINING_DOWNLOADS':
                return __('Remaining Downloads','registrationmagic-addon');
                
            case 'LABEL_ACCESS_EXPIRES':
                return __('Access Expires','registrationmagic-addon');
                
            case 'LABEL_ORDER_STATUS':
                return __('Status','registrationmagic-addon');
                
            case 'LABEL_AMOUNT':
                return __('Amount','registrationmagic-addon');
                
            case 'LABEL_PLACED_ON':
                return __('Placed on','registrationmagic-addon');
                
            case 'LABEL_ITEMS':
                return __('Items','registrationmagic-addon');
                
            case 'LABEL_REMAINING_DLS_UNLIMITED':
                return __('Unlimited','registrationmagic-addon');
                
            case 'LABEL_ACCESS_EXPIRES_NEVER':
                return __('Never','registrationmagic-addon');
                
            case 'LABEL_WOO_REG_FORM' : 
                return __ ('Default Registration Form', 'registrationmagic-addon');
                
            case 'HELP_WOO_REG_FORM' : 
                return sprintf(__("Once selected, this form will appear on the default WooCommerce registration page. <a target='_blank' class='rm-more' href='%s'>More</a>", 'registrationmagic-addon'),'https://registrationmagic.com/knowledgebase/woocommerce-integration/#htdefregform');
                
            case 'LABEL_RM_GLOBAL_SETTING_MENU' : 
                return __ ('WooCommerce Integration', 'registrationmagic-addon');
                
            case 'SUBTITLE_RM_GLOBAL_SETTING_MENU' : 
                return __ ('Integrate forms inside WooCommerce', 'registrationmagic-addon');
                
            case 'LABEL_GO_SHOP' : 
                return __ ('Go shopping!', 'registrationmagic-addon');
                
            case 'LABEL_CART_EMPTY' : 
                return __ ('No item in the cart', 'registrationmagic-addon');
                
            case 'LABEL_TOTAL_REVENUE':
                return __('Total Revenue','registrationmagic-addon');
                
            case 'LABEL_ENABLE_CART_IN_FAB':
                return __('Show Cart with MagicPopup','registrationmagic-addon');
                
            case 'HELP_ENABLE_CART_IN_FAB':
                return sprintf(__("Enables quick access to the cart from MagicPopup Menu. <a target='_blank' class='rm-more' href='%s'>More</a>",'registrationmagic-addon'),'https://registrationmagic.com/knowledgebase/woocommerce-integration/#htcartonpop');
                
            case 'LABEL_ORDER_NOTES':
                return __('Order Notes','registrationmagic-addon');
                
            case 'LABEL_ORDER_NOTE_FOOTER':
                return __('Added by %s on %s','registrationmagic-addon');
                
            case 'ALERT_GUEST_CHECKOUT_ENABLED':
                return __('Guest Checkout is enabled in WooCommerce. Disable it to display RegistrationMagic form for registration during checkout.','registrationmagic-addon');
            
             case 'NAME_WC':
                return __('WooCommerce','registrationmagic-addon');
                
             case 'WC_ERROR':
                return sprintf(__("<div class='rmnotice'><ul class='rm-notice-info'><div class='rm-notice-head'>Oops!! Something went wrong.</div><li>Possible causes:-</li><li><a target='_blank' href='%s'>Woocommerce</a> is not installed/active.</li></ul></div>", 'registrationmagic-addon'),'https://wordpress.org/plugins/woocommerce/');
                 
            case 'WC_FORM_SETTING_TEXT':
             return sprintf(__("<div class='rmnotice'><ul class='rm-notice-info'><li><span class='rm-notice-head'>You can configure WooCommerce from</span> <a href='%s'> Global Settings->Woocommerce Integration</a></li></ul></div>",'registrationmagic-addon'),'?page=rm_wc_settings');
                
            case 'LABEL_ENABLE_RM_ROLE_OVERRIDE':
                return __('Enable Role Assignment','registrationmagic-addon');

            case 'LABEL_ENABLE_RM_ACCOUNT_LINK':
                return __('Enable RegistrationMagic Account Link','registrationmagic-addon');
                
            case 'HELP_ENABLE_RM_ROLE_OVERRIDE':
                return sprintf(__("Enable to assign custom role defined inside Form Dashboard --> Accounts to WooCommerce registrations. If turned off, default WooCommerce role will be assigned. <a target='_blank' class='rm-more' href='%s'>More</a>",'registrationmagic-addon'),'https://registrationmagic.com/knowledgebase/woocommerce-integration/#htenablerole');
            
            case 'HELP_ENABLE_RM_ACCOUNT_LINK':
                return __("Enable this option to display RegistrationMagic account link on the WooCommerce account page.",'registrationmagic-addon');
            
            default:
                return __("NO STRING FOUND (rmwc)", 'registrationmagic-addon');
        }
    }
}
