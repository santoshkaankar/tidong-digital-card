<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Category_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }
    // public function get_categories($id = NULL, $limit = '', $offset = '', $sort = 'row_order', $order = 'ASC', $has_child_or_item = 'true', $slug = '', $ignore_status = '', $seller_id = '', $from_select = '', $ignore_category = '')
    // {

    //     $level = 0;

    //     if ($ignore_status == 1) {
    //         $where = (isset($id) && !empty($id)) ? ['c1.id' => $id] : ['c1.parent_id' => 0];
    //     } else {
    //         $where = (isset($id) && !empty($id)) ? ['c1.id' => $id, 'c1.status' => 1] : ['c1.parent_id' => 0, 'c1.status' => 1];
    //         // $where = (isset($id) && !empty($id)) ? ['c1.id' => $id, 'c1.status' => 1] : ['c1.status' => 1];
    //     }

    //     if ($from_select == 1) {
    //         $where = (isset($id) && !empty($id)) ? ['c1.id' => $id, 'c1.status' => 1] : ['c1.status' => 1];
    //     }

    //     // Build the base query
    //     $this->db->select('c1.*');
    //     $this->db->from('categories c1');
    //     $this->db->where($where);

    //     if (!empty($slug)) {
    //         $this->db->where('c1.slug', $slug);
    //     }

    //     if ($has_child_or_item == 'false') {
    //         $this->db->join('categories c2', 'c2.parent_id = c1.id', 'left');
    //         $this->db->join('products p', 'p.category_id = c1.id', 'left');
    //         $this->db->group_start();
    //         $this->db->or_where(['c1.id ' => ' p.category_id ', ' c2.parent_id ' => ' c1.id '], NULL, FALSE);
    //         $this->db->group_end();
    //         $this->db->group_by('c1.id');
    //     }

    //     // Clone the query for counting before adding limit and offset
    //     $count_query = clone $this->db;
    //     $count_res = $count_query->count_all_results();



    //     // Continue with the main query
    //     if (!empty($limit) || !empty($offset)) {
    //         $this->db->limit($limit);
    //         $this->db->offset($offset);
    //     }


    //     $this->db->order_by((string) $sort, (string) $order);
    //     $parent = $this->db->get();
    //     $categories = $parent->result();

    //     // echo $this->db->last_query();

    //     $i = 0;
    //     foreach ($categories as $p_cat) {
    //         $categories[$i]->children = $this->sub_categories($p_cat->id, $level);
    //         $categories[$i]->text = output_escaping($p_cat->name);
    //         $categories[$i]->name = output_escaping($categories[$i]->name);
    //         $categories[$i]->state = ['opened' => true];
    //         $categories[$i]->icon = "jstree-folder";
    //         $categories[$i]->level = $level;
    //         $categories[$i]->relative_path = $categories[$i]->image;
    //         $categories[$i]->image = get_image_url($categories[$i]->image, 'thumb', 'sm');
    //         $categories[$i]->banner = get_image_url($categories[$i]->banner, 'thumb', 'md');
    //         $i++;
    //     }

    //     if (isset($categories[0])) {
    //         $categories[0]->total = $count_res;
    //     }

    //     // print_r($categories);

    //     return json_decode(json_encode($categories), 1);
    // }

     public function get_categories($id = NULL, $limit = '', $offset = '', $sort = 'row_order', $order = 'ASC', $has_child_or_item = 'true', $slug = '', $ignore_status = '', $seller_id = '', $from_select = '', $ignore_category = '')
    {
        $level = 0;

        if ($ignore_status == 1) {
            $where = (isset($id) && !empty($id)) ? ['c1.id' => $id] : ['c1.parent_id' => 0];
        } else {
            $where = (isset($id) && !empty($id)) ? ['c1.id' => $id, 'c1.status' => 1] : ['c1.parent_id' => 0, 'c1.status' => 1];
        }

        // Fix: keep parent_id = 0 even for from_select=1 (prevent recursion loading all categories)
        if ($from_select == 1) {
            $where = ['c1.status' => 1];
        }

        // Base query
        $this->db->select('c1.*');
        $this->db->from('categories c1');
        $this->db->where($where);

        if (!empty($slug)) {
            $this->db->where('c1.slug', $slug);
        }

        if ($has_child_or_item == 'false') {
            $this->db->join('categories c2', 'c2.parent_id = c1.id', 'left');
            $this->db->join('products p', 'p.category_id = c1.id', 'left');
            $this->db->group_start();
            $this->db->or_where(['c1.id ' => ' p.category_id ', ' c2.parent_id ' => ' c1.id '], NULL, FALSE);
            $this->db->group_end();
            $this->db->group_by('c1.id');
        }

        // Count results (optimized)
        $count_query = clone $this->db;
        $count_res = $count_query->count_all_results();

        if (!empty($limit) || !empty($offset)) {
            $this->db->limit($limit);
            $this->db->offset($offset);
        }

        $this->db->order_by((string) $sort, (string) $order);
        $parent = $this->db->get();
        $categories = $parent->result();

        $i = 0;
        foreach ($categories as $p_cat) {
            // Fix: avoid recursion for from_select=1
            if ($from_select != 1) {
                $categories[$i]->children = $this->sub_categories($p_cat->id, $level + 1);
            } else {
                $categories[$i]->children = [];
            }

            $categories[$i]->text = output_escaping($p_cat->name);
            $categories[$i]->name = output_escaping($categories[$i]->name);
            $categories[$i]->state = ['opened' => true];
            $categories[$i]->icon = "jstree-folder";
            $categories[$i]->level = $level;
            $categories[$i]->relative_path = $categories[$i]->image;
            $categories[$i]->image = get_image_url($categories[$i]->image, 'thumb', 'sm');
            $categories[$i]->banner = get_image_url($categories[$i]->banner, 'thumb', 'md');
            $i++;
        }

        if (isset($categories[0])) {
            $categories[0]->total = $count_res;
        }

        return json_decode(json_encode($categories), true);
    }



    // public function get_seller_categories($seller_id)
    // {
    //     $level = 0;
    //     $this->db->select('category_ids');
    //     $where = 'user_id = ' . $seller_id;
    //     $this->db->where($where);
    //     $result = $this->db->get('seller_data')->result_array();
    //     $count_res = $this->db->count_all_results('seller_data');
    //     $result = explode(",", (string)$result[0]['category_ids']);
    //     $categories =  fetch_details('categories', "status = 1", '*', "", "", "", "", "id", $result);

    //     $i = 0;
    //     foreach ($categories as $p_cat) {
    //         $categories[$i]['children'] = $this->sub_categories($p_cat['id'], $level);
    //         $categories[$i]['text'] = output_escaping($p_cat['name']);
    //         $categories[$i]['name'] = output_escaping($categories[$i]['name']);
    //         $categories[$i]['state'] = ['opened' => true];
    //         $categories[$i]['icon'] = "jstree-folder";
    //         $categories[$i]['level'] = $level;
    //         $categories[$i]['image'] = get_image_url($categories[$i]['image'], 'thumb', 'md');
    //         $categories[$i]['relative_path'] = $categories[$i]['image'];
    //         $categories[$i]['banner'] = get_image_url($categories[$i]['banner'], 'thumb', 'md');
    //         $i++;
    //     }
    //     if (isset($categories[0])) {
    //         $categories[0]['total'] = $count_res;
    //     }
    //     return  $categories;
    // }

    public function get_seller_categories($seller_id, bool $all_categories = false, $current_category_id = null)
    {
        $level = 0;

        // Get the seller's category IDs
        $this->db->select('category_ids');
        $this->db->where('user_id', $seller_id);
        $result = $this->db->get('seller_data')->row_array();

        // If no categories are found, return an empty array
        $category_ids = isset($result['category_ids']) && !empty($result['category_ids']) ? explode(",", $result['category_ids']) : [];

        if ($all_categories) {
            $this->db->select('*')->where('status', 1);
            $categories = $this->db->get('categories')->result_array();
        } else {
            if (empty($category_ids)) {
                return [];
            }            
            // Only get the assigned categories - no parent categories
            $this->db->select('*')->where('status', 1)->where_in('id', $category_ids);
            $categories = $this->db->get('categories')->result_array();
        }
        
        $assigned_ids = $category_ids;
        $categories_map = [];
        
        foreach ($categories as $cat) {
            $categories_map[$cat['id']] = $cat;
        }
        
        $current_category_tree = [];
        if ($current_category_id && isset($categories_map[$current_category_id])) {
            $cat_id = $current_category_id;
            while ($cat_id && isset($categories_map[$cat_id])) {
                $current_category_tree[] = $cat_id;
                $cat_id = $categories_map[$cat_id]['parent_id'] ?? 0;
            }
        }

        $categories_by_id = [];
        foreach ($categories_map as $id => $cat) {
            $state = ['opened' => true];

            $categories_by_id[$id] = [
                'id' => $cat['id'],
                'parent_id' => 0, // Set parent_id to 0 to display all categories at root level
                'name' => $cat['name'],
                'slug' => $cat['slug'],
                'image' => $cat['image'],
                'banner' => $cat['banner'],
                'status' => $cat['status'],
                'children' => [],
                'text' => output_escaping($cat['name']),
                'state' => $state,
                'icon' => "jstree-folder",
                'level' => 0, // Set level to 0 for flat display
                'relative_path' => get_image_url($cat['image'], 'thumb', 'md')
            ];
            
            $categories_by_id[$id]['image'] = $categories_by_id[$id]['relative_path'];
            $categories_by_id[$id]['banner'] = get_image_url($cat['banner'], 'thumb', 'md');
        }

        // Build flat list (all categories at root level for sellers)
        $hierarchy = [];
        foreach ($categories_by_id as $id => $cat) {
            $hierarchy[] = $cat;
        }

        if (!empty($hierarchy)) {
            $hierarchy[0]['total'] = count($categories);
        }

        return $hierarchy;
    }

    public function sub_categories($id, $level)
    {
        // Prevent runaway recursion (max 10 levels)
        if ($level > 10) {
            return [];
        }

        $this->db->select('c1.*');
        $this->db->from('categories c1');
        $this->db->where(['c1.parent_id' => $id, 'c1.status' => 1]);
        $child = $this->db->get();
        $categories = $child->result();

        $i = 0;
        foreach ($categories as $p_cat) {
            // Recursive call with depth control
            $categories[$i]->children = $this->sub_categories($p_cat->id, $level + 1);
            $categories[$i]->text = output_escaping($p_cat->name);
            $categories[$i]->state = ['opened' => true];
            $categories[$i]->level = $level;
            $categories[$i]->relative_path = $categories[$i]->image;
            $categories[$i]->image = get_image_url($categories[$i]->image, 'thumb', 'md');
            $categories[$i]->banner = get_image_url($categories[$i]->banner, 'thumb', 'md');
            $i++;
        }

        return $categories;
    }

    // public function sub_categories($id, $level)
    // {
    //     $level = $level + 1;
    //     $this->db->select('c1.*');
    //     $this->db->from('categories c1');
    //     $this->db->where(['c1.parent_id' => $id, 'c1.status' => 1]);
    //     $child = $this->db->get();
    //     $categories = $child->result();
    //     $i = 0;
    //     foreach ($categories as $p_cat) {
    //         $categories[$i]->children = $this->sub_categories($p_cat->id, $level);
    //         $categories[$i]->text = output_escaping($p_cat->name);
    //         $categories[$i]->state = ['opened' => true];
    //         $categories[$i]->level = $level;
    //         $categories[$i]->relative_path = $categories[$i]->image;
    //         $categories[$i]->image = get_image_url($categories[$i]->image, 'thumb', 'md');
    //         $categories[$i]->banner = get_image_url($categories[$i]->banner, 'thumb', 'md');
    //         $i++;
    //     }
    //     return $categories;
    // }

    public function get_category_list($seller_id = NULL)
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $multipleWhere = [];
        $where = ['status !=' => NULL];

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $where['parent_id'] = $_GET['id'];
        }
        if (isset($_GET['offset']) && !empty($_GET['offset'])) {
            $offset = $_GET['offset'];
        }
        if (isset($_GET['limit']) && !empty($_GET['limit'])) {
            $limit = $_GET['limit'];
        }
        if (isset($_GET['sort']) && !empty($_GET['sort'])) {
            $sort = $_GET['sort'];
        }
        if (isset($_GET['order']) && !empty($_GET['order'])) {
            $order = $_GET['order'];
        }
        if (isset($_GET['status']) && $_GET['status'] != '') {
            $where['status'] = $_GET['status'];
        }
        if (isset($_GET['search']) && $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                'id' => $search,
                'name' => $search
            ];
        }

        if (isset($seller_id) && $seller_id != "") {
            $this->db->select('category_ids');
            $this->db->where('user_id', $seller_id);
            $result = $this->db->get('seller_data')->row_array();
            $cat_ids = isset($result['category_ids']) ? explode(',', $result['category_ids']) : [];
        }

        $this->db->select('COUNT(id) as total');
        if (!empty($multipleWhere)) {
            $this->db->group_start();
            foreach ($multipleWhere as $key => $value) {
                $this->db->or_like($key, $value);
            }
            $this->db->group_end();
        }
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (isset($cat_ids) && !empty($cat_ids)) {
            $this->db->where_in('id', $cat_ids);
        }
        $cat_count = $this->db->get('categories')->row_array();
        $total = $cat_count['total'];

        $this->db->select('*');
        if (!empty($multipleWhere)) {
            $this->db->group_start();
            foreach ($multipleWhere as $key => $value) {
                $this->db->or_like($key, $value);
            }
            $this->db->group_end();
        }
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (isset($cat_ids) && !empty($cat_ids)) {
            $this->db->where_in('id', $cat_ids);
        }
        $cat_search_res = $this->db->order_by($sort, $order)->limit($limit, $offset)->get('categories')->result_array();

        // echo $this->db->last_query();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();

        if (!empty($cat_search_res)) {
            foreach ($cat_search_res as $row) {

                $tempRow = array();
                // Create dropdown menu for operate column
                $operate = '';
                if (!$this->ion_auth->is_seller()) {
                    $operate = '
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

                    // Edit Category
                    $operate .= '<li>
                        <a class="dropdown-item" href="javascript:void(0)" 
                           data-category-id="' . $row['id'] . '" 
                           data-category-parent-id="' . $row['parent_id'] . '" 
                           data-bs-toggle="offcanvas" 
                           data-bs-target="#addCategory">
                            <i class="ti ti-pencil me-2"></i>Edit
                        </a>
                    </li>';

                    // Status actions based on current status
                    if ($row['status'] == '1') {
                        $tempRow['status'] = '<a class="badge badge-success bg-success-lt text-white">Active</a>';
                        $operate .= '<li>
                            <a class="dropdown-item update_active_status" href="javascript:void(0)" 
                               data-table="categories" 
                               data-id="' . $row['id'] . '" 
                               data-status="' . $row['status'] . '">
                                <i class="ti ti-eye-off me-2"></i>Deactivate
                            </a>
                        </li>';
                    } else {
                        $tempRow['status'] = '<a class="badge badge-danger bg-danger-lt text-white">Inactive</a>';
                        $operate .= '<li>
                            <a class="dropdown-item update_active_status" href="javascript:void(0)" 
                               data-table="categories" 
                               data-id="' . $row['id'] . '" 
                               data-status="' . $row['status'] . '">
                                <i class="ti ti-eye me-2"></i>Activate
                            </a>
                        </li>';
                    }

                    // Divider
                    $operate .= '<li><hr class="dropdown-divider"></li>';

                    // Delete Category
                    $operate .= '<li>
                        <a class="dropdown-item text-danger" href="javascript:void(0)"
                           x-data="ajaxDelete({
                               url: base_url + \'admin/category/delete_category\',
                               id: \'' . $row['id'] . '\',
                               tableSelector: \'#category_table\',
                               confirmTitle: \'Delete Category\',
                               confirmMessage: \'Do you really want to delete this category?\'
                           })"
                           @click="deleteItem">
                            <i class="ti ti-trash me-2"></i>Delete
                        </a>
                    </li>';

                    $operate .= '
                        </ul>
                    </div>';
                } else {
                    // For sellers, just show status without actions
                    if ($row['status'] == '1') {
                        $tempRow['status'] = '<a class="badge badge-success bg-success-lt text-white">Active</a>';
                    } else {
                        $tempRow['status'] = '<a class="badge badge-danger bg-danger-lt text-white">Inactive</a>';
                    }
                }

                $tempRow['id'] = $row['id'];

                $tempRow['name'] = output_escaping($row['name']);

                $tempRow['text'] = output_escaping($row['name']);
                $tempRow['image_main_url'] = $row['image'];
                $tempRow['seo_page_title'] = $row['seo_page_title'];
                $tempRow['seo_meta_keywords'] = $row['seo_meta_keywords'];
                $tempRow['seo_meta_description'] = $row['seo_meta_description'];
                $tempRow['seo_og_image'] = $row['seo_og_image'];
                $tempRow['parent_id'] = $row['parent_id'];

                if (empty($row['image']) || !file_exists(FCPATH . $row['image'])) {
                    $row['image'] = base_url() . NO_IMAGE;
                    $row['image_main'] = base_url() . NO_IMAGE;
                } else {
                    $row['image_main'] = base_url($row['image']);
                    $row['image'] = get_image_url($row['image'], 'thumb', 'sm');
                }
