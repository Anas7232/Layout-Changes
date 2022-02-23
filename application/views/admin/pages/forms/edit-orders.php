<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>View Order</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="transaction_modal" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="user_name">Order Tracking</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-info">
                                            <!-- form start -->
                                            <form class="form-horizontal " id="order_tracking_form" action="<?= base_url('admin/orders/update-order-tracking/'); ?>" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="order_id" id="order_id">
                                                <input type="hidden" name="order_item_id" id="order_item_id">
                                                <div class="card-body pad">
                                                    <div class="form-group ">
                                                        <label for="courier_agency">Courier Agency</label>
                                                        <input type="text" class="form-control" name="courier_agency" id="courier_agency" placeholder="Courier Agency" />
                                                    </div>
                                                    <div class="form-group ">
                                                        <label for="tracking_id">Tracking Id</label>
                                                        <input type="text" class="form-control" name="tracking_id" id="tracking_id" placeholder="Tracking Id" />
                                                    </div>
                                                    <div class="form-group ">
                                                        <label for="url">URL</label>
                                                        <input type="text" class="form-control" name="url" id="url" placeholder="URL" />
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="reset" class="btn btn-warning">Reset</button>
                                                        <button type="submit" class="btn btn-success" id="submit_btn">Save</button>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-center">
                                                    <div class="form-group" id="error_box">
                                                    </div>
                                                </div>
                                                <!-- /.card-body -->
                                            </form>
                                        </div>
                                        <!--/.card-->
                                    </div>
                                    <!--/.col-md-12-->
                                </div>
                                <!-- /.row -->

                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <!-- The time line -->
                    <section class="time-line-box text-center">
                        <div class="swiper-wrapper col-12">
                            <?php
                            $status = json_decode($order_detls[0]['status']);
                            $status_wise_class = [
                                'awaiting' => ['fa fa-xs fa-history', 'bg-red'],
                                'received' => ['fa fa-xs fa-level-down-alt', 'bg-indigo'],
                                'processed' => ['fa fa-xs fa-people-carry ', 'bg-navy'],
                                'shipped' => ['fa fa-xs fa-shipping-fast ', 'bg-yellow'],
                                'delivered' => ['fa fa-xs fa-user-check ', 'bg-success'],
                                'cancelled' => ['fa fa-xs fa-times-circle ', 'bg-red'],
                                'returned' => ['fa fa-xs fa-level-up-alt ', 'bg-orange'],
                            ];
                            foreach ($status as $row) {
                            ?>
                                <div class="swiper-slide">
                                    <div class="max-auto col-md-6 offset-md-3">
                                        <div class="<?= $status_wise_class[$row[0]][1] ?> pt-2 pb-2 rounded"> <span class="fa-lg"><i class="<?= $status_wise_class[$row[0]][0] ?>"></i></span></div>
                                    </div>
                                    <div class="timestamp m-1"><small class="date"><i class="fas fa-clock"></i>&nbsp;<?= strtoupper($row[1]) ?> </small> </div>
                                    <div class="status text-bold"><span> <?= strtoupper($row[0]) ?> </span></div>
                                </div>
                            <?php } ?>

                        </div>
                    </section>
                </div>
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <input type="hidden" name="hidden" id="order_id" value="<?php echo $order_detls[0]['id']; ?>">
                                    <th class="w-10px">ID</th>
                                    <td><?php echo $order_detls[0]['id']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Name</th>
                                    <td><?php echo $order_detls[0]['uname']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Email</th>
                                    <td><?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($order_detls[0]['email']) - 3) . substr($order_detls[0]['email'], -3) : $order_detls[0]['email']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Contact</th>
                                    <td><?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($order_detls[0]['mobile']) - 3) . substr($order_detls[0]['mobile'], -3) : $order_detls[0]['mobile']; ?>
                                    </td>
                                </tr>
                                <?php if ($order_detls[0]['otp'] != 0) { ?>
                                    <tr>
                                        <th class="w-10px">OTP</th>
                                        <td><?php echo $order_detls[0]['otp']; ?></td>
                                    </tr>
                                <?php } ?>
                                <?php if (isset($order_tracking) && !empty($order_tracking) && isset($order_detls[0]['active_status']) && $order_detls[0]['active_status'] != 'awaiting') { ?>
                                    <tr>
                                        <th class="w-10px">Order Tracking</th>
                                        <td>
                                            <a href="javascript:void(0)" class="edit_order_tracking btn btn-success btn-xs mr-1 " title="Order Tracking" data-order_id=' <?= $order_detls[0]['id']; ?>' data-courier_agency=' <?= $order_tracking[0]['courier_agency'] ?> ' data-tracking_id=' <?= $order_tracking[0]['tracking_id'] ?> ' data-url=' <?= $order_tracking[0]['url'] ?> ' data-target="#transaction_modal" data-toggle="modal"><i class="fa fa-map-marker-alt"></i> Click Here to View</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <th class="w-10px">Items</th>
                                    <td><?php $total = 0;
                                        $tax_amount = 0;
                                        echo '<div class="container-fluid row">';
                                        foreach ($items as $item) {
                                            $item['discounted_price'] = ($item['discounted_price'] == '') ? 0 : $item['discounted_price'];
                                            $total += $subtotal = ($item['quantity'] != 0 && ($item['discounted_price'] != '' && $item['discounted_price'] > 0) && $item['price'] > $item['discounted_price']) ? ($item['price'] - $item['discounted_price']) : ($item['price'] * $item['quantity']);
                                            $tax_amount += $item['tax_amount'];
                                            $total += $subtotal = $tax_amount;
                                        ?>
                                            <div class="  card col-md-3 col-sm-12 p-3 mb-2 bg-white rounded m-1 grow">
                                                <div class="row mb-1">
                                                    <div class="col-md-7 text-center"><select class="form-control-sm w-100">
                                                            <option value="processed" <?= (strtolower($item['active_status']) == 'processed') ? 'selected' : '' ?>>Processed</option>
                                                            <option value="shipped" <?= (strtolower($item['active_status']) == 'shipped') ? 'selected' : '' ?>>Shipped</option>
                                                            <option value="delivered" <?= (strtolower($item['active_status']) == 'delivered') ? 'selected' : '' ?>>Delivered</option>
                                                            <option value="returned" <?= (strtolower($item['active_status']) == 'returned') ? 'selected' : '' ?>>Return</option>
                                                            <option value="cancelled" <?= (strtolower($item['active_status']) == 'cancelled') ? 'selected' : '' ?>>Cancel</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-5 d-flex align-items-center"><a href="javascript:void(0);" title="Update status" data-id='<?= $item['id'] ?>' class="btn btn-primary btn-xs update_status_admin mr-1"><i class="far fa-arrow-alt-circle-up"></i></a><a href=" <?= BASE_URL('admin/product/view-product?edit_id=' . $item['product_id'] . '') ?> " title="View Product" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i></a> </div>
                                                </div>
                                                <div class="order-product-image">
                                                    <a href='<?= base_url() . $item['product_image'] ?>' data-toggle='lightbox' data-gallery='order-images'> <img src='<?= base_url() . $item['product_image'] ?>' class='h-75'></a>
                                                </div>
                                                <div><span class="text-bold">Product Type : </span><small><?= ucwords(str_replace('_', ' ', $item['product_type'])); ?> </small></div>
                                                <div><span class="text-bold">Variant ID : </span><?= $item['product_variant_id'] ?> </div>
                                                <?php if (isset($item['product_variants']) && !empty($item['product_variants'])) { ?>
                                                    <div><span class="text-bold">Variants : </span><?= str_replace(',', ' | ', $item['product_variants'][0]['variant_values']) ?> </div>
                                                <?php } ?>
                                                <div><span class="text-bold">Name : </span><small><?= $item['pname'] ?> </small></div>
                                                <div><span class="text-bold">Quantity : </span><?= $item['quantity'] ?> </div>
                                                <div><span class="text-bold">Price : </span><?= $item['price'] + $item['tax_amount'] ?></div>
                                                <div><span class="text-bold">Discounted Price : </span> <?= $item['discounted_price'] ?> </div>
                                                <div><span class="text-bold">Subtotal : </span><?= $item['price'] * $item['quantity'] ?> </div>
                                                <?php
                                                $badges = ["awaiting" => "secondary", "received" => "primary", "processed" => "info", "shipped" => "warning", "delivered" => "success", "returned" => "danger", "cancelled" => "danger"]
                                                ?>
                                                <div><span class="text-bold">Active Status : </span> <span class="badge badge-<?= $badges[$item['active_status']] ?>"> <small><?= $item['active_status'] ?></small></span></div>
                                            </div>
                                        <?php

                                        }
                                        echo '</div>';
                                        ?>
                                        <div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Total(<?= $settings['currency'] ?>)</th>
                                    <td id=' amount'><?php echo $order_detls[0]['order_total'];
                                                        $total = $order_detls[0]['order_total'];
                                                        ?></td>
                                </tr>

                                <tr class="d-none">
                                    <th class="w-10px">Tax(<?= $settings['currency'] ?>)</th>
                                    <td id='amount'><?php echo $tax_amount;
                                                    //$total = floatval($total + $tax_amount);  
                                                    ?></td>
                                </tr>

                                <tr>
                                    <th class="w-10px">Delivery Charge(<?= $settings['currency'] ?>)</th>
                                    <td id='delivery_charge'><?php echo $order_detls[0]['delivery_charge'];
                                                                $total = $total + $order_detls[0]['delivery_charge']; ?></td>

                                </tr>

                                <tr>
                                    <th class="w-10px">Wallet Balance(<?= $settings['currency'] ?>)</th>
                                    <td><?php echo $order_detls[0]['wallet_balance'];
                                        $total = $total - $order_detls[0]['wallet_balance']; ?></td>
                                </tr>

                                <input type="hidden" name="total_amount" id="total_amount" value="<?php echo $order_detls[0]['order_total'] + $order_detls[0]['delivery_charge'] ?>">
                                <input type="hidden" name="final_amount" id="final_amount" value="<?php echo $order_detls[0]['final_total']; ?>">

                                <tr>
                                    <th class="w-10px">Promo Code Discount (<?= $settings['currency'] ?>)</th>
                                    <td><?php echo $order_detls[0]['promo_discount'];
                                        $total = floatval($total -
                                            $order_detls[0]['promo_discount']); ?></td>
                                </tr>
                                <?php
                                if (isset($order_detls[0]['discount']) && $order_detls[0]['discount'] > 0) {
                                    $discount = $order_detls[0]['total_payable']  *  ($order_detls[0]['discount'] / 100);
                                    $total = round($order_detls[0]['total_payable'] - $discount, 2);
                                }
                                ?>
                                <tr>
                                    <th class="w-10px">Payable Total(<?= $settings['currency'] ?>)</th>
                                    <td><input type="text" class="form-control" id="final_total" name="final_total" value="<?= $total; ?>" disabled></td>
                                </tr>
                                <tr>
                                    <th>Deliver By</th>
                                    <td>
                                        <select id='deliver_by' name='deliver_by' class='form-control col-md-7 col-xs-12' required>
                                            <option value=''>Select Delivery Boy</option>
                                            <?php

                                            foreach ($delivery_res as $row) {
                                                $selected = (!empty($order_detls[0]['delivery_boy_id']) && $order_detls[0]['delivery_boy_id'] == $row['user_id']) ? 'selected' : '';
                                            ?>
                                                <option value="<?= $row['user_id'] ?>" <?= $selected ?>><?= $row['username'] ?></option>
                                            <?php  } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Payment Method</th>
                                    <td><?php echo $order_detls[0]['payment_method']; ?></td>
                                </tr>
                                <?php if (!empty($bank_transfer)) { ?>
                                    <tr>
                                        <th class="w-10px">Bank Transfers</th>
                                        <td>
                                            <div class="col-md-6">
                                                <?php $status = ["history", "ban", "check"]; ?>
                                                <a class="btn btn-primary btn-xs mr-1 mb-1 " title="Current Status: Pending" href="javascript:void(0)" data-id="<?= $order_detls[0]['id']; ?>"><i class="fa fa-<?= $status[$bank_transfer[0]['status']] ?>"></i></a>
                                                <?php $i = 1;
                                                foreach ($bank_transfer as $row1) { ?>
                                                    <small>[<a href="<?= base_url() . $row1['attachments'] ?>" target="_blank">Attachment <?= $i ?> </a>] </small>
                                                    <a class="delete-receipt btn btn-danger btn-xs mr-1 mb-1" title="Delete" href="javascript:void(0)" data-id="<?= $row1['id']; ?>"><i class="fa fa-trash"></i></a>
                                                <?php $i++;
                                                } ?>
                                                <select name="update_receipt_status" id="update_receipt_status" class="form-control status" data-id="<?= $order_detls[0]['id']; ?>" data-user_id="<?= $order_detls[0]['user_id']; ?>">
                                                    <option value=''>Select Status</option>
                                                    <option value="1" <?= (isset($bank_transfer[0]['status']) && $bank_transfer[0]['status'] == 1) ? "selected" : ""; ?>>Rejected</option>
                                                    <option value="2" <?= (isset($bank_transfer[0]['status']) && $bank_transfer[0]['status'] == 2) ? "selected" : ""; ?>>Accepted</option>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <th class="w-10px">Address</th>
                                    <td><?php echo $order_detls[0]['address']; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Delivery Date & Time</th>
                                    <td><?php echo (!empty($order_detls[0]['delivery_date']) && $order_detls[0]['delivery_date'] != NUll) ? date('d-M-Y', strtotime($order_detls[0]['delivery_date'])) . " - " . $order_detls[0]['delivery_time'] : "Anytime"; ?></td>
                                </tr>
                                <tr>
                                    <th class="w-10px">Order Date</th>
                                    <td><?php echo date('d-M-Y', strtotime($order_detls[0]['date_added'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <select name="status" id="status" class="form-control" data-isjson="true" data-orderid="<?= $order_detls[0]['id']; ?>">
                                            <?php if ($order_detls[0]['payment_method'] != 'bank_transfer') { ?>
                                                <option value="received" <?= (isset($order_detls[0]['active_status']) && $order_detls[0]['active_status'] == 'received') ? 'selected' : '' ?>>Received</option>
                                            <?php } ?>
                                            <option value="processed" <?= (isset($order_detls[0]['active_status']) && $order_detls[0]['active_status'] == 'processed') ? 'selected'  : '' ?>>Processed</option>
                                            <option value="shipped" <?= (isset($order_detls[0]['active_status']) && $order_detls[0]['active_status'] == 'shipped') ? 'selected' : '' ?>>Shipped</option>
                                            <option value="delivered" <?= (isset($order_detls[0]['active_status']) && $order_detls[0]['active_status'] == 'delivered') ? 'selected'  : '' ?>>Delivered</option>
                                            <option value="cancelled" <?= (isset($order_detls[0]['active_status']) && $order_detls[0]['active_status'] == 'cancelled') ? 'selected'  : '' ?>>Cancel</option>
                                            <option value="returned" <?= (isset($order_detls[0]['active_status']) && $order_detls[0]['active_status'] == 'returned') ? 'selected' : '' ?>>Returned</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="form-group">
                                            <button type="reset" class="btn btn-warning">Reset</button>
                                            <button type="submit" class="btn btn-success update_order" id="submit_btn">Update Order</button>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <!--/.card-->
                </div>
                <!--/.col-md-12-->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>