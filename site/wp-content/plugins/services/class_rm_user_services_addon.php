<?php

class RM_User_Services_Addon {

    // This function creates a copy of the role with a different name
    public function create_role($role_name, $display_name, $capability, $parent_service, $additional_data = null) {
        $role = get_role($capability);
        $is_paid = false;
        $amount = null;

        if ($additional_data) {
            if (isset($additional_data['is_paid']))
                $is_paid = $additional_data['is_paid'];

            if (isset($additional_data['amount']))
                $amount = $additional_data['amount'];
        }
        if (add_role($role_name, $display_name, $role->capabilities) !== null) {
            $user_role_custom_data = $parent_service->get_setting('user_role_custom_data');

            if (empty($user_role_custom_data))
                $user_role_custom_data = array($role_name => (object) array('is_paid' => $is_paid, 'amount' => $amount));
            else
                $user_role_custom_data[$role_name] = (object) array('is_paid' => $is_paid, 'amount' => $amount);

            $parent_service->set_setting('user_role_custom_data', $user_role_custom_data);

            return true;
        } else
            return false;
    }
    
    // This function creates a copy of the role with a different name
    public function edit_role($role_name, $display_name, $capability, $parent_service, $additional_data = null) {
        $role = get_role($capability);
        $is_paid = false;
        $amount = null;

        if ($additional_data) {
            if (isset($additional_data['is_paid']))
                $is_paid = $additional_data['is_paid'];

            if (isset($additional_data['amount']))
                $amount = $additional_data['amount'];
        }
        
        remove_role( $role_name );
        if (add_role($role_name, $display_name, $role->capabilities) !== null) {
            $user_role_custom_data = $parent_service->get_setting('user_role_custom_data');

            if (empty($user_role_custom_data))
                $user_role_custom_data = array($role_name => (object) array('is_paid' => $is_paid, 'amount' => $amount));
            else
                $user_role_custom_data[$role_name] = (object) array('is_paid' => $is_paid, 'amount' => $amount);

            $parent_service->set_setting('user_role_custom_data', $user_role_custom_data);

            return true;
        } else
            return false;
    }
    public function deactivate_user_by_id($user_id,$parent_service) {
        $user_model= new RM_User;
        $gopts = new RM_Options();
        $user_auto_approval = $gopts->get_value_of('user_auto_approval');
        $prov_act_acc = $gopts->get_value_of('prov_act_acc');
        $form_id = get_user_meta($user_id, 'RM_UMETA_FORM_ID', true);
        $form_auto_approval = '';
        if (!empty($form_id)) {
            $form = new RM_Forms();
            $form->load_from_db($form_id);
            $form_options = $form->get_form_options();
            $form_auto_approval = $form_options->user_auto_approval;
        }

        if ($user_auto_approval == 'verify' && $prov_act_acc == 'yes' && $form_auto_approval != 'yes') {
            $prov_acc_act_criteria = $gopts->get_value_of('prov_acc_act_criteria');
            if (!empty($prov_acc_act_criteria)) {
                if ($prov_acc_act_criteria == 'until_user_logsout') {
                    update_user_meta($user_id, 'rm_prov_activation', $prov_acc_act_criteria);
                }
            }
        }
        $curr_user = wp_get_current_user();
        if (isset($curr_user->ID))
            $curr_user_id = $curr_user->ID;
        else
            $curr_user_id = null;
        if ($curr_user_id != $user_id)
            $user_model->deactivate_user($user_id);
    }

    public function delete_roles($roles,$parent_service) {
        if (is_array($roles) && !empty($roles)) {
            $custom_role_data = $parent_service->get_setting('user_role_custom_data');
            foreach ($roles as $name) {
                $users = $parent_service->get_users_by_role($name);
                foreach ($users as $user) {
                    $user->add_role('subscriber');
                }

                remove_role($name);


                if (isset($custom_role_data[$name]))
                    unset($custom_role_data[$name]);
            }
            $parent_service->set_setting('user_role_custom_data', $custom_role_data);
        }
    }

