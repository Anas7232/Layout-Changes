<?php
error_reporting(0);
defined('BASEPATH') or exit('No direct script access allowed');

class Order_model extends CI_Model
{

    public function update_order($set, $where, $isjson = false, $table = 'orders')
    {
        $set = escape_array($set);

        if ($isjson == true) {
            $field = array_keys($set); // active_status
            $current_status = $set[$field[0]]; //processed
            $res = fetch_details($where, $table, '*');
            $priority_status = [
                'received' => 0,
                'processed' => 1,
                'shipped' => 2,
                'delivered' => 3,
                'cancelled' => 4,
                'returned' => 5,
            ];
            if (count($res) >= 1) {
                $i = 0;
                foreach ($res  as $row) {
                    $set = array();
                    $temp = array();
                    $active_status = array();
                    $active_status[$i] = json_decode($row['status'], 1);
                    $current_selected_status = end($active_status[$i]);
                    $temp = $active_status[$i];
                    $cnt = count($temp);
                    $currTime = date('Y-m-d H:i:s');
                    $min_value = (!empty($temp)) ? $priority_status[$current_selected_status[0]] : -1;
                    $max_value = $priority_status[$current_status];
                    if ($current_status == 'returned'  || $current_status == 'cancelled') {
                        $temp[$cnt] = [$current_status, $currTime];
                    } else {
                        foreach ($priority_status  as $key => $value) {
                            if ($value > $min_value && $value <= $max_value) {
                                $temp[$cnt] = [$key, $currTime];
                            }
                            ++$cnt;
                        }
                    }
                    $set = [$field[0] => json_encode(array_values($temp))];
                    $this->db->trans_start();
                    $this->db->set($set)->where(['id' => $row['id']])->update($table);
                    $this->db->trans_complete();
                    $response = FALSE;
                    if ($this->db->trans_status() === TRUE) {
                        $response = TRUE;
                    }
                    /* give commission to the delivery boy if the order is delivered */
                    if ($current_status == 'delivered' && $table == 'orders') {
                        $order = fetch_details($where, 'orders', 'delivery_boy_id,final_total,payment_method,total_payable');
                        if (!empty($order)) {
                            $delivery_boy_id = $order[0]['delivery_boy_id'];
                            if ($delivery_boy_id > 0) {
                                $delivery_boy = fetch_details("id=$delivery_boy_id", 'users', 'bonus');
                                $final_total = $order[0]['final_total'];
                                $total_payable = $order[0]['total_payable'];
                                $commission = 0;
                                $settings = get_settings('system_settings', true);
                                $commission = (isset($delivery_boy[0]['bonus']) && $delivery_boy[0]['bonus'] > 0) ? $delivery_boy[0]['bonus'] : $settings['delivery_boy_bonus_percentage'];
                                $commission = $final_total * ($commission / 100);
                                if ($commission > $final_total) {
                                    $commission = $final_total;
                                }
                                /* commission must be greater then zero to be credited into the account */
                                if ($commission > 0) {
                                    $this->load->model("transaction_model");
                                    $transaction_data = [
                                        'transaction_type' => "wallet",
                                        'user_id' => $delivery_boy_id,
                                        'order_id' => $row['id'],
                                        'type' => "credit",
                                        'txn_id' => "",
                                        'amount' => $commission,
                                        'status' => "success",
                                        'message' => "Order delivery bonus for order ID: #" . $row['id'],
                                    ];
                                    $this->transaction_model->add_transaction($transaction_data);
                                    $this->load->model('customer_model');
                                    $this->customer_model->update_balance($commission, $delivery_boy_id, 'add');

                                    if (strtolower($order[0]['payment_method']) == "cod") {
                                        $transaction_data = [
                                            'transaction_type' => "transaction",
                                            'user_id' => $delivery_boy_id,
                                            'order_id' => $row['id'],
                                            'type' => "delivery_boy_cash",
                                            'txn_id' => "",
                                            'amount' => $total_payable,
                                            'status' => "1",
                                            'message' => "Delivery boy collected COD",
                                        ];
                                        $this->transaction_model->add_transaction($transaction_data);
                                        $this->load->model('customer_model');
                                        update_cash_received($total_payable, $delivery_boy_id, "add");
                                    }
                                }
                            }
                        }
                    }
                    ++$i;
                }
                return $response;
            }
        } else {
            $this->db->trans_start();
            $this->db->set($set)->where($where)->update($table);
            $this->db->trans_complete();
            $response = FALSE;
            if ($this->db->trans_status() === TRUE) {
                $response = TRUE;
            }
            return $response;
        }
    }

