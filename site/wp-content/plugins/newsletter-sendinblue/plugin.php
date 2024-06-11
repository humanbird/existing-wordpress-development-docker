<?php

class NewsletterSendinblue extends NewsletterMailerAddon {

    /**
     * @var NewsletterSendinblue
     */
    static $instance;

    function __construct($version) {
        self::$instance = $this;
        $this->menu_title = 'Brevo';
        parent::__construct('sendinblue', $version, __DIR__);
    }

    function init() {
        parent::init();

        add_action('wp_ajax_nopriv_newsletter-sendinblue', [$this, 'webhook_callback']);
    }

    function weekly_check() {
        parent::weekly_check();
        $license_key = Newsletter::instance()->get_license_key();
        $response = wp_remote_post('https://www.thenewsletterplugin.com/wp-content/addon-check.php?k=' . urlencode($license_key)
                . '&a=' . urlencode($this->name) . '&d=' . urlencode(home_url()) . '&v=' . urlencode($this->version)
                . '&ml=' . (Newsletter::instance()->is_multilanguage() ? '1' : '0'));
    }

    function webhook_callback() {
        $logger = $this->get_logger();
        $body = file_get_contents('php://input');
        $data = json_decode($body);
        if (!$data) {
            $logger->error('Not a JSON content');
            $logger->error($body);
            die();
        }
        $logger->debug($data);
        $event = $data->event;
        $email = $data->email;

        $logger->info('Status notification for email: ' . $email);

        $newsletter = Newsletter::instance();

        $user = $newsletter->get_user($email);
        if (!$user) {
            $logger->error('Subscriber not found: ' . $email);
            die();
        }

        switch ($event) {
            case 'hard_bounce':
            case 'invalid_email':
            case 'soft_bounce':
            case 'blocked':
                $newsletter->set_user_status($user, TNP_User::STATUS_BOUNCED);
                break;
            case 'spam':
            case 'complaint':
                $newsletter->set_user_status($user, TNP_User::STATUS_COMPLAINED);
                break;
            case 'unsubscribed':
                $newsletter->set_user_status($user, TNP_User::STATUS_UNSUBSCRIBED);
                break;
        }
    }

    function get_webhook_url() {
        return admin_url('admin-ajax.php') . '?action=newsletter-sendinblue';
    }

    function is_webhook_active() {
        $whs = $this->get_webhooks();
        if (is_wp_error($whs)) {
            return $whs;
        }
        if (empty($whs)) {
            return false;
        }

        $url = $this->get_webhook_url();
        foreach ($whs->webhooks as $wh) {
            if ($wh->url === $url) {
                if (array_search('spam', $wh->events) === false) {
                    $this->update_webhook($wh->id);
                }
                return true;
            }
        }
        return false;
    }

    function update_webhook($id) {
        if (empty($this->options['api_key'])) {
            return new WP_Error('1', 'Missing API key: set it on general configuration');
        }
        $data = [
            'events' => ['hardBounce', 'softBounce', 'unsubscribed', 'spam'],
            'url' => $this->get_webhook_url(),
            'description' => 'The Newsletter Plugin Webhook',
            'type' => 'transactional'
        ];
        $response = wp_remote_request('https://api.brevo.com/v3/webhooks/' . $id, [
            'method' => 'PUT',
            'headers' => [
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
                'api-key' => $this->options['api_key']
            ],
            'body' => json_encode($data)
        ]);

        if (is_wp_error($response)) {
            return $response;
        }
        if (wp_remote_retrieve_response_code($response) == 204) {
            return wp_remote_retrieve_body($response);
        }

        return new WP_Error('1', wp_remote_retrieve_body($response));
    }

    function get_webhooks() {
        if (empty($this->options['api_key'])) {
            return new WP_Error('1', 'Missing API key: set it on general configuration');
        }

        $response = wp_remote_get('https://api.brevo.com/v3/webhooks?type=transactional',
                [
                    'headers' => [
                        'accept' => 'application/json',
                        'api-key' => $this->options['api_key']
                    ],
                    'sslverify' => 0
                ]
        );

        if (is_wp_error($response)) {
            return $response;
        }
        if (wp_remote_retrieve_response_code($response) == 200) {
            return json_decode(wp_remote_retrieve_body($response));
        }
        $code = wp_remote_retrieve_response_code($response);
        if ($code == 404 || $code == 400) {
            return [];
        }

        return new WP_Error('1', 'HTTP Response: ' . wp_remote_retrieve_response_code($response) . ' - ' . wp_remote_retrieve_body($response));
    }

