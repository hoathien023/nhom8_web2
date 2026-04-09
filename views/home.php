<?php
    $listProducts = $ProductModel->select_products_limit(8);

    $listCategories = $CategoryModel->select_categories_limit(8);

    $product_limit_3 = $ProductModel->select_products_limit(3);
    $product_order_by = $ProductModel->select_products_order_by(3, 'ASC');
    $wishlist_ids = array();
    if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
        $wishlist_user_id = (int)$_SESSION['user']['id'];
        if (isset($_SESSION['wishlist'][$wishlist_user_id]) && is_array($_SESSION['wishlist'][$wishlist_user_id])) {
            $wishlist_ids = $_SESSION['wishlist'][$wishlist_user_id];
        }
    }
?>

<!-- Banner Section Begin -->
<section class="container my-3">
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div id="header-carousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner" style="border-radius: 10px;">
                    <div class="carousel-item active">
                        <img class="img-fluid" src="upload/banner/banner-traicay-1.jpg" alt="Image">

                    </div>
                    <div class="carousel-item">
                        <img class="img-fluid" src="upload/banner/banner-traicay-2.jpg" alt="Image">

                    </div>
                    <div class="carousel-item">
                        <img class="img-fluid" src="upload/banner/banner-traicay-3.jpg" alt="Image">

                    </div>
                </div>
                <a class="carousel-control-prev" href="#header-carousel" data-slide="prev">
                    <div class="btn btn-dark" style="width: 45px; height: 45px;">
                        <span class="carousel-control-prev-icon mb-n2"></span>
                    </div>
                </a>
                <a class="carousel-control-next" href="#header-carousel" data-slide="next">
                    <div class="btn btn-dark" style="width: 45px; height: 45px;">
                        <span class="carousel-control-next-icon mb-n2"></span>
                    </div>
                </a>

            </div>
        </div>
        <!-- <div class="col-lg-4">
                <div class="product-offer" >
                    <img class="img-fluid"src="upload/banner_quanao_main4.png" alt="">
                    
                </div>
                <div class="product-offer">
                    <img class="img-fluid" src="upload/banner_quanao_main5.png" alt="">
                    
                </div>
            </div> -->
    </div>
</section>
<!-- Banner Section End -->


