<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Area_model extends CI_Model
{

    function add_city($data)
    {
        $data = escape_array($data);
        $city_data = [
            'name' => $data['city_name'],
            'minimum_free_delivery_order_amount' => $data['minimum_free_delivery_order_amount'],
            'delivery_charges' => $data['delivery_charges'],
        ];
        if (isset($data['edit_city']) && !empty($data['edit_city'])) {
            $this->db->set($city_data)->where('id', $data['edit_city'])->update('cities');
        } else {
            $this->db->insert('cities', $city_data);
        }
    }
    function add_zipcode($data)
    {
        $data = escape_array($data);
        $zipcode_data = [
            'zipcode' => (isset($data['zipcode']) && !empty($data['zipcode'])) ? $data['zipcode'] : '',
            'city_id' => $data['city'],
            'minimum_free_delivery_order_amount' => $data['minimum_free_delivery_order_amount'],
            'delivery_charges' => $data['delivery_charges'],
        ];
        if (isset($data['edit_zipcode']) && !empty($data['edit_zipcode'])) {
            $this->db->set($zipcode_data)->where('id', $data['edit_zipcode'])->update('zipcodes');
        } else {
            $this->db->insert('zipcodes', $zipcode_data);
        }
    }
    function add_area($data)
    {
        $data = escape_array($data);

        $area_data = [
            'name' => $data['area_name'],
            'city_id' => $data['city'],
            'zipcode_id' => $data['zipcode'],
            'minimum_free_delivery_order_amount' => $data['minimum_free_delivery_order_amount'],
            'delivery_charges' => $data['delivery_charges'],
        ];

        if (isset($data['edit_area']) && !empty($data['edit_area'])) {
            $this->db->set($area_data)->where('id', $data['edit_area'])->update('areas');
        } else {
            $this->db->insert('areas', $area_data);
        }
    }
    function bulk_edit_area($data)
    {
        $data = escape_array($data);

        $area_data = [
            'minimum_free_delivery_order_amount' => $data['bulk_update_minimum_free_delivery_order_amount'],
            'delivery_charges' => $data['bulk_update_delivery_charges'],
        ];
        $this->db->set($area_data)->where('city_id', $data['city'])->update('areas');
    }
    public function get_list($table, $offset = 0, $limit = 10, $sort = 'u.id')
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
            if ($table == 'areas') {
                $multipleWhere = ['areas.id' => $search, 'areas.name' => $search, 'cities.name' => $search, 'areas.minimum_free_delivery_order_amount' => $search, 'areas.delivery_charges' => $search, 'zipcodes.zipcode' => $search];
            } else {
                $multipleWhere = ['cities.name' => $search, 'cities.id' => $search];
            }
        }
        if ($table == 'areas') {
            $count_res = $this->db->select(' COUNT(areas.id) as `total` ')->join('cities', 'areas.city_id=cities.id')->join('zipcodes', 'areas.zipcode_id=zipcodes.id');
        } else {
            $count_res = $this->db->select(' COUNT(id) as `total` ');
        }


        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_Start();
            $count_res->or_like($multipleWhere);
            $count_res->group_End();
        }


        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $city_count = $count_res->get($table)->result_array();

        foreach ($city_count as $row) {
            $total = $row['total'];
        }

        if ($table == 'areas') {
            $search_res = $this->db->select(' areas.* , cities.name as city_name , zipcodes.zipcode as zipcode')->join('cities', 'areas.city_id=cities.id')->join('zipcodes', 'areas.zipcode_id=zipcodes.id');
        } else {
            $search_res = $this->db->select(' * ');
        }

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_Start();
            $search_res->or_like($multipleWhere);
            $count_res->group_End();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $city_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get($table)->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $url = 'manage_' . $table;
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

                // Edit City
                $operate .= '<li>
                    <a class="dropdown-item edit_btn" href="javascript:void(0)" 
                       data-id="' . $row['id'] . '" 
                       data-bs-toggle="offcanvas" 
                       data-bs-target="#addCity">
                        <i class="ti ti-pencil me-2"></i>Edit
                    </a>
                </li>';

                // Divider
                $operate .= '<li><hr class="dropdown-divider"></li>';

                // Delete City
                $operate .= '<li>
                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                       x-data="ajaxDelete({
                           url: base_url + \'admin/Area/delete_city\',
                           id: \'' . $row['id'] . '\',
                           tableSelector: \'#cities_table\',
                           confirmTitle: \'Delete City\',
                           confirmMessage: \'Do you really want to delete this city?\'
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
            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['name'];
            $tempRow['minimum_free_delivery_order_amount'] = $row['minimum_free_delivery_order_amount'];
            $tempRow['delivery_charges'] = $row['delivery_charges'];
            if ($table == 'areas') {
                $tempRow['city_name'] = $row['city_name'];
                $tempRow['zipcode'] = $row['zipcode'];
                $tempRow['minimum_free_delivery_order_amount'] = $row['minimum_free_delivery_order_amount'];
                $tempRow['delivery_charges'] = $row['delivery_charges'];
            }
            if (!$this->ion_auth->is_seller()) {

                $tempRow['operate'] = $operate;
            }
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    function get_zipcode_list()
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
                $sort = "zipcodes.id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']) && !empty($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['`zipcodes.id`' => $search, '`zipcodes.zipcode`' => $search, '`cities.name`' => $search, '`zipcodes.minimum_free_delivery_order_amount`' => $search, '`zipcodes.delivery_charges`' => $search];
        }

        $count_res = $this->db->select(' COUNT(zipcodes.id) as `total`, cities.id as city_id, cities.name as city_name');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_Start();
            $count_res->or_like($multipleWhere);
            $count_res->group_End();
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $tax_count = $count_res->join('cities', 'zipcodes.city_id=cities.id', 'left')->get('zipcodes')->result_array();

        foreach ($tax_count as $row) {
            $total = $row['total'];
        }

        if (!$this->db->field_exists('city_id', 'zipcodes')) {
            $search_res = $this->db->select(' * ');
        } else {
            $search_res = $this->db->select(' zipcodes.* ,cities.name as city_name')->join('cities', 'zipcodes.city_id=cities.id', 'left');
        }
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_Start();
            $search_res->or_like($multipleWhere);
            $search_res->group_End();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $tax_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('zipcodes')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($tax_search_res as $row) {
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

                // Edit Zipcode
                $operate .= '<li>
                    <a class="dropdown-item" href="javascript:void(0)" 
                       data-id="' . $row['id'] . '" 
                       data-city-id="' . $row['city_id'] . '" 
                       data-bs-toggle="offcanvas" 
                       data-bs-target="#addZipcode" 
                       role="button" 
                       aria-controls="addZipcode">
                        <i class="ti ti-pencil me-2"></i>Edit
                    </a>
                </li>';

                // Divider
                $operate .= '<li><hr class="dropdown-divider"></li>';

                // Delete Zipcode
                $operate .= '<li>
                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                       x-data="ajaxDelete({
                           url: base_url + \'admin/Area/delete_zipcode\',
                           id: \'' . $row['id'] . '\',
                           tableSelector: \'#zipcode-table\',
                           confirmTitle: \'Delete Zipcode\',
                           confirmMessage: \'Do you really want to delete this zipcode?\'
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
            $tempRow['id'] = $row['id'];
            $tempRow['zipcode'] = $row['zipcode'];
            $tempRow['city_id'] = $row['city_id'];
            if (!$this->db->field_exists('city_id', 'zipcodes')) {
                $tempRow['city_name'] = '';
                $tempRow['minimum_free_delivery_order_amount'] = 0;
                $tempRow['delivery_charges'] = 0;
            } else {
                $tempRow['city_name'] = $row['city_name'];
                $tempRow['minimum_free_delivery_order_amount'] = $row['minimum_free_delivery_order_amount'];
                $tempRow['delivery_charges'] = $row['delivery_charges'];
            }
            if (!$this->ion_auth->is_seller()) {
                $tempRow['operate'] = $operate;
            }
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }


    function get_zipcodes($search = '', $limit = NULL, $offset = NULL, $seller_id = '')
    {
        $where = [];
        $zipcodes = [];
        //Fetch serviceable zipcodes from seller_data
        if (!empty($seller_id)) {
            $seller = $this->db->select('deliverable_zipcode_type,serviceable_zipcodes')
                ->where('user_id', $seller_id)
                ->get('seller_data')
                ->row_array();

            if (!empty($seller)) {
                $zipcode_type = $seller['deliverable_zipcode_type'];
                $seller_zipcodes = !empty($seller['serviceable_zipcodes'])
                    ? explode(',', $seller['serviceable_zipcodes'])
                    : [];

                

                if ($zipcode_type == 2 && !empty($seller_zipcodes)) { // INCLUDED
                    $this->db->where_in('id', $seller_zipcodes);
                } elseif ($zipcode_type == 3 && !empty($seller_zipcodes)) { // EXCLUDED
                    $this->db->where_not_in('id', $seller_zipcodes);
                }
            }
        }

        //Apply search filter
        if (!empty($search)) {
            $where['zipcode LIKE'] = "%$search%";
        }

        //Get total count
        $this->db->select('COUNT(id) as total')
            ->from('zipcodes')
            ->where($where);

        // Re-apply seller filters for count
        if (!empty($seller_id) && !empty($seller)) {
            if ($zipcode_type == 2 && !empty($seller_zipcodes)) {
                $this->db->where_in('id', $seller_zipcodes);
            } elseif ($zipcode_type == 3 && !empty($seller_zipcodes)) {
                $this->db->where_not_in('id', $seller_zipcodes);
            }
        }

        $total = $this->db->get()->row()->total;



        // Fetch Zipcodes
        $this->db->select('*')->from('zipcodes')->where($where);

        // Re-apply seller filters for results
        if (!empty($seller_id) && !empty($seller)) {
            if ($zipcode_type == 2 && !empty($seller_zipcodes)) {
                $this->db->where_in('id', $seller_zipcodes);
            } elseif ($zipcode_type == 3 && !empty($seller_zipcodes)) {
                $this->db->where_not_in('id', $seller_zipcodes);
            }
        }

        $cat_search_res = $this->db->limit($limit, $offset)->get()->result_array();

        // Prepare Response
        $bulkData = [
            'error' => empty($cat_search_res),
            'message' => empty($cat_search_res) ? 'No serviceable pincodes found' : 'Pincodes retrieved successfully',
            'total' => $total,

            'data' => []
        ];

        foreach ($cat_search_res as $row) {
            $bulkData['data'][] = [
                'id' => output_escaping($row['id']),
                'text' => output_escaping($row['zipcode']),
                'zipcode' => output_escaping($row['zipcode']),
                'city_id' => output_escaping($row['city_id']),
                'minimum_free_delivery_order_amount' => !empty($row['minimum_free_delivery_order_amount']) ? output_escaping($row['minimum_free_delivery_order_amount']) : '',
                'delivery_charges' => !empty($row['delivery_charges']) ? output_escaping($row['delivery_charges']) : '',
            ];

            // $bulkData['data'][] = [
            //     'zipcode' => output_escaping($row['zipcode']),
            // ];
        }


        return $bulkData;
    }

    function get_area_by_city($city_id, $sort = "areas.name", $order = "ASC", $search = "", $limit = '', $offset = '')
    {
        $multipleWhere = '';
        $where = array();
        if (!empty($search)) {
            $multipleWhere = [
                '`z.zipcode`' => $search
            ];
        }
        if ($city_id != '') {
            $where['a.city_id'] = $city_id;
        }
        if ($this->db->field_exists('minimum_free_delivery_order_amount', 'zipcodes')) {

            $search_res = $this->db->select('z.zipcode,z.id as id');
            if (isset($multipleWhere) && !empty($multipleWhere)) {
                $search_res->group_start();
                $search_res->or_like($multipleWhere);
                $search_res->group_end();
            }
            $areas = $search_res->where('city_id', $city_id)->order_by($sort, $order)->limit($limit, $offset)->get('zipcodes z')->result_array();
        } else {
            $search_res = $this->db->select('z.zipcode,z.id as id')->join('zipcodes z', 'z.id=a.zipcode_id');
            if (isset($multipleWhere) && !empty($multipleWhere)) {
                $search_res->group_start();
                $search_res->or_like($multipleWhere);
                $search_res->group_end();
            }
            $areas = $search_res->where('city_id', $city_id)->order_by($sort, $order)->limit($limit, $offset)->get('areas a')->result_array();
        }

        $bulkData = array();
        $bulkData['error'] = (empty($areas)) ? true : false;
        if (!empty($areas)) {
            for ($i = 0; $i < count($areas); $i++) {
                $areas[$i] = output_escaping($areas[$i]);
            }
        }
        $bulkData['data'] = (empty($areas)) ? [] : $areas;
        return $bulkData;
    }

    function get_cities_list($search = "", $limit = 20, $offset = 0, $seller_id = '')
    {
        $where = [];
        $cities = [];
        //Fetch serviceable cities from seller_data
        if (!empty($seller_id)) {
            $seller = $this->db->select('deliverable_city_type, serviceable_cities')
                ->where('user_id', $seller_id)
                ->get('seller_data')
                ->row_array();

            if (!empty($seller)) {
                $city_type = $seller['deliverable_city_type'];
                $seller_cities = !empty($seller['serviceable_cities']) ? explode(',', $seller['serviceable_cities']) : [];

                if ($city_type == '1' && !empty($seller_cities)) { // INCLUDED
                    $this->db->where_in('id', $seller_cities);
                } elseif ($city_type == '0' && !empty($seller_cities)) { // EXCLUDED
                    $this->db->where_not_in('id', $seller_cities);
                }
            }
        }

        //Apply search filter
        if (!empty($search)) {
            $where['name LIKE'] = "%$search%";
        }

        //Get total count
        $this->db->select('COUNT(id) as total')
            ->from('cities')
            ->where($where);

        // Re-apply seller filters for count
        if (!empty($seller_id) && !empty($seller)) {
            if ($city_type == '1' && !empty($seller_cities)) {
                $this->db->where_in('id', $seller_cities);
            } elseif ($city_type == '0' && !empty($seller_cities)) {
                $this->db->where_not_in('id', $seller_cities);
            }
        }

        $total = $this->db->get()->row()->total;

        // Fetch cities
        $this->db->select('*')->from('cities')->where($where);

        // Re-apply seller filters for results
        if (!empty($seller_id) && !empty($seller)) {
            if ($city_type == '1' && !empty($seller_cities)) {
                $this->db->where_in('id', $seller_cities);
            } elseif ($city_type == '0' && !empty($seller_cities)) {
                $this->db->where_not_in('id', $seller_cities);
            }
        }

        $cat_search_res = $this->db->limit($limit, $offset)->get()->result_array();

        $bulkData = array();
        foreach ($cat_search_res as $row) {
            $bulkData[] = [
                'id' => output_escaping($row['id']),
                'text' => output_escaping($row['name'])
            ];
        }

        return $bulkData;
    }

    function get_cities($sort = "name", $order = "ASC", $search = "", $limit = '', $offset = '', $seller_id = '')
    {
        $where = [];
        $seller_cities = [];
        $city_type = null;

        // Fetch serviceable cities from seller_data
        if (!empty($seller_id)) {
            $seller = $this->db->select('deliverable_city_type, serviceable_cities')
                ->where('user_id', $seller_id)
                ->get('seller_data')
                ->row_array();

            if (!empty($seller)) {
                $city_type = $seller['deliverable_city_type']; // 1 = include, 0 = exclude
                $seller_cities = !empty($seller['serviceable_cities'])
                    ? explode(',', $seller['serviceable_cities'])
                    : [];
            }
        }

        // Apply search filter
        if (!empty($search)) {
            $where['name LIKE'] = "%$search%";
        }

        /* ---------- TOTAL COUNT ---------- */
        $this->db->select('COUNT(id) as total')
            ->from('cities')
            ->where($where);

        if (!empty($seller_id) && !empty($seller_cities)) {
            if ($city_type == 2) { // INCLUDED
                $this->db->where_in('id', $seller_cities);
            } elseif ($city_type == 3) { // EXCLUDED
                $this->db->where_not_in('id', $seller_cities);
            }
        }

        $total = $this->db->get()->row()->total;

        /* ---------- FETCH CITIES ---------- */
        $this->db->select('*')
            ->from('cities')
            ->where($where)
            ->order_by($sort, $order);

        if (!empty($seller_id) && !empty($seller_cities)) {
            if ($city_type == 2) { // INCLUDED
                $this->db->where_in('id', $seller_cities);
            } elseif ($city_type == 3) { // EXCLUDED
                $this->db->where_not_in('id', $seller_cities);
            }
        }

        $cat_search_res = $this->db
            ->limit($limit, $offset)
            ->get()
            ->result_array();

        /* ---------- RESPONSE ---------- */
        $bulkData = [
            'error' => empty($cat_search_res),
            'message' => empty($cat_search_res)
                ? 'No serviceable cities found'
                : 'Cities retrieved successfully',
            'total' => $total,
            'data' => []
        ];

        foreach ($cat_search_res as $row) {
            $bulkData['data'][] = [
                'id' => output_escaping($row['id']),
                'name' => output_escaping($row['name']),
                'minimum_free_delivery_order_amount' =>
                    !empty($row['minimum_free_delivery_order_amount'])
                    ? output_escaping($row['minimum_free_delivery_order_amount'])
                    : '',
                'delivery_charges' =>
                    !empty($row['delivery_charges'])
                    ? output_escaping($row['delivery_charges'])
                    : '',
            ];
        }

        return $bulkData;
    }


    function get_zipcode($search = "")
    {
        // Fetch users
        $this->db->select('*');
        $this->db->where("zipcode like '%" . $search . "%'");
        $fetched_records = $this->db->get('zipcodes');
        $zipcodes = $fetched_records->result_array();

        // Initialize Array with fetched data
        $data = array();
        foreach ($zipcodes as $zipcode) {
            $data[] = array("id" => $zipcode['id'], "text" => $zipcode['zipcode']);
        }
        return $data;
    }
    public function get_countries()
    {
        $this->load->helper('file');
        $data = file_get_contents(base_url('countries.sql'));
    }

    public function get_countries_list(
        $offset = 0,
        $limit = 10,
        $sort = 'id',
        $order = 'ASC'
    ) {
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
            $multipleWhere = ['numeric_code' => $search, 'name' => $search, 'currency' => $search];
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

        $attr_count = $count_res->get('countries')->result_array();

        foreach ($attr_count as $row) {
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

        $city_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('countries')->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        foreach ($city_search_res as $row) {
            $row = output_escaping($row);
            $tempRow['id'] = $row['id'];
            $tempRow['numeric_code'] = $row['numeric_code'];
            $tempRow['name'] = $row['name'];
            $tempRow['capital'] = $row['capital'];
            $tempRow['phonecode'] = $row['phonecode'];
            $tempRow['currency'] = $row['currency'];
            $tempRow['currency_name'] = $row['currency_name'];
            $tempRow['currency_symbol'] = $row['currency_symbol'];
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function delete_zipcodes($ids)
    {
        // Example: Delete media items from database where id in $ids array
        $this->db->where_in('id', $ids);
        return $this->db->delete('zipcodes'); // Replace with your actual table name
    }

    public function get_download_zipcodes()
    {
        $zipcodes = $this->db->get('zipcodes')->result_array();
        return $zipcodes;
    }
    public function get_download_cities()
    {
        $cities = $this->db->get('cities')->result_array();
        return $cities;
    }
    public function get_download_countries()
    {
        $countries = $this->db->get('countries')->result_array();
        return $countries;
    }
    function get_city_groups_list($search = "", $limit = 20, $offset = 0, $seller_id = '')
    {
        $where = [];

        if (!empty($search)) {
            $where['group_name LIKE'] = "%$search%";
        }

        /* ---------------- Seller based settings ---------------- */
        $seller_groups = [];
        $deliverable_city_type = 0;

        if (!empty($seller_id)) {

            $seller = $this->db->select('deliverable_city_type, deliverable_cities_group_ids')
                ->where('user_id', $seller_id)
                ->get('seller_data')
                ->row_array();

            $deliverable_city_type = (int) ($seller['deliverable_city_type'] ?? 0);

            if (!empty($seller['deliverable_cities_group_ids'])) {
                $seller_groups = explode(',', $seller['deliverable_cities_group_ids']);
            }

            // 0 = none → show nothing
            if ($deliverable_city_type === 0) {
                return [];
            }

            // 2 = include → no groups selected
            if ($deliverable_city_type === 2 && empty($seller_groups)) {
                return [];
            }
        }

        /* ---------------- Main query ---------------- */
        $this->db->select('id, group_name, delivery_charges')
            ->from('city_groups')
            ->where($where);

        // Seller based filtering
        if (!empty($seller_id)) {

            // 2 = include
            if ($deliverable_city_type === 2) {
                $this->db->where_in('id', $seller_groups);
            }

            // 3 = exclude
            if ($deliverable_city_type === 3 && !empty($seller_groups)) {
                $this->db->where_not_in('id', $seller_groups);
            }

            // 1 = all → no filter needed
        }

        $groups = $this->db->limit($limit, $offset)
            ->get()
            ->result_array();

        if (empty($groups)) {
            return [];
        }

        /* ---------------- Build response ---------------- */
        $bulkData = [];

        foreach ($groups as $group) {

            $cities = $this->db->select('c.name')
                ->join('cities c', 'c.id = city_group_items.city_id')
                ->where('group_id', $group['id'])
                ->get('city_group_items')
                ->result_array();

            $city_names = array_column($cities, 'name');

            $bulkData[] = [
                'id' => (string) $group['id'], // keep as string
                'text' => output_escaping(
                    $group['group_name']
                    . ' (Cities: ' . implode(', ', $city_names)
                    . ', Charges: ' . number_format($group['delivery_charges'], 2) . ')'
                )
            ];
        }

        return $bulkData;
    }

    // function get_city_groups_list($search = "", $limit = 20, $offset = 0, $seller_id = '')
    // {
    //     $where = [];

    //     if (!empty($search)) {
    //         $where['group_name LIKE'] = "%$search%";
    //     }

    //     // Filter by Seller's Groups
    //     $seller_groups = [];
    //     if (!empty($seller_id)) {
    //         $seller = $this->db->select('deliverable_cities_group_ids')
    //             ->where('user_id', $seller_id)
    //             ->get('seller_data')
    //             ->row_array();
    //         if (!empty($seller['deliverable_cities_group_ids'])) {
    //             $seller_groups = explode(',', $seller['deliverable_cities_group_ids']);
    //         }
    //     }

    //     if (!empty($seller_id) && empty($seller_groups)) {
    //         // Seller has no groups assigned, return empty
    //         return [];
    //     }

    //     $this->db->select('id, group_name, delivery_charges')
    //         ->from('city_groups')
    //         ->where($where);

    //     if (!empty($seller_groups)) {
    //         $this->db->where_in('id', $seller_groups);
    //     }

    //     $groups = $this->db->limit($limit, $offset)
    //         ->get()
    //         ->result_array();

    //     $bulkData = [];

    //     foreach ($groups as $group) {

    //         $cities = $this->db->select('c.name')
    //             ->join('cities c', 'c.id = city_group_items.city_id')
    //             ->where('group_id', $group['id'])
    //             ->get('city_group_items')
    //             ->result_array();

    //         $city_names = array_column($cities, 'name');

    //         $bulkData[] = [
    //             'id' => (string) $group['id'], // ✅ string like cities
    //             'text' => output_escaping(
    //                 $group['group_name']
    //                 . ' (Cities: ' . implode(', ', $city_names)
    //                 . ', Charges: ' . number_format($group['delivery_charges'], 2) . ')'
    //             )
    //         ];
    //     }
    //     return $bulkData;
    // }

    function get_zipcode_groups_list($search = "", $limit = 20, $offset = 0, $seller_id = '')
    {
        $seller_groups = [];
        $deliverable_zipcode_type = 0;

        /* ---------------- Seller based settings ---------------- */
        if (!empty($seller_id)) {

            $seller = $this->db->select('deliverable_zipcode_type, deliverable_zipcodes_group_ids')
                ->where('user_id', $seller_id)
                ->get('seller_data')
                ->row_array();

            $deliverable_zipcode_type = (int) ($seller['deliverable_zipcode_type'] ?? 0);

            if (!empty($seller['deliverable_zipcodes_group_ids'])) {
                $seller_groups = explode(',', $seller['deliverable_zipcodes_group_ids']);
            }

            // 0 = none → show nothing
            if ($deliverable_zipcode_type === 0) {
                return [];
            }

            // 2 = include → no groups selected
            if ($deliverable_zipcode_type === 2 && empty($seller_groups)) {
                return [];
            }
        }

        /* ---------------- Base query ---------------- */
        $this->db->select('
        zg.id,
        zg.group_name,
        zg.delivery_charges,
        GROUP_CONCAT(z.zipcode ORDER BY z.zipcode SEPARATOR ", ") AS zipcodes
    ');
        $this->db->from('zipcode_groups zg');
        $this->db->join('zipcode_group_items g', 'g.group_id = zg.id', 'left');
        $this->db->join('zipcodes z', 'z.id = g.zipcode_id', 'left');

        // Seller based filtering
        if (!empty($seller_id)) {

            // 2 = include
            if ($deliverable_zipcode_type === 2) {
                $this->db->where_in('zg.id', $seller_groups);
            }

            // 3 = exclude
            if ($deliverable_zipcode_type === 3 && !empty($seller_groups)) {
                $this->db->where_not_in('zg.id', $seller_groups);
            }

            // 1 = all → no filter needed
        }

        /* ---------------- Search ---------------- */
        if (!empty($search)) {
            $this->db->group_start()
                ->like('zg.group_name', $search)
                ->or_like('z.zipcode', $search)
                ->group_end();
        }

        $this->db->group_by('zg.id');
        $this->db->limit($limit, $offset);

        $groups = $this->db->get()->result_array();

        if (empty($groups)) {
            return [];
        }

        /* ---------------- Response ---------------- */
        $bulkData = [];

        foreach ($groups as $group) {
            $bulkData[] = [
                'id' => (string) $group['id'],
                'text' => output_escaping(
                    $group['group_name']
                    . ' (Zipcodes: ' . ($group['zipcodes'] ?: '-')
                    . ', Charges: ' . number_format($group['delivery_charges'], 2) . ')'
                )
            ];
        }

        return $bulkData;
    }


    public function get_zipcode_groups($search = '', $limit = 25, $offset = 0, $seller_id = '')
    {
        $seller_groups = [];
        $deliverable_zipcode_type = 0;

        /* ---------------- Seller based settings ---------------- */
        if (!empty($seller_id)) {

            $seller = $this->db->select('deliverable_zipcode_type, deliverable_zipcodes_group_ids')
                ->where('user_id', $seller_id)
                ->get('seller_data')
                ->row_array();

            $deliverable_zipcode_type = (int) ($seller['deliverable_zipcode_type'] ?? 0);

            if (!empty($seller['deliverable_zipcodes_group_ids'])) {
                $seller_groups = explode(',', $seller['deliverable_zipcodes_group_ids']);
            }

            // 0 = none → show nothing
            if ($deliverable_zipcode_type === 0) {
                return [
                    'total' => 0,
                    'data' => []
                ];
            }

            // 2 = include → no groups selected
            if ($deliverable_zipcode_type === 2 && empty($seller_groups)) {
                return [
                    'total' => 0,
                    'data' => []
                ];
            }
        }

        /* ---------------- Base query (for total count) ---------------- */
        $this->db->from('zipcode_groups zg');

        // Seller based filtering
        if (!empty($seller_id)) {

            // 2 = include
            if ($deliverable_zipcode_type === 2) {
                $this->db->where_in('zg.id', $seller_groups);
            }

            // 3 = exclude
            if ($deliverable_zipcode_type === 3 && !empty($seller_groups)) {
                $this->db->where_not_in('zg.id', $seller_groups);
            }

            // 1 = all → no filter needed
        }

        if (!empty($search)) {
            $this->db->like('zg.group_name', $search);
        }

        $total = $this->db->count_all_results();

        /* ---------------- Main data query ---------------- */
        $this->db->select('zg.id, zg.group_name, zg.delivery_charges');
        $this->db->from('zipcode_groups zg');

        // Same seller filters again
        if (!empty($seller_id)) {

            if ($deliverable_zipcode_type === 2) {
                $this->db->where_in('zg.id', $seller_groups);
            }

            if ($deliverable_zipcode_type === 3 && !empty($seller_groups)) {
                $this->db->where_not_in('zg.id', $seller_groups);
            }
        }

        if (!empty($search)) {
            $this->db->like('zg.group_name', $search);
        }

        $this->db->limit($limit, $offset);
        $groups = $this->db->get()->result_array();

        if (empty($groups)) {
            return [
                'total' => $total,
                'data' => []
            ];
        }

        /* ---------------- Fetch zipcodes ---------------- */
        $group_ids = array_column($groups, 'id');

        $this->db->select('zgi.group_id, z.id as zipcode_id, z.zipcode');
        $this->db->from('zipcode_group_items zgi');
        $this->db->join('zipcodes z', 'z.id = zgi.zipcode_id', 'left');
        $this->db->where_in('zgi.group_id', $group_ids);

        $zipcode_data = $this->db->get()->result_array();

        $zipcode_map = [];
        foreach ($zipcode_data as $row) {
            $zipcode_map[$row['group_id']][] = [
                'zipcode_id' => $row['zipcode_id'],
                'zipcode' => $row['zipcode'],
            ];
        }

        foreach ($groups as &$group) {
            $group['zipcodes'] = $zipcode_map[$group['id']] ?? [];
        }

        return [
            'total' => $total,
            'data' => $groups
        ];
    }


    public function get_city_groups($search = '', $limit = 25, $offset = 0, $seller_id = '')
    {
        $seller_groups = [];
        $deliverable_city_type = 0;

        /* ---------------- Seller based settings ---------------- */
        if (!empty($seller_id)) {

            $seller = $this->db->select('deliverable_city_type, deliverable_cities_group_ids')
                ->where('user_id', $seller_id)
                ->get('seller_data')
                ->row_array();

            $deliverable_city_type = (int) ($seller['deliverable_city_type'] ?? 0);

            if (!empty($seller['deliverable_cities_group_ids'])) {
                $seller_groups = explode(',', $seller['deliverable_cities_group_ids']);
            }

            // 0 = none → show nothing
            if ($deliverable_city_type === 0) {
                return [
                    'total' => 0,
                    'data' => []
                ];
            }

            // 2 = include → no groups selected
            if ($deliverable_city_type === 2 && empty($seller_groups)) {
                return [
                    'total' => 0,
                    'data' => []
                ];
            }
        }

        /* ---------------- Main query ---------------- */
        $this->db->select('cg.id, cg.group_name, cg.delivery_charges');
        $this->db->from('city_groups cg');

        // Seller city group filtering
        if (!empty($seller_id)) {

            // 2 = include
            if ($deliverable_city_type === 2) {
                $this->db->where_in('cg.id', $seller_groups);
            }

            // 3 = exclude
            if ($deliverable_city_type === 3 && !empty($seller_groups)) {
                $this->db->where_not_in('cg.id', $seller_groups);
            }

            // 1 = all → no filter needed
        }

        // Search
        if (!empty($search)) {
            $this->db->like('cg.group_name', $search);
        }

        /* ---------------- Total count (before limit) ---------------- */
        $total = $this->db->count_all_results('', false);

        // Pagination
        $this->db->limit($limit, $offset);
        $groups = $this->db->get()->result_array();

        if (empty($groups)) {
            return [
                'total' => $total,
                'data' => []
            ];
        }

        /* ---------------- Fetch cities ---------------- */
        $group_ids = array_column($groups, 'id');

        $this->db->select('cgi.group_id, c.id as city_id, c.name as city_name');
        $this->db->from('city_group_items cgi');
        $this->db->join('cities c', 'c.id = cgi.city_id', 'left');
        $this->db->where_in('cgi.group_id', $group_ids);

        $cities = $this->db->get()->result_array();

        $city_map = [];
        foreach ($cities as $row) {
            $city_map[$row['group_id']][] = [
                'city_id' => $row['city_id'],
                'city_name' => $row['city_name']
            ];
        }

        foreach ($groups as &$group) {
            $group['cities'] = $city_map[$group['id']] ?? [];
        }

        return [
            'total' => $total,
            'data' => $groups
        ];
    }

    // public function get_city_groups($search = '', $limit = 25, $offset = 0, $seller_id = '')
    // {
    //     $seller_groups = [];
    //     if (!empty($seller_id)) {
    //         $seller = $this->db->select('deliverable_cities_group_ids')
    //             ->where('user_id', $seller_id)
    //             ->get('seller_data')
    //             ->row_array();

    //         if (!empty($seller['deliverable_cities_group_ids'])) {
    //             $seller_groups = explode(',', $seller['deliverable_cities_group_ids']);
    //         }
    //         if (empty($seller_groups)) {
    //             return [
    //                 'total' => 0,
    //                 'data' => []
    //             ];
    //         }
    //     }

    //     $total = $this->db->count_all('city_groups');

    //     $this->db->select('cg.id, cg.group_name, cg.delivery_charges');
    //     $this->db->from('city_groups cg');

    //     if (!empty($seller_groups)) {
    //         $this->db->where_in('cg.id', $seller_groups);
    //     }

    //     if (!empty($search)) {
    //         $this->db->like('cg.group_name', $search);
    //     }

    //     $this->db->limit($limit, $offset);
    //     $groups = $this->db->get()->result_array();

    //     if (empty($groups)) {
    //         return [
    //             'total' => $total,
    //             'data' => []
    //         ];
    //     }

    //     $group_ids = array_column($groups, 'id');

    //     $this->db->select('cgi.group_id, c.id as city_id, c.name as city_name');
    //     $this->db->from('city_group_items cgi');
    //     $this->db->join('cities c', 'c.id = cgi.city_id', 'left');
    //     $this->db->where_in('cgi.group_id', $group_ids);

    //     $cities = $this->db->get()->result_array();

    //     $city_map = [];
    //     foreach ($cities as $row) {
    //         $city_map[$row['group_id']][] = [
    //             'city_id' => $row['city_id'],
    //             'city_name' => $row['city_name']
    //         ];
    //     }

    //     foreach ($groups as &$group) {
    //         $group['cities'] = $city_map[$group['id']] ?? [];
    //     }

    //     return [
    //         'total' => $total,
    //         'data' => $groups
    //     ];
    // }

}
