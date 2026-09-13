<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Language_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper', 'timezone_helper']);
    }

    public function create($data)
    {
        $data['language'] = strtolower($data['language']);
        $arr = array(
            'id' => $data['language_id'],
            'language' => $data['language'],
            'code' => $data['code'],
            'native_language' => $data['native_language'],
            'is_rtl' => (isset($data['is_rtl']) && $data['is_rtl'] == 1) ? 1 : 0,
        );

        if (isset($data['language_id']) && !empty($data['language_id'])) {
            return $this->db->where('id', $data['language_id'])->update('languages', $arr);
        }
        return $this->db->insert('languages', $arr);
    }

    public function update($data)
    {
        $arr = array(
            'is_rtl' => (isset($data['is_rtl']) && $data['is_rtl'] == 1) ? 1 : 0,
        );
        return $this->db->where('id', $data['language_id'])->update('languages', $arr);
    }

    public function is_default_for_web($data)
    {
        // Initialize the array to update the 'is_default' field
        $arr = array(
            'is_default' => (isset($data['is_default']) && $data['is_default'] == 1) ? 1 : 0,
        );

        // Set all 'is_default' fields to 0
        $this->db->where('is_default', '1')->set(['is_default' => '0'])->update('languages');

        // Update the specific language with the new 'is_default' value
        return $this->db->where('id', $data['language_id'])->update('languages', $arr);
    }

    public function get_language_list()
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

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['id' => $search, 'language' => $search, 'code' => $search, 'is_rtl' => $search];
        }

        $count_res = $this->db->select(' COUNT(id) as `total`');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_Start();
            $count_res->or_like($multipleWhere);
            $count_res->group_End();
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $address_count = $count_res->get('languages')->result_array();

        foreach ($address_count as $row) {
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

        $theme = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('languages')->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        foreach ($theme as $row) {
            $row = output_escaping($row);

            // Create dropdown menu for operate column
            $operate = '
            <div class="dropdown">
                <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

            // Edit Language
            $operate .= '<li>
                <a class="dropdown-item " href="javascript:void(0)" 
                                     data-bs-toggle="offcanvas" 
                   data-bs-target="#addLanguage">
                    <i class="ti ti-pencil me-2"></i>Edit
                </a>
            </li>';

            // Upload Language File
            $operate .= '<li>
                <a class="dropdown-item upload-language" href="javascript:void(0)" 
                   data-bs-toggle="offcanvas" 
                   data-bs-target="#uploadLanguageFile">
                    <i class="ti ti-upload me-2"></i>Upload File
                </a>
            </li>';

            // Set as Default (disabled if already default)
            if ($row['is_default'] == '1') {
                $operate .= '<li>
                    <span class="dropdown-item text-muted disabled">
                        <i class="ti ti-star me-2"></i>Already Default
                    </span>
                </li>';
            } else {
                $operate .= '<li>
                    <a class="dropdown-item set-as-default-language" href="javascript:void(0)" 
                       data-id="' . $row['id'] . '">
                        <i class="ti ti-star me-2"></i>Set as Default
                    </a>
                </li>';
            }

            // Divider
            $operate .= '<li><hr class="dropdown-divider"></li>';

            // Delete Language (disabled if default language)
            if ($row['is_default'] == '1') {
                $operate .= '<li>
                    <span class="dropdown-item text-muted disabled">
                        <i class="ti ti-trash me-2"></i>Cannot Delete Default
                    </span>
                </li>';
            } else {
                $operate .= '<li>
                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                       x-data="ajaxDelete({
                           url: base_url + \'admin/language/delete_language\',
                           id: \'' . $row['id'] . '\',
                           tableSelector: \'#web_theme_table\',
                           confirmTitle: \'Delete Language\',
                           confirmMessage: \'Do you really want to delete this language? This will permanently delete the language and all its related files and folders.\'
                       })"
                       @click="deleteItem">
                        <i class="ti ti-trash me-2"></i>Delete
                    </a>
                </li>';
            }

            $operate .= '
                </ul>
            </div>';

            $tempRow['id'] = $row['id'];
            $tempRow['language'] = $row['language'];
            $tempRow['native_language'] = $row['native_language'];
            $tempRow['code'] = $row['code'];
            $tempRow['is_rtl_main'] = $row['is_rtl'];
            if ($row['is_rtl'] == '1') {
                $tempRow['is_rtl'] = '<a class="badge badge-success bg-success-lt text-decoration-none" >Yes</a>';
            } else {
                $tempRow['is_rtl'] = '<a class="badge badge-danger bg-danger-lt text-decoration-none" >No</a>';
            }

            // Add default status indicator
            if ($row['is_default'] == '1') {
                $tempRow['is_default'] = '<span class="badge badge-warning bg-warning-lt text-decoration-none"><i class="ti ti-star me-1"></i>Default</span>';
            } else {
                $tempRow['is_default'] = '<span class="badge badge-secondary bg-secondary-lt text-decoration-none">-</span>';
            }

            $tempRow['created_on'] = $row['created_on'];
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function delete_language($id)
    {
        // Delete the language from the database
        $this->db->where('id', $id);
        return $this->db->delete('languages'); // Assuming 'languages' is your table name
    }
}


