<!-- Breadcrumb Begin -->
<?php
    $success = '';
    $error = '';
try {    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["checkout"])) {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?url=dang-nhap");
            exit();
        }
        // Table orders
        $user_id = (int)$_SESSION['user']['id'];
        $total = $_POST["total_checkout"];
        $address = $_POST["address"];
        $phone = $_POST["phone"];
        $note = $_POST["note"];
        $payment_method = isset($_POST["payment_method"]) ? trim($_POST["payment_method"]) : "cod";

        // Table orderdetails
        $arr_product_id = $_POST["product_id"];
        $arr_quantity = $_POST["quantity"];
        $arr_price = $_POST["price"];
        if ($payment_method === 'momo') {
            header("Location: index.php?url=thanh-toan-momo");
            exit();
        }

        $items = [];
        for ($i = 0; $i < count($arr_product_id); $i++) {
            $items[] = [
                'product_id' => (int)$arr_product_id[$i],
                'quantity' => (int)$arr_quantity[$i],
                'price' => (int)$arr_price[$i]
            ];
        }

        $order_id = $OrderModel->create_order_with_stock_validation($user_id, $total, $address, $phone, $note, $items);
        if(!empty($order_id)) {
            $_SESSION['last_order_summary'] = array(
                'order_id' => $order_id,
                'address' => $address,
                'phone' => $phone,
                'payment_method' => $payment_method,
                'total' => (int)$total
            );
            if (!empty($_SESSION['checkout_selected_product_ids'])) {
                $OrderModel->delete_cart_by_product_ids($user_id, $_SESSION['checkout_selected_product_ids']);
                unset($_SESSION['checkout_selected_product_ids']);
            }
            if ($payment_method === 'bank') {
                header("Location: index.php?url=thanh-toan-ngan-hang");
                exit();
            }
            header("Location: cam-on");
            exit();
        }
        

    }
} catch (Exception $e) {
    $error = $e->getMessage();
}


?>
<?php 
    if(isset($_SESSION['user'])) { 
        $user_id = $_SESSION['user']['id'];
        $secondary_address = $AddressModel->select_address_user($user_id);
        $has_secondary_address = is_array($secondary_address) && !empty(trim((string)($secondary_address['address'] ?? '')));
        $list_carts = $CartModel->select_all_carts($user_id);
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['selected_product_ids']) && is_array($_POST['selected_product_ids'])) {
            $_SESSION['checkout_selected_product_ids'] = array_values(array_unique(array_filter(array_map('intval', $_POST['selected_product_ids']))));
        }
        if (!empty($_SESSION['checkout_selected_product_ids'])) {
            $selected_ids = $_SESSION['checkout_selected_product_ids'];
            $list_carts = array_values(array_filter($list_carts, function ($item) use ($selected_ids) {
                return in_array((int)$item['product_id'], $selected_ids, true);
            }));
        }
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
                    if($success != '' || $error != '') {
                        $alert = $BaseModel->alert_error_success($error, $success);
                        echo $alert;
                    }
                ?>
            <div class="row checkout-layout">
                <div class="col-lg-8 checkout-main">
                    <h5 class="checkout-title">CHI TIẾT THANH TOÁN</h5>
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
                                <input disabled type="text" value="<?= $_SESSION['user']['email'] ?>">
                            </div>
                        </div>
                        <div class="col-lg-12">

                            <div class="checkout__form__input">
                                <p>Địa chỉ <span>*</span></p>
                                <input disabled type="text" value="<?= $_SESSION['user']['address'] ?>">

                            </div>

                        </div>
                        <div class="col-lg-12">
                            <div class="checkout__form__input">
                                <p>Số điện thoại <span>*</span></p>
                                <input disabled type="text" name="phone" value="<?= $_SESSION['user']['phone'] ?>">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="checkout__form__input">
                                <p>Ghi chú<span></span></p>
                                <input type="text" name="note">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <p class="checkout-helper-text">Bạn có thể sử dụng địa chỉ mặc định khi đăng ký, hoặc nhập địa chỉ khác.</p>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="cart__btn address-switch-wrap">
                                <a href="index.php?url=thanh-toan-2">Địa chỉ mới</a>
                            </div>

                        </div>

                        <?php if ($has_secondary_address): ?>
                            <div class="col-lg-4 col-md-5 col-sm-6">
                                <div class="cart__btn address-switch-wrap">
                                    <a href="index.php?url=thanh-toan-dia-chi2">Sử dụng địa chỉ 2</a>
                                </div>
                            </div>
                        <?php endif; ?>


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
                                    <input type="hidden" name="address" value="<?=$_SESSION['user']['address']?>">
                                    <input type="hidden" name="phone" value="<?=$_SESSION['user']['phone']?>">
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
                        <!-- <div class="checkout__order__widget">
                                <label for="paypal">
                                    Thanh toán khi nhận hàng
                                    <input type="checkbox" id="paypal">
                                    <span class="checkmark"></span>
                                </label>
                            </div> -->
                        <?php if($count_cart > 0) {?>
                        <div class="checkout__order__widget payment-method-box mb-2">
                            <label class="payment-option is-selected">
                                <input type="radio" name="payment_method" value="cod" checked>
                                <span class="payment-icon"><i class="fa fa-money"></i></span>
                                <span>Thanh toán khi nhận hàng (COD)</span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="bank">
                                <span class="payment-icon"><i class="fa fa-university"></i></span>
                                <span>Chuyển khoản ngân hàng</span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="momo">
                                <span class="payment-icon"><i class="fa fa-credit-card"></i></span>
                                <span>Ví MoMo</span>
                            </label>
                            <div id="bank-transfer-note" class="bank-transfer-note" style="display:none;">
                                Chuyển khoản an toàn: chỉ chuyển vào tài khoản chính chủ do cửa hàng cung cấp sau khi xác nhận đơn. Không chia sẻ OTP, mã PIN, mật khẩu ngân hàng cho bất kỳ ai.
                            </div>
                        </div>
                        <button type="button" class="site-btn" data-toggle="modal" data-target="#thanh-toan-1">
                            ĐẶT HÀNG
                        </button>
                        <!-- Modal thanh toán-->
                        <div class="modal fade" id="thanh-toan-1" tabindex="-1" role="dialog"
                            aria-labelledby="thanh-toan-1" aria-hidden="true">
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
                                        <button type="submit" name="checkout" class="btn btn-primary">Xác nhận</button>
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
    margin-bottom: 12px;
}

