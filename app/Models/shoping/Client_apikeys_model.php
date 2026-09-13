<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Client_apikeys_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    public function set($data)
    {
        $this->load->helper('string');
        $secret_key = random_string('sha1', 40);

        $client_data = [
            'name' => $data['name'],
            'secret' => $secret_key
        ];
        if (isset($data['edit_client_api_keys']) && !empty($data['edit_client_api_keys'])) {
            unset($client_data['secret']);
            $this->db->set($client_data)->where('id', $data['edit_client_api_keys'])->update('client_api_keys');
        } else {
            $this->db->insert('client_api_keys', $client_data);
        }
    }

    public function get_list()
    {

        $offset = 0;
        $limit = 10;
        $sort = 'u.id';
        $order = 'ASC';
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
                $count_res->group_Start();
            $count_res->or_like($multipleWhere);
                $count_res->group_End();
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $city_count = $count_res->get('client_api_keys')->result_array();

        foreach ($city_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select('*');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_Start();
            $search_res->or_like($multipleWhere);
            $search_res->group_End();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $client_search_res = $search_res->order_by($sort, "desc")->limit($limit, $offset)->get('client_api_keys')->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        foreach ($client_search_res as $row) {
            $row = output_escaping($row);
            
  $operate = '
<div class="dropdown" style="position: relative; z-index: 9999;">
    <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button"
            data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
        <i class="ti ti-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu"
        style="z-index: 9999 !important; min-width: 160px;">
';

// Delete
$operate .= '
    <li>
        <a class="dropdown-item text-danger"
           href="javascript:void(0)"
           x-data="ajaxDelete({
               url: base_url + \'admin/client_api_keys/delete_client\',
               id: \'' . $row['id'] . '\',
               tableSelector: \'#client_api_key_table\',
               confirmTitle: \'Delete client API Key\',
               confirmMessage: \'Do you really want to delete this client API Key?\'
           })"
           @click="deleteItem">
           <i class="ti ti-trash me-2"></i>Delete
        </a>
    </li>';

// Status (Active / Inactive)
if ($row['status'] == '1') {
    $tempRow['status'] = '<span class="badge bg-success-lt">Active</span>';
    $operate .= '
        <li>
            <a class="dropdown-item text-warning update_active_status"
               href="javascript:void(0)"
               data-table="client_api_keys"
               data-id="' . $row['id'] . '"
               data-status="' . $row['status'] . '">
               <i class="ti ti-eye-off me-2"></i>Deactivate
            </a>
        </li>';
} else {
    $tempRow['status'] = '<span class="badge bg-danger-lt">Inactive</span>';
    $operate .= '
        <li>
            <a class="dropdown-item text-primary update_active_status"
               href="javascript:void(0)"
               data-table="client_api_keys"
               data-id="' . $row['id'] . '"
               data-status="' . $row['status'] . '">
               <i class="ti ti-eye me-2"></i>Activate
            </a>
        </li>';
}

$operate .= '</ul></div>';




            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['name'];
            $tempRow['secret'] = $row['secret'];
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
}
