<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Notification_model extends CI_Model
{

    public function add_notification($data)
    {
        $data = escape_array($data);
        $notification_data = array(
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type'],
            'send_to' => $data['send_to'],
            'users_id' => (isset($data['users_id']) && !empty($data['users_id'])) ? $data['users_id'] : 0,
        );

        if (isset($data['type']) && $data['type'] == 'categories') {
            $notification_data['type_id'] = $data['category_id'];
        }
        if (isset($data['type']) && $data['type'] == 'products') {
            $notification_data['type_id'] = $data['product_id'];
        }
        if (isset($data['type']) && $data['type'] == 'notification_url') {
            $notification_data['link'] = $data['link'];
        }
        if (isset($data['send_to']) && $data['send_to'] == 'specific_user') {
            $notification_data['users_id'] = stripslashes($data['select_user_id']);
        }

        if (isset($data['image']) && !empty($data['image'])) {
            $notification_data['image'] = $data['image'];
        }

        return $this->db->insert('notifications', $notification_data);
    }

    function get_notifications($offset, $limit, $sort, $order)
    {
        $notification_data = [];
        $count_res = $this->db->select(' COUNT(id) as `total` ')->get('notifications')->result_array();
        $search_res = $this->db->select(' * ')->order_by($sort, $order)->limit($limit, $offset)->get('notifications')->result_array();
        for ($i = 0; $i < count($search_res); $i++) {
            $search_res[$i]['title'] = output_escaping($search_res[$i]['title']);
            $search_res[$i]['message'] = output_escaping($search_res[$i]['message']);
            $search_res[$i]['send_to'] = output_escaping($search_res[$i]['send_to']);
            $search_res[$i]['users_id'] = output_escaping($search_res[$i]['users_id']);
            $search_res[$i]['link'] = (isset($search_res[$i]['link']) && !empty($search_res[$i]['link']) ? $search_res[$i]['link'] : '');
            if (empty($search_res[$i]['image'])) {
                $search_res[$i]['image'] = '';
            } else {
                if (file_exists(FCPATH . $search_res[$i]['image']) == FALSE) {
                    $search_res[$i]['image'] = base_url() . NO_IMAGE;
                } else {
                    $search_res[$i]['image'] = base_url() . $search_res[$i]['image'];
                }
            }
        }
        $notification_data['total'] = $count_res[0]['total'];
        $notification_data['data'] = $search_res;
        return $notification_data;
    }
    public function get_notifications_data($offset = 0, $limit = 10, $sort = 'read_by', $order = 'DESC')
    {

        $multipleWhere = '';
        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'read_by') {
                $sort = "read_by";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['id' => $search, 'title' => $search, 'message' => $search];
        }

        if (isset($_GET['message_type']) && ($_GET['message_type'] != '')) {
            $where = ('read_by =' . $_GET['message_type']);
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
        $city_count = $count_res->get('system_notification')->result_array();

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

        $city_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('system_notification')->result_array();

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

            // View action based on type
            if (isset($row['type']) && $row['type'] == 'return_request') {
                $operate .= '<li>
                    <a class="dropdown-item" href="' . base_url('admin/return-request') . '?edit_id=' . $row['type_id'] . '&noti_id=' . $row['id'] . '">
                        <i class="ti ti-eye me-2"></i>View Return Request
                    </a>
                </li>';
            } else {
                $operate .= '<li>
                    <a class="dropdown-item" href="' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['type_id'] . '&noti_id=' . $row['id'] . '">
                        <i class="ti ti-eye me-2"></i>View Order
                    </a>
                </li>';
            }

            // Divider
            $operate .= '<li><hr class="dropdown-divider"></li>';

            // Delete Notification
            $operate .= '<li>
                <a class="dropdown-item text-danger" href="javascript:void(0)"
                   x-data="ajaxDelete({
                       url: base_url + \'admin/Notification_settings/delete_system_notification\',
                       id: \'' . $row['id'] . '\',
                       tableSelector: \'#system_notofication_table\',
                       confirmTitle: \'Delete System Notification\',
                       confirmMessage: \'Do you really want to delete this Notification?\'
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
            $tempRow['message'] = $row['message'];
            $tempRow['type'] = str_replace('_', ' ', $row['type']);
            $tempRow['type_id'] = $row['type_id'];
            $tempRow['read_by'] = ($row['read_by'] == 1) ? '<label class="badge badge-primary bg-primary-lt">Read</label>' : '<label class="badge badge-danger bg-danger-lt">Un-Read</label>';
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
    public function get_notification_list($offset = 0, $limit = 10, $sort = 'id', $order = 'DESC')
    {

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
            $multipleWhere = ['id' => $search, 'title' => $search, 'message' => $search];
        }

        $count_res = $this->db->select(' COUNT(id) as `total` ');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }
        $city_count = $count_res->get('notifications')->result_array();

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

        $city_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('notifications')->result_array();
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
                <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">
                    <li>
                        <a class="dropdown-item text-danger" href="javascript:void(0)"
                           x-data="ajaxDelete({
                               url: base_url + \'admin/Notification_settings/delete_notification\',
                               id: \'' . $row['id'] . '\',
                               tableSelector: \'#send_notification_table\',
                               confirmTitle: \'Delete Notification\',
                               confirmMessage: \'Do you really want to delete this notification?\'
                           })"
                           @click="deleteItem">
                            <i class="ti ti-trash me-2"></i>Delete
                        </a>
                    </li>
                </ul>
            </div>';

            $tempRow['id'] = $row['id'];
            $tempRow['title'] = $row['title'];
            $tempRow['type'] = $row['type'];
            $tempRow['type_id'] = $row['type_id'];
            if ($row['type'] == 'products') {
                $tempRow['type_name'] = fetch_details('products', ['id' => $row['type_id']], 'name')[0]['name'];
            } elseif ($row['type'] == 'categories') {
                $tempRow['type_name'] = fetch_details('categories', ['id' => $row['type_id']], 'name')[0]['name'];
            } else {
                $tempRow['type_name'] = '';
            }

            $tempRow['message'] = $row['message'];
            $tempRow['send_to'] = ucwords(str_replace('_', " ", $row['send_to']));

            $user_names = array();

            if (isset($row['users_id']) && $row['users_id'] != 0 && !empty($row['users_id'])) {
                $json_array = $row['users_id'];

                // Decode the JSON array to a PHP array
                $users = json_decode($json_array, true);
                foreach ($users as $user_id) {
                    $username = fetch_details('users', ['id' => $user_id], 'username');
                    if (!empty($username)) {
                        $user_names[] = $username[0]['username'];
                    }
                }
            }

            $tempRow['users_id'] = implode(' , ', $user_names);

            if (empty($row['image'])) {
                $row['image'] = '';
            } else {
                if (file_exists(FCPATH . $row['image']) == FALSE) {
                    $row['image'] = base_url() . NO_IMAGE;
                } else {
                    $row['image'] = base_url() . $row['image'];
                }
            }
            $tempRow['image_src'] = $row['image'];
            $tempRow['image'] = "<div class='mx-auto product-image image-box-100 h-100'><a href='" . $row['image'] . "' data-lightbox='notification' >
      <img class='rounded'  src='" . $row['image'] . "'></a></div>";
            $full_notification = '<div class="d-flex"><div class="' . (!empty($row['image']) ? "" : "d-none") . '">' . $tempRow['image'] . '</div><div class="ms-2"><b class="m-0">' . $row['title'] . '</b><p class="m-0">' . $row['message'] . '</p></div></div>';
            $tempRow['full_notification'] = $full_notification;

            $tempRow['link'] = !empty($row['link']) && $row['link'] != 'NULL' ? $row['link'] : '';
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
    public function mark_all_as_read()
    {
        if (update_details(['read_by' => '1'], ['read_by' => 0], 'system_notification')) {
            $response_data['error'] = false;
            $response_data['message'] = 'All notifications marked as read successfully.';
        } else {
            $response_data['error'] = true;
            $response_data['message'] = 'Opps! Something went wrong.';
        }
        print_r(json_encode($response_data));
    }
}
