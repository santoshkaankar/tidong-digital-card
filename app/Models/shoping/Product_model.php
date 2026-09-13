<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Product_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }

    public function add_product($data)
    {
        $data = escape_array($data);

       
        if ($data['product_type'] == 'simple_product' || $data['product_type'] == 'variable_product') {
            $pro_type = ($data['product_type'] == 'simple_product') ? 'simple_product' : 'variable_product';
        } else {
            $pro_type = ($data['product_type'] == 'digital_product') ? 'digital_product' : '';
        }
        $short_description = $data['short_description'];
        $category_id = $data['category_id'];
        $seller_id = $data['seller_id'];
        $permits = fetch_details('seller_data', ['user_id' => $seller_id], 'permissions');
        // print_r($permits);
        $s_permits = isset($permits[0]['permissions']) && !empty($permits[0]['permissions']) ? json_decode($permits[0]['permissions'], true) : '';
        if (isset($data['edit_product_id']) && !empty($data['edit_product_id'])) {
            $edit_status = fetch_details('products', ['id' => $data['edit_product_id']], ['status', 'name', 'slug']);
            $require_products_approval = isset($data['status']) && ($data['status'] != '') ? $data['status'] : $edit_status[0]['status'];
        } else {
            $is_permit = (isset($s_permits['require_products_approval']) && $s_permits['require_products_approval'] == 0) ? 1 : 2;
            $require_products_approval = $is_permit;
        }
        $made_in = (isset($data['made_in']) && !empty($data['made_in'])) ? $data['made_in'] : null;

        $brand = (isset($data['brand']) && !empty($data['brand'])) ? $data['brand'] : null;
        $indicator = (isset($data['indicator']) && !empty($data['indicator'])) ? $data['indicator'] : null;
        $description = $data['pro_input_description'];
        $extra_description = $data['extra_input_description'];
        $tags = (!empty($data['tags'])) ? $data['tags'] : "";
        $seo_page_title = (!empty($data['seo_page_title'])) ? $data['seo_page_title'] : "";
        $seo_meta_keywords = (!empty($data['seo_meta_keywords'])) ? $data['seo_meta_keywords'] : "";
        $seo_meta_description = (!empty($data['seo_meta_description'])) ? $data['seo_meta_description'] : "";
        $seo_og_image = (!empty($data['seo_og_image'])) ? $data['seo_og_image'] : "";

        // check slug 
        $edit_product_name = isset($edit_status[0]['name']) ? $edit_status[0]['name'] : '';
        if ($edit_product_name != $data['pro_input_name']) {
            $slug = create_unique_slug($data['pro_input_name'], 'products');
        } else {
            $slug = $edit_status[0]['slug'];
        }

        $main_image_name = $data['pro_input_image'];

        $other_images = (isset($data['other_images']) && !empty($data['other_images'])) ? array_unique($data['other_images']) : [];
        if (isset($data['product_type']) && $data['product_type'] == 'digital_product') {
            $total_allowed_quantity = 1;
        } else {
            $total_allowed_quantity = (isset($data['total_allowed_quantity']) && !empty($data['total_allowed_quantity'])) ? $data['total_allowed_quantity'] : 10;
        }
        $minimum_order_quantity = (isset($data['minimum_order_quantity']) && !empty($data['minimum_order_quantity'])) ? $data['minimum_order_quantity'] : 1;
        $quantity_step_size = (isset($data['quantity_step_size']) && !empty($data['quantity_step_size'])) ? $data['quantity_step_size'] : 1;
        $warranty_period = (isset($data['warranty_period']) && !empty($data['warranty_period'])) ? $data['warranty_period'] : "";
        $guarantee_period = (isset($data['guarantee_period']) && !empty($data['guarantee_period'])) ? $data['guarantee_period'] : "";
        $tax = (isset($data['pro_input_tax']) && !empty($data['pro_input_tax'])) ? implode(',', (array) $data['pro_input_tax']) : '';
        $video_type = (isset($data['video_type']) && !empty($data['video_type'])) ? $data['video_type'] : "";
        $video = (!empty($video_type)) ? (($video_type == 'youtube' || $video_type == 'vimeo') ? $data['video'] : $data['pro_input_video']) : "";
        $is_attachment_required = (isset($data['is_attachment_required']) && !empty($data['is_attachment_required'])) ? $data['is_attachment_required'] : '0';
        $hsn_code = (isset($data['hsn_code']) && !empty($data['hsn_code'])) ? $data['hsn_code'] : "";
        $download_type = (isset($data['download_link_type']) && !empty($data['download_link_type'])) ? $data['download_link_type'] : "";
        $download_link = (!empty($download_type)) ? (($download_type == 'add_link') ? $data['download_link'] : $data['pro_input_zip']) : "";
        $low_stock_limit = (isset($data['low_stock_limit'])) ? $data['low_stock_limit'] : 0;
        $deliverable_zipcodes_group = !empty($data['deliverable_zipcodes_group']) && is_array($data['deliverable_zipcodes_group'])
        ? implode(',', $data['deliverable_zipcodes_group'])
        : '';

        $pickup_location = (isset($data['pickup_location'])) ? $data['pickup_location'] : null;


        $deliverable_cities_group = !empty($data['deliverable_cities_group']) && is_array($data['deliverable_cities_group'])
            ? implode(',', $data['deliverable_cities_group'])
            : '';
        $pro_data = [
            'name' => $data['pro_input_name'],
            'short_description' => $short_description,
            'slug' => $slug,
            'type' => $pro_type,
            'tax' => $tax,
            'category_id' => $category_id,
            'seller_id' => $seller_id,
            'made_in' => $made_in,
            'brand' => $brand,
            'indicator' => $indicator,
            'image' => $main_image_name,
            'total_allowed_quantity' => $total_allowed_quantity,
            'minimum_order_quantity' => $minimum_order_quantity,
            'quantity_step_size' => $quantity_step_size,
            'warranty_period' => $warranty_period,
            'guarantee_period' => $guarantee_period,
            'other_images' => $other_images,
            'video_type' => $video_type,
            'video' => $video,
            'tags' => $tags,
            'seo_page_title' => $seo_page_title,
            'seo_meta_keywords' => $seo_meta_keywords,
            'seo_meta_description' => $seo_meta_description,
            'seo_og_image' => $seo_og_image,
            'status' => $require_products_approval,
            'description' => $description,
            'extra_description' => $extra_description,
            'deliverable_type' => isset($data['deliverable_type']) && !empty($data['deliverable_type']) ? $data['deliverable_type'] : 0,
            'deliverable_group_type' => isset($data['deliverable_group_type']) && !empty($data['deliverable_group_type']) ? $data['deliverable_group_type'] : 0,
            'deliverable_city_group_type' => isset($data['deliverable_city_group_type']) && !empty($data['deliverable_city_group_type']) ? $data['deliverable_city_group_type'] : 0,
            'deliverable_city_type' => isset($data['deliverable_city_type']) && !empty($data['deliverable_city_type']) ? $data['deliverable_city_type'] : 0,
            'deliverable_zipcodes' => (isset($data['deliverable_type']) && !empty($data['deliverable_type']) && ($data['deliverable_type'] == ALL || $data['deliverable_type'] == NONE)) ? NULL : $data['zipcodes'],
            'deliverable_zipcodes_group' => (isset($data['deliverable_group_type']) && !empty($data['deliverable_group_type']) && ($data['deliverable_group_type'] == ALL || $data['deliverable_group_type'] == NONE)) ? NULL : $deliverable_zipcodes_group,
            'deliverable_cities' => (isset($data['deliverable_city_type']) && !empty($data['deliverable_city_type']) && ($data['deliverable_city_type'] == ALL || $data['deliverable_city_type'] == NONE)) ? NULL : $data['cities'],
            'deliverable_cities_group' => (isset($data['deliverable_city_group_type']) && !empty($data['deliverable_city_group_type']) && ($data['deliverable_city_group_type'] == ALL || $data['deliverable_city_group_type'] == NONE)) ? NULL : $deliverable_cities_group,
            'hsn_code' => $hsn_code,
            'pickup_location' => $pickup_location,
            'low_stock_limit' => $low_stock_limit,
            'is_attachment_required' => $is_attachment_required
        ];
        if ($data['product_type'] == 'simple_product') {
            if (isset($data['simple_product_stock_status']) && empty($data['simple_product_stock_status'])) {
                $pro_data['stock_type'] = NULL;
                $pro_data['sku'] = NULL;
                $pro_data['stock'] = NULL;
            }

            if (isset($data['simple_product_stock_status']) && in_array($data['simple_product_stock_status'], array('0', '1'))) {
                $pro_data['stock_type'] = '0';
            }

            if (isset($data['simple_product_stock_status']) && in_array($data['simple_product_stock_status'], array('0', '1'))) {
                if (!empty($data['product_sku'])) {
                    $pro_data['sku'] = $data['product_sku'];
                }
                $pro_data['stock'] = $data['product_total_stock'];
                $pro_data['availability'] = $data['simple_product_stock_status'];
            }
        }

        if ((isset($data['variant_stock_status']) || $data['variant_stock_status'] == '' || empty($data['variant_stock_status']) || $data['variant_stock_status'] == ' ') && $data['product_type'] == 'variable_product') {
            $pro_data['stock_type'] = NULL;
        }
        if (isset($data['variant_stock_level_type']) && !empty($data['variant_stock_level_type']) && $data['product_type'] != 'digital_product') {
            $pro_data['stock_type'] = ($data['variant_stock_level_type'] == 'product_level') ? 1 : 2;
            
            if ($data['variant_stock_level_type'] == 'product_level') {
                if (!empty($data['sku_variant_type'])) {
                    $pro_data['sku'] = $data['sku_variant_type'];
                }
                if (!empty($data['total_stock_variant_type'])) {
                    $pro_data['stock'] = $data['total_stock_variant_type'];
                }
                if (isset($data['variant_status'])) {
                    $pro_data['availability'] = $data['variant_status'];
                }
            }
        }

        if (isset($data['is_attachment_required']) && $data['is_attachment_required']) {
            $pro_data['is_attachment_required'] = '1';
        }

        if ($data['product_type'] != 'digital_product' && isset($data['is_returnable']) && $data['is_returnable'] != "" && ($data['is_returnable'] == "on" || $data['is_returnable'] == '1')) {
            $pro_data['is_returnable'] = '1';
        } else {
            $pro_data['is_returnable'] = '0';
        }

        if ($data['product_type'] != 'digital_product' && isset($data['is_cancelable']) && $data['is_cancelable'] != "" && ($data['is_cancelable'] == "on" || $data['is_cancelable'] == '1')) {
            $pro_data['is_cancelable'] = '1';
            $pro_data['cancelable_till'] = $data['cancelable_till'];
        } else {
            $pro_data['is_cancelable'] = '0';
            $pro_data['cancelable_till'] = '';
        }

        if (isset($data['download_allowed']) && $data['download_allowed'] != "" && ($data['download_allowed'] == "on" || $data['download_allowed'] == '1')) {
            $pro_data['download_allowed'] = '1';
            $pro_data['download_type'] = $download_type;
            $pro_data['download_link'] = $download_link;
        } else {
            $pro_data['download_allowed'] = '0';
            $pro_data['download_type'] = '';
            $pro_data['download_link'] = '';
        }

        if ($data['product_type'] != 'digital_product' && isset($data['cod_allowed']) && $data['cod_allowed'] != "" && ($data['cod_allowed'] == "on" || $data['cod_allowed'] == '1')) {
            $pro_data['cod_allowed'] = '1';
        } else {
            $pro_data['cod_allowed'] = '0';
        }

        if (isset($data['is_in_affiliate']) && $data['is_in_affiliate'] != "" && ($data['is_in_affiliate'] == "on" || $data['is_in_affiliate'] == '1')) {
            $pro_data['is_in_affiliate'] = '1';
        } else {
            $pro_data['is_in_affiliate'] = '0';
        }
        if (isset($data['is_prices_inclusive_tax']) && $data['is_prices_inclusive_tax'] != "" && ($data['is_prices_inclusive_tax'] == "on" || $data['is_prices_inclusive_tax'] == '1')) {
            $pro_data['is_prices_inclusive_tax'] = '1';
        } else {
            $pro_data['is_prices_inclusive_tax'] = '0';
        }

        $variant_images = (!empty($data['variant_images']) && isset($data['variant_images'])) ? $data['variant_images'] : [];

        if (isset($data['edit_product_id']) && !empty($data['edit_product_id'])) {
            if (empty($main_image_name)) {
                unset($pro_data['image']);
            }

            $pro_data['other_images'] = json_encode($other_images, 1);
            $this->db->set($pro_data)->where('id', $data['edit_product_id'])->update('products');
        } else {
            $pro_data['other_images'] = json_encode($other_images, 1);
            $this->db->insert('products', $pro_data);
        }
        $pro_variance_data['weight'] = 0.0;
        $p_id = (isset($data['edit_product_id']) && !empty($data['edit_product_id'])) ? $data['edit_product_id'] : $this->db->insert_id();
        $pro_variance_data['product_id'] = $p_id;
        $pro_attr_data = [
            'product_id' => $p_id,
            'attribute_value_ids' => strval($data['attribute_values']),
        ];
        if (isset($p_id) && !empty($p_id)) {
            recalculateTaxedPrice([$p_id]);
        }

        if (isset($data['edit_product_id']) && !empty($data['edit_product_id'])) {
            $this->db->where('product_id', $data['edit_product_id'])->update('product_attributes', $pro_attr_data);
        } else {
            $this->db->insert('product_attributes', $pro_attr_data);
        }

        if ($pro_type == 'simple_product') {
            $pro_variance_data = [
                'product_id' => $p_id,
                'price' => $data['simple_price'],
                'special_price' => (isset($data['simple_special_price']) && !empty($data['simple_special_price'])) ? $data['simple_special_price'] : $data['simple_price'],
                'stock' => (isset($data['product_total_stock']) && !empty($data['product_total_stock'])) ? floatval($data['product_total_stock']) : NULL,
                'weight' => (isset($data['weight']) && !empty($data['weight'])) ? floatval($data['weight']) : 0,
                'height' => (isset($data['height']) && !empty($data['height'])) ? $data['height'] : 0,
                'breadth' => (isset($data['breadth']) && !empty($data['breadth'])) ? $data['breadth'] : 0,
                'length' => (isset($data['length']) && !empty($data['length'])) ? $data['length'] : 0,
            ];

            if (isset($data['edit_product_id']) && !empty($data['edit_product_id'])) {
                if (isset($_POST['reset_settings']) && trim($_POST['reset_settings']) == '1') {
                    $this->db->insert('product_variants', $pro_variance_data);
                } else {
                    $this->db->where('product_id', $data['edit_product_id'])->update('product_variants', $pro_variance_data);
                }
            } else {
                $this->db->insert('product_variants', $pro_variance_data);
            }
        } elseif ($pro_type == 'digital_product') {
            $pro_variance_data = [
                'product_id' => $p_id,
                'price' => $data['simple_price'],
                'special_price' => (isset($data['simple_special_price']) && !empty($data['simple_special_price'])) ? $data['simple_special_price'] : $data['simple_price'],
            ];

            if (isset($data['edit_product_id']) && !empty($data['edit_product_id'])) {
                if (isset($_POST['reset_settings']) && trim($_POST['reset_settings']) == '1') {
                    $this->db->insert('product_variants', $pro_variance_data);
                } else {
                    $this->db->where('product_id', $data['edit_product_id'])->update('product_variants', $pro_variance_data);
                }
            } else {
                $this->db->insert('product_variants', $pro_variance_data);
            }
        } 
        else {

            $flag = " ";
            if (isset($data['variant_stock_status']) && $data['variant_stock_status'] == '0') {
                if ($data['variant_stock_level_type'] == "product_level") {
                    $flag = "product_level";
                    $pro_variance_data['sku'] = (isset($data['sku_variant_type']) && !empty($data['sku_variant_type'])) ? $data['sku_variant_type'] : '';
                    $pro_variance_data['stock'] = (isset($data['total_stock_variant_type']) && !empty($data['total_stock_variant_type'])) ? $data['total_stock_variant_type'] : '';
                    $pro_variance_data['availability'] = (isset($data['variant_status']) && !empty($data['variant_status'])) ? $data['variant_status'] : '';
                    $variant_price = (isset($data['variant_price']) && !empty($data['variant_price'])) ? $data['variant_price'] : '';
                    $variant_special_price = (isset($data['variant_special_price']) && !empty($data['variant_special_price'])) ? $data['variant_special_price'] : $data['variant_price'];
                    $variant_weight = (isset($data['weight']) && !empty($data['weight'])) ? $data['weight'] : 0.0;
                    $variant_height = (isset($data['height']) && !empty($data['height'])) ? $data['height'] : 0.0;
                    $variant_breadth = (isset($data['breadth']) && !empty($data['breadth'])) ? $data['breadth'] : 0.0;
                    $variant_length = (isset($data['length']) && !empty($data['length'])) ? $data['length'] : 0.0;
                } else {
                    $flag = "variant_level";
                    $variant_price = (isset($data['variant_price']) && !empty($data['variant_price'])) ? $data['variant_price'] : '';
                    $variant_special_price = (isset($data['variant_special_price']) && !empty($data['variant_special_price'])) ? $data['variant_special_price'] : $data['variant_price'];
                    $variant_sku = $data['variant_sku'];
                    $variant_total_stock = $data['variant_total_stock'];
                    $variant_stock_status = $data['variant_level_stock_status'];
                    $variant_weight = (isset($data['weight']) && !empty($data['weight'])) ? $data['weight'] : 0.0;
                    $variant_height = (isset($data['height']) && !empty($data['height'])) ? $data['height'] : 0.0;
                    $variant_breadth = (isset($data['breadth']) && !empty($data['breadth'])) ? $data['breadth'] : 0.0;
                    $variant_length = (isset($data['length']) && !empty($data['length'])) ? $data['length'] : 0.0;
                }
            } else {
                $variant_price = (isset($data['variant_price']) && !empty($data['variant_price'])) ? $data['variant_price'] : '';
                $variant_special_price = (isset($data['variant_special_price']) && !empty($data['variant_special_price'])) ? $data['variant_special_price'] : $data['variant_price'];
                $variant_weight = (isset($data['weight']) && !empty($data['weight'])) ? $data['weight'] : 0.0;
                $variant_height = (isset($data['height']) && !empty($data['height'])) ? $data['height'] : 0.0;
                $variant_breadth = (isset($data['breadth']) && !empty($data['breadth'])) ? $data['breadth'] : 0.0;
                $variant_length = (isset($data['length']) && !empty($data['length'])) ? $data['length'] : 0.0;
            }

            if (!empty($data['variants_ids'])) {

                $variants_ids = $data['variants_ids'];

                // --------------------------------------------------
                // 1. Fetch existing variants from DB
                // --------------------------------------------------
                $existing_variants = [];
                if (isset($data['edit_product_id']) && !empty($data['edit_product_id'])) {
                    $this->db->where('product_id', $data['edit_product_id']);
                    $this->db->where('status !=', 0);
                    $existing_variants = $this->db->get('product_variants')->result_array();
                }

                // --------------------------------------------------
                // 2. Create DB variant map (normalized)
                // --------------------------------------------------
                $dbVariantMap = [];
                foreach ($existing_variants as $row) {
                    $key = normalize_variant_ids($row['attribute_value_ids']);
                    $dbVariantMap[$key] = $row;
                }

                // --------------------------------------------------
                // 3. Create POST variant map (normalized → index)
                // --------------------------------------------------
                $postedVariantMap = [];
                foreach ($variants_ids as $i => $value) {
                    $key = normalize_variant_ids($value);
                    $postedVariantMap[$key] = $i;
                }

                // --------------------------------------------------
                // 4. UPDATE existing & INSERT new
                // --------------------------------------------------
                $formIndex = 0;

                foreach ($postedVariantMap as $variantKey => $unusedIndex) {

                    $pro_variance_data = [];

                    if ($flag === "variant_level") {
                        $pro_variance_data['price'] = $variant_price[$formIndex];
                        $pro_variance_data['special_price'] =
                            !empty($variant_special_price[$formIndex])
                            ? $variant_special_price[$formIndex]
                            : $variant_price[$formIndex];

                        $pro_variance_data['sku'] = $variant_sku[$formIndex];
                        $pro_variance_data['stock'] = $variant_total_stock[$formIndex];
                        $pro_variance_data['availability'] = $variant_stock_status[$formIndex];
                    } else {
                        $pro_variance_data['price'] = $variant_price[$formIndex];
                        $pro_variance_data['special_price'] =
                            !empty($variant_special_price[$formIndex])
                            ? $variant_special_price[$formIndex]
                            : $variant_price[$formIndex];
                        
                        $pro_variance_data['sku'] = (isset($data['sku_variant_type']) && !empty($data['sku_variant_type'])) ? $data['sku_variant_type'] : '';
                        $pro_variance_data['stock'] = (isset($data['total_stock_variant_type']) && !empty($data['total_stock_variant_type'])) ? $data['total_stock_variant_type'] : '';
                        $pro_variance_data['availability'] = (isset($data['variant_status']) && !empty($data['variant_status'])) ? $data['variant_status'] : '';
                    }

                    $pro_variance_data['weight'] = $variant_weight[$formIndex] ?? '0.0';
                    $pro_variance_data['height'] = $variant_height[$formIndex] ?? '0.0';
                    $pro_variance_data['breadth'] = $variant_breadth[$formIndex] ?? '0.0';
                    $pro_variance_data['length'] = $variant_length[$formIndex] ?? '0.0';

                    $pro_variance_data['images'] =
                        !empty($variant_images[$formIndex])
                        ? json_encode($variant_images[$formIndex])
                        : '[]';

                    $pro_variance_data['attribute_value_ids'] = $variantKey;
                    $pro_variance_data['product_id'] = $p_id;
                    $pro_variance_data['status'] = 1;

                    // UPDATE
                    if (isset($dbVariantMap[$variantKey])) {
                        $this->db->where('id', $dbVariantMap[$variantKey]['id']);
                        $this->db->update('product_variants', $pro_variance_data);
                    }
                    // INSERT
                    else {
                        $this->db->insert('product_variants', $pro_variance_data);
                    }

                    $formIndex++; // 🔑 increment safely
                }

                // --------------------------------------------------
                // 5. Deactivate removed variants (status = 7)
                // --------------------------------------------------
                foreach ($dbVariantMap as $variantKey => $row) {

                    if (!isset($postedVariantMap[$variantKey])) {
                        $this->db->where('id', $row['id']);
                        $this->db->update('product_variants', ['status' => 7]);
                    }
                }
            }


            // if (!empty($data['variants_ids'])) {
            //     $variants_ids = $data['variants_ids'];

            //     log_message('error', 'edit_variant_id --> ' . var_export($data['edit_variant_id'], true));

            //     if (isset($data['edit_variant_id']) && !empty($data['edit_variant_id'])) {

            //         $this->db->set('status', 7)->where('product_id', $data['edit_product_id'])->where('status !=', 0)->where_in('id', $data['edit_variant_id'])->update('product_variants');
            //     }

            //     foreach ($variants_ids as $value) {
            //         $parts = explode(',', $value);
            //         sort($parts, SORT_NUMERIC);      // normalize order
            //         $key = implode(',', $parts);

            //         $unique[$key] = $key;            // using key removes duplicates
            //     }
            //     $result = array_values($unique);
            //     log_message('error', 'result data --> ' . var_export($result, true));

            //     for ($i = 0; $i < count(array_unique($variants_ids)); $i++) {
            //         $value = str_replace(' ', ',', trim($variants_ids[$i]));
            //         if ($flag == "variant_level") {
            //             $pro_variance_data['price'] = $variant_price[$i];
            //             $pro_variance_data['special_price'] = (isset($variant_special_price[$i]) && !empty($variant_special_price[$i])) ? $variant_special_price[$i] : $variant_price[$i];
            //             $pro_variance_data['weight'] = (isset($variant_weight[$i]) && !empty($variant_weight[$i])) ? $variant_weight[$i] : '0.0';
            //             $pro_variance_data['height'] = (isset($variant_height[$i]) && !empty($variant_height[$i])) ? $variant_height[$i] : '0.0';
            //             $pro_variance_data['breadth'] = (isset($variant_breadth[$i]) && !empty($variant_breadth[$i])) ? $variant_breadth[$i] : '0.0';
            //             $pro_variance_data['length'] = (isset($variant_length[$i]) && !empty($variant_length[$i])) ? $variant_length[$i] : '0.0';
            //             $pro_variance_data['sku'] = $variant_sku[$i];
            //             $pro_variance_data['stock'] = $variant_total_stock[$i];
            //             $pro_variance_data['availability'] = $variant_stock_status[$i];
            //         } else {
            //             $pro_variance_data['price'] = $variant_price[$i];
            //             $pro_variance_data['special_price'] = (isset($variant_special_price[$i]) && !empty($variant_special_price[$i])) ? $variant_special_price[$i] : $variant_price[$i];
            //             $pro_variance_data['weight'] = (isset($variant_weight[$i]) && !empty($variant_weight[$i])) ? $variant_weight[$i] : '0.0';
            //             $pro_variance_data['height'] = (isset($variant_height[$i]) && !empty($variant_height[$i])) ? $variant_height[$i] : '0.0';
            //             $pro_variance_data['breadth'] = (isset($variant_breadth[$i]) && !empty($variant_breadth[$i])) ? $variant_breadth[$i] : '0.0';
            //             $pro_variance_data['length'] = (isset($variant_length[$i]) && !empty($variant_length[$i])) ? $variant_length[$i] : '0.0';
            //         }

            //         if (isset($variant_images[$i]) && !empty($variant_images[$i])) {
            //             $pro_variance_data['images'] = json_encode($variant_images[$i]);
            //         } else {
            //             $pro_variance_data['images'] = '[]';
            //         }

            //         $pro_variance_data['attribute_value_ids'] = $value;

            //         // if (isset($data['edit_variant_id'][$i]) && !empty($data['edit_variant_id'][$i])) {

            //         //     $this->db->where('id', $data['edit_variant_id'][$i])->update('product_variants', $pro_variance_data);
            //         // } else {
            //         $this->db->insert('product_variants', $pro_variance_data);
            //         // }
            //         // $this->db->insert('product_variants', $pro_variance_data);

            //     }
            // }
        }
    }


    public function get_product_details($flag = NULL, $seller_id = NULL, $p_status = NULL, $from_faq = 0, $from_select = 0)
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = '';
        if (isset($_GET['offset']) && !empty($_GET['offset']))

            $offset = $_GET['offset'];

        if (isset($_GET['limit']) && !empty($_GET['limit']))

            $limit = $_GET['limit'];

        if (isset($_GET['sort']) && !empty($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "product_variants.id";
            } else {
                $sort = $_GET['sort'];
            }

        if (isset($_GET['order']) && !empty($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = trim($_GET['search']);
            $multipleWhere = ['p.`id`' => $search, 'p.`name`' => $search, 'p.`description`' => $search, 'p.`short_description`' => $search, 'c.name' => $search];
        }

        if (isset($_GET['category_id']) || isset($_GET['search'])) {
            if (isset($_GET['search']) and $_GET['search'] != '') {
                $multipleWhere['p.`category_id`'] = $search;
            }
            if (isset($_GET['category_id']) and $_GET['category_id'] != '') {
                $category_id = $_GET['category_id'];
            }
        }
        if (isset($_GET['brand_id']) || isset($_GET['search'])) {
            // if (isset($_GET['search']) && $_GET['search'] != '') {
            //     $multipleWhere['p.`brand_id`'] = $search;
            // }
            if (isset($_GET['brand_id']) && $_GET['brand_id'] != '' && !empty($_GET['brand_id'])) {
                $brand_id = $_GET['brand_id'];
            }
        }

        $count_res = $this->db->select(' COUNT( distinct(p.id)) as `total` ')
            ->join(" categories c", "p.category_id=c.id ")
            ->join(" brands b", "p.brand=b.id ", 'left')
            ->join('product_variants', 'product_variants.product_id = p.id')->join("seller_data sd", "p.seller_id=sd.user_id ");

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_Start();
            $count_res->or_like($multipleWhere);
            $count_res->group_End();
        }

        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        if ($flag == 'low') {
            $where = "p.stock_type is  NOT NULL";
            $count_res->where($where);
            $count_res->group_Start();
            // $count_res->where('p.stock <=', $low_stock_limit);
            $count_res->where('(CASE 
                WHEN p.low_stock_limit > 0 THEN p.stock <= p.low_stock_limit 
                ELSE p.stock <= sd.low_stock_limit 
            END)');
            $count_res->where('p.availability  =', '1');
            // $count_res->or_where('product_variants.stock <=', $low_stock_limit);
            $count_res->or_where('(CASE 
                WHEN p.low_stock_limit > 0 THEN product_variants.stock <= p.low_stock_limit 
                ELSE product_variants.stock <= sd.low_stock_limit 
            END)');
            $count_res->where('product_variants.availability  =', '1');
            $count_res->group_End();
        }

        if (isset($seller_id) && $seller_id != "") {
            $count_res->where("p.seller_id", $seller_id);
        }

        if (isset($p_status) && $p_status != "") {
            $count_res->where("p.status", $p_status);
        }


        if (isset($from_faq) && $from_faq != "0") {
            $count_res->where("p.status", $from_faq);
        }

        if ($flag == 'sold') {
            $where = "p.stock_type is  NOT NULL";
            $count_res->where($where);
            $count_res->group_Start();
            $count_res->where('p.stock ', '0');
            $count_res->where('p.availability ', '0');
            $count_res->or_where('product_variants.stock ', '0');
            $count_res->where('product_variants.availability ', '0');
            $count_res->group_End();
        }

        if (isset($category_id) && !empty($category_id)) {
            $count_res->group_Start();
            $count_res->or_where('p.category_id', $category_id);
            $count_res->or_where('c.parent_id', $category_id);
            $count_res->group_End();
        }
        if (isset($brand_id) && !empty($brand_id)) {
            $count_res->group_Start();
            $count_res->or_where('p.brand', $brand_id);
            $count_res->group_End();
        }

        $product_count = $count_res->get('products p')->result_array();

        foreach ($product_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select('product_variants.id AS id,c.name as category_name,b.name as brand_name,sd.store_name, p.id as pid,p.rating,p.no_of_ratings,p.name, p.type, p.image, p.status,p.brand,product_variants.price , product_variants.special_price, product_variants.stock')
            ->join("categories c", "p.category_id=c.id")
            ->join("brands b", "p.brand=b.id", 'left')
            ->join("seller_data sd", "sd.user_id=p.seller_id ")
            ->join('product_variants', 'product_variants.product_id = p.id');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_Start();
            $search_res->or_like($multipleWhere);
            $search_res->group_End();
        }

        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }
        if ($flag != null && $flag == 'low') {
            $search_res->group_Start();
            $where = "p.stock_type is  NOT NULL";
            $search_res->where($where);
            // $search_res->where('p.stock <=', $low_stock_limit);
            $search_res->where('(CASE 
                WHEN p.low_stock_limit > 0 THEN p.stock <= p.low_stock_limit 
                ELSE p.stock <= sd.low_stock_limit 
            END)');
            $search_res->where('p.availability  =', '1');
            // $search_res->or_where('product_variants.stock <=', $low_stock_limit);
            $search_res->or_where('(CASE 
                WHEN p.low_stock_limit > 0 THEN product_variants.stock <= p.low_stock_limit 
                ELSE product_variants.stock <= sd.low_stock_limit 
            END)');
            $search_res->where('product_variants.availability  =', '1');
            $search_res->group_End();
        }

        if ($flag != null && $flag == 'sold') {
            $search_res->group_Start();
            $where = "p.stock_type is  NOT NULL";
            $search_res->where($where);
            $search_res->where('p.stock ', '0');
            $search_res->where('p.availability ', '0');
            $search_res->or_where('product_variants.stock ', '0');
            $search_res->where('product_variants.availability ', '0');
            $search_res->group_End();
        }

        if (isset($category_id) && !empty($category_id)) {
            //category select where
            $search_res->group_Start();
            $search_res->or_where('p.category_id', $category_id);
            $search_res->or_where('c.parent_id', $category_id);
            $search_res->group_End();
        }
        if (isset($brand_id) && !empty($brand_id)) {
            //category select where
            $search_res->group_Start();
            $search_res->or_where('p.brand', $brand_id);
            $search_res->group_End();
        }

        if (isset($seller_id) && $seller_id != "") {
            $search_res->where("p.seller_id", $seller_id);
        }

        if (isset($p_status) && $p_status != "") {
            $search_res->where("p.status", $p_status);
        }

        if (isset($from_faq) && $from_faq != "0") {
            $search_res->where("p.status", $from_faq);
        }

        if (isset($from_select) && ($from_select != 0 || $from_select != '0')) {

            $pro_search_res = $search_res->group_by('pid')->order_by($sort, $order)->get('products p')->result_array();
        } else {
            $pro_search_res = $search_res->group_by('pid')->order_by($sort, $order)->limit($limit, $offset)->get('products p')->result_array();
        }



        $currency = get_settings('currency');
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        foreach ($pro_search_res as $row) {

            $row = output_escaping($row);

            $view_url = "";
            $create_url = "";
            $delete_url = "";

            if ($this->ion_auth->is_seller()) {
                $view_url = base_url("seller/product/view-product?edit_id=$row[pid]");
            } else if ($this->ion_auth->is_admin()) {
                $view_url = base_url("admin/product/view-product?edit_id=$row[pid]");
            }

            if ($this->ion_auth->is_seller()) {
                $create_url = base_url("seller/product/create-product?edit_id=$row[pid]");
                $delete_url = base_url("seller/product/delete_product?edit_id=$row[pid]");
            } else if ($this->ion_auth->is_admin()) {
                $create_url = base_url("admin/product/create-product?edit_id=$row[pid]");
                $delete_url = base_url("admin/product/delete_product?edit_id=$row[pid]");
            }
            // Create dropdown menu for operate column
            $operate = '
            <div class="dropdown">
                <button class="btn btn-secondary btn-sm bg-secondary-lt" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end table-dropdown-menu table-dropdown-menu">';

            // View Product
            // if ($this->ion_auth->is_seller()) {
            //     $view_url = base_url("seller/product?view_product_id=$row[pid]");
            // } else if ($this->ion_auth->is_admin()) {
            //     $view_url = base_url("admin/product?view_product_id=$row[pid]");
            // }
            $operate .= '<li>
                <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#viewProductOffcanvas" data-id="' . $row['pid'] . '">
                    <i class="ti ti-eye me-2"></i>View
                </a>
            </li>';

            // Edit Product
            $operate .= '<li>
                <a class="dropdown-item" href="' . $create_url . '" data-id="' . $row['pid'] . '">
                    <i class="ti ti-pencil me-2"></i>Edit
                </a>
            </li>';

            // Status actions based on current status
            if ($row['status'] == '2') {
                $tempRow['status'] = '<a class="badge bg-danger-lt text-white">Not-Approved</a>';
                if ($this->ion_auth->is_seller()) {
                    $operate .= '<li>
                        <span class="dropdown-item-text">
                            <i class="ti ti-ban me-2"></i>Not Approved
                        </span>
                    </li>';
                } else {
                    // Approve
                    $operate .= '<li>
                        <a class="dropdown-item update_active_status" href="javascript:void(0)" 
                           data-table="products" 
                           data-id="' . $row['pid'] . '" 
                           data-status="' . $row['status'] . '">
                            <i class="ti ti-circle-check me-2"></i>Approve
                        </a>
                    </li>';
                }
            } else if ($row['status'] == '1') {
                $tempRow['status'] = '<a class="badge bg-success-lt text-success">Active</a>';
                $operate .= '<li>
                    <a class="dropdown-item update_active_status" href="javascript:void(0)" 
                       data-table="products" 
                       data-id="' . $row['pid'] . '" 
                       data-status="' . $row['status'] . '">
                        <i class="ti ti-toggle-right me-2"></i>Deactivated
                    </a>
                </li>';
            } else if ($row['status'] == '0') {
                $tempRow['status'] = '<a class="badge bg-danger-lt text-white">Inactive</a>';
                $operate .= '<li>
                    <a class="dropdown-item update_active_status" href="javascript:void(0)" 
                       data-table="products" 
                       data-id="' . $row['pid'] . '" 
                       data-status="' . $row['status'] . '">
                        <i class="ti ti-toggle-left me-2"></i>Activate
                    </a>
                </li>';
            }

            // View Ratings
            // $operate .= '<li>
            //     <a class="dropdown-item view-rating" href="javascript:void(0)" 
            //        data-id="' . $row['pid'] . '" 
            //        data-bs-toggle="offcanvas" 
            //        data-bs-target="#product-rating-modal">
            //         <i class="ti ti-star me-2"></i>View Ratings
            //     </a>
            // </li>';

            // View Product FAQs
            // $operate .= '<li>
            //     <a class="dropdown-item" href="javascript:void(0)" 
            //        data-id="' . $row['pid'] . '" 
            //        data-bs-toggle="offcanvas" 
            //        data-bs-target="#product-faqs-modal">
            //         <i class="ti ti-messages me-2"></i>View FAQs
            //     </a>
            // </li>';

            // Divider
            $operate .= '<li><hr class="dropdown-divider"></li>';

            // Delete Product
            $operate .= '<li>
                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                       x-data="ajaxDelete({
                           url: \'' . $delete_url . '\',
                           id: \'' . $row['pid'] . '\',
                           tableSelector: \'#products_table\',
                           confirmTitle: \'Delete Product\',
                           confirmMessage: \'Do you really want to delete this product?\'
                       })"
                       @click="deleteItem">
                        <i class="ti ti-trash me-2"></i>Delete
                    </a>
                </li>';

            $operate .= '
                </ul>
            </div>';

            $attr_values = get_variants_values_by_pid($row['pid']);
            $tempRow['id'] = $row['pid'];
            $tempRow['varaint_id'] = $row['id'];
            $tempRow['name'] = $row['name'] . '<br><small>' . ucwords(str_replace('_', ' ', $row['type'])) . '</small><br><small> By </small><b>' . $row['store_name'] . '</b>';
            $tempRow['product_name'] = $row['name'] . '   ' . '<small>(' . ucwords(str_replace('_', ' ', $row['type'])) . '</small><small> By </small><b>' . $row['store_name'] . ')</b>';
            $tempRow['text'] = $row['name'];
            $tempRow['type'] = $row['type'];
            $tempRow['text'] = $row['name'];
            $tempRow['brand'] = $row['brand_name'];
            $tempRow['category_name'] = $row['category_name'];
            $tempRow['price'] = ($row['special_price'] == null || $row['special_price'] == '0') ? $currency . $row['price'] : $currency . $row['special_price'];
            $tempRow['stock'] = $row['stock'];
            $variations = '';
            foreach ($attr_values as $variants) {
                if (isset($attr_values[0]['attr_name'])) {
                    if (!empty($variations)) {
                        $variations .= '---------------------<br>';
                    }
                    $attr_name = explode(',', $variants['attr_name']);
                    $varaint_values = explode(',', $variants['variant_values']);

                    for ($i = 0; $i < count($attr_name); $i++) {
                        if (isset($varaint_values) && !empty($varaint_values[$i])) {
                            $variations .= '<b>' . $attr_name[$i] . '</b> : ' . $varaint_values[$i] . '&nbsp;&nbsp;<b> Varient id : </b>' . $variants['id'] . '<br>';
                        } else {
                            $variations .= '&nbsp;&nbsp;<b> Varient id : </b>' . $variants['id'] . '<br>';
                        }
                    }
                }
            }

            $tempRow['variations'] = (!empty($variations)) ? $variations : '-';
            $row['image'] = get_image_url($row['image'], 'thumb', 'sm');
            $tempRow['image'] = '
                    <div class="text-center" style="width:80px; height:80px; margin:auto; overflow:hidden; border-radius:8px; display:flex; align-items:center; justify-content:center; background:#f8f9fa;">
                        <a href="' . $row['image'] . '" data-lightbox="product">
                            <img src="' . $row['image'] . '" 
                                alt="Image" 
                                class="img-xl" 
                                style="max-width:100%; max-height:100%; object-fit:contain;">
                        </a>
                    </div>';

            $rating = (float) $row['rating'];
            $maxStars = 5;
            $starsHtml = '<div class="rating rating-xs">';


            for ($i = 1; $i <= $maxStars; $i++) {
                if ($i <= floor($rating)) {
                    $starsHtml .= '<i class="ti ti-star-filled text-warning"></i>';
                } elseif ($i - $rating < 1) {
                    $starsHtml .= '<i class="ti ti-star-half-filled text-warning"></i>';
                } else {
                    $starsHtml .= '<i class="ti ti-star "></i>';
                }
            }

            $starsHtml .= ' <span class="small ">(' . $row['rating'] . '/' . $row['no_of_ratings'] . ')</span>';
            $starsHtml .= '</div>';


            $tempRow['rating'] = $starsHtml;


            $tempRow['operate'] = $operate;

            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;


        if (isset($from_select) && ($from_select != 0 || $from_select != '0')) {


            print_r(json_encode($rows));
        } else {
            print_r(json_encode($bulkData));
        }
    }



    public function get_digital_product_details($flag = NULL, $seller_id = NULL, $p_status = NULL, $from_select = 0)
    {

        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = '';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "product_variants.id";
            } else {
                $sort = $_GET['sort'];
            }

        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = trim($_GET['search']);
            $multipleWhere = ['p.`id`' => $search, 'p.`name`' => $search, 'p.`description`' => $search, 'p.`short_description`' => $search, 'c.name' => $search];
        }

        if (isset($_GET['category_id']) || isset($_GET['search'])) {
            if (isset($_GET['search']) and $_GET['search'] != '') {
                $multipleWhere['p.`category_id`'] = $search;
            }

            if (isset($_GET['category_id']) and $_GET['category_id'] != '') {
                $category_id = $_GET['category_id'];
            }
        }

        $count_res = $this->db->select(' COUNT( distinct(p.id)) as `total` ')->join(" categories c", "p.category_id=c.id ")->join('product_variants', 'product_variants.product_id = p.id');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_Start();
            $count_res->or_like($multipleWhere);
            $count_res->group_End();
        }

        $where = ['p.`type` =' => 'digital_product'];
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }
        if (isset($p_status) && $p_status != "") {
            $count_res->where("p.status", 1);
        }
        $product_count = $count_res->get('products p')->result_array();
        foreach ($product_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select('product_variants.id AS id,c.name as category_name,sd.store_name, p.id as pid,p.rating,p.no_of_ratings,p.name, p.type, p.image, p.status,p.brand,product_variants.price , product_variants.special_price, product_variants.stock')
            ->join("categories c", "p.category_id=c.id")
            ->join("seller_data sd", "sd.user_id=p.seller_id ")
            ->join('product_variants', 'product_variants.product_id = p.id');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_Start();
            $search_res->or_like($multipleWhere);
            $search_res->group_End();
        }

        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        if (isset($from_select) && ($from_select != 0 || $from_select != '0')) {
            $pro_search_res = $search_res->group_by('pid')->order_by($sort, "DESC")->get('products p')->result_array();
        } else {
            $pro_search_res = $search_res->group_by('pid')->order_by($sort, "DESC")->limit($limit, $offset)->get('products p')->result_array();
        }

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        foreach ($pro_search_res as $row) {
            $row = output_escaping($row);
            $attr_values = get_variants_values_by_pid($row['pid']);
            $tempRow['id'] = $row['pid'];
            $tempRow['varaint_id'] = $row['id'];
            $tempRow['text'] = $row['name'];
            $tempRow['name'] = $row['name'] . '<br><small>' . ucwords(str_replace('_', ' ', $row['type'])) . '</small><br><small> By </small><b>' . $row['store_name'] . '</b>';
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        if (isset($from_select) && ($from_select != 0 || $from_select != '0')) {
            print_r(json_encode($rows));

        } else {

            print_r(json_encode($bulkData));
        }
    }

    function get_countries($search_term = "")
    {
        // Fetch users
        $this->db->select('*');
        $this->db->where("name like '%" . $search_term . "%'");
        $fetched_records = $this->db->get('countries');
        $countries = $fetched_records->result_array();

        // Initialize Array with fetched data
        $data = array();
        foreach ($countries as $country) {
            $data[] = array("id" => $country['name'], "text" => $country['name']);
        }
        return $data;
    }

    function get_brands($search_term = "")
    {
        // Fetch users
        $this->db->select('*');
        $this->db->where("name like '%" . $search_term . "%'");
        $this->db->where("status", 1);
        $fetched_records = $this->db->get('brands');
        $brands = $fetched_records->result_array();
        // Initialize Array with fetched data
        $data = array();
        foreach ($brands as $brand) {
            $data[] = array("id" => $brand['id'], "text" => $brand['name']);
        }
        return $data;
    }
    function get_categories($search_term = "")
    {
        // Fetch users
        $this->db->select('*');
        $this->db->where("name like '%" . $search_term . "%'");
        $this->db->where("status", 1);
        $fetched_records = $this->db->get('categories');
        $categories = $fetched_records->result_array();
        // Initialize Array with fetched data
        $data = array();
        foreach ($categories as $category) {
            $data[] = array("id" => $category['id'], "text" => $category['name']);
        }
        return $data;
    }

    function get_faqs_data($search_term = "")
    {
        // Fetch users

        $this->db->select('*');
        $this->db->where("question like '%" . $search_term . "%'");
        $fetched_records = $this->db->get('product_faqs');
        $faqs = $fetched_records->result_array();
        // Initialize Array with fetched data
        $data = array();
        foreach ($faqs as $faq) {
            $data[] = array("id" => $faq['id'], "text" => $faq['question']);
        }
        return $data;
    }

    function get_country_list($search = "", $offset = 0, $limit = 25)
    {
        $multipleWhere = '';
        $where = array();
        if (!empty($search)) {
            $multipleWhere = [
                '`name`' => $search,
            ];
        }

        $search_res = $this->db->select('id,name');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_start();
            $search_res->or_like($multipleWhere);
            $search_res->group_end();
        }

        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $countries = $search_res->limit($limit, $offset)->get('countries')->result_array();
        $bulkData = array();
        $bulkData['error'] = (empty($countries)) ? true : false;
        $bulkData['message'] = (empty($countries)) ? "Countries Not Found" : "Countries Retrived Successfully";
        if (!empty($countries)) {
            for ($i = 0; $i < count($countries); $i++) {
                $countries[$i] = output_escaping($countries[$i]);
            }
        }

        $bulkData['data'] = (empty($countries)) ? [] : $countries;
        return $bulkData;
    }

    function get_brand_list($search = "", $offset = 0, $limit = 25)
    {
        $multipleWhere = '';
        $where = array('status' => 1); // Add the condition for status = 1

        if (!empty($search)) {
            $multipleWhere = [
                '`name`' => $search,
            ];
        }

        $search_res = $this->db->select('id, name, image, slug');

        if (!empty($multipleWhere)) {
            $search_res->group_start();
            $search_res->or_like($multipleWhere);
            $search_res->group_end();
        }

        // Ensure status condition is applied
        if (!empty($where)) {
            $search_res->where($where);
        }

        $brands = $search_res->limit($limit, $offset)->get('brands')->result_array();

        $bulkData = array();
        $bulkData['error'] = empty($brands);
        $bulkData['message'] = empty($brands) ? "Brands Not Found" : "Brands Retrieved Successfully";

        if (!empty($brands)) {
            foreach ($brands as $i => $brand) {
                $brands[$i] = output_escaping($brand);
                $brands[$i]['image'] = base_url() . $brand['image'];
            }
        }

        $bulkData['data'] = $brands ?? [];
        return $bulkData;
    }


    /* add_product_faqs */

    function add_product_faqs($data)
    {
        $answered_by = fetch_details('users', 'id=' . $_SESSION['user_id'], 'username');
        $data = escape_array($data);
        if (isset($data['edit_product_faq']) && !empty($data['edit_product_faq'])) {
            $edit_data = [
                'answer' => $data['answer'],
                'answered_by' => $_SESSION['user_id'],
            ];

            $this->db->set($edit_data)->where('id', $data['edit_product_faq'])->update('product_faqs');
        } else {
            $faq_data = [
                'product_id' => $data['product_id'],
                'user_id' => $data['user_id'],
                'question' => $data['question'],
                'answer' => $data['answer'],
                'answered_by' => (isset($data['answer']) && ($data['answer']) != "") ? $data['answer_by'] : 0,
            ];

            $this->db->insert('product_faqs', $faq_data);
            return $this->db->insert_id();
        }
    }

    /* get_product_faqs */

    function get_product_faqs($id = '', $product_id = '', $user_id = '', $search = '', $offset = '0', $limit = '10', $sort = 'id', $order = 'DESC', $is_seller = false, $seller_id = '', $from_app = false)
    {

        $multipleWhere = '';
        $where = array();
        if (!empty($search)) {
            $multipleWhere = [
                '`pf.id`' => $search,
                '`pf.product_id`' => $search,
                '`pf.user_id`' => $search,
                '`pf.question`' => $search,
                '`pf.answer`' => $search
            ];
        }

        if (!empty($id)) {
            $where['pf.id'] = $id;
        }

        if (!empty($product_id)) {
            $where['pf.product_id'] = $product_id;
        }

        if (!empty($user_id)) {
            $where['pf.user_id'] = $user_id;
        }

        if (!empty($seller_id)) {
            $where['pf.seller_id'] = $seller_id;
        }
        if (isset($is_seller) && (($is_seller == FALSE))) {
            $where['pf.answered_by !='] = 0;
        }

        //  count of total product faqs

        $count_res = $this->db->select(' COUNT(pf.id) as `total`')
            ->join('users u', 'u.id=pf.user_id', 'left')
            ->join('products p', 'p.id=pf.product_id', 'left');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_start();
            $count_res->or_like($multipleWhere);
            $count_res->group_end();
        }

        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }

        $cat_count = $count_res->get('product_faqs pf')->result_array();

        foreach ($cat_count as $row) {
            $total = $row['total'];
        }

        // get product faqs data

        $search_res = $this->db->select('pf.*,u.username')
            ->join('users u', 'u.id=pf.user_id', 'left')
            ->join('products p', 'p.id=pf.product_id', 'left');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_start();
            $search_res->or_like($multipleWhere);
            $search_res->group_end();
        }

        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $faq_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('product_faqs pf')->result_array();

        $rows = $tempRow = $bulkData = array();


        if (!empty($faq_search_res)) {

            foreach ($faq_search_res as $row) {

                $row = output_escaping($row);
                $tempRow['id'] = $row['id'];
                $tempRow['product_id'] = $row['product_id'];
                $tempRow['user_id'] = $row['user_id'];
                $tempRow['username'] = $row['username'];
                $tempRow['question'] = $row['question'];
                $tempRow['votes'] = $row['votes'];
                $tempRow['answered_by'] = (isset($row['answered_by']) && $row['answered_by'] != '') ? $row['answered_by'] : '';
                $ans_by_name = fetch_details('users', 'id=' . $row['answered_by'], 'username');
                $tempRow['answered_by_name'] = (isset($row['answered_by']) && $row['answered_by'] != '' && !empty($ans_by_name[0]['username'])) ? $ans_by_name[0]['username'] : '';
                // $tempRow['date_added'] = $row['date_added'];
                if (isset($from_app) && $from_app == true) {
                    $tempRow['date_added'] = $row['date_added'];
                } else {

                    $date = new DateTime($row['date_added']);
                    $tempRow['date_added'] = $date->format('d-M-Y');
                }
                $tempRow['answer'] = (isset($row['answer']) && $row['answer'] != '') ? $row['answer'] : "";

                if (isset($tempRow) && !empty($tempRow)) {
                    $rows[] = $tempRow;
                }
            }
            $bulkData['error'] = (empty($faq_search_res)) ? true : false;
            $bulkData['message'] = (empty($faq_search_res)) ? 'FAQs does not exist' : 'FAQs retrieved successfully';
            $bulkData['total'] = (empty($faq_search_res)) ? 0 : $total;
            $bulkData['data'] = $rows;
        } else {
            $bulkData['error'] = true;
            $bulkData['message'] = 'FAQs does not exist';
            $bulkData['total'] = 0;
            $bulkData['data'] = [];
        }
        return $bulkData;
    }

    public function delete_faq($faq_id)
    {
        $faq_id = escape_array($faq_id);
        $this->db->delete('product_faqs', ['id' => $faq_id]);
    }

    public function get_faqs()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';

        $multipleWhere = '';

        if (isset($offset))
            $offset = $_GET['offset'];
        if (isset($limit))
            $limit = $_GET['limit'];
        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_GET['sort'];
            }

        if (isset($order) and $order != '') {
            $search = $order;
        }

        if (isset($_GET['product_id']) && $_GET['product_id'] != null) {
            $where['product_id'] = $_GET['product_id'];
        }

        if (isset($_GET['user_id']) && $_GET['user_id'] != null) {
            $where['user_id'] = $_GET['user_id'];
        }

        $count_res = $this->db->select(' COUNT(pf.id) as total  ')->join('users u', 'u.id=pf.user_id');
        if (isset($_GET['search']) && trim($_GET['search'])) {
            $search = trim($_GET['search']);
            $multipleWhere = ['pf.id' => $search, 'pf.product_id' => $search, 'pf.user_id' => $search];
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

        $search_res = $this->db->select('pf.*,u.username as user_name')->join('users u', 'u.id=pf.user_id');

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

            $row = output_escaping($row);

            $date = new DateTime($row['date_added']);

            if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {

                $operate = ' <a href="javascript:void(0)" class="edit_btn  btn-sm btn-success bg-success-lt mr-1 mb-1" title="View" data-id="' . $row['id'] . '" data-url="admin/product/">
                    <i class="ti ti-edit"></i>
                 </a>';

                $operate .= '<a class="btn btn-sm btn-danger-lt mr-1 mb-1 delete-product-faq" href="javascript:void(0)" title="Delete" data-id="' . $row['id'] . '">
                    <i class="ti ti-trash"></i>
                 </a>';
            } else {

                $operate = ' <a href="javascript:void(0)" class="edit_btn btn btn-sm btn-success bg-success-lt mr-1 mb-1" title="View" data-id="' . $row['id'] . '" data-url="seller/product/">
                    <i class="ti ti-edit"></i>
                 </a>';

                $operate .= '<a class="btn btn-sm btn-danger bg-danger-lt mr-1 mb-1 delete-seller-product-faq" href="javascript:void(0)" title="Delete" data-id="' . $row['id'] . '">
                    <i class="ti ti-trash"></i>
                 </a>';
            }


            $tempRow['id'] = $row['id'];

            $tempRow['user_id'] = $row['user_id'];

            $tempRow['product_id'] = $row['product_id'];

            $tempRow['votes'] = $row['votes'];

            $tempRow['question'] = $row['question'];

            $tempRow['answer'] = $row['answer'];

            $tempRow['answered_by'] = $row['answered_by'];

            $tempRow['username'] = $row['user_name'];

            $tempRow['date_added'] = $date->format('d-M-Y');

            $tempRow['operate'] = $operate;

            $rows[] = $tempRow;

            $i++;
        }

        $bulkData['rows'] = $rows;

        print_r(json_encode($bulkData));
    }

    public function get_stock_details()
    {

        $filters['show_only_stock_product'] = true;

        $offset = 0;

        $limit = 10;

        $sort = 'id';

        $order = 'ASC';

        $filters['search'] = (isset($_GET['search'])) ? $_GET['search'] : null;

        if (isset($_GET['seller_id'])) {

            $seller_id = $_GET['seller_id'];
        }

        if (isset($_GET['category_id'])) {

            $category_id = $_GET['category_id'];
        }

        if (isset($_GET['stock_type']) && $_GET['stock_type'] != '') {
            $filters['stock_type'] = $_GET['stock_type'];
        }

        if (isset($_GET['offset']))

            $offset = $_GET['offset'];

        if (isset($_GET['limit']))

            $limit = $_GET['limit'];

        if (isset($_GET['order']))

            $order = $_GET['order'];

        $products = fetch_product("", (isset($filters)) ? $filters : null, "", isset($category_id) ? $category_id : null, $limit, $offset, $sort, $order, "", "", isset($seller_id) ? $seller_id : null);

        // echo $this->db->last_query();
        $total = $products['total'];

        $bulkData = $rows = $tempRow = array();

        $bulkData['total'] = $total;




        foreach ($products['product'] as $product) {

            $category_id = $product['category_id'];

            $category_name = fetch_details('categories', ['id' => $category_id], 'name');

            $operate = $stock = "";

            $variants = get_variants_values_by_pid($product['id']);

            $stock = implode("<br/>", array_column($variants, 'stock'));

            // Determine stock type
            $stock_type = $product['stock_type'];

            // Stock type badge with icon and tooltip
            $stock_type_badge = '';
            $stock_type_text = '';
            if ($stock_type === null || $stock_type === '' || $stock_type === ' ') {
                $stock_type_badge = '<span class="badge bg-secondary-lt" data-bs-toggle="tooltip" title="This product does not track inventory">
                    <i class="ti ti-ban me-1"></i>Not Managed
                </span>';
                $stock_type_text = 'not_managed';
            } elseif ($stock_type == '0') {
                $stock_type_badge = '<span class="badge bg-success-lt" data-bs-toggle="tooltip" title="Stock tracked at product level with no variants">
                    <i class="ti ti-box me-1"></i>Simple Product
                </span>';
                $stock_type_text = 'simple';
            } elseif ($stock_type == '1') {
                $stock_type_badge = '<span class="badge bg-warning-lt" data-bs-toggle="tooltip" title="Stock tracked at product level, shared across all variants">
                    <i class="ti ti-package me-1"></i>Product Level
                </span>';
                $stock_type_text = 'product_level';
            } elseif ($stock_type == '2') {
                $stock_type_badge = '<span class="badge bg-info-lt" data-bs-toggle="tooltip" title="Stock tracked individually for each variant">
                    <i class="ti ti-boxes me-1"></i>Variant Level
                </span>';
                $stock_type_text = 'variant_level';
            }

            $tempRow['id'] = $product['variants'][0]['id'];
            $tempRow['product_id'] = $product['id'];

            $tempRow['name'] = $product['name'];

            $tempRow['seller_name'] = $product['seller_name'];

            $tempRow['category_name'] = $category_name[0]['name'];

            $tempRow['stock_type'] = $stock_type_badge;
            $tempRow['stock_type_value'] = $stock_type_text;

            $tempRow['image'] = '<div class="mx-auto product-image image-box-table"><a href=' . $product['image'] . ' data-lightbox="product"><img src=' . $product['image'] . ' class="rounded"></a></div>';

            // Build operate column based on stock type
            if ($stock_type === null || $stock_type === '' || $stock_type === ' ') {
                // Not managed - show message
                $tempRow['operate'] = '<div class="text-muted text-center py-3">
                    <i class="ti ti-info-circle me-1"></i>Stock not tracked
                </div>';
            } elseif ($stock_type == '0') {
                // Simple product
                $edit = '<a href="javascript:void(0)" class="edit_stock_btn btn btn-sm btn-success bg-success-lt" title="Edit Stock" data-id="' . $product['variants'][0]['id'] . '" data-url="admin/manage_stock/" data-bs-toggle="offcanvas" data-bs-target="#editVariantOffcanvas">
                    <i class="ti ti-pencil"></i>
                </a>';
                $tempRow['operate'] = '<table class="mb-0 table table-borderless table-sm">
                    <thead class="table-light">
                      
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center align-middle"><b class="fs-4">' . $product['stock'] . '</b></td>
                            <td class="text-center align-middle">' . $edit . '</td>
                        </tr>
                    </tbody>
                </table>';
            } else {
                // Variable products (type 1 or 2)
                $operate = '<table class="mb-0 table table-borderless table-sm">';
                $operate .= '<thead class="table-light">
                    
                </thead>
                <tbody>';

                for ($i = 0; $i < count($variants); $i++) {

                    $edit = '<a href="javascript:void(0)" class="edit_stock_btn btn btn-sm btn-success bg-success-lt" title="Edit Stock" data-id="' . $variants[$i]['id'] . '" data-url="admin/manage_stock/" data-bs-toggle="offcanvas" data-bs-target="#editVariantOffcanvas">
                        <i class="ti ti-pencil"></i>
                    </a>';

                    $operate .= "<tr>";
                    $operate .= "<td class='align-middle fw-bold'>" . str_replace(",", ", ", $variants[$i]['variant_values']) . '</td>';

                    if ($product['stock_type'] != 1) {
                        // Type 2 - Each variant has its own stock
                        $stockValue = $variants[$i]['stock'];
                        $stockClass = '';
                        if ($stockValue <= 0) {
                            $stockClass = 'text-danger';
                        } elseif ($stockValue < 10) {
                            $stockClass = 'text-warning';
                        } else {
                            $stockClass = 'text-success';
                        }
                        $operate .= '<td class="text-center align-middle fw-bold"><b class="' . $stockClass . ' fs-5">' . $stockValue . '</b></td>';
                        $operate .= '<td class="text-center align-middle fw-bold">' . $edit . '</td>';
                    } else {
                        // Type 1 - Product level stock (shared)
                        if ($i == 0) {
                            $stockValue = $variants[$i]['stock'];
                            $stockClass = '';
                            if ($stockValue <= 0) {
                                $stockClass = 'text-danger';
                            } elseif ($stockValue < 10) {
                                $stockClass = 'text-warning';
                            } else {
                                $stockClass = 'text-success';
                            }
                            $operate .= '<td class="text-center align-middle fw-bold" rowspan="' . count($variants) . '">
                                <b class="' . $stockClass . ' fs-5">' . $stockValue . '</b>
                                <div class="text-muted small mt-1">(Shared)</div>
                            </td>';
                            $operate .= '<td class="text-center align-middle fw-bold" rowspan="' . count($variants) . '">' . $edit . '</td>';
                        }
                    }
                    $operate .= "</tr>";
                }

                $operate .= "</tbody></table>";
                $tempRow['operate'] = $operate;
            }

            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;



        print_r(json_encode($bulkData));
    }

    public function get_seller_stock_details()
    {
        $seller_id = $this->ion_auth->get_user_id();
        $filters['show_only_stock_product'] = true;
        $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $sort = 'id';
        $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
        $filters['search'] = isset($_GET['search']) ? $_GET['search'] : null;
        $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';

        if (isset($_GET['stock_type']) && $_GET['stock_type'] != '') {
            $filters['stock_type'] = $_GET['stock_type'];
        }
        if (isset($_GET['category_id']) && $_GET['category_id'] != '') {
            $category_id = $_GET['category_id'];
        }

        $products = fetch_product("", $filters, "", $category_id, $limit, $offset, $sort, $order, "", "", $seller_id);

        $total = $products['total'];
        $bulkData = ['total' => $total];
        $rows = [];

        foreach ($products['product'] as $product) {
            $category_name = fetch_details('categories', ['id' => $product['category_id']], 'name');
            $variants = get_variants_values_by_pid($product['id']);
            $operate = $stock = "";

            // Determine stock type
            $stock_type = $product['stock_type'];

            // Stock type badge with icon and tooltip
            $stock_type_badge = '';
            $stock_type_text = '';
            if ($stock_type === null || $stock_type === '' || $stock_type === ' ') {
                $stock_type_badge = '<span class="badge bg-secondary-lt" data-bs-toggle="tooltip" title="This product does not track inventory">
                    <i class="ti ti-ban me-1"></i>Not Managed
                </span>';
                $stock_type_text = 'not_managed';
            } elseif ($stock_type == '0') {
                $stock_type_badge = '<span class="badge bg-success-lt" data-bs-toggle="tooltip" title="Stock tracked at product level with no variants">
                    <i class="ti ti-box me-1"></i>Simple Product
                </span>';
                $stock_type_text = 'simple';
            } elseif ($stock_type == '1') {
                $stock_type_badge = '<span class="badge bg-warning-lt" data-bs-toggle="tooltip" title="Stock tracked at product level, shared across all variants">
                    <i class="ti ti-package me-1"></i>Product Level
                </span>';
                $stock_type_text = 'product_level';
            } elseif ($stock_type == '2') {
                $stock_type_badge = '<span class="badge bg-info-lt" data-bs-toggle="tooltip" title="Stock tracked individually for each variant">
                    <i class="ti ti-boxes me-1"></i>Variant Level
                </span>';
                $stock_type_text = 'variant_level';
            }

            $tempRow = [];
            $tempRow['id'] = $product['variants'][0]['id'];
            $tempRow['name'] = $product['name'];
            $tempRow['seller_name'] = $product['seller_name'];
            $tempRow['category_name'] = $category_name[0]['name'];
            $tempRow['stock_type'] = $stock_type_badge;
            $tempRow['stock_type_value'] = $stock_type_text;

            $tempRow['image'] = '<div class="mx-auto product-image image-box-table"><a href=' . $product['image'] . ' data-lightbox="product"><img src=' . $product['image'] . ' class="rounded"></a></div>';

            // Build operate column based on stock type
            if ($stock_type === null || $stock_type === '' || $stock_type === ' ') {
                // Not managed - show message
                $tempRow['operate'] = '<div class="text-muted text-center py-3">
                    <i class="ti ti-info-circle me-1"></i>Stock not tracked
                </div>';
            } elseif ($stock_type == '0') {
                // Simple product
                $edit = '<a href="javascript:void(0)" class="edit_stock_btn btn btn-sm btn-success bg-success-lt" title="Edit Stock" data-id="' . $product['variants'][0]['id'] . '" data-url="seller/manage_stock/" data-bs-toggle="offcanvas" data-bs-target="#manage_stock">
                    <i class="ti ti-pencil"></i>
                </a>';
                $tempRow['operate'] = '<table class="mb-0 table table-borderless table-sm">
                    <thead class="table-light">
                      
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center align-middle"><b class="fs-4">' . $product['stock'] . '</b></td>
                            <td class="text-center align-middle">' . $edit . '</td>
                        </tr>
                    </tbody>
                </table>';
            } else {
                // Variable products (type 1 or 2)
                $operate = '<table class="mb-0 table table-borderless table-sm">';
                $operate .= '<thead class="table-light">
                    
                </thead>
                <tbody>';

                for ($i = 0; $i < count($variants); $i++) {

                    $edit = '<a href="javascript:void(0)" class="edit_stock_btn btn btn-sm btn-success bg-success-lt" title="Edit Stock" data-id="' . $variants[$i]['id'] . '" data-url="seller/manage_stock/" data-bs-toggle="offcanvas" data-bs-target="#manage_stock">
                        <i class="ti ti-pencil"></i>
                    </a>';

                    $operate .= "<tr>";
                    $operate .= "<td class='align-middle fw-bold'>" . str_replace(",", ", ", $variants[$i]['variant_values']) . '</td>';

                    if ($product['stock_type'] != 1) {
                        // Type 2 - Each variant has its own stock
                        $stockValue = $variants[$i]['stock'];
                        $stockClass = '';
                        if ($stockValue <= 0) {
                            $stockClass = 'text-danger';
                        } elseif ($stockValue <= 5) {
                            $stockClass = 'text-warning';
                        } else {
                            $stockClass = 'text-success';
                        }
                        $operate .= "<td class='text-center align-middle {$stockClass}'><b class='fs-4'>" . $stockValue . '</b></td>';
                        $operate .= '<td class="text-center align-middle">' . $edit . '</td>';
                    } else {
                        // Type 1 - Shared stock across all variants
                        if ($i == 0) {
                            $stockValue = $variants[$i]['stock'];
                            $stockClass = '';
                            if ($stockValue <= 0) {
                                $stockClass = 'text-danger';
                            } elseif ($stockValue <= 5) {
                                $stockClass = 'text-warning';
                            } else {
                                $stockClass = 'text-success';
                            }
                            $operate .= '<td class="text-center align-middle ' . $stockClass . '" rowspan="' . count($variants) . '"><b class="fs-4">' . $stockValue . '</b></td>';
                            $operate .= '<td class="text-center align-middle" rowspan="' . count($variants) . '">' . $edit . '</td>';
                        }
                    }

                    $operate .= "</tr>";
                }

                $operate .= "</tbody></table>";
                $tempRow['operate'] = $operate;
            }

            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        echo json_encode($bulkData);
    }


    public function getProductsAndVariants($seller_id = null)
    {
        // If seller_id is provided, apply a filter for it, otherwise, fetch all products
        if ($seller_id !== null) {
            $this->db->where('seller_id', $seller_id);  // Filter products by seller_id
        }

        // Fetch products (filtered by seller_id if provided)
        $products = $this->db->get('products')->result_array();

        // Loop through each product to fetch its variants
        foreach ($products as &$product) {
            $product_id = $product['id'];

            // Fetch variants for the current product
            $variants = $this->db->get_where('product_variants', array('product_id' => $product_id))->result_array();
            $product['variants'] = $variants;
        }

        return $products;
    }


    public function get_sellers($search = "")
    {
        // Fetch users
        $sellers = $this->db->select(' u.username as seller_name,u.id as seller_id,sd.category_ids,sd.id as seller_data_id ,sd.status as seller_status')
            ->join('users_groups ug', ' ug.user_id = u.id ')
            ->join('seller_data sd', ' sd.user_id = u.id ')
            ->where(['ug.group_id' => '4'])
            ->where(['sd.status' => 1])
            ->where("u.username like '%" . $search . "%'")
            ->get('users u')->result_array();
        // Initialize Array with fetched data
        $data = array();

        foreach ($sellers as $seller) {
            $data[] = array("id" => $seller['seller_id'], "name" => $seller['seller_name'], "text" => $seller['seller_name']);
        }
        return $data;
    }

    public function get_all_sellers($search = "")
    {
        // Fetch users
        $sellers = $this->db->select(' u.username as seller_name,u.id as seller_id,sd.category_ids,sd.id as seller_data_id ,sd.status as seller_status')
            ->join('users_groups ug', ' ug.user_id = u.id ')
            ->join('seller_data sd', ' sd.user_id = u.id ')
            ->where(['ug.group_id' => '4'])
            ->where(['sd.status' => 1])
            ->where("u.username like '%" . $search . "%'")
            ->get('users u')->result_array();
        // Initialize Array with fetched data
        $data = array();

        foreach ($sellers as $seller) {
            $data[] = array("id" => $seller['seller_id'], "text" => $seller['seller_name']);
        }
        return $data;
    }
}
