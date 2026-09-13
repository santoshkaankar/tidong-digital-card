<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Featured_section_model extends CI_Model
{
    function add_featured_section($data)
    {

        $data = escape_array($data);

        if (isset($data['product_ids']) && !empty($data['product_ids']) && trim($data['product_type']) == 'custom_products') {
            $product_ids = implode(',', $data['product_ids']);
        } elseif (isset($data['digital_product_ids']) && !empty($data['digital_product_ids']) && trim($data['product_type']) == 'digital_product') {
            $product_ids = implode(',', $data['digital_product_ids']);
        } else {
            $product_ids = null;
        }

        $featured_data = [
            'title' => $data['title'],
            'short_description' => $data['short_description'],
            'product_type' => $data['product_type'],
            'categories' => (isset($data['categories']) && !empty($data['categories'])) ? implode(',', $data['categories']) : null,
            'product_ids' => $product_ids,
            'seo_page_title' => $data['seo_page_title'],
            'seo_meta_keywords' => $data['seo_meta_keywords'],
            'seo_meta_description' => $data['seo_meta_description'],
            'seo_og_image' => isset($data['seo_og_image']) && !empty($data['seo_og_image']) ? $data['seo_og_image'] : '',
            'style' => $data['style']
        ];

        if (isset($data['edit_featured_section']) && !empty($data['edit_featured_section'])) {
            if (strtolower(trim($data['product_type'])) != 'custom_products' && trim($data['product_type']) != 'digital_product') {
                $featured_data['product_ids'] = null;
            }
            $this->db->set($featured_data)->where('id', $data['edit_featured_section'])->update('sections');
        } else {
            $this->db->insert('sections', $featured_data);
        }
    }
    public function get_section_list()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
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

        $where = [];
        if (isset($_GET['product_type']) && !empty($_GET['product_type'])) {
            $where['product_type'] = $_GET['product_type'];
        }

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['id' => $search, 'title' => $search, 'short_description' => $search];
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

        $city_count = $count_res->get('sections')->result_array();

        foreach ($city_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' * ');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_Start();
            $search_res->or_like($multipleWhere);
            $search_res->group_End();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $city_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('sections')->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        foreach ($city_search_res as $row) {
            $row = output_escaping($row);

            // Create dropdown menu for operate column
            $operate = '
            <div class="dropdown">
                <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

            // Edit Featured Section
            $operate .= '<li>
                <a class="dropdown-item" href="' . base_url('admin/Featured_sections?edit_id=' . $row['id']) . '" 
                   data-id="' . $row['id'] . '" 
                   data-product-ids="' . $row['product_ids'] . '" 
                   data-category-id="' . $row['categories'] . '" 
                   data-bs-toggle="offcanvas" 
                   data-bs-target="#addFeatureSection">
                    <i class="ti ti-pencil me-2"></i>Edit
                </a>
            </li>';

            // Divider
            $operate .= '<li><hr class="dropdown-divider"></li>';

            // Delete Featured Section
            $operate .= '<li>
                <a class="dropdown-item text-danger" href="javascript:void(0)"
                   x-data="ajaxDelete({
                       url: base_url + \'admin/featured_sections/delete_featured_section\',
                       id: \'' . $row['id'] . '\',
                       tableSelector: \'#feature_section_table\',
                       confirmTitle: \'Delete Feature Section\',
                       confirmMessage: \'Do you really want to delete this feature section?\'
                   })"
                   @click="deleteItem">
                    <i class="ti ti-trash me-2"></i>Delete
                </a>
            </li>';

            $operate .= '
                </ul>
            </div>';

            $tempRow['id'] = $row['id'];
            $tempRow['title'] = $row['title'];
            $tempRow['short_description'] = $row['short_description'];
            $tempRow['style'] = ucfirst(str_replace('_', ' ', $row['style']));

            // Fetch product names instead of IDs
            $product_names = '';
            if (!empty($row['product_ids'])) {
                $product_ids_array = explode(',', $row['product_ids']);
                $this->db->select('name');
                $this->db->from('products');
                $this->db->where_in('id', $product_ids_array);
                $products = $this->db->get()->result_array();
                $product_names_array = array_column($products, 'name');
                $product_names = implode(', ', $product_names_array);
            }
            $tempRow['product_ids'] = description_word_limit($product_names, 50);

            // Fetch category names instead of IDs
            $category_names = '';
            if (!empty($row['categories'])) {
                $category_ids_array = explode(',', $row['categories']);
                $this->db->select('name');
                $this->db->from('categories');
                $this->db->where_in('id', $category_ids_array);
                $categories = $this->db->get()->result_array();
                $category_names_array = array_column($categories, 'name');
                $category_names = implode(', ', $category_names_array);
            }
            $tempRow['categories'] = $category_names;

            $tempRow['product_type'] = ucwords(str_replace('_', ' ', $row['product_type']));
            $tempRow['date'] = date('d-m-Y', strtotime($row['date_added']));
            $tempRow['seo_page_title'] = $row['seo_page_title'];
            $tempRow['seo_meta_keywords'] = $row['seo_meta_keywords'];
            $tempRow['seo_meta_description'] = $row['seo_meta_description'];
            $tempRow['seo_og_image'] = $row['seo_og_image'];
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
}
