<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info " id="section-to-print">
                    <div class="row m-3">
                        <div class="col-md-12 d-flex justify-content-between">
                            <h2 class="text-left">
                                <img src="<?= base_url()  . get_settings('logo') ?>" class="d-block " style="max-width:250px;max-height:100px;">
                            </h2>
                            <h2 class="text-right">
                                Mo. <?= $settings['support_number'] ?>
                            </h2>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row m-3 d-flex justify-content-between">
                        <div class="col-sm-4 invoice-col">From <address>
                                <strong><?= $settings['app_name'] ?></strong><br>
                                Email: <?= $settings['support_email'] ?><br>
                                Customer Care : <?= $settings['support_number'] ?><br>
                                <?php if (isset($settings['tax_name']) && !empty($settings['tax_name'])) { ?>
                                    <b><?= $settings['tax_name'] ?></b> : <?= $settings['tax_number'] ?><br>
                                <?php } ?>
                                <?php if (!empty($items[0]['delivery_boy'])) { ?>Delivery By: <?= $items[0]['delivery_boy'] ?><?php } ?>
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">To <address>
                                <strong><?= ($order_detls[0]['user_name'] != "") ? $order_detls[0]['user_name'] : $order_detls[0]['uname'] ?></strong><br>
                                <?= $order_detls[0]['address'] ?><br>
                                <strong><?= $order_detls[0]['mobile'] ?></strong><br>
                                <strong><?= $order_detls[0]['email'] ?></strong><br>
                            </address>
                        </div>
                        <!-- /.col -->
                        <?php if (!empty($order_detls[0]['id'])) { ?>
                            <div class="col-sm-2 invoice-col">
                                <br> <b>Retail Invoice</b>
                                <br> <b>No : </b>#<?= $order_detls[0]['id'] ?>
                                <br> <b>Date: </b><?= $order_detls[0]['date_added'] ?>
                                <br>
                            </div>
                        <?php } ?>
                    </div>
                    <!-- /.row -->
                    <!-- Table row -->
                    <div class="row m-3">
                        <div class="col-xs-12 table-responsive">
                            <table class="table borderless text-center text-sm">
                                <thead class="">
                                    <tr>
                                        <th>Sr No.</th>
                                        <th>Product Code</th>
                                        <?= (isset($row['product_identity']) && !empty($row['product_identity']))?" <th>Product Identity</th> ": ""?>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Tax (%)</th>
                                        <th>Qty</th>
                                        <th class="d-none">Tax Amount (<?= $settings['currency'] ?>)</th>
                                        <th>SubTotal (<?= $settings['currency'] ?>)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;
                                    $total = $quantity = $total_tax = $total_discount = 0;

                                    foreach ($items as $row) {
                                        $total += floatval($row['price']) * floatval($row['quantity']);
                                        $quantity += floatval($row['quantity']);
                                        $total_tax += floatval($row['tax_amount']); ?>
                                        <tr>
                                            <td>
                                                <?= $i ?>
                                                <br>
                                            </td>
                                            <td>
                                                <?= $row['product_variant_id'] ?><br>
                                            </td>
                                            <td class="w-25">
                                                <?= $row['pname'] ?>
                                                <br>
                                            </td>
                                            <?= (isset($row['product_identity']) && !empty($row['product_identity']))?"<td>". $row['product_identity']."<br></td>" : ""?>
                                            <td>
                                                <?= $settings['currency'] . ' ' . number_format($row['price'], 2) ?>
                                                <br>
                                            </td>

                                            <td>
                                                <?= ($row['tax_percent']) ? $row['tax_percent'] : '0' ?>
                                                <br>
                                            </td>
                                            <td>
                                                <?= $row['quantity'] ?>
                                                <br>
                                            </td>
                                            <td class="d-none">
                                                <?= ($row['tax_amount']) ? $settings['currency'] . ' ' . number_format($row['tax_amount'], 2) : '0' ?>
                                                <br>
                                            </td>
                                            <td>
                                                <?= $settings['currency'] . ' ' . number_format(floatval($row['price']) * $row['quantity'], 2) ?>
                                                <br>
                                            </td>
                                        </tr>
                                    <?php $i++;
                                    } ?>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>Total</th>
                                        <th>
                                            <?= $quantity ?><br>
                                        </th>
                                        <th>
                                            <?= $settings['currency'] . ' ' . number_format($total, 2) ?>
                                            <br>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <div class="row m-2 text-right">
                        <!-- accepted payments column -->
                        <div class="col-md-9 offset-md-2">
                            <!--<p class="lead">Payment Date: </p>-->
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th>Total Order Price</th>
                                            <td>+
                                                <?= $settings['currency'] . ' ' . number_format($total, 2) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Delivery Charge</th>
                                            <td>+
                                                <?php $total += $order_detls[0]['delivery_charge'];
                                                echo $settings['currency'] . ' ' . number_format($order_detls[0]['delivery_charge'], 2); ?>
                                            </td>
                                        </tr>
                                        <tr class="d-none">
                                            <th>Tax - (<?= $items[0]['tax_percent'] ?>%)</th>
                                            <td>+
                                                <?php $total += $total_tax;
                                                echo $settings['currency'] . ' ' . number_format($total_tax, 2); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Wallet Used</th>
                                            <td>-
                                                <?php $total -= $order_detls[0]['wallet_balance'];
                                                echo  $settings['currency'] . ' ' . number_format($order_detls[0]['wallet_balance'], 2); ?>
                                            </td>
                                        </tr>
                                        <?php
                                        if (isset($promo_code[0]['promo_code'])) { ?>
                                            <tr>
                                                <th>Promo (
                                                    <?= $promo_code[0]['promo_code'] ?>) Discount (
                                                    <?= floatval($promo_code[0]['discount']); ?>
                                                    <?= ($promo_code[0]['discount_type'] == 'percentage') ? '%' : ' '; ?> )
                                                </th>
                                                <td>-
                                                    <?php
                                                    echo $order_detls[0]['promo_discount'];
                                                    $total = $total - $order_detls[0]['promo_discount'];
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <?php
                                        if (isset($order_detls[0]['discount']) && $order_detls[0]['discount'] > 0 && $order_detls[0]['discount'] != NULL) { ?>
                                            <tr>
                                                <th>Special Discount
                                                    <?= $settings['currency'] ?>(<?= $order_detls[0]['discount'] ?> %)</th>
                                                <td>-
                                                    <?php echo $special_discount = round($total * $order_detls[0]['discount'] / 100, 2);
                                                    $total = floatval($total - $special_discount);
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr class="d-none">
                                            <th>Total Payable</th>
                                            <td>
                                                <?= $settings['currency'] . '  ' . number_format($total, 2) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Final Total</th>
                                            <td>
                                                <?php $final_total = $order_detls[0]['final_total'] - $order_detls[0]['wallet_balance'] - $order_detls[0]['promo_discount'] - $order_detls[0]['discount']; ?>
                                                <?= $settings['currency'] . '  ' . $final_total ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                </div>
                <!--/.card-->
            </div>
            <!--/.col-md-12-->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    <?php if (isset($print_btn_enabled) && $print_btn_enabled) { ?>
        <div class="col-12">
            <div class="text-center">
                <button class="btn btn-primary" onclick="window.print();">Print</button>
            </div>
        </div>
    <?php } ?>
</section>