    function create_webhook() {
        if (empty($this->options['api_key'])) {
            return new WP_Error('1', 'Missing API key: set it on general configuration');
        }
        $data = [
            'events' => ['hardBounce', 'softBounce', 'unsubscribed', 'spam'],
            'url' => $this->get_webhook_url(),
            'description' => 'The Newsletter Plugin Webhook',
            'type' => 'transactional'
        ];
        $response = wp_remote_post('https://api.brevo.com/v3/webhooks', [
            'headers' => [
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
                'api-key' => $this->options['api_key']
            ],
            'body' => json_encode($data),
            'sslverify' => 0
        ]);

        if (is_wp_error($response)) {
            return $response;
        }
        if (wp_remote_retrieve_response_code($response) >= 200 && wp_remote_retrieve_response_code($response) <= 299) {
            return json_decode(wp_remote_retrieve_body($response));
        }

        return new WP_Error('1', wp_remote_retrieve_body($response));
    }

    function get_mailer() {
        static $mailer = null;

        if (!$mailer) {
            $mailer = new NewsletterSendinblueMailer($this);
        }

        return $mailer;
    }
}

class NewsletterSendinblueMailer extends NewsletterMailer {

    var $module;

    /**
     *
     * @param NewsletterSendinblue $module
     */
    function __construct($module) {
        $this->module = $module;
        parent::__construct($module->name, $module->options);
        if (!empty($module->options['turbo'])) {
            $this->batch_size = (int) $module->options['turbo'];
        }
    }

    function get_description() {
        return 'Sendinblue Addon';
    }

    function build_curl(TNP_Mailer_Message $message) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        $data = $this->build_data($message);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $post_headers = array();
        $post_headers[] = 'Content-Type: application/json';
        $post_headers[] = 'api-key: ' . $this->options['api_key'];
        //var_dump($post_headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $post_headers);

        curl_setopt($ch, CURLOPT_URL, "https://api.brevo.com/v3/smtp/email");
        return $ch;
    }

    /**
     * @param type $to
     * @param type $subject
     * @param type $message
     * @param type $headers
     * @param array $from
     * @return array
     */
    public function build_data(TNP_Mailer_Message $message) {
        $newsletter = Newsletter::instance();

        $data = array('sender' => array(), 'to' => array());

        $data['to'][] = array('email' => $message->to);

        $data['subject'] = $message->subject;

        // They should be available even as global headers, not clear!
        if (!empty($message->headers)) {
            $data['headers'] = $message->headers;
        }

        $data['sender']['email'] = $newsletter->options['sender_email'];
        $data['sender']['name'] = $newsletter->options['sender_name'];

        if (!empty($newsletter->options['reply_to'])) {
            $data['replyTo'] = array('email' => $newsletter->options['reply_to']);
        }

        $data['htmlContent'] = $message->body;
        if (!empty($message->body_text)) {
            $data['textContent'] = $message->body_text;
        }

        $this->get_logger()->debug($data);

        return $data;
    }

    /**
     *
     * @param TNP_Mailer_Message[] $messages
     * @return \WP_Error|boolean
     */
    public function send_chunk($messages) {

        $logger = $this->get_logger();
        $mh = curl_multi_init();

        foreach ($messages as $message) {
            $ch = $this->build_curl($message);
            $message->ch = $ch;
            curl_multi_add_handle($mh, $ch);
        }
        $active = 1;
        $start = time();
        while ($active) {
            curl_multi_exec($mh, $active);
            curl_multi_select($mh);
            if (time() - $start > 300) {
                break;
            }
        }

        $wp_error = null;
        foreach ($messages as $message) {
            $curl = $message->ch;
            $logger->debug(curl_errno($curl));

            if (curl_errno($curl) != 0) {
                $message->error = 'cURL error: ' . curl_errno($curl) . ' - ' . curl_error($curl);
                $wp_error = new WP_Error(self::ERROR_GENERIC, $message->error);
                $logger->error($message->error);
            } else {

                $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                if ($code < 200 || $code > 299) {
                    $message->error = 'HTTP Error code ' . $code . ' ' . curl_multi_getcontent($curl);
                    $wp_error = new WP_Error(self::ERROR_GENERIC, $message->error);
                    $logger->error($message->error);
                }
            }
        }

        curl_multi_close($mh);

        if ($wp_error) {
            return $wp_error;
        }
        return true;
    }

    /**
     *
     * @param TNP_Mailer_Message $message
     * @return \WP_Error|boolean
     */
    function send($message) {
        $logger = $this->get_logger();

        $curl = $this->build_curl($message);

        $response = curl_exec($curl);
        $result = true;

        $logger->debug($response);

        if (curl_errno($curl) != 0) {
            $message->error = 'cURL error: ' . curl_errno($curl) . ' - ' . curl_error($curl);
            $logger->error($message->error);
            $result = new WP_Error(self::ERROR_GENERIC, $message->error);
        } else {

            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($code < 200 || $code > 299) {
                $message->error = 'HTTP Error code ' . $code . ' ' . $response;
                $logger->error($message->error);
                $result = new WP_Error(self::ERROR_GENERIC, $message->error);
            }
        }

        curl_close($curl);

        $logger->debug('Sent to ' . $message->to);

        return $result;
    }
}
