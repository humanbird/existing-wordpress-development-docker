<?php
/* @var $this NewsletterSendinblue */

require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$mailer = $this->get_mailer();

if (!$controls->is_action()) {
    $controls->data = $this->options;
} else {

    if ($controls->is_action('save')) {
        $this->save_options($controls->data);
        $controls->add_toast_saved();
    }

    if ($controls->is_action('trigger')) {
        $res = $this->bounce();
        if (is_wp_error($res)) {
            $controls->errors .= $res->get_error_message();
            if ($res->get_error_data()) {
                $controls->errors .= '<textarea style="width: 100%; height: 200px; font-family: monospace; font-size: 11px">' . esc_html($res->get_error_data()) . '</textarea>';
            }
        } else {
            $controls->messages = 'Done. Found ' . $res . ' bounces.';
            if ($res) {
                $controls->messages .= '<div style="height: 200px; overflow: hidden; scroll: auto">' . esc_html(implode(', ', $this->bounce_emails)) . '</div>';
            }
        }
    }

    if ($controls->is_action('reset')) {
        $this->save_last_run(0);
        $controls->messages = 'Done.';
    }

    if ($controls->is_action('test')) {
        if (!empty($this->options['turbo'])) {
            $messages = $this->get_test_messages($controls->data['test_email'], $this->options['turbo']);

            $r = $mailer->send_batch_with_stats($messages);

            if (is_wp_error($r)) {
                $controls->errors .= '<strong>Delivery error: ' . $r->get_error_message() . '</strong><br>';
                foreach ($messages as $message) {
                    $controls->errors .= 'Error: ' . esc_html($message->error) . '<br>';
                }
            } else {
                $controls->messages = 'Success. You should see ' . $this->options['turbo'] . ' test messages on Brevo console panel and in the selected mailbox.';
                $controls->messages .= '<br>Max speed: ' . $mailer->get_capability() . ' emails per hour';
            }
        } else {
            $message = $this->get_test_message($controls->data['test_email']);
            $result = $mailer->send_with_stats($message);
            if (is_wp_error($result)) {
                $controls->errors .= 'Delivery error: ' . $result->get_error_message() . '<br>';
            } else {
                $controls->messages = 'Success. You should see the test message in Brevo console panel.';
                $controls->messages .= '<br>Max speed: ' . $mailer->get_capability() . ' emails per hour';
            }
        }
    }
}

$this->set_warnings($controls);

$is_webhook_active = false;

if (!empty($this->options['api_key'])) {
    $is_webhook_active = $this->is_webhook_active();
    if (is_wp_error($is_webhook_active)) {
        $controls->errors .= 'Unable to check the webhook, please verify the API key: ' . $is_webhook_active->get_error_message();
    } else {
        if (!$is_webhook_active) {
            //$controls->errors .= 'No webhook active';
            $r = $this->create_webhook();
            if (is_wp_error($r)) {
                $controls->errors .= 'Unable to create a webhook: ' . $r->get_error_message();
            }
        }
    }
}
?>

<div class="wrap" id="tnp-wrap">
    <?php include NEWSLETTER_ADMIN_HEADER ?>
    <div id="tnp-heading">
        <?php $controls->title_help('https://www.thenewsletterplugin.com/documentation/?p=198432') ?>
        <h2><?php echo $this->get_title(); ?></h2>
    </div>

    <div id="tnp-body">

        <?php $controls->show(); ?>

        <p style="font-weight: bold; padding: 15px 0; font-size: 1.1em;">
            Warning. We can use only the transactional email delivery service and Brevo applies strong restrictions on the number of
            emails that can be sent per hour/day/month.<br><br>
            Only with a dedicated IP plan, those limits can be raised.
            <br><br>
            You can still use this addon to test their service and/or to send a really small amount of emails.
            <br><br>
            You need a <a href="https://get.brevo.com/7y22fro1brqh" target="_blank">free account with Brevo (aff)</a>.
        </p>

        <form action="" method="post">
            <?php $controls->init(); ?>

            <div id="tabs">
                <ul>
                    <li><a href="#tabs-general">General</a></li>
                    <li><a href="#tabs-3">Bounces</a></li>
                </ul>

                <div id="tabs-general">


                    <table class="form-table">
                        <tr valign="top">
                            <th>Enabled?</th>
                            <td>
                                <?php $controls->enabled(); ?>
                                <p class="description">
                                    When not enabled the extension suspends all its activities.
                                </p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th>API Key</th>
                            <td>
                                <?php $controls->text('api_key', 80); ?>
                                <p class="description"><a href="https://get.brevo.com/7y22fro1brqh" target="_blank">Get a free API key (version 3)</a></p>
                            </td>
                        </tr>


                        <tr>
                            <th>
                                Turbo send
                            </th>
                            <td>
                                <?php
                                $controls->select('turbo', array('' => 'Disabled',
                                    '2' => '2 processors',
                                    '3' => '3 processors',
                                    '4' => '4 processors',
                                    '5' => '5 processors',
                                    '6' => '6 processors',
                                    '7' => '7 processors',
                                    '8' => '8 processors',
                                    '9' => '9 processors',
                                    '10' => '10 processors'));
                                ?>
                                (read carefully the help page)
                            </td>
                        </tr>

                        <tr>
                            <th>To test this configuration</th>
                            <td>
                                <?php $controls->text('test_email', 30); ?>
                                <?php $controls->button_primary('test', 'Send a message to this email'); ?>
                                <p class="description">
                                    The test is made using the configuration you see, without saving it. The test works even if the
                                    extensions is set as "disabled".
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>


                <div id="tabs-3">

                    <?php if ($is_webhook_active) { ?>
                        <p>Correctly connected to Brevo to manage bounces and cancellations.</p>
                    <?php } else { ?>
                        <p><strong>Still not connected to Brevo to manage bounces and cancellations.</strong></p>
                    <?php } ?>

                    <?php
                    $whs = $this->get_webhooks();
                    if (is_wp_error($whs)) {
                        $mex = $whs->get_error_message();
                    } else {
                        // JSON response from Brevo
                        $mex = print_r($whs, true);
                    }
                    ?>

                    <table class="form-table">
                        <tr valign="top">
                            <th>Debug information</th>
                            <td>
                                <pre style="height: 250px; overflow: scroll;"><?php echo esc_html($mex); ?></pre>
                            </td>
                        </tr>
                    </table>

                </div>

            </div>

            <p>
                <?php $controls->button_save(); ?>
            </p>
        </form>
    </div>
    <?php include NEWSLETTER_ADMIN_FOOTER; ?>
</div>
