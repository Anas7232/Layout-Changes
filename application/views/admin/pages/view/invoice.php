<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Invoice</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/home'); ?>">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Invoice</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info " id="section-to-print">
                        <div class="row m-3">
                            <div class="col-md-12 d-flex justify-content-between">
                                <h2 class="text-left">
                                    <img src="<?= base_url()  . get_settings('logo') ?>" class="d-block" style="max-width: 250px;max-height: 100px;">
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
                                    <strong><?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($order_detls[0]['mobile']) - 3) . substr($order_detls[0]['mobile'], -3) : $order_detls[0]['mobile']; ?></strong><br>
                                    <strong><?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? str_repeat("X", strlen($order_detls[0]['email']) - 3) . substr($order_detls[0]['email'], -3) : $order_detls[0]['email']; ?></strong><br>
                                </address>
                            </div>
                            <!-- /.col -->
                            <?php if (!empty($order_detls[0]['id'])) { ?>
                                <div class="col-sm-2 invoice-col">
                                    <br> <b>Retail Invoice</b>
                                    <br> <b>No : </b>#
                                    <?= $order_detls[0]['id'] ?>
                                    <br> <b>Date: </b>
                                    <?= $order_detls[0]['date_added'] ?>
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
                                            <th>variants</th>
                                            <th>Price</th>
                                            <th>Tax (%)</th>
                                            <th class="d-none">Tax Amount (
                                                <?= $settings['currency'] ?>)</th>
                                            <th>Qty</th>
                                            <th>SubTotal (
                                                <?= $settings['currency'] ?>)</th>
                                            <th class="d-none">Order Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1;
                                        $total = $quantity = $total_tax = $total_discount = 0;
                                        foreach ($items as $row) {
                                            $product_variants = get_variants_values_by_id($row['product_variant_id']);
                                            $product_variants = isset($product_variants[0]['variant_values']) && !empty($product_variants[0]['variant_values']) ? str_replace(',', ' | ', $product_variants[0]['variant_values']) : '-';
                                            $tax_amount = ($row['tax_amount']) ? $row['tax_amount'] : '0';
                                            $total += floatval($row['price'] + $tax_amount) * floatval($row['quantity']);
                                            $quantity += floatval($row['quantity']);
                                            $total_tax += floatval($row['tax_amount']);
                                            $price_with_tax = $row['price'] + $tax_amount;
                                            $sub_total = floatval($row['price']) * $row['quantity'] + $tax_amount;
                                        ?>
                                            <tr>
                                                <td>
                                                    <?= $i ?>
                                                    <br>
                                                </td>
                                                <td>
                                                    <?= $row['product_variant_id'] ?>
                                                    <br>
                                                </td>
                                                <?= (isset($row['product_identity']) && !empty($row['product_identity']))?"<td>". $row['product_identity']."<br></td>" : ""?>
                                                <td class="w-25">
                                                    <?= $row['pname'] ?>
                                                    <br>
                                                </td>
                                                <td class="w-25">
                                                    <?= $product_variants ?>
                                                    <br>
                                                </td>
                                                <td>
                                                    <?= $settings['currency'] . ' ' . $price_with_tax ?>
                                                    <br>
                                                </td>

                                                <td>
                                                    <?= ($row['tax_percent']) ? $row['tax_percent'] : '0' ?>
                                                    <br>
                                                </td>
                                                <td class="d-none">
                                                    <?= $tax_amount ?>
                                                    <br>
                                                </td>
                                                <td>
                                                    <?= $row['quantity'] ?>
                                                    <br>
                                                </td>
                                                <td>
                                                    <?= $settings['currency'] . ' ' . $sub_total ?>
                                                    <br>
                                                </td>
                                                <td class="d-none">
                                                    <?= $row['active_status'] ?>
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
                                            <th></th>
                                            <th>Total</th>
                                            <th class="d-none">
                                                <?= $total_tax ?>
                                            </th>
                                            <th>
                                                <?= $quantity ?>
                                                <br>
                                            </th>
                                            <th>
                                                <?= $settings['currency'] . ' ' . $total ?>
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
                                                <th>Total Order Price (
                                                    <?= $settings['currency'] ?>)</th>
                                                <td>+
                                                    <?= $total ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Delivery Charge (
                                                    <?= $settings['currency'] ?>)</th>
                                                <td>+
                                                    <?php $total += $order_detls[0]['delivery_charge'];
                                                    echo $order_detls[0]['delivery_charge']; ?>
                                                </td>
                                            </tr>
                                            <tr class="d-none">
                                                <th>Tax
                                                    <?= $settings['currency'] ?></th>
                                                <td>+
                                                    <?php //$total += $total_tax;
                                                    echo $total_tax; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Wallet Used (
                                                    <?= $settings['currency'] ?>)</th>
                                                <td><?php $total -= $order_detls[0]['wallet_balance'];
                                                    echo  '- ' . $order_detls[0]['wallet_balance']; ?> </td>
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
                                                <th>Total Payable (
                                                    <?= $settings['currency'] ?>)</th>
                                                <td>
                                                    <?= $settings['currency'] . '  ' . $total ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Final Total (
                                                    <?= $settings['currency'] ?>)</th>
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
                        <!-- /.row -->
                        <!-- this row will not appear when printing -->
                        <div class="row m-3" id="section-not-to-print">
                            <div class="col-xs-12">
                                <button type='button' value='Print this page' onclick='{window.print()};' class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                            </div>
                        </div>
                    </div>
                    <!--/.card-->
                </div>
                <!--/.col-md-12-->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>