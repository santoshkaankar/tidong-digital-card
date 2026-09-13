<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sales_report_model extends CI_Model
{
    public function get_sales_list(
        $offset = 0,
        $limit = 10,
        $sort = "oi.id",
        $order = 'ASC'
    ) {
        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        if (isset($_GET['search']) && $_GET['search'] != '') {
            $search = $_GET['search'];
            $filters = [
                'u.username' => $search,
                'u.email' => $search,
                'u.mobile' => $search,
                'o.final_total' => $search,
                'o.date_added' => $search,
                'oi.id' => $search,
                'oi.product_name' => $search,
                'o.payment_method' => $search,
                'oi.active_status' => $search,
                'sd.store_name' => $search,
            ];
        }

        // Count total unique order items
        $count_res = $this->db->select('COUNT(DISTINCT oi.id) as `total`')
            ->join('orders o', 'o.id = oi.order_id', 'left')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->join('seller_data sd', 'sd.user_id = oi.seller_id', 'left')
            ->join('users su', 'su.id = oi.seller_id', 'left');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $count_res->where("DATE(o.date_added) >= DATE('" . $_GET['start_date'] . "')");
            $count_res->where("DATE(o.date_added) <= DATE('" . $_GET['end_date'] . "')");
        }

        if (isset($_GET['payment_method']) && $_GET['payment_method'] != '') {
            $count_res->where('o.payment_method', $_GET['payment_method']);
        }

        if (isset($_GET['seller_id']) && $_GET['seller_id'] != '') {
            $count_res->where('oi.seller_id', $_GET['seller_id']);
        }

        if (isset($_GET['order_status']) && $_GET['order_status'] != '') {
            $count_res->where('oi.active_status', $_GET['order_status']);
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_start();
            $count_res->or_like($filters);
            $this->db->group_end();
        }

        $sales_count = $count_res->get('order_items oi')->result_array();
        $total = $sales_count[0]['total'] ?? 0;

        // Calculate total sum of final_total for filtered orders
        $sum_res = $this->db->select('SUM(oi.sub_total) as total_order_sum')
            ->join('orders o', 'o.id = oi.order_id', 'left')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->join('seller_data sd', 'sd.user_id = oi.seller_id', 'left')
            ->join('users su', 'su.id = oi.seller_id', 'left');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $sum_res->where("DATE(o.date_added) >= DATE('" . $_GET['start_date'] . "')");
            $sum_res->where("DATE(o.date_added) <= DATE('" . $_GET['end_date'] . "')");
        }

        if (isset($_GET['payment_method']) && $_GET['payment_method'] != '') {
            $sum_res->where('o.payment_method', $_GET['payment_method']);
        }

        if (isset($_GET['seller_id']) && $_GET['seller_id'] != '') {
            $sum_res->where('oi.seller_id', $_GET['seller_id']);
        }

        if (isset($_GET['order_status']) && $_GET['order_status'] != '') {
            $sum_res->where('oi.active_status', $_GET['order_status']);
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_start();
            $sum_res->or_like($filters);
            $this->db->group_end();
        }

        $sum_result = $sum_res->get('order_items oi')->result_array();
        $total_order_sum = $sum_result[0]['total_order_sum'] ?? 0;

        // Main data query
        $search_res = $this->db->select('oi.id, oi.product_name, oi.variant_name, oi.sub_total as final_total, o.payment_method, sd.store_name, su.username as seller_name, o.date_added, oi.active_status')
            ->join('orders o', 'o.id = oi.order_id', 'left')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->join('seller_data sd', 'sd.user_id = oi.seller_id', 'left')
            ->join('users su', 'su.id = oi.seller_id', 'left');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $search_res->where("DATE(o.date_added) >= DATE('" . $_GET['start_date'] . "')");
            $search_res->where("DATE(o.date_added) <= DATE('" . $_GET['end_date'] . "')");
        }

        if (isset($_GET['payment_method']) && $_GET['payment_method'] != '') {
            $search_res->where('o.payment_method', $_GET['payment_method']);
        }

        if (isset($_GET['seller_id']) && $_GET['seller_id'] != '') {
            $search_res->where('oi.seller_id', $_GET['seller_id']);
        }

        if (isset($_GET['order_status']) && $_GET['order_status'] != '') {
            $search_res->where('oi.active_status', $_GET['order_status']);
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_start();
            $search_res->or_like($filters);
            $this->db->group_end();
        }

        $user_details = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('order_items oi')->result_array();
        // echo $this->db->last_query();

        $bulkData = [];
        $bulkData['total'] = $total;
        $rows = [];
        $tempRow = [];
        $currency = get_settings('currency');

        foreach ($user_details as $row) {
            // print_R($row);
            $active_status = $row['active_status'];
            if ($active_status == 'awaiting') {
                $active_status = '<label class="badge badge-secondary bg-secondary-lt">Awaiting</label>';
            } elseif ($active_status == 'received') {
                $active_status = '<label class="badge badge-primary bg-primary-lt">Received</label>';
            } elseif ($active_status == 'processed') {
                $active_status = '<label class="badge badge-info bg-info-lt">Processed</label>';
            } elseif ($active_status == 'shipped') {
                $active_status = '<label class="badge badge-warning bg-warning-lt" >Shipped</label>';
            } elseif ($active_status == 'delivered') {
                $active_status = '<label class="badge badge-success bg-success-lt">Delivered</label>';
            } elseif (in_array($active_status, ['returned', 'cancelled'])) {
                $active_status = '<label class="badge badge-danger bg-danger-lt">' . ucfirst($active_status) . '</label>';
            } elseif ($active_status == 'return_request_decline') {
                $active_status = '<label class="badge badge-danger bg-danger-lt">Return Request Declined</label>';
            } elseif ($active_status == 'return_request_approved') {
                $active_status = '<label class="badge badge-success bg-success-lt">Return Request Approved</label>';
            } elseif ($active_status == 'return_request_pending') {
                $active_status = '<label class="badge badge-secondary bg-secondary-lt">Return Request Pending</label>';
            } else {
                $active_status = '<label class="badge badge-secondary bg-secondary-lt">' . ucfirst($active_status) . '</label>';
            }

            $tempRow['id'] = $row['id'];
            $tempRow['product name'] = $row['product_name'];
            $tempRow['product name'] .= !empty($row['variant_name']) ? '(' . $row['variant_name'] . ')' : '';
            $tempRow['final total'] = $currency . ' ' . number_format($row['final_total'], 2);
            $tempRow['payment method'] = $row['payment_method'];
            $tempRow['store name'] = $row['store_name'];
            $tempRow['seller name'] = $row['seller_name'];
            // $tempRow['date_added'] = date('d-m-Y', strtotime($row['date_added']));
            $date = new DateTime($row['date_added']);
            $tempRow['date added'] = $date->format('d-M-Y');
            $tempRow['active status'] = $active_status;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        $bulkData['total_order_sum'] = number_format($total_order_sum, 2, '.', '');
        echo json_encode($bulkData);
    }

    public function get_seller_sales_list(
        $offset = 0,
        $limit = 10,
        $sort = " o.id ",
        $order = 'ASC'
    ) {
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : intval($offset);
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : intval($limit);
         $sort_map = [
        'id'            => 'o.id',
        'product_name'  => 'oi.product_name',
        'final_total'   => 'o.final_total',
        'payment_method'=> 'o.payment_method',
        'date_added'    => 'o.date_added',
        'active_status' => 'oi.active_status',
        'seller_name'   => 'su.username',
        // add more mappings if your table has more sortable columns
    ];

     $requested_sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';

    // if frontend sends table header name like 'o.id' handle that too
    if (isset($sort_map[$requested_sort])) {
        $sort_column = $sort_map[$requested_sort];
    } else {
        // try to strip table alias if they passed 'o.id'
        $clean = preg_replace('/[^a-z0-9_]/i', '', $requested_sort);
        $sort_column = isset($sort_map[$clean]) ? $sort_map[$clean] : 'o.id';
    }

    // sanitize order direction
    $order = (isset($_GET['order']) && strtolower($_GET['order']) === 'asc') ? 'ASC' : 'DESC';
        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        if (isset($_GET['search']) && $_GET['search'] != '') {
            $search = $_GET['search'];
            $filters = [
                'u.username' => $search,
                'u.email' => $search,
                'u.mobile' => $search,
                'o.final_total' => $search,
                'o.date_added' => $search,
                'o.id' => $search,
                'oi.product_name' => $search,
                'o.payment_method' => $search,
                'oi.active_status' => $search,
            ];
        }

        $count_res = $this->db->select(' COUNT(DISTINCT o.id) as `total` ')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->join('order_items oi', 'oi.order_id = o.id', 'left')
            ->join('seller_data sd', 'sd.user_id = oi.seller_id', 'left')
            ->join('users su', 'su.id = oi.seller_id', 'left');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $count_res->where(" DATE(o.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $count_res->where(" DATE(o.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($_GET['payment_method']) && $_GET['payment_method'] != '') {
            $count_res->where('o.payment_method', $_GET['payment_method']);
        }

        if (isset($_GET['seller_name']) && $_GET['seller_name'] != '') {
            $count_res->where('su.username', $_GET['seller_name']);
        } elseif ($this->ion_auth->is_seller() && !empty($_SESSION['user_id'])) {
            $count_res->where('su.id', $_SESSION['user_id']);
        }

        if (isset($_GET['order_status']) && $_GET['order_status'] != '') {
            $count_res->where('oi.active_status', $_GET['order_status']);
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_start();
            $count_res->or_like($filters);
            $this->db->group_end();
        }

        $sales_count = $count_res->get('orders o')->result_array();
        $total = $sales_count[0]['total'] ?? 0;

        // Calculate total sum of final_total for all filtered orders
        $sum_res = $this->db->select(' SUM(o.final_total) as total_order_sum ')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->join('order_items oi', 'oi.order_id = o.id', 'left')
            ->join('seller_data sd', 'sd.user_id = oi.seller_id', 'left')
            ->join('users su', 'su.id = oi.seller_id', 'left');

        // Filter by date range
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $sum_res->where("DATE(o.date_added) >=", $_GET['start_date']);
            $sum_res->where("DATE(o.date_added) <=", $_GET['end_date']);
        }

        // Filter by payment method
        if (isset($_GET['payment_method']) && $_GET['payment_method'] != '') {
            $sum_res->where('o.payment_method', $_GET['payment_method']);
        }

        // Filter by seller
        if (isset($_GET['seller_name']) && $_GET['seller_name'] != '') {
            $sum_res->where('su.username', $_GET['seller_name']);
        } elseif ($this->ion_auth->is_seller() && !empty($_SESSION['user_id'])) {
            $sum_res->where('su.id', $_SESSION['user_id']);
        }

        // Filter by order status
        if (isset($_GET['order_status']) && $_GET['order_status'] != '') {
            $sum_res->where('oi.active_status', $_GET['order_status']);
        }

        // Exclude specific statuses
        $sum_res->where_not_in('oi.active_status', ['awaiting', 'returned', 'cancelled']);

        // Apply additional filters (search)
        if (isset($filters) && !empty($filters)) {
            $this->db->group_start();
            $sum_res->or_like($filters);
            $this->db->group_end();
        }

        // Execute and get result
        $sum_result = $sum_res->get('orders o')->result_array();


        $total_order_sum = $sum_result[0]['total_order_sum'] ?? 0;

        $search_res = $this->db->select('o.*, oi.*, u.username, u.email, u.mobile, sd.store_name, su.username as seller_name, oi.active_status')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->join('order_items oi', 'oi.order_id = o.id', 'left')
            ->join('seller_data sd', 'sd.user_id = oi.seller_id', 'left')
            ->join('users su', 'su.id = oi.seller_id', 'left');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $search_res->where(" DATE(o.date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $search_res->where(" DATE(o.date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($_GET['payment_method']) && $_GET['payment_method'] != '') {
            $search_res->where('o.payment_method', $_GET['payment_method']);
        }

        if (isset($_GET['seller_name']) && $_GET['seller_name'] != '') {
            $search_res->where('su.username', $_GET['seller_name']);
        } elseif ($this->ion_auth->is_seller() && !empty($_SESSION['user_id'])) {
            $search_res->where('su.id', $_SESSION['user_id']);
        }

        if (isset($_GET['order_status']) && $_GET['order_status'] != '') {
            $search_res->where('oi.active_status', $_GET['order_status']);
        }

        if (isset($filters) && !empty($filters)) {
            $search_res->group_start();
            $search_res->or_like($filters);
            $this->db->group_end();
        }

     $search_res->group_by('o.id');

// Ensure order is always applied properly, even when same as group_by column
if ($sort_column === 'o.id') {
    $search_res->order_by('o.id + 0', $order, false); // numeric-safe ordering
} else {
    $search_res->order_by($sort_column, $order);
}

$user_details = $search_res->limit($limit, $offset)->get('orders o')->result_array();


        $bulkData = [];
        $bulkData['total'] = $total;
        $rows = [];
        $tempRow = [];
        $total_amount = 0;
        $final_total_amount = 0;
        $total_delivery_charge = 0;

        foreach ($user_details as $row) {
            // Create dropdown menu for operate column
            if (!$this->ion_auth->is_seller()) {
                $operate = '
                <div class="dropdown">
                    <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

                // View Order
                $operate .= '<li>
                    <a class="dropdown-item" href="' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['id'] . '">
                        <i class="fa fa-eye me-2"></i>View Order
                    </a>
                </li>';

                // Generate Invoice
                $operate .= '<li>
                    <a class="dropdown-item" href="' . base_url() . 'admin/invoice?edit_id=' . $row['id'] . '">
                        <i class="fa fa-file me-2"></i>Generate Invoice
                    </a>
                </li>';

                // Divider
                $operate .= '<li><hr class="dropdown-divider"></li>';

                // Delete Order
                $operate .= '<li>
                    <a class="dropdown-item text-danger delete-orders" href="javascript:void(0)" 
                       data-id="' . $row['id'] . '">
                        <i class="fa fa-trash me-2"></i>Delete Order
                    </a>
                </li>';

                $operate .= '
                    </ul>
                </div>';
            } else {
                $operate = '
                <div class="dropdown">
                    <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="' . base_url('seller/orders/edit_orders') . '?edit_id=' . $row['id'] . '">
                                <i class="fa fa-eye me-2"></i>View Order
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="' . base_url() . 'seller/invoice?edit_id=' . $row['id'] . '">
                                <i class="fa fa-file me-2"></i>Generate Invoice
                            </a>
                        </li>
                    </ul>
                </div>';
            }

            $active_status = $row['active_status'];
            if ($active_status == 'awaiting') {
                $active_status = '<label class="badge badge-secondary bg-secondary-lt">Awaiting</label>';
            } elseif ($active_status == 'received') {
                $active_status = '<label class="badge badge-primary bg-primary-lt">Received</label>';
            } elseif ($active_status == 'processed') {
                $active_status = '<label class="badge badge-info bg-info-lt">Processed</label>';
            } elseif ($active_status == 'shipped') {
                $active_status = '<label class="badge badge-warning bg-warning-lt">Shipped</label>';
            } elseif ($active_status == 'delivered') {
                $active_status = '<label class="badge badge-success bg-success-lt">Delivered</label>';
            } elseif (in_array($active_status, ['returned', 'cancelled'])) {
                $active_status = '<label class="badge badge-danger bg-danger-lt">' . ucfirst($active_status) . '</label>';
            } elseif ($active_status == 'return_request_decline') {
                $active_status = '<label class="badge badge-danger bg-danger-lt">Return Request Declined</label>';
            } elseif ($active_status == 'return_request_approved') {
                $active_status = '<label class="badge badge-success bg-success-lt">Return Request Approved</label>';
            } elseif ($active_status == 'return_request_pending') {
                $active_status = '<label class="badge badge-secondary bg-secondary-lt">Return Request Pending</label>';
            } else {
                $active_status = '<label class="badge badge-secondary bg-secondary-lt">' . ucfirst($active_status) . '</label>';
            }

            $tempRow['id'] = $row['id'];
            $tempRow['product_name'] = $row['product_name'];
            $tempRow['product_name'] .= !empty($row['variant_name']) ? '(' . $row['variant_name'] . ')' : '';
            if (!$this->ion_auth->is_seller()) {
                $tempRow['address'] = $row['address'];
                $tempRow['mobile'] = (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat('X', strlen($row['mobile']) - 3) . substr($row['mobile'], -3) : $row['mobile'];
            }
            // $tempRow['date_added'] = date('d-m-Y', strtotime($row['date_added']));
            $date = new DateTime($row['date_added']);
            $tempRow['date_added'] = $date->format('d-M-Y');

          $currency = get_settings('currency') ?: '₹'; // Fallback to ₹ if not set
$tempRow['final_total'] = $currency . ' ' . number_format((float)$row['final_total'], 2);
            $total_amount += intval($row['total'] ?? 0);
            $final_total_amount += intval($row['final_total'] ?? 0);
            $total_delivery_charge += intval($row['delivery_charge'] ?? 0);
            if ($this->ion_auth->is_seller()) {
                $tempRow['payment_method'] = $row['payment_method'];
                $tempRow['store_name'] = $row['store_name'];
                $tempRow['seller_name'] = $row['seller_name'];
            }
            $tempRow['active_status'] = $active_status;
            if (!$this->ion_auth->is_seller()) {
                $tempRow['operate'] = $operate;
            }
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        $bulkData['total_order_sum'] = number_format($total_order_sum, 2, '.', '');
        echo json_encode($bulkData);
    }
}
