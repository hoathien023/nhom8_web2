<?php
    $parse_price_input = function ($value) {
        if (!isset($value) || $value === '') return null;
        $normalized = preg_replace('/[^\d]/', '', (string)$value);
        if ($normalized === '') return null;
        return (int)$normalized;
    };

    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }

    $list_products = $ProductModel->select_list_products($page, 9);
    $list_catgories = $CategoryModel->select_all_categories();
    $is_hidden_category = function ($name) {
        $category_name = trim((string)$name);
        return $category_name === 'Chưa có danh mục' || $category_name === 'chưa có danh mục';
    };
    $min_max_price = $ProductModel->get_min_max_prices();
    $min_filter_price = 0;
    $max_filter_price = 10000000;
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';
    $selected_category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
    $from_price = $parse_price_input($_GET['from_price'] ?? '');
    $to_price = $parse_price_input($_GET['to_price'] ?? '');


?>

<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
                        <span>Sản Phẩm</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Shop Section Begin -->
    <section class="shop spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-3">
                    <div class="shop__sidebar">
                        <div class="sidebar__categories">
                            <div class="section-title">
                                <h4>DANH MỤC</h4>
                            </div>
                            <div class="categories__accordion">
                                <div class="accordion" id="accordionExample">
                                    <?php foreach ($list_catgories as $value) {
                                        if ($is_hidden_category($value['name'])) {
                                            continue;
                                        }
                                        extract($value);
                                    ?>
                                    <div class="card">
                                        <div class="card-heading active">
                                            <a href="index.php?url=danh-muc-san-pham&id=<?=$category_id?>" ><?=$name?></a>
                                        </div>
                                        
                                    </div>
                                    <?php 
                                    }
                                    ?>
                                
                                    
                                </div>
                            </div>
                        </div>
                        <div class="sidebar__filter">
                            <div class="section-title">
                                <h4>TÌM NÂNG CAO</h4>
                            </div>
                            <div class="filter-range-wrap">
                                <div class="price-range ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content"
                                data-min="<?=$min_filter_price?>" data-max="<?=$max_filter_price?>"></div>
                                <div class="range-slider">
                                    <form action="index.php" method="get">
                                        <input type="hidden" name="url" value="tim-kiem">
                                        <div class="price-input mb-2">
                                            <p>Tên sản phẩm:</p>
                                            <input type="text" name="query" value="<?=htmlspecialchars($query)?>">
                                        </div>
                                        <div class="price-input mb-2">
                                            <p>Danh mục:</p>
                                            <select name="category_id" class="form-control">
                                                <option value="" <?=$selected_category_id <= 0 ? 'selected' : ''?>>Tất cả</option>
                                                <?php foreach ($list_catgories as $cate_item) {
                                                    if ($is_hidden_category($cate_item['name'])) {
                                                        continue;
                                                    }
                                                ?>
                                                    <option value="<?=$cate_item['category_id']?>" <?=$selected_category_id === (int)$cate_item['category_id'] ? 'selected' : ''?>><?=$cate_item['name']?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        
                                        <div class="price-input">
                                            <p>Khoảng giá</p>
                                            <div class="price-range-box">
                                                <input type="text" id="minamount" name="from_price" value="<?=$from_price !== null ? number_format($from_price) : ''?>" placeholder="<?=number_format($min_filter_price)?>">
                                                <span class="price-separator">đến</span>
                                                <input type="text" id="maxamount" name="to_price" value="<?=$to_price !== null ? number_format($to_price) : ''?>" placeholder="<?=number_format($max_filter_price)?>">
                                            </div>
                                            <input type="submit" class="filter-price btn-filter-price" value="LỌC GIÁ">
                                        </div>
                                    </form>
        
                                </div>
                            </div>
                            <!-- <a href="#">LỌC GIÁ</a> -->
                        </div>
                        
                        
                    </div>
                </div>
                <div class="col-lg-9 col-md-9">
                    <div class="row">
                        <?php foreach ($list_products as $value) {
                            extract($value);
                            $discount_percentage = $ProductModel->discount_percentage($price, $sale_price);
                        ?>
                        <div class="col-lg-4 col-md-6 col-6-rp-mobile">
                            <div class="product__item sale">
                                <div class="product__item__pic set-bg" data-setbg="upload/<?=$image?>">
                                    <!-- <div class="label sale">New</div> -->
                                    <div class="label_right sale">-<?=$discount_percentage?></div>
                                    <ul class="product__hover">
                                        <li><a href="upload/<?=$image?>" class="image-popup"><span class="arrow_expand"></span></a></li>
                                        <li>
                                            <a href="index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>"><span class="icon_search_alt"></span></a>
                                        </li>
                                        
                                        
                                        <li>
                                        <?php if(isset($_SESSION['user'])) {?>
                                            <form action="index.php?url=gio-hang" method="post">
                                                <input value="<?=$product_id?>" type="hidden" name="product_id">
                                                <input value="<?=$_SESSION['user']['id']?>" type="hidden" name="user_id">
                                                <input value="<?=$name?>" type="hidden" name="name">
                                                <input value="<?=$image?>"type="hidden" name="image">
                                                <input value="<?=$sale_price?>" type="hidden" name="price">
                                                <input value="1" type="hidden" name="product_quantity">
                                                <input value="<?=$image?>" type="hidden" name="image">

                                                <button type="submit" name="add_to_cart" id="toastr-success-top-right">
                                                    <a href="#" ><span class="icon_bag_alt"></span></a>
                                                </button>
                                            </form>
                                        <?php }else{?>
                                            <button type="submit" onclick="alert('Vui lòng dăng nhập để thực hiện chức năng');" name="add_to_cart" id="toastr-success-top-right">
                                                <a href="dang-nhap" ><span class="icon_bag_alt"></span></a>
                                            </button>
                                        <?php }?>
                                        </li>
                                        
                                    </ul>
                                    
                                </div>
                                <div class="product__item__text">
                                    <h6 class="text-truncate-1"><a href="product-details.html"><?=$name?></a></h6>
                                    <div class="rating">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="product__price"><?=number_format($sale_price)."₫"?> <span><?=number_format($price)."đ"?> </span></div>
                                </div>
                            </div>
                        </div>
                        <?php 
                        }
                        ?>

                        <?php
                            // Phân trang
                            $qty_product = $ProductModel->count_products();
                            $totalProducts = (int)($qty_product['total'] ?? 0); // Tổng số sản phẩm
                            $productsPerPage = 9; // sản phẩm trên 1 trang

                            // Tính số trang
                            $productsPerPage = intval($productsPerPage);
                            $numberOfPages = ceil($totalProducts / $productsPerPage);

                            $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;

                            $html_pagination = '';
                            $pagination_next = '';
                            $pagination_prev = '';
                            for ($i = 1; $i <= $numberOfPages; $i++) {
                                if ($i === $currentPage) {
                                    $active = 'active';
                                } else {
                                    $active = '';
                                }

                                $html_pagination .= '
                                    <a class="' . $active . '" href="index.php?url=cua-hang&page=' . $i . '">' . $i . '</a>
                                ';

                                //  Next
                                if ($currentPage < $numberOfPages) {
                                    $pagination_next = '
                                        <a href="index.php?url=cua-hang&page=' . ($currentPage + 1) . '"><i class="fa fa-angle-right"></i></a>
                                    ';
                                }

                                //  Prev
                                if ($currentPage > 1) {
                                    $pagination_prev = '
                                        <a href="index.php?url=cua-hang&page=' . ($currentPage - 1) . '"><i class="fa fa-angle-left"></i></a>
                                    ';
                                }
                            }
                        ?>
                        
                        <div class="col-lg-12 text-center">
                            <div class="pagination__option">
                                <?=$pagination_prev?>
                                <?=$html_pagination?>
                                <?=$pagination_next?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Shop Section End -->

<style>
.btn-filter-price {
    width: 100% !important;
    max-width: 100% !important;
    margin: 12px auto 0;
    background: #0a68ff;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 10px 12px !important;
    font-weight: 600;
    text-align: center;
    display: block;
    position: static !important;
    float: none !important;
    font-size: 14px !important;
}

.price-range-box {
    display: flex;
    align-items: center;
    gap: 4px;
    border: 1px solid #e1e1e1;
    border-radius: 6px;
    padding: 8px 10px;
    margin-top: 6px;
}

.price-range-box input {
    width: calc((100% - 42px) / 2);
    border: none;
    outline: none;
    background: transparent;
    text-align: center;
    font-size: 12px !important;
    font-weight: 500;
    line-height: 1.2;
    padding: 0 2px !important;
    overflow: visible;
    white-space: nowrap;
    box-sizing: border-box;
    min-width: 0;
}

.price-separator {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    flex: 0 0 34px;
    margin: 0;
    text-align: center;
    transform: none;
    color: #555;
    font-weight: 500;
    white-space: nowrap;
    font-size: 13px;
}

#maxamount {
    text-align: center;
    padding-left: 0;
}
</style>