$tempRow['image'] = "
  <div class='d-flex justify-content-center'>
    <a href='" . $row['image_main'] . "' data-lightbox='logo' class='image-box-100'>
      <img class='rounded' src='" . $row['image'] . "' style='width: 120px; height: 120px; object-fit: cover; border-radius: 6px;'>
    </a>
  </div>";



                if (empty($row['banner']) || !file_exists(FCPATH . $row['banner'])) {
                    $row['banner'] = base_url() . NO_IMAGE;
                    $row['banner_main'] = base_url() . NO_IMAGE;
                } else {
                    $row['banner_main'] = base_url($row['banner']);
                    $row['banner'] = get_image_url($row['banner'], 'thumb', 'sm');
                }
                $tempRow['banner'] = "<div><a href='" . $row['banner_main'] . "' data-lightbox='category' class='image-box-100'> <img src='" . $row['banner'] . "' style='width:120px;height:120px;object-fit:cover;' class='rounded'></a></div>";

                if (!$this->ion_auth->is_seller()) {
                    $tempRow['operate'] = $operate;
                }
                $rows[] = $tempRow;
            }
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData);
    }

    public function add_category($data)
    {
        $data = escape_array($data);

        if (isset($data['edit_category']) && !empty($data['edit_category'])) {
            $category_id = fetch_details('categories', ['id' => $data['edit_category']]);
            $category_name = $category_id[0]['name'];
        } else {
            $category_id = "";
            $category_name = "";
        }
        if ($category_name != $data['category_input_name']) {
            $cat_data = [
                'name' => $data['category_input_name'],
                'parent_id' => ($data['category_parent'] == NULL && isset($data['category_parent']) && !empty($data['category_parent'])) ? '0' : $data['category_parent'],
                'slug' => create_unique_slug($data['category_input_name'], 'categories'),
                'status' => '1',
                'seo_page_title' => $data['seo_page_title'],
                'seo_meta_keywords' => $data['seo_meta_keywords'],
                'seo_meta_description' => $data['seo_meta_description'],
                'seo_og_image' => isset($data['seo_og_image']) && !empty($data['seo_og_image']) ? $data['seo_og_image'] : '',
            ];
        } else {
            $cat_data = [
                'name' => $data['category_input_name'],
                'parent_id' => ($data['category_parent'] == NULL && isset($data['category_parent'])) ? '0' : $data['category_parent'],
                'status' => '1',
                'seo_page_title' => $data['seo_page_title'],
                'seo_meta_keywords' => $data['seo_meta_keywords'],
                'seo_meta_description' => $data['seo_meta_description'],
                'seo_og_image' => isset($data['seo_og_image']) && !empty($data['seo_og_image']) ? $data['seo_og_image'] : '',
            ];
        }

        if (isset($data['edit_category']) && !empty($data['edit_category'])) {
            unset($cat_data['status']);
            if (isset($data['category_input_image']) && !empty($data['category_input_image'])) {
                $cat_data['image'] = $data['category_input_image'];
            }

            $cat_data['banner'] = (isset($data['banner']) && !empty($data['banner'])) ? $data['banner'] : '';

            $this->db->set($cat_data)->where('id', $data['edit_category'])->update('categories');
        } else {
            if (isset($data['category_input_image']) && !empty($data['category_input_image'])) {
                $cat_data['image'] = $data['category_input_image'];
            }
            if (isset($data['banner']) && !empty($data['banner'])) {
                $cat_data['banner'] = (isset($data['banner']) && !empty($data['banner'])) ? $data['banner'] : '';
            }
            $this->db->insert('categories', $cat_data);
        }
    }

    public function top_category()
    {
        $query = $this->db->select('*')
            ->where('status', 1)
            ->limit('4')
            ->order_by('clicks', 'Desc')
            ->get('categories');

        $data['total'] = $query->num_rows();
        $categories = $query->result_array();
        $rows = array();

        $bulkData = array();
        $bulkData['total'] = $data['total'];
        $rows = array();

        if (!empty($query)) {
            foreach ($categories as $category) {
                $tempRow = array();
                $tempRow['id'] = $category['id'];
                $tempRow['name'] = str_replace('\\', '', $category['name']);
                $tempRow['clicks'] = $category['clicks'];
                $rows[] = $tempRow;
            }
        }
        $data['rows'] = $rows;
        echo json_encode($data);
    }

    public function get_categories_list($data)
    {
        $offset = 0;
        $limit = 10000;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = [];
        $where = ['status !=' => NULL];

        if (isset($data['id'])) {
            $where['parent_id'] = $data['id'];
        }
        if (isset($data['offset'])) {
            $offset = $data['offset'];
        }
        if (isset($data['limit'])) {
            $limit = $data['limit'];
        }
        if (isset($data['sort'])) {
            $sort = $data['sort'];
        }
        if (isset($data['order'])) {
            $order = $data['order'];
        }
        if (!empty($data['search'])) {
            $search = $data['search'];
            $multipleWhere = [
                'id' => $search,
                'name' => $search
            ];
        }

        if (isset($data['seller_id']) && !empty($data['seller_id'])) {
            $seller_id = $data['seller_id'];
            $this->db->select('category_ids');
            $this->db->where('user_id', $seller_id);
            $result = $this->db->get('seller_data')->row_array();
            $cat_ids = isset($result['category_ids']) ? explode(',', $result['category_ids']) : [];
        }

        // Count total records
        $this->db->select('COUNT(id) as total');
        if (!empty($multipleWhere)) {
            $this->db->group_start();
            foreach ($multipleWhere as $key => $value) {
                $this->db->or_like($key, $value);
            }
            $this->db->group_end();
        }
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (!empty($cat_ids)) {
            $this->db->where_in('id', $cat_ids);
        }
        $total = $this->db->get('categories')->row_array()['total'] ?? 0;

        // Fetch actual records
        $this->db->select('*');
        if (!empty($multipleWhere)) {
            $this->db->group_start();
            foreach ($multipleWhere as $key => $value) {
                $this->db->or_like($key, $value);
            }
            $this->db->group_end();
        }
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (!empty($cat_ids)) {
            $this->db->where_in('id', $cat_ids);
        }
        $categories = $this->db->order_by($sort, $order)->limit($limit, $offset)->get('categories')->result_array();

        $rows = [];
        foreach ($categories as $row) {
            $tempRow = [
                'id' => $row['id'],
                'name' => $row['name'],
            ];

            // Handle image
            $image_main = (!empty($row['image']) && file_exists(FCPATH . $row['image']))
                ? base_url($row['image'])
                : base_url() . NO_IMAGE;
            $image_thumb = (!empty($row['image']) && file_exists(FCPATH . $row['image']))
                ? get_image_url($row['image'], 'thumb', 'sm')
                : base_url() . NO_IMAGE;

            $tempRow['image'] = $image_main;
            $tempRow['image_thumb'] = $image_thumb;

            // Handle banner
            $banner_main = (!empty($row['banner']) && file_exists(FCPATH . $row['banner']))
                ? base_url($row['banner'])
                : base_url() . NO_IMAGE;
            $banner_thumb = (!empty($row['banner']) && file_exists(FCPATH . $row['banner']))
                ? get_image_url($row['banner'], 'thumb', 'sm')
                : base_url() . NO_IMAGE;

            $tempRow['banner'] = $banner_main;
            $tempRow['banner_thumb'] = $banner_thumb;

            $rows[] = $tempRow;
        }

        return [
            'error' => false,
            'message' => 'Category retrieved successfully',
            'total' => $total,
            'rows' => $rows
        ];
    }


    public function get_download_categories()
    {
        $categories = $this->db->get('categories')->result_array();
        return $categories;
    }
}
