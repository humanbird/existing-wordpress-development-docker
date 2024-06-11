<?php

class RM_Activator_Addon {
    
    public function __construct() {
        add_action('rm_addon_create_login_tables',
                   array($this,'create_login_tables')
                  );
        create_tabs_table();
    }
    
    public function create_login_tables() {
        RM_Table_Tech::create_login_tables();
    }
    public function create_tabs_table(){
    	RM_Table_Tech::create_tabs_table();
    }
    
}

$RM_Activator_Addon = new RM_Activator_Addon();