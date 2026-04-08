<!-- Breadcrumb Begin -->
<?php
    $success = '';
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
        if ($payment_method === 'momo') {
            header("Location: index.php?url=thanh-toan-momo-address");
            exit();
        }

        if(empty(array_filter($error))) {
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
        $list_carts = $CartModel->select_all_carts($user_id);
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
                    if($success != '' || !empty(array_filter($error))) {
                        $alert = $BaseModel->alert_error_success(implode('<br>', array_filter($error)), $success);
                        echo $alert;
                    }
                ?>
            <div class="row">
                <div class="col-lg-8">
                    <h5>CHI TIẾT THANH TOÁN</h5>
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
                            <p style="color: #000000; font-weight:500; font-size: 15px;">Bạn có thể nhập nhập địa chỉ
                                khác, hoặc sử dụng địa chỉ mặc định</p>
                        </div>
                        <div class="col-lg-5">
                            <div class="cart__btn">
                                <a href="thanh-toan">Sử dụng địa chỉ mặc định</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
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
                        <button type="button" class="site-btn" data-toggle="modal" data-target="#thanhtoan">
                            ĐẶT HÀNG
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
.cart__btn a:hover {
    background-color: #0A68FF;
    color: #fff;
    transition: 0.2s;
}

.checkout__form .checkout__form__input input {
    color: #000000;
}

.checkout__form .checkout__form__input input:focus {
    border: 1px solid #999999;
}

.error {
    display: inline-block;
    height: 20px;
    font-size: 15px;
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
            label.classList.toggle('is-selected', !!radio.checked);
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