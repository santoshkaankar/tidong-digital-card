<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Slider_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    function add_slider($data)
    {
        $data = escape_array($data);

        $slider_data = [
            'type' => $data['slider_type'],
            'image' => $data['image'],
        ];
        $slider_data['link'] = '';
        if (isset($data['slider_type']) && $data['slider_type'] == 'categories' && isset($data['category_id']) && !empty($data['category_id'])) {
            $slider_data['type_id'] = $data['category_id'];
            $slider_data['link'] = '';
        }
        if (isset($data['slider_type']) && ($data['slider_type'] == 'sliderurl' || $data['slider_type'] == 'slider url') && isset($data['link']) && !empty($data['link'])) {
            $slider_data['link'] = $data['link'];
            $slider_data['type_id'] = 0;
            // Always save as sliderurl in database
            $slider_data['type'] = 'sliderurl';
        }

        if (isset($data['slider_type']) && $data['slider_type'] == 'products' && isset($data['product_id']) && !empty($data['product_id'])) {
            $slider_data['type_id'] = $data['product_id'];
            $slider_data['link'] = '';
        }

        if (isset($data['edit_slider']) && !empty($data['edit_slider'])) {
            if (empty($data['image'])) {
                unset($slider_data['image']);
            }

            $this->db->set($slider_data)->where('id', $data['edit_slider'])->update('sliders');
        } else {
            $this->db->insert('sliders', $slider_data);
        }
    }

    function get_slider_list($limit = 10, $offset = 0, $sort = 'id', $order = 'DESC', $search = '', $type_filter = NULL)
    {
        $multipleWhere = '';
        $where = [];
        
        // Convert legacy slider_url to sliderurl if needed
        if (isset($type_filter) && $type_filter === 'slider url') {
            $type_filter = 'sliderurl';
        }

        // Search handling
        if (!empty($search)) {
            $multipleWhere = ['sliders.`id`' => $search, 'sliders.`type`' => $search, 'sliders.`type_id`' => $search];
        }

        // Add type filter
        if (isset($type_filter) && !empty($type_filter)) {
            $where['sliders.type'] = $type_filter;
        }

        // Count query
        $count_res = $this->db->select('COUNT(DISTINCT sliders.id) as `total`');

        // Add joins for name search
        if (!empty($search)) {
            $this->db->join('products', 'products.id = sliders.type_id AND sliders.type = "products"', 'left');
            $this->db->join('categories', 'categories.id = sliders.type_id AND sliders.type = "categories"', 'left');

            $this->db->group_start();
            $this->db->or_like($multipleWhere);
            $this->db->or_like('products.name', $search);
            $this->db->or_like('categories.name', $search);
            $this->db->group_end();
        }

        // Add where conditions for filter
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        // Add where conditions for filter
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $slider_count = $count_res->get('sliders')->result_array();
        $total = $slider_count[0]['total'];

        // Main query
        $search_res = $this->db->select('sliders.*');

        if (!empty($search)) {
            $this->db->join('products', 'products.id = sliders.type_id AND sliders.type = "products"', 'left');
            $this->db->join('categories', 'categories.id = sliders.type_id AND sliders.type = "categories"', 'left');

            $this->db->group_start();
            $this->db->or_like($multipleWhere);
            $this->db->or_like('products.name', $search);
            $this->db->or_like('categories.name', $search);
            $this->db->group_end();
        }

        // Add where conditions for filter
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $slider_search_res = $search_res->order_by('sliders.' . $sort, $order)->limit($limit, $offset)->get('sliders')->result_array();

        // Rest of your code remains the same
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($slider_search_res as $row) {
            $row = output_escaping($row);

            if (isset($row['type_id']) && !empty($row['type_id']) && $row['type'] == 'products') {
                $product = fetch_details('products', ['id' => $row['type_id']], 'name');
                $tempRow['name'] = !empty($product[0]['name']) ? $product[0]['name'] : '';
            } elseif (isset($row['type_id']) && !empty($row['type_id']) && $row['type'] == 'categories') {
                $categories = fetch_details('categories', ['id' => $row['type_id']], 'name');
                $tempRow['name'] = !empty($categories[0]['name']) ? $categories[0]['name'] : '';
            } else {
                $tempRow['name'] = '';
            }

            // Create dropdown menu for operate column
            $operate = '
        <div class="dropdown">
            <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                    data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                <i class="ti ti-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

            // Edit Slider
            $operate .= '<li>
            <a class="dropdown-item editSliderBtn" href="javascript:void(0) " 
               data-id="' . $row['id'] . '" 
               data-type-id="' . $row['type_id'] . '" 
               data-link="' . $row['link'] . '" 
               data-bs-target="#addSlider" 
               data-bs-toggle="offcanvas">
                <i class="ti ti-pencil me-2"></i>Edit
            </a>
        </li>';

            // Divider
            $operate .= '<li><hr class="dropdown-divider"></li>';

            // Delete Slider
            $operate .= '<li>
            <a class="dropdown-item text-danger" href="javascript:void(0)"
               x-data="ajaxDelete({
                   url: base_url + \'admin/Slider/delete_slider\',
                   id: \'' . $row['id'] . '\',
                   tableSelector: \'#slider_table\',
                   confirmTitle: \'Delete Slider\',
                   confirmMessage: \'Do you really want to delete this slider?\'
               })"
               @click="deleteItem">
                <i class="ti ti-trash me-2"></i>Delete
            </a>
        </li>';

            $operate .= '
            </ul>
        </div>';

            $tempRow['id'] = $row['id'];
            $tempRow['type'] = $row['type'];
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
<div style='width:100px;height:100px;overflow:hidden;display:flex;align-items:center;justify-content:center;'>
    <a href='" . $row['image_main'] . "' data-lightbox='slider'>
        <img src='" . $row['image'] . "' class='rounded' 
             style='width:100px;height:100px;object-fit:cover;border-radius:8px;' alt='Image'>
    </a>
</div>";
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
}
