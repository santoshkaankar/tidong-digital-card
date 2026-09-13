<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Media_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    function set_media($data, $seller_id = 0)
    {
        $data = escape_array($data);
        $extenstion = trim($data['file_ext'], '.');
        $extenstionData = find_media_type($extenstion);
        $media_type = $extenstionData[0];
        if (empty($seller_id))
            $seller_id = ($this->ion_auth->is_seller()) ? $this->ion_auth->get_user_id() : 0;
        $data = [
            'name' => $data['file_name'],
            'seller_id' => $seller_id,
            'extension' => ltrim($data['file_ext'], '.'),
            'title' => $data['raw_name'],
            'type' => ($media_type != false) ? $media_type : 'other',
            'size' => $data['file_size'],
            'sub_directory' => $data['sub_directory'],
        ];

        $this->db->insert('media', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    function get_media_by_id($id)
    {
        $this->db->where('id', $id);
        $q = $this->db->get('media');
        return $q->result_array();
    }

    public function delete_media($ids)
    {
        // Example: Delete media items from database where id in $ids array
        $this->db->where_in('id', $ids);
        return $this->db->delete('media'); // Replace with your actual table name
    }


    public function fetch_media($fromSeller = false)
    {
        if (($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) || ($this->ion_auth->logged_in() && $this->ion_auth->is_seller() && ($this->ion_auth->seller_status() == 1 || $this->ion_auth->seller_status() == 0))) {

            $multipleWhere = $where_in = '';

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
                $multipleWhere = ['id' => $search, 'name' => $search];
            }
            if (isset($_GET['type']) and $_GET['type'] != '') {
                $type = explode(",", $this->input->get('type'));
                $where_in = $type;
            }

            // Force seller to only see their own media
            if ($this->ion_auth->is_seller()) {
                $where['seller_id'] = $this->ion_auth->get_user_id();
            }
            // Admin can filter manually
            elseif (isset($_GET['seller_id']) && $_GET['seller_id'] != '' && $fromSeller == true) {
                $where['seller_id'] = $_GET['seller_id'];
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
            if (isset($where_in) && !empty($where_in)) {
                $count_res->where_in("type", $where_in);
            }
            if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {

                $count_res->where(" DATE(date_created) >= DATE('" . $_GET['start_date'] . "') ");
                $count_res->where(" DATE(date_created) <= DATE('" . $_GET['end_date'] . "') ");
            }
            $attr_count = $count_res->get('media')->result_array();

            foreach ($attr_count as $row) {
                $total = $row['total'];
            }

            $search_res = $this->db->select('*');
            if (isset($multipleWhere) && !empty($multipleWhere)) {
                $this->db->group_Start();
                $search_res->or_like($multipleWhere);
                $this->db->group_End();
            }
            if (isset($where) && !empty($where)) {
                $search_res->where($where);
            }

            if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {

                $search_res->where(" DATE(date_created) >= DATE('" . $_GET['start_date'] . "') ");
                $search_res->where(" DATE(date_created) <= DATE('" . $_GET['end_date'] . "') ");
            }

            if (isset($where_in) && !empty($where_in)) {
                $search_res->where_in("type", $where_in);
            }

            $city_search_res = $search_res->order_by($sort, 'desc')->limit($limit, $offset)->get('media')->result_array();
            $bulkData = array();
            $bulkData['total'] = $total;
            $rows = array();
            $tempRow = array();

            $i = 0;
            foreach ($city_search_res as $row) {
                // Create dropdown menu for operate column
                $operate = '
                <div class="dropdown">
                    <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu">';

                // Copy to clipboard
                $operate .= '<li>
                    <a class="dropdown-item copy-to-clipboard" href="javascript:void(0);">
                        <i class="ti ti-clipboard me-2"></i>Copy to Clipboard
                    </a>
                </li>';

                // Copy relative path
                $operate .= '<li>
                    <a class="dropdown-item copy-relative-path" href="javascript:void(0);" 
                       data-path="' . $row['sub_directory'] . $row['name'] . '">
                        <i class="ti ti-copy me-2"></i>Copy Image Path
                    </a>
                </li>';

                // Delete action (based on permissions)
                if (($this->ion_auth->is_seller() && $row['seller_id'] == $this->ion_auth->get_user_id()) || $this->ion_auth->is_admin()) {
                    // Divider
                    $operate .= '<li><hr class="dropdown-divider"></li>';

                    // Delete Media
                    if ($this->ion_auth->is_admin()) {
                        $operate .= '<li>
                            <a class="dropdown-item text-danger" href="javascript:void(0)"
                               x-data="ajaxDelete({
                                   url: base_url + \'admin/media/delete\',
                                   id: \'' . $row['id'] . '\',
                                   tableSelector: \'#media-table\',
                                   confirmTitle: \'Delete Media\',
                                   confirmMessage: \'Do you really want to delete this media?\'
                               })"
                               @click="deleteItem">
                                <i class="ti ti-trash me-2"></i>Delete
                            </a>
                        </li>';
                    } else {
                        $operate .= '<li>
                            <a class="dropdown-item text-danger" href="javascript:void(0)"
                               x-data="ajaxDelete({
                                   url: base_url + \'seller/media/delete\',
                                   id: \'' . $row['id'] . '\',
                                   tableSelector: \'#media-table\',
                                   confirmTitle: \'Delete Media\',
                                   confirmMessage: \'Do you really want to delete this media?\'
                               })"
                               @click="deleteItem">
                                <i class="ti ti-trash me-2"></i>Delete
                            </a>
                        </li>';
                    }
                }

                $operate .= '
                    </ul>
                </div>';

                $tempRow['id'] = $row['id'];
                $tempRow['seller_id'] = $row['seller_id'];
                $tempRow['name'] = $row['name'];
                if (file_exists(FCPATH . $row['sub_directory'] . $row['name'])) {
                    $row['image'] = get_image_url($row['sub_directory'] . $row['name'], 'thumb', 'sm', trim(strtolower($row['type'])));
                } else {
                    $row['image'] = base_url() . NO_IMAGE;
                }

                $tempRow['image'] = '<div class="image-box-table text-center"><span class="path d-none">' . base_url() . $row['sub_directory'] . $row['name'] . '</span><span class="relative-path d-none">' . $row['sub_directory'] . $row['name'] . '</span><a href="' . $row['image'] . '" data-lightbox="media" ><img class="rounded" src="' . $row['image'] . '" ></a></div>';


                $tempRow['extension'] = $row['extension'];
                $tempRow['seller_id'] = $row['seller_id'];
                $tempRow['sub_directory'] = $row['sub_directory'];
                $tempRow['size'] = ($row['size'] > 1) ? formatBytes($row['size']) : $row['size'];
                $tempRow['operate'] = $operate;
                $rows[] = $tempRow;
                $i++;
            }
            $bulkData['rows'] = $rows;
            print_r(json_encode($bulkData));
        } else {
            redirect('admin/login', 'refresh');
        }
    }

    public function get_media($limit = "", $offset = '', $sort = 'id', $order = 'DESC', $search = NULL, $type = "", $seller_id = NULL)
    {

        $multipleWhere = '';

        if (isset($search) and $search != '') {
            $multipleWhere = ['id' => $search, 'name' => $search];
        }

        if (isset($type) and $type != '') {
            $media_type = explode(",", $type);
            $where_in = $media_type;
        }
        if (isset($seller_id) and $seller_id != '') {
            $where['seller_id'] = $seller_id;
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
        if (isset($where_in) && !empty($where_in)) {
            $count_res->where_in("type", $where_in);
        }
        $attr_count = $count_res->get('media')->result_array();

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
        if (isset($where_in) && !empty($where_in)) {
            $search_res->where_in("type", $where_in);
        }

        $city_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('media')->result_array();
        $bulkData = array();
        $bulkData['error'] = (empty($city_search_res)) ? true : false;
        $bulkData['message'] = (empty($city_search_res)) ? 'Media(s) does not exist' : 'Media retrieved successfully';
        $bulkData['total'] = (empty($city_search_res)) ? 0 : $total;
        $rows = $tempRow = array();
        $i = 0;
        foreach ($city_search_res as $row) {
            $tempRow['id'] = $row['id'];
            $tempRow['seller_id'] = $row['seller_id'];
            $tempRow['name'] = $row['name'];
            if (file_exists(FCPATH . $row['sub_directory'] . $row['name'])) {
                $row['image'] = get_image_url($row['sub_directory'] . $row['name'], 'thumb', 'sm', trim(strtolower($row['type'])));
            } else {
                $row['image'] = base_url() . NO_IMAGE;
            }
            $tempRow['image'] = base_url() . $row['sub_directory'] . $row['name'];
            $tempRow['extension'] = $row['extension'];
            $tempRow['seller_id'] = $row['seller_id'];
            $tempRow['sub_directory'] = $row['sub_directory'];
            $tempRow['relative_path'] = $row['sub_directory'] . $row['name'];
            $tempRow['size'] = ($row['size'] > 1) ? formatBytes($row['size']) : $row['size'];
            $rows[] = $tempRow;
            $i++;
        }
        $bulkData['data'] = $rows;
        print_r(json_encode($bulkData));
    }
}
