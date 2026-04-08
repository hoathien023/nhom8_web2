<?php
    $parse_price_input = function ($value) {
        if (!isset($value) || $value === '') {
            return null;
        }
        $normalized = preg_replace('/[^\d]/', '', (string)$value);
        if ($normalized === '') {
            return null;
        }
        return (int)$normalized;
    };

    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = 9;

    $query = isset($_GET['query']) ? trim($_GET['query']) : '';
    $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
    $from_price = $parse_price_input($_GET['from_price'] ?? '');
    $to_price = $parse_price_input($_GET['to_price'] ?? '');

    // Giá cao và thấp nhất của sản phẩm
    $min_max_price = $ProductModel->get_min_max_prices();
    $min_filter_price = (int)($min_max_price['min_price'] ?? 0);
    $max_filter_price = min((int)($min_max_price['max_price'] ?? 100000000), 100000000);

    if ($from_price !== null) {
        $from_price = min($from_price, 100000000);
    }
    if ($to_price !== null) {
        $to_price = min($to_price, 100000000);
    }

    if ($from_price !== null && $to_price !== null && $from_price > $to_price) {
        $tmp = $from_price;
        $from_price = $to_price;
        $to_price = $tmp;
    }

    $list_products = $ProductModel->search_products_advanced($query, $category_id, $from_price, $to_price, $page, $perPage);
    $total_info = $ProductModel->count_products_advanced($query, $category_id, $from_price, $to_price);
    $totalProducts = (int)($total_info['total'] ?? 0);
    $numberOfPages = max(1, (int)ceil($totalProducts / $perPage));
    if ($page > $numberOfPages) {
        $page = $numberOfPages;
    }

    $list_catgories = $CategoryModel->select_all_categories();

    $search_params = array(
        'url' => 'tim-kiem',
        'query' => $query,
        'category_id' => $category_id > 0 ? $category_id : '',
        'from_price' => $from_price !== null ? $from_price : '',
        'to_price' => $to_price !== null ? $to_price : '',
    );
?>

