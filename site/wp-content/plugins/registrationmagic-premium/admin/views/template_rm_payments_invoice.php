<?php
if (!defined('WPINC')) {
    die('Closed');
}
?>

<style>

    html,body{
        border: 0;
        outline: 0;
        font-size: 100%;
        vertical-align: baseline;
        background: transparent;
        margin: 0;
        padding: 0;
    }

    body {
     color:#5F6776;
    }

    .item-header {
        line-height: 38px;
        padding: 0 0 0 20px;
        text-transform: uppercase;
        color: #555;
        background: #efebeb;
        border-top: 1px solid #F6F7F9;
        border-bottom: 1px solid #F6F7F9;
    }

    .topheading {


    }

    .heading-row {
        border-bottom: 1px solid #F6F7F9;
        border-top: 1px solid #ddd;
        padding: 0px;
    }

    table{
        border-spacing: 0px;
        font-size: 14px;
        border-collapse: collapse;


    }

    table.table-border{
        border: 1px solid #F6F7F9;
    }

    table tr {
        border-spacing: 0px;
        padding: 0px;
    }

    table tr td {
        border-spacing: 0px;
        padding: 0px;
    }
    .items-table tr th{
      
    }
    .items-table tr td { 
        border-top: 1px solid #F6F7F9;
    }

    .items-table tr.rm-blank-row td {
        border: 0px;
        line-height: 20px;
    }

