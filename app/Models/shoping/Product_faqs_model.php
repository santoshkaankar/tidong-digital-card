<?php


defined('BASEPATH') or exit('No direct script access allowed');


class Product_faqs_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    // function edit_product_faqs($data, $id)
    // {
    //     $data = escape_array($data);
    //     $this->db->set($data)->where('id', $id)->update('product_faqs');
    // }

    function add_product_faqs($data)
    {
        
        
        $product_id = isset($data['product_id']) && !empty($data['product_id']) ? $data['product_id'] : (isset($data['hidden_product_id']) && !empty($data['hidden_product_id']) ? $data['hidden_product_id'] : 0);
        $user_id = $data['user_id'] ?? $this->ion_auth->get_user_id();
        $answer = $data['answer'] ?? "";

        $seller_id = [];
        if (!empty($product_id)) {
            $seller_id = fetch_details('products', ['id' => $product_id], 'seller_id');
        }

        $faq_data = [
            'product_id' => $product_id,
            'user_id' => $user_id,
            'seller_id' => isset($data['seller_id']) && !empty($data['seller_id']) ? $data['seller_id'] : (isset($seller_id[0]['seller_id']) ? $seller_id[0]['seller_id'] : 0),
            'question' => $data['question'] ?? '',
            'answer' => $answer,
            'answered_by' => !empty($answer) ? $this->ion_auth->get_user_id() : 0,
        ];


        if (isset($data['edit_product_faq']) && !empty($data['edit_product_faq'])) {
            // update existing FAQ
            $this->db->set($faq_data)->where('id', $data['edit_product_faq'])->update('product_faqs');
            return true;
        } else {
            // insert new FAQ
            $this->db->insert('product_faqs', $faq_data);
            return $this->db->insert_id();
        }
    }
    public function delete_faq($faq_id)
    {
        $faq_id = escape_array($faq_id);
        $this->db->delete('product_faqs', ['id' => $faq_id]);
    }
    function get_faqs()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';

        $multipleWhere = '';

        if (isset($offset) && !empty($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($limit) && !empty($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']) && !empty($_GET['sort'])) {
            $sort = $_GET['sort'];
        }
        if (isset($_GET['order']) && !empty($_GET['order'])) {
            $order = $_GET['order'];
        }
        if (isset($_GET['product_id']) && $_GET['product_id'] != null) {
            $where['product_id'] = $_GET['product_id'];
        }
        if (isset($_GET['user_id']) && $_GET['user_id'] != null) {
            $where['user_id'] = $_GET['user_id'];
        }
        if (isset($_GET['seller_id']) && $_GET['seller_id'] != null) {
            $where['pf.seller_id'] = $_GET['seller_id'];
        }
        $count_res = $this->db->select(' COUNT(pf.id) as total  ')->join('users u', 'u.id=pf.user_id')->join('products p', 'p.id=pf.product_id');
        if (isset($_GET['search']) && trim($_GET['search'])) {
            $search = trim($_GET['search']);
            $multipleWhere = ['pf.id' => $search, 'pf.product_id' => $search, 'pf.user_id' => $search, 'pf.question' => $search, 'pf.answer' => $search, 'p.name' => $search];
        }
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_start();
            $count_res->or_like($multipleWhere);
            $this->db->group_end();
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $rating_count = $count_res->get('product_faqs pf')->result_array();
        
        foreach ($rating_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select('pf.*,u.username as user_name,p.name as product_name')->join('users u', 'u.id=pf.user_id')->join('products p', 'p.id=pf.product_id');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $this->db->group_start();
            $search_res->or_like($multipleWhere);
            $this->db->group_end();
        }

        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $rating_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('product_faqs pf')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        $i = 0;
        foreach ($rating_search_res as $row) {

            $product = fetch_details('products', ['id' => $row['product_id']], 'name');

            $row = output_escaping($row);
            $date = new DateTime($row['date_added']);

            $answered_by = fetch_details('users', 'id=' . $row['answered_by'], 'username');

            // Create dropdown menu for operate column
            $operate = '
            <div class="dropdown">
                <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

            if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
                // Edit FAQ
                $operate .= '<li>
                    <a class="dropdown-item edit_btn" href="javascript:void(0)" 
                       data-id="' . $row['id'] . '" 
                       data-product-id="' . $row['product_id'] . '"
                       data-bs-toggle="offcanvas"
                       data-bs-target="#addProductFAQ">
                        <i class="ti ti-pencil me-2"></i>Edit
                    </a>
                </li>';

                // Divider
                $operate .= '<li><hr class="dropdown-divider"></li>';

                // Delete FAQ
                $operate .= '<li>
                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                       x-data="ajaxDelete({
                           url: base_url + \'admin/product_faqs/delete_product_faq\',
                           id: \'' . $row['id'] . '\',
                           tableSelector: \'#products_faqs_table\',
                           confirmTitle: \'Delete Product FAQ\',
                           confirmMessage: \'Do you really want to delete this product FAQ?\'
                       })"
                       @click="deleteItem">
                        <i class="ti ti-trash me-2"></i>Delete
                    </a>
                </li>';
            } else {
                // Seller view - Attach question, answer and product_id as data attributes
                $safe_question = htmlspecialchars($row['question'], ENT_QUOTES);
                $safe_answer = htmlspecialchars($row['answer'], ENT_QUOTES);

                // Edit FAQ
                $operate .= '<li>
                    <a class="dropdown-item edit_btn" href="javascript:void(0)" 
                       data-bs-toggle="offcanvas" 
                       data-bs-target="#product_faq_value_id" 
                       aria-controls="product_faq_value_id" 
                       data-id="' . $row['id'] . '" 
                       data-product-id="' . $row['product_id'] . '" 
                       data-question="' . $safe_question . '" 
                       data-answer="' . $safe_answer . '" 
                       data-url="seller/product_faqs/">
                        <i class="ti ti-pencil me-2"></i>Edit
                    </a>
                </li>';

                // Divider
                $operate .= '<li><hr class="dropdown-divider"></li>';

                // Delete FAQ
                $operate .= '<li>
                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                       x-data="ajaxDelete({
                           url: base_url + \'seller/product_faqs/delete_product_faq\',
                           id: \'' . $row['id'] . '\',
                           tableSelector: \'#products_faqs_table\',
                           confirmTitle: \'Delete Product FAQ\',
                           confirmMessage: \'Do you really want to delete this product FAQ?\'
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
            $tempRow['user_id'] = $row['user_id'];
            $tempRow['product_id'] = $row['product_id'];
            $tempRow['product_name'] = (isset($product[0]['name']) && !empty($product[0]['name'])) ? $product[0]['name'] : '';
            $tempRow['votes'] = $row['votes'];
            $tempRow['question'] = $row['question'];
            $tempRow['answer'] = $row['answer'];
            // $tempRow['question'] = word_limit($row['question'], 15);
            // $tempRow['answer'] =  word_limit($row['answer'], 15);
            $tempRow['answered_by'] = $row['answered_by'];
            $tempRow['answered_by_name'] = (isset($answered_by[0]['username']) && !empty($answered_by)) ? $answered_by[0]['username'] : '';
            $tempRow['username'] = $row['user_name'];
            $tempRow['date_added'] = $date->format('d-M-Y');
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
            $i++;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }
}