<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
                        <a href="index.php?url=cua-hang">
                            Tìm kiếm sản phẩm
                        </a>
                        <span>Kết quả tìm kiếm sản phẩm</span>
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
                                        extract($value);
                                    ?>
                                    <div class="card">
                                        <div class="card-heading active">
                                            <a href="index.php?url=tim-kiem&category_id=<?=$category_id?>&query=<?=urlencode($query)?>&from_price=<?=$from_price !== null ? $from_price : ''?>&to_price=<?=$to_price !== null ? $to_price : ''?>" >
                                                <?=$name?>
                                            </a>
                                        </div>
                                        
                                    </div>
                                    <?php 
                                    }
                                    ?>
                                
                                    
                                </div>
                            </div>
                        </div>
                        <div class="sidebar__filter">
                            <div class="section-title"><h4>TÌM NÂNG CAO</h4></div>
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
                                                <option value="">Tất cả</option>
                                                <?php foreach ($list_catgories as $cate_item) { ?>
                                                    <option value="<?=$cate_item['category_id']?>" <?=$category_id === (int)$cate_item['category_id'] ? 'selected' : ''?>>
                                                        <?=$cate_item['name']?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="price-input">
                                            <p class="price-label">Khoảng giá</p>
                                            <div class="price-range-box">
                                                <input type="text" name="from_price" id="minamount_display" value="<?=$from_price !== null ? number_format($from_price) : ''?>" placeholder="0">
                                                <span class="price-separator">đến</span>
                                                <input type="text" name="to_price" id="maxamount_display" value="<?=$to_price !== null ? number_format($to_price) : ''?>" placeholder="0">
                                            </div>
                                            <input type="hidden" id="minamount" value="<?=$from_price !== null ? $from_price : $min_filter_price?>">
                                            <input type="hidden" id="maxamount" value="<?=$to_price !== null ? $to_price : $max_filter_price?>">
                                            <input type="submit" class="filter-price btn-filter-price" value="LỌC KẾT QUẢ">
                                        </div>
                                    </form>
        
                                </div>
                            </div>
                            <!-- <a href="#">LỌC GIÁ</a> -->
                        </div>
                        
                    </div>
                </div>
                <?php if(is_array($list_products) && count($list_products) >0) {?>
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
                        
                        
                    </div>
                </div>
                <?php }else {?>
                <div class="col-lg-9 col-md-9">
                    <div class="container-fluid mt-5">
                        <div class="row rounded justify-content-center mx-0 pt-5">
                            <div class="col-md-6 text-center">
                                <h4 class="mb-4">Không tìm thấy kết quả</h4>
                                <form action="index.php" method="get">
                                    <input type="hidden" name="url" value="tim-kiem">
                                    <div class="form-outline">
                                        <input type="search" name="query" class="form-control" placeholder="Tìm kiếm" />
                                    </div>
                                </form>
                                <a class="btn btn-primary rounded-pill py-3 px-5 mt-5" href="index.php?url=cua-hang">Trở lại cửa hàng</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php }?>

                <?php if($totalProducts > 0) {
                    $baseParams = $search_params;
                    unset($baseParams['page']);
                    $baseQuery = http_build_query($baseParams);
                    $pagination_prev = '';
                    $pagination_next = '';
                    $html_pagination = '';

                    if ($page > 1) {
                        $pagination_prev = '<a href="index.php?' . $baseQuery . '&page=' . ($page - 1) . '"><i class="fa fa-angle-left"></i></a>';
                    }
                    if ($page < $numberOfPages) {
                        $pagination_next = '<a href="index.php?' . $baseQuery . '&page=' . ($page + 1) . '"><i class="fa fa-angle-right"></i></a>';
                    }

                    for ($i = 1; $i <= $numberOfPages; $i++) {
                        $active = $i === $page ? 'active' : '';
                        $html_pagination .= '<a class="' . $active . '" href="index.php?' . $baseQuery . '&page=' . $i . '">' . $i . '</a>';
                    }
                ?>
                <div class="col-lg-12 text-center mt-3">
                    <div class="pagination__option">
                        <?=$pagination_prev?>
                        <?=$html_pagination?>
                        <?=$pagination_next?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <!-- Shop Section End -->

<style>
.btn-filter-price {
    width: 100%;
    max-width: 210px;
    margin: 12px auto 0;
    background: #0a68ff;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 10px 12px;
    font-weight: 600;
    text-align: center;
    display: block;
}

.btn-filter-price:hover {
    opacity: 0.9;
}

.price-range-box {
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #e1e1e1;
    border-radius: 6px;
    padding: 8px;
    margin-top: 6px;
}

.price-range-box input {
    width: calc(50% - 20px);
    border: none;
    outline: none;
    background: transparent;
    text-align: center;
}

.price-separator {
    color: #555;
    font-weight: 500;
    white-space: nowrap;
}

.price-label {
    margin-bottom: 6px;
}
</style>

<script>
(function() {
    function formatNumberInput(value) {
        var digits = String(value || '').replace(/[^\d]/g, '');
        if (!digits) return '';
        var numberValue = Math.min(parseInt(digits, 10), 100000000);
        return numberValue.toLocaleString('en-US');
    }

    function normalizePriceInputs() {
        var minInput = document.getElementById('minamount');
        var maxInput = document.getElementById('maxamount');
        var minDisplay = document.getElementById('minamount_display');
        var maxDisplay = document.getElementById('maxamount_display');
        if (!minInput || !maxInput || !minDisplay || !maxDisplay) return;

        function digitsOnly(value) {
            var digits = String(value || '').replace(/[^\d]/g, '');
            if (!digits) return '';
            return String(Math.min(parseInt(digits, 10), 100000000));
        }

        function syncFromSliderToDisplay() {
            minDisplay.value = formatNumberInput(minInput.value);
            maxDisplay.value = formatNumberInput(maxInput.value);
        }

        function syncFromDisplayToSlider() {
            minInput.value = digitsOnly(minDisplay.value);
            maxInput.value = digitsOnly(maxDisplay.value);
            minDisplay.value = formatNumberInput(minDisplay.value);
            maxDisplay.value = formatNumberInput(maxDisplay.value);
        }

        syncFromSliderToDisplay();

        minDisplay.addEventListener('input', function() {
            syncFromDisplayToSlider();
        });

        maxDisplay.addEventListener('input', function() {
            syncFromDisplayToSlider();
        });

        // Khi kéo slider script cũ sẽ ghi vào hidden inputs -> đồng bộ lại ra ô hiển thị.
        setInterval(syncFromSliderToDisplay, 200);

        var form = minDisplay.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                syncFromDisplayToSlider();
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