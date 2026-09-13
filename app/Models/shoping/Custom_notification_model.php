<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Custom_notification_model extends CI_Model
{

    public function add_custom_notification($data)
    {
        $custom_notification_data = [
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type']
        ];
        if (isset($data['edit_custom_notification']) && !empty($data['edit_custom_notification'])) {
            $this->db->set($custom_notification_data)->where('id', $data['edit_custom_notification'])->update('custom_notifications');
        } else {
            $this->db->insert('custom_notifications', $custom_notification_data);
        }
    }

    public function get_custom_notifications_data($offset = 0, $limit = 10, $sort = 'id', $order = 'ASC')
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
            $count_res->group_Start();
            $count_res->or_like($multipleWhere);
            $count_res->group_End();
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }
        $city_count = $count_res->get('custom_notifications')->result_array();

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

        $city_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('custom_notifications')->result_array();

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

            // Edit Custom Notification
            $operate .= '<li>
                <a class="dropdown-item edit_custom_notification" href="' . base_url("admin/custom_notification?edit_id=" . $row['id']) . '" 
                   data-id="' . $row['id'] . '" 
                   data-bs-toggle="offcanvas" 
                   data-bs-target="#customNotification">
                    <i class="ti ti-pencil me-2"></i>Edit
                </a>
            </li>';

            // Divider
            $operate .= '<li><hr class="dropdown-divider"></li>';

            // Delete Custom Notification
            $operate .= '<li>
                <a class="dropdown-item text-danger" href="javascript:void(0)"
                   x-data="ajaxDelete({
                       url: base_url + \'admin/custom_notification/delete_custom_notification\',
                       id: \'' . $row['id'] . '\',
                       tableSelector: \'#custom_notification_table\',
                       confirmTitle: \'Delete Custom Notification\',
                       confirmMessage: \'Do you really want to delete this custom notification?\'
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
            $tempRow['type'] = ucwords(str_replace('_', " ", $row['type']));
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
}
