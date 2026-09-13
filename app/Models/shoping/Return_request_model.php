<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Return_request_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    function get_return_request_list($status_filter = NULL)
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $multipleWhere = '';
        $where = [];

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

        if (isset($_GET['order_id']))
            $order_id = $_GET['order_id'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = ['rr.`id`' => $search, 'oi.`order_id`' => $search, 'u.`username`' => $search, 'u.`email`' => $search, 'u.`mobile`' => $search, 'p.`name`' => $search, 'oi.`price`' => $search,];
        }

        // Add status filter
        if (isset($status_filter) && $status_filter !== '') {
            $where['rr.status'] = $status_filter;
        }

        // Add seller filter
        if (isset($_GET['seller_filter']) && $_GET['seller_filter'] != '') {
            $where['oi.seller_id'] = $_GET['seller_filter'];
        }

        $count_res = $this->db->select(' COUNT(rr.id) as `total` ')->join('users u', 'u.id=rr.user_id')->join('products p', 'p.id=rr.product_id')->join('order_items oi', 'oi.id=rr.order_item_id');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_Start();
            $count_res->or_where($multipleWhere);
            $count_res->group_End();
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }
        if (isset($order_id) && !empty($order_id)) {
            $count_res->where("oi.order_id", $order_id);
        }

        $request_count = $count_res->get('return_requests rr')->result_array();

        foreach ($request_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' rr.id,rr.remarks,rr.return_reason, rr.return_item_image, oi.order_id, oi.seller_id,oi.delivery_boy_id, u.id as user_id,u.username as username ,p.name as product_name,oi.price,oi.discounted_price,oi.id as order_item_id,oi.quantity,oi.sub_total,oi.variant_name,rr.status')
            ->join('users u', 'u.id=rr.user_id')
            ->join('products p', 'p.id=rr.product_id')
            ->join('order_items oi', 'oi.id=rr.order_item_id');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_Start();
            $search_res->or_like($multipleWhere);
            $search_res->group_End();
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }
        if (isset($order_id) && !empty($order_id)) {
            $search_res->where("oi.order_id", $order_id);
        }

        $offer_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('return_requests rr')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $currency = get_settings('currency');

        foreach ($offer_search_res as $row) {

            $row = output_escaping($row);

            if (isset($row['return_item_image']) && !empty($row['return_item_image'])) {

                $return_images = explode(',', $row['return_item_image']);

                $return_image = '';

                // Output <img> tags
                foreach ($return_images as $url) {
                   $return_image .= '<div class="d-flex justify-content-center align-items-center">
    <img src="' . base_url($url) . '" alt="Return Image" class="img-responsive p-0"
         style="width:50px; height:100%; margin-right:5px; object-fit:cover;">
</div>';

                }
            } else {
             $return_image = '
<div class="d-flex justify-content-center align-items-center">
    <img src="' . base_url() . NO_IMAGE . '" 
         alt="Return Image" 
         class="img-responsive p-0" 
         style="width:50px; height:50px; object-fit:cover; margin-right:5px; border-radius:4px;">
</div>';

            }

            // Create dropdown menu for operate column
            $operate = '
            <div class="dropdown">
                <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

            // Edit Return Request
            $operate .= '<li>
                <a class="dropdown-item edit_request edit_return_request" href="javascript:void(0)" 
                   data-id="' . $row['order_item_id'] . '" 
                   data-delivery-boy-id="' . $row['delivery_boy_id'] . '" 
                   data-bs-target="#updateReturnRequest" 
                   data-bs-toggle="offcanvas">
                    <i class="ti ti-pencil me-2"></i>Edit
                </a>
            </li>';

            $operate .= '
                </ul>
            </div>';

            $seller_name = fetch_details('users', ['id' => $row['seller_id']], 'username');

            // $seller_name = fetch_users($row['seller_id']);
            // print_r($seller_name);
            $tempRow['id'] = $row['id'];
            $tempRow['user_id'] = $row['user_id'];
            $tempRow['user_name'] = $row['username'];
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['seller_id'] = $row['seller_id'];
            $tempRow['seller_name'] = $seller_name[0]['username'];
            $tempRow['delivery_boy_id'] = $row['delivery_boy_id'];
            $tempRow['order_item_id'] = $row['order_item_id'];
            $tempRow['product_name'] = $row['product_name'];
            $tempRow['variant_name'] = !empty($row['variant_name']) ? $row['variant_name'] : '-';
            $tempRow['return_reason'] = $row['return_reason'];
            $tempRow['return_item_image'] = $return_image;
            $tempRow['price'] = $currency . ' ' . $row['price'];
            // $tempRow['discounted_price'] = $currency . ' ' . $row['discounted_price'];
            $tempRow['quantity'] = $row['quantity'];
            $tempRow['sub_total'] = $currency . ' ' . $row['sub_total'];
            $tempRow['status_digit'] = $row['status'];
            $status = [
                '0' => '<span class="badge badge-warning bg-warning-lt">Pending</span>',
                '1' => '<span class="badge badge-primary bg-primary-lt">Approved</span>',
                '2' => '<span class="badge badge-danger bg-danger-lt">Rejected</span>',
                '8' => '<span class="badge badge-secondary bg-secondary-lt">Return Pickedup</span>',
                '3' => '<span class="badge badge-success bg-success-lt">Returned</span>',
            ];

            $tempRow['status'] = $status[$row['status']];
            $tempRow['remarks'] = $row['remarks'];
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
    function update_return_request($data)
    {
        $data = escape_array($data);
        $request = array(
            'status' => $data['status'],
            'remarks' => (isset($data['update_remarks']) && !empty($data['update_remarks'])) ? $data['update_remarks'] : null,
        );
        $item_id = $data['order_item_id'];
        $return_request_current_status = fetch_details('return_requests', ['id' => $data['return_request_id']], 'status')[0]['status'];
    
        $firebase_project_id = get_settings('firebase_project_id');
        $service_account_file = get_settings('service_account_file');
        $data += fetch_details('order_items', ['id' => $data['order_item_id']], 'product_variant_id,quantity,user_id,active_status');
        $active_status = $data[0]['active_status'] ?? "";

        $new_status = $data['status']; // numeric status

        // Map numeric statuses to keywords
        $status_map = [
            '0' => 'pending',
            '1' => 'return_request_approved',
            '2' => 'return_request_decline',
            '8' => 'return_pickedup',
            '3' => 'returned'
        ];

        $current_status_text = $active_status;
        $new_status_text = isset($status_map[$new_status]) ? $status_map[$new_status] : '';

        // --- Prevent invalid backward moves ---
        // If current status is Approved or Rejected, cannot go back to Pending
        if (
            in_array($current_status_text, ['return_request_approved', 'return_request_decline']) &&
            $new_status == '0'
        ) {
            return [
                'error' => true,
                'message' => "You cannot set status back to Pending once it's Approved or Rejected.",
            ];
        }
        if(
            $return_request_current_status == "8" &&
            $new_status == '0'
        ){
            return [
                'error' => true,
                'message' => "You cannot set status back to Pending once it's Picked Up.",
            ];
        }
       
        // If trying to mark Returned but not yet Picked Up
        if (
            $new_status == '3' && // Returned
            $current_status_text != 'return_pickedup'
        ) {
            return [
                'error' => true,
                'message' => "Item Is Already Returned",
            ];
        }

        // Prevent duplicate updates (already approved, rejected, or returned)
        if ($current_status_text == "return_request_approved") {
            return [
                'error' => true,
                'message' => "This Item is already Approved.",
            ];
        }
        if ($current_status_text == "return_request_decline") {
            return [
                'error' => true,
                'message' => "This Item is already Rejected.",
            ];
        }
        if ($current_status_text == "returned") {
            return [
                'error' => true,
                'message' => "This Item is already Returned.",
            ];
        }
     

        $this->db->where('id', $data['return_request_id'])->update('return_requests', $request);

        if ($data['status'] == '3') {

            $this->load->model('order_model');
            process_refund($data['order_item_id'], 'returned');
            update_stock($data[0]['product_variant_id'], $data[0]['quantity'], 'plus');
            $this->order_model->update_order_item($item_id, 'returned', 1);

            //for delivery boy notification
            $order_item_res = fetch_details('order_items', ['id' => $item_id], 'order_id');
            $delivery_boy_id = $data['delivery_boy_id'];
            $cutomer_id = $data[0]['user_id'];
            $settings = get_settings('system_settings', true);
            $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
            $delivery_boy_res = fetch_details('users', ['id' => $delivery_boy_id], 'username,fcm_id,email,mobile,platform_type');
            $customer_res = fetch_details('users', ['id' => $cutomer_id], 'username,fcm_id,email,mobile,platform_type');
            $fcm_ids = array();
            //custom message

            $custom_notification = fetch_details('custom_notifications', ['type' => "customer_order_returned"], '');
            $hashtag_cutomer_name = '< cutomer_name >';
            $hashtag_order_id = '< order_item_id >';
            $hashtag_application_name = '< application_name >';
            $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
            $hashtag = html_entity_decode($string);
            $data1 = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($customer_res[0]['username'], $order_item_res[0]['order_id'], $app_name), $hashtag);
            $message = output_escaping(trim($data1, '"'));
            $delivery_boy_msg = 'Hello Dear ' . $customer_res[0]['username'] . ' ' . 'you have new order to be pickup order ID #' . $order_item_res[0]['order_id'] . ' please take note of it! Thank you. Regards ' . $app_name . '';
            $customer_msg = (!empty($custom_notification)) ? $message : 'Hello Dear ' . $customer_res[0]['username'] . ',your return of order item id' . $item_id . ' is Picked Successfully';

            (notify_event(
                "customer_order_returned",
                ["customer" => [$customer_res[0]['email']]],
                ["customer" => [$customer_res[0]['mobile']]],
                ["users.mobile" => $customer_res[0]['mobile']]
            ));

            if (!empty($delivery_boy_res[0]['fcm_id']) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                $fcmMsg = array(
                    'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "You have new order to deliver",
                    'body' => $delivery_boy_msg,
                    'type' => "order",
                );
                // Step 1: Group by platform
                $groupedByPlatform = [];
                foreach ($delivery_boy_res as $item) {
                    $platform = $item['platform_type'];
                    $groupedByPlatform[$platform][] = $item['fcm_id'];
                }

                // Step 2: Chunk each platform group into arrays of 1000
                $delivery_boy_fcm_ids = [];
                foreach ($groupedByPlatform as $platform => $fcmIds) {
                    $delivery_boy_fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                }

                $delivery_boy_fcm_ids[0][] = $delivery_boy_fcm_ids;
                send_notification($fcmMsg, $delivery_boy_fcm_ids, $fcmMsg);
            }
            if (!empty($customer_res[0]['fcm_id']) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                $fcmMsg = array(
                    'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                    'body' => $customer_msg,
                    'type' => "order",
                );


                // Step 1: Group by platform
                $groupedByPlatform = [];
                foreach ($customer_res as $item) {
                    $platform = $item['platform_type'];
                    $groupedByPlatform[$platform][] = $item['fcm_id'];
                }

                // Step 2: Chunk each platform group into arrays of 1000
                $fcm_ids = [];
                foreach ($groupedByPlatform as $platform => $fcmIds) {
                    $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                }

                $fcm_ids[0][] = $fcm_ids;
                send_notification($fcmMsg, $fcm_ids, $fcmMsg);
            }
        } elseif ($data['status'] == '2') {

            $this->load->model('order_model');
            $this->order_model->update_order_item($item_id, 'return_request_decline', 1);
            //for delivery boy notification
            $order_item_res = fetch_details('order_items', ['id' => $item_id], 'order_id');

            $cutomer_id = $data[0]['user_id'];
            $settings = get_settings('system_settings', true);
            $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
            $customer_res = fetch_details('users', ['id' => $cutomer_id], 'username,fcm_id,email,mobile,username,platform_type');
            $fcm_ids = array();
            //custom message

            $custom_notification = fetch_details('custom_notifications', ['type' => "customer_order_returned_request_approved"], '');
            $hashtag_cutomer_name = '< cutomer_name >';
            $hashtag_order_id = '< order_item_id >';
            $hashtag_application_name = '< application_name >';
            $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
            $hashtag = html_entity_decode($string);
            $data1 = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($customer_res[0]['username'], $order_item_res[0]['order_id'], $app_name), $hashtag);
            $message = output_escaping(trim($data1, '"'));
            $customer_msg = (!empty($custom_notification)) ? $message : 'Hello Dear ' . $customer_res[0]['username'] . ',your return request of order item id' . $item_id . ' has been declined';

            (notify_event(
                "customer_order_returned_request_approved",
                ["customer" => [$customer_res[0]['email']]],
                ["customer" => [$customer_res[0]['mobile']]],
                ["users.mobile" => $customer_res[0]['mobile']]
            ));


            if (!empty($customer_res[0]['fcm_id']) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                $fcmMsg = array(
                    'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                    'body' => $customer_msg,
                    'type' => "order",
                );

                // Step 1: Group by platform
                $groupedByPlatform = [];
                foreach ($customer_res as $item) {
                    $platform = $item['platform_type'];
                    $groupedByPlatform[$platform][] = $item['fcm_id'];
                }

                // Step 2: Chunk each platform group into arrays of 1000
                $fcm_ids = [];
                foreach ($groupedByPlatform as $platform => $fcmIds) {
                    $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                }

                $fcm_ids[0][] = $fcm_ids;
                send_notification($fcmMsg, $fcm_ids, $fcmMsg);
            }
        } elseif ($data['status'] == '1') {
            $this->load->model('order_model');
            $this->order_model->update_order_item($item_id, 'return_request_approved', 1);

            update_details(['delivery_boy_id' => $_POST['deliver_by']], ['id' => $item_id], 'order_items');

            //for delivery boy notification
            $order_item_res = fetch_details('order_items', ['id' => $item_id], 'order_id,seller_id,delivery_boy_id');
            $seller_id = $order_item_res[0]['seller_id'];
            $delivery_boy_id = $order_item_res[0]['delivery_boy_id'];

            $cutomer_id = $data[0]['user_id'];
            $settings = get_settings('system_settings', true);
            $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
            $customer_res = fetch_details('users', ['id' => $cutomer_id], 'username,fcm_id,email,mobile,username, platform_type');
            $seller_res = fetch_details('users', ['id' => $seller_id], 'username,fcm_id,email,mobile,username, platform_type');
            $delivery_boy_res = fetch_details('users', ['id' => $delivery_boy_id], 'username,fcm_id,email,mobile,username, platform_type');
            $fcm_ids = array();
            //custom message

            $custom_notification = fetch_details('custom_notifications', ['type' => "customer_order_returned_request_approved"], '');
            $hashtag_cutomer_name = '< cutomer_name >';
            $hashtag_order_id = '< order_item_id >';
            $hashtag_application_name = '< application_name >';
            $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
            $hashtag = html_entity_decode($string);
            $data1 = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($customer_res[0]['username'], $order_item_res[0]['order_id'], $app_name), $hashtag);
            $message = output_escaping(trim($data1, '"'));
            $customer_msg = (!empty($custom_notification)) ? $message : 'Hello Dear ' . $customer_res[0]['username'] . ',your return request of order item id' . $item_id . ' has been Approved';
            $seller_msg = (!empty($custom_notification)) ? $message : 'Hello Dear ' . $seller_res[0]['username'] . ', return request of order item id' . $item_id . ' has been Approved';
            $delivery_boy_msg = (!empty($custom_notification)) ? $message : 'Hello Dear ' . $delivery_boy_res[0]['username'] . ', return request of order item id' . $item_id . ' has been Approved. Please pickup the item';

            (notify_event(
                "customer_order_returned_request_approved",
                ["customer" => [$customer_res[0]['email']]],
                ["customer" => [$customer_res[0]['mobile']]],
                ["users.mobile" => $customer_res[0]['mobile']]
            ));

            // customer get notification for item return 
            if (!empty($customer_res[0]['fcm_id']) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                $fcmMsg = array(
                    'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                    'body' => $customer_msg,
                    'type' => "order",
                );

                // Step 1: Group by platform
                $groupedByPlatform = [];
                foreach ($customer_res as $item) {
                    $platform = $item['platform_type'];
                    $groupedByPlatform[$platform][] = $item['fcm_id'];
                }

                // Step 2: Chunk each platform group into arrays of 1000
                $fcm_ids = [];
                foreach ($groupedByPlatform as $platform => $fcmIds) {
                    $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                }


                $fcm_ids[0][] = $fcm_ids;
                send_notification($fcmMsg, $fcm_ids, $fcmMsg);
            }

            // Seller get notification for item return 
            if (!empty($seller_res[0]['fcm_id']) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                $fcmMsg = array(
                    'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                    'body' => $seller_msg,
                    'type' => "order",
                );

                // Step 1: Group by platform
                $groupedByPlatform = [];
                foreach ($seller_res as $item) {
                    $platform = $item['platform_type'];
                    $groupedByPlatform[$platform][] = $item['fcm_id'];
                }

                // Step 2: Chunk each platform group into arrays of 1000
                $seller_fcm_ids = [];
                foreach ($groupedByPlatform as $platform => $fcmIds) {
                    $seller_fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                }

                $seller_fcm_ids[0][] = $seller_fcm_ids;
                send_notification($fcmMsg, $seller_fcm_ids, $fcmMsg);
            }

            // Delivery boy get notification for item return 
            if (!empty($delivery_boy_res[0]['fcm_id']) && isset($firebase_project_id) && isset($service_account_file) && !empty($firebase_project_id) && !empty($service_account_file)) {
                $fcmMsg = array(
                    'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Return Request Approved",
                    'body' => $delivery_boy_msg,
                    'type' => "order",
                );

                // Step 1: Group by platform
                $groupedByPlatform = [];
                foreach ($delivery_boy_res as $item) {
                    $platform = $item['platform_type'];
                    $groupedByPlatform[$platform][] = $item['fcm_id'];
                }

                // Step 2: Chunk each platform group into arrays of 1000
                $delivery_boy_fcm_ids = [];
                foreach ($groupedByPlatform as $platform => $fcmIds) {
                    $delivery_boy_fcm_ids[$platform] = array_chunk($fcmIds, 1000);
                }

                $delivery_boy_fcm_ids[0][] = $delivery_boy_fcm_ids;
                send_notification($fcmMsg, $delivery_boy_fcm_ids, $fcmMsg);
            }
        }
        return [
            'error' => false,
            'message' => "Return request updated successfully",
        ];
    }
    function get_order_details($data)
    {
        $data = escape_array($data);
        $res = fetch_details('order_items', ['id' => $data['order_item_id']]);
        print_r(json_encode($res[0]));
    }
}
