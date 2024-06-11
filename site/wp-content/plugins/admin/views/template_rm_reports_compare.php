<?php
if (!defined('WPINC')) {
    die('Closed');
}
    $multi_forms = '';
    foreach ($data->forms as $id => $form_title):
        if(in_array($id, $data->forms_ids)){
            $multi_forms .= '<option value="'.$id.'" selected>'.$form_title.'</option>';
        }else{
           $multi_forms .= '<option value="'.$id.'">'.$form_title.'</option>'; 
        }
    endforeach;
    $date_filter='';
    $start_date='';
    $end_date='';
    if(isset($data->req->filter_date)){
        $date_filter = $data->req->filter_date;
        $start_date = $data->req->start_date;
        $end_date = $data->req->end_date;
    }
    ?>
    <div class="rmagic">
        <div class="rmagic-reports">
            <div class="rm-reports-dashboard rm-box-title rm-box-mb-25">
                <?php _e('Form Comparison Report','registrationmagic-addon');?>
            </div>
            <div class="rm-reports-filters-box rm-box-border rm-box-white-bg rm-box-mb-25 rm-box-ptb">
                <div class="rm-filter-reports-form">
                    <form class="rm-report-forms rm-box-wrap" action="" method="GET">
                        <input type="hidden" name="page" value="rm_reports_form_compare"/>
                        <div class="rm-report-form rm-box-row rm-box-bottom">
                            <div class="rm-box-col-12">
                                <div class="rm-box-row">
                                    <div class="rm-box-col-6">
                                        <div class="rm-report-filter-attr">
                                            <label><?php _e('Select Forms', 'registrationmagic-addon'); ?></label>    
                                            <select class="js-select2" multiple="multiple" name="forms_ids[]" id="multiple-select-values">
                                                <?php echo $multi_forms; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="rm-box-col-3">      
                                        <div class="rm-report-filter-attr">
                                            <label><?php _e('Date', 'registrationmagic-addon'); ?></label>
                                            <div id="rm-reportrange" >
                                               <input type="text" name="rm_filter_date" value="<?php echo $date_filter; ?>" onkeydown="return false;"/>
                                            </div>
                                        </div>
                                    </div> 
                                    
                                    <div class="rm-box-col-3">
                                        <div class="rm-box-btn-wrap rm-report-filter-attr rm-box-text-right">
                                             <label><?php _e('Action', 'registrationmagic-addon'); ?></label>
                                            <button type="submit" id="rm_submit_btn" class="rm_btn rm-btn rm-btn-primary"><?php _e('Compare', 'registrationmagic-addon'); ?></button>
                                            <button type="button" id="rm_reset_btn" class="rm-btn-secondary rm-btn" onclick="window.location.href='<?php echo admin_url('?page=rm_reports_form_compare'); ?>'"><?php _e('Reset', 'registrationmagic-addon'); ?></button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                       
                        </div>
                    </form>
                </div>
            </div>
            <?php if($data->submissions->total_submission_found):?>
            <div class="rm-compare-day-wise-chart rm-box-border rm-box-white-bg rm-box-mb-25 rm-box-p">
                <canvas id="rm-form-compare-charts"></canvas>
            </div>
            <?php endif;?>
            <div class="rm-reports-compare-forms rm-report-preview">
                <div class="rm-box-title rm-box-mb-25"><?php _e('Comparison Table','registrationmagic-addon');?></div>
                <table class="rm-forms-compare-table rm-reports-table">
                    <tr>
                        <th class="rm-compare-table-head"></th>
                        <?php foreach($data->submissions->all_submissions as $compare_data):?>
                        <th class="rm-compare-table-head"><?php echo $compare_data->form_name;?></th>
                        <?php endforeach;?>
                    </tr>
                    <tr>
                        <th class="rm-compare-table-head"><?php _e('Created on','registrationmagic-addon');?></th>
                        <?php foreach($data->submissions->all_submissions as $compare_data):?>
                        <td><?php echo esc_html(RM_Utilities::localize_time($compare_data->created_on,'j M Y, h:i a'));?></td>
                        <?php endforeach;?>
                    </tr>
                    <tr>
                        <th class="rm-compare-table-head"><?php _e('Total views','registrationmagic-addon');?></th>
                        <?php foreach($data->submissions->all_submissions as $compare_data):?>
                        <td><?php echo esc_html($compare_data->total_view);?></td>
                        <?php endforeach;?>
                    </tr>
                    <tr>
                        <th class="rm-compare-table-head"><?php _e('Success rate','registrationmagic-addon');?></th>
                        <?php foreach($data->submissions->all_submissions as $compare_data):?>
                        <td><?php echo esc_html($compare_data->success_rate);?></td>
                        <?php endforeach;?>
                    </tr>
                    <tr>
                        <th class="rm-compare-table-head"><?php _e('Total submissions','registrationmagic-addon');?></th>
                        <?php foreach($data->submissions->all_submissions as $compare_data):?>
                        <td><?php echo esc_html($compare_data->total_submission);?></td>
                        <?php endforeach;?>
                    </tr>
                    <tr>
                        <th class="rm-compare-table-head"><?php _e('Average submission time','registrationmagic-addon');?></th>
                        <?php foreach($data->submissions->all_submissions as $compare_data):?>
                        <td><?php echo esc_html($compare_data->avg_filling_time);?></td>
                        <?php endforeach;?>
                    </tr>
                    <tr>
                        <th class="rm-compare-table-head"><?php _e('Number of fields','registrationmagic-addon');?></th>
                        <?php foreach($data->submissions->all_submissions as $compare_data):?>
                        <td><?php echo esc_html($compare_data->total_fields);?></td>
                        <?php endforeach;?>
                    </tr>
                    <tr>
                        <th class="rm-compare-table-head"><?php _e('Number of Pages','registrationmagic-addon');?></th>
                        <?php foreach($data->submissions->all_submissions as $compare_data):?>
                        <td><?php echo esc_html($compare_data->total_pages);?></td>
                        <?php endforeach;?>
                    </tr>
                    <tr>
                        <th class="rm-compare-table-head"><?php _e('Creates WP Account','registrationmagic-addon');?></th>
                        <?php foreach($data->submissions->all_submissions as $compare_data):?>
                        <td><?php echo esc_html($compare_data->registration_form);?></td>
                        <?php endforeach;?>
                    </tr>
                    <tr>
                        <th class="rm-compare-table-head"><?php _e('Total payments count','registrationmagic-addon');?></th>
                        <?php foreach($data->submissions->all_submissions as $compare_data):?>
                        <td><?php echo esc_html($compare_data->total_payments_count);?></td>
                        <?php endforeach;?>
                    </tr>
                    <tr>
                        <th class="rm-compare-table-head"><?php _e('Total payments completed','registrationmagic-addon');?></th>
                        <?php foreach($data->submissions->all_submissions as $compare_data):?>
                        <td><?php echo $compare_data->payment_completed_count .' (' .wp_kses_post((string)RM_Utilities::get_formatted_price($compare_data->payment_completed_sum)).')';?></td>
                        <?php endforeach;?>
                    </tr>
                    <tr>
                        <th class="rm-compare-table-head"><?php _e('Total payments pending','registrationmagic-addon');?></th>
                        <?php foreach($data->submissions->all_submissions as $compare_data):?>
                        <td><?php echo $compare_data->payment_pending_count .' (' .wp_kses_post((string)RM_Utilities::get_formatted_price($compare_data->payment_pending_sum)).')';?></td>
                        <?php endforeach;?>
                    </tr>
                    <tr>
                        <th class="rm-compare-table-head"><?php _e('Total payments cancelled','registrationmagic-addon');?></th>
                        <?php foreach($data->submissions->all_submissions as $compare_data):?>
                        <td><?php echo $compare_data->payment_canceled_count .' (' .wp_kses_post((string)RM_Utilities::get_formatted_price($compare_data->payment_canceled_sum)).')';?></td>
                        <?php endforeach;?>
                    </tr>
                    <tr>
                        <th class="rm-compare-table-head"><?php _e('Total payments refunded','registrationmagic-addon');?></th>
                        <?php foreach($data->submissions->all_submissions as $compare_data):?>
                        <td><?php echo $compare_data->payment_refunded_count .' (' .wp_kses_post((string)RM_Utilities::get_formatted_price($compare_data->payment_refunded_sum)).')';?></td>
                        <?php endforeach;?>
                    </tr>
                </table>
                <div class="rm-compare-charts-area rm-box-border rm-box-white-bg rm-box-mb-25">
                    <?php  if(count($data->submissions->chart_data_submissions)):?>
                    <div class="rm-dash-compare-chart-container">  
                        <div class="rm-compare-chart-title">
                            <?php echo RM_UI_Strings::get("REPORTS_COMPARE_TOTAL_SUBMISSION");?>
                        </div>
                        <canvas id="rm-total-submission-chart"></canvas>                           
                    </div>
                    <?php endif;?>
                    <?php  if(count($data->submissions->chart_data_payment_completed)):?>
                    <div class="rm-dash-compare-chart-container">   
                        <div class="rm-compare-chart-title">
                            <?php echo RM_UI_Strings::get("REPORTS_COMPARE_TOTAL_PAYMENT_COMPLETED");?>
                        </div>
                        <canvas id="rm-total-payment-completed-chart"></canvas>                           
                    </div>
                    <?php endif;?>
                    <?php  if(count($data->submissions->chart_data_payment_pending)):?>
                    <div class="rm-dash-compare-chart-container">
                        <div class="rm-compare-chart-title">
                            <?php echo RM_UI_Strings::get("REPORTS_COMPARE_TOTAL_PAYMENT_PENDING");?>
                        </div>
                        <canvas id="rm-total-payment-pending-chart"></canvas>                           
                    </div>
                    <?php endif;?>
                    <?php if(count($data->submissions->chart_data_payment_canceled)):?>
                    <div class="rm-dash-compare-chart-container">
                        <div class="rm-compare-chart-title">
                           
                            <?php echo RM_UI_Strings::get("REPORTS_COMPARE_TOTAL_PAYMENT_CANCELED");?>
                        </div>
                        <canvas id="rm-total-payment-canceled-chart"></canvas>                           
                    </div>
                    <?php endif;?>
                    <?php  if(count($data->submissions->chart_data_payment_refunded)):?>
                    <div class="rm-dash-compare-chart-container">
                        <div class="rm-compare-chart-title">
                            <?php echo RM_UI_Strings::get("REPORTS_COMPARE_TOTAL_PAYMENT_REFUNDED");?>
                        </div>
                        <canvas id="rm-total-payment-refunded-chart"></canvas>                           
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
jQuery(document).ready(function(e){
    var options = {
        cutout : '30%',
	responsive: true,
        plugins: {
            legend: {
                display:false
            },
            title: {
		display: false,
		text: '<?php echo RM_UI_Strings::get("DASHBOARD_FORMS_CHART_TITLE");?>'
		},
            }
    };
    <?php if(count($data->submissions->chart_data_submissions)):?>
        load_total_submission_chart();
    <?php endif;?>
    <?php if(count($data->submissions->chart_data_payment_completed)):?>
        load_total_payment_completed_chart();
    <?php endif;?> 
    <?php if(count($data->submissions->chart_data_payment_pending)):?>
        load_total_payment_pending_chart();
    <?php endif;?>
    <?php if(count($data->submissions->chart_data_payment_canceled)):?>
        load_total_payment_canceled_chart();
    <?php endif;?>
    <?php if(count($data->submissions->chart_data_payment_refunded)):?>
        load_total_payment_refunded_chart();
    <?php endif;?>
    <?php if($data->submissions->total_submission_found):?>
    load_total_submission_day_wise();
    <?php endif;?>

function load_total_submission_chart(){
    var canvas = document.getElementById("rm-total-submission-chart");
    var ctx = canvas.getContext('2d');
    var data = {
        labels: <?php echo json_encode($data->submissions->chart_forms_title);?>,
        datasets: [
		{
		    label: "Submit",
		    fill: true,
		    backgroundColor: ['#2271B1','#A6CEEE','#73B2E4','#358FD8','#2E5E84'],
                    borderWidth: 0,
		    data: <?php echo json_encode($data->submissions->chart_data_submissions);?>
		}
            ]
    };
    new Chart(ctx, {
        type: 'pie',
        data: data,
        options: options 
    });
}
function load_total_payment_completed_chart(){
    var canvas = document.getElementById("rm-total-payment-completed-chart");
    var ctx = canvas.getContext('2d');
    var data = {
        labels: <?php echo json_encode($data->submissions->chart_forms_title);?>,
        datasets: [
		{
		    label: "Submit",
		    fill: true,
		    backgroundColor: ['#2271B1','#A6CEEE','#73B2E4','#358FD8','#2E5E84'],
                    borderWidth: 0,
		    data: <?php echo json_encode($data->submissions->chart_data_payment_completed);?>
		}
            ]
    };
    new Chart(ctx, {
        type: 'pie',
        data: data,
        options: options 
    });
}
function load_total_payment_pending_chart(){
    var canvas = document.getElementById("rm-total-payment-pending-chart");
    var ctx = canvas.getContext('2d');
    var data = {
        labels: <?php echo json_encode($data->submissions->chart_forms_title);?>,
        datasets: [
		{
		    label: "Submit",
		    fill: true,
		    backgroundColor: ['#2271B1','#A6CEEE','#73B2E4','#358FD8','#2E5E84'],
                    borderWidth: 0,
		    data: <?php echo json_encode($data->submissions->chart_data_payment_pending);?>
		}
            ]
    };
    new Chart(ctx, {
        type: 'pie',
        data: data,
        options: options 
    });
}
function load_total_payment_refunded_chart(){
    var canvas = document.getElementById("rm-total-payment-refunded-chart");
    var ctx = canvas.getContext('2d');
    var data = {
        labels: <?php echo json_encode($data->submissions->chart_forms_title);?>,
        datasets: [
		{
		    label: "Submit",
		    fill: true,
		    backgroundColor: ['#2271B1','#A6CEEE','#73B2E4','#358FD8','#2E5E84'],
                    borderWidth: 0,
		    data: <?php echo json_encode($data->submissions->chart_data_payment_refunded);?>
		}
            ]
    };
    new Chart(ctx, {
        type: 'pie',
        data: data,
        options: options 
    });
}
function load_total_payment_canceled_chart(){
    var canvas = document.getElementById("rm-total-payment-canceled-chart");
    var ctx = canvas.getContext('2d');
    var data = {
        labels: <?php echo json_encode($data->submissions->chart_forms_title);?>,
        datasets: [
		{
		    label: "Submit",
		    fill: true,
		    backgroundColor: ['#2271B1','#A6CEEE','#73B2E4','#358FD8','#2E5E84'],
                    borderWidth: 0,
		    data: <?php echo json_encode($data->submissions->chart_data_payment_canceled);?>
		}
            ]
    };
    
    new Chart(ctx, {
        type: 'pie',
        data: data,
        options: options 
    });
}
function load_total_submission_day_wise(){
    var canvas = document.getElementById("rm-form-compare-charts");
    var ctx = canvas.getContext('2d');
    var data = {
        labels: <?php echo json_encode($data->submissions->chart_date_range);?>,
        datasets: <?php echo json_encode($data->submissions->chart_form_data);?>
	};
	var options = {
            responsive: true,
            plugins: {
                    legend: {
                            display:true,
			    position: 'bottom'
			},
                    title: {
			    display: false,
			    text: '<?php echo RM_UI_Strings::get("DASHBOARD_USERS_CHART_TITLE");?>'
			    }
                },
            scale: {
                    y:{
                        ticks: {
                            precision: 0
                            },
                       min: 0
                    }
                }
	};

		new Chart(ctx, {
		    
		    data: data,
		    options: options 
		});
        }
});
jQuery(document).ready(function(e){
    var select2var = jQuery(".js-select2").select2({
	closeOnSelect : false,
	placeholder : "Select Forms",
	allowClear: true,
	tags: true
    });
    jQuery(document.body).on("change",".js-select2",function(){
        var seldata = jQuery('.js-select2').val();
        if(Array.isArray(seldata) && (seldata.length >= 2 && seldata.length <=5)){
            jQuery('#rm_submit_btn').removeClass('disabled-btn');
            jQuery('#rm_submit_btn').prop('disabled', false);
        }
        else{
            jQuery('#rm_submit_btn').addClass('disabled-btn');
            jQuery('#rm_submit_btn').prop('disabled', true);
        }
    });
});
</script>

<style>
.disabled-btn {
    cursor: not-allowed;
    opacity: .5;
}
th.rm-compare-table-head {
    background-color: #2271B1;
    color: #fff;
    border-color: #2E5E84;
}
.select2-container.select2-container--default.select2-container--open .select2-results .select2-results__option {
    background-color: #f7fbfe;
    margin-bottom: 1px;
}

.select2-container.select2-container--default.select2-container--open .select2-results .select2-results__option.select2-results__option--highlighted{
    background-color: #2271B1;
    color: #fff;
}
</style>