.address-switch-wrap a {
    width: 100%;
    text-align: center;
    border-radius: 10px;
    border: 1px solid #d9e4f2;
    background: #f7faff;
    color: #1f3855;
    font-weight: 600;
    padding: 12px 16px 10px;
    transition: all .2s ease;
}

.address-switch-wrap a:hover {
    background-color: #0A68FF;
    border-color: #0A68FF;
    color: #fff;
}

.checkout__order h5 {
    font-size: 26px;
    margin-bottom: 14px;
}

.checkout__order__product ul li:first-child {
    font-weight: 700;
    color: #24364d;
}

.checkout__order__product ul li {
    border-bottom: 1px dashed #e8edf5;
    padding: 10px 0;
    margin-bottom: 0;
}

.checkout__order__product ul li:last-child {
    border-bottom: none;
}

.checkout__order__total ul li {
    font-size: 22px;
}

.checkout__order__total ul li span {
    color: #0A68FF;
}

.payment-method-box label {
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid #dfe3eb;
    border-radius: 10px;
    padding: 10px 12px;
    margin-bottom: 10px;
    font-size: 14px;
    background: #fff;
    cursor: pointer;
    transition: all 0.2s ease;
}

.payment-method-box label:hover {
    border-color: #0a68ff;
    box-shadow: 0 4px 10px rgba(10, 104, 255, 0.12);
}

.payment-method-box input[type="radio"] {
    width: 17px;
    height: 17px;
    accent-color: #0a68ff;
}

.payment-option.is-selected {
    border-color: #0a68ff;
    background: #eef4ff;
    box-shadow: 0 4px 12px rgba(10, 104, 255, 0.18);
}

.payment-icon {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #f3f7ff;
    color: #0a68ff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
}

.bank-transfer-note {
    margin-top: 4px;
    padding: 9px 10px;
    border-radius: 8px;
    border: 1px dashed #91b4ff;
    background: #f5f9ff;
    color: #174ea6;
    font-size: 12px;
    line-height: 1.45;
}

.checkout-sidebar .site-btn {
    width: 100%;
    border-radius: 10px;
    font-weight: 700;
}

@media (max-width: 991px) {
    .checkout-main {
        margin-bottom: 16px;
    }
}
</style>

<script>
(function() {
    var radios = document.querySelectorAll('.payment-option input[type="radio"]');
    var bankNote = document.getElementById('bank-transfer-note');
    if (!radios.length) return;

    function syncPaymentState() {
        radios.forEach(function(radio) {
            var label = radio.closest('.payment-option');
            if (!label) return;
            if (radio.checked) {
                label.classList.add('is-selected');
            } else {
                label.classList.remove('is-selected');
            }
        });

        var selected = document.querySelector('.payment-option input[type="radio"]:checked');
        if (bankNote) {
            bankNote.style.display = (selected && selected.value === 'bank') ? 'block' : 'none';
        }
    }

    radios.forEach(function(radio) {
        radio.addEventListener('change', syncPaymentState);
    });
    syncPaymentState();
})();
</script>