    public function get_users($parent_service, $offset = '', $number = '', $search_str = '', $user_status = 'all', $interval = 'all', $user_ids = array(), $fields_to_return = 'all') {
        $args = array('number' => $number, 'offset' => $offset, 'include' => $user_ids, 'search' => '*' . $search_str . '*', 'fields' => $fields_to_return);
        //$args = array();

        switch ($user_status) {
            case 'active':
                $args['meta_query'] = array('relation' => 'OR',
                    array(
                        'key' => 'rm_user_status',
                        'value' => '1',
                        'compare' => '!='
                    ),
                    array(
                        'key' => 'rm_user_status',
                        'value' => '1',
                        'compare' => 'NOT EXISTS'
                ));
                break;

            case 'pending':
                $args['meta_query'] = array(array(
                        'key' => 'rm_user_status',
                        'value' => '1',
                        'compare' => '='
                ));
                break;
        }

        switch ($interval) {
            case 'today':
                $args['date_query'] = array(array('after' => date('Y-m-d', strtotime('today')), 'inclusive' => true));
                break;

            case 'week':
                $args['date_query'] = array(array('after' => date('Y-m-d', strtotime('this week')), 'inclusive' => true));
                break;

            case 'month':
                $args['date_query'] = array(array('after' => 'first day of this month', 'inclusive' => true));
                break;

            case 'year':
                $args['date_query'] = array(array('year' => date('Y'), 'inclusive' => true));
                break;
        }
        //echo "Args:<pre>", var_dump($args), "</pre>";
        $users = get_users($args);

        return $users;
    }

    public function google_login_html($parent_service) {
        $gopts = new RM_Options;
        if ($gopts->get_value_of('enable_gplus') == 'yes') {
            $client_id = $gopts->get_value_of('gplus_client_id');
                        
            return '<pre class="rm-pre-wrapper-for-script-tags"><script src="https://accounts.google.com/gsi/client" async defer></script></pre>
        <pre class="rm-pre-wrapper-for-script-tags"><script>
        function decodeJwtResponse(token) {
            var jsonPayload = token.split(".");
            return JSON.parse(atob(jsonPayload[1]));
        }
        function handleCredentialResponse(response) {
         const responsePayload = decodeJwtResponse(response.credential);
         
         handle_data(responsePayload.email,responsePayload.name,"google",response.credential);
        }
        window.onload = function () {
          google.accounts.id.initialize({
            client_id: "'.$client_id.'",
            callback: handleCredentialResponse
          });
          google.accounts.id.renderButton(
            document.getElementById("buttonDiv"),
            { theme: "outline", size: "large" }  // customization attributes
          );
          google.accounts.id.prompt(); // also display the One Tap dialog
        }
        </script></pre>
	    <div id="buttonDiv" class="rm-google-login"></div>';
        } else
            return null;
    }
    