<!-- Product Section Begin -->
<section class="product spad" style="background-color: #F4F4F9;">

    <!-- CATER -->
    <section class="container cate-home" style="background-color: #ffffff; border-radius: 10px;">

        <div class="section-title pt-2" style="margin-bottom: 30px;">
            <h4>Danh mục sản phẩm</h4>
        </div>

        <div class="row g-1 mb-4 mt-2 pb-4">
            <?php foreach ($listCategories as $value) {
                extract($value);
                $link = 'index.php?url=danh-muc-san-pham&id=' .$category_id;
            ?>
            <div class="col-lg-2 col-md-3 col-sm-6 text-center p-1 cate-gory">
                <a href="<?=$link?>"><img style="width: 50%;" src="upload/<?=$image?>" alt=""></a>
                <div class="mt-2">
                    <a class="cate-name text-dark" href="<?=$link?>"><?=$name?></a>
                </div>
            </div>

            <?php
            }
            ?>


        </div>
    </section>
    <!-- CATE END-->


    <div class="container" style="background-color: #ffffff; border-radius: 10px;">

        <div class="row pt-3">
            <div class="col-lg-4 col-md-4">
                <div class="section-title">
                    <h4>Sản phẩm</h4>
                </div>
            </div>

        </div>
        <div class="row property__gallery">
            <?php foreach ($listProducts as $product) {
                extract($product);

                $discount_percentage = $ProductModel->discount_percentage($price, $sale_price);
                $is_in_wishlist = in_array((int)$product_id, $wishlist_ids, true);
                $wishlist_form_id = 'wishlist-home-' . (int)$product_id;
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mix sach-1">
                <div class="product__item sale">
                    <div class="product__item__pic set-bg"
                        data-setbg="upload/<?=$image?>"
                        onclick="window.location.href='index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>'">
                        <!-- <div class="label sale">Sale</div> -->
                        <div class="label_right sale">-<?=$discount_percentage?></div>
                        <ul class="product__hover" onclick="event.stopPropagation();">
                            <li><a href="upload/<?=$image?>" class="image-popup"><span class="arrow_expand"></span></a>
                            </li>
                            <li>
                                <a href="index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>">
                                    <span class="icon_search_alt"></span>
                                </a>
                            </li>


                            <li>
                                <?php if(isset($_SESSION['user'])) {?>
                                <form action="index.php?url=gio-hang" method="post">
                                    <input value="<?=$product_id?>" type="hidden" name="product_id">
                                    <input value="<?=$_SESSION['user']['id']?>" type="hidden" name="user_id">
                                    <input value="<?=$name?>" type="hidden" name="name">
                                    <input value="<?=$image?>" type="hidden" name="image">
                                    <input value="<?=$sale_price?>" type="hidden" name="price">
                                    <input value="1" type="hidden" name="product_quantity">
                                    <input value="<?=$image?>" type="hidden" name="image">

                                    <button type="submit" name="add_to_cart" id="toastr-success-top-right">
                                        <a href="#"><span class="icon_bag_alt"></span></a>
                                    </button>
                                </form>
                                <?php }else{?>
                                <button type="submit" onclick="alert('Vui lòng dăng nhập để thực hiện chức năng');"
                                    name="add_to_cart" id="toastr-success-top-right">
                                    <a href="dang-nhap"><span class="icon_bag_alt"></span></a>
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
                            <form id="<?=$wishlist_form_id?>" action="index.php?url=yeu-thich" method="post"
                                class="wishlist-inline-form" style="display:none;">
                                <input type="hidden" name="wishlist_action" value="<?=$is_in_wishlist ? 'remove' : 'add'?>">
                                <input type="hidden" name="product_id" value="<?=$product_id?>">
                                <input type="hidden" name="redirect_to" value="index.php">
                                <input type="hidden" name="ajax_wishlist" value="1">
                            </form>
                            <?php endif; ?>
                        </div>
                        <div class="product__price"><?=number_format($sale_price) ."₫"?>
                            <span><?=number_format($price)."đ"?></span>
                        </div>
                    </div>
                </div>
            </div>

            <?php 
            } 
            ?>



            <div class="col-lg-12 text-center mb-4">
                <a href="index.php?url=cua-hang" class="btn btn-outline-primary">Xem tất cả</a>
            </div>
        </div>

    </div>




</section>


<!-- Banner Section Begin -->
<section class="banner set-bg" data-setbg="upload/banner/banner-traicay-2.jpg">
    <div class="container">
        <div class="row">
            <div class="col-xl-7 col-lg-8 m-auto">
                <!-- <div class="banner__slider owl-carousel">
                    <div class="banner__item">
                        <div class="banner__text">
                            <span>Bộ sưu tập</span>
                            <h1>Máy tính bảng</h1>
                            <a href="cua-hang">Mua ngay</a>
                        </div>
                    </div>
                    <div class="banner__item">
                        <div class="banner__text">
                            <span>Bộ sưu tập</span>
                            <h1>Lap top</h1>
                            <a href="cua-hang">Mua ngay</a>
                        </div>
                    </div>
                    <div class="banner__item">
                        <div class="banner__text">
                            <span>Bộ sưu tập</span>
                            <h1>Tivi</h1>
                            <a href="cua-hang">Mua ngay</a>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->

<!-- Trend Section Begin -->
<section class="trend spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="trend__content">
                    <div class="section-title">
                        <h4>Xu hướng</h4>
                    </div>
                    <?php
                        foreach ($product_limit_3 as $value) {
                            extract($value);
                        
                    ?>
                    <div class="trend__item">
                        <div class="trend__item__pic">
                            <a href="chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>"><img
                                    src="upload/<?=$image?>" style="width: 90px;" alt=""></a>
                        </div>
                        <div class="trend__item__text">
                            <h6>
                                <a href="chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>"
                                    class="text-dark"><?=$name?></a>
                            </h6>
                            <div class="rating">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <div class="product__price"><?=number_format($sale_price)?>₫</div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="trend__content">
                    <div class="section-title">
                        <h4>BÁN CHẠY</h4>
                    </div>
                    <?php
                        foreach ($product_order_by as $value) {
                            extract($value);
                        
                    ?>
                    <div class="trend__item">
                        <div class="trend__item__pic">
                            <a href="chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>"><img
                                    src="upload/<?=$image?>" style="width: 90px;" alt=""></a>
                        </div>
                        <div class="trend__item__text">
                            <h6>
                                <a href="chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>"
                                    class="text-dark"><?=$name?></a>
                            </h6>
                            <div class="rating">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <div class="product__price"><?=number_format($sale_price)?>₫</div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="trend__content">
                    <div class="section-title">
                        <h4>Hot sale</h4>
                    </div>
                    <?php
                        foreach ($product_limit_3 as $value) {
                            extract($value);
                        
                    ?>
                    <div class="trend__item">
                        <div class="trend__item__pic">
                            <a href="chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>"><img
                                    src="upload/<?=$image?>" style="width: 90px;" alt=""></a>
                        </div>
                        <div class="trend__item__text">
                            <h6>
                                <a href="chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>"
                                    class="text-dark"><?=$name?></a>
                            </h6>
                            <div class="rating">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <div class="product__price"><?=number_format($sale_price)?>₫</div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Trend Section End -->

<!-- Discount Section Begin -->
<section class="discount">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 p-0">
                <div class="discount__pic">
                    <img src="upload/banner/banner-nho2.jpg" alt="Hình ảnh">
                </div>
            </div>
            <div class="col-lg-6 p-0">
                <div class="discount__text">
                    <div class="discount__text__title">
                        <span>Khuyến mãi</span>
                        <h2>11 - 10</h2>
                        <h5><span>Sale</span> 20%</h5>
                    </div>
                    <div class="discount__countdown" id="countdown-time">
                        <div class="countdown__item">
                            <span>05</span>
                            <p>Ngày</p>
                        </div>
                        <div class="countdown__item">
                            <span>18</span>
                            <p>Giờ</p>
                        </div>
                        <div class="countdown__item">
                            <span>46</span>
                            <p>Phút</p>
                        </div>
                        <div class="countdown__item">
                            <span>05</span>
                            <p>Giây</p>
                        </div>
                    </div>
                    <a href="#">Mua ngay</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Discount Section End -->

<!-- Services Section Begin -->
<section class="services spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-car"></i>
                    <h6>Miễn phí vận chuyển</h6>
                    <p>Đơn hàng trên 400.000đ</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-money"></i>
                    <h6>Đảm bảo hoàn tiền</h6>
                    <p>Nếu sản phẩm có vấn đề</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-support"></i>
                    <h6>Hỗ trợ trực tuyến 24/7</h6>
                    <p>Hỗ trợ chuyên dụng</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-headphones"></i>
                    <h6>Thanh toán an toàn</h6>
                    <p>Thanh toán an toàn 100%</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Services Section End -->

<style>
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