</style>
<?php
if (isset($data->options['enable_invoice']) && !empty($data->options['enable_invoice'])):
    $custom_logo_id = get_theme_mod('custom_logo');
    $image = wp_get_attachment_url($custom_logo_id, 'full');
    $invoice_logo_url = isset($data->options['invoice_company_logo']) && $data->options['invoice_company_logo'] != '' ? wp_get_attachment_url($data->options['invoice_company_logo']) : $image;
    $form_data = isset($data->submission->data) ? maybe_unserialize($data->submission->data) : array();
    $field_data = array();
    if (isset($form_data)):
        foreach ($form_data as $field):
            $field_data[$field->type] = $field->value;
        endforeach;
    endif;
    $name = '';
    if (isset($field_data['Fname'])):
        $first_name = $field_data['Fname'];
        $name .= $first_name;
    endif;
    if (isset($field_data['Lname'])):
        $last_name = $field_data['Lname'];
        $name .= ' ' . $last_name;
    endif;

    $invoice_company_name = isset($data->options['invoice_company_name']) && $data->options['invoice_company_name'] != '' ? $data->options['invoice_company_name'] : get_bloginfo('name');
    $invoice_company_address = isset($data->options['invoice_company_address']) ? $data->options['invoice_company_address'] : '';
    $invoice_company_contact_no = isset($data->options['invoice_company_contact_no']) ? $data->options['invoice_company_contact_no'] : '';
    $invoice_company_email = isset($data->options['invoice_company_email']) ? $data->options['invoice_company_email'] : '';
    $invoice_company_vat = isset($data->options['invoice_company_vat']) ? $data->options['invoice_company_vat'] : '';
    $invoice_enable_footer = isset($data->options['invoice_enable_footer']) ? $data->options['invoice_enable_footer'] : '';
    $invoice_footer_text = isset($data->options['invoice_footer_text']) ? $data->options['invoice_footer_text'] : '';
    ?>

    <html lang="en-US" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#">  
        <head>
            <!-- Metadata -->
            <meta charset="UTF-8">
            <meta name="HandheldFriendly" content="true" />

            <title><?php _e('Invoice', 'registrationmagic-addon'); ?></title>
        </head>

        <body>
            <div id="main">

                <table style="text-align:left;">

                    <tr>
                        <td style="width:30%"><img src="<?php echo $invoice_logo_url; ?>"></td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;</td>
                    </tr>

                        <tr>
                            <td class="" style="width:30%" style="line-height:2"><h2> Invoice</h2></td>
                            <td>&nbsp;&nbsp;</td>
                        </tr>
                        <tr><td>&nbsp;&nbsp;</td></tr>

                    <tr style="border:1px solid #ddd">
                        <td width="30%" style="padding:0px">
                            <table style="padding:0px">
                                <thead>
                                    <tr class="heading-row">
                                      <td class="topheading"><b><?php _e('From', 'registrationmagic-addon'); ?></b></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>  

                                    <?php if (!empty($invoice_company_name)): ?>
                                        <tr>
                                            <td><?php echo wp_kses_post((string)$invoice_company_name); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($invoice_company_address)): ?>
                                        <tr>
                                            <td><?php echo wp_kses_post((string)$invoice_company_address); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($invoice_company_email)): ?>
                                        <tr>
                                            <td><?php echo wp_kses_post((string)$invoice_company_email); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($invoice_company_contact_no)): ?>
                                        <tr>
                                            <td><?php echo wp_kses_post((string)$invoice_company_contact_no); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($invoice_company_vat)): ?>
                                        <tr>
                                            <td><?php echo wp_kses_post((string)$invoice_company_vat); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </td>
                        <td  width="70%" style="padding:0px">
                            <table>
                                <thead>
                                    <tr class="heading-row">
                                        <td class="topheading">
                                            <b><?php _e('To', 'registrationmagic-addon'); ?></b>
                                        </td>
                                    </tr>
                                </thead>

                                <tr>
                                    <td>&nbsp;&nbsp;</td>
                                </tr> 
                                <?php if (!empty($name)): ?>
                                    <tr>
                                        <td><?php echo wp_kses_post((string)$name); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($field_data['Email'])): ?>
                                    <tr>
                                        <td> &nbsp;<a href="mailto:<?php echo $field_data['Email']; ?>"><?php echo $field_data['Email']; ?></a></td>
                                    </tr>
                                <?php endif; ?>
                                <tr><td>&nbsp;&nbsp;</td></tr>
                                
                                <tr>
                                    <td>
                                        <table>
                                            <?php echo do_action('rm_invoice_additional_data', $data->submission);?>
                                            <tr>
                                                <td width="40%"><b><?php _e('Invoice Date: ', 'registrationmagic-addon'); ?></b></td>
                                                <td width="60%"><?php echo esc_html(RM_Utilities::localize_time($data->payment->posted_date, 'j M Y, h:i a')); ?></td>
                                            </tr>
                                            <tr>
                                                
                                                <td width="40%"><b><?php _e('Invoice No.: ', 'registrationmagic-addon'); ?></b></td>
                                                <td width="60%"><?php echo esc_html($data->payment->invoice); ?></td>
                                            </tr>
                                            <tr>
                                                <td width="40%"><b><?php _e('Form: ', 'registrationmagic-addon'); ?></b></td>
                                                <td width="60%"><?php echo wp_kses_post((string)$data->form_name); ?></td>
                                            </tr>

                                            <tr>
                                                <td width="40%"><b><?php _e('Payment Method: ', 'registrationmagic-addon'); ?></b></td>
                                                <td width="60%"><?php echo esc_html(ucfirst($data->payment->pay_proc)); ?></td>
                                            </tr>

                                            <tr>
                                                <td width="40%"><b><?php _e('Payment Status: ', 'registrationmagic-addon'); ?></b></td>
                                                <td width="60%"><?php echo esc_html($data->payment->status); ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td>&nbsp;	&nbsp;</td></tr>
                                <tr>
                                    <td>

                                    </td>
                                </tr>
                            </table>
                            
                            <table class="" style="border:1px solid #F6F7F9">
                    <thead>
                        <tr class="heading-row" style="background-color: #F6F7F9;">
                            <td class="topheading" style="line-height:45px">   <b><?php _e('Invoice Items ', 'registrationmagic-addon'); ?></b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <table class="items-table" style="vertical-align: middle; line-height: 40px">
                                    <thead>
                                        <tr class="rm-ordered-product-table" style="vertical-align: middle; line-height: 40px">
                                            <th style="vertical-align: middle;"><b><?php _e('Product', 'registrationmagic-addon'); ?></b></th>
                                            <th><b><?php _e('Qty', 'registrationmagic-addon'); ?></b></th>
                                            <th><b><?php _e('Price', 'registrationmagic-addon'); ?></b></th>
                                            <th><b><?php _e('Total', 'registrationmagic-addon'); ?></b></th>
                                        </tr>


                                    </thead>
                                    <?php if (isset($data->payment)): ?>
                                        <tbody>

                                            <?php
                                            $bill_products = unserialize($data->payment->bill);
                                            if (is_object($bill_products)):
                                                $grand_total = 0;
                                                foreach ($bill_products->billing as $key => $product) {
                                                    $sub_total = wp_kses_post((string)$product->price * $product->qty);
                                                    $grand_total = $grand_total + $sub_total;
                                                    ?>
                                                    <tr class="rm-products-list" style="vertical-align: middle;">
                                                        <td class="service" style="line-height: 20px"><span><br><?php echo wp_kses_post((string)$product->label); ?><br></span></td>    
                                                        <td class="qty"><span><?php echo wp_kses_post((string)$product->qty); ?></span></td>
                                                        <td class="unit"><span><?php echo wp_kses_post((string)RM_Utilities::get_formatted_price($product->price)); ?></span></td>
                                                        <td class="total"><span><?php echo wp_kses_post((string)RM_Utilities::get_formatted_price($sub_total)); ?></span></td>

                                                    </tr>
                                                    <?php
                                                }
                                            endif;
                                            ?>
                                        </tbody>
                                        <tfoot class="order-totals-footer">

                                            <?php if(isset($bill_products->tax) && !empty($bill_products->tax)):?>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td><b><?php $gopts = new RM_Options(); echo $gopts->get_value_of('tax_rename'); ?></b></td>
                                                <td class="total"><?php echo wp_kses_post((string)RM_Utilities::get_formatted_price($bill_products->tax)); ?></td>
                                            </tr>
                                            <?php endif;?>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td><b><?php _e('Grand Total', 'registrationmagic-addon'); ?></b></td>
                                                <td class="total"><?php echo wp_kses_post((string)RM_Utilities::get_formatted_price($bill_products->total_price)); ?></td>
                                            </tr>
                                        </tfoot>
                                    <?php endif; ?> 
                                </table>
                            </td>
                        </tr>

                        <tr><td>&nbsp;	&nbsp;&nbsp;	&nbsp;</td></tr>
                    </tbody>
                </table>
                    
                        </td>
                    </tr>

                </table>


           

                <?php if (!empty($invoice_enable_footer)): ?>
                    <table>

                        <tr class="invoice-footer">
                            <td><?php echo wp_kses_post((string)$invoice_footer_text); ?></td>
                        </tr>

                    </table>

                </div>

            </body> 
        </html>
    <?php endif; ?>
<?php endif; ?>