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

        if (isset($data['edit_attribute_set']) && !empty($data['edit_attribute_set'])) {
            $this->db->set($attr_data)->where('id', $data['edit_attribute_set'])->update('attribute_set');
        } else {
            $this->db->insert('attribute_set', $attr_data);
        }
    }

    function get_attribute_set_list(
        $offset = 0,
        $limit = 10,
        $sort = " id ",
        $order = 'DESC',
        $from_select = 0
    ) {

        $multipleWhere = '';

        if (isset($_GET['offset']) && !empty($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']) && !empty($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']) && !empty($_GET['sort']))
            $sort = ($_GET['sort'] == 'id') ? "id" : $_GET['sort'];
        if (isset($_GET['order']) && !empty($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) && $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['id' => $search, 'name' => $search];
        }

        $count_res = $this->db->select(' COUNT(id) as `total` ');

        // Apply search filter
        if (!empty($multipleWhere)) {
            $count_res->or_like($multipleWhere);
        }

        // Check if the user is a seller and apply status filter if true
        if ($this->ion_auth->is_seller()) {
            $count_res->where('status', 1); // Only include active attributes for sellers
        }

        $attr_count = $count_res->get('attribute_set')->result_array();
        $total = isset($attr_count[0]['total']) ? $attr_count[0]['total'] : 0;

        $search_res = $this->db->select(' * ');

        // Apply search filter again for the actual data query
        if (!empty($multipleWhere)) {
            $search_res->or_like($multipleWhere);
        }

        // Apply status filter for sellers
        if ($this->ion_auth->is_seller()) {
            $search_res->where('status', 1); // Only show active attributes for sellers
        }

        $city_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('attribute_set')->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($city_search_res as $row) {
            $row = output_escaping($row);
            // Create dropdown menu for operate column
            if (!$this->ion_auth->is_seller()) {
                $operate = '
                <div class="dropdown">
                    <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

                // Edit Attribute Set
                $operate .= '<li>
                    <a class="dropdown-item edit_btn" href="javascript:void(0)" 
                       data-id="' . $row['id'] . '" 
                       data-bs-toggle="offcanvas" data-bs-target="#addAttributeSet">
                        <i class="ti ti-pencil"></i>Edit
                    </a>
                </li>';

                // Status actions based on current status
                if ($row['status'] == '1') {
                    $tempRow['status'] = '<a class="badge bg-success-lt text-success">Active</a>';
                    $operate .= '<li>
                        <a class="dropdown-item update_active_status" href="javascript:void(0)" 
                           data-table="attribute_set" 
                           data-id="' . $row['id'] . '" 
                           data-status="' . $row['status'] . '">
                            <i class="ti ti-eye-off"></i>Deactivate
                        </a>
                    </li>';
                } else {
                    $tempRow['status'] = '<a class="badge bg-danger-lt text-danger">Inactive</a>';
                    $operate .= '<li>
                        <a class="dropdown-item update_active_status" href="javascript:void(0)" 
                           data-table="attribute_set" 
                           data-id="' . $row['id'] . '" 
                           data-status="' . $row['status'] . '">
                            <i class="ti ti-eye"></i>Activate
                        </a>
                    </li>';
                }

                $operate .= '
                    </ul>
                </div>';
            } else {
                // For sellers, just show status without actions
                if ($row['status'] == '1') {
                    $tempRow['status'] = '<a class="badge bg-success-lt text-success text-decoration-none">Active</a>';
                } else {
                    $tempRow['status'] = '<a class="badge bg-danger-lt text-danger text-decoration-none">Inactive</a>';
                }
            }
            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['name'];
            $tempRow['text'] = $row['name'];
            if (!$this->ion_auth->is_seller()) {
                $tempRow['operate'] = $operate;
            }
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        if (isset($from_select) && ($from_select != 0 || $from_select != '0')) {
            print_r(json_encode($rows));

        } else {

            print_r(json_encode($bulkData));
        }
    }

    public function add_attributes($data)
    {
        $data = escape_array($data);

        $attr_data = [
            'name' => $data['name'],
            'attribute_set_id' => $data['attribute_set'],
            'status' => '1',
        ];
        if (isset($data['edit_attribute']) && !empty($data['edit_attribute'])) {
            $this->db->set($attr_data)->where('id', $data['edit_attribute'])->update('attributes');
        } else {
            $this->db->insert('attributes', $attr_data);
        }

        $attribute_id = $this->db->get_where('attributes', array('name' => $data['name']))->result_array();

        for ($i = 0; $i < count($data['attribute_value']); $i++) {

            $attr_val = [
                'attribute_id' => $attribute_id[0]['id'],
                'value' => $data['attribute_value'][$i],
                'swatche_type' => $data['swatche_type'][$i],
                'swatche_value' => $data['swatche_value'][$i],
                'status' => '1',
            ];

            if (isset($data['edit_attribute_value']) && !empty($data['edit_attribute_value'])) {
                $this->db->set($attr_val)->where('id', $data['edit_attribute_value'])->update('attribute_values');
            } else {
                $this->db->insert('attribute_values', $attr_val);
            }
        }
    }


    public function get_attribute_list(
        $offset = 0,
        $limit = 10,
        $sort = 'id',
        $order = 'ASC',
        $from_select = 0
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
            $count_res->group_Start();
            $count_res->or_like($multipleWhere);
            $count_res->group_End();
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
            $search_res->group_Start();
            $search_res->or_like($multipleWhere);
            $search_res->group_End();
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
            // Create dropdown menu for operate column
            if (!$this->ion_auth->is_seller()) {
                $operate = '
                <div class="dropdown">
                    <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

                // Edit Attribute
                $operate .= '<li>
                    <a class="dropdown-item edit_btn" href="javascript:void(0)" 
                       data-id="' . $row['id'] . '" 
                       data-url="admin/attributes/">
                        <i class="fa fa-pen me-2"></i>Edit
                    </a>
                </li>';

                // Status actions based on current status
                if ($row['status'] == '1') {
                    $tempRow['status'] = '<a class="badge bg-success-lt text-success text-white">Active</a>';
                    $operate .= '<li>
                        <a class="dropdown-item update_active_status" href="javascript:void(0)" 
                           data-table="attributes" 
                           data-id="' . $row['id'] . '" 
                           data-status="' . $row['status'] . '">
                            <i class="fa fa-eye-slash me-2"></i>Deactivate
                        </a>
                    </li>';
                } else {
                    $tempRow['status'] = '<a class="badge bg-danger-lt text-danger text-white">Inactive</a>';
                    $operate .= '<li>
                        <a class="dropdown-item update_active_status" href="javascript:void(0)" 
                           data-table="attributes" 
                           data-id="' . $row['id'] . '" 
                           data-status="' . $row['status'] . '">
                            <i class="fa fa-eye me-2"></i>Activate
                        </a>
                    </li>';
                }

                $operate .= '
                    </ul>
                </div>';
            } else {
                // For sellers, just show status without actions
                if ($row['status'] == '1') {
                    $tempRow['status'] = '<a class="badge bg-success-lt text-success text-white">Active</a>';
                } else {
                    $tempRow['status'] = '<a class="badge bg-danger-lt text-danger text-white">Inactive</a>';
                }
            }
            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['name'];
            $tempRow['text'] = $row['name'];
            $tempRow['attribute_set'] = $row['attr_set_name'];
            if (!$this->ion_auth->is_seller()) {
                $tempRow['operate'] = $operate;
            }
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;

        if (isset($from_select) && ($from_select != 0 || $from_select != '0')) {
            print_r(json_encode($rows));

        } else {

            print_r(json_encode($bulkData));
        }

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

        if (isset($data['edit_attribute_value']) && !empty($data['edit_attribute_value'])) {
            $this->db->set($attr_data)->where('id', $data['edit_attribute_value'])->update('attribute_values');
        } else {
            $this->db->insert('attribute_values', $attr_data);
        }

    }


    public function get_attribute_values(
        $offset = 0,
        $limit = 10,
        $sort = " id ",
        $order = 'DESC'
    ) {
        $multipleWhere = '';

        if (isset($_GET['offset']) && !empty($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']) && !empty($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']) && !empty($_GET['sort']))
            $sort = ($_GET['sort'] == 'id') ? "id" : $_GET['sort'];
        if (isset($_GET['order']) && !empty($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) && $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['attr.id' => $search, 'attr.name' => $search, 'attr_vals.value' => $search];
        }

        $count_res = $this->db->select('COUNT(attr_vals.id) as `total`')
            ->join('attributes attr', 'attr.id=attr_vals.attribute_id', 'left');

        // Apply search filter
        if (!empty($multipleWhere)) {
            $count_res->group_Start();
            $count_res->or_like($multipleWhere);
            $count_res->group_End();
        }

        // Check if the user is a seller and apply status filter if true
        if ($this->ion_auth->is_seller()) {
            $count_res->where('attr_vals.status', 1); // Only include active attribute values for sellers
        }

        $attr_count = $count_res->get('attribute_values attr_vals')->result_array();
        $total = isset($attr_count[0]['total']) ? $attr_count[0]['total'] : 0;

        $search_res = $this->db->select('attr_vals.*, attr.name as attr_name')
            ->join('attributes attr', 'attr.id=attr_vals.attribute_id', 'left');

        // Apply search filter again for the actual data query
        if (!empty($multipleWhere)) {
            $search_res->or_like($multipleWhere);
        }

        // Apply status filter for sellers
        if ($this->ion_auth->is_seller()) {
            $search_res->where('attr_vals.status', 1); // Only show active attribute values for sellers
        }

        $city_search_res = $search_res->order_by($sort, $order)
            ->limit($limit, $offset)
            ->get('attribute_values attr_vals')
            ->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($city_search_res as $row) {
            $row = output_escaping($row);
            // print_r($row);
            // Create dropdown menu for operate column
            if (!$this->ion_auth->is_seller()) {
                $operate = '
                <div class="dropdown">
                    <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

                // Edit Attribute Value
                $operate .= '<li>
                    <a class="dropdown-item edit_btn" href="javascript:void(0)" 
                       data-attribute-id="' . $row['attribute_id'] . '" 
                       data-bs-toggle="offcanvas" data-bs-target="#addAttributeValue">
                        <i class="ti ti-pencil me-2"></i>Edit
                    </a>
                </li>';

                // Status actions based on current status
                if ($row['status'] == '1') {
                    $tempRow['status'] = '<a class="badge bg-success-lt text-success text-white">Active</a>';
                    $operate .= '<li>
                        <a class="dropdown-item update_active_status" href="javascript:void(0)" 
                           data-table="attribute_values" 
                           data-id="' . $row['id'] . '" 
                           data-status="' . $row['status'] . '">
                            <i class="ti ti-eye-off me-2"></i>Deactivate
                        </a>
                    </li>';
                } else {
                    $tempRow['status'] = '<a class="badge bg-danger-lt text-danger text-white">Inactive</a>';
                    $operate .= '<li>
                        <a class="dropdown-item update_active_status" href="javascript:void(0)" 
                           data-table="attribute_values" 
                           data-id="' . $row['id'] . '" 
                           data-status="' . $row['status'] . '">
                            <i class="ti ti-eye me-2"></i>Activate
                        </a>
                    </li>';
                }

                $operate .= '
                    </ul>
                </div>';
            } else {
                // For sellers, just show status without actions
                if ($row['status'] == '1') {
                    $tempRow['status'] = '<a class="badge bg-success-lt text-success">Active</a>';
                } else {
                    $tempRow['status'] = '<a class="badge bg-danger-lt text-danger">Inactive</a>';
                }
            }

            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['value'];
            $tempRow['attributes'] = $row['attr_name'];
            $tempRow['swatche_type'] = $row['swatche_type'];
            $tempRow['swatche_value'] = $row['swatche_value'];
            if (!$this->ion_auth->is_seller()) {
                $tempRow['operate'] = $operate;
            }
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    function get_attribute_set($sort = "ats.name", $order = "ASC", $search = "", $offset = NULL, $limit = NULL)
    {
        $multipleWhere = '';
        $where = array();
        if (!empty($search)) {
            $multipleWhere = [
                'ats.`name`' => $search
            ];
        }

        $search_res = $this->db->select('ats.*')->join('attributes a', 'ats.id=a.attribute_set_id')->join('attribute_values av', 'av.attribute_id=a.id');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_start();
            $search_res->or_like($multipleWhere);
            $search_res->group_end();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }
        $attribute_set = $search_res->where("ats.status=1 and a.status=1")->group_by('ats.id')->order_by($sort, $order)->limit($offset, $limit)->get('`attribute_set` ats')->result_array();
        $bulkData = array();
        $bulkData['error'] = (empty($attribute_set)) ? true : false;
        if (!empty($attribute_set)) {
            for ($i = 0; $i < count($attribute_set); $i++) {
                $attribute_set[$i] = output_escaping($attribute_set[$i]);
            }
        }
        $bulkData['data'] = (empty($attribute_set)) ? [] : $attribute_set;
        $bulkData['message'] = (empty($attribute_set)) ? [] : "Attribute Set Retrived Successfully";
        return $bulkData;
    }

    function get_attributes($sort = "a.name", $order = "ASC", $search = "", $attribute_set_id = "", $offset = NULL, $limit = NULL)
    {
        $multipleWhere = '';
        $where = array();
        if (!empty($search)) {
            $multipleWhere = [
                '`a.name`' => $search,
                '`as.name`' => $search
            ];
        }

        $search_res = $this->db->select('a.*,as.name as attribute_set_name')->join('attribute_set as', 'as.id=a.attribute_set_id')->join('attribute_values av', 'av.attribute_id=a.id');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_start();
            $search_res->or_like($multipleWhere);
            $search_res->group_end();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }
        if (isset($attribute_set_id) && !empty($attribute_set_id)) {
            $search_res->where('a.attribute_set_id = ' . $attribute_set_id);
        }
        $attribute_set = $search_res->where("a.status=1 and as.status=1")->group_by('a.id')->order_by($sort, $order)->limit($offset, $limit)->get('attributes a')->result_array();
        $bulkData = array();
        $bulkData['error'] = (empty($attribute_set)) ? true : false;
        $bulkData['message'] = (empty($attribute_set)) ? "Attributes Not Found" : "Attributes Retrivede Successfully";
        if (!empty($attribute_set)) {
            for ($i = 0; $i < count($attribute_set); $i++) {
                $attribute_set[$i] = output_escaping($attribute_set[$i]);
            }
        }
        $bulkData['data'] = (empty($attribute_set)) ? [] : $attribute_set;
        return $bulkData;
    }

    function get_attribute_value($sort = "av.id", $order = "ASC", $search = "", $attribute_id = "", $offset = NULL, $limit = NULL)
    {
        $multipleWhere = '';
        $where = array();
        if (!empty($search)) {
            $multipleWhere = [
                '`a.name`' => $search,
                '`av.value`' => $search,
                '`av.swatche_value`' => $search
            ];
        }
        $search_res = $this->db->select('av.*,a.name as attribute_name')->join('attributes a', 'a.id=av.attribute_id');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_start();
            $search_res->or_like($multipleWhere);
            $search_res->group_end();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }
        if (isset($attribute_id) && !empty($attribute_id)) {
            $search_res->where('av.attribute_id = ' . $attribute_id);
        }
        $attribute_set = $search_res->where("av.status=1 and a.status=1")->group_by('av.id')->order_by($sort, $order)->limit($offset, $limit)->get('attribute_values av')->result_array();
        $bulkData = array();
        $bulkData['error'] = (empty($attribute_set)) ? true : false;
        $bulkData['message'] = (empty($attribute_set)) ? "Atributes Not Found" : "Attributes Retrived Successfully";
        if (!empty($attribute_set)) {
            for ($i = 0; $i < count($attribute_set); $i++) {
                $attribute_set[$i] = output_escaping($attribute_set[$i]);
            }
        }
        $bulkData['data'] = (empty($attribute_set)) ? [] : $attribute_set;
        return $bulkData;
    }
}
