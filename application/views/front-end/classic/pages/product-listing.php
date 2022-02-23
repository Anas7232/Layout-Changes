<!-- breadcrumb --><?php //echo phpinfo();?>
<section class="breadcrumb-title-bar colored-breadcrumb" style="display:none;">
    <div class="main-content responsive-breadcrumb">
        <h1><?= isset($page_main_bread_crumb) ? $page_main_bread_crumb : 'Product Listing' ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>"><?= !empty($this->lang->line('home')) ? $this->lang->line('home') : 'Home' ?></a></li>
                <?php if (isset($right_breadcrumb) && !empty($right_breadcrumb)) {
                    foreach ($right_breadcrumb as $row) {
                ?>
                        <li class="breadcrumb-item"><?= $row ?></li>
                <?php }
                } ?>
                <li class="breadcrumb-item active" aria-current="page"><?= !empty($this->lang->line('products')) ? $this->lang->line('products') : 'Products' ?></li>
            </ol>
        </nav>
    </div>

</section>
<!-- end breadcrumb -->
<input type="hidden" id="product-filters" value='<?= (!empty($filters)) ? escape_array($filters) : ""  ?>' data-key="<?= $filters_key ?>" />
<section class="listing-page content main-content">
    <div class="product-listing card-solid py-4">
        <div class="row mx-0">
            <!-- Dektop Sidebar -->
            <?php if (isset($products['filters']) && !empty($products['filters'])) { ?>
                <div class=" order-md-1 col-lg-3 filter-section sidebar-filter-sm container pt-2 pb-2 filter-sidebar-view">
                    <div id="product-filters-desktop">
                        <?php foreach ($products['filters'] as $key => $row) {
                            $row_attr_name = str_replace(' ', '-', $row['name']);
                            $attribute_name = isset($_GET[strtolower('filter-' . $row_attr_name)]) ? $this->input->get(strtolower('filter-' . $row_attr_name), true) : 'null';
                            $selected_attributes = explode('|', $attribute_name);
                            $attribute_values = explode(',', $row['attribute_values']);
                            $attribute_values_id = explode(',', $row['attribute_values_id']);
                        ?>
                            <div class="card-custom">
                                <div class="card-header-custom" id="h1">
                                    <h2 class="clearfix mb-0">
                                        <a class="collapse-arrow btn btn-link collapsed" data-toggle="collapse" data-target="#c<?= $key ?>" aria-expanded="true" aria-controls="collapseone"><?= html_escape($row['name']) ?><i class="fa fa-angle-down rotate"></i></a>
                                    </h2>
                                </div>
                                <div id="c<?= $key ?>" class="collapse <?= ($attribute_name != 'null') ? 'show' : '' ?>" aria-labelledby="h1" data-parent="#accordionExample">
                                    <div class="card-body-custom">
                                        <?php foreach ($attribute_values as $key => $values) {
                                            $values = strtolower($values);
                                        ?>
                                            <div class="input-container d-flex" >
                                                <?= form_checkbox(
                                                    $values,
                                                    $values,
                                                    (in_array($values, $selected_attributes)) ? TRUE : FALSE,
                                                    array(
                                                        'class' => 'toggle-input product_attributes',
                                                        'id' => $row_attr_name . ' ' . $values,
                                                        'data-attribute' => strtolower($row['name']),                                                    	
                                                    )
                                                ) ?>
                                                <label class="toggle checkbox"  for="<?= $row_attr_name . ' ' . $values ?>">
                                                    <div class="toggle-inner"></div>
                                                </label>
                                                <?= form_label($values, $row_attr_name . ' ' . $values, array('class' => 'text-label')) ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="text-center">
                        <button class="button button-rounded button-warning product_filter_btn">Filter</button>
                    </div>
                </div>
            <?php } ?>
            <div class="col-md-8 order-md-2 <?= (isset($products['filters']) && !empty($products['filters'])) ? "col-lg-9" : "col-lg-12" ?>">
                <div class="container-fluid filter-section pt-3  pb-3">                    
                    <?php if (isset($sub_categories) && !empty($sub_categories)) { ?>
                      	<div class="category-section container-fluid text-center" style="box-shadow: unset;margin: unset;padding: unset;">
                            <div class="row">								<div class="col-md-12 col-sm-12 py-3">		                            <?php if (isset($single_category) && !empty($single_category)) { ?>				                                <span class="h3"><?= $single_category['name'] ?> </span>				                            <?php } ?>				                        </div>
                                
                           		<div class="col-md-12 col-sm-12">
                               		<div class="category-image w-75">
                                     	<div class="swiper-container category-swiper swiper-container-initialized swiper-container-horizontal icon-swiper">									            <div class="swiper-wrapper">																                <?php foreach ($sub_categories as $key => $row) { ?>																                    <div class="swiper-slide" style="background: unset !important;">																                        <div class="" >																                            <div class="category-image">																                                <div class="">																                                    <a href="javascript:;" onclick="getsubCatFunction('<?php echo html_escape($row->slug)?>');" title="<?php echo html_escape($row->name);?>">							                                                <div class="btn btn-danger rounded-0 subcatbtn" id="cat-txt-<?php echo html_escape($row->slug)?>">													                                                <span class="cat-txt" ><?= html_escape($row->name) ?></span>														                                            </div>												                                            </a>																                                </div>																                            </div>																                        </div>																                    </div>																                <?php } ?>																            </div>																            <!-- Add Pagination -->																		            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>																		            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>																		        </div>									
                                        </div>
                                    </div>                              
                            </div>
                        </div>                                                <div class="category-section container-fluid text-center" id="subcategory-div" style="box-shadow: unset;">														<div class="swiper-container category-swiper1 swiper-container-initialized swiper-container-horizontal icon-swiper">					            <div class="swiper-wrapper" id="subcategoryHtml-container">										                										            </div>										            <!-- Add Pagination -->										            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>										            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>										        </div>                            <div class="row" id="">																<!-- Sub Category HTML -->							                            </div>                                                        <div class="" id="productsHtml-div" style="margin-top:2vw;">																								<div class="row mx-0">					            <!-- Dektop Sidebar -->					            					                <div class=" order-md-1 col-lg-3 filter-section sidebar-filter-sm container pt-2 pb-2 filter-sidebar-view">										                    <div id="product-filters-desktop">											<?php if(isset($filters) && !empty($filters)){ ?>					                        					                        <?php foreach ($filters as $key => $row) {										                            $row_attr_name = str_replace(' ', '-', $row['name']);										                            $attribute_name = isset($_GET[strtolower('filter-' . $row_attr_name)]) ? $this->input->get(strtolower('filter-' . $row_attr_name), true) : 'null';										                            $selected_attributes = explode('|', $attribute_name);										                            $attribute_values = explode(',', $row['attribute_values']);										                            $attribute_values_id = explode(',', $row['attribute_values_id']);										                        ?>										                            <div class="card-custom">										                                <div class="card-header-custom" id="h1">										                                    <h2 class="clearfix mb-0">										                                        <a class="collapse-arrow btn btn-link collapsed" data-toggle="collapse" data-target="#c<?= $key ?>" aria-expanded="true" aria-controls="collapseone"><?= html_escape($row['name']) ?><i class="fa fa-angle-down rotate"></i></a>										                                    </h2>										                                </div>										                                <div id="c<?= $key ?>" class="collapse <?= ($attribute_name != 'null') ? 'show' : '' ?>" aria-labelledby="h1" data-parent="#accordionExample">										                                    <div class="card-body-custom" >										                                        <?php foreach ($attribute_values as $key => $values) {										                                            $values = strtolower($values);					                                            $onclick = "getFilterVal('". $values ."','". strtolower($row['name']) ."')";										                                        ?>										                                            <div class="input-container d-flex" >										                                                <?= 					                                                	form_checkbox(										                                                    $values,										                                                    $values,										                                                    (in_array($values, $selected_attributes)) ? TRUE : FALSE,																							                                                						                                                    array(										                                                        'class' => 'toggle-input product_attributes',										                                                        'id' => $row_attr_name . ' ' . $values,										                                                        'data-attribute' => strtolower($row['name']),																								                                                    	'onclick' => $onclick,					                                                    )										                                                ) ?>										                                                <label class="toggle checkbox" for="<?= $row_attr_name . ' ' . $values ?>">										                                                    <div class="toggle-inner"></div>										                                                </label>										                                                <?= form_label($values, $row_attr_name . ' ' . $values, array('class' => 'text-label')) ?>										                                            </div>										                                        <?php } ?>										                                    </div>										                                </div>										                            </div>										                        <?php }  }?>										                    </div>										                    <div class="text-center">										                        <button class="button button-rounded button-warning product_filter_btn1" onclick="getsubCatProductFunction();">Filter</button>										                    </div>										                </div>										            						            <div class="col-md-8 order-md-2 <?= (isset($products['filters']) && !empty($products['filters'])) ? "col-lg-9" : "col-lg-9" ?>">												                <div class="container-fluid filter-section pt-3  pb-3">												                    <div class="col-12">												                        <div class="dropdown">												                            <div class="filter-bars">												                                <div class="menu js-menu">												                                    <span class="menu__line"></span>												                                    <span class="menu__line"></span>												                                    <span class="menu__line"></span>												                                </div>												                            </div>												                            <div class="col-12 sort-by py-3">												                                <div class="dropdown float-md-right d-flex mb-4">										                                        <a href="javascript:;" id="product_grid_view_btn1" onclick="changeview(1);" class="grid-view active"><i class="fas fa-th mr-12" ></i></a>										                                        <a href="javascript:;" id="product_list_view_btn1" onclick="changeview(2);" class="grid-view"><i class="fas fa-th-list mr-12"></i></a>					                                        					                                    </div>											                                    <div class="ele-wrapper d-flex">										                                        <div class="form-group col-md-11 d-flex">										                                            <label for="product_sort_by1" class="w-25"><?= !empty($this->lang->line('sort_by')) ? $this->lang->line('sort_by') : 'Sort By' ?>:</label>										                                            <select id="product_sort_by1" class="form-control" onchange="getsubCatProductFunction();">										                                                <option><?= !empty($this->lang->line('relevance')) ? $this->lang->line('relevance') : 'Relevance' ?></option>										                                                <option value="top-rated" <?= ($this->input->get('sort') == "top-rated") ? 'selected' : '' ?>><?= !empty($this->lang->line('top_rated')) ? $this->lang->line('top_rated') : 'Top Rated' ?></option>										                                                <option value="date-desc" <?= ($this->input->get('sort') == "date-desc") ? 'selected' : '' ?>><?= !empty($this->lang->line('newest_first')) ? $this->lang->line('newest_first') : 'Newest First' ?></option>										                                                <option value="date-asc" <?= ($this->input->get('sort') == "date-asc") ? 'selected' : '' ?>><?= !empty($this->lang->line('oldest_first')) ? $this->lang->line('oldest_first') : 'Oldest First' ?></option>										                                                <option value="price-asc" <?= ($this->input->get('sort') == "price-asc") ? 'selected' : '' ?>><?= !empty($this->lang->line('price_low_to_high')) ? $this->lang->line('price_low_to_high') : 'Price - Low To High' ?></option>										                                                <option value="price-desc" <?= ($this->input->get('sort') == "price-desc") ? 'selected' : '' ?>><?= !empty($this->lang->line('price_high_to_low')) ? $this->lang->line('price_high_to_low') : 'Price - High To Low' ?></option>										                                            </select>										                                        </div>										                                    </div>												                            </div>												                        </div>												                    </div>															                    																<div class="row" id="productsListHtml-container" style="display:none;">	                             					                             				<div class="col-md-12 col-sm-6">									                                <div class="mt-4" itemscope itemtype="https://schema.org/Product">				                             							                             			<h4 class="h4"><?= !empty($this->lang->line('products')) ? $this->lang->line('products') : 'Products' ?></h4>					                                    					                                    <div class="row" id="productsListHtml-container1">					                                    					                                    	<!-- Products HTML -->					                                    						                                    </div>					                                    				                             							                             				                             		</div>			                             					                             		</div>			                             					                             	</div>				                            						                  	<div class="gridview" id="productsGridHtml-container">	                             				<div class="col-12">																		<h4 class="h4">Products</h4>																								</div>												<div class="row" id="productsGridHtml-container1">																									<!-- Products HTML -->																								</div>					                   								                             					                    	</div>					                    						                </div>												            </div>												        </div>								                            </div>                        </div>
                    <?php } ?>
                    <?php if (isset($products) && !empty($products['product'])) { ?>

                        <?php if (isset($_GET['type']) && $_GET['type'] == "list") { ?>
                            <div class="col-md-12 col-sm-6">
                                <div class="row mt-4" itemscope itemtype="https://schema.org/Product">
                                    <div class="col-12">
                                        <h4 class="h4"><?= !empty($this->lang->line('products')) ? $this->lang->line('products') : 'Products' ?></h4>
                                    </div>
                                    <?php foreach ($products['product'] as $row) { ?>
                                        <div class="col-md-3">
                                            <div class="product-grid">
                                                <div class="product-image">
                                                    <div class="product-image-container">
                                                        <a href="<?= base_url('products/details/' . $row['slug']) ?>">
                                                            <link itemprop="image" href="<?= $row['image_sm'] ?>" />
                                                            <img class="pic-1 lazy" data-src="<?= $row['image_sm'] ?>">
                                                        </a>
                                                    </div>
                                                    <ul class="social">
                                                        <?php
                                                        if (count($row['variants']) <= 1) {
                                                            $variant_id = $row['variants'][0]['id'];
                                                            $modal = "";
                                                        } else {
                                                            $variant_id = "";
                                                            $modal = "#quick-view";
                                                        }
                                                        ?>
                                                        <li><a href="" class="quick-view-btn" data-tip="Quick View" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $row['variants'][0]['id'] ?>" data-izimodal-open="#quick-view"><i class="fa fa-search"></i></a></li>
                                                        <li><a href="" data-tip="Add to Cart" class="add_to_cart" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>" data-izimodal-open="<?= $modal ?>"><i class="fa fa-shopping-cart"></i></a></li>
                                                    </ul>
                                                    <?php if (isset($row['min_max_price']['special_price']) && $row['min_max_price']['special_price'] != '' && $row['min_max_price']['special_price'] != 0 && $row['min_max_price']['special_price'] < $row['min_max_price']['min_price']) { ?>
                                                        <span class="product-new-label"><?= !empty($this->lang->line('sale')) ? $this->lang->line('sale') : 'Sale' ?></span>
                                                        <span class="product-discount-label"><?= $row['min_max_price']['discount_in_percentage'] ?>%</span>
                                                    <?php } ?>
                                                    <aside class="add-favorite">
                                                        <button type="button" class="btn far fa-heart add-to-fav-btn <?= ($row['is_favorite'] == 1) ? 'fa text-danger' : '' ?>" data-product-id="<?= $row['id'] ?>"></button>
                                                    </aside>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="product-content">
                                                <h3 class="list-product-title title" itemprop="name"><a href="<?= base_url('products/details/' . $row['slug']) ?>"><?= $row['name'] ?></a></h3>
                                                <?php if (isset($row['rating']) && isset($row['no_of_ratings']) && !empty($row['no_of_ratings']) &&  !empty($row['rating']) && $row['no_of_ratings'] != "") { ?>
                                                    <div class="rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                                                        <meta itemprop="reviewCount" content="<?= $row['no_of_ratings'] ?>" />
                                                        <meta itemprop="ratingValue" content="<?= $row['rating'] ?>" />
                                                        <input type="text" class="kv-fa rating-loading" value="<?= $row['rating'] ?>" data-size="sm" title="" readonly>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="rating">
                                                        <input type="text" class="kv-fa rating-loading" value="<?= $row['rating'] ?>" data-size="sm" title="" readonly>
                                                    </div>
                                                <?php } ?>
                                                <p class="text-muted list-product-desc" itemprop="description"><?= (isset($row['short_description']) && !empty($row['short_description'])) ? output_escaping(str_replace('\r\n', '&#13;&#10;', $row['short_description'])) : "" ?></p>
                                                <div class="price mb-2 list-view-price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                                    <meta itemprop="price" content="<?= $row['variants'][0]['price']; ?>" />
                                                    <meta itemprop="priceCurrency" content="<?= $settings['currency'] ?>" />
                                                </div>
                                                <?php if (!empty($row['min_max_price']['special_price'])) { ?>
                                                    <?= $settings['currency'] ?></i><?= number_format($row['min_max_price']['special_price']) ?>
                                                    <span class="striped-price" itemprop="price"><?= $settings['currency'] . ' ' . number_format($row['min_max_price']['min_price']) ?></span>
                                                <?php } else { ?>
                                                    <span itemprop="price"> <?= $settings['currency'] ?></i><?= number_format($row['min_max_price']['min_price']) ?></span>
                                                <?php } ?>

                                                <div class="button button-sm m-0 p-0">
                                                    <a class="add-to-cart add_to_cart" href="" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>" data-izimodal-open="<?= $modal ?>">+ <?= !empty($this->lang->line('add_to_cart')) ? $this->lang->line('add_to_cart') : 'Add To Cart' ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="row w-100">
                                <div class="col-12">
                                    <h4 class="h4"><?= !empty($this->lang->line('products')) ? $this->lang->line('products') : 'Products' ?></h4>
                                </div>
                                <?php foreach ($products['product'] as $row) { ?>
                                    <div class="col-md-4 col-sm-6" itemscope itemtype="https://schema.org/Product">
                                        <div class="product-grid">
                                            <aside class="add-favorite">
                                                <button type="button" class="btn far fa-heart add-to-fav-btn <?= ($row['is_favorite'] == 1) ? 'fa text-danger' : '' ?>" data-product-id="<?= $row['id'] ?>"></button>
                                            </aside>
                                            <div class="product-image">
                                                <div class="product-image-container">
                                                    <a href="<?= base_url('products/details/' . $row['slug']) ?>">
                                                        <link itemprop="image" href="<?= $row['image_sm'] ?>" />
                                                        <img class="pic-1 lazy" data-src="<?= $row['image_sm'] ?>">
                                                    </a>
                                                </div>
                                                <ul class="social">
                                                    <?php
                                                    if (count($row['variants']) <= 1) {
                                                        $variant_id = $row['variants'][0]['id'];
                                                        $modal = "";
                                                    } else {
                                                        $variant_id = "";
                                                        $modal = "#quick-view";
                                                    }
                                                    ?>
                                                    <li><a href="" class="quick-view-btn" data-tip="Quick View" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $row['variants'][0]['id'] ?>" data-izimodal-open="#quick-view"><i class="fa fa-search"></i></a></li>
                                                    <li><a href="" data-tip="Add to Cart" class="add_to_cart" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>" data-izimodal-open="<?= $modal ?>"><i class="fa fa-shopping-cart"></i></a></li>
                                                </ul>
                                                <?php if (isset($row['min_max_price']['special_price']) && $row['min_max_price']['special_price'] != '' && $row['min_max_price']['special_price'] != 0 && $row['min_max_price']['special_price'] < $row['min_max_price']['min_price']) { ?>
                                                    <span class="product-new-label"><?= !empty($this->lang->line('sale')) ? $this->lang->line('sale') : 'Sale' ?></span>
                                                    <span class="product-discount-label"><?= $row['min_max_price']['discount_in_percentage'] ?>%</span>
                                                <?php } ?>
                                            </div>
                                            <div class="product-content">
                                                <?php if (isset($row['rating']) && isset($row['no_of_ratings']) && !empty($row['no_of_ratings']) &&  !empty($row['rating']) && $row['no_of_ratings'] != "") { ?>
                                                    <div class="rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                                                        <meta itemprop="reviewCount" content="<?= $row['no_of_ratings'] ?>" />
                                                        <meta itemprop="ratingValue" content="<?= $row['rating'] ?>" />
                                                        <input type="text" class="kv-fa rating-loading" itemprop="ratingValue" value="<?= $row['rating'] ?>" data-size="sm" title="" readonly>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="rating">
                                                        <input type="text" class="kv-fa rating-loading" value="<?= $row['rating'] ?>" data-size="sm" title="" readonly>
                                                    </div>
                                                <?php } ?>
                                                <div class="product-content">
                                                    <meta itemprop="description" content="<?= (isset($row['short_description']) && !empty($row['short_description'])) ? output_escaping(str_replace('\r\n', '&#13;&#10;', $row['short_description'])) : "" ?>" />
                                                    <h3 class="title" itemprop="name"><a href="<?= base_url('products/details/' . $row['slug']) ?>"><?= $row['name'] ?></a></h3>
                                                    <div itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                                        <meta itemprop="price" content="<?= $row['variants'][0]['price']; ?>" />
                                                        <meta itemprop="priceCurrency" content="<?= $settings['currency'] ?>" />
                                                    </div>
                                                    <div class="price">
                                                        <?php $price = get_price_range_of_product($row['id']);
                                                        echo $price['range'];
                                                        ?>
                                                    </div>

                                                    <a class="add-to-cart add_to_cart" href="" data-product-id="<?= $row['id'] ?>" data-product-variant-id="<?= $variant_id ?>" data-izimodal-open="<?= $modal ?>">+ <?= !empty($this->lang->line('add_to_cart')) ? $this->lang->line('add_to_cart') : 'Add To Cart' ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } ?>

                    <?php if ((!isset($sub_categories) || empty($sub_categories)) && (!isset($products) || empty($products['product']))) { ?>
                        <div class="col-12 text-center">
                            <h1 class="h2">No Products Found.</h1>
                            <a href="<?= base_url('products') ?>" class="button button-rounded button-warning"><?= !empty($this->lang->line('go_to_shop')) ? $this->lang->line('go_to_shop') : 'Go to Shop' ?></a>
                        </div>
                    <?php } ?>
                    <nav class="text-center mt-4">
                        <?= (isset($links)) ? $links : '' ?>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Mobile Filter Menu -->
        <div class="filter-nav js-filter-nav filter-nav-sm">
            <div class="filter-nav__list js-filter-nav__list">
                <h3 class="mt-0">Showing <span class="text-primary">12</span> Products</h3>
                <div class="col-md-4 order-md-1 col-lg-3">										<div id="product-filters-mobile">
                        <?php if (isset($filters) && !empty($filters)) { ?>
                            <div class="accordion" id="accordionExample">
                                <?php foreach ($filters as $key => $row) {
                                    $row_attr_name = str_replace(' ', '-', $row['name']);
                                    $attribute_name = isset($_GET[strtolower('filter-' . $row_attr_name)]) ? $this->input->get(strtolower('filter-' . $row_attr_name), true) : 'null';
                                    $selected_attributes = explode('|', $attribute_name);
                                    $attribute_values = explode(',', $row['attribute_values']);
                                    $attribute_values_id = explode(',', $row['attribute_values_id']);
                                ?>
                                    <div class="card-custom">
                                        <div class="card-header-custom" id="headingOne">
                                            <h2 class="mb-0">
                                                <a class="collapse-arrow btn btn-link collapsed" data-toggle="collapse" data-target="#m<?= $key ?>" aria-expanded="false" aria-controls="#m<?= $key ?>"><?= html_escape($row['name']) ?><i class="fa fa-angle-down rotate"></i></a>
                                            </h2>
                                        </div>
                                        <div id="m<?= $key ?>" class="collapse <?= ($attribute_name != 'null') ? 'show' : '' ?>" aria-labelledby="headingOne" data-parent="#accordionExample">
                                            <div class="card-body-custom">
                                                <?php foreach ($attribute_values as $key => $values) {
                                                    $values = strtolower($values);
                                                ?>
                                                    <div class="input-container d-flex">
                                                        <?= form_checkbox(
                                                            $values,
                                                            $values,
                                                            (in_array($values, $selected_attributes)) ? TRUE : FALSE,
                                                            array(
                                                                'class' => 'toggle-input product_attributes',
                                                                'id' => 'm' . $row_attr_name . ' ' . $values,
                                                                'data-attribute' => strtolower($row['name']),
                                                            )
                                                        ) ?>
                                                        <label class="toggle checkbox" for="<?= 'm' . $values ?>">
                                                            <div class="toggle-inner"></div>
                                                        </label>
                                                        <?= form_label($values, 'm' . $row_attr_name . ' ' . $values, array('class' => 'text-label')) ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="text-center">
                        <button class="button button-rounded button-warning product_filter_btn1" onclick="getsubCatProductFunction();"><?= !empty($this->lang->line('filter')) ? $this->lang->line('filter') : 'Filter' ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section><script>var siteurl = '<?php echo site_url();?>';		if (siteurl.indexOf("index.php") >= 0){		siteurl = siteurl + '/';}	var baseurl  = ' <?php echo base_url();?>';var tokenName = '<?php echo $this->security->get_csrf_token_name();?>';var tokenHash = '<?php echo $this->security->get_csrf_hash() ?>';var htmlnew = '<div class="swiper-slide" style="background: unset !important;">'+				    '<div class="">'+						        '<div class="category-image">'+						            '<div class="">'+						                '<a href="javascript:;" onclick="getsubCatProductFunction(<ID>);" title="<NAME>">'+						                    '<div class="btn btn-outline-danger subsubcatbtn" id="subsubCat-btn-<ID>" style="">'+						                        '<span class="cat-txt" ><NAME></span>'+						                    '</div>'+						                '</a>'+						            '</div>'+						        '</div>'+						    '</div>'+						'</div>';var html ='<div class="col-md-2 col-sm-2" id="subcat-<ID>" >'+			'<div class="category-image w-75">'+				'<div class="btn btn-outline-danger subsubcatbtn" id="subsubCat-btn-<ID>" onclick="getsubCatProductFunction(<ID>);" style="margin-bottom:10px;width: 100%;">'+					'<span><NAME></span>'+				'</div> '+						'</div>'+		'</div>';		$("#subcategory-div").slideUp();var prodGridHTML = '<div class="col-md-3 col-sm-4" itemscope itemtype="https://schema.org/Product" style="margin-top: 1vw;">'+									    '<div class="product-grid">'+								        '<aside class="add-favorite">'+								            '<button type="button" class="btn far fa-heart add-to-fav-btn <IS_FAV>" data-product-id="<ID>"></button>'+								        '</aside>'+								        '<div class="product-image">'+								            '<div class="product-image-container">'+								                '<a href="'+baseurl+'products/details/<SLUG>">'+								                    '<link itemprop="image" href="<PROD_IMAGE>" />'+								                    '<img class="pic-1 lazy" src="<PROD_IMAGE>">'+								                '</a>'+								            '</div>'+								            '<ul class="social">'+								                '<li><a href="" class="quick-view-btn" data-tip="Quick View" data-product-id="<ID>" data-product-variant-id="<VARIENT_ID>" data-izimodal-open="#quick-view"><i class="fa fa-search"></i></a></li>'+								                '<li><a href=""  onclick="addproduct(<ID>,<VARIENT_ID>);" data-tip="Add to Cart" class="add_to_cart" data-product-id="<ID>" data-product-variant-id="<VARIENT_ID>" data-izimodal-open="<MODAL>"><i class="fa fa-shopping-cart"></i></a></li>'+								            '</ul>'+								            			                '<span class="product-new-label" style="<DISPLAY1>">Sale</span>'+						                '<span class="product-discount-label" style="<DISPLAY1>"><DISCOUNT_IN_PERCENTAGE>%</span>'+				        '</div>'+								        '<div class="product-content">'+								            			                '<div class="rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating" style="<DISPLAY2>">'+						                   '<meta itemprop="reviewCount" content="<NO_OF_RATINGS>" />'+						                    '<meta itemprop="ratingValue" content="<RATING>" />'+						                    '<input type="text" class="kv-fa rating-loading" itemprop="ratingValue" value="<RATING>" data-size="sm" title="" readonly>'+						                '</div>'+								            			                '<div class="rating" style="<DISPLAY3>">'+						                    '<input type="text" class="kv-fa rating-loading" value="<RATING>" data-size="sm" title="" readonly>'+						                '</div>'+								            '<div class="product-content">'+								                '<meta itemprop="description" content="<SHORT_DESCRIPTION>" />'+								                '<h3 class="title" itemprop="name"><a href="'+baseurl+'products/details/<SLUG>"><NAME></a></h3>'+								                '<div itemprop="offers" itemscope itemtype="https://schema.org/Offer">'+								                    '<meta itemprop="price" content="<VARIANTS_PRICE>" />'+								                    '<meta itemprop="priceCurrency" content="<CURRENCY>" />'+								                '</div>'+								                '<div class="price">'+								                    '<CURRENCY> <MIN_PRICE_RANGE> - <CURRENCY> <MAX_PRICE_RANGE>'+								                '</div>'+								                '<a class="add-to-cart add_to_cart" href="" onclick="addproduct(<ID>,<VARIENT_ID>);"  data-product-id="<ID>" data-product-variant-id="<VARIENT_ID>" data-izimodal-open="<MODAL>"> Add To Cart</a>'+								            '</div>'+								        '</div>'+								    '</div>'+								'</div>';var prodListHTML = '<div class="col-md-4">'+						    '<div class="product-grid">'+										        '<div class="product-image">'+										            '<div class="product-image-container">'+										            	'<a href="'+baseurl+'products/details/<SLUG>">'+										                    '<link itemprop="image" href="<PROD_IMAGE>" />'+										                    '<img class="pic-1 lazy" src="<PROD_IMAGE>">'+										                '</a>'+										            '</div>'+										            '<ul class="social">'+										                '<li><a href="" class="quick-view-btn" data-tip="Quick View" data-product-id="<ID>" data-product-variant-id="<VARIENT_ID>" data-izimodal-open="#quick-view"><i class="fa fa-search"></i></a></li>'+										                '<li><a href="" onclick="addproduct(<ID>,<VARIENT_ID>);" data-tip="Add to Cart" class="add_to_cart" data-product-id="<ID>" data-product-variant-id="<VARIENT_ID>" data-izimodal-open="<MODAL>"><i class="fa fa-shopping-cart"></i></a></li>'+										            '</ul>'+										            					         	'<span class="product-new-label" style="<DISPLAY1>">Sale</span>'+										         	'<span class="product-discount-label" style="<DISPLAY1>"><DISCOUNT_IN_PERCENTAGE>%</span>'+										            					            '<aside class="add-favorite">'+										                '<button type="button" class="btn far fa-heart add-to-fav-btn <IS_FAV>" data-product-id="<ID>"></button>'+										            '</aside>'+										            '</span>'+										        '</div>'+										    '</div>'+										'</div>'+										'<div class="col-md-8">'+										    '<div class="product-content">'+										        '<h3 class="list-product-title title" itemprop="name"><a href="'+baseurl+'products/details/<SLUG>"><NAME></a></h3>'+										        					            '<div class="rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating" style="<DISPLAY2>">'+										                '<meta itemprop="reviewCount" content="<NO_OF_RATINGS>" />'+										                '<meta itemprop="ratingValue" content="<RATING>" />'+										                '<input type="text" class="kv-fa rating-loading" value="<RATING>" data-size="sm" title="" readonly>'+										            '</div>'+										        					            '<div class="rating" style="<DISPLAY3>">'+										                '<input type="text" class="kv-fa rating-loading" value="<RATING>" data-size="sm" title="" readonly>'+										            '</div>'+										        					        '<p class="text-muted list-product-desc" itemprop="description"><SHORT_DESCRIPTION></p>'+										        '<div class="price mb-2 list-view-price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">'+										            '<meta itemprop="price" content="<VARIANTS_PRICE>" />'+										            '<meta itemprop="priceCurrency" content="<CURRENCY>" />'+										        '</div>'+										        '<span itemprop="price"> <CURRENCY></i><MIN_PRICE_RANGE></span>'+													        '<div class="button button-sm m-0 p-0">'+										            '<a class="add-to-cart add_to_cart" href="" id="addCart_<ID>" onclick="addproduct(<ID>,<VARIENT_ID>);" data-product-id="<ID>" data-product-variant-id="<VARIENT_ID>" data-izimodal-open="<MODAL>">+ Add To Cart</a>'+										        '</div>'+										    '</div>'+										'</div>';					$( document ).ready(function() {						if(screen.width <= 500){							const swiper = new Swiper('.category-swiper', {                				slidesPerView:3,        						loop: false,                			 	pagination: {                					el: '.swiper-pagination',                				},                				navigation: {                				  nextEl: '.swiper-button-next',                				  prevEl: '.swiper-button-prev',                				},                			});						}					});function getsubCatFunction(slug){	$(".subcatbtn").removeClass('activeSubCatbtn');	$("#cat-txt-"+slug).addClass('activeSubCatbtn');	$("#subcategoryHtml-container").empty();	$.ajax({		        type: "POST",        url: siteurl+"custom/getAllSubCategories/"+slug,        data: tokenName+"="+tokenHash,        dataType : 'JSON',        success: function (data) {				          	var subCat = data.category;          	if(subCat.length > 0){          		for(var i=0; i<subCat.length; i++){    				var row = htmlnew.replace(/<ID>/g,subCat[i]['id']);        			row = row.replace(/<NAME>/g,subCat[i]['name']);        			$("#subcategoryHtml-container").append(row);        			setTimeout(function(){            			if(screen.width <= 500){            				const swiper = new Swiper('.category-swiper1', {                				slidesPerView:2,        						loop: false,                			 	pagination: {                					el: '.swiper-pagination',                				},                				navigation: {                				  nextEl: '.swiper-button-next',                				  prevEl: '.swiper-button-prev',                				},                			});                		}else{                			const swiper = new Swiper('.category-swiper1', {                				slidesPerView:5,        						loop: false,                			 	pagination: {                					el: '.swiper-pagination',                				},                				navigation: {                				  nextEl: '.swiper-button-next',                				  prevEl: '.swiper-button-prev',                				},                			});                		}        				            		}, 600);        			        		}           	}else{           		$("#subcategoryHtml-container").append('<div class="col-md-12 col-sm-12"><div style="margin-left: 1vw;"><p style="text-align: left;">Sub Category Not Found!</p></div></div>');            }          	$("#subcategory-div").slideDown(1500);          	$("#productsHtml-div").slideUp();		}    });}var anothersubCatId = '';var filterVal = [];var filterName = [];function getsubCatProductFunction(subCatId=''){	$(".subsubcatbtn").removeClass('activeSubCatbtn1');	$("#subsubCat-btn-"+subCatId).addClass('activeSubCatbtn1');		var data = new Object();	if(subCatId != ''){		data.subCatId   = subCatId;		anothersubCatId = subCatId;	}else{		data.subCatId   = anothersubCatId;	}	data.sort   = $('#product_sort_by1').val();	data.filterVal = filterVal;	data.filterName = filterName;	var temp = $.param({head: data});			$.ajax({		        type: "POST",        url: siteurl+"custom/getAllProductsSubCategories",        data: temp+"&"+tokenName+"="+tokenHash,        dataType : 'JSON',        success: function (data) {				          	var products = data.products;          	var allproducts = products['product'];          	var settings = data.settings;          	makeProductHtml(allproducts, settings);   	          	          	$("#productsHtml-div").slideDown(1500);		}    });}function makeProductHtml(allproducts, settings){	$("#productsGridHtml-container1").empty();	$("#productsListHtml-container1").empty();	if(allproducts.length > 0){      	var modal;      	var variant_id;  		for(var i=0; i<allproducts.length; i++){						var row = prodGridHTML.replace(/<ID>/g,allproducts[i]['id']);			row = row.replace(/<IS_FAV>/g,allproducts[i]['is_favorite'] == '1' ? 'fa text-danger' : '' );			row = row.replace(/<NAME>/g,allproducts[i]['name']);			row = row.replace(/<SLUG>/g,allproducts[i]['slug']);			row = row.replace(/<PROD_IMAGE>/g,allproducts[i]['image_sm']);			if (allproducts[i]['variants'].length <= 1) {                variant_id = allproducts[i]['variants'][0]['id'];                modal = "";            } else {                variant_id = "";                modal = "#quick-view";            }			row = row.replace(/<VARIENT_ID>/g, variant_id);			row = row.replace(/<MODAL>/g, modal);			if ( allproducts[i]['min_max_price']['special_price'] != undefined && allproducts[i]['min_max_price']['special_price'] != '' && allproducts[i]['min_max_price']['special_price'] != 0 && allproducts[i]['min_max_price']['special_price'] < allproducts[i]['min_max_price']['min_price']) {				row = row.replace(/<MODAL>/g, allproducts[i]['min_max_price']['discount_in_percentage']);				row = row.replace(/<DISPLAY1>/g,'');    		}else{    			row = row.replace(/<DISPLAY1>/g,'display:none;');        	}			if (allproducts[i]['rating'] != undefined && allproducts[i]['no_of_ratings'] != undefined && allproducts[i]['no_of_ratings']!='' &&  allproducts[i]['rating']!='' && allproducts[i]['no_of_ratings'] != "") {				row = row.replace(/<NO_OF_RATINGS>/g, allproducts[i]['no_of_ratings']);				row = row.replace(/<RATING>/g, allproducts[i]['rating']);				row = row.replace(/<DISPLAY2>/g,'');				row = row.replace(/<DISPLAY3>/g,'display:none;');							}else{				row = row.replace(/<RATING>/g, allproducts[i]['rating']);				row = row.replace(/<DISPLAY2>/g,'display:none;');				row = row.replace(/<DISPLAY3>/g,'');    		}			row = row.replace(/<SHORT_DESCRIPTION>/g, allproducts[i]['short_description'] != '' ? allproducts[i]['short_description'] : '');			row = row.replace(/<VARIANTS_PRICE>/g, allproducts[i]['variants'][0]['price']);			row = row.replace(/<CURRENCY>/g, settings['currency']);			row = row.replace(/<MIN_PRICE_RANGE>/g, allproducts[i]['min_max_price']['min_price']);			row = row.replace(/<MAX_PRICE_RANGE>/g, allproducts[i]['min_max_price']['max_price']);			$("#productsGridHtml-container1").append(row);						var row = prodListHTML.replace(/<ID>/g,allproducts[i]['id']);			row = row.replace(/<IS_FAV>/g,allproducts[i]['is_favorite'] == '1' ? 'fa text-danger' : '' );			row = row.replace(/<NAME>/g,allproducts[i]['name']);			row = row.replace(/<SLUG>/g,allproducts[i]['slug']);			row = row.replace(/<PROD_IMAGE>/g,allproducts[i]['image_sm']);			if (allproducts[i]['variants'].length <= 1) {                variant_id = allproducts[i]['variants'][0]['id'];                modal = "";            } else {                variant_id = '';                modal = "#quick-view";            }			row = row.replace(/<VARIENT_ID>/g, variant_id);			row = row.replace(/<MODAL>/g, modal);			if ( allproducts[i]['min_max_price']['special_price'] != undefined && allproducts[i]['min_max_price']['special_price'] != '' && allproducts[i]['min_max_price']['special_price'] != 0 && allproducts[i]['min_max_price']['special_price'] < allproducts[i]['min_max_price']['min_price']) {				row = row.replace(/<MODAL>/g, allproducts[i]['min_max_price']['discount_in_percentage']);				row = row.replace(/<DISPLAY1>/g,'');    		}else{    			row = row.replace(/<DISPLAY1>/g,'display:none;');        	}			if (allproducts[i]['rating'] != undefined && allproducts[i]['no_of_ratings'] != undefined && allproducts[i]['no_of_ratings']!='' &&  allproducts[i]['rating']!='' && allproducts[i]['no_of_ratings'] != "") {				row = row.replace(/<NO_OF_RATINGS>/g, allproducts[i]['no_of_ratings']);				row = row.replace(/<RATING>/g, allproducts[i]['rating']);				row = row.replace(/<DISPLAY2>/g,'');				row = row.replace(/<DISPLAY3>/g,'display:none;');							}else{				row = row.replace(/<RATING>/g, allproducts[i]['rating']);				row = row.replace(/<DISPLAY2>/g,'display:none;');				row = row.replace(/<DISPLAY3>/g,'');    		}			row = row.replace(/<SHORT_DESCRIPTION>/g, allproducts[i]['short_description'] != '' ? allproducts[i]['short_description'] : '');			row = row.replace(/<VARIANTS_PRICE>/g, allproducts[i]['variants'][0]['price']);			row = row.replace(/<CURRENCY>/g, settings['currency']);			row = row.replace(/<MIN_PRICE_RANGE>/g, allproducts[i]['min_max_price']['min_price']);			row = row.replace(/<MAX_PRICE_RANGE>/g, allproducts[i]['min_max_price']['max_price']);			$("#productsListHtml-container1").append(row);					}   	}else{   		$("#productsGridHtml-container1").append('<div class="col-md-12 col-sm-12"><div style="margin-left: 1vw;"><p style="text-align: left;">Products Not Found!</p></div></div>');   		$("#productsListHtml-container1").append('<div class="col-md-12 col-sm-12"><div style="margin-left: 1vw;"><p style="text-align: left;">Products Not Found!</p></div></div>');    }	}function changeview(i){	$(".grid-view").removeClass('active');	if(i==1){		$("#productsGridHtml-container").show();		$("#productsListHtml-container").hide();		$("#product_grid_view_btn1").addClass('active');		}else if(i==2){		$("#productsGridHtml-container").hide();		$("#productsListHtml-container").show();			$("#product_list_view_btn1").addClass('active');	}	}function getFilterVal(value, name){	if($("input[name="+value+"]").is(':checked')){				filterVal.push(value);		filterName.push(name);			}else{		filterVal = jQuery.grep(filterVal, function(val) {		  return val != value;		});	}}function addproduct(id, varientId){	    var e = $('[name="qty"]').val();    $("#quick-view").data("data-product-id", $('#addCart_'+id).data("productId"));    var i = varientId,        s = $('#addCart_'+id).attr("data-min"),        a = $('#addCart_'+id).attr("data-max"),        r = $('#addCart_'+id).attr("data-step"),        n = $('#addCart_'+id),        l = $('#addCart_'+id).html(),        t = $('#addCart_'+id).attr("data-izimodal-open");        $.ajax({            type: "POST",            url: base_url + "cart/manage",            data: { product_variant_id: i, qty: e, is_saved_for_later: !1, [csrfName]: csrfHash },            dataType: "json",            beforeSend: function () {                n.html("Please Wait").text("Please Wait").attr("disabled", !0);            },            success: function (t) {                var o;                (csrfName = t.csrfName),                    (csrfHash = t.csrfHash),                    n.html(l).attr("disabled", !1),                    0 == t.error                        ? (Toast.fire({ icon: "success", title: t.message }),                          $("#cart-count").text(t.data.cart_count),                          (o = ""),                          $.each(t.data.items, function (t, e) {                              var i = void 0 !== e.product_variants.variant_values && null != e.product_variants.variant_values ? e.product_variants.variant_values : "",                                  n = e.special_price < e.price && 0 != e.special_price ? e.special_price : e.price;                              o +=                                  '<div class="row"><div class="cart-product product-sm col-md-12"><div class="product-image"><img class="pic-1" src="' +                                  base_url +                                  e.image +                                  '" alt="Not Found"></div><div class="product-details"><div class="product-title">' +                                  e.name +                                  "</div><span>" +                                  i +                                  '</span><p class="product-descriptions">' +                                  e.short_description +                                  '</p></div><div class="product-pricing d-flex py-2 px-1 w-100"><div class="product-price align-self-center">' +                                  currency +                                  " " +                                  n +                                  '</div><div class="product-sm-quantity px-1"><input type="number" class="form-input" value="' +                                  e.qty +                                  '"  data-id="' +                                  e.product_variant_id +                                  '" data-price="' +                                  n +                                  '" min="' +                                  s +                                  '" max="' +                                  a +                                  '" step="' +                                  r +                                  '" ></div><div class="product-sm-removal align-self-center"><button class="remove-product button button-danger" data-id="' +                                  e.product_variant_id +                                  '"><i class="fa fa-trash"></i></button></div><div class="product-line-price align-self-center px-1">' +                                  currency +                                  " " +                                  (e.qty * n).toLocaleString(void 0, { minimumFractionDigits: 2 }) +                                  "</div></div></div></div>";                          }),                          $("#cart-item-sidebar").html(o))                        : Toast.fire({ icon: "error", title: t.message });            },        });}</script>