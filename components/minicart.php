<?php
    $list_minicarts = [];
    $count_carts = 0;

    if(isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['id'];
        // Hiển thị nhiều sản phẩm hơn, phần danh sách sẽ có thanh cuộn.
        $list_minicarts = $CartModel->select_mini_carts($user_id, 50);
        $count_carts = count($CartModel->count_cart($user_id));

    }
   
?>

<!-- Mini cart -->
<div class="shopping-cart shopping-cart-modern">
    <div class="shopping-cart-header">
        <div class="row">
            <div class="col-4">
                
                <div id="close-minicart">
                    <i class="fa fa-close cart-icon"></i>
                </div>
            </div>
            <div class="col-8">
                
                <div style="font-size: 25px;" class="float-right">
                    <i class="fa fa-shopping-cart cart-icon"></i><span class="badge js-cart-count"><?=$count_carts?></span>
                </div>
            </div>
            
        </div>
    </div> <!--end shopping-cart-header -->
    

    <ul class="row pt-2 mini-cart mini-cart-scroll">
        
        <?php 
        $totalPayment = 0;
        foreach ($list_minicarts as $value) {
            extract($value);
            $totalPrice = ($product_price * $product_quantity);
            //Tổn thanh toán
            $totalPayment += $totalPrice;
            $cate = $ProductModel->select_cate_in_product($product_id);
            $mini_category_id = isset($cate['category_id']) ? (int)$cate['category_id'] : 0;
            $detail_link = "index.php?url=chitietsanpham&id_sp=" . (int)$product_id . "&id_dm=" . $mini_category_id;
        ?>
        <li class="col-xl-12 col-md-4 mini-cart-item" data-product-id="<?=$product_id?>" data-cart-id="<?=$cart_id?>">
            <div class="mini-cart-row">
                <a href="<?=$detail_link?>" class="mini-cart-thumb">
                    <img src="upload/<?=$product_image?>" class="img-sm border" alt="<?=$product_name?>">
                </a>
                <div class="mini-cart-info">
                    <a href="<?=$detail_link?>" class="text-truncate-2 text-dark mini-cart-title"><?=$product_name?></a>
                    <div class="mini-cart-line-price">
                        <span class="text-primary mini-cart-price"><?=number_format($product_price)?>đ</span>
                        <div class="mini-cart-qty-controls" data-product-id="<?=$product_id?>">
                            <button type="button" class="mini-qty-btn js-mini-cart-minus" aria-label="Giảm số lượng">-</button>
                            <span class="mini-qty-value"><?=$product_quantity?></span>
                            <button type="button" class="mini-qty-btn js-mini-cart-plus" aria-label="Tăng số lượng">+</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="mini-cart-remove-btn js-mini-cart-remove" data-cart-id="<?=$cart_id?>" title="Xóa sản phẩm" aria-label="Xóa sản phẩm">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </li>
        <?php 
        }
        ?>
        <?php if (empty($list_minicarts)) { ?>
        <li class="col-12">
            <div class="mini-cart-empty">Giỏ hàng đang trống.</div>
        </li>
        <?php } ?>
        
        <li class="col-xl-12 col-md-4">
            <div class="text-center text-dark js-cart-count-text"><?=$count_carts?> sản phẩm thêm vào giỏ</div>
        </li>
    </ul>
    <hr>
    <div class="row">
        <div class="col-12">
            <div class="text-center">
                <!-- <i class="fa fa-shopping-cart cart-icon"></i><span class="badge">323</span> -->
                <span class="text-dark font-weight-bolder">Tổng số tiền:</span>
                <span class="text-danger font-weight-bolder js-mini-cart-total"><?=number_format($totalPayment)?>₫</span>
            </div>
        </div>
    </div>
    <hr style="margin-bottom: -15px;">

    <div class="row">
        <div class="col-6">
            <a href="index.php?url=gio-hang" class="button">Xem giỏ hàng</a>
            
        </div>
        <div class="col-6">
            
            <a href="index.php?url=thanh-toan" class="button btn-outline-primary js-mini-cart-checkout">Thanh toán</a>
        </div>
    </div>

</div> 
<!--end mini-cart -->

<style>
.shopping-cart-modern {
    border-radius: 14px;
}

.mini-cart-scroll {
    max-height: min(56vh, 520px);
    overflow-y: auto;
    overflow-x: hidden;
    padding-right: 6px;
    scroll-behavior: smooth;
}

.mini-cart-scroll::-webkit-scrollbar {
    width: 8px;
}

.mini-cart-scroll::-webkit-scrollbar-track {
    background: #f3f6fb;
    border-radius: 999px;
}

.mini-cart-scroll::-webkit-scrollbar-thumb {
    background: #c5d3e4;
    border-radius: 999px;
}

.mini-cart-scroll::-webkit-scrollbar-thumb:hover {
    background: #9fb6d1;
}

.mini-cart-item {
    padding: 8px 0;
    border-bottom: 1px solid #eef3fa;
}

.mini-cart-item:last-of-type {
    border-bottom: none;
}

.mini-cart-row {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
}

.mini-cart-thumb img {
    width: 78px;
    height: 78px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e8edf5 !important;
}

.mini-cart-info {
    flex: 1;
    min-width: 0;
}

.mini-cart-title {
    display: block;
    font-weight: 600;
    line-height: 1.3;
    margin-bottom: 6px;
    font-size: 16px;
}

.mini-cart-line-price {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.mini-cart-price {
    font-size: 20px;
    line-height: 1;
}

.mini-cart-qty-controls {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #f7faff;
    border: 1px solid #dce7f4;
    border-radius: 8px;
    padding: 2px;
    white-space: nowrap;
}

.mini-qty-btn {
    width: 24px;
    height: 24px;
    border: 1px solid #d4deea;
    background: #fff;
    color: #2a3e57;
    border-radius: 6px;
    line-height: 1;
    font-size: 16px;
    font-weight: 700;
    padding: 0;
}

.mini-qty-btn:hover {
    background: #eaf2ff;
}

.mini-qty-value {
    min-width: 22px;
    text-align: center;
    font-size: 14px;
    font-weight: 600;
    color: #223548;
}

.mini-cart-remove-btn {
    border: 1px solid #e3ebf6;
    background: #f8fbff;
    color: #7a8ca3;
    width: 28px;
    height: 28px;
    border-radius: 999px;
    font-size: 12px;
    line-height: 1;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.mini-cart-remove-btn:hover {
    color: #dc2626;
    border-color: #fecaca;
    background: #fff5f5;
}

.mini-cart-empty {
    text-align: center;
    color: #6f8093;
    padding: 16px 0 10px;
}
</style>
