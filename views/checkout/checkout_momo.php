<!-- Breadcrumb Begin -->
<?php

    $error = array(
        'address' => '',
        'phone' => '',
    );
    $temp = array(
        'address' => '',
        'phone' => '',
        'note' => '',
    );
try {    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["payUrl"])) {
        // Table orders
        $user_id = $_POST["user_id"];
        $total = $_POST["total_checkout"];
        $address = $_POST["address"];
        $phone = $_POST["phone"];
        $note = $_POST["note"];

        // Check form
        if(empty($address)) {
            $error['address'] = 'Địa chỉ không được để trống';
        }

        if(strlen($address) > 255) {
            $error['address'] = 'Địa chỉ tối đa 255 ký tự';
        }

        if(empty($phone)) {
            $error['phone'] = 'Số điện thoại không được để trống';
        }
        else {
            //Biểu thức chính quy kiểm tra định dạng sdt
            if (!preg_match('/^(03|05|07|08|09)\d{8}$/', $phone)) {
                $error['phone'] = 'Số điện thoại không đúng định dạng.';
            }
        }
        // End heck form

        // Table orderdetails
        $arr_product_id = $_POST["product_id"];
        $arr_quantity = $_POST["quantity"];
        $arr_price = $_POST["price"];

        if(empty(array_filter($error))) {
            // Thanh toán MOMO
            include_once "views/checkout/momo.php";

            // Sau khi thanh toán momo thành công
            $items = [];
            for ($i = 0; $i < count($arr_product_id); $i++) {
                $items[] = [
                    'product_id' => (int)$arr_product_id[$i],
                    'quantity' => (int)$arr_quantity[$i],
                    'price' => (int)$arr_price[$i]
                ];
            }

            $order_id = $OrderModel->create_order_with_stock_validation($user_id, $total, $address, $phone, $note, $items);

            // Gửi mail
            include_once "views/checkout/send-mail-order.php";
        }else {
            $temp['address'] = $address;
            $temp['phone'] = $phone;
            $temp['note'] = $note;
        }

    }
} catch (Exception $e) {
    $error['general'] = $e->getMessage();
}


