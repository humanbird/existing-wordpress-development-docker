<?php
if (!defined('WPINC')) {
    die('Closed');
}
wp_enqueue_script('chart_js');
wp_enqueue_script('script_rm_moment');
wp_enqueue_script('script_rm_daterangepicker');
wp_enqueue_style('style_rm_daterangepicker');
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
if(isset($data->req->filter_date)){
    $date_filter = $data->req->filter_date;
    $start_date = $data->req->start_date;
    $end_date = $data->req->end_date;
}
?>
<div class="rmagic">
    <div class="rmagic-reports">
        <div class="rm-reports-dashboard rm-box-title rm-box-mb-25">
            <?php _e('Attachments Report','registrationmagic-addon');?>
        </div>
        <div class="rm-reports-filters-box rm-box-border rm-box-white-bg rm-box-mb-25 rm-box-ptb">
            
            <div class="rm-filter-reports-form">
                <form class="rm-report-forms rm-box-wrap" action="" method="GET">
                    <input type="hidden" name="page" value="rm_reports_attachments"/>
                    <div class="rm-report-form rm-box-row rm-box-bottom">
                        <div class="rm-box-col-8">
                            <div class="rm-box-row">
                                <div class="rm-box-col-6">
                                    <div class="rm-report-filter-attr">
                                        <label><?php _e('Date', 'registrationmagic-addon'); ?></label>
                                        <div id="rm-reportrange" >
                                          <input type="text" name="rm_filter_date" value="<?php echo $date_filter; ?>" onkeydown="return false;"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="rm-box-col-6">
                                    <div class="rm-report-filter-attr">
                                        <label><?php _e('Select Form', 'registrationmagic-addon'); ?></label>
                                        <select class="" name="rm_form_id"><?php echo $forms; ?></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="rm-box-col-1"></div>
                        <div class="rm-box-col-3">

                            <div class="rm-box-btn-wrap rm-box-text-right">
                                <button type="submit" id="rm_submit_btn" class="rm-btn-primary rm-btn"><?php _e('Search', 'registrationmagic-addon'); ?></button>
                                <button type="button" id="rm_reset_btn" class="rm-btn-secondary rm-btn" onclick="window.location.href='<?php echo admin_url('?page=rm_reports_attachments'); ?>'"><?php _e('Reset', 'registrationmagic-addon'); ?></button>
                            </div>
                            

                        </div>

                    </div>
                </form>
            </div>
            
        </div>
        <div class="rm-reports-attachments">
            <?php if(!empty($data->attached_files->attachments)){?>
            <div class="rm-reports-attachments-charts rm-box-border rm-box-white-bg rm-box-mb-25 rm-box-p">
                <div class="rm-reports-section-title rm-box-subtitle"><?php echo RM_UI_Strings::get("DASHBOARD_ATTACHMENT_CHART_TITLE");?></div>
                <canvas id="attachmentCounter"></canvas>
            </div>
            <?php } ?>
            <?php if(!empty($data->attached_files->attachments)):?>
            <div class="rm-reports-submissions-preview rm-report-preview">
                <div class="rm-reports-preview-title rm-box-title rm-box-mb-25"><?php _e('Preview','registrationmagic-addon');?></div>
                <div class="rm-reports-preview-sub-title rm-box-sub-title rm-box-mb-25"><?php _e('These preview shows some of the files submitted with this form.','registrationmagic-addon');?></div>
            </div>
            <?php endif;?>
            <div class="rmagic-cards">
                <?php 
                if(!empty($data->attached_files->attachments)): $i = 1;
                    foreach ($data->attached_files->attachments as $value) {
                    ?>
                    <div id="<?php echo $value; ?>" class="rmcard">
                        <div class="cardtitle">
                            <?php echo get_the_title($value); ?>
                        </div>
                        <div class="rmattachment">
                            <?php
                            echo wp_get_attachment_link($value, 'thumbnail', false, true, false);
                            ?>
                        </div>
                        <div class="rm-form-links">
                            <div class="rm-form-row"><a href="?page=rm_attachment_download&rm_att_id=<?php echo $value; ?>"><?php echo RM_UI_Strings::get('LABEL_DOWNLOAD'); ?></a></div>
                        </div>
                    </div>
                    <?php
                    if($i >= 10){ break;}
                    $i++;
                    }
                else:?>
                    <div class="rm-reports-no-data-found rmnotice rm-box-border rm-box-mb-25"><?php _e('No data found.','registrationmagic-addon');?></div>
                <?php
                endif;
                ?>
            </div>
            <div class="rm-total-record-wrap rm-box-border rm-box-white-bg rm-box-mb-25 rm-box-ptb rm-box-wrap">
                <div class="rm-box-row rm-box-center">
                    <div class="rm-total-record-found rm-box-col-10"><?php echo 'Total records found: '. $data->attached_files->count;?></div>
                    <?php
                        $filter_date='';
                        if(isset($data->req->form_id)){
                            $form_id = $data->req->form_id;
                        }
                        if(isset($data->req->filter_date)){
                            $filter_date = $data->req->filter_date;
                        }
                    if(defined('REGMAGIC_ADDON') && $data->attached_files->count):?>
                    <div class="rm-report-attachment-zip-download rm-reports-export rm-box-col-2 rm-box-btn-wrap rm-box-text-right">
                        <form action="" method="post">
                            <input type="hidden" name="rm_slug" value="rm_reports_attachments_download_all">
                            <input type="hidden" name="rm_filter_date" value="<?php echo $filter_date; ?>">
                            <input type="hidden" name="rm_form_id" value="<?php echo $form_id; ?>">
                            <button type="submit" name="submit" class="rm-reports-export-btn rm-btn-primary rm-btn"><?php _e('Export All','registrationmagic-addon');?></button>
                        </form>
                    </div>
                    <?php endif;?>
                </div>
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
<script type="text/javascript">
    jQuery(window).load(function(e){
        <?php if(!empty($data->attached_files->chart_data)){?>
            load_attachment_counter();
        <?php }?>
    });
    function load_attachment_counter(){
        <?php 
        $attach_label = array();
        $attach_value = array();
            if(!empty($data->attached_files->chart_data)){
                foreach($data->attached_files->chart_data as $file_types => $file_counts){
                    $attach_label[] = $file_types;
                    $attach_value[] = $file_counts;
                }
            }
        ?>
        var attachCounter = document.getElementById("attachmentCounter");
	var counterData = {
		labels: <?php echo json_encode($attach_label);?>,
		    datasets: [
		        {   label: "Submission",
		            data: <?php echo json_encode($attach_value);?>,
		            backgroundColor: [
                                "#562e2e",
		                "#FF6384",
                                "#1c59b3",
		                "#ffb6c1",
		                "#84FF63",
		                "#8463FF",
                                "#e1c435"
		            ]
		        }]
		};
		var Counter = new Chart(attachCounter, {
		  type: 'doughnut',
		  data: counterData,
		  options: {
		    responsive: true,
		    plugins: {
		      legend: {
                        display: true,
		      },
		      title: {
		        display: false
		      }
		    }
		  }
		});
	}

</script>
