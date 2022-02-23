<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Verify extends  CI_Controller{
    
public function __construct()

    {

        parent::__construct();

        $this->load->database();

        $this->load->helper(['url', 'language', 'timezone_helper']);

        $this->load->model(['address_model', 'category_model', 'cart_model', 'faq_model']);

        $this->data['is_logged_in'] = ($this->ion_auth->logged_in()) ? 1 : 0;

        $this->data['settings'] = get_settings('system_settings', true);

        $this->data['web_settings'] = get_settings('web_settings', true);

        $this->response['csrfName'] = $this->security->get_csrf_token_name();

        $this->response['csrfHash'] = $this->security->get_csrf_hash();
        
    }
    

    public function google(){
    	
    	$this->load->model ( 'User_model', 'user');
    	$this->load->model ( 'Ion_auth_model', 'ion_authh' );
    	 
    	
    	if( ! $this->data['is_logged_in'] ){
    	
	    	include_once APPPATH . "third_party/google-api/vendor/autoload.php";
	    	
	    	$google_client = new Google_Client();
	    	
	    	$google_client->setClientId('932721763814-h6n4171pe68dehlqi00mjq5ho5vebqa3.apps.googleusercontent.com'); //Define your ClientID
	    	
	    	$google_client->setClientSecret('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'); 
	    	
	    	$google_client->setRedirectUri('https://eshop.samisalon.com/verify/google');
	    	
	    	$google_client->addScope('email');
	    	
	    	$google_client->addScope('profile');
	    	
	    	if(isset($_GET["code"]))
	    	{
	    		$token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);
	    		
	    		if(!isset($token["error"]))
	    		{
	    			$google_client->setAccessToken($token['access_token']);
	    	
	    			$this->session->set_userdata('access_token', $token['access_token']);
	    	
	    			$google_service = new Google_Service_Oauth2($google_client);
	    	
	    			$data = $google_service->userinfo->get();
	    			
	    			$this->session->unset_userdata('access_token');
	    			
	    			$current_datetime = date('Y-m-d H:i:s');
	    	
	    			$userDetails = $this->user->getSpecificUserDetail($data['email'], 2);
	    			
	    			
	    			if(isset($userDetails) && !empty($userDetails)) {
	    			
	    				
	    				$this->trigger_events('pre_set_session');
	    					
	    				$session_data = [
	    						 
	    						'email'                    => $userDetails['email'],
	    						 
	    						'user_id'                  => $userDetails['id'],
	    						 
	    						'old_last_login'           => $userDetails['last_login'],
	    						 
	    						'last_check'               => time(),
	    						 
	    						'ion_auth_session_hash'    => $this->config->item('session_hash', 'ion_auth'),
	    						
	    						'identity'                 => $userDetails['mobile'],
	    							
	    						'mobile'                   => $userDetails['mobile'],
	    						 
	    				];
	    				
	    				$this->session->set_userdata($session_data);
	    			
	    				$this->trigger_events('post_set_session');
	    				 
	    				redirect('home', 'refresh');
	    				
	    				$this->session->unset_userdata('access_token');
	    				
	    			} else {
	    
	    							
	    				$firstName = $data['given_name'];
	    				$lastName = $data['family_name'];
	    				$ipaddress= $_SERVER['REMOTE_ADDR'];
	    				$userName = $firstName.' '.$lastName;
	    				
	    				$userId = $this->user->addUserDetail($ipaddress, $userName, '', $data['email'], $data['email'] );
	    				
	    				$userDetails = $this->user->getSpecificUserDetail($userId, 1);
	    				
	    				$this->trigger_events('pre_set_session');
	    				
	    				$session_data = [
	    				
	    						'email'                    => $userDetails['email'],
	    				
	    						'user_id'                  => $userDetails['id'],
	    						
	    						'old_last_login'           => $userDetails['last_login'],
	    				
	    						'last_check'               => time(),
	    				
	    						'ion_auth_session_hash'    => $this->config->item('session_hash', 'ion_auth'),
	    						
	    						'identity'                    => $userDetails['mobile'],
	    						
	    						'mobile'                    => $userDetails['mobile'],
	    				
	    				];
	    				
	    				$this->session->set_userdata($session_data);
	    				
	    				$this->trigger_events('post_set_session');
	    				
	    				redirect('home', 'refresh');
	    				
	    				$this->session->unset_userdata('access_token');
	    				
	    			}
	    	
	    		}
	    	}
    		 
    	}else{
    		 
    		redirect('home', 'refresh');
    	}
    	
    	
    	
    }
    
    public function facebook(){
    	 
    	$this->load->model('User_model', 'user');
    	
    	if(! $this->data['is_logged_in'] ){
    	
	    	include_once APPPATH . "third_party/facebook-api/fb-config.php";
	    	
	    	$facebook_output = '';
	    	
	    	if(isset($_GET['code']))
	    	{
    			$access_token = $helper->getAccessToken();
	    		
    			$_SESSION['access_token'] = $access_token;
	    	
    			$fb->setDefaultAccessToken($_SESSION['access_token']);
	    	
	   	
	    		$graph_response = $fb->get("/me?fields=name,email", $access_token);
	    	
	    		$data = $graph_response->getGraphUser();
	    		
	    		$current_datetime = date('Y-m-d H:i:s');
	    		 
	    		$userDetails = $this->user->getSpecificUserDetail($data['email'], 2);
	    		
	    		if(isset($userDetails) && !empty($userDetails)) {
	    			 
	    			$this->trigger_events('pre_set_session');
	    			
	    			$session_data = [
	    			
	    					'email'                    => $userDetails['email'],
	    			
	    					'user_id'                  => $userDetails['id'], 
	    			
	    					'old_last_login'           => $userDetails['last_login'],
	    			
	    					'last_check'               => time(),
	    			
	    					'ion_auth_session_hash'    => $this->config->item('session_hash', 'ion_auth'),
	    					
	    					'identity'                    => $userDetails['mobile'],
	    						
	    					'mobile'                    => $userDetails['mobile'],
	    			
	    					];
	    			
	    			$this->session->set_userdata($session_data);
	    			
	    			$this->trigger_events('post_set_session');
	    			
	    			redirect('home', 'refresh');
	    			
	    		} else {
	    		
	    			$ecplode = explode(' ', $data['name']);
	    			$firstName = isset($ecplode[0]) ? $ecplode[0] : '';
	    			$lastName = isset($ecplode[1]) ? $ecplode[1] : '';
	    			
	    			$ipaddress= $_SERVER['REMOTE_ADDR'];
	    			$userName = $firstName.' '.$lastName;
	    			$userId = $this->user->addUserDetail($ipaddress, $userName, '', $data['email'], $data['email'] );
	    			
	    			$userDetails = $this->user->getSpecificUserDetail($userId, 1);
	    			
	    			$this->trigger_events('pre_set_session');
	    			
	    			$session_data = [
	    			
	    					'email'                    => $userDetails['email'],
	    			
	    					'user_id'                  => $userDetails['id'],
	    			
	    					'old_last_login'           => $userDetails['last_login'],
	    			
	    					'last_check'               => time(),
	    			
	    					'ion_auth_session_hash'    => $this->config->item('session_hash', 'ion_auth'),
	    					
	    					'identity'                    => $userDetails['mobile'],
	    						
	    					'mobile'                    => $userDetails['mobile'],
	    			
	    			];
	    			
	    			$this->session->set_userdata($session_data);
	    			
	    			$this->trigger_events('post_set_session');
	    			
	    			redirect('home', 'refresh');
	    		
	    		}
	    	
	    	}
    		 
    	}else{
    		 
    		 redirect('home', 'refresh');
    	}
    	
    	
    	
    	
    }
    
    public function loadSuccessView()
    
    {
    
    	$this->data['main_page'] = 'home';
    
    	$this->data['title'] = 'Home | ' . $this->data['web_settings']['site_title'];
    
    	$this->data['keywords'] = 'Home, ' . $this->data['web_settings']['meta_keywords'];
    
    	$this->data['description'] = 'Home | ' . $this->data['web_settings']['meta_description'];
    
    
    
    	$limit =  12;
    
    	$offset =  0;
    
    	$sort = 'row_order';
    
    	$order =  'ASC';
    
    	$has_child_or_item = 'false';
    
    	$filters = [];
    
    	
    	$categories = $this->category_model->get_categories('', $limit, $offset, $sort, $order, 'false');
    
    
    
    	
    
    
    	$sections = $this->db->limit($limit, $offset)->order_by('row_order')->get('sections')->result_array();
    
    	$user_id = NULL;
    
    	if ($this->data['is_logged_in']) {
    
    		$user_id = $this->data['user']->id;
    
    	}
    
    	$filters['show_only_active_products'] = true;
    
    	if (!empty($sections)) {
    
    		for ($i = 0; $i < count($sections); $i++) {
    
    			$product_ids = explode(',', $sections[$i]['product_ids']);
    
    			$product_ids = array_filter($product_ids);
    
    			$product_categories = (isset($sections[$i]['categories']) && !empty($sections[$i]['categories']) && $sections[$i]['categories'] != NULL) ? explode(',', $sections[$i]['categories']) : null;
    
    			if (isset($sections[$i]['product_type']) && !empty($sections[$i]['product_type'])) {
    
    				$filters['product_type'] = (isset($sections[$i]['product_type'])) ? $sections[$i]['product_type'] : null;
    
    			}
    
    
    
    			if ($sections[$i]['style'] == "default") {
    
    				$limit = 10;
    
    			} elseif ($sections[$i]['style'] == "style_1" || $sections[$i]['style'] == "style_2") {
    
    				$limit = 7;
    
    			} elseif ($sections[$i]['style'] == "style_3" || $sections[$i]['style'] == "style_4") {
    
    				$limit = 5;
    
    			} else {
    
    				$limit = null;
    
    			}
    
    			$products = fetch_product($user_id, (isset($filters)) ? $filters : null, (isset($product_ids)) ? $product_ids : null, $product_categories, $limit, null, null, null);
    
    			$sections[$i]['title'] =  output_escaping($sections[$i]['title']);
    
    			$sections[$i]['slug'] =  url_title($sections[$i]['title'], 'dash', true);
    
    			$sections[$i]['short_description'] =  output_escaping($sections[$i]['short_description']);
    
    			$sections[$i]['filters'] = (isset($products['filters'])) ? $products['filters'] : [];
    
    			$sections[$i]['product_details'] =  $products['product'];
    
    			unset($sections[$i]['product_details'][0]['total']);
    
    			$sections[$i]['product_details'] = $products['product'];
    
    			unset($product_details);
    
    		}
    
    	}
    
    	$this->data['sections'] = $sections;
    
    	$this->data['categories'] = $categories;
    
    	$this->data['username'] = $this->session->userdata('username');
    
    	$this->data['sliders'] = get_sliders();
    
    	$this->load->view('front-end/' . THEME . '/template', $this->data);
    
    }
    
    public function trigger_events($events)
    
    {
    
    	if (is_array($events) && !empty($events))
    
    	{
    
    		foreach ($events as $event)
    
    		{
    
    			$this->trigger_events($event);
    
    		}
    
    	}
    
    	else
    
    	{
    
    		if (isset($this->_ion_hooks->$events) && !empty($this->_ion_hooks->$events))
    
    		{
    
    			foreach ($this->_ion_hooks->$events as $name => $hook)
    
    			{
    
    				$this->_call_hook($events, $name);
    
    			}
    
    		}
    
    	}
    
    }
}
