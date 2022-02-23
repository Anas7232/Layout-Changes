<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Attribute_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    function add_attribute_set($data)
    {
        $data = escape_array($data);

        $attr_data = [
            'name' => $data['name']
        ];

        if (isset($data['edit_attribute_set'])) {
            $this->db->set($attr_data)->where('id', $data['edit_attribute_set'])->update('attribute_set');
        } else {
            $this->db->insert('attribute_set', $attr_data);
        }
    }

    function get_attribute_set_list(
        $offset = 0,
        $limit = 10,
        $sort = " id ",
        $order = 'ASC'
    ) {

        $multipleWhere = '';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['id' => $search, 'name' => $search];
        }

        $count_res = $this->db->select(' COUNT(id) as `total` ');


        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $attr_count = $count_res->get('attribute_set')->result_array();

        foreach ($attr_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' * ');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $city_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('attribute_set')->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($city_search_res as $row) {
            $row = output_escaping($row);
            $operate = ' <a href="javascript:void(0)" class="edit_btn btn btn-success btn-xs mr-1 mb-1" title="Edit" data-id="' . $row['id'] . '" data-url="admin/attribute_set/"><i class="fa fa-pen"></i></a>';
            if ($row['status'] == '1') {
                $tempRow['status'] = '<a class="badge badge-success text-white" >Active</a>';
                $operate .= '<a class="btn btn-warning btn-xs update_active_status mr-1" data-table="attribute_set" title="Deactivate" href="javascript:void(0)" data-id="' . $row['id'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-eye-slash"></i></a>';
            } else {
                $tempRow['status'] = '<a class="badge badge-danger text-white" >Inactive</a>';
                $operate .= '<a class="btn btn-primary mr-1 btn-xs update_active_status" data-table="attribute_set" href="javascript:void(0)" title="Active" data-id="' . $row['id'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-eye"></i></a>';
            }
            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['name'];
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function add_attributes($data)
    {
        $data = escape_array($data);

        $attr_data = [
            'name' => $data['name'],
            'attribute_set_id' => $data['attribute_set']
        ];

        if (isset($data['edit_attribute'])) {
            $this->db->set($attr_data)->where('id', $data['edit_attribute'])->update('attributes');
        } else {
            $this->db->insert('attributes', $attr_data);
        }
    }


    public function get_attribute_list(
        $offset = 0,
        $limit = 10,
        $sort = 'id',
        $order = 'ASC'
    ) {
        $multipleWhere = '';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "attr.id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['attr.id' => $search, 'attr_set.name' => $search, 'attr.name' => $search];
        }

        $count_res = $this->db->select(' COUNT(attr.id) as `total` ')->join('attribute_set attr_set', 'attr.attribute_set_id=attr_set.id', 'left');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $attr_count = $count_res->get('attributes attr')->result_array();

        foreach ($attr_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' attr.*,attr_set.name as attr_set_name ')->join('attribute_set attr_set', 'attr.attribute_set_id=attr_set.id', 'left');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $city_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('attributes attr')->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        foreach ($city_search_res as $row) {
            $row = output_escaping($row);
            $operate = ' <a href="javascript:void(0)" class="edit_btn btn btn-success btn-xs mr-1 mb-1" title="View" data-id="' . $row['id'] . '" data-url="admin/attribute/"><i class="fa fa-pen"></i></a>';
            if ($row['status'] == '1') {
                $tempRow['status'] = '<a class="badge badge-success text-white" >Active</a>';
                $operate .= '<a class="btn btn-warning btn-xs update_active_status mr-1" data-table="attributes" title="Deactivate" href="javascript:void(0)" data-id="' . $row['id'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-eye-slash"></i></a>';
            } else {
                $tempRow['status'] = '<a class="badge badge-danger text-white" >Inactive</a>';
                $operate .= '<a class="btn btn-primary mr-1 btn-xs update_active_status" data-table="attributes" href="javascript:void(0)" title="Active" data-id="' . $row['id'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-eye"></i></a>';
            }
            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['name'];
            $tempRow['attribute_set'] = $row['attr_set_name'];
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function add_attribute_value($data)
    {
        $data = escape_array($data);
        $attr_data = [
            'attribute_id' => $data['attributes_id'],
            'value' => $data['value'],
            'swatche_type' => $data['swatche_type'],
            'swatche_value' => $data['swatche_value'],
            'status' => '1',
        ];

        if (isset($data['edit_attribute_value'])) {
            $this->db->set($attr_data)->where('id', $data['edit_attribute_value'])->update('attribute_values');
        } else {
            $this->db->insert('attribute_values', $attr_data);
        }
    }


    public function get_attribute_values(
        $offset = 0,
        $limit = 10,
        $sort = " id ",
        $order = 'ASC'
    ) {

        $multipleWhere = '';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['attr.id' => $search, 'attr.name' => $search, 'attr_vals.value' => $search];
        }

        $count_res = $this->db->select(' COUNT(attr_vals.id) as `total` ')
            ->join('attributes attr', 'attr.id=attr_vals.attribute_id', 'left');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $attr_count = $count_res->get('attribute_values attr_vals')->result_array();

        foreach ($attr_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' attr_vals.*,attr.name as attr_name')->join('attributes attr', 'attr.id=attr_vals.attribute_id', 'left');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $city_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('attribute_values attr_vals')->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($city_search_res as $row) {
            $row = output_escaping($row);
            $operate = ' <a href="javascript:void(0)" class="edit_btn btn btn-success btn-xs mr-1 mb-1" title="View" data-id="' . $row['id'] . '" data-url="admin/attribute_value/"><i class="fa fa-pen"></i></a>';
            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['value'];
            $tempRow['attributes'] = $row['attr_name'];
            if ($row['status'] == '1') {
                $tempRow['status'] = '<a class="badge badge-success text-white" >Active</a>';
                $operate .= '<a class="btn btn-warning btn-xs update_active_status mr-1" data-table="attribute_values" title="Deactivate" href="javascript:void(0)" data-id="' . $row['id'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-eye-slash"></i></a>';
            } else {
                $tempRow['status'] = '<a class="badge badge-danger text-white" >Inactive</a>';
                $operate .= '<a class="btn btn-primary mr-1 btn-xs update_active_status" data-table="attribute_values" href="javascript:void(0)" title="Active" data-id="' . $row['id'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-eye"></i></a>';
            }

            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
}