    public function update_order_item($id, $status, $return_request = 0)
    {
        if ($return_request == 0) {
            $res = validate_order_status($id, $status);
            if ($res['error']) {
                $response['error'] = (isset($res['return_request_flag'])) ? false : true;
                $response['message'] = $res['message'];
                $response['data'] = $res['data'];
                return $response;
            }
        }


        $order_item_details = fetch_details(['id' => $id], 'order_items', 'order_id');
        $order_details =  fetch_orders($order_item_details[0]['order_id']);
        if (!empty($order_details) && !empty($order_item_details)) {

            $order_details = $order_details['order_data'];
            $order_items_details = $order_details[0]['order_items'];
            $key = array_search($id, array_column($order_items_details, 'id'));
            $order_id = $order_details[0]['id'];
            $user_id = $order_details[0]['user_id'];
            $order_counter = $order_items_details[$key]['order_counter'];
            $order_cancel_counter = $order_items_details[$key]['order_cancel_counter'];
            $order_return_counter = $order_items_details[$key]['order_return_counter'];
            $user_res = fetch_details(['id' => $user_id], 'users', 'fcm_id');
            $fcm_ids = array();
            if (!empty($user_res[0]['fcm_id'])) {
                $fcm_ids[0][] = $user_res[0]['fcm_id'];
            }


            if ($this->update_order(['status' => $status], ['id' => $id], true, 'order_items')) {

                $this->order_model->update_order(['active_status' => $status], ['id' => $id], false, 'order_items');
                if (($order_counter == intval($order_cancel_counter) + 1 && $status == 'cancelled') ||  ($order_counter == intval($order_return_counter) + 1 && $status == 'returned')) {
                    if ($this->update_order(['status' => $status], ['id' => $order_id], true)) {
                        $this->update_order(['active_status' => $status], ['id' => $order_id]);
                    }
                }
            }

            $response['error'] = false;
            $response['message'] = 'Status Updated Successfully';
            $response['data'] = array();
            return $response;
        }
    }

