<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://nabillemsieh.com
 * @since      0.1.0
 *
 * @package    Easy_Media_Replace
 * @subpackage Easy_Media_Replace/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Easy_Media_Replace
 * @subpackage Easy_Media_Replace/admin
 * @author     Nabil Lemsieh <contact@nabillemsieh.com>
 */

use Easy_Media_Replace_Helper as Helper;

class Easy_Media_Replace_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.1.0
     *
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    public function add_replace_link_to_media_row($actions, $post)
    {
        $replace['emr-replace']
            = '<a href="#" class="js-emr-open-dialog" data-attachment-id="'
            . $post->ID . '" data-attachment-mime="' . $post->post_mime_type
            . '" aria-label="View ' . Helper::trans(
                sprintf('Replace %s', $post->post_title)
            ) . '">' . Helper::trans('Replace') . '</a>';

        return $replace + $actions;
    }

    public function add_replace_link_to_attachment_misc_actions($post)
    {
        $file_type = Helper::file_type($post->post_mime_type);
        ?>
        <div class="misc-pub-section misc-pub-emr">
            <button type="button" class="button-secondary js-emr-open-dialog emr-dialog__open" data-attachment-id="<?php echo $post->ID ?>" data-attachment-mime="<?php echo $post->post_mime_type ?>">
                <?php echo Helper::trans(sprintf(
                            'Replace %s',
                            $file_type
                        )) ?></button>
        </div>'
<?php
    }

    public function add_replace_link_to_media_dialog($form_fields, $post)
    {
        $screen = get_current_screen();
        if ($screen && $screen->id === "attachment") {
            return $form_fields;
        }
        $form_fields['emr-replace'] = [
            'label' => '',
            'input' => 'html',
            'html'  => '<button type="button" class="button-secondary js-emr-open-dialog emr-dialog__open">Replace</button>',
        ];

        return $form_fields;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    0.1.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            'jquery-ui',
            EMR_DIR_URL . 'admin/css/jquery-ui.min.css',
            [],
            '1.12.1',
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name,
            EMR_DIR_URL . 'admin/css/easy-media-replace-admin.css',
            array('jquery-ui'),
            filemtime(EMR_DIR . 'admin/css/easy-media-replace-admin.css'),
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    0.1.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script(
            "dropzone",
            plugin_dir_url(__FILE__) . 'js/dropzone.js',
            array(),
            "5.2",
            true
        );
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/easy-media-replace-admin.js',
            array('jquery-ui-dialog', 'dropzone'),
            filemtime(
                plugin_dir_path(__FILE__) . 'js/easy-media-replace-admin.js'
            ),
            true
        );

        wp_localize_script(
            $this->plugin_name,
            'emr_ajax_object',
            [
                'ajax_url'   => admin_url('admin-ajax.php'),
                '_ajax_nonce' => wp_create_nonce('media-form'),
            ]
        );
    }

    /**
     * Send dialog html.
     *
     * @return void
     */
    public function dialog()
    {
        check_ajax_referer('media-form');

        $max_upload_size = round(wp_max_upload_size() / MB_IN_BYTES);

        $mime = filter_input(INPUT_GET, 'mime', FILTER_SANITIZE_STRING);

        $file_type = Helper::file_type($mime);

        ob_start();
        include(EMR_DIR . 'admin/partials/dialog.php');
        die(ob_get_clean());
    }

    /**
     * Process file upload.
     * Then send back file path.
     *
     * @return void
     */
    public function upload()
    {
        check_ajax_referer('media-form');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message'=> 'Sorry, you are not allowed to upload files.'], 403);
        }
        
        add_filter('big_image_size_threshold', '__return_false', 11111);
        add_filter('intermediate_image_sizes_advanced', '__return_empty_array', 11111);
        $id = media_handle_upload('async-upload', 0);
        remove_filter('big_image_size_threshold', '__return_false', 11111);
        remove_filter('intermediate_image_sizes_advanced', '__return_empty_array', 11111);

        if (is_wp_error($id) ) {
            wp_send_json_error(['message'=> $id->get_error_message()], 500);
        } else {
            wp_send_json_success([ 'new_id'=> $id ]);
        }
    }

    /**
     * Maybe remove file from the server.
     *
     * @return void
     */
    public function remove()
    {
        check_ajax_referer( 'media-form');

        if(empty($_POST['id'])){
            wp_die(-1);
        }

        $id = $_POST['id'];

        if(! current_user_can('delete_post', $id ) ){
            wp_send_json_error(['message'=> 'Sorry, you are not allowed to delete this file.'], 403);
        }

        if(wp_delete_attachment($id, true)){
            wp_die(1);
        } else {
            wp_die( 0 );
        }
        
    }

    /**
     * Process file replacing.
     *
     * @return void
     */
    public function replace()
    {
        check_ajax_referer('media-form');

        $old_id = !empty($_POST['old_id']) ? $_POST['old_id'] : null;
        $new_id = !empty($_POST['new_id']) ? $_POST['new_id'] : null;
     
       if( !$old_id || !$new_id ){
            wp_die(-1); 
       }
       
       $file_type = wp_attachment_is_image($old_id) ? 'image' : 'file';
       $new_file = get_attached_file($new_id);

       if($file_type === 'image'){
            $old_file = function_exists('wp_get_original_image_path') ? wp_get_original_image_path($old_id) : get_attached_file($old_id);
        }else{
            $old_file = get_attached_file($old_id);
        }

        if(!current_user_can('delete_post', $old_id) || !current_user_can('delete_post', $new_id)){
           wp_die(-1); 
        }

        $old_file_mime = get_post_mime_type($old_id);
        $new_file_mime = get_post_mime_type($new_id);
        
        if ($old_file_mime !== $new_file_mime) {
            wp_send_json_error(['message' => sprintf('The new %s must have the same type as the old one: %s.',$file_type,basename($old_file))], 400);
            
        }

        if (rename($new_file, $old_file)) {
            wp_delete_attachment( $new_id );

                if (!empty($_POST['regen_thumbs'])) {
                    $attachment_meta = wp_get_attachment_metadata( $old_id );

                    foreach ($attachment_meta['sizes'] as $size) {
                        if($size['file'] !== $attachment_meta['file']){
                            unlink(dirname($old_file) . '/' . $size['file']);
                        }
                    }

                    $attachment_meta = wp_generate_attachment_metadata( $old_id, $old_file );
                    wp_update_attachment_metadata( $old_id, $attachment_meta );
                }

            if (!empty($_POST['modified_time'])) {
                $post_update = [
                    'ID'                => $old_id,
                    'post_modified'     => current_time('mysql'),
                    'post_modified_gmt' => current_time('mysql', 1),
                ];
                wp_update_post($post_update);
            }

            wp_cache_flush();

           wp_die();
        } else {
            wp_send_json_error(['message'=> 'Unable to replace the file. Please try again.'], 500);
        }
    }
}
