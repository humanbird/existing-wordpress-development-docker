<?php
if (!defined('WPINC')) {
    die('Closed');
}
?>
<div class="rm-dash-head-wrap">
    <div class="rm-dash-widget-logo"><img src="<?php echo esc_url(RM_IMG_URL.'svg/rm-logo-overview.svg');?>" width="200px"><span class="rm-logo-text"></span></div>
</div>
<div class="rm-dashboard-main-container">
    
     <!---  Head Section----> 
    
     <div class="rm-dashboard-header rm-box-border rm-box-white-bg rm-box-mb-25">
    <?php if (isset($data->statics)): ?>
            <div class="rm-box-row">
                <?php foreach ($data->statics as $statics):
                    if($statics['title'] == RM_UI_Strings::get('DASHBOARD_STATICS_FORMS_TITLE')) {
                        $last_form_id = $statics['state'];
                    }
                    ?>
                    <div class="rm-box-col-3 rm-box-border-right">
                        <div class="rm-bullet-title">
                            <?php echo wp_kses_post((string)$statics['title']); ?>
                        </div>
                        <div class="rm-bullet-statics">
                            <?php echo wp_kses_post((string)$statics['state']); ?>
                        </div>
                        <div class="rm-bullet-link">
                            <?php if($statics['link'] == 'rm_form_setup') { ?>
                                <a href="#rm_add_new_form_popup" onclick="CallModalBox(this)"><?php echo wp_kses_post((string)$statics['link_label']);?> <span class="material-icons"> navigate_next </span></a>
                            <?php } else { ?>
                                <a href="<?php echo admin_url("admin.php?page=".wp_kses_post((string)$statics['link']));?>"><?php echo wp_kses_post((string)$statics['link_label']);?> <span class="material-icons"> navigate_next </span></a>
                            <?php } ?>
                        </div>
                        
                    </div>
                <?php endforeach; ?>
            </div>
       
    <?php endif; ?>  
    
    
    </div>
    
    
    <!--- Ends: Head Section---->
    
    <!--- Second Row Section---->
    
        <div class="rm-box-row rm-box-mb-25">
            
            <div class="rm-box-col-9">
                <div class="rm-box-row rm-box-h-100">
                    <div class="rm-box-col-6">
                        <div class="rm-box-border rm-box-white-bg  rm-dash-counter-chart rm-box-animated">
                            <div class="rm-dash-card-title"><?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_COUNTER')); ?></div>
                            <div class="rm-dash-counter-chart-container">                            
                                <canvas id="formCounter" width="100%" height="80%"></canvas>
                             
                            </div>
                            
                            <div class="rm-dash-demo-notice">   
                                <?php if ($data->count->demo): ?>
                                
                                    <p class="rm-dash-demo-data">
                                        <span class="material-icons"> info </span> <?php _e('Displaying demo data since there are no submissions yet.', 'registrationmagic-addon'); ?>
                                    </p>
                                <?php endif; ?></div>
                        </div>
                    </div>
                    <div class="rm-box-col-6">          
                        <div class="rm-box-border rm-box-white-bg  rm-dash-popular-chart rm-box-animated">
                              <div class="rm-dash-card-title"><?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_FORMS_CHART_TITLE')); ?></div>
                            <div class="rm-dash-popular-chart-container">                              
                                <canvas id="formChart"></canvas>                           
                            </div>
                            
                            <div class="rm-dash-demo-notice">                            
                                 <?php if (empty($data->popular_forms)): ?>
                                    <p class="rm-dash-demo-data">
                                        <span class="material-icons"> info </span> <?php _e('Displaying demo data since there are no submissions yet.', 'registrationmagic-addon'); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="rm-box-col-3">

                    <div class="rm-box-border rm-box-white-bg rm_dash_submissions rm-box-animated">
                        <div class="rm-dash-card-title"><?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_WIDGET_TABLE_CAPTION')); ?></div>
                        <?php if (!empty($data->submissions)): ?>
                        
                                    <?php foreach ($data->submissions as $submission): ?>
                                        <div class="rm-submissions-box">            
                                            <div class="rm-submissions-image"> 
                                                <?php 
                                                $user = get_user_by( 'email', (string)$submission->user_email );
                                                if($user):
                                                    if(class_exists('Profile_Magic')):
                                                        $pg_user_avatar_id = get_user_meta( $user->ID, 'pm_user_avatar', true );
                                                        if($pg_user_avatar_id):
                                                            $avatar_url = wp_get_attachment_url($pg_user_avatar_id,'thumbnail');?>
                                                            <img class="fd_img" src="<?php echo esc_url($avatar_url); ?>"><?php
                                                        else:
                                                            $avatar_url = get_avatar_url($user->ID);?>
                                                            <img class="fd_img" src="<?php echo esc_url($avatar_url); ?>">
                                                            <?php endif;
                                                    else:
                                                    $avatar_url = get_avatar_url($user->ID);?>
                                                    <img class="fd_img" src="<?php echo esc_url($avatar_url); ?>">
                                                    <?php endif;
                                                else:?>
                                                    <img class="fd_imgs" src="http://0.gravatar.com/avatar/f9085ebbab1db83bdf8394cc56cb3bb7?s=96&amp;d=mm&amp;r=g">
                                                <?php endif;?>
                                                
                                            </div>
                                            <div class="rm-form-submissions-info">
                                                
                                                <div class="rm-form-submission-email"><?php echo esc_html($submission->user_email);?></div>
                                                <div class="rm-submissions-form-title"><?php
                                                                if ($submission->name)
                                                                    echo esc_html($submission->name);
                                                                else
                                                                    echo wp_kses_post((string)RM_UI_Strings::get('LABEL_FORM_DELETED'));
                                                                ?>
                                                </div>
                                                <div class="rm-form-submissions-date">
                                                    <?php 
                                                    $sub_date = strtotime($submission->date);
                                                    $current = strtotime(date("Y-m-d"));
                                                    $datediff = $sub_date - $current;
                                                    $difference = floor($datediff/(60*60*24));
                                                    if($difference==0)
                                                    {
                                                       echo '<span class="rm-rep-sub-date">Today </span>';
                                                    }
                                                    else{
                                                       echo '<span class="rm-rep-sub-date">'.date('d M Y',strtotime($submission->date)).'</span>'; 
                                                    }
                                                    echo '<span class="rm-rep-sub-seprator">&#8226;</span>';
                                                    echo '<span class="rm-rep-sub-time">'.date('h:iA',strtotime($submission->date)).'</span>'; 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="rm-more-submissions"><a href="<?php echo admin_url("admin.php?page=rm_submission_view&rm_submission_id=".$submission->submission_id);?>"><span class="material-icons"> navigate_next </span></a></div>
                                        </div>
                                  <?php endforeach; ?>
                                <div class="rm-more-btn"><a href="<?php echo admin_url("admin.php?page=rm_submission_manage");?>"> <?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_MORE')); ?> <span class="material-icons"> navigate_next </span></a></div>
                        <?php else: ?>
                                <div class="rm-dash-demo-notice"> <div class="rm-dash-demo-data"><span class="material-icons"> info </span><?php _e('No Submissions Found.', 'registrationmagic-addon'); ?></div></div>
                     <?php endif; ?>
                    </div>

                </div>

        </div>
    
    <!--- Ends: Second Row Section---->
    
    <!--- Third Row Section---->
    
    
        <div class="rm-box-row rm-box-mb-25">
            
            <div class="rm-box-col-6">
                <div class="rm-box-border rm-box-white-bg rm-dash-users-chart">
                    <div class="rm-dash-card-title"><?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_USERS_CHART_TITLE')); ?></div>
                    <div class="rm-dash-users-chart-wrap">
                        <div class="rm-center-stats-box">
                            <div class="rm-timerange-toggle">
                                <?php _e('Show data from last', 'registrationmagic-addon'); ?>
                                <select id="rm_stat_timerange" onchange="rm_refresh_stats()">
                                    <?php
                                    $trs = array('days' => '7 Days', 'weeks' => '7 Weeks', 'months' => '12 Months', 'years' => 'All Time');
                                    foreach ($trs as $key => $tr) {
                                        echo "<option value=$key";
                                        if ($data->rm_ur == $key)
                                            echo " selected";
                                        echo '>' . ucfirst($tr) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="rm-dash-users-chart-container" >
                            <canvas id="userChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>
                     
            <div class="rm-box-col-6">
                <div class="rm-box-white-bg rm-box-border rm-dashboard-users-loggedin">
                    <div class="rm-dash-card-title"><?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_LOGIN_LOGS'));?></div>
                    <div class="rm-dash-loggin-chart-wrap">
                        <div class="rm-center-stats-box">
                            <div class="rm-timerange-toggle">
                                <?php _e('Show data from last', 'registrationmagic-addon'); ?>
                                <select id="rm_stat_timerange_login" onchange="rm_refresh_stats_login()">
                                    <?php
                                    $login_filter = array('7' => '7 Days', '30' => '30 Days', '60' => '60 Days', '90' => '90 Days');
                                    foreach ($login_filter as $key => $tr) {
                                        echo "<option value=$key";
                                        if ($data->rm_tr == $key)
                                            echo " selected";
                                        echo '>' . ucfirst($tr) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="rm-dash-users-chart-container" >
                            <canvas class="rm-box-graph" id="rm_subs_over_time_chart_div"></canvas>
                        </div>
                        
                    </div>
                    <div class="rm-more-btn"><a href="<?php echo admin_url("admin.php?page=rm_login_analytics");?>"> <?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_MORE')); ?> <span class="material-icons"> navigate_next </span></a></div>
                </div>
            </div>
        </div>
    
    <!--- Ends: Third Row Section---->
    
    
      <!--- Fourth Row Section---->
      
      <div class="rm-box-row rm-box-mb-25">
         
          <div class="rm-box-col-9">
              
              <div class="rm-box-row">
                      <div class="rm-box-col-5">
                          <div class="rm-box-border rm-box-white-bg rm-dash-submission-card-range">
                              <div class="rm-dash-card-title"><?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_COUNTER')); ?></div>
                              <div class="rm-card-range-list-wrap">
                              <ul class="rm-dash-list rm-dash-count-present">
                                  <li>
                                      <label class="rm-dash-list-today-label"><?php echo wp_kses_post((string)RM_UI_Strings::get('LABEL_TODAY')); ?></label>
                                      <span class="rm-dash-list-today-value"><?php echo esc_html($data->count->today); ?></span>
                                  </li>
                                  <li>
                                      <label class="rm-dash-list-week-label"><?php echo wp_kses_post((string)RM_UI_Strings::get('LABEL_THIS_WEEK')); ?></label>
                                      <span class="rm-dash-list-week-value"><?php echo esc_html($data->count->this_week); ?></span>
                                  </li>
                                  <li>
                                      <label class="rm-dash-list-month-label"><?php echo wp_kses_post((string)RM_UI_Strings::get('LABEL_THIS_MONTH')); ?></label>
                                      <span class="rm-dash-list-month-value"><?php echo esc_html($data->count->this_month); ?></span>
                                  </li>
                              </ul>
                              <ul class="rm-dash-list rm-dash-count-past">
                                  <li>
                                      <label class="rm-dash-list-today-label"><?php echo wp_kses_post((string)RM_UI_Strings::get('LABEL_YESTERDAY')); ?></label>
                                      <span class="rm-dash-list-today-value"><?php echo esc_html($data->count->yesterday); ?></span>
                                  </li>
                                   <li>
                                      <label class="rm-dash-list-today-label"><?php echo wp_kses_post((string)RM_UI_Strings::get('LABEL_LAST_WEEK')); ?></label>
                                      <span class="rm-dash-list-today-value"><?php echo esc_html($data->count->last_week); ?></span>
                                  </li>
                                  <li>
                                      <label class="rm-dash-list-today-label"><?php echo wp_kses_post((string)RM_UI_Strings::get('LABEL_LAST_MONTH')); ?></label>
                                      <span class="rm-dash-list-today-value"><?php echo esc_html($data->count->last_month); ?></span>
                                  </li>
                              </ul>
                              
                              </div>
                          </div>  
                      </div>
                  <div class="rm-box-col-7">
                      <div class="rm-box-border rm-box-white-bg rm-latest-forms">
                           <div class="rm-dash-card-title"><?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_LATEST_FORMS')); ?></div>
                           <?php if(!empty($data->latest_forms)):?>
				<div class="rm-latest-forms-table-card">
			
				<?php foreach($data->latest_forms as $form):?>
                                    <div class="rm-latest-forms-row">
                                        <div class="rm-latest-forms-name"><?php echo wp_kses_post((string)$form->form_name);?></div>
                                        <div class="rm-latest-forms-shortcode" ><span id="rmformshortcode<?php echo $form->form_id;?>"><?php echo "[RM_Form id='".$form->form_id."']";?></span> <span class="rm-shortcode-copy-icon material-icons" onclick="rm_copy_to_clipboard_dashboard(document.getElementById('rmformshortcode<?php echo $form->form_id;?>'), this)"> content_copy </span></div>
					<div class="rm-latest-forms-field-link"><a href="<?php echo admin_url("admin.php?page=rm_field_manage&rm_form_id=".$form->form_id);?>" ><?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_FIELDS')); ?> <span class="material-icons"> navigate_next </span></a></div>
                                        <div class="rm-latest-forms-dash-link"><a href="<?php echo admin_url("admin.php?page=rm_form_sett_manage&rm_form_id=".$form->form_id);?>" ><?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD')); ?> <span class="material-icons"> navigate_next </span></a></div>
                                    </div>
				<?php endforeach;?>
				</div>
				<?php else:?>
                                 <div class="rm-dash-demo-notice"> <div class="rm-dash-demo-data"><span class="material-icons"> info </span><?php _e('No Form Found.','registrationmagic-addon');?></div></div>
                                <?php endif;?>
                           
                           
                      </div>
                          
                  </div>
                  
              </div>
              
              
          </div>
          
          <div class="rm-box-col-3">
              <div class="rm-box-border rm-box-white-bg rm-important-shortcodes">
                    <div class="rm-dash-card-title"><?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_IMP_SHORTCODES')); ?></div>
                <div class="rm-important-shortcodes-wrap">
                    <div class="rm-latest-forms-row">
                       <div class="rm-latest-forms-name"><?php _e('Login Form', 'registrationmagic-addon'); ?></div> 
                       <div class="rm-latest-forms-shortcode" ><span id="rmformshortcode-login">[RM_Login]</span> <span class="rm-shortcode-copy-icon material-icons" onclick="rm_copy_to_clipboard_dashboard(document.getElementById('rmformshortcode-login'), this)"> content_copy </span></div>
                    </div> 
                    <div class="rm-latest-forms-row">
                       <div class="rm-latest-forms-name"><?php _e('Register Forms', 'registrationmagic-addon'); ?></div> 
                       <div class="rm-latest-forms-shortcode" ><span id="rmformshortcode-form">[RM_Form id='x']</span> <span class="rm-shortcode-copy-icon material-icons" onclick="rm_copy_to_clipboard_dashboard(document.getElementById('rmformshortcode-form'), this)"> content_copy </span></div>
                    </div>      
                    <div class="rm-latest-forms-row">
                       <div class="rm-latest-forms-name"><?php _e('User Directory', 'registrationmagic-addon'); ?></div> 
                       <div class="rm-latest-forms-shortcode" ><span id="rmformshortcode-user">[RM_Users]</span> <span class="rm-shortcode-copy-icon material-icons" onclick="rm_copy_to_clipboard_dashboard(document.getElementById('rmformshortcode-user'), this)"> content_copy </span></div>
                    </div>
                    <div class="rm-latest-forms-row">
                       <div class="rm-latest-forms-name"><?php _e('User Directory Form Specific', 'registrationmagic-addon'); ?></div> 
                       <div class="rm-latest-forms-shortcode" ><span id="rmformshortcode-user-spec">[RM_Users form_id='x']</span> <span class="rm-shortcode-copy-icon material-icons" onclick="rm_copy_to_clipboard_dashboard(document.getElementById('rmformshortcode-user-spec'), this)"> content_copy </span></div>
                    </div>        
                    <div class="rm-latest-forms-row">
                       <div class="rm-latest-forms-name"><?php _e('User Area', 'registrationmagic-addon'); ?></div> 
                       <div class="rm-latest-forms-shortcode" ><span id="rmformshortcode-submission">[RM_Front_Submissions]</span> <span class="rm-shortcode-copy-icon material-icons" onclick="rm_copy_to_clipboard_dashboard(document.getElementById('rmformshortcode-submission'), this)"> content_copy </span></div>
                    </div> 
                </div>
                <div class="rm-more-btn"><a target="__blank" href="https://registrationmagic.com/wordpress-registration-shortcodes-list/"> <?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_MORE')); ?> <span class="material-icons"> navigate_next </span></a></div>    
          </div>

      </div>
      
      </div>
      
      <!--- End Fourth Row Section---->
      
      
    
    <!--- Fifth Row Section---->
    
    <div class="rm-box-row rm-box-mb-25">
        <div class="rm-box-col-5">
                <div class="rm-box-border rm-box-white-bg rm-dashboard-users-logged-in-logs">
                        <div class="rm-dash-card-title"><?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_LOGIN_LOGS'));?></div>
                        <table class="rm-latest-login-table">
                            <tbody>
                                <?php
                                if(isset($data->login_logs)){
                                    if (!empty($data->login_logs) && (is_array($data->login_logs) || is_object($data->login_logs))){
                                        $gopt=new RM_Options;
                                        $blocked_ips=array();
                                        $blocked_ips=$gopt->get_value_of('banned_ip');

                                        foreach ($data->login_logs as $login_log){
                                            ?>
                                            <tr class="rm-login-result <?php echo ($login_log->status==1)?'rm-login-success':'rm-login-failed'; ?>">
                                                <td><div class="rm-login-user-time-log"><?php echo esc_html(RM_Utilities::localize_time($login_log->time,'j M Y, h:i a')); ?></div></td>
                                                <td>
                                                    <div class="rm-login-form-user">
                                                        <a href="#">
                                                            <?php echo get_avatar($login_log->email)?get_avatar($login_log->email):'<img src="'.RM_IMG_URL.'default_person.png">'; ?>
                                                        </a>
                                                        <?php $user = get_user_by( 'email', (string)$login_log->email ); ?>
                                                        <?php if(!empty($user)): ?>
                                                            <span class="rm-login-user-status <?php echo (RM_Utilities::is_user_online($user->ID))?'rm-login-user-online':'' ?>"><i class="fa fa-circle"></i></span>
                                                        <?php else: ?>
                                                            <span class="rm-login-user-status"><i class="fa fa-circle"></i></span>
                                                        <?php endif; ?>
                                                            <span class="rm-login-form-user-name" title="<?php echo ($user)?esc_attr($user->display_name):($login_log->social_type=='instagram'?esc_attr($login_log->username_used):esc_attr($login_log->email)); ?>"><?php echo ($user)?esc_attr($user->display_name):($login_log->social_type=='instagram'?esc_attr($login_log->username_used):esc_attr($login_log->email)); ?></span>
                                                    </div>
                                                </td>
                                                <td><div class="rm-login-method rm-login-<?php echo esc_attr(strtolower($login_log->type)) ?>"><?php echo esc_html($login_log->type) ?></div></td>
                                                <?php
                                                if($login_log->status==1){
                                                    $login_icon = '<i class="fa fa-unlock-alt"></i>';
                                                    if(strtolower($login_log->type)=='otp'){
                                                        $login_icon = '<i class="fa fa-unlock-alt"></i>';
                                                    }else if(strtolower($login_log->type)=='2fa' || strtolower($login_log->type)=='fa'){
                                                        $login_icon = '<i class="fa fa-unlock-alt"></i><i class="fa fa-unlock-alt"></i>';
                                                    }else if(strtolower($login_log->type)=='social'){
                                                        $login_icon = '<i class="fa fa-'.$login_log->social_type.'"></i>';
                                                    }
                                                }else{
                                                    $login_icon = '<i class="fa fa-lock"></i>';
                                                    if(strtolower($login_log->type)=='otp'){
                                                        $login_icon = '<i class="fa fa-lock"></i>';
                                                    }else if(strtolower($login_log->type)=='2fa' || strtolower($login_log->type)=='fa'){
                                                        $login_icon = '<i class="fa fa-lock"></i><i class="fa fa-lock"></i>';
                                                    }else if(strtolower($login_log->type)=='social'){
                                                        $login_icon = '<i class="fa fa-'.$login_log->social_type.'"></i>';
                                                    }
                                                }
                                                ?>
                                                <td><div class="rm-login-boolean-result <?php echo ($login_log->status==1)?'rm-login-true':'rm-login-false'; ?>"><i class="fa fa-<?php echo ($login_log->status==1)?'check':'times'; ?>"></i></div></td>
                                                
                                            </tr>
                                            <?php
                                        }
                                    }else{
                                        echo '<div class="rm-dash-demo-notice"><div class="rm-dash-demo-data"><span class="material-icons"> info </span>'.sprintf(__('Not enough data. Come back later to check login activity. <a target="_blank" href="%s">More Info</a>', 'registrationmagic-addon'),'https://registrationmagic.com/wordpress-user-login-plugin-guide/#login-analytics').'</div></div>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php if (!empty($data->login_logs)):?><div class="rm-more-btn"><a href="<?php echo admin_url("admin.php?page=rm_login_analytics");?>"> <?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_MORE')); ?> <span class="material-icons"> navigate_next </span></a></div><?php endif;?>
                    </div>
            
        </div> 
        <div class="rm-box-col-3">
            <div class="rm-box-border rm-box-white-bg rm-dashboard-setting">
                    <div class="rm-dash-card-title"><?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_SETTINGS'));?></div>
                    
                        <div class="rm-dashboard-setting-wrap">
                            <ul>
                                <li>
                                    <a href="admin.php?page=rm_options_general">
                                        <div class="rm-dash-setting-icon"><img class="rm-grid-icon dibfl" src="<?php echo esc_url(RM_IMG_URL); ?>general-settings.png"></div>
                                        <div class="rm-dash-setting-label"><?php _e('General Settings', 'registrationmagic-addon'); ?></div>
                                    </a>
                                </li>
                                <li>
                                    <a href="admin.php?page=rm_options_user">
                                        <div class="rm-dash-setting-icon"><img class="rm-grid-icon dibfl" src="<?php echo esc_url(RM_IMG_URL); ?>rm-user-accounts.png"></div>
                                        <div class="rm-dash-setting-label"><?php _e('User Accounts', 'registrationmagic-addon'); ?></div>
                                    </a>
                                </li>
                              
                                <li>
                                    <a href="admin.php?page=rm_options_user_privacy">
                                        <div class="rm-dash-setting-icon"><img class="rm-grid-icon dibfl" src="<?php echo esc_url(RM_IMG_URL); ?>user-privacy.png"></div>
                                        <div class="rm-dash-setting-label"><?php _e('Privacy', 'registrationmagic-addon'); ?></div>
                                    </a>
                                </li>
             
                                  <li>
                                    <a href="admin.php?page=rm_options_autoresponder">
                                        <div class="rm-dash-setting-icon"><img class="rm-grid-icon dibfl" src="<?php echo esc_url(RM_IMG_URL); ?>rm-email-notifications.png"></div>
                                        <div class="rm-dash-setting-label"><?php _e('Email Configuration', 'registrationmagic-addon'); ?></div>
                                    </a>
                                </li>
                                
                                <li>
                                    <a href="admin.php?page=rm_options_tabs">
                                        <div class="rm-dash-setting-icon"><img class="rm-grid-icon dibfl" src="<?php echo esc_url(RM_IMG_URL); ?>rm-tab-reorder-icon.png"></div>
                                        <div class="rm-dash-setting-label"><?php _e('User Area Layout', 'registrationmagic-addon'); ?></div>
                                    </a>
                                </li>
                                
                                <li>
                                    <a href="admin.php?page=rm_options_thirdparty">
                                        <div class="rm-dash-setting-icon"><img class="rm-grid-icon dibfl" src="<?php echo esc_url(RM_IMG_URL); ?>rm-third-party.png"></div>
                                        <div class="rm-dash-setting-label"><?php _e('External Integrations', 'registrationmagic-addon'); ?></div>
                                    </a>
                                </li>
                            </ul>

                        </div>
                    
                    <div class="rm-more-btn"><a href="<?php echo admin_url("admin.php?page=rm_options_manage");?>"> <?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_MORE')); ?> <span class="material-icons"> navigate_next </span></a></div>
            </div> 
            
        </div>
        
        <div class="rm-box-col-4">
            
            <div class="rm-box-border rm-box-white-bg rm-dash-export-section">
                    <div class="rm-dash-card-title"><?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_EXPORT_TITLE')); ?></div>
                    <div class="rm-dash-export-submissions">
                        <form method="post" action="" name="rm_submission_manage" id="rm_submission_manager_form">
                            <input type="hidden" name="rm_slug" value="" id="rm_slug_input_field" />
                            <select name="rm_form_id" class="rm-dash-export-field" onchange="rm_on_form_selection_change()">
                                <?php
                                foreach ($data->forms as $form) {
                                    echo '<option value="' . $form->form_id . '">' . $form->form_name . '</option>';
                                }
                                ?>
                            </select>
                            <input type="hidden" name="rm_interval" value="all" />
                        </form>
                        <form name="rm_form_manager" id="rm_form_manager_operartionbar" class="rm_static_forms" method="post" action="">
                            <input type="hidden" name="rm_slug" value="" id="rm_slug_input_field">
                            <input type="hidden" name="rm_selected"value="">
                            <input type="hidden" name="req_source" value="form_manager">
                            <input type="checkbox" name="rm_selected_forms[]" id="rm_form_export_id" value="35" style="display:none;" checked="checked">
                            <?php wp_nonce_field('rm_form_manager_template'); ?>
                        </form>
                        <div class="rm-dash-export-btns">
                            <div class="rm-dash-export-btn-wrap rm-export-premium">

                            <button class="rm-dash-export-btn" onclick="jQuery.rm_do_action('rm_submission_manager_form', 'rm_submission_export')">
                                <?php echo RM_UI_Strings::get("DASHBOARD_EXPORT_SUBMISSION_BUTTON"); ?>
                            </button>

                                <div class="rm-dash-export-info"><?php _e('Download submissions for this
                                        form in spreadsheet-friendly
                                        CSV format.', 'registrationmagic-addon'); ?></div>
                            </div>
                            <div class="rm-dash-export-btn-wrap">
                                <button class="rm-dash-export-btn" onclick="jQuery.rm_do_action('rm_form_manager_operartionbar', 'rm_form_export')">
                                 <?php echo RM_UI_Strings::get("DASHBOARD_EXPORT_FORMS_BUTTON"); ?>
                                </button>
                                <div class="rm-dash-export-info"><?php _e("Download form with it's layout and options as backup or to import inside another installation.", 'registrationmagic-addon'); ?></div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            
            
            
        </div>
    
   
    <!--- End Fifth Row Section---->
    <!--- Start sixth row  Section --->
    
    <div class="rm-box-row rm-box-mb-25">
        <div class="rm-box-col-6">
            <div class="rm-box-border rm-box-white-bg rm-dash-attachment">
                <div class="rm-dash-card-title"><?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_LATEST_ATTACHMENTS')); ?></div>
                <div class="rm-latest-attacments-table-card rm-latest-forms-table-card">
                <?php 
                    if(!empty($data->latest_attachments)):
                        foreach($data->latest_attachments as $attachment_id):?>
                            <div id="attachment-<?php echo $attachment_id; ?>" class="rm-dash-attach rm-latest-forms-row">
                                
                                <div class="rm-latest-forms-icon">
                                    <?php 
                                    $file_url = wp_get_attachment_url( $attachment_id );
                                    $filetype = wp_check_filetype( $file_url )['ext']; ?>
                                    <div class="rm-dashboard-attach-icons rm-dash-icon-<?php echo $filetype;?>">
                                   <span class="material-icons"> file_present </span>
                                    </div>
                                </div>
                                <div class="rm-dash-attach-title">
                                    <?php echo get_the_title($attachment_id); ?>
                                </div>
                                <div class="rm-latest-forms-dash-link">
                                  <a href="?page=rm_attachment_download&rm_att_id=<?php echo $attachment_id; ?>"><?php echo RM_UI_Strings::get('LABEL_DOWNLOAD'); ?><span class="material-icons">file_download</span></a>
                                </div>
                            </div><?php
                        endforeach;
                        
                    else:
                        echo '<div class="rm-dash-demo-notice"><div class="rm-dash-demo-data"><span class="material-icons"> info </span>'.sprintf(__('Not enough data.', 'registrationmagic-addon')).'</div></div>';
                        
                    endif;
                    if(!empty($data->latest_attachments)):?>
                    <div class="rm-more-btn"><a href="<?php echo admin_url("admin.php?page=rm_attachment_manage");?>"> <?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_MORE')); ?> <span class="material-icons"> navigate_next </span></a></div>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <div class="rm-box-col-6">
            <div class="rm-box-border rm-box-white-bg rm-dash-payment-report">
                <div class="rm-dash-card-title"><?php echo wp_kses_post((string)RM_UI_Strings::get('DASHBOARD_LATEST_PAYMENTS')); ?></div>
                <div class="rm-latest-payments-table-card rm-latest-forms-table-card">
                    <table class="rm-dash-payment-report-table" cellspacing="0" cellpadding="0">
                        <tbody>
                    <?php 
                    if(!empty($data->latest_payments)):
                        foreach($data->latest_payments as $payments):
                            ?>
                    <tr class="rm-dash-payment-report-row">
                        <td class="rm-latest-forms-icon"><span class="material-icons"> description </span></td>
                        <td class="rm-payments-col rm-latest-forms-name">
                            <?php echo wp_kses_post((string)$payments->form_name);?>
                        </td>
                        <td class="rm-payments-col rm-latest-forms-shortcode">
                            <?php echo wp_kses_post((string)$payments->user_email);?>
                        </td>
                        <td class="rm-payments-col rm-payment-currency">
                            <?php echo wp_kses_post((string)RM_Utilities::get_formatted_price($payments->total_amount));?>
                        </td>
                        <td class="rm-payments-col rm-latest-forms-dash-link">
                            <?php 
                            if(strtolower($payments->status) =='completed' || strtolower($payments->status) =='succeeded'):
                                echo wp_kses_post((string)'Completed');
                            else:
                                echo wp_kses_post((string)$payments->status);
                            endif;
                            ?>
                        </td>
                        </tr>
                            <?php
                        endforeach;
                    else:
                        echo ''
                        .'<tr class="rm-dash-payment-report-row">'
                        .'<td class="rm-latest-forms-icon"><span class="material-icons"> description </span></td>' 
                        . '<td class="rm-payments-col rm-latest-forms-name">Form 1</td>' 
                        . '<td class="rm-payments-col rm-latest-forms-shortcode">David.Powell@xyz.com</td>'
                        . '<td class="rm-payments-col rm-payment-currency">$199</td>'
                         . '<td class="rm-payments-col rm-latest-forms-dash-link">Completed</td>'
                        .'</tr>' 
                          
                        .'<tr class="rm-dash-payment-report-row">'
                        .'<td class="rm-latest-forms-icon"><span class="material-icons"> description </span></td>' 
                        . '<td class="rm-payments-col rm-latest-forms-name">Form 1</td>' 
                        . '<td class="rm-payments-col rm-latest-forms-shortcode">Amelia.Hurd@xyz.com</td>'
                        . '<td class="rm-payments-col rm-payment-currency">$149</td>'
                         . '<td class="rm-payments-col rm-latest-forms-dash-link">Completed</td>'
                        .'</tr>' 
                     
                        .'<tr class="rm-dash-payment-report-row">'
                        .'<td class="rm-latest-forms-icon"><span class="material-icons"> description </span></td>' 
                        . '<td class="rm-payments-col rm-latest-forms-name">Form 1</td>' 
                        . '<td class="rm-payments-col rm-latest-forms-shortcode">Frances.Church@xyz.com</td>'
                        . '<td class="rm-payments-col rm-payment-currency">$299</td>'
                         . '<td class="rm-payments-col rm-latest-forms-dash-link">Completed</td>'
                        .'</tr>' 
                            
                        .'<tr class="rm-dash-payment-report-row">'
                        .'<td class="rm-latest-forms-icon"><span class="material-icons"> description </span></td>' 
                        . '<td class="rm-payments-col rm-latest-forms-name">Form 1</td>' 
                        . '<td class="rm-payments-col rm-latest-forms-shortcode">Mario.Goins@xyz.com</td>'
                        . '<td class="rm-payments-col rm-payment-currency">$100</td>'
                         . '<td class="rm-payments-col rm-latest-forms-dash-link">Completed</td>'
                        .'</tr>'
                             
                        . '<div class="rm-dash-demo-notice"><div class="rm-dash-demo-data"><span class="material-icons"> info </span>'.sprintf(__('Displaying demo data since there are no payments yet.', 'registrationmagic-addon')).'</div></div>';
                    
                    endif;
                    ?></tbody>
                   </table> 
                </div>
            </div>
        </div>
    </div>
    <!-- End Seventh Row Section --->
    <!-- Add New Form popup -->
    <div class="rmagic rm-hide-version-number">
    <div id="rm_add_new_form_popup" class="rm-modal-view" style="display:none;">
        <div class="rm-modal-overlay rm-form-popup-overlay-fade-in"></div>
        <div class="rm_add_new_form_wrap rm-create-new-from rm-form-popup-out">
            <div class="rm-box-row rm-box-center rm-box-secondary-bg">
            <div class="rm-box-col-12 rm-box-white-bg rm-form-box">                       
                    <div class="rm-modal-titlebar rm-new-form-popup-header">
                        <div class="rm-modal-title">
                            <?php _e('Add New Form', 'registrationmagic-addon'); ?>
                        </div>
                        <div class="rm-modal-subtitle">
                            <a href="<?php echo admin_url("admin.php?page=rm_form_setup");?>" title="<?php _e('Form Template', 'registrationmagic-addon'); ?>">
                              <?php _e('Or choose from a template.', 'registrationmagic-addon'); ?>
                            </a>
                            
                             <span class="rm-modal-close material-icons">close</span>
                        </div>
                            
                    </div>
                    <div class="rm-modal-container">
                        <?php require RM_ADMIN_DIR . 'views/template_rm_new_form_exerpt.php'; ?>
                    </div>
                </div> 
                
    
            </div>
        </div>
    </div>
    </div>
    <!-- End: Add New Form popup -->
</div>
<pre class="rm-pre-wrapper-for-script-tags">
    <script type="text/javascript">
    function CallModalBox(ele) {
        jQuery(jQuery(ele).attr('href')).toggle().find("input[type='text']").focus();
        if(jQuery(ele).attr('href')=='#rm_add_new_form_popup' || jQuery(ele).attr('href')=='#rm_add_new_form_popup_template'){
            jQuery('.rmagic .rm_add_new_form_wrap.rm-create-new-from').removeClass('rm-form-popup-out');
            jQuery('.rmagic .rm_add_new_form_wrap.rm-create-new-from').addClass('rm-form-popup-in');
            
            jQuery('.rm-modal-overlay').removeClass('rm-form-popup-overlay-fade-out');
            jQuery('.rm-modal-overlay').addClass('rm-form-popup-overlay-fade-in');
        }
    }
    </script>
</pre>