    public function place_order($data)
    {
        $data = escape_array($data);

        $CI = &get_instance();
        $CI->load->model('Address_model');

        $response = array();
        $user = fetch_details(['id' => $data['user_id']], 'users');
        $product_variant_id = explode(',', $data['product_variant_id']);
        $quantity = explode(',', $data['quantity']);
        $otp = mt_rand(100000, 999999);

        $check_current_stock_status = validate_stock($product_variant_id, $quantity);

        if (isset($check_current_stock_status['error']) && $check_current_stock_status['error'] == true) {
            return json_encode($check_current_stock_status);
        }

        /* Calculating Final Total */

        $total = 0;
        $product_variant = $this->db->select('pv.*,tax.percentage as tax_percentage,tax.title as tax_name,p.name as product_name,p.is_prices_inclusive_tax')
            ->join('products p ', 'pv.product_id=p.id', 'left')
            ->join('categories c', 'p.category_id = c.id', 'left')
            ->join('`taxes` tax', 'tax.id = p.tax', 'LEFT')
            ->where_in('pv.id', $product_variant_id)->order_by('FIELD(pv.id,' . $data['product_variant_id'] . ')')->get('product_variants pv')->result_array();

        if (!empty($product_variant)) {

            $system_settings = get_settings('system_settings', true);
            $delivery_charge = isset($data['delivery_charge']) && !empty($data['delivery_charge']) ? $data['delivery_charge'] : 0;
            $gross_total = 0;
            $cart_data = [];
            for ($i = 0; $i < count($product_variant); $i++) {
                $pv_price[$i] = ($product_variant[$i]['special_price'] > 0 && $product_variant[$i]['special_price'] != null) ? $product_variant[$i]['special_price'] : $product_variant[$i]['price'];
                $tax_percentage[$i] = (isset($product_variant[$i]['tax_percentage']) && intval($product_variant[$i]['tax_percentage']) > 0 && $product_variant[$i]['tax_percentage'] != null) ? $product_variant[$i]['tax_percentage'] : '0';
                if ((isset($product_variant[$i]['is_prices_inclusive_tax']) && $product_variant[$i]['is_prices_inclusive_tax'] == 0) || (!isset($product_variant[$i]['is_prices_inclusive_tax'])) && $tax_percentage[$i] > 0) {
                    $tax_amount[$i] = $pv_price[$i] * ($tax_percentage[$i] / 100);
                    $pv_price[$i] = $pv_price[$i] + $tax_amount[$i];
                }

                $subtotal[$i] = ($pv_price[$i])  * $quantity[$i];
                $pro_name[$i] = $product_variant[$i]['product_name'];
                $variant_info = get_variants_values_by_id($product_variant[$i]['id']);
                $product_variant[$i]['variant_name'] = (isset($variant_info[0]['variant_values']) && !empty($variant_info[0]['variant_values'])) ? $variant_info[0]['variant_values'] : "";

                $tax_percentage[$i] = (!empty($product_variant[$i]['tax_percentage'])) ? $product_variant[$i]['tax_percentage'] : 0;
                if ($tax_percentage[$i] != NUll && $tax_percentage[$i] > 0) {
                    $tax_amount[$i] = round($subtotal[$i] *  $tax_percentage[$i] / 100, 2);
                } else {
                    $tax_amount[$i] = 0;
                    $tax_percentage[$i] = 0;
                }
                $gross_total += $subtotal[$i];
                $total += $subtotal[$i];
                $total = round($total, 2);
                $gross_total  = round($gross_total, 2);

                array_push($cart_data, array(
                    'name' => $pro_name[$i],
                    'tax_amount' => $tax_amount[$i],
                    'qty' => $quantity[$i],
                    'sub_total' => $subtotal[$i],
                ));
            }
            $system_settings = get_settings('system_settings', true);

            /* Calculating Promo Discount */
            if (isset($data['promo_code']) && !empty($data['promo_code'])) {

                $promo_code = validate_promo_code($data['promo_code'], $data['user_id'], $data['final_total']);

                if ($promo_code['error'] == false) {

                    if ($promo_code['data'][0]['discount_type'] == 'percentage') {
                        $promo_code_discount =  floatval($total  * $promo_code['data'][0]['discount'] / 100);
                    } else {
                        $promo_code_discount = $promo_code['data'][0]['discount'];
                        // $promo_code_discount = floatval($total - $promo_code['data'][0]['discount']);
                    }
                    if ($promo_code_discount <= $promo_code['data'][0]['max_discount_amount']) {
                        $total = floatval($total) - $promo_code_discount;
                    } else {
                        $total = floatval($total) - $promo_code['data'][0]['max_discount_amount'];
                        $promo_code_discount = $promo_code['data'][0]['max_discount_amount'];
                    }
                } else {
                    return $promo_code;
                }
            }

            $final_total = $total + $delivery_charge;
            $final_total = round($final_total, 2);

            /* Calculating Wallet Balance */
            $total_payable = $final_total;
            if ($data['is_wallet_used'] == '1' && $data['wallet_balance_used'] <= $final_total) {

                /* function update_wallet_balance($operation,$user_id,$amount,$message="Balance Debited") */
                $wallet_balance = update_wallet_balance('debit', $data['user_id'], $data['wallet_balance_used'], "Used against Order Placement");
                if ($wallet_balance['error'] == false) {
                    $total_payable -= $data['wallet_balance_used'];
                    $Wallet_used = true;
                } else {
                    $response['error'] = true;
                    $response['message'] = $wallet_balance['message'];
                    return $response;
                }
            } else {
                if ($data['is_wallet_used'] == 1) {
                    $response['error'] = true;
                    $response['message'] = 'Wallet Balance should not exceed the total amount';
                    return $response;
                }
            }

            $status = (isset($data['active_status'])) ? $data['active_status'] : 'received';
            $order_data = [
                'user_id' => $data['user_id'],
                'mobile' => $data['mobile'],
                'total' => $gross_total,
                'promo_discount' => (isset($promo_code_discount) && $promo_code_discount != NULL) ? $promo_code_discount : '0',
                'total_payable' => $total_payable,
                'delivery_charge' => $delivery_charge,
                'is_delivery_charge_returnable' => $data['is_delivery_charge_returnable'],
                'wallet_balance' => (isset($Wallet_used) && $Wallet_used == true) ? $data['wallet_balance_used'] : '0',
                'final_total' => $final_total,
                'discount' => '0',
                'payment_method' => $data['payment_method'],
                'status' =>  json_encode(array(array($status, date("d-m-Y h:i:sa")))),
                'active_status' => $status,
                'promo_code' => (isset($data['promo_code'])) ? $data['promo_code'] : ' '
            ];
            if (isset($data['address_id']) && !empty($data['address_id'])) {
                $order_data['address_id'] = $data['address_id'];
            }

            if (isset($data['delivery_date']) && !empty($data['delivery_date']) && !empty($data['delivery_time']) && isset($data['delivery_time'])) {
                $order_data['delivery_date'] = date('Y-m-d', strtotime($data['delivery_date']));
                $order_data['delivery_time'] = $data['delivery_time'];
            }
            if (isset($data['address_id']) && !empty($data['address_id'])) {
                $address_data = $CI->address_model->get_address('', $data['address_id'], true);
                if (!empty($address_data)) {
                    $order_data['latitude'] = $address_data[0]['latitude'];
                    $order_data['longitude'] = $address_data[0]['longitude'];
                    $order_data['address'] = (!empty($address_data[0]['address'])) ? $address_data[0]['address'] . ', ' : '';
                    $order_data['address'] .= (!empty($address_data[0]['landmark'])) ? $address_data[0]['landmark'] . ', ' : '';
                    $order_data['address'] .= (!empty($address_data[0]['area'])) ? $address_data[0]['area'] . ', ' : '';
                    $order_data['address'] .= (!empty($address_data[0]['city'])) ? $address_data[0]['city'] . ', ' : '';
                    $order_data['address'] .= (!empty($address_data[0]['state'])) ? $address_data[0]['state'] . ', ' : '';
                    $order_data['address'] .= (!empty($address_data[0]['country'])) ? $address_data[0]['country'] . ', ' : '';
                    $order_data['address'] .= (!empty($address_data[0]['pincode'])) ? $address_data[0]['pincode'] : '';
                    $order_data['mobile'] = (!empty($address_data[0]['mobile'])) ? $address_data[0]['mobile'] : $data['mobile'];
                }
            } else {
                $order_data['address'] = "";
            }


            if (!empty($_POST['latitude']) && !empty($_POST['longitude'])) {
                $order_data['latitude'] = $_POST['latitude'];
                $order_data['longitude'] = $_POST['longitude'];
            }
            if ($system_settings['is_delivery_boy_otp_setting_on'] == '1') {
                $order_data['otp'] = $otp;
            } else {
                $order_data['otp'] = 0;
            }
            $order_data['notes'] = $data['order_note'];


            $this->db->insert('orders', $order_data);
            $last_order_id = $this->db->insert_id();

            for ($i = 0; $i < count($product_variant); $i++) {
                $product_variant_data[$i] = [
                    'user_id' => $data['user_id'],
                    'order_id' => $last_order_id,
                    'product_name' => $product_variant[$i]['product_name'],
                    'variant_name' => $product_variant[$i]['variant_name'],
                    'product_variant_id' => $product_variant[$i]['id'],
                    'quantity' => $quantity[$i],
                    'price' => $pv_price[$i],
                    'tax_percent' => $tax_percentage[$i],
                    'tax_amount' => 0,
                    'sub_total' => $subtotal[$i],
                    'status' =>  json_encode(array(array($status, date("d-m-Y h:i:sa")))),
                    'active_status' => $status,
                ];

                $this->db->insert('order_items', $product_variant_data[$i]);
            }
            $product_variant_ids = explode(',', $data['product_variant_id']);

            $qtns = explode(',', $data['quantity']);
            update_stock($product_variant_ids, $qtns);

            $overall_total = array(
                'total_amount' => array_sum($subtotal),
                'delivery_charge' => $delivery_charge,
                'tax_amount' => array_sum($tax_amount),
                'tax_percentage' => array_sum($tax_percentage),
                'discount' =>  $order_data['promo_discount'],
                'wallet' =>  $order_data['wallet_balance'],
                'final_total' =>  $order_data['final_total'],
                'total_payable' =>  $order_data['total_payable'],
                'otp' => $otp,
                'address' => (isset($order_data['address'])) ? $order_data['address'] : '',
                'payment_method' => $data['payment_method']
            );
            if (trim(strtolower($data['payment_method'])) != 'paypal' || trim(strtolower($data['payment_method'])) != 'stripe') {
                $overall_order_data = array(
                    'cart_data' => $cart_data,
                    'order_data' => $overall_total,
                    'subject' => 'Order received successfully',
                    'user_data' => $user[0],
                    'system_settings' => $system_settings,
                    'user_msg' => 'Hello, Dear ' . ucfirst($user[0]['username']) . ', We have received your order successfully. Your order summaries are as followed',
                    'otp_msg' => 'Here is your OTP. Please, give it to delivery boy only while getting your order.',
                );
                if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 1) {
                    $system_settings = get_settings('system_settings', true);
                    if (isset($system_settings['support_email']) && !empty($system_settings['support_email'])) {
                        send_mail($system_settings['support_email'], 'New order placed ID #' . $last_order_id, 'New order received for ' . $system_settings['app_name'] . ' please process it.');
                        $user_roles = fetch_details("", "user_permissions", '*', '',  '', '', '');
                        foreach ($user_roles as $user) {
                            $user_res = fetch_details(['id' => $user['user_id']], 'users', 'fcm_id');
                            $fcm_ids[0][] = $user_res[0]['fcm_id'];
                        }
                        $fcm_admin_msg = 'New order received for ' . $system_settings['app_name'] . ' please process it.';
                        // $fcm_admin_msg = $result['data'];
                        $fcm_admin_subject = 'New order placed ID #' . $last_order_id;
                        if (!empty($fcm_ids)) {
                            $fcmMsg = array(
                                'title' => $fcm_admin_subject,
                                'body' => $fcm_admin_msg,
                                'type' => "place_order",
                                'content_available' => true
                            );
                            send_notification($fcmMsg, $fcm_ids);
                        }
                    }
                }

                send_mail($user[0]['email'], 'Order received successfully', $this->load->view('admin/pages/view/email-template.php', $overall_order_data, TRUE));
            }

            $this->cart_model->remove_from_cart($data);


            $user_balance = fetch_details(['id' => $data['user_id']], 'users', 'balance');

            $response['error'] = false;
            $response['message'] = 'Order Placed Successfully';
            $response['order_id'] = $last_order_id;
            $response['order_item_data'] = $product_variant_data;
            $response['balance'] = $user_balance;
            return $response;
        } else {
            $user_balance = fetch_details(['id' => $data['user_id']], 'users', 'balance');

            $response['error'] = true;
            $response['message'] = "Product(s) Not Found!";
            $response['balance'] = $user_balance;
            return $response;
        }
    }

    public function get_order_details($where = NULL, $status = false)
    {
        $res = $this->db->select('oi.*,a.name as user_name,oi.id as order_item_id,p.*,v.product_id,o.*,o.id as order_id,o.total as order_total,o.wallet_balance,oi.active_status as oi_active_status,u.email,u.username as uname,o.status as order_status,p.name as pname,p.type,p.image as product_image,p.is_prices_inclusive_tax,(SELECT username FROM users db where db.id=o.delivery_boy_id ) as delivery_boy,(SELECT status FROM orders o where o.id=oi.order_id  ) as order_status ')
            ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
            ->join('products p ', ' p.id = v.product_id ', 'left')
            ->join('users u ', ' u.id = oi.user_id', 'left')
            ->join('orders o ', 'o.id=oi.order_id', 'left')
            ->join('addresses a', 'a.id=o.address_id', 'left');
        if (isset($where) && $where != NULL) {
            $res->where($where);
            if ($status == true) {
                $res->group_Start()
                    ->where_not_in(' `oi`.active_status ', array('cancelled', 'returned'))
                    ->group_End();
            }
        }
        if (!isset($where) && $status == true) {
            $res->where_not_in(' `oi`.active_status ', array('cancelled', 'returned'));
        }
        $order_result = $res->get(' `order_items` oi')->result_array();
        if (!empty($order_result)) {
            for ($i = 0; $i < count($order_result); $i++) {
                $order_result[$i] = output_escaping($order_result[$i]);
            }
        }
        return $order_result;
    }

    public function get_orders_list(
        $delivery_boy_id = NULL,
        $offset = 0,
        $limit = 10,
        $sort = " o.id ",
        $order = 'ASC'
    ) {
        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];

            $filters = [
                'u.username' => $search,
                'db.username' => $search,
                'u.email' => $search,
                'o.id' => $search,
                'o.mobile' => $search,
                'o.address' => $search,
                'o.wallet_balance' => $search,
                'o.total' => $search,
                'o.final_total' => $search,
                'o.total_payable' => $search,
                'o.payment_method' => $search,
                'o.delivery_charge' => $search,
                'o.delivery_time' => $search,
                'o.status' => $search,
                'o.active_status' => $search,
                'date_added' => $search
            ];
        }

        $count_res = $this->db->select(' COUNT(o.id) as `total` ')
            ->join(' `users` u', 'u.id= o.user_id', 'left')
            ->join('order_tracking ot ', ' ot.order_id = o.id', 'left')
            ->join('users db ', ' db.id = o.delivery_boy_id', 'left');
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {

            $count_res->where(" DATE(date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $count_res->where(" DATE(date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_Start();
            $count_res->or_like($filters);
            $this->db->group_End();
        }

        if (isset($delivery_boy_id)) {
            $count_res->where("o.delivery_boy_id", $delivery_boy_id);
        }

        if (isset($_GET['user_id']) && $_GET['user_id'] != null) {
            $count_res->where("o.user_id", $_GET['user_id']);
        }

        if (isset($_GET['order_status']) && !empty($_GET['order_status'])) {
            $count_res->where('active_status', $_GET['order_status']);
        }

        $product_count = $count_res->get('`orders` o')->result_array();

        foreach ($product_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select(' o.*,ot.courier_agency,ot.tracking_id,ot.url , u.username, db.username as delivery_boy ')
            ->join(' `users` u', 'u.id= o.user_id', 'left')
            ->join(' `users` db ', 'db.id = o.delivery_boy_id', 'left')
            ->join(' `order_tracking` ot', 'ot.order_id = o.id', 'left');

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $search_res->where(" DATE(date_added) >= DATE('" . $_GET['start_date'] . "') ");
            $search_res->where(" DATE(date_added) <= DATE('" . $_GET['end_date'] . "') ");
        }

        if (isset($filters) && !empty($filters)) {
            $search_res->group_Start();
            $search_res->or_like($filters);
            $search_res->group_End();
        }

        if (isset($delivery_boy_id)) {
            $search_res->where("o.delivery_boy_id", $delivery_boy_id);
        }

        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $search_res->where("o.user_id", $_GET['user_id']);
        }

        if (isset($_GET['order_status']) && !empty($_GET['order_status'])) {
            $search_res->where('active_status', $_GET['order_status']);
        }

        $user_details = $search_res->order_by($sort, "DESC")->limit($limit, $offset)->get('`orders` o')->result_array();
        // print_r($this->db->last_query());
        $i = 0;
        foreach ($user_details as $row) {
            $user_details[$i]['items'] = $this->db->select('oi.*,p.name as name,p.id as product_id, u.username as uname, (SELECT status FROM orders o where o.id=oi.order_id  ) as order_status  ')
                ->join('product_variants v ', ' oi.product_variant_id = v.id', 'left')
                ->join('products p ', ' p.id = v.product_id ', 'left')
                ->join('users u ', ' u.id = oi.user_id', 'left')
                ->where('oi.order_id', $row['id'])
                ->get(' `order_items` oi  ')->result_array();
            ++$i;
        }

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $tota_amount = 0;
        $final_tota_amount = 0;
        $currency_symbol = get_settings('currency');
        foreach ($user_details as $row) {
            if (!empty($row['items'])) {
                $items = $row['items'];
                $items1 = '';
                $temp = '';
                $total_amt = $total_qty = 0;
                foreach ($items as $item) {
                    $product_variants = get_variants_values_by_id($item['product_variant_id']);
                    $variants = isset($product_variants[0]['variant_values']) && !empty($product_variants[0]['variant_values']) ? str_replace(',', ' | ', $product_variants[0]['variant_values']) : '-';
                    $temp .= "<b>ID :</b>" . $item['id'] . "<b> Product Variant Id :</b> " . $item['product_variant_id'] . "<b> Variants :</b> " . $variants . "<b> Name : </b>" . $item['name'] . " <b>Price : </b>" . $item['price'] . " <b>QTY : </b>" . $item['quantity'] . " <b>Subtotal : </b>" . $item['quantity'] * $item['price'] . "<br>------<br>";
                    $total_amt += $item['sub_total'];
                    $total_qty += $item['quantity'];
                }

                $items1 = $temp;
                $temp = '';
                if (!empty($row['items'][0]['order_status'])) {
                    $status = json_decode($row['items'][0]['order_status'], 1);
                    foreach ($status as $st) {
                        $temp .= @$st[0] . " : " . @$st[1] . "<br>------<br>";
                    }
                }

                if (trim($row['active_status']) == 'awaiting') {
                    $active_status = '<label class="badge badge-secondary">' . $row['active_status'] . '</label>';
                }
                if ($row['active_status'] == 'received') {
                    $active_status = '<label class="badge badge-primary">' . $row['active_status'] . '</label>';
                }
                if ($row['active_status'] == 'processed') {
                    $active_status = '<label class="badge badge-info">' . $row['active_status'] . '</label>';
                }
                if ($row['active_status'] == 'shipped') {
                    $active_status = '<label class="badge badge-warning">' . $row['active_status'] . '</label>';
                }
                if ($row['active_status'] == 'delivered') {
                    $active_status = '<label class="badge badge-success">' . $row['active_status'] . '</label>';
                }
                if ($row['active_status'] == 'returned' || $row['active_status'] == 'cancelled') {
                    $active_status = '<label class="badge badge-danger">' . $row['active_status'] . '</label>';
                }

                $status = $temp;
                $discounted_amount = $row['total'] * $row['items'][0]['discount'] / 100; /*  */
                $final_total = $row['total'] - $discounted_amount;
                $discount_in_rupees = $row['total'] - $final_total;
                $discount_in_rupees = floor($discount_in_rupees);
                $tempRow['id'] = $row['id'];
                $tempRow['user_id'] = $row['user_id'];
                $tempRow['name'] = $row['items'][0]['uname'];
                if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                    $tempRow['mobile'] = str_repeat("X", strlen($row['mobile']) - 3) . substr($row['mobile'], -3);
                } else {
                    $tempRow['mobile'] = $row['mobile'];
                }
                $tempRow['notes'] = $row['notes'];
                // $tempRow['mobile'] = $row['mobile'];
                $tempRow['delivery_charge'] = $currency_symbol . ' ' . $row['delivery_charge'];
                $tempRow['items'] = $items1;
                $tempRow['total'] = $currency_symbol . ' ' . $row['total'];
                $tota_amount += intval($row['total']);
                $tempRow['wallet_balance'] = $currency_symbol . ' ' . $row['wallet_balance'];
                $tempRow['discount'] = $currency_symbol . ' ' . $discount_in_rupees . '(' . $row['items'][0]['discount'] . '%)';
                $tempRow['promo_discount'] = $currency_symbol . ' ' . $row['promo_discount'];
                $tempRow['promo_code'] = $row['promo_code'];
                $tempRow['qty'] =  $total_qty;
                $tempRow['final_total'] = $currency_symbol . ' ' . $row['total_payable'];
                $final_total = $row['final_total'] - $row['wallet_balance'] - $row['discount'];
                $tempRow['final_total'] = $currency_symbol . ' ' .$final_total;
                $final_tota_amount += intval($row['final_total']);
                $tempRow['deliver_by'] = $row['delivery_boy'];
                $tempRow['payment_method'] = $row['payment_method'];
                $tempRow['address'] = output_escaping(str_replace('\r\n', '</br>', $row['address']));
                $tempRow['delivery_date'] = $row['delivery_date'];
                $tempRow['delivery_time'] = $row['delivery_time'];
                $tempRow['status'] = $status;
                $tempRow['active_status'] = $active_status;
                $tempRow['date_added'] = date('d-m-Y', strtotime($row['date_added']));
                $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['id'] . '" class="btn btn-primary btn-xs mr-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';
                if (!$this->ion_auth->is_delivery_boy()) {
                    $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['id'] . ' class="btn btn-primary btn-xs mr-1 mb-1" title="View" ><i class="fa fa-eye"></i></a>';
                    $operate .= '<a href="javascript:void(0)" class="delete-orders btn btn-danger btn-xs mr-1 mb-1" data-id=' . $row['id'] . ' title="Delete" ><i class="fa fa-trash"></i></a>';
                    $operate .= '<a href="' . base_url() . 'admin/invoice?edit_id=' . $row['id'] . '" class="btn btn-info btn-xs mr-1 mb-1" title="Invoice" ><i class="fa fa-file"></i></a>';
                    if (trim($row['active_status']) != 'awaiting') {
                        $operate .= ' <a href="javascript:void(0)" class="edit_order_tracking btn btn-success btn-xs mr-1 mb-1" title="Order Tracking" data-order_id="' . $row['id'] . '"  data-courier_agency="' . $row['courier_agency'] . '"  data-tracking_id="' . $row['tracking_id'] . '" data-url="' . $row['url'] . '" data-target="#transaction_modal" data-toggle="modal"><i class="fa fa-map-marker-alt"></i></a>';
                    }
                } else {
                    $operate = '<a href=' . base_url('delivery_boy/orders/edit_orders') . '?edit_id=' . $row['id'] . ' class="btn btn-primary btn-xs mr-1 mb-1" title="View"><i class="fa fa-eye"></i></a>';
                }
                $tempRow['operate'] = $operate;
                $rows[] = $tempRow;
            }
        }
        if (!empty($user_details)) {
            $tempRow['id'] = '-';
            $tempRow['user_id'] = '-';
            $tempRow['name'] = '-';
            $tempRow['mobile'] = '-';
            $tempRow['delivery_charge'] = '-';
            $tempRow['items'] = '-';
            $tempRow['total'] = '<span class="badge badge-danger">' . $currency_symbol . ' ' . $tota_amount . '</span>';
            $tempRow['wallet_balance'] = '-';
            $tempRow['discount'] = '-';
            $tempRow['qty'] = '-';
            $tempRow['final_total'] = '<span class="badge badge-danger">' . $currency_symbol . ' ' . $final_tota_amount . '</span>';
            $tempRow['deliver_by'] = '-';
            $tempRow['payment_method'] = '-';
            $tempRow['address'] = '-';
            $tempRow['delivery_time'] = '-';
            $tempRow['status'] = '-';
            $tempRow['active_status'] = '-';
            $tempRow['wallet_balance'] = '-';
            $tempRow['date_added'] = '-';
            $tempRow['operate'] = '-';
            array_push($rows, $tempRow);
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function add_bank_transfer_proof($data)
    {
        $data = escape_array($data);
        for ($i = 0; $i < count($data['attachments']); $i++) {
            $order_data = [
                'order_id' => $data['order_id'],
                'attachments' => $data['attachments'][$i],
            ];
            $this->db->insert('order_bank_transfer', $order_data);
        }
        return true;
    }

    public function get_order_tracking_list()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $multipleWhere = '';
        $where = [];
        
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
            $multipleWhere = ['`id`' => $search, '`order_id`' => $search, '`tracking_id`' => $search, 'courier_agency' => $search, 'url' => $search];
        }
        if (isset($_GET['order_id']) and $_GET['order_id'] != '') {
            $where = ['order_id' => $_GET['order_id']];
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

       
        $txn_count = $count_res->get('order_tracking')->result_array();

        foreach ($txn_count as $row) {
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
        
        $txn_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('order_tracking')->result_array();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($txn_search_res as $row) {
            $row = output_escaping($row);
            if($this->ion_auth->is_admin()){
                $operate = '<a href=' . base_url('admin/orders/edit_orders') . '?edit_id=' . $row['order_id'] . ' class="btn btn-primary btn-xs mr-1 mb-1" title="View Order" ><i class="fa fa-eye"></i></a>';
            }else{
                $operate = "";
            }
            
            $tempRow['id'] = $row['id'];
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['courier_agency'] = $row['courier_agency'];
            $tempRow['tracking_id'] = $row['tracking_id'];
            $tempRow['url'] = $row['url'];
            $tempRow['date'] = $row['date_created'];
            $tempRow['operate'] = $operate;

            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    public function get_order_tracking($limit = "", $offset = '', $sort = 'id', $order = 'DESC',$search = NULL)
    {

        $multipleWhere = '';

        if (isset($search) and $search != '') {
            $multipleWhere = ['ot.id' => $search, 'ot.order_id' => $search, 'tracking_id' => $search, 'courier_agency' => $search, 'url' => $search];
        }

        $count_res = $this->db->select(' COUNT(ot.id) as `total` ')->join("orders o","o.id=ot.order_id");

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }
        $attr_count = $count_res->get('order_tracking ot')->result_array();

        foreach ($attr_count as $row) {
            $total = $row['total'];
        }

        $search_res = $this->db->select('ot.*')->join("orders o","o.id=ot.order_id");
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->or_like($multipleWhere);
        }
        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        $city_search_res = $search_res->order_by($sort, $order)->limit($limit, $offset)->get('order_tracking ot')->result_array();
        $bulkData = array();
        $bulkData['error'] = (empty($city_search_res)) ? true : false;
        $bulkData['message'] = (empty($city_search_res)) ? 'Order Tracking details does not exist' : 'Order Tracking details are retrieve successfully';
        $bulkData['total'] = (empty($city_search_res)) ? 0 : $total;
        $rows = $tempRow = array();
       
        foreach ($city_search_res as $row) {
            $tempRow['id'] = $row['id'];
            $tempRow['order_id'] = $row['order_id'];
            $tempRow['courier_agency'] = $row['courier_agency'];
            $tempRow['tracking_id'] = $row['tracking_id'];
            $tempRow['url'] = $row['url'];
            $tempRow['date'] = $row['date_created'];
            $rows[] = $tempRow;
        }
        $bulkData['data'] = $rows;
        return $bulkData;
    }
}
