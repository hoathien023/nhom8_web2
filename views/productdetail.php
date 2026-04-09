<?php
   if(isset($_GET['id_sp'])) {
        $id_sp = $_GET['id_sp'];
        $id_danhmuc = $_GET['id_dm'];

        $ProductModel->update_views($id_sp);

        $product_details = $ProductModel->select_products_by_id($id_sp);
        if (!$product_details) {
            header("Location: index.php?url=cua-hang");
            exit();
        }
        $similar_product = $ProductModel->select_products_similar($id_danhmuc);
        $name_catgoty = $CategoryModel->select_name_categories();
    } 

    
?>

<?php
    extract($product_details);
    $discount_percentage = $ProductModel->discount_percentage($price, $sale_price);
    $wishlist_notice = '';
    $wishlist_is_error = false;
    $is_in_wishlist = false;

    if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
        $wishlist_user_id = (int)$_SESSION['user']['id'];
        $wishlist_ids = isset($_SESSION['wishlist'][$wishlist_user_id]) && is_array($_SESSION['wishlist'][$wishlist_user_id])
            ? $_SESSION['wishlist'][$wishlist_user_id]
            : array();
        $is_in_wishlist = in_array((int)$product_id, $wishlist_ids, true);
    }

    if (!empty($_SESSION['wishlist_success'])) {
        $wishlist_notice = $_SESSION['wishlist_success'];
        $wishlist_is_error = false;
        unset($_SESSION['wishlist_success']);
    } elseif (!empty($_SESSION['wishlist_error'])) {
        $wishlist_notice = $_SESSION['wishlist_error'];
        $wishlist_is_error = true;
        unset($_SESSION['wishlist_error']);
    }

    // Bình luận
    if(isset($_GET['id_sp'])) {
        $product_id = $_GET['id_sp'];
        $list_comments = $CommentModel->select_comments_by_id($product_id);

    }
?>
<?php
    $wishlist_form_id = 'wishlist-form-' . (int)$product_id;
?>
<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
                    <a href="index.php?url=cua-hang">Sản phẩm </a>
                    <a href="index.php?url=danh-muc-san-pham&id=<?=$id_danhmuc?>">
                        <?php foreach ($name_catgoty as $value) {
                                if($value['category_id'] == $id_danhmuc) {
                                    echo $value['name'];
                                }
                            } ?>
                    </a>
                    <span><?=$name?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Product Details Section Begin -->