<script>
(function() {
    function formatNumberInput(value) {
        var digits = String(value || '').replace(/[^\d]/g, '');
        if (!digits) return '';
        var numberValue = Math.min(parseInt(digits, 10), 10000000);
        return numberValue.toLocaleString('en-US');
    }

    function toNumber(value) {
        var digits = String(value || '').replace(/[^\d]/g, '');
        if (!digits) return '';
        return String(Math.min(parseInt(digits, 10), 10000000));
    }

    function normalizeAndValidate(minInput, maxInput) {
        var minVal = toNumber(minInput.value);
        var maxVal = toNumber(maxInput.value);
        if (minVal !== '' && maxVal !== '' && parseInt(maxVal, 10) < parseInt(minVal, 10)) {
            maxVal = minVal;
        }
        minInput.value = minVal === '' ? '' : Number(minVal).toLocaleString('en-US');
        maxInput.value = maxVal === '' ? '' : Number(maxVal).toLocaleString('en-US');
    }

    function normalizePriceInputs() {
        var minInput = document.getElementById('minamount');
        var maxInput = document.getElementById('maxamount');
        if (!minInput || !maxInput) return;

        minInput.value = formatNumberInput(minInput.value);
        maxInput.value = formatNumberInput(maxInput.value);

        minInput.addEventListener('input', function() { minInput.value = formatNumberInput(minInput.value); });
        maxInput.addEventListener('input', function() { maxInput.value = formatNumberInput(maxInput.value); });
        minInput.addEventListener('blur', function() { normalizeAndValidate(minInput, maxInput); });
        maxInput.addEventListener('blur', function() { normalizeAndValidate(minInput, maxInput); });

        var form = minInput.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                normalizeAndValidate(minInput, maxInput);
                minInput.value = toNumber(minInput.value);
                maxInput.value = toNumber(maxInput.value);
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', normalizePriceInputs);
    } else {
        normalizePriceInputs();
    }
})();
</script>