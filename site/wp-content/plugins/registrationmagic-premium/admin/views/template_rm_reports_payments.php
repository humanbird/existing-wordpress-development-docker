<?php
if (!defined('WPINC')) {
    die('Closed');
}

    $selected_form='all';
    if(isset($data->req->form_id)){
       $selected_form = $data->req->form_id;
    }
    $forms = '';
    foreach ($data->forms as $id => $form_title):
        if($selected_form == $id){
            $forms .= '<option value="'.$id.'" selected>'.$form_title.'</option>';
        }
        else{
            $forms .= '<option value="'.$id.'">'.$form_title.'</option>';
        }
    endforeach;
    $date_filter='';
    $start_date='';
    $end_date='';
    $selected_status ='all';
    $form_id = $data->req->form_id;
    if(isset($data->req->filter_date)){
        $date_filter = $data->req->filter_date;
        $start_date = $data->req->start_date;
        $end_date = $data->req->end_date;
        $selected_status = $data->req->status;
    }
    ?>
    <div class="rmagic">
        <div class="rmagic-reports">
            <div class="rm-reports-dashboard rm-box-title rm-box-mb-25">
                <?php _e('Payments Report','registrationmagic-addon');?>
            </div>
            <div class="rm-reports-filters-box rm-box-border rm-box-white-bg rm-box-mb-25 rm-box-ptb">

                <div class="rm-filter-reports-form">
                    <form class="rm-report-forms rm-box-wrap" action="" method="GET">
                        <input type="hidden" name="page" value="rm_reports_payments"/>
                        <div class="rm-report-form rm-box-row rm-box-bottom">
                            <div class="rm-box-col-9">
                                <div class="rm-box-row">
                                    <div class="rm-box-col-4">      
                                        <div class="rm-report-filter-attr">
                                            <label><?php _e('Date', 'registrationmagic-addon'); ?></label>
                                            <div id="rm-reportrange" >
                                              <input type="text" name="rm_filter_date" value="<?php echo $date_filter; ?>" onkeydown="return false;"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rm-box-col-4">
                                        <div class="rm-report-filter-attr">
                                            <label><?php _e('Select Form', 'registrationmagic-addon'); ?></label>
                                            <select class="" name="rm_form_id"><option value="all"><?php _e('All', 'registrationmagic-addon'); ?></option><?php echo $forms; ?></select>
                                        </div>
                                    </div>
                                        <div class="rm-box-col-4">
                                            <div class="rm-report-filter-attr">
                                                <label><?php _e('Status', 'registrationmagic-addon'); ?></label>
                                                <select class="" name="rm_status">
                                                    <?php
                                                    $status = array('all' => 'All', 'Pending' => 'Pending', 'Completed' => 'Completed', 'Canceled' => 'Canceled', 'Refunded' => 'Refunded');
                                                    foreach ($status as $key => $value) {
                                                        $selected = '';
                                                        if ($key == $data->req->status) {
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="rm-box-col-3">                       
                                    <div class="rm-box-btn-wrap rm-box-text-right">
                                        <button type="submit" id="rm_submit_btn" class="rm-btn-primary rm-btn"><?php _e('Search', 'registrationmagic-addon'); ?></button>
                                        <button type="button" id="rm_reset_btn" class="rm-btn-secondary rm-btn" onclick="window.location.href='<?php echo admin_url('?page=rm_reports_payments'); ?>'"><?php _e('Reset', 'registrationmagic-addon'); ?></button>
                                    </div>

                                </div>
                            </div>

                    </form>
                </div>   
                </div>

            </div>
            <div class="rm-reports-payments">
                <?php if(!empty($data->payments)):?>
                <div class="rm-reports-payments-revenue-charts rm-box-border rm-box-white-bg rm-box-mb-25 rm-box-p">
                    <canvas id="rmPaymentsRevenueChart"></canvas>
                </div>
                <div class="rm-reports-payments-submission-charts rm-box-border rm-box-white-bg rm-box-mb-25 rm-box-p">
                    <canvas id="rmPaymentsSubmissionChart"></canvas>
                </div>
                <div class="rm-reports-payments-preview rm-report-preview">
                    
                    <div class="rm-reports-preview-title rm-box-title rm-box-mb-25"><?php _e('Preview','registrationmagic-addon');?></div>
                    <div class="rm-reports-preview-sub-title rm-box-sub-title rm-box-mb-25"><?php _e('This preview only displays initial few rows of the generated report. The downloaded file will have complete report data.','registrationmagic-addon');?></div>
                    <table class="rm-reports-table-payments rm-reports-table">
                        <thead>
                        <tr>
                            <th><?php _e('Date','registrationmagic-addon');?></th>
                            <th><?php _e('Form Name','registrationmagic-addon');?></th>
                            <th><?php _e('User Name','registrationmagic-addon');?></th>
                            <th><?php _e('Amount','registrationmagic-addon');?></th>
                            <th><?php _e('Status','registrationmagic-addon');?></th>
                            <th><?php _e('Payment Method','registrationmagic-addon');?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php 
                        
                            foreach($data->payments as $payment){
                                ?>
                            <tr>
                                <td><?php echo date('d-m-Y', strtotime($payment->submitted_on));?></td>
                                <td><?php echo $payment->form_name;?></td>
                                <td><?php echo $payment->user_email;?></td>
                                <td><?php echo $payment->currency .' '.$payment->total_amount;?></td>
                                <td>
                                    <?php if($payment->status =='Succeeded' || $payment->status =='succeeded'){
                                        echo 'Completed';
                                    }else{
                                        echo $payment->status;
                                    }
                                    ?>
                                </td>
                                <td><?php echo ucfirst($payment->pay_proc);?></td>
                            </tr>
                                <?php 
                            }
                        
                        ?></tbody>
                    </table>
                    
                </div>
                <?php else:?>
                <div class="rmagic-cards"><div class="rm-reports-no-data-found rmnotice rm-box-border rm-box-mb-25"><?php _e('No data found.','registrationmagic-addon');?></div></div>
                <?php endif;?>
                <div class="rm-total-record-wrap rm-box-border rm-box-white-bg rm-box-mb-25 rm-box-ptb rm-box-wrap">
                    <div class="rm-box-row rm-box-center">
                        <div class="rm-total-record-found rm-box-col-10"> <?php echo 'Total records found: '.$data->payments_count;?></div>
                        <?php if(!empty($data->payments)):?>
                        <div class="rm-report-payments-export rm-reports-export rm-box-col-2 rm-box-btn-wrap rm-box-text-right">
                            <form action="" method="post">
                                <input type="hidden" name="rm_slug" value="rm_reports_payments_download">
                                <input type="hidden" name="rm_filter_date" value="<?php echo $date_filter; ?>">
                                <input type="hidden" name="rm_form_id" value="<?php echo $form_id; ?>">
                                <input type="hidden" name="rm_status" value="<?php echo $selected_status; ?>">
                                <button type="submit" name="submit" class="rm-reports-export-btn rm-btn-primary rm-btn"><?php _e('Export All','registrationmagic-addon'); ?></button>
                            </form>
                        </div>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>
    

<script type="text/javascript">

jQuery(function() {
    var start = moment('<?php echo $start_date;?>');
    var end = moment('<?php echo $end_date;?>');
    console.log(start+ 'HHH' +end);
    function cb(start, end) {
        jQuery('#rm-reportrange input').val(start.format('YYYY/MM/DD') + '-' + end.format('YYYY/MM/DD'));
    }

    jQuery('#rm-reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        maxDate: new Date(),
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);

});
</script>
<script>
jQuery(window).load(function(e){
    load_payments_revenue_chart();
    load_payments_submission_chart();

});
function load_payments_revenue_chart(){
    var ctx = document.getElementById("rmPaymentsRevenueChart");
    new Chart(ctx, {
        data: {
            datasets: [
              {
                type : "line",
                label: 'Revenue',
                data: <?php echo json_encode($data->payments_chart->payment_total);?>,
                borderColor: 'rgb(34 113 177)',
                backgroundColor: 'rgb(0 134 245 / 20%)',
                fill: true,
                borderWidth: 1,
                tension: .5
              }
            ],
            labels: <?php echo json_encode($data->payments_chart->payment_date);?>
        },
        options: {scales: {
            xAxes: [{
                barThickness: 6,  // number (pixels) or 'flex'
                maxBarThickness: 8 // number (pixels)
            }]
        }}
    });
}
function load_payments_submission_chart(){
    var ctx = document.getElementById("rmPaymentsSubmissionChart");
    new Chart(ctx, {
        data: {
            datasets: [
              {
                type : "line",
                label: 'Payment Count',
                data: <?php echo json_encode($data->payments_chart->payment_count);?>,
                borderColor: 'rgb(99, 190, 225)',
                backgroundColor: 'rgba(99, 190, 225, 0.2)',
                fill: true,
                borderWidth: 1,
                tension: .5
              }
            ],
            labels: <?php echo json_encode($data->payments_chart->payment_date);?>
        },
        options: {scales: {
            xAxes: [{
                barThickness: 6,  // number (pixels) or 'flex'
                maxBarThickness: 8 // number (pixels)
            }]
        },
        scale: {
                y:{
                    ticks: {
                        precision: 0
                    },
                    min: 0
                }
            }
    }
    });
}
</script>