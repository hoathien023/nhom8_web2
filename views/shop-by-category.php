<?php
    if(isset($_GET['id']) && $_GET['id'] > 0) {
        $category_id = $_GET['id'];
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $productsPerPage = 9;
        $list_products = $ProductModel->select_products_by_cate_paginated($category_id, $page, $productsPerPage);
        $total_by_category = $ProductModel->count_products_by_cate($category_id);
        $totalProducts = (int)($total_by_category['total'] ?? 0);
        $numberOfPages = max(1, (int)ceil($totalProducts / $productsPerPage));
    }else {
        header("Location: index.php");
    }

    $list_catgories = $CategoryModel->select_all_categories();
    $min_max_price = $ProductModel->get_min_max_prices();
    $min_filter_price = 0;
    $max_filter_price = 10000000;
?>

<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
                        <a href="index.php?url=cua-hang">
                            Sản phẩm
                        </a>
                        <span>
                            <?php foreach ($list_catgories as $value) {
                                if($value['category_id'] == $category_id) {
                                    echo $value['name'];
                                }
                            } ?>
                        </span>
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
                                            <a href="index.php?url=danh-muc-san-pham&id=<?=$category_id?>" >
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
                            <div class="section-title">
                                <h4>TÌM NÂNG CAO</h4>
                            </div>
                            <div class="filter-range-wrap">
                                <div class="price-range ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content"
                                data-min="<?=$min_filter_price?>" data-max="<?=$max_filter_price?>"></div>
                                <div class="range-slider">
                                    <form action="index.php" method="get">
                                        <input type="hidden" name="url" value="tim-kiem">
                                        <input type="hidden" name="category_id" value="<?=$category_id?>">
                                        <div class="price-input mb-2">
                                            <p>Tên sản phẩm:</p>
                                            <input type="text" name="query" value="">
                                        </div>
                                        
                                        <div class="price-input">
                                            <p>Khoảng giá</p>
                                            <div class="price-range-box">
                                                <input type="text" id="minamount" name="from_price" placeholder="<?=number_format($min_filter_price)?>">
                                                <span class="price-separator">đến</span>
                                                <input type="text" id="maxamount" name="to_price" placeholder="<?=number_format($max_filter_price)?>">
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
                <?php if(count($list_products) >0) {?>
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
                            $pagination_prev = '';
                            $pagination_next = '';
                            $html_pagination = '';
                            if ($page > 1) {
                                $pagination_prev = '<a href="index.php?url=danh-muc-san-pham&id=' . $category_id . '&page=' . ($page - 1) . '"><i class="fa fa-angle-left"></i></a>';
                            }
                            if ($page < $numberOfPages) {
                                $pagination_next = '<a href="index.php?url=danh-muc-san-pham&id=' . $category_id . '&page=' . ($page + 1) . '"><i class="fa fa-angle-right"></i></a>';
                            }

                            for ($i = 1; $i <= $numberOfPages; $i++) {
                                $active = $i === $page ? 'active' : '';
                                $html_pagination .= '<a class="' . $active . '" href="index.php?url=danh-muc-san-pham&id=' . $category_id . '&page=' . $i . '">' . $i . '</a>';
                            }
                        ?>
                        <div class="col-lg-12 text-center mt-3">
                            <div class="pagination__option">
                                <?=$pagination_prev?>
                                <?=$html_pagination?>
                                <?=$pagination_next?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php }else {?>
                <div class="col-lg-9 col-md-9">
                    <div class="container-fluid mt-5">
                        <div class="row rounded justify-content-center mx-0 pt-5">
                            <div class="col-md-6 text-center">
                                <h4 class="mb-4">Danh mục chưa có sản phẩm</h4>
                                <a class="btn btn-primary rounded-pill py-3 px-5" href="index.php?url=cua-hang">Trở lại cửa hàng</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php }?>
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
    gap: 8px;
    border: 1px solid #e1e1e1;
    border-radius: 6px;
    padding: 8px;
    margin-top: 6px;
}

.price-range-box input {
    width: calc(50% - 18px);
    border: none;
    outline: none;
    background: transparent;
    text-align: center;
    font-size: 14px;
    font-weight: 500;
    padding: 0;
    box-sizing: border-box;
}

.price-separator {
    color: #555;
    font-weight: 500;
    white-space: nowrap;
    font-size: 13px;
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