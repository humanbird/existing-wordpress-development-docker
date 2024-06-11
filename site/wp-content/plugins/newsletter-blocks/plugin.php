<?php

class NewsletterBlocks extends NewsletterAddon {

    /**
     * @var NewsletterBlocks
     */
    static $instance;

    function __construct($version) {
        self::$instance = $this;
        parent::__construct('blocks', $version);

        add_filter('newsletter_blocks_dir', array($this, 'hook_newsletter_blocks_dir'));
    }

    public function init() {
        parent::init();
    }

    function weekly_check() {
        parent::weekly_check();
        $license_key = Newsletter::instance()->get_license_key();
        $response = wp_remote_post('https://www.thenewsletterplugin.com/wp-content/addon-check.php?k=' . urlencode($license_key)
                . '&a=' . urlencode($this->name) . '&d=' . urlencode(home_url()) . '&v=' . urlencode($this->version)
                . '&ml=' . (Newsletter::instance()->is_multilanguage() ? '1' : '0'));
    }

    function hook_newsletter_blocks_dir($blocks_dir) {
        $blocks_dir[] = __DIR__ . '/blocks';

        return $blocks_dir;
    }
}