<section class="product-details spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="product__details__pic">
                    <div class="product__details__pic__left product__thumb nice-scroll">
                        <a class="pt active" href="#product-1">
                            <img src="upload/<?=$image?>" alt="">
                        </a>
                        <a class="pt" href="#product-2">
                            <img src="upload/<?=$image?>" alt="">
                        </a>
                        <!-- <a class="pt" href="#product-3">
                                <img src="img/product/conan-1.jpg" alt="">
                            </a> -->

                    </div>
                    <div class="product__details__slider__content">
                        <div class="product__details__pic__slider owl-carousel">
                            <img data-hash="product-1" class="product__big__img" src="upload/<?=$image?>" alt="">
                            <img data-hash="product-2" class="product__big__img" src="upload/<?=$image?>" alt="">
                            <img data-hash="product-3" class="product__big__img" src="upload/<?=$image?>" alt="">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="product__details__text">
                    <h3><?=$name?>
                        <span>
                            Danh mục: <?php foreach ($name_catgoty as $value) {
                                    if($value['category_id'] == $id_danhmuc) {
                                        echo $value['name'];
                                    }
                                } ?>
                        </span>
                    </h3>
                    <div class="rating">
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <span>( <?=count($list_comments)?> bình luận )</span>
                    </div>
                    <div class="product__details__price">
                        <?=$ProductModel->formatted_price($sale_price); ?>
                        <span class="ml-2">
                            <?=$ProductModel->formatted_price($price); ?>
                        </span>
                        <div class="label_right ml-2"><?=$discount_percentage?></div>
                    </div>

                    <div class="short__description">
                        <?=$short_description?>
                    </div>
                    <?php if ($wishlist_notice !== ''): ?>
                        <div class="alert <?=$wishlist_is_error ? 'alert-danger' : 'alert-success'?>" style="margin-bottom:15px;">
                            <?=$wishlist_notice?>
                        </div>
                    <?php endif; ?>

                    <?php
                        $available_quantity = max(0, (int)$quantity);
                        $is_low_stock = $available_quantity > 0 && $available_quantity <= 5;
                    ?>
                    <?php if($available_quantity <= 0){?>

                    <div class="quantity">
                        <button style="border: none;" type="submit" class="btn btn-warning">
                            Hết hàng
                        </button>

                    </div>
                    <?php }else{?>

                    <div class="product__details__button">

                        <?php if(isset($_SESSION['user'])) {?>
                        <form action="index.php?url=gio-hang" method="post">
                            <div class="input-group d-flex align-items-center">
                                <span class="text-dark">Số lượng</span>
                                <div class="input-next-cart d-flex mx-4">
                                    <input type="button" value="-" class="button-minus" data-field="quantity">
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="off"
                                        step="1" min="1" max="<?=$available_quantity?>" value="1" name="product_quantity"
                                        class="quantity-field-cart js-product-qty-input" data-stock="<?=$available_quantity?>">
                                    <input type="button" value="+" class="button-plus" data-field="quantity">
                                </div>
                                <span class="<?=$is_low_stock ? 'text-danger font-weight-bold' : 'text-dark'?>">
                                    <?=$is_low_stock ? 'Chỉ còn '.$available_quantity.' sản phẩm' : $available_quantity.' sản phẩm có sẵn'?>
                                </span>

                            </div>
                            <div id="product-qty-error" class="text-danger mt-2" style="display:none; font-size:14px;"></div>

                            <input value="<?=$product_id?>" type="hidden" name="product_id">
                            <input value="<?=$_SESSION['user']['id']?>" type="hidden" name="user_id">
                            <input value="<?=$name?>" type="hidden" name="name">
                            <input value="<?=$image?>" type="hidden" name="image">
                            <input value="<?=$sale_price?>" type="hidden" name="price">
                            <!-- <input value="1" type="hidden" name="product_quantity"> -->
                            <input value="<?=$image?>" type="hidden" name="image">
                            <input type="hidden" name="redirect_to"
                                value="index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$id_danhmuc?>">

                            <div class="quantity">

                                <button name="add_to_cart" style="border: none;" type="submit"
                                    class="cart-btn btn-primary"><span class="icon_bag_alt"></span> Thêm vào
                                    giỏ</button>
                                <button name="add_to_cart" type="submit"
                                    style="background-color: #ca1515; border: none;" class="cart-btn"><span
                                        class="icon_bag_alt"></span>Mua ngay</button>
                                <button type="button" class="cart-btn js-product-wishlist-btn"
                                    onclick="document.getElementById('<?=$wishlist_form_id?>').requestSubmit();"
                                    style="background-color: <?=$is_in_wishlist ? '#dc3545' : '#f4f7fb'?>; border: none; color: <?=$is_in_wishlist ? '#ffffff' : '#1a1a1a'?>;">
                                    <span class="js-product-wishlist-icon <?=$is_in_wishlist ? 'fa fa-heart' : 'icon_heart_alt'?>"
                                        style="<?=$is_in_wishlist ? 'color:#ffffff;' : ''?>"></span>
                                    <?=$is_in_wishlist ? 'Đã thích' : 'Yêu thích'?>
                                </button>
                            </div>
                        </form>
                        <form id="<?=$wishlist_form_id?>" action="index.php?url=yeu-thich" method="post" class="product-wishlist-form" style="display:none;">
                            <input type="hidden" name="wishlist_action" value="<?=$is_in_wishlist ? 'remove' : 'add'?>">
                            <input type="hidden" name="product_id" value="<?=$product_id?>">
                            <input type="hidden" name="redirect_to" value="index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$id_danhmuc?>">
                            <input type="hidden" name="ajax_wishlist" value="1">
                        </form>
                        <?php }else{?>
                        <div class="input-group d-flex align-items-center">
                            <span class="text-dark">Số lượng</span>
                            <div class="input-next-cart d-flex mx-4">
                                <input type="button" value="-" class="button-minus" data-field="quantity">
                                <input type="text" readonly inputmode="numeric" pattern="[0-9]*" value="1"
                                    name="product_quantity" class="quantity-field-cart">
                                <input type="button" value="+" class="button-plus" data-field="quantity">
                            </div>
                            <span class="<?=$is_low_stock ? 'text-danger font-weight-bold' : 'text-dark'?>">
                                <?=$is_low_stock ? 'Chỉ còn '.$available_quantity.' sản phẩm' : $available_quantity.' sản phẩm có sẵn'?>
                            </span>

                        </div>
                        <div class="quantity">
                            <button name="add_to_cart" onclick="alert('Vui lòng dăng nhập để thực hiện chức năng');"
                                style="border: none;" type="submit" class="cart-btn btn-primary">
                                <span class="icon_bag_alt"></span> <a href="dang-nhap" style="color: #ffffff;">Thêm vào
                                    giỏ</a>
                            </button>
                            <button name="add_to_cart" onclick="alert('Vui lòng dăng nhập để thực hiện chức năng');"
                                type="submit" style="background-color: #ca1515; border: none;" class="cart-btn">
                                <span class="icon_bag_alt"></span> <a href="dang-nhap" style="color: #ffffff;">Mua
                                    ngay</a>
                            </button>
                            <a href="index.php?url=dang-nhap" class="cart-btn"
                                style="background-color:#f4f7fb; color:#1a1a1a; display:inline-flex; align-items:center; justify-content:center;">
                                <span class="icon_heart_alt" style="margin-right:6px;"></span>Yêu thích
                            </a>
                        </div>
                        <?php }?>
                    </div>

                    <?php }?>

                </div>
            </div>
            <div class="col-lg-12">
                <div class="product__details__tab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Mô tả</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Bình luận (
                                <?=count($list_comments)?> )</a>
                        </li>

                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tabs-1" role="tabpanel">
                            <h6>Mô tả</h6>
                            <p><?=$details?></p>
                            <p></p>
                        </div>

                        <!-- Bình luận   -->
                        <?php include_once "views/comments.php"; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="related__title">
                    <h5>SẢM PHẨM TƯƠNG TỰ</h5>
                </div>
            </div>
            <?php
                    foreach ($similar_product as $value) {
                        if(is_array($value)) {
                            extract($value);
                            $discount_percentage = $ProductModel->discount_percentage($price, $sale_price);
                        }
                    
                ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mix">
                <div class="product__item sale">
                    <div class="product__item__pic set-bg" data-setbg="upload/<?=$image?>">

                        <div class="label_right sale">-<?=$discount_percentage?></div>
                        <ul class="product__hover">
                            <li><a href="upload/<?=$image?> " class="image-popup"><span class="arrow_expand"></span></a>
                            </li>
                            <li>
                                <a href="index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>"><span
                                        class="icon_search_alt"></span></a>
                            </li>

                            <li>
                                <form action="blog.html" method="post">
                                    <input type="hidden" name="product_id">
                                    <input type="hidden" name="user_id">
                                    <input type="hidden" name="name">
                                    <input type="hidden" name="price">
                                    <input type="hidden" name="quantity">
                                    <input type="hidden" name="image">
                                    <button type="submit" name="add_to_cart">
                                        <a href="#"><span class="icon_bag_alt"></span></a>
                                    </button>
                                </form>
                            </li>

                        </ul>

                    </div>
                    <div class="product__item__text">
                        <h6 class="text-truncate-1">
                            <a href="index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>">
                                <?=$name?>
                            </a>
                        </h6>
                        <div class="rating">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                        </div>
                        <div class="product__price"><?=$ProductModel->formatted_price($sale_price); ?>
                            <span><?=$ProductModel->formatted_price($price); ?> </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                    }
                ?>




        </div>
    </div>
    </div>
