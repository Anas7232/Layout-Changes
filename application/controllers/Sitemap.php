<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Sitemap extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language','xml']);
        $this->data['facebookbtn'] = facebook_login();        $this->data['googlebtn'] = google_login();        
    }

    public function index()
    {
        $product_slugs = $this->db->select('p.slug')->where('p.slug != "" and p.status=1 and c.status=1 and pv.status=1')
            ->join('categories c', 'c.id = p.category_id')
            ->join('product_variants pv', 'pv.product_id = p.id')
            ->group_by('p.id')->get('products p')->result_array();
        $product_slugs = array_column($product_slugs,"slug");
        $data['product_slugs'] = $product_slugs;

        // $categories_slugs = fetch_details('slug != "" and status=1',"categories",'slug');
        $categories_slugs = $this->db->select('c.slug,c.name')->where('p.slug != "" and p.status=1 and c.status=1 and pv.status=1')
        ->join('categories c', 'c.id = p.category_id')
        ->join('product_variants pv', 'pv.product_id = p.id')
        ->group_by('c.id')->get('products p')->result_array();
        $categories_slugs = array_column($categories_slugs,"slug");
        $data['categories_slugs'] = $categories_slugs;
        $data['urls'] = array("products");
        header("Content-Type: text/xml;charset=iso-8859-1");
        $this->load->view('front-end/'.THEME.'/sitemap', $data);
    }
}
