<?php

class RM_i18n_Addon
{

    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            'registrationmagic-addon', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

}