</section>
<!-- Product Details Section End -->


<style>
.label_right {
    font-size: 14px;
    color: #ffffff;
    font-weight: 700;
    display: inline-block;
    padding: 2px 8px;
    text-transform: uppercase;
    background: #ca1515;
    border-radius: 5px;
}
</style>

<script>
(function() {
    function bindProductQtyValidation() {
        var input = document.querySelector('.js-product-qty-input');
        if (!input) return;
        var form = input.closest('form');
        var errorEl = document.getElementById('product-qty-error');
        var stock = Number(input.getAttribute('data-stock') || input.getAttribute('max') || 0);

        function showQtyError(message) {
            if (!errorEl) return;
            errorEl.textContent = message || '';
            errorEl.style.display = message ? 'block' : 'none';
        }

        function sanitizeValue() {
            var raw = String(input.value || '');
            var digitsOnly = raw.replace(/[^\d]/g, '');
            if (raw !== digitsOnly) {
                input.value = digitsOnly;
            }
        }

        function normalizeValue(allowEmpty) {
            sanitizeValue();
            var current = String(input.value || '');
            if (current === '') {
                if (!allowEmpty) {
                    input.value = '1';
                }
                return;
            }
            var value = Number(current);
            if (!Number.isFinite(value) || value < 1) {
                input.value = '1';
                showQtyError('Số lượng phải lớn hơn 0.');
                return;
            }
            if (value > stock) {
                input.value = String(stock);
                showQtyError('Chỉ còn ' + stock + ' sản phẩm. Đã tự điều chỉnh về ' + stock + '.');
                return;
            }
            showQtyError('');
        }

        input.addEventListener('input', function() {
            sanitizeValue();
            if (String(input.value || '') === '') {
                showQtyError('Số lượng chỉ được nhập số.');
                return;
            }
            normalizeValue(true);
        });

        input.addEventListener('blur', function() {
            normalizeValue(false);
        });

        input.addEventListener('keydown', function(e) {
            if (['e', 'E', '+', '-', '.', ','].indexOf(e.key) >= 0) {
                e.preventDefault();
            }
        });

        if (form) {
            form.addEventListener('submit', function(e) {
                normalizeValue(false);
                var value = Number(input.value || 0);
                if (!Number.isFinite(value) || value < 1 || value > stock) {
                    e.preventDefault();
                    showQtyError('Số lượng không hợp lệ. Vui lòng nhập từ 1 đến ' + stock + '.');
                }
            });
        }
    }

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

    function bindProductWishlistAjax() {
        var form = document.querySelector('.product-wishlist-form');
        var button = document.querySelector('.js-product-wishlist-btn');
        var icon = document.querySelector('.js-product-wishlist-icon');
        if (!form || !button || !icon) return;

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

                var actionInput = form.querySelector('input[name="wishlist_action"]');
                if (!actionInput) return;
                if (actionInput.value === 'add') {
                    actionInput.value = 'remove';
                    button.style.backgroundColor = '#dc3545';
                    button.style.color = '#ffffff';
                    icon.className = 'js-product-wishlist-icon fa fa-heart';
                    icon.style.color = '#ffffff';
                    button.innerHTML = '<span class="js-product-wishlist-icon fa fa-heart" style="color:#ffffff;"></span> Đã thích';
                } else {
                    actionInput.value = 'add';
                    button.style.backgroundColor = '#f4f7fb';
                    button.style.color = '#1a1a1a';
                    button.innerHTML = '<span class="js-product-wishlist-icon icon_heart_alt"></span> Yêu thích';
                }

                if (typeof json.count !== 'undefined') {
                    document.querySelectorAll('.js-wishlist-count').forEach(function(el) {
                        el.textContent = json.count;
                    });
                }
                showWishlistNotice(json.message || 'Đã cập nhật danh sách yêu thích.', false);
            })
            .catch(function() {
                showWishlistNotice('Không thể cập nhật yêu thích.', true);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindProductQtyValidation);
        document.addEventListener('DOMContentLoaded', bindProductWishlistAjax);
    } else {
        bindProductQtyValidation();
        bindProductWishlistAjax();
    }
})();
</script>