    public function google_login_callback($parent_service, $token = null, $email = null) {
        if((is_null($token) || empty($token)) || (is_null($email) || empty($email)))
            return false;
        
        $gopts = new RM_Options;
        $client_id = $gopts->get_value_of('gplus_client_id');
        $response = wp_remote_get("https://oauth2.googleapis.com/tokeninfo?id_token=$token");
        $response = json_decode(wp_remote_retrieve_body($response));

        if(isset($response->email_verified) && $response->email_verified == 'true' && $response->email == $email && strpos($response->aud, $client_id) !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function linkedin_login_html($parent_service) {
        $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $current_url = remove_query_arg(array('code','state'),$current_url);
        $gopts = new RM_Options;
        if ($gopts->get_value_of('enable_linked') == 'yes') {
            $api_key = $gopts->get_value_of('linkedin_api_key');
            $sec_key=  $gopts->get_value_of('linkedin_secret_key');
            $code= isset($_GET['code']) ? $_GET['code'] : false;
            $state= isset($_GET['state']) ? wp_verify_nonce($_GET['state'],'rm_linked_in_login') : false;

            if(!empty($code) && !empty($state)){
                $api_key = $gopts->get_value_of('linkedin_api_key');
                $url = "https://www.linkedin.com/oauth/v2/accessToken";
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type'=>'application/x-www-form-urlencoded'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);

                $data = array(
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri'=>$current_url,
                    'client_id'=>$api_key,
                    'client_secret'=>$sec_key
                );
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                $contents = curl_exec($ch);
                $res= json_decode($contents);
                curl_close($ch);
                
                if($res && empty($res->error)){
                    $user_info= wp_remote_get('https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))&oauth2_access_token='.$res->access_token);
                    if (is_array($user_info)) {
                        $user_info = json_decode($user_info['body']);
                        if(!empty($user_info)){
                            if(is_email($user_info->elements[0]->{'handle~'}->emailAddress)){
                                //echo '<script type="text/javascript">handle_data("'.$user_info->elements[0]->{'handle~'}->emailAddress.'","","linkedin");</script>';
                                $parent_service->social_login_using_email_direct($user_info->elements[0]->{'handle~'}->emailAddress, "", "linkedin");
                            }
                        }
                    }
                }
            }
        
            return '<pre class="rm-pre-wrapper-for-script-tags">
                        <script type="text/javascript">
                        function onLinkedInLoad() {
                            window.location= "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id='.$api_key.'&redirect_uri='.$current_url.'&state='.wp_create_nonce('rm_linked_in_login').'&scope=r_emailaddress";
                            return;
                        }
                        </script>
                    </pre>
                    <div class="rm-linkedin-login rm-third-party-login"><input class="rm-third-party-login-btn" type="button" onclick="onLinkedInLoad()" value="'.__('Sign in with LinkedIn','registrationmagic-addon').'" />
                        <span><svg aria-hidden="true" data-prefix="fab" data-icon="linkedin-in" class="svg-inline--fa fa-linkedin-in fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#fff" d="M100.3 448H7.4V148.9h92.9V448zM53.8 108.1C24.1 108.1 0 83.5 0 53.8S24.1 0 53.8 0s53.8 24.1 53.8 53.8-24.1 54.3-53.8 54.3zM448 448h-92.7V302.4c0-34.7-.7-79.2-48.3-79.2-48.3 0-55.7 37.7-55.7 76.7V448h-92.8V148.9h89.1v40.8h1.3c12.4-23.5 42.7-48.3 87.9-48.3 94 0 111.3 61.9 111.3 142.3V448h-.1z"></path></svg></span>        
                    </div>';
        } else
            return null;
    }

    public function instagram_login_html($parent_service) {
        $gopts = new RM_Options;
        $link = get_permalink();
        $ext_link = RM_ADDON_BASE_URL . 'external/instagram/instagram_auth.php';
        if ($gopts->get_value_of('enable_instagram_login') == 'yes') {
            $client_id = $gopts->get_value_of('instagram_client_id');
            $client_secret = $gopts->get_value_of('instagram_client_secret');

            return "<pre class=\"rm-pre-wrapper-for-script-tags\"><script type='text/javascript'>
		var accessCode = null; //the code is required to retrieve access token
		var authenticateInstagram = function(instagramClientId, instagramRedirectUri, callback) {
			//the pop-up window size, change if you want
			var popupWidth = 700,
				popupHeight = 500,
				popupLeft = (window.screen.width - popupWidth) / 2,
				popupTop = (window.screen.height - popupHeight) / 2;
			//the url needs to point to instagram_auth.php
			var popup = window.open('" . $ext_link . "', '', 'width='+popupWidth+',height='+popupHeight+',left='+popupLeft+',top='+popupTop+'');
			popup.onload = function() {
				//open authorize url in pop-up
				if(window.location.hash.length == 0) {
					popup.open('https://api.instagram.com/oauth/authorize/?client_id='+instagramClientId+'&redirect_uri='+instagramRedirectUri+'&response_type=code&scope=user_profile,user_media', '_self');
				}
				//an interval runs to get the access code from the pop-up
				var interval = setInterval(function() {
					try {
						//check if code exists
                        popup.location.search.substr(1).split('&').forEach(function (item) {
                            tmp = item.split('=');
                            if (tmp[0] === 'code') {
                                //code found
                                clearInterval(interval);
                                accessCode = tmp[1];
                                popup.close();
                                if(callback != undefined && typeof callback == 'function') callback();
                            }
                        });
					}
					catch(evt) {
						//permission denied
					}
				}, 100);
			}
		};
		function login_callback() {
            jQuery.ajax({
                type: 'POST',
                data: {
                    client_id : '".$client_id."',
                    client_secret : '".$client_secret."',
                    grant_type : 'authorization_code',
                    redirect_uri : '".$link."',
                    code : accessCode
                },
                url: 'https://api.instagram.com/oauth/access_token',
                success: function(response){
                    handle_data('','','instagram',response.access_token);
                }
            });
		}
		function login_instagram() {
			authenticateInstagram(
			    '" . $client_id . "', //instagram client ID
			    '" . $link . "', //instagram redirect URI
			    login_callback //optional - a callback function
			);
			return false;
		}
	</script></pre>
        <div class='rm-instagram-login rm-third-party-login'><input class='rm-third-party-login-btn' type='button' onclick='login_instagram()' value='".__('Sign in with Instagram','registrationmagic-addon')."' /><span><svg aria-hidden='true' data-prefix='fab' data-icon='instagram' class='svg-inline--fa fa-instagram fa-w-14' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 448 512'><path fill='#fff' d='M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z'></path></svg></span></div>
";
        } else
            return null;
    }

    public function twitter_login_html($parent_service) {
        $twitter = $parent_service->get_twitter_keys();
        if ($twitter['enable_twitter'] == 'yes') {
            include_once(RM_ADDON_EXTERNAL_DIR . "twitter/inc/twitteroauth.php");

            $connection = new TwitterOAuth($twitter['tw_consumer_key'], $twitter['tw_consumer_secret']);
            $request_token = $connection->getRequestToken(get_permalink());
            if(empty($request_token['oauth_token']) || empty($request_token['oauth_token_secret']))
                return null;
            //Received token info from twitter
            $_SESSION['token'] = $request_token['oauth_token'];
            $_SESSION['token_secret'] = $request_token['oauth_token_secret'];

            //Any value other than 200 is failure, so continue only if http code is 200
            if ($connection->http_code == '200') {
                //redirect user to twitter
                $twitter_url = $connection->getAuthorizeURL($request_token['oauth_token']);
            }
            return "<pre class='rm-pre-wrapper-for-script-tags'><script>
       function rm_twitter_login()
       {
       window.location = '" . $twitter_url . "' 
            }
            </script></pre>
            <div class='rm-twitter-login rm-third-party-login'><input class='rm-third-party-login-btn' type='button' onclick='rm_twitter_login();' value='".__('Sign in with Twitter','registrationmagic-addon')."' /><span><svg aria-hidden='true' data-prefix='fab' data-icon='twitter' class='svg-inline--fa fa-twitter fa-w-16' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path fill='#fff' d='M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z'></path></svg></span></div>";
        } else
            return null;
    }

    public function windows_login_html($parent_service) {
        $link = get_permalink();
        $gopts = new RM_Options;
        if ($gopts->get_value_of('enable_window_login') == 'yes') {
            $client_id = $gopts->get_value_of('windows_client_id');

            return '<pre class="rm-pre-wrapper-for-script-tags"><script src="//js.live.net/v5.0/wl.js" type="text/javascript" language="javascript"></script></pre>
        <pre class="rm-pre-wrapper-for-script-tags"><script>
            WL.init({
                client_id: "' . $client_id . '",
                redirect_uri: "' . $link . '",
                scope: "wl.signin",
                response_type: "token"
            });
            /*WL.ui({
                name: "signin",
                element: "signin"
            });*/
            function moreScopes_onClick() {
    WL.login({
        scope: ["wl.signin", "wl.emails"]
    }).then(
        function (session) {
             WL.api({
                        path: "me",
                        method: "GET"
                    }).then(
                        function (response) {
                         handle_data(response.emails.account,response,"windows","");
                        },
                        function (responseFailed) {
                        }
                    );
        },
        function (sessionError) {
            document.getElementById("info").innerText = 
                "Error signing in: " + sessionError.error_description;
        }
    );
}

        </script></pre>      
<div class="rm-microsoft-login rm-third-party-login"><input class="rm-third-party-login-btn" type="button" value="'.__('Sign in with Microsoft Live','registrationmagic-addon').'" onclick="moreScopes_onClick()"/><span><svg aria-hidden="true" data-prefix="fab" data-icon="windows" class="svg-inline--fa fa-windows fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#fff" d="M0 93.7l183.6-25.3v177.4H0V93.7zm0 324.6l183.6 25.3V268.4H0v149.9zm203.8 28L448 480V268.4H203.8v177.9zm0-380.6v180.1H448V32L203.8 65.7z"></path></svg></span></div>';
        } else
            return null;
    }

    /*
      public function user_search($criterions, $type)
      {
      $user_ids = array();


      if ($type == "time")
      {
      $search_periods = array();
      foreach ($criterions as $period)
      {
      switch ($period)
      {
      case "today": $search_periods['today'] = array("start" => date('Y-m-d'), "end" => date('Y-m-d', strtotime("+1 day")));
      break;
      case "yesterday": $search_periods['yesterday'] = array("start" => date('Y-m-d', strtotime("-1 days")), "end" => date('Y-m-d'));
      break;
      case "this_week": $search_periods['this_week'] = array("start" => date('Y-m-d', strtotime("this week")), "end" => date('Y-m-d', strtotime("+1 day")));
      break;
      case "last_week": $search_periods['last_week'] = array("start" => date('Y-m-d', strtotime("last week")), "end" => date('Y-m-d', strtotime("+1 day")));
      break;
      case "this_month": $search_periods['this_month'] = array("start" => date("Y-m") . '-01', "end" => date('Y-m-d', strtotime("+1 day")));
      break;
      case "this_year": $search_periods['this_year'] = array("start" => date("Y") . '-01-01', "end" => date('Y-m-d', strtotime("+1 day")));
      break;
      }
      }
      $user_ids = RM_DBManager::sidebar_user_search($search_periods, $type);
      }

      echo 'TIme: ';
      print_r($user_ids);
      if ($type == "user_status")
      {
      $user_ids = RM_DBManager::sidebar_user_search($criterions, $type);
      echo 'Status: ';
      print_r($user_ids);
      }


      if ($type == "type")
      {
      foreach ($criterions as $el)
      {
      if ($type == "name")
      {
      $user_ids = RM_DBManager::sidebar_user_search($criterions, $type);
      echo 'name: ';
      print_r($user_ids);
      break;
      }


      if ($type == "email")
      {
      $user_ids = RM_DBManager::sidebar_user_search($criterions, $type);
      echo 'Email: ';
      print_r($user_ids);
      break;
      }
      }
      }


      die;

      return $user_ids;
      }
     */

    public function get_twitter_keys($parent_service) {
        $gopts = new RM_Options;
        $twitter = array();
        $twitter['enable_twitter'] = $gopts->get_value_of('enable_twitter_login');
        $twitter['tw_consumer_key'] = $gopts->get_value_of('tw_consumer_key');
        $twitter['tw_consumer_secret'] = $gopts->get_value_of('tw_consumer_secret');
        return $twitter;
    }

    public function get_instagram_user($parent_service) {
        $gopts = new RM_Options;
        $args = array(
            'client_id' => $gopts->get_value_of('instagram_client_id'),
            'client_secret' => $gopts->get_value_of('instagram_client_secret'),
            'code' => $_POST['accessCode'],
            'grant_type' => 'authorization_code',
            'redirect_uri' => $_POST['redirectLink']
        );
        $response = wp_remote_post('https://api.instagram.com/oauth/access_token', array('body'=>$args));
        $response = json_decode($response['body']);
        if(isset($response->user_id)){
            $user_response = wp_remote_get("https://graph.instagram.com/" . $response->user_id, array(
                'body' => array(
                    'fields' => 'id,username',
                    'access_token' => $response->access_token
                )
            ));
            $user_response = json_decode($user_response['body']);
            if(isset($user_response->username))
                echo $user_response->username;
            else
                echo '';
        } else {
            echo '';
        }
        die;
    }
    
    public function auto_login_by_id($user_id, $parent_service){
        wp_clear_auth_cookie();
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
    }

}