<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Tax_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    function add_tax($data)
    {
        $data = escape_array($data);
        $tax_data = [
            'title' => $data['title'],
            'percentage' => $data['percentage'],
        ];

        if (isset($data['edit_tax_id']) && !empty($data['edit_tax_id'])) {
            $product_data = fetch_details('products', ['tax' => $data['edit_tax_id']], 'id,tax');

            $product_ids = array_column($product_data, 'id');
            $this->db->set($tax_data)->where('id', $data['edit_tax_id'])->update('taxes');
            if (count($product_ids) > 0) {
                recalculateTaxedPrice($product_ids);
            }
        } else {
            $this->db->insert('taxes', $tax_data);
        }
    }

    function get_tax_list($seller_id = '')
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $multipleWhere = '';
        $where = '';

        if (isset($_GET['offset']) && !empty($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']) && !empty($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']) && !empty($_GET['sort'])) {
            if ($_GET['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_GET['sort'];
            }
            if (isset($_GET['order']) && !empty($_GET['order']))
                $order = $_GET['order'];

            if (isset($_GET['search']) and $_GET['search'] != '') {
                $search = $_GET['search'];
                $multipleWhere = ['`id`' => $search, '`title`' => $search];
            }

            $count_res = $this->db->select(' COUNT(id) as `total` ');

            if (isset($multipleWhere) && !empty($multipleWhere)) {
                $count_res->group_Start();
                $count_res->or_like($multipleWhere);
                $count_res->group_End();
            }

            if (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) {
                $count_res->where('seller_id', $_GET['seller_id']);
            }


            if (isset($seller_id) && !empty($seller_id)) {
                $count_res->group_start()
                    ->where('seller_id', $seller_id)
                    ->or_where('seller_id', 0)
                    ->group_end();
            }

            $tax_count = $count_res->get('taxes')->result_array();

            foreach ($tax_count as $row) {
                $total = $row['total'];
            }

            $search_res = $this->db->select(' * ');
            if (isset($multipleWhere) && !empty($multipleWhere)) {
                $search_res->group_Start();
                $search_res->or_like($multipleWhere);
                $search_res->group_End();
            }

            if (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) {
                $search_res->where('seller_id', $_GET['seller_id']);
            }

            if (isset($seller_id) && !empty($seller_id)) {
                $search_res->group_start()
                    ->where('seller_id', $seller_id)
                    ->or_where('seller_id', 0)
                    ->group_end();
            }

            $tax_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('taxes')->result_array();


            $bulkData = array();
            $bulkData['total'] = $total;
            $rows = array();
            $tempRow = array();

            foreach ($tax_search_res as $row) {
                $row = output_escaping($row);
                if ($row['status'] == '1') {
                    if (!$this->ion_auth->is_seller()) {
                        // Create dropdown menu for operate column
                        $operate = '
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

                        // Edit Tax
                        $operate .= '<li>
                        <a class="dropdown-item" href="javascript:void(0)" 
                           data-id="' . $row['id'] . '" 
                           data-bs-toggle="offcanvas" 
                           data-bs-target="#addTax" 
                           role="button" 
                           aria-controls="addTax">
                            <i class="ti ti-pencil me-2"></i>Edit
                        </a>
                    </li>';

                        // Divider
                        $operate .= '<li><hr class="dropdown-divider"></li>';

                        // Delete Tax
                        $operate .= '<li>
                        <a class="dropdown-item text-danger" href="javascript:void(0)"
                           x-data="ajaxDelete({
                               url: base_url + \'admin/taxes/delete_tax\',
                               id: \'' . $row['id'] . '\',
                               tableSelector: \'#tax_table\',
                               confirmTitle: \'Delete Tax\',
                               confirmMessage: \'Do you really want to delete this Tax?\'
                           })"
                           @click="deleteItem">
                            <i class="ti ti-trash me-2"></i>Delete
                        </a>
                    </li>';

                        $operate .= '
                        </ul>
                    </div>';
                    } else {
                        $operate = '';
                    }
                } else {
                    $operate = '';
                }

                $tempRow['id'] = $row['id'];
                $tempRow['title'] = $row['title'];
                $tempRow['percentage'] = $row['percentage'];
                $tempRow['operate'] = $operate;
                $rows[] = $tempRow;
            }
            $bulkData['rows'] = $rows;
            print_r(json_encode($bulkData));
        }
    }
}