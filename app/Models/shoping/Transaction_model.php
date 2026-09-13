<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Transaction_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    function add_transaction($data)
    {
        $this->load->model('Order_model');
        $data = escape_array($data);
        /* transaction_type : transaction - for payment transactions | wallet - for wallet transactions  */
        $transaction_type = (!isset($data['transaction_type']) || empty($data['transaction_type'])) ? 'transaction' : $data['transaction_type'];
        $trans_data = [
            'transaction_type' => $transaction_type,
            'user_id' => $data['user_id'],
            'order_id' => $data['order_id'],
            'order_item_id' => $data['order_item_id'],
            'type' => strtolower($data['type']),
            'txn_id' => $data['txn_id'],
            'amount' => $data['amount'],
            'status' => $data['status'],
            'message' => $data['message'],
        ];
        $this->db->insert('transactions', $trans_data);
    }

    function get_transactions_list($user_id = '', $group_id = 2, $transaction_type = '')
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = '';
        $where = [];

        // Merge filter params instead of overwriting $where so multiple filters can apply together
        if (isset($_GET['transaction_type']) && $_GET['transaction_type'] != '') {
            $where['transactions.transaction_type'] = $_GET['transaction_type'];
        }
        if (isset($_GET['seller_filter']) && $_GET['seller_filter'] != '') {
            $where['transactions.user_id'] = $_GET['seller_filter'];
        }

        // payment_type is used to filter by payment gateway / method (e.g. phonepay, razorpay, payu)
        if (isset($_GET['payment_type']) && $_GET['payment_type'] != '') {
            // normalize to lower case to match stored `type` values
            $where['transactions.type'] = strtolower($_GET['payment_type']);
        }

        // legacy or alternate filter name for transaction type (maps to transactions.type)
        if (isset($_GET['transaction_status_type_filter']) && $_GET['transaction_status_type_filter'] != '') {
            $where['transactions.type'] = $_GET['transaction_status_type_filter'];
        }

        // Allow filtering by transaction status (e.g. awaiting, success, failed)
        if (isset($_GET['status']) && $_GET['status'] != '') {
            $where['transactions.status'] = $_GET['status'];
        }

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            $sort = ($_GET['sort'] == 'id') ? "id" : $_GET['sort'];
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                '`transactions.id`' => $search,
                '`transactions.amount`' => $search,
                '`transactions.date_created`' => $search,
                'users.username' => $search,
                'users.mobile' => $search,
                'users.email' => $search,
                'transactions.type' => $search,
                'transactions.status' => $search,
                'transactions.txn_id' => $search
            ];
        }

        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $where['users.id'] = $_GET['user_id'];
        }
        if (isset($_GET['filter_user_id']) && !empty($_GET['filter_user_id'])) {
            $where['users.id'] = $_GET['filter_user_id'];
        }

        if (isset($user_id) && !empty($user_id)) {
            $user_where = ['users.id' => $user_id];
        }

        if (isset($_GET['user_type']) && !empty($_GET['user_type'])) {
            $group_id_res = fetch_details("groups", ['name' => $_GET['user_type']], "id");
            $group_id = $group_id_res[0]['id'];
        }

        /* Count Records */
        $count_res = $this->db->select('COUNT(transactions.id) as `total`');

        if (!empty($multipleWhere)) {
            $this->db->group_Start();
            $count_res->or_like($multipleWhere);
            $this->db->group_End();
        }

        if (!empty($where))
            $count_res->where($where);
        if (!empty($user_where))
            $count_res->where($user_where);
        // Only filter by group if no specific user is requested
        if (!empty($group_id) && empty($user_where)) {
            $count_res->where('ug.group_id', $group_id);
        }

        if (!empty($transaction_type)) {
            // When filtering by 'wallet', only show records where transaction_type='wallet'
            // This avoids showing payment gateway transactions (type='wallet')
            $count_res->where('transactions.transaction_type', $transaction_type);
        }

        $txn_count = $count_res->join('users', 'transactions.user_id = users.id', 'left')
            ->join('users_groups ug', 'ug.user_id = users.id', 'left')
            ->get('transactions')->result_array();

        $total = $txn_count[0]['total'];

        /* Fetch Actual Records */
        $search_res = $this->db->select('transactions.*, users.username as name');

        if (!empty($multipleWhere)) {
            $this->db->group_Start();
            $search_res->or_like($multipleWhere);
            $this->db->group_End();
        }

        if (!empty($where))
            $search_res->where($where);
        if (!empty($user_where))
            $search_res->where($user_where);
        // Only filter by group if no specific user is requested
        if (!empty($group_id) && empty($user_where)) {
            $search_res->where('ug.group_id', $group_id);
        }

        if (!empty($transaction_type)) {
            // When filtering by 'wallet', only show records where transaction_type='wallet'
            // This avoids showing payment gateway transactions (type='wallet')
            $search_res->where('transactions.transaction_type', $transaction_type);
        }

        $txn_search_res = $search_res->join('users', 'transactions.user_id = users.id', 'left')
            ->join('users_groups ug', 'ug.user_id = users.id', 'left')
            ->order_by($sort, $order)
            ->limit($limit, $offset)
            ->get('transactions')
            ->result_array();

        /* Format Data */
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $currency = get_settings('currency');

        foreach ($txn_search_res as $row) {
            $row = output_escaping($row);
            $tempRow = [];


            switch ($row['type']) {
                case 'bank_transfer':
                    $operate = '<a href="javascript:void(0)" class="edit_transaction action-btn btn btn-success btn-xs mr-1 mb-1" title="Edit"
                    data-id="' . $row['id'] . '" data-txn_id="' . $row['txn_id'] . '" data-status="' . $row['status'] . '"
                    data-message="' . $row['message'] . '" data-target="#transaction_modal" data-toggle="modal">
                    <i class="fa fa-pen"></i></a>';
                    $tempRow['type'] = '<span class="badge bg-primary-lt text-primary">Bank Transfer</span>';
                    break;

                case 'credit':
                    $operate = "";
                    $tempRow['type'] = '<span class="badge bg-info-lt text-info">Credit</span>';
                    break;

                case 'debit':
                    $operate = "";
                    $tempRow['type'] = '<span class="badge bg-warning-lt text-warning">Debit</span>';
                    break;
                case 'cod':
                    $operate = "";
                    $tempRow['type'] = '<span class="badge bg-info-lt text-info">cod</span>';
                    break;


                default:
                    $operate = "";
                    $tempRow['type'] = '<span class="badge bg-secondary-lt text-secondary">' . ucfirst($row['type']) . '</span>';
                    break;
            }

            //badge for the status 
            switch ($row['status']) {
                case 'awaiting':
                    $tempRow['status'] = '<span class="badge bg-info-lt text-info">Awaiting</span>';
                    break;
                case 'success':
                    $tempRow['status'] = '<span class="badge bg-success-lt text-success">Success</span>';
                    break;
                case 'failed':
                    $tempRow['status'] = '<span class="badge bg-danger-lt text-danger">Failed</span>';
                    break;

                default:
                    $tempRow['status'] = '<span class="badge bg-secondary-lt text-secondary">' . ucfirst($row['status']) . '</span>';
                    break;
            }

            /* Remaining Fields */
            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['name'];
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['txn_id'] = $row['txn_id'];
            $tempRow['payu_txn_id'] = $row['payu_txn_id'];
            $tempRow['amount'] = $currency . ' ' . $row['amount'];
            $tempRow['message'] = $row['message'];
            $tempRow['txn_date'] = date('d-M-Y', strtotime($row['transaction_date']));
            $tempRow['date'] = date('d-M-Y', strtotime($row['date_created']));
            $tempRow['operate'] = $operate;

            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }


    function get_transactions($id = '', $user_id = '', $transaction_type = '', $type = '', $search = '', $offset = '0', $limit = '25', $sort = 'id', $order = 'DESC')
    {
        $where = $multiple_where = [];
        $count_sql = $this->db->select(' COUNT(id) as `total`');
        if (!empty($user_id)) {
            $where['user_id'] = $user_id;
        }

        if ($transaction_type != '') {
            $where['transaction_type'] = $transaction_type;
        }

        if ($type != '') {
            $where['type'] = $type;
        }

        if ($id !== '') {
            $where['id'] = $id;
        }

        if ($search !== '') {
            $multiple_where = [
                'id' => $search,
                'transaction_type' => $search,
                'type' => $search,
                'order_id' => $search,
                'txn_id' => $search,
                'amount' => $search,
                'status' => $search,
                'message' => $search,
                'transaction_date' => $search,
                'date_created' => $search,
            ];
        }

        if (isset($where) && !empty($where)) {
            $count_sql->where($where);
        }

        if (isset($multiple_where) && !empty($multiple_where)) {
            $count_sql->group_start();  //group start
            $count_sql->or_like($multiple_where);
            $count_sql->group_end();  //group end
        }

        $count = $count_sql->get('transactions')->result_array();
        $total = $count[0]['total'];

        /* query for transactions list */
        $transactions_sql = $this->db->select('*');
        if (isset($where) && !empty($where)) {
            $transactions_sql->where($where);
        }

        if (isset($multiple_where) && !empty($multiple_where)) {
            $transactions_sql->group_start();  //group start
            $transactions_sql->or_like($multiple_where);
            $transactions_sql->group_end();  //group end
        }

        if ($limit != '' && $offset !== '') {
            $transactions_sql->limit($limit, $offset);
        }

        $transactions_sql->order_by($sort, $order);
        $q = $this->db->get('transactions');

        $transactions['data'] = $q->result_array();
        if (!empty($transactions['data'])) {
            for ($i = 0; $i < count($transactions['data']); $i++) {
                $transactions['data'][$i]['payu_txn_id'] = ($transactions['data'][$i]['payu_txn_id'] != null) ? $transactions['data'][$i]['payu_txn_id'] : "";
                $transactions['data'][$i]['currency_code'] = ($transactions['data'][$i]['currency_code'] != null) ? $transactions['data'][$i]['currency_code'] : "";
                $transactions['data'][$i]['payer_email'] = ($transactions['data'][$i]['payer_email'] != null) ? $transactions['data'][$i]['payer_email'] : "";
            }
        }
        $transactions['total'] = $total;
        return $transactions;
    }

    function edit_transactions($data)
    {
        $data = escape_array($data);

        $t_data = [
            'status' => $data['status'],
            'txn_id' => $data['txn_id'],
            'message' => $data['message'],
        ];

        if ($this->db->set($t_data)->where('order_id', $data['id'])->update('transactions')) {
            return false;
        } else {
            return true;
        }
    }
    function get_withdrawal_transactions_list($user_id = '')
    {
        $sort = 'id';
        $order = 'ASC';
        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];



        if (!empty($user_id)) {
            $user_where = ['user_id' => $user_id];
        }

        $count_res = $this->db->select(' COUNT(id) as `total` ');

        if (isset($user_where) && !empty($user_where)) {
            $count_res->where($user_where);
        }

        $txn_count = $count_res->get('payment_requests')->result_array();
        foreach ($txn_count as $row) {
            $total = $row['total'];
        }
        $search_res = $this->db->select(' * ');
        $search_res->where($user_where);
        $txn_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('payment_requests')->result_array();


        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();


        $username = fetch_details('users', ['id' => $user_id], 'username');
        foreach ($txn_search_res as $row) {
            $row = output_escaping($row);

            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $username[0]['username'];
            $tempRow['payment_address'] = $row['payment_address'];
            $tempRow['amount_requested'] = $row['amount_requested'];
            $status = [
                '0' => '<span class="badge badge-secondary">Pending</span>',
                '1' => '<span class="badge badge-success">Approved</span>',
                '2' => '<span class="badge badge-danger">Rejected</span>',
            ];
            $tempRow['status'] = $status[$row['status']];
            $tempRow['date_created'] = $row['date_created'];
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    function update_transaction($data, $txn_id)
    {
        $this->load->model('Order_model');
        $data = escape_array($data);
        /* transaction_type : transaction - for payment transactions | wallet - for wallet transactions  */
        $trans_data = [
            'status' => $data['status'],
            'message' => $data['message'],
        ];
        $this->db->where('txn_id', $txn_id);
        $this->db->update('transactions', $trans_data);
    }
}
