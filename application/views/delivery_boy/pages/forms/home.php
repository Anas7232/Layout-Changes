 <div class="content-wrapper">
     <section class="content">
         <div class="container-fluid">
             <div class="row pt-4">
                 <div class="col-xl col-lg col-md col-12">
                     <div class="card pull-up">
                         <div class="card-content">
                             <div class="card-body">
                                 <div class="media d-flex">
                                     <div class="align-self-center text-warning">
                                         <i class="ion-ios-cart-outline display-4"></i>
                                     </div>
                                     <div class="media-body text-right">
                                         <h5 class="text-muted text-bold-500">Orders</h5>
                                         <h3 class="text-bold-600"><?= $order_counter ?></h3>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 <?php if ($bonus > 0 && $bonus != null) { ?>
                     <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                         <div class="card pull-up">
                             <div class="card-content">
                                 <div class="card-body">
                                     <div class="media d-flex">
                                         <div class="align-self-center text-primary">
                                             <i class="fas fa-wallet fa-3x"></i>
                                         </div>
                                         <div class="media-body text-right">
                                             <h5 class="text-muted text-bold-500">Bonus</h5>
                                             <h3 class="text-bold-600"><?= $bonus ?></h3>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 <?php } ?>
                 <div class="col-xl col-lg col-md col-12">
                     <div class="card pull-up">
                         <div class="card-content">
                             <div class="card-body">
                                 <div class="media d-flex">
                                     <div class="align-self-center text-success">
                                         <i class="ion-cash display-4"></i>
                                     </div>
                                     <div class="media-body text-right">
                                         <h5 class="text-muted text-bold-500">Balance</h5>
                                         <h3 class="text-bold-600"><?= $curreny . ' ' . number_format($balance, 2) ?></h3>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="col-md-12 main-content">
                     <div class="card content-area p-4">
                         <div class="card-innr">
                             <div class="gaps-1-5x row d-flex adjust-items-center">
                                 <div class="row col-md-12">
                                     <div class="form-group col-md-4">
                                         <label>Date and time range:</label>
                                         <div class="input-group col-md-12">
                                             <div class="input-group-prepend">
                                                 <span class="input-group-text"><i class="far fa-clock"></i></span>
                                             </div>
                                             <input type="text" class="form-control float-right" id="datepicker">
                                             <input type="hidden" id="start_date" class="form-control float-right">
                                             <input type="hidden" id="end_date" class="form-control float-right">
                                         </div>
                                         <!-- /.input group -->
                                     </div>
                                     <div class="form-group col-md-4">
                                         <div>
                                             <label>Filter By status</label>
                                             <select id="order_status" name="order_status" placeholder="Select Status" required="" class="form-control">
                                                 <option value="">All Orders</option>
                                                 <option value="received">Received</option>
                                                 <option value="processed">Processed</option>
                                                 <option value="shipped">Shipped</option>
                                                 <option value="delivered">Delivered</option>
                                                 <option value="cancelled">Cancelled</option>
                                                 <option value="returned">Returned</option>
                                             </select>
                                         </div>
                                     </div>
                                     <div class="form-group col-md-4 d-flex align-items-center pt-4">
                                         <button type="button" class="btn btn-outline-primary btn-sm" onclick="status_date_wise_search()">Filter</button>
                                     </div>
                                 </div>
                             </div>
                             <table class='table-striped' data-toggle="table" data-url="<?= base_url('delivery_boy/orders/view_orders') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" 
                             data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel"]' data-export-options='{
                      "fileName": "orders-list",
                      "ignoreColumn": ["state"] 
                      }' data-query-params="home_query_params">
                                 <thead>
                                     <tr>
                                         <th data-field="id" data-sortable='true' data-footer-formatter="totalFormatter">Order ID</th>
                                         <th data-field="user_id" data-sortable='true' data-visible="false">User ID</th>
                                         <th data-field="qty" data-sortable='true' data-visible="false">Qty</th>
                                         <th data-field="name" data-sortable='true'>User Name</th>
                                         <th data-field="notes" data-sortable='false' data-visible='false'>O. Notes</th>
                                         <th data-field="mobile" data-sortable='true'>Mobile</th>
                                         <th data-field="items" data-sortable='true' data-visible="false">Items</th>
                                         <th data-field="total" data-sortable='true' data-visible="true">Total(<?= $curreny ?>)</th>
                                         <th data-field="delivery_charge" data-sortable='true' data-footer-formatter="delivery_chargeFormatter">D.Charge(<?= $curreny ?>)</th>
                                         <th data-field="wallet_balance" data-sortable='true' data-visible="true">Wallet Used(<?= $curreny ?>)</th>
                                         <th data-field="promo_code" data-sortable='true' data-visible="false">Promo Code</th>
                                         <th data-field="promo_discount" data-sortable='true' data-visible="true">Promo disc.(<?= $curreny ?>)</th>
                                         <th data-field="discount" data-sortable='true' data-visible="true">Discount <?= $curreny ?>(%)</th>
                                         <th data-field="final_total" data-sortable='true'>Final Total(<?= $curreny ?>)</th>
                                         <th data-field="deliver_by" data-sortable='true' data-visible='false'>Deliver By</th>
                                         <th data-field="payment_method" data-sortable='true' data-visible="true">Payment Method</th>
                                         <th data-field="address" data-sortable='true'>Address</th>
                                         <th data-field="delivery_time" data-sortable='true' data-visible='false'>Delivery Time</th>
                                         <th data-field="delivery_date" data-sortable='true' data-visible='false'>Delivery Date</th>
                                         <th data-field="status" data-sortable='true' data-visible='false'>Status</th>
                                         <th data-field="active_status" data-sortable='true' data-visible='true'>Active Status</th>
                                         <th data-field="date_added" data-sortable='true'>Order Date</th>
                                         <th data-field="operate">Action</th>
                                     </tr>
                                 </thead>
                             </table>
                         </div><!-- .card-innr -->
                     </div><!-- .card -->
                 </div>
             </div>
         </div>
     </section>
 </div>