<?php

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
                <?php _e('Form Submissions Report','registrationmagic-addon');?>
            </div>
            <div class="rm-reports-filters-box rm-box-border rm-box-white-bg rm-box-mb-25 rm-box-ptb">
                <div class="rm-filter-reports-form">
                    <form class="rm-report-forms rm-box-wrap" action="" method="GET">
                        <input type="hidden" name="page" value="rm_reports_submissions"/>
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
                                            <label><?php _e('Email', 'registrationmagic-addon'); ?></label>
                                            <input type="email" name="rm_email" value="<?php echo $data->req->email; ?>" placeholder="Enter Email for specific user">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="rm-box-col-3">
                                <div class="rm-box-btn-wrap rm-box-text-right">                             
                                        <button type="submit" id="rm_submit_btn" class="rm-btn-primary rm-btn"><?php _e('Search', 'registrationmagic-addon'); ?></button>
                                        <button type="button" id="rm_reset_btn" class="rm-btn-secondary rm-btn" onclick="window.location.href='<?php echo admin_url('?page=rm_reports_submissions'); ?>'"><?php _e('Reset', 'registrationmagic-addon'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="rm-reports-submission">
                
                <?php if(!empty($data->submissions)):?>
                <!-- Canvas Start-->
                <div class="rm-reports-submission-charts rm-box-border rm-box-white-bg rm-box-mb-25 rm-box-p">
                    <canvas id="rmSubChart"></canvas>
                </div>
                <!-- Canvas End-->
                
                <!-- Table Start-->
                <div class="rm-reports-submissions-preview rm-report-preview">
                    <div class="rm-reports-preview-title rm-box-title rm-box-mb-25"><?php _e('Preview','registrationmagic-addon');?></div>
                    <div class="rm-reports-preview-sub-title rm-box-sub-title rm-box-mb-25"><?php _e('This preview only displays initial few rows of the generated report. The downloaded file will have complete report data.','registrationmagic-addon');?></div>
                    <table class="rm-report-submission-tables rm-reports-table">
                        <thead
                        <tr>
                            <th><?php _e('Date','registrationmagic-addon'); ?></th>
                            <th><?php _e('Email','registrationmagic-addon'); ?></th>
                            <th><?php _e('Form ID','registrationmagic-addon'); ?></th>
                            <th><?php _e('View','registrationmagic-addon'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data->submissions as $submission):?>
                        <tr>
                            <td><?php echo date('d M, Y',strtotime($submission->submitted_on));?></td>
                            <td><?php echo $submission->user_email;?></td>
                            <td><?php echo $submission->form_id;?></td>
                            <td><a target="__blank" href="<?php echo admin_url('?page=rm_submission_view&rm_submission_id='.$submission->submission_id);?>"><span class="material-icons"> open_in_new </span></a></td>
                        </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
                <?php else:?>
                    <div class="rmagic-cards"><div class="rm-reports-no-data-found rmnotice rm-box-border rm-box-mb-25"><?php _e('No data found.','registrationmagic-addon');?></div></div>
                <?php
                endif;?>
                <div class="rm-total-record-wrap rm-box-border rm-box-white-bg rm-box-mb-25 rm-box-ptb rm-box-wrap">
                    <div class="rm-box-row rm-box-center">
                    <div class="rm-total-record-found rm-box-col-10"><?php echo __('Total records found: ','registrationmagic-addon').$data->submissions_count;?></div>
                    <?php
                        $form_id='all';
                        $filter_date='';
                        if(isset($data->req->form_id) && $data->req->form_id != 'all'){
                            $form_id = $data->req->form_id;
                        }
                        if(isset($data->req->filter_date)){
                            $filter_date = $data->req->filter_date;
                        }
                        if($form_id != 'all' && defined('REGMAGIC_ADDON') && $data->submissions_count):?>
                        <div class="rm-report-submission-export rm-reports-export rm-box-col-2 rm-box-btn-wrap rm-box-text-right">
                            <form action="" method="post">
                                <input type="hidden" name="rm_slug" value="rm_reports_submission_export">
                                <input type="hidden" name="rm_filter_date" value="<?php echo $filter_date; ?>">
                                <input type="hidden" name="rm_form_id" value="<?php echo $form_id; ?>">
                                <button type="submit" name="submit" class="rm-reports-export-btn rm-btn-primary rm-btn"><?php _e('Export All','registrationmagic-addon'); ?></button>
                            </form>
                        </div>
                        <?php endif;?>
                        <?php if($form_id == 'all' && $data->submissions_count):?>
                        <div class="rm-report-submission-export rm-reports-export rm-box-col-2 rm-box-btn-wrap rm-box-text-right">
                            <button type="button" class="rm-reports-export-btn rm-btn-primary rm-btn rm-locked" style="opacity:.5;"><?php _e('Export All','custom-registration-form-builder-with-submission-manage'); ?></button>
                        </div>
                        <?php endif;?>
                    </div>
                </div>
                
            </div>
        </div>
    </div>