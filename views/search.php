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
    $product_ids_raw = isset($_GET['product_ids']) ? trim((string)$_GET['product_ids']) : '';
    $product_ids = array();
    if ($product_ids_raw !== '') {
        $product_ids = array_values(array_filter(array_map('intval', explode(',', $product_ids_raw)), function ($id) {
            return $id > 0;
        }));
    }
    $wishlist_ids = array();
    if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
        $wishlist_user_id = (int)$_SESSION['user']['id'];
        if (isset($_SESSION['wishlist'][$wishlist_user_id]) && is_array($_SESSION['wishlist'][$wishlist_user_id])) {
            $wishlist_ids = $_SESSION['wishlist'][$wishlist_user_id];
        }
    }

    // Giá cao và thấp nhất của sản phẩm
    $min_max_price = $ProductModel->get_min_max_prices();
    $min_filter_price = 0;
    $max_filter_price = 10000000;

    if ($from_price !== null) {
        $from_price = min($from_price, 10000000);
    }
    if ($to_price !== null) {
        $to_price = min($to_price, 10000000);
    }

    if ($from_price !== null && $to_price !== null && $to_price < $from_price) {
        $to_price = $from_price;
    }

    if (!empty($product_ids)) {
        // Chế độ mua lại: chỉ hiển thị đúng các sản phẩm trong đơn đã chọn.
        $list_products = $ProductModel->select_products_by_ids_paginated($product_ids, $page, $perPage);
        $total_info = $ProductModel->count_products_by_ids($product_ids);
        $query = '';
        $category_id = 0;
        $from_price = null;
        $to_price = null;
    } else {
        $list_products = $ProductModel->search_products_advanced($query, $category_id, $from_price, $to_price, $page, $perPage);
        $total_info = $ProductModel->count_products_advanced($query, $category_id, $from_price, $to_price);
    }
    $totalProducts = (int)($total_info['total'] ?? 0);
    $numberOfPages = max(1, (int)ceil($totalProducts / $perPage));
    if ($page > $numberOfPages) {
        $page = $numberOfPages;
    }

    $list_catgories = $CategoryModel->select_all_categories();
    $is_hidden_category = function ($name) {
        $category_name = trim((string)$name);
        return $category_name === 'Chưa có danh mục' || $category_name === 'chưa có danh mục';
    };

    $search_params = array(
        'url' => 'tim-kiem',
        'query' => $query,
        'category_id' => $category_id > 0 ? $category_id : '',
        'from_price' => $from_price !== null ? $from_price : '',
        'to_price' => $to_price !== null ? $to_price : '',
        'product_ids' => $product_ids_raw,
    );

    $selected_category_name = 'Tất cả';
    if (!empty($product_ids)) {
        $selected_category_name = 'Mua lại sản phẩm';
    } else {
        foreach ($list_catgories as $cate_item) {
            if ($is_hidden_category($cate_item['name'])) {
                continue;
            }
            if ((int)$cate_item['category_id'] === (int)$category_id) {
                $selected_category_name = $cate_item['name'];
                break;
            }
        }
    }
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
                        <span>Kết quả: <?=$selected_category_name?></span>
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
                                        $cate_id = (int)$value['category_id'];
                                        $cate_name = $value['name'];
                                        if ($is_hidden_category($cate_name)) {
                                            continue;
                                        }
                                    ?>
                                    <div class="card">
                                        <div class="card-heading active">
                                            <a href="index.php?url=tim-kiem&category_id=<?=$cate_id?>&query=<?=urlencode($query)?>&from_price=<?=$from_price !== null ? $from_price : ''?>&to_price=<?=$to_price !== null ? $to_price : ''?>" >
                                                <?=$cate_name?>
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
                                                <option value="" <?=$category_id <= 0 ? 'selected' : ''?>>Tất cả</option>
                                                <?php foreach ($list_catgories as $cate_item) {
                                                    if ($is_hidden_category($cate_item['name'])) {
                                                        continue;
                                                    }
                                                ?>
                                                    <option value="<?=$cate_item['category_id']?>" <?=$category_id === (int)$cate_item['category_id'] ? 'selected' : ''?>>
                                                        <?=$cate_item['name']?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="price-input">
                                            <p class="price-label">Khoảng giá</p>
                                            <div class="price-range-box">
                                                <input type="text" name="from_price" id="minamount" value="<?=$from_price !== null ? number_format($from_price) : ''?>" placeholder="0">
                                                <span class="price-separator">đến</span>
                                                <input type="text" name="to_price" id="maxamount" value="<?=$to_price !== null ? number_format($to_price) : ''?>" placeholder="0">
                                            </div>
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
                            $is_in_wishlist = in_array((int)$product_id, $wishlist_ids, true);
                            $wishlist_form_id = 'wishlist-search-' . (int)$product_id;
                        ?>
                        <div class="col-lg-4 col-md-6 col-6-rp-mobile">
                            <div class="product__item sale">
                                <div class="product__item__pic set-bg"
                                    data-setbg="upload/<?=$image?>"
                                    onclick="window.location.href='index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>'">
                                    <!-- <div class="label sale">New</div> -->
                                    <div class="label_right sale">-<?=$discount_percentage?></div>
                                    <ul class="product__hover" onclick="event.stopPropagation();">
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
                                    <h6 class="text-truncate-1"><a href="index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>"><?=$name?></a></h6>
                                    <div class="rating">
                                        <div class="rating-stars">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <?php if (isset($_SESSION['user'])): ?>
                                            <button type="button" class="wishlist-inline-btn js-wishlist-toggle"
                                                onclick="document.getElementById('<?=$wishlist_form_id?>').requestSubmit();"
                                                title="<?=$is_in_wishlist ? 'Bỏ yêu thích' : 'Thêm yêu thích'?>">
                                                <span class="<?=$is_in_wishlist ? 'fa fa-heart' : 'icon_heart_alt'?>"
                                                    style="color: <?=$is_in_wishlist ? '#dc3545' : '#1f1f1f'?>;"></span>
                                            </button>
                                            <form id="<?=$wishlist_form_id?>" action="index.php?url=yeu-thich" method="post" class="wishlist-inline-form" style="display:none;">
                                                <input type="hidden" name="wishlist_action" value="<?=$is_in_wishlist ? 'remove' : 'add'?>">
                                                <input type="hidden" name="product_id" value="<?=$product_id?>">
                                                <input type="hidden" name="redirect_to" value="index.php?url=tim-kiem">
                                                <input type="hidden" name="ajax_wishlist" value="1">
                                            </form>
                                        <?php endif; ?>
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
    letter-spacing: normal !important;
    font-size: 14px !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.btn-filter-price:hover {
    opacity: 0.9;
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
    letter-spacing: 0;
    line-height: 1.2;
    min-width: 0;
    overflow: visible;
    white-space: nowrap;
    padding: 0 2px !important;
    box-sizing: border-box;
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

.price-label {
    margin-bottom: 6px;
}

.product__item {
    transition: transform .22s ease, box-shadow .22s ease;
}

.product__item:hover {
    transform: scale(1.03);
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
}

.product__item__text .rating {
    display: inline-flex;
    align-items: center;
    justify-content: flex-start;
    width: auto;
    gap: 12px;
}

.rating-stars {
    display: inline-flex;
    align-items: center;
    gap: 1px;
}

.wishlist-inline-btn {
    border: none;
    background: transparent;
    padding: 0;
    line-height: 1;
    font-size: 17px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
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

        minInput.addEventListener('input', function() {
            minInput.value = formatNumberInput(minInput.value);
        });

        maxInput.addEventListener('input', function() {
            maxInput.value = formatNumberInput(maxInput.value);
        });

        minInput.addEventListener('blur', function() {
            normalizeAndValidate(minInput, maxInput);
        });

        maxInput.addEventListener('blur', function() {
            normalizeAndValidate(minInput, maxInput);
        });

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

<script>
(function() {
    function showWishlistNotice(message, isError) {
        var old = document.getElementById('wishlist-inline-toast');
        if (old) old.remove();
        var wrap = document.createElement('div');
        wrap.id = 'wishlist-inline-toast';
        wrap.style.position = 'fixed';
        wrap.style.top = '88px';
        wrap.style.left = '50%';
        wrap.style.transform = 'translateX(-50%)';
        wrap.style.zIndex = '99999';
        wrap.style.minWidth = '320px';
        wrap.style.maxWidth = '90vw';
        wrap.style.padding = '12px 18px';
        wrap.style.borderRadius = '10px';
        wrap.style.boxShadow = '0 10px 24px rgba(0,0,0,.16)';
        wrap.style.textAlign = 'center';
        wrap.style.fontSize = '17px';
        wrap.style.fontWeight = '600';
        wrap.style.color = '#fff';
        wrap.style.background = isError ? 'linear-gradient(90deg,#ef4444,#dc2626)' : 'linear-gradient(90deg,#0a68ff,#2563eb)';
        wrap.textContent = message || 'Đã cập nhật danh sách yêu thích.';
        document.body.appendChild(wrap);
        setTimeout(function() {
            if (wrap && wrap.parentNode) wrap.parentNode.removeChild(wrap);
        }, 1800);
    }

    function bindWishlistAjax() {
        var forms = document.querySelectorAll('.wishlist-inline-form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var data = new URLSearchParams(new FormData(form));
                fetch(form.getAttribute('action') || 'index.php?url=yeu-thich', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    body: data.toString()
                })
                .then(function(res) { return res.json(); })
                .then(function(json) {
                    if (!json || !json.ok) {
                        if (json && json.requires_login) {
                            window.location.href = 'index.php?url=dang-nhap';
                            return;
                        }
                        showWishlistNotice((json && json.message) || 'Không thể cập nhật yêu thích.', true);
                        return;
                    }
                    var btn = form.parentElement.querySelector('.js-wishlist-toggle');
                    if (!btn) return;
                    var icon = btn.querySelector('span');
                    var actionInput = form.querySelector('input[name="wishlist_action"]');
                    if (!icon || !actionInput) return;

                    if (actionInput.value === 'add') {
                        icon.className = 'fa fa-heart';
                        icon.style.color = '#dc3545';
                        actionInput.value = 'remove';
                    } else {
                        icon.className = 'icon_heart_alt';
                        icon.style.color = '#1f1f1f';
                        actionInput.value = 'add';
                    }
                    if (typeof json.count !== 'undefined') {
                        document.querySelectorAll('.js-wishlist-count').forEach(function(el) {
                            el.textContent = json.count;
                        });
                    }
                    showWishlistNotice(json.message || 'Đã cập nhật danh sách yêu thích.', false);
                })
                .catch(function() {});
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindWishlistAjax);
    } else {
        bindWishlistAjax();
    }
})();
</script>