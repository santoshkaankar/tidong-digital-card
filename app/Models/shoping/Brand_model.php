<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Brand_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }


    public function add_brand($data)
    {
        $data = escape_array($data);

        $brands_data = [
            'name' => $data['brand_input_name'],
            'slug' => create_unique_slug($data['brand_input_name'], 'brands'),
            'status' => '1',
        ];

        if (isset($data['edit_brand']) && !empty($data['edit_brand'])) {
            unset($brands_data['status']);
            if (isset($data['brand_input_image']) && !empty($data['brand_input_image'])) {
                $brands_data['image'] = $data['brand_input_image'];
            }
            $this->db->set($brands_data)->where('id', $data['edit_brand'])->update('brands');
        } else {
            if (isset($data['brand_input_image']) && !empty($data['brand_input_image'])) {
                $brands_data['image'] = $data['brand_input_image'];
            }
            $this->db->insert('brands', $brands_data);
        }
    }



    public function delete_brand($id)
    {
        $id = escape_array($id);
        $this->db->delete('brands', ['id' => $id]);
        $response = TRUE;
        return $response;
    }

    public function get_brands($id = NULL, $limit = '', $offset = '', $sort = 'row_order', $order = 'ASC', $status = '1')
    {
        $this->db->select('b.id as brand_id, b.name as brand_name, b.slug as brand_slug, b.image as brand_img, b.status as brand_status');

        $this->db->join('products p', 'p.brand = b.name', 'left');
        $this->db->where('b.status', $status);
        $this->db->group_by('b.id');

        if (!empty($limit)) {
            $this->db->limit($limit, $offset);
        }
        $this->db->order_by($sort, $order);

        $query = $this->db->get('brands b');
        $brands = $query->result();

        // Count the total results that match the status condition
        $this->db->where('b.status', $status);

        $count_res = $this->db->count_all_results('brands b');

        // Convert the result to an associative array
        return json_decode(json_encode($brands), true);
    }


    public function get_brand_list()
{
      $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
    $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    // Allow only safe sortable columns
    $allowedSortColumns = ['id', 'name', 'status'];
    if (!in_array($sort, $allowedSortColumns)) {
        $sort = 'id';
    }

    $where = ['status !=' => NULL];
    $multipleWhere = [];

    // Search condition
    if (!empty($search)) {
        $multipleWhere = [
            '`id`' => $search,
            '`name`' => $search,
        ];
    }

    // ---- COUNT QUERY ----
    $count_res = $this->db->select('COUNT(id) as total');
    if (!empty($multipleWhere)) {
        $count_res->group_Start();
        $count_res->or_like($multipleWhere);
        $count_res->group_End();
    }
    if (!empty($where)) {
        $count_res->where($where);
    }

    $brand_count = $count_res->get('brands')->result_array();
    $total = isset($brand_count[0]['total']) ? $brand_count[0]['total'] : 0;

    // ---- MAIN QUERY ----
    $search_res = $this->db->select('*');
    if (!empty($multipleWhere)) {
        $search_res->group_Start();
        $search_res->or_like($multipleWhere);
        $search_res->group_End();
    }
    if (!empty($where)) {
        $search_res->where($where);
    }

    $brand_search_res = $search_res->order_by($sort, $order)
        ->limit($limit, $offset)
        ->get('brands')
        ->result_array();

    $bulkData = [];
    $bulkData['total'] = $total;
    $rows = [];

    foreach ($brand_search_res as $row) {
        $tempRow = [];

        // Handle images
        if (empty($row['image']) || !file_exists(FCPATH . $row['image'])) {
            $row['image'] = base_url() . NO_IMAGE;
            $row['image_main'] = base_url() . NO_IMAGE;
        } else {
            $row['image_main'] = base_url($row['image']);
            $row['image'] = get_image_url($row['image'], 'thumb', 'sm');
        }

        // ---- STATUS BADGE ----
        if ($row['status'] == '1') {
            $tempRow['status'] = '<a class="badge badge-success bg-success-lt">Active</a>';
        } else {
            $tempRow['status'] = '<a class="badge badge-danger bg-danger-lt">Inactive</a>';
        }

        // ---- COMMON FIELDS ----
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = output_escaping($row['name']);
        // $tempRow['image_main_url'] = base_url($row['image_main']);
        $tempRow['image_main_url'] = ($row['image_main']);
    $tempRow['image'] = "
<div class='d-flex justify-content-center'>
    <a href='" . $row['image_main'] . "' data-lightbox='brand'>
        <img class='rounded' src='" . $row['image'] . "' style='width:120px; height:120px; object-fit:cover; border-radius:6px;'>
    </a>
</div>";


        // ---- ADMIN ONLY OPERATE COLUMN ----
        if (!$this->ion_auth->is_seller()) {
            $operate = '
            <div class="dropdown">
                <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

            // Edit
            $operate .= '<li>
                <a class="dropdown-item" href="javascript:void(0)" 
                   data-id="' . $row['id'] . '" 
                   data-bs-toggle="offcanvas" 
                   data-bs-target="#addBrands">
                    <i class="ti ti-pencil me-2"></i>Edit
                </a>
            </li>';

            // Toggle status
            if ($row['status'] == '1') {
                $operate .= '<li>
                    <a class="dropdown-item update_active_status" href="javascript:void(0)" 
                       data-table="brands" 
                       data-id="' . $row['id'] . '" 
                       data-status="' . $row['status'] . '">
                        <i class="ti ti-toggle-right me-2"></i>Deactivate
                    </a>
                </li>';
            } else {
                $operate .= '<li>
                    <a class="dropdown-item update_active_status" href="javascript:void(0)" 
                       data-table="brands" 
                       data-id="' . $row['id'] . '" 
                       data-status="' . $row['status'] . '">
                        <i class="ti ti-toggle-left me-2"></i>Activate
                    </a>
                </li>';
            }

            // Divider
            $operate .= '<li><hr class="dropdown-divider"></li>';

            // Delete
            $operate .= '<li>
                <a class="dropdown-item text-danger" href="javascript:void(0)"
                   x-data="ajaxDelete({
                       url: base_url + \'admin/brand/delete_brand\',
                       id: \'' . $row['id'] . '\',
                       tableSelector: \'#brand_table\',
                       confirmTitle: \'Delete Brand\',
                       confirmMessage: \'Do you really want to delete this brand?\'
                   })"
                   @click="deleteItem">
                    <i class="ti ti-trash me-2"></i>Delete
                </a>
            </li>';

            $operate .= '</ul></div>';

            $tempRow['operate'] = $operate;
        }

        $rows[] = $tempRow;
    }

    $bulkData['rows'] = $rows;
    echo json_encode($bulkData);
}

}
