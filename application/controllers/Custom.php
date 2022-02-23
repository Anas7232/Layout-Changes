<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Custom extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'language', 'timezone_helper']);
        $this->load->model(['cart_model', 'category_model', 'rating_model']);
        $this->load->library(['pagination']);
        $this->data['settings'] = get_settings('system_settings', true);
        $this->data['web_settings'] = get_settings('web_settings', true);
        $this->data['is_logged_in'] = ($this->ion_auth->logged_in()) ? 1 : 0;
        $this->data['user'] = ($this->ion_auth->logged_in()) ? $this->ion_auth->user()->row() : array();
        $this->response['csrfName'] = $this->security->get_csrf_token_name();
        $this->response['csrfHash'] = $this->security->get_csrf_hash();                $this->data['facebookbtn'] = facebook_login();        $this->data['googlebtn'] = google_login();        
    }	function getAllSubCategories($x=null){			$this->load->helper ( 'JsonHelper' );			$category_id = get_category_id_by_slug($x);		$arrRes['category'] = $this->category_model->fetchAllSubCatgories($category_id);				echo json_encode($arrRes);	}	function getAllProductsSubCategories(){			$this->load->helper ( 'JsonHelper' );			$data = $_REQUEST['head'];				$category_id = $data['subCatId'];		$sort_by = $data['sort'];				$attribute_values = isset($data['filterVal']) ? $data['filterVal'] : '' ;		$attribute_names = isset($data['filterName']) ? array_unique($data['filterName']) : '' ;				if($attribute_values == ''){			$attribute_names = '' ;		}		$filter = null;		$user_id = null;				$sort = '';				$order = '';				$filter['search'] =  null;		$filter['attribute_value_ids'] = null;				if($attribute_values != ''){			$filter['attribute_value_ids'] = get_attribute_ids_by_value($attribute_values, $attribute_names);						$filter['attribute_value_ids'] = implode(',', $filter['attribute_value_ids']);		}						if ($sort_by == "top-rated") {					$filter['product_type'] = "top_rated_product_including_all_products";				} elseif ($sort_by == "date-desc") {					$sort = 'pv.date_added';					$order = 'desc';				} elseif ($sort_by == "date-asc") {					$sort = 'pv.date_added';					$order = 'asc';				} elseif ($sort_by == "price-asc") {					$sort = 'price';					$order = 'asc';				} elseif ($sort_by == "price-desc") {					$sort = 'price';					$order = 'desc';				}				if ($this->data['is_logged_in']) {					$user_id = $this->data['user']->id;				}		$total_rows = fetch_product($user_id, $filter, null, $category_id, null, null, $sort, $order);				$arrRes['products'] = $total_rows;		$arrRes['settings'] = get_settings('system_settings', true);		echo json_encode($arrRes);	}}