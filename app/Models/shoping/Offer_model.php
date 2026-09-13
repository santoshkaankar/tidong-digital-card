<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Offer_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    function add_offer($image_name)
    {
        $image_name = escape_array($image_name);
        $offer_data = [
            'type' => $image_name['offer_type'],
            'image' => $image_name['image'],
        ];
        $offer_data['link'] = '';
        if (isset($image_name['offer_type']) && $image_name['offer_type'] == 'categories' && isset($image_name['category_id']) && !empty($image_name['category_id'])) {
            $offer_data['type_id'] = $image_name['category_id'];
            $offer_data['link'] = '';
        }

        if (isset($image_name['offer_type']) && $image_name['offer_type'] == 'products' && isset($image_name['product_id']) && !empty($image_name['product_id'])) {
            $offer_data['type_id'] = $image_name['product_id'];
            $offer_data['link'] = '';
        }
        if (isset($image_name['offer_type']) && $image_name['offer_type'] == 'offer_url' && isset($image_name['link']) && !empty($image_name['link'])) {
            $offer_data['link'] = $image_name['link'];
            $offer_data['type_id'] = 0;
        }
        if (isset($image_name['edit_offer']) && !empty($image_name['edit_offer'])) {
            if (empty($image_name['image'])) {
                unset($offer_data['image']);
            }
            $this->db->set($offer_data)->where('id', $image_name['edit_offer'])->update('offers');
        } else {
            $this->db->insert('offers', $offer_data);
        }
    }

    function get_offer_list($type_filter = NULL)
    {

        $offset = 0;
        $limit = 10;
        $sort = 'o.id';
        $order = 'DESC';
        $multipleWhere = '';
        $where = [];


        if (isset($_GET['offset']) && !empty($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']) && !empty($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']) && !empty($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "o.id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']) && !empty($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['`o.id`' => $search, '`o.type`' => $search, '`o.type_id`' => $search, 'p.name' => $search, 'c.name' => $search];
        }

        // Add type filter
        if (isset($type_filter) && !empty($type_filter)) {
            $where['o.type'] = $type_filter;
        }

        $count_res = $this->db->select(' COUNT(o.id) as `total`, p.id as product_id, c.id as category_id');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_Start();
            $count_res->or_like($multipleWhere);
            $count_res->group_End();
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $count_res->join('products p', 'p.id=o.type_id', 'left')
            ->join('categories c', 'c.id=o.type_id', 'left');

        $offer_count = $count_res->get('offers o')->result_array();

        foreach ($offer_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' o.*, p.name as product_name, c.name as category_name ');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_Start();
            $search_res->or_like($multipleWhere);
            $search_res->group_End();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }
        $search_res->join('products p', 'p.id=o.type_id', 'left')
            ->join('categories c', 'c.id=o.type_id', 'left');

        $offer_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('offers o')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($offer_search_res as $row) {
            $row = output_escaping($row);
            // Create dropdown menu for operate column
            $operate = '
            <div class="dropdown">
                <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

            // Edit Offer
            $operate .= '<li>
                <a class="dropdown-item edit_offer" href="' . base_url('admin/offer/manage_offer?edit_id=' . $row['id']) . '" 
                   data-id="' . $row['id'] . '" 
                   data-type-id="' . $row['type_id'] . '" 
                   data-bs-target="#addOffer" 
                   data-bs-toggle="offcanvas">
                    <i class="ti ti-pencil me-2"></i>Edit
                </a>
            </li>';

            // Divider
            $operate .= '<li><hr class="dropdown-divider"></li>';

            // Delete Offer
            $operate .= '<li>
                <a class="dropdown-item text-danger" href="javascript:void(0)"
                   x-data="ajaxDelete({
                       url: base_url + \'admin/Offer/delete_offer\',
                       id: \'' . $row['id'] . '\',
                       tableSelector: \'#offer_table\',
                       confirmTitle: \'Delete Offer\',
                       confirmMessage: \'Do you really want to delete this offer?\'
                   })"
                   @click="deleteItem">
                    <i class="ti ti-trash me-2"></i>Delete
                </a>
            </li>';

            $operate .= '
                </ul>
            </div>';


            if (isset($row['type_id']) && !empty($row['type_id']) && $row['type'] == 'products') {
                $tempRow['name'] = !empty($row['product_name']) ? $row['product_name'] : '';
            } elseif (isset($row['type_id']) && !empty($row['type_id']) && $row['type'] == 'categories') {
                $tempRow['name'] = !empty($row['category_name']) ? $row['category_name'] : '';
            } else {
                $tempRow['name'] = '';
            }

            $tempRow['id'] = $row['id'];
          $tempRow['type'] = str_replace('_', ' ', $row['type']);
            $tempRow['type_id'] = $row['type_id'];
            $tempRow['link'] = $row['link'];
            $tempRow['image_main_url'] = $row['image'];
            if (empty($row['image']) || file_exists(FCPATH . $row['image']) == FALSE) {
                $row['image'] = base_url() . NO_IMAGE;
                $row['image_main'] = base_url() . NO_IMAGE;
            } else {
                $row['image_main'] = base_url($row['image']);
                $row['image'] = get_image_url($row['image'], 'thumb', 'sm');
            }
          $tempRow['image'] = "
<div class='d-flex justify-content-center align-items-center'>
    <a href='" . $row['image_main'] . "' data-lightbox='offer' class='image-box-100'>
        <img class='rounded' src='" . $row['image'] . "' style='width:100px; height:100px; object-fit:cover; border-radius:6px;'>
    </a>
</div>";


            // $tempRow['date_added'] = $row['date_added'];
            $date = new DateTime($row['date_added']);
            $tempRow['date_added'] = $date->format('d-M-Y');
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
}
