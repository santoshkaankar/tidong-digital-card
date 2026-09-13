<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Pickup_location_model extends CI_Model
{

    function add_pickup_location($data)
    {
        $data = escape_array($data);
        $pickup_location_data = [
            'seller_id' => $data['seller_id'],
            'pickup_location' => $data['pickup_location'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'address_2' => $data['address2'],
            'city' => $data['city'],
            'state' => $data['state'],
            'country' => $data['country'],
            'pin_code' => $data['pincode'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
        ];
        if (isset($data['edit_pickup_location']) && !empty($data['edit_pickup_location'])) {
            $this->db->set($pickup_location_data)->where('id', $data['edit_pickup_location'])->update('pickup_locations');
        } else {
            //    send add_pickup_location request in shiprocket

            $this->load->library(['Shiprocket']);
            $shiprocket_address = $this->shiprocket->add_pickup_location($pickup_location_data);
            if (isset($shiprocket_address['success']) && !empty($shiprocket_address['success']) && ($shiprocket_address['success'] == 1 || $shiprocket_address['success'] == '1')) {
                $res = $this->db->insert('pickup_locations', $pickup_location_data);
            } else {
                $response['message'] = (isset($shiprocket_address['message']) && !empty($shiprocket_address['message'])) ? $shiprocket_address['message'] : "please check your shiprocket credentials and try again";
                $response['error'] = true;
                return $response;
            }
        }
    }

    public function get_list($table, $where = NULL, $seller_id = 0, $from_app = false)
    {

        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $multipleWhere = '';
        $where = [];

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_POST['offset']))
            $offset = $_POST['offset'];

        if (isset($_GET['limit']))
            $limit = $_GET['limit'];
        if (isset($_POST['limit']))
            $limit = $_POST['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_POST['sort']))
            if ($_POST['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_POST['sort'];
            }

        if (isset($_GET['order']))
            $order = $_GET['order'];
        if (isset($_POST['order']))
            $order = $_POST['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            if ($table == 'pickup_locations') {
                $multipleWhere = ['pickup_locations.id' => $search, 'pickup_locations.pickup_location' => $search, 'pickup_locations.email' => $search, 'pickup_locations.phone' => $search, 'pickup_locations.name' => $search];
            }
        }
        if (isset($_POST['search']) and $_POST['search'] != '') {
            $search = $_POST['search'];
            if ($table == 'pickup_locations') {
                $multipleWhere = ['pickup_locations.id' => $search, 'pickup_locations.pickup_location' => $search, 'pickup_locations.email' => $search, 'pickup_locations.phone' => $search, 'pickup_locations.name' => $search];
            }
        }
        if (isset($_GET['seller_id']) and $_GET['seller_id'] != '') {
            $where = ['seller_id' => $_GET['seller_id']];
        }
        if (isset($seller_id) && $seller_id != 0) {
            $where = ['seller_id' => $seller_id];
        }
        if (isset($_GET['verified_status']) && $_GET['verified_status'] !== '') {
            $where['status'] = $_GET['verified_status'];
        }

        $count_res = $this->db->select(' COUNT(id) as `total` ');
        



        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $count_res->or_like($multipleWhere);
            $this->db->group_End();
        }


        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $city_count = $count_res->get($table)->result_array();

        foreach ($city_count as $row) {
            $total = $row['total'];
        }


        $search_res = $this->db->select(' * ');



        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_Start();
            $search_res->or_like($multipleWhere);
            $this->db->group_End();
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
            if ($this->ion_auth->is_admin()) {
                $operate = '
                <div class="dropdown">
                    <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

                // Edit Pickup Location
                $operate .= '<li>
                    <a class="dropdown-item" href="javascript:void(0)" 
                       data-id="' . $row['id'] . '" 
                       data-bs-toggle="offcanvas" 
                       data-bs-target="#editPickupLocation">
                        <i class="ti ti-pencil me-2"></i>Edit
                    </a>
                </li>';

                // Status actions based on current status
                if ($row['status'] == '1') {
                    $verify = '<a class="badge badge-success bg-success-lt">Active</a>';
                    $operate .= '<li>
                        <a class="dropdown-item update_active_status" href="javascript:void(0)" 
                           data-table="pickup_locations" 
                           data-id="' . $row['id'] . '" 
                           data-status="' . $row['status'] . '">
                            <i class="ti ti-toggle-right me-2"></i>Deactivate
                        </a>
                    </li>';
                } else {
                    $verify = '<a class="badge badge-danger bg-danger-lt">Inactive</a>';
                    $operate .= '<li>
                        <a class="dropdown-item update_active_status" href="javascript:void(0)" 
                           data-table="pickup_locations" 
                           data-id="' . $row['id'] . '" 
                           data-status="' . $row['status'] . '">
                            <i class="ti ti-toggle-left me-2"></i>Activate
                        </a>
                    </li>';
                }

                // Divider
                $operate .= '<li><hr class="dropdown-divider"></li>';

                // Delete Pickup Location
                $operate .= '<li>
                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                       x-data="ajaxDelete({
                           url: base_url + \'admin/pickup_location/delete_pickup_location\',
                           id: \'' . $row['id'] . '\',
                           table: \'pickup_locations\',
                           tableSelector: \'#pickup_location_table\',
                           confirmTitle: \'Delete Pickup Location\',
                           confirmMessage: \'Do you really want to delete this pickup location?\'
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

            $seller_name = fetch_details('users', ['id' => $row['seller_id']], 'username');

            // $seller_name = fetch_users($row['seller_id']);
            // print_r($seller_name);

            $tempRow['id'] = $row['id'];
            $tempRow['seller_id'] = $row['seller_id'];
            $tempRow['seller_name'] = !empty($seller_name) ? $seller_name[0]['username'] : '-';
            $tempRow['pickup_location'] = $row['pickup_location'];
            $tempRow['name'] = $row['name'];
            $tempRow['email'] = $row['email'];
            $tempRow['phone'] = $row['phone'];
            $tempRow['address'] = $row['address'];
            $tempRow['address2'] = $row['address_2'];
            $tempRow['city'] = $row['city'];
            $tempRow['state'] = $row['state'];
            $tempRow['country'] = $row['country'];
            $tempRow['pin_code'] = $row['pin_code'];
            $tempRow['latitude'] = $row['latitude'];
            $tempRow['longitude'] = $row['longitude'];

            if ($this->ion_auth->is_admin()) {
                $tempRow['verified'] = $verify;
                $tempRow['operate'] = $operate;
            } else {

                if ($row['status'] == '1') {
                    $tempRow['status'] = '<span class="badge badge-success bg-success-lt">Success</span>';
                } else {
                    $tempRow['status'] = '<span class="badge badge-primary bg-primary-lt">pending</span>';
                }
            }
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        if ($from_app == true) {
            return $bulkData;
        } else {
            print_r(json_encode($bulkData));
        }
    }
}