?>
<?php 
    if(isset($_SESSION['user'])) { 
        $user_id = $_SESSION['user']['id'];
        $secondary_address = $AddressModel->select_address_user($user_id);
        $has_secondary_address = is_array($secondary_address) && !empty(trim((string)($secondary_address['address'] ?? '')));
        $list_carts = $CartModel->select_all_carts($user_id);
        $count_cart = count($CartModel->count_cart($user_id));
    ?>
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
                    <span>Thanh toán</span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Checkout Section Begin -->
<section class="checkout spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h6 class="coupon__link"><span class="icon_tag_alt mr-1"></span>Tiến hành thanh toán đơn hàng <a
                        class="text-primary" href="gio-hang">Trở lại giỏ hàng</a> </h6>
            </div>
        </div>
        <form action="" method="post" class="checkout__form">
            <?php
                if (!empty(array_filter($error))) {
                    echo $BaseModel->alert_error_success(implode('<br>', array_filter($error)), '');
                }
            ?>
            <div class="row checkout-layout">
                <div class="col-lg-8 checkout-main">
                    <h5 class="checkout-title">CHI TIẾT THANH TOÁN QUA MOMO</h5>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="checkout__form__input">
                                <p>Họ tên <span>*</span></p>
                                <input type="text" disabled name="full_name"
                                    value="<?= $_SESSION['user']['full_name'] ?>">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="checkout__form__input">
                                <p>Email <span>*</span></p>
                                <input disabled type="text" name="email" value="<?= $_SESSION['user']['email'] ?>">
                            </div>
                        </div>
                        <div class="col-lg-12">

                            <div class="checkout__form__input">
                                <p>Địa chỉ <span>*</span></p>
                                <input class="mb-0" type="text" name="address" value="<?=$temp['address']?>">
                                <span class="text-danger error"><?=$error['address']?></span>
                            </div>

                        </div>
                        <div class="col-lg-12">
                            <div class="checkout__form__input">
                                <p>Số điện thoại <span>*</span></p>
                                <input class="mb-0" type="text" name="phone" value="<?=$temp['phone']?>">
                                <span class="text-danger error"><?=$error['phone']?></span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="checkout__form__input">
                                <p>Ghi chú<span></span></p>
                                <input type="text" value="<?=$temp['note']?>" name="note">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <p class="checkout-helper-text">Chọn địa chỉ nhận hàng trước khi chuyển sang ví MoMo để thanh toán.</p>
                            <div class="d-flex flex-wrap address-switch-group">
                                <a href="thanh-toan-momo-address" class="btn btn-outline-dark block mr-1">Địa chỉ 1</a>
                                <?php if ($has_secondary_address): ?>
                                    <a href="thanh-toan-momo-address-2" class="btn btn-outline-dark">Địa chỉ 2</a>
                                <?php endif; ?>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="col-lg-4 checkout-sidebar">
                    <div class="checkout__order">
                        <h5>ĐƠN HÀNG</h5>
                        <div class="checkout__order__product">
                            <ul>
                                <li>
                                    <span class="top__text">Sản phẩm</span>
                                    <span class="top__text__right">Tổng</span>
                                </li>
                                <?php
                                        $i = 0;
                                        $totalPayment = 0;
                                        foreach ($list_carts as $value) {
                                        extract($value);
                                        $totalPrice = ($product_price * $product_quantity);
                                        $totalPayment += $totalPrice;
                                        $i++;
                                    ?>
                                <li>
                                    <!-- Thông tin insert vào orders -->
                                    <input type="hidden" name="user_id" value="<?=$user_id?>">
                                    <input type="hidden" name="total_checkout" value="<?=$totalPayment?>">
                                    <!-- Thông tin insert vào orderdetails -->
                                    <input type="hidden" name="product_id[]" value="<?=$product_id?>">
                                    <input type="hidden" name="quantity[]" value="<?=$product_quantity?>">
                                    <input type="hidden" name="price[]" value="<?=$product_price?>">

                                    <?=$i?>.
                                    <?=$product_name?>
                                    <a class="text-primary">x<?=$product_quantity?></a>
                                    <span><?=number_format($totalPrice)?>đ</span>
                                </li>
                                <?php
                                        }
                                    ?>
                            </ul>
                        </div>
                        <div class="checkout__order__total">
                            <ul>

                                <li>Tổng <span><?=number_format($totalPayment)?>đ</span></li>
                            </ul>
                        </div>

                        <?php if($count_cart > 0) {?>
                        <div class="checkout__order__widget momo-payment-box mb-2">
                            <div class="momo-header">
                                <span class="momo-icon"><i class="fa fa-credit-card"></i></span>
                                <strong>Thanh toán qua Ví MoMo</strong>
                            </div>
                            <ul class="momo-checklist">
                                <li>Kiểm tra chính xác tổng tiền và địa chỉ nhận hàng.</li>
                                <li>Sau khi bấm thanh toán, bạn sẽ được chuyển tới cổng MoMo bảo mật.</li>
                                <li>Không chia sẻ OTP, mã PIN, mật khẩu cho bất kỳ ai.</li>
                            </ul>
                        </div>
                        <button type="button" class="site-btn btn-momo" data-toggle="modal" data-target="#thanhtoan">
                            THANH TOÁN MOMO
                        </button>
                        <!-- Modal thanh toán-->
                        <div class="modal fade" id="thanhtoan" tabindex="-1" role="dialog" aria-labelledby="thanhtoan"
                            aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4>Xác nhận đặt hàng</h4>
                                    </div>
                                    <div class="modal-body text-dark">
                                        Bạn có muốn tiếp tục đặt hàng ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Hủy</button>
                                        <button type="submit" name="payUrl" class="btn btn-primary">Xác nhận</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <?php }else {?>
                        <div class="checkout__order__widget text-center text-primary mb-2">
                            Chưa có sản phẩm trong giỏ hàng
                        </div>
                        <a href="cua-hang" class="site-btn btn">Xem sản phẩm</a>
                        <?php }?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<!-- Checkout Section End -->
<?php } else { ?>
<div class="row" style="margin-bottom: 400px;">
    <div class="col-lg-12 col-md-12">
        <div class="container-fluid mt-5">
            <div class="row rounded justify-content-center mx-0 pt-5">
                <div class="col-md-6 text-center">
                    <h4 class="mb-4">Vui lòng đăng nhập để có thể thanh toán</h4>
                    <a class="btn btn-primary rounded-pill py-3 px-5" href="index.php?url=dang-nhap">Đăng nhập</a>
                    <a class="btn btn-secondary rounded-pill py-3 px-5" href="index.php">Trang chủ</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>


<style>
.checkout.spad {
    padding-top: 28px;
}

.checkout-layout {
    align-items: flex-start;
}

.checkout-main {
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 16px;
    padding: 18px 18px 10px;
    box-shadow: 0 10px 26px rgba(21, 40, 74, 0.06);
}

.checkout-sidebar .checkout__order {
    border: 1px solid #e8edf5;
    border-radius: 16px;
    box-shadow: 0 10px 26px rgba(21, 40, 74, 0.06);
    background: #fff;
    padding: 20px 18px 18px;
}

.checkout-title {
    font-size: 25px;
    margin-bottom: 18px;
}

.checkout__form .checkout__form__input p {
    color: #24364d;
    margin-bottom: 8px;
    font-weight: 600;
}

.checkout__form .checkout__form__input input {
    color: #122033;
    border: 1px solid #dbe4ef;
    border-radius: 10px;
    height: 48px;
    background: #fbfdff;
}

.checkout__form .checkout__form__input input:focus {
    border: 1px solid #0A68FF;
    box-shadow: 0 0 0 3px rgba(10, 104, 255, 0.12);
}

.checkout-helper-text {
    color: #4f6279;
    font-size: 14px;
    margin-bottom: 10px;
}

.address-switch-group .btn {
    border-radius: 9px;
}

.error {
    display: inline-block;
    min-height: 20px;
    font-size: 14px;
}

.momo-payment-box {
    border: 1px solid #f2d7e8;
    border-radius: 12px;
    background: linear-gradient(180deg,#fff,#fff7fc);
    padding: 12px;
}

.momo-header {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #6d1d52;
    margin-bottom: 8px;
}

.momo-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #ffe5f2;
    color: #d82d8b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.momo-checklist {
    margin: 0;
    padding-left: 18px;
    color: #5b4a56;
    font-size: 13px;
}

.momo-checklist li {
    margin-bottom: 4px;
}

.btn-momo {
    background: linear-gradient(135deg, #d82d8b, #b71f75);
    color: #fff;
    border-radius: 10px;
    width: 100%;
    font-weight: 700;
}

.btn-momo:hover {
    opacity: 0.9;
    color: #fff;
}

@media (max-width: 991px) {
    .checkout-main {
        margin-bottom: 16px;
    }
}
</style>