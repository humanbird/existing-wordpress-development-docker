<?php

class RM_Table_Tech_Addon
{

    private static $instance;
    private static $table_name_for;

    private function __construct()
    {
        //global $wpdb;

        $prefix = 'rm_';
        self::$table_name_for = array();
        self::$table_name_for['RES'] = $prefix.'resources';
        //self::$table_name_for['CUSTOM_STATUS'] = $prefix . 'custom_status';
    }

    public function __wakeup()
    {
        
    }

    private function __clone()
    {
        
    }

    public static function get_instance()
    {
        if (null === static::$instance)
        {
            static::$instance = new static();
        }

        return static::$instance;
    }

    //Multi-site specific hook functions
    //hook: wpmu_new_blog
    public static function on_create_blog($blog_id, $user_id, $domain, $path, $site_id, $meta)
    {
        // Makes sure the plugin is defined before trying to use it
        if (!function_exists('is_plugin_active_for_network'))
        {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }

        if (is_plugin_active_for_network(RM_ADDON_PLUGIN_BASENAME))
        {
            switch_to_blog($blog_id);
            self::create_tables_per_site();
            RM_Utilities::create_submission_page();
            restore_current_blog();
        }
    }

    //hook: wpmu_drop_tables
    public static function on_delete_blog($tables)
    {
        global $wpdb;

        foreach (self::$table_name_for as $table_name)
            $tables[] = $table_name;

        return $tables;
    }

    public static function create_tables($network_wide)
    {  
        global $wpdb;

        if (is_multisite() && $network_wide)
        {
            // store the current blog id
            $current_blog = $wpdb->blogid;
            // Get all blogs in the network and activate plugin on each one
            $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blog_ids as $blog_id)
            {
                switch_to_blog($blog_id);
                self::create_tables_per_site();
                restore_current_blog();
            }
        } else
        {
            self::create_tables_per_site();
        }
    }

    public static function create_tables_per_site()
    {
        if(version_compare(get_bloginfo('version'),'6.1') < 0)
            require_once( ABSPATH . 'wp-includes/wp-db.php');
        else
            require_once( ABSPATH . 'wp-includes/class-wpdb.php');
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        global $wpdb;
        
        $table_name = self::get_table_name_for('RES');

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                      `res_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                      `type` varchar(100) NOT NULL,
                      `data` longtext DEFAULT NULL,
                      `meta` longtext DEFAULT NULL)$charset_collate;";
            dbDelta($sql);
        }
        
        /*
        $table_name = self::get_table_name_for('CUSTOM_STATUS');
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (
                    `cus_status_id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `status_index` INT(6),
                    `submission_id` INT(6),
                    `form_id` INT(6)
                    )$charset_collate;";
            dbDelta($sql);
        }
        */
        
        do_action("rm_per_site_tables_created_addon");
    }

    /**
     * returns the table name according to its identifier
     *
     * @param string $model_identifier
     * @return string
     */
    public static function get_table_name_for($model_identifier)
    {
        global $wpdb;
        
        //These are global tables, hence wpdb->prefix should not be used.
        //wpdb->prefix is site specifc in mulitsite environment.
        if($model_identifier == 'WP_USERS') return $wpdb->users;
        if($model_identifier == 'WP_USERS_META') return $wpdb->usermeta;
        
        if (isset(self::$table_name_for[$model_identifier]))
            return $wpdb->prefix . self::$table_name_for[$model_identifier];
    }

    public static function delete_and_reset_table($identifier)
    {
        global $wpdb;

        $table_name = self::get_table_name_for($identifier);

        $qry = "TRUNCATE `$table_name`";
        $wpdb->query($qry);
    }
    
}

$RM_Table_Tech_Addon = RM_Table_Tech_Addon::get_instance();