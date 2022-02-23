<!-- Demo header-->
<section class="header settings-tab">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-12 orders-section settings-tab-content">
                <div class="mb-4 card border-0">
                    <div class="card-header bg-white">
                        <div class="row justify-content-between">
                            <div class="col">
                                <p class="text-muted"> <?= !empty($this->lang->line('order_id')) ? $this->lang->line('order_id') : 'Order ID' ?><span class="font-weight-bold text-dark"> : <?= $order['id'] ?></span></p>
                                <p class="text-muted"> <?= !empty($this->lang->line('place_on')) ? $this->lang->line('place_on') : 'Place On' ?><span class="font-weight-bold text-dark"> : <?= $order['date_added'] ?></span> </p>
                                <?php if ($order['otp'] != 0) { ?>
                                    <p class="text-muted"> <?= !empty($this->lang->line('otp')) ? $this->lang->line('otp') : 'OTP' ?> <span class="font-weight-bold text-dark"> : <?= $order['otp'] ?></span> </p>
                                <?php } ?>
                            </div>

                            <div class="flex-col my-auto">
                                <h6 class="ml-auto mr-3">
                                    <a target="_blank" href="<?= base_url('my-account/order-invoice/' . $order['id']) ?>" class='button button-primary'><?= !empty($this->lang->line('invoice')) ? $this->lang->line('invoice') : 'Invoice' ?></a>
                                    <a href="<?= base_url('my-account/orders/') ?>" class='button button-danger-outline'><?= !empty($this->lang->line('back_to_list')) ? $this->lang->line('back_to_list') : 'Back to List' ?></a>
                                </h6>
                            </div>
                        </div>
                        <br>
                        <?php if ($order['payment_method'] == "Bank Transfer") { ?>
                            <div class="row">
                                <form class="form-horizontal " id="send_bank_receipt_form" action="<?= base_url('cart/send-bank-receipt'); ?>" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <div class="form-group ">
                                        <label for="receipt"> <strong> Bank Payment Receipt</strong> </label>
                                        <input type="file" class="form-control" name="attachments[]" id="receipt" multiple />
                                    </div>
                                    <div class="form-group">
                                        <button type="reset" class="button button-warning-outline">Reset</button>
                                        <button type="submit" class="button button-success-outline" id="submit_btn">Send</button>
                                    </div>
                                </form>

                            </div>
                        <?php } ?>
                        <div class="row">
                            <?php if (!empty($bank_transfer)) { ?>
                                <div class="col-md-6">
                                    <?php $i = 1;
                                    foreach ($bank_transfer as $row1) { ?>
                                        <small>[<a href="<?= base_url() . $row1['attachments'] ?>" target="_blank">Attachment <?= $i ?> </a>]</small>
                                    <?php $i++;
                                    }
                                    if ($bank_transfer[0]['status'] == 0) { ?>
                                        <label class="badge badge-warning">Pending</label>
                                    <?php } else if ($bank_transfer[0]['status'] == 1) { ?>
                                        <label class="badge badge-danger">Rejected</label>
                                    <?php } else if ($bank_transfer[0]['status'] == 2) { ?>
                                        <label class="badge badge-primary">Accepted</label>
                                    <?php } else { ?>
                                        <label class="badge badge-danger">Invalid Value</label>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php foreach ($order['order_items'] as $key => $item) { ?>
                            <div class="media flex-column flex-sm-row">
                                <div class="media-body ">
                                    <h5 class="bold"><?= ($key + 1) . '. ' . $item['name'] ?></h5>
                                    <p class="text-muted"> <?= !empty($this->lang->line('quantity')) ? $this->lang->line('quantity') : 'Quantity' ?> : <?= $item['quantity'] ?></p>

                                    <h4 class="mt-3 mb-2 bold"> <span class="mt-5"><i><?= $settings['currency'] ?></i></span> <?= number_format(($item['price'] * $item['quantity']), 2) ?> <span class="small text-muted"></span></h4>
                                    <?php if (!$item['is_already_cancelled'] && $item['is_cancelable']) { ?>
                                        <button class="button button-danger button-sm update-order-item" data-status="cancelled" data-item-id="<?= $item['id'] ?>"><?= !empty($this->lang->line('cancel')) ? $this->lang->line('cancel') : 'Cancel' ?></button>
                                    <?php } ?>
                                    <?php if (!$item['is_already_cancelled'] && !$item['is_already_returned'] && $item['is_returnable'] && $item['active_status'] == 'delivered') { ?>
                                        <button class="button button-warning button-sm update-order-item" data-status="returned" data-item-id="<?= $item['id'] ?>"><?= !empty($this->lang->line('return')) ? $this->lang->line('return') : 'Return' ?></button>
                                    <?php } ?>
                                </div>
                                <img class="align-self-center img-fluid" src="<?= $item['image_sm'] ?>" width="180 " height="180">
                            </div>
                            <div class="row">
                                <div class="col p-0">
                                    <ul id="progressbar">
                                        <?php
                                        $status = array('received', 'processed', 'shipped', 'delivered');
                                        $i = 1;
                                        foreach ($item['status'] as $value) { ?>
                                            <?php
                                            $class = '';
                                            if ($value[0] == "cancelled" || $value[0] == "returned") {
                                                $class = 'cancel';
                                                $status = array();
                                            } elseif (($ar_key = array_search($value[0], $status)) !== false) {
                                                unset($status[$ar_key]);
                                            }
                                            ?>
                                            <li class="active <?= $class ?>" id="step<?= $i ?>">
                                                <p><?= strtoupper($value[0]) ?></p>
                                                <p><?= $value[1] ?></p>
                                            </li>
                                        <?php
                                            $i++;
                                        } ?>
                                        <?php foreach ($status as $value) { ?>
                                            <li id="step<?= $i ?>">
                                                <p><?= strtoupper($value) ?></p>
                                            </li>
                                        <?php $i++;
                                        } ?>
                                    </ul>
                                </div>
                            </div>
                            <hr>
                        <?php } ?>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="h5"><?= !empty($this->lang->line('shipping_details')) ? $this->lang->line('shipping_details') : 'Shipping Details' ?></h6>
                                <hr>
                                <span><?= $order['username'] ?></span> <br>
                                <span><?= $order['address'] ?></span> <br>
                                <span><?= $order['mobile'] ?></span> <br>
                                <span><?= $order['delivery_time'] ?></span> <br>
                                <span><?= $order['delivery_date'] ?></span> <br>
                            </div>
                            <div class="col-md-6">
                                <h6 class="h5"><?= !empty($this->lang->line('price_details')) ? $this->lang->line('price_details') : 'Price Details' ?></h6>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th><?= !empty($this->lang->line('total_order_price')) ? $this->lang->line('total_order_price') : 'Total Order Price' ?></th>
                                                <td>+ <?= $settings['currency'] . ' ' . number_format($order['total'], 2) ?></td>
                                            </tr>
                                            <tr>
                                                <th><?= !empty($this->lang->line('delivery_charge')) ? $this->lang->line('delivery_charge') : 'Delivery Charge' ?></th>
                                                <td>+ <?= $settings['currency'] . ' ' . number_format($order['delivery_charge'], 2) ?></td>
                                            </tr>
                                            <tr class="d-none">
                                                <th><?= !empty($this->lang->line('tax')) ? $this->lang->line('tax') : 'Tax' ?> - (<?= $order['total_tax_percent'] ?>%)</th>
                                                <td>+ <?= $settings['currency'] . ' ' . number_format($order['total_tax_amount'], 2) ?></td>
                                            </tr>
                                            <?php if (!empty($order['promo_code']) && !empty($order['promo_discount'])) { ?>
                                                <tr>
                                                    <th><?= !empty($this->lang->line('promocode_discount')) ? $this->lang->line('promocode_discount') : 'Promocode Discount' ?> - (<?= $order['promo_code'] ?>)
                                                    </th>
                                                    <td>- <?= $settings['currency'] . ' ' . number_format($order['promo_discount'], 2) ?></td>
                                                </tr>
                                            <?php } ?>
                                            <tr>
                                                <th><?= !empty($this->lang->line('wallet_used')) ? $this->lang->line('wallet_used') : 'Wallet Used' ?></th>
                                                <td>- <?= $settings['currency'] . ' ' . number_format($order['wallet_balance'], 2) ?></td>
                                            </tr>
                                            <tr class="h6">
                                                <th><?= !empty($this->lang->line('final_total')) ? $this->lang->line('final_total') : 'Final Total' ?></th>
                                                <td><?= $settings['currency'] . ' ' . number_format($order['final_total'], 2) ?><span class="small text-muted"> <?= !empty($this->lang->line('via')) ? $this->lang->line('via') : 'via' ?> (<?= $order['payment_method'] ?>) </span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- /.col -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white px-sm-3 pt-sm-4 px-0">
                        <div class="row text-center ">
                            <?php if ($order['is_cancelable'] && !$order['is_already_cancelled']) { ?>
                                <div class="col my-auto">
                                    <a class="update-order block button-sm buttons btn-6-1 mt-3 m-0" data-status="cancelled" data-order-id="<?= $order['id'] ?>"><?= !empty($this->lang->line('cancel')) ? $this->lang->line('cancel') : 'Cancel' ?></a>
                                </div>
                            <?php } ?>
                            <?php if ($order['is_returnable'] && !$order['is_already_returned']) { ?>
                                <div class="col my-auto ">
                                    <a class="update-order block buttons button-sm btn-6-3 mt-3 m-0" data-status="returned" data-order-id="<?= $order['id'] ?>"><?= !empty($this->lang->line('return')) ? $this->lang->line('return') : 'Return' ?></a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>