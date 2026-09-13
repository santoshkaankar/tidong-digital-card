<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Return_reason_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    public function add_return_reason_details($data)
    {

        $data = escape_array($data);

        $return_reasons = [
            'return_reason' => $data['return_reason'],
            'message' => (isset($data['message']) && !empty($data['message'])) ? $data['message'] : '',
            'image' => $data['image'],
        ];
        if (isset($data['edit_return_reason_id']) && !empty($data['edit_return_reason_id'])) {
            $this->db->set($return_reasons)->where('id', $data['edit_return_reason_id'])->update('return_reasons');
        } else {
            $this->db->insert('return_reasons', $return_reasons);
        }
    }

    public function get_return_reason_list($offset = 0, $limit = 10, $sort = 'id', $order = 'DESC')
    {
        $multipleWhere = '';

        if (isset($_GET['offset']) && !empty($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']) && !empty($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']) && !empty($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']) && !empty($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['rr.`id`' => $search, 'rr.`return_reason`' => $search, 'rr.`message`' => $search];
        }

        $count_res = $this->db->select(' COUNT(rr.id) as `total` ');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_Start();
            $count_res->or_where($multipleWhere);
            $count_res->group_End();
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $sc_count = $count_res->get('return_reasons rr')->result_array();

        foreach ($sc_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' rr.`id` as id , rr.`return_reason`, rr.`image` , rr.`message` ');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_Start();
            $search_res->or_like($multipleWhere);
            $search_res->group_End();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $sc_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('return_reasons rr')->result_array();

        $bulkData = array();
        $bulkData['total'] = count($sc_search_res);
        $rows = array();
        $tempRow = array();

        foreach ($sc_search_res as $row) {
            $row = output_escaping($row);

            // Create dropdown menu for operate column
            $operate = '
            <div class="dropdown">
                <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

            // Edit Return Reason
            $operate .= '<li>
                <a class="dropdown-item edit_return_reason" href="javascript:void(0)" 
                   data-id="' . $row['id'] . '" 
                   data-bs-target="#addReturnReason" 
                   data-bs-toggle="offcanvas">
                    <i class="ti ti-pencil me-2"></i>Edit
                </a>
            </li>';

            // Divider
            $operate .= '<li><hr class="dropdown-divider"></li>';

            // Delete Return Reason
            $operate .= '<li>
                <a class="dropdown-item text-danger" href="javascript:void(0)"
                   x-data="ajaxDelete({
                       url: base_url + \'admin/return_reasons/delete_return_reason\',
                       id: \'' . $row['id'] . '\',
                       tableSelector: \'#return_reason_table\',
                       confirmTitle: \'Delete Return Reason\',
                       confirmMessage: \'Do you really want to delete this return reason?\'
                   })"
                   @click="deleteItem">
                    <i class="ti ti-trash me-2"></i>Delete
                </a>
            </li>';

            $operate .= '
                </ul>
            </div>';

            $tempRow['id'] = $row['id'];
            $tempRow['return_reason'] = $row['return_reason'];
            // $tempRow['message'] = $row['message'];
            $tempRow['image_url'] = (isset($row['image']) && !empty($row['image'])) ? $row['image'] : base_url() . NO_IMAGE;
            $row['image'] = (isset($row['image']) && !empty($row['image'])) ? base_url() . $row['image'] : base_url() . NO_IMAGE;
            $tempRow['image'] = '<div class="image-box-100 h-100"><a href="' . htmlspecialchars($row['image'], ENT_QUOTES) . '" data-lightbox="return-reason"><img src="' . htmlspecialchars($row['image'], ENT_QUOTES) . '" class="rounded"></a></div>';
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
}
