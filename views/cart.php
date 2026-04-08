<?php

    $success = '';
    $error = '';
    if (!empty($_SESSION['cart_flash_success'])) {
        $success = $_SESSION['cart_flash_success'];
        unset($_SESSION['cart_flash_success']);
    }
    if (!empty($_SESSION['cart_flash_error'])) {
        $error = $_SESSION['cart_flash_error'];
        unset($_SESSION['cart_flash_error']);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ajax_update_qty"]) && isset($_SESSION['user'])) {
        header('Content-Type: application/json; charset=utf-8');
        $user_id = (int)$_SESSION['user']['id'];
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if ($quantity < 1) {
            $quantity = 1;
        }
        if ($product_id <= 0) {
            echo json_encode(array('ok' => false, 'message' => 'Sản phẩm không hợp lệ'));
            exit();
        }
        $product_info = $ProductModel->select_products_by_id($product_id);
        if (!$product_info || (int)$product_info['quantity'] <= 0) {
            echo json_encode(array('ok' => false, 'message' => 'Sản phẩm không còn khả dụng'));
            exit();
        }
        if ($quantity > (int)$product_info['quantity']) {
            $quantity = (int)$product_info['quantity'];
        }
        $CartModel->update_cart($quantity, $product_id, $user_id);
        echo json_encode(array('ok' => true, 'quantity' => $quantity));
        exit();
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_to_cart"])) {
        $is_ajax_add_to_cart = isset($_POST['ajax_add_to_cart']) && (int)$_POST['ajax_add_to_cart'] === 1;
        $product_id = (int)$_POST["product_id"];
        $user_id = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : (int)($_POST["user_id"] ?? 0);
        $product_quantity = (int)$_POST["product_quantity"];

        if($product_quantity < 1 ) {
            if ($is_ajax_add_to_cart) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(array(
                    'ok' => false,
                    'message' => 'Số lượng sản phẩm không hợp lệ'
                ));
                exit();
            }
            $error = 'Số lượng sản phẩm không hợp lệ';
        }

        if ($error === '') {
            $product_info = $ProductModel->select_products_by_id($product_id);
            if (!$product_info) {
                $error = 'Sản phẩm không tồn tại hoặc đã bị ẩn.';
            } elseif ((int)$product_info['quantity'] <= 0) {
                $error = 'Sản phẩm đã hết hàng.';
            } else {
                $product_name = $product_info['name'];
                $product_price = $product_info['sale_price'];
                $product_image = $product_info['image'];
                $max_quantity = (int)$product_info['quantity'];

                // Đếm số lượng sản trong giỏ hàng
                $product = $CartModel->select_cart_by_id($product_id, $user_id);
                // Kiểm tra xem có sản phẩm trong giỏ hàng hay không
                if($product && is_array($product)) {
                    // Số lượng mới = số lượng hiện tại + số lượng vừa thêm
                    $current_quantity = (int)$product['product_quantity'];
                    $new_quantity = $current_quantity + $product_quantity;
                    if ($new_quantity > $max_quantity) {
                        $new_quantity = $max_quantity;
                        $error = 'Số lượng trong giỏ đã được giới hạn theo tồn kho hiện tại.';
                    }

                    // Cập nhật số lượng
                    $CartModel->update_cart($new_quantity, $product_id, $user_id);
                    if ($success === '') {
                        $success .= 'Đã cập nhật số lượng cho sản phẩm: '.$product_name;
                    }
                }
                else {
                    if ($product_quantity > $max_quantity) {
                        $product_quantity = $max_quantity;
                        $error = 'Số lượng thêm vào giỏ đã được giới hạn theo tồn kho hiện tại.';
                    }
                    $CartModel->insert_cart($product_id, $user_id, $product_name, $product_price, $product_quantity, $product_image);
                    if ($success === '') {
                        $success = "Đã thêm sản phẩm vào giỏ hàng";
                    }
                }
            }
        }

        if ($is_ajax_add_to_cart) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array(
                'ok' => $error === '',
                'message' => $error === '' ? 'Bạn đã thêm sản phẩm vào giỏ hàng thành công :3' : $error
            ));
            exit();
        }

        $_SESSION['cart_flash_success'] = $success;
        $_SESSION['cart_flash_error'] = $error;
        if ($success !== '') {
            $_SESSION['cart_toast_success'] = 'Bạn đã thêm sản phẩm vào giỏ hàng thành công :3';
        }

        $redirect_url = '';
        if (!empty($_POST['redirect_to'])) {
            $redirect_url = trim((string)$_POST['redirect_to']);
        } elseif (!empty($_SERVER['HTTP_REFERER'])) {
            $redirect_url = trim((string)$_SERVER['HTTP_REFERER']);
        }

        if ($redirect_url === '') {
            $redirect_url = "index.php?url=gio-hang";
        }

        header("Location: " . $redirect_url);
        exit();
    }

    if(isset($_GET['xoa'])) {
        $cart_id = $_GET['xoa'];
        $CartModel->delete_cart_by_id($cart_id);

        $success = 'Đã xóa 1 sản phẩm';
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_selected_products"]) && isset($_SESSION['user'])) {
        $user_id = (int)$_SESSION['user']['id'];
        $selected_ids = isset($_POST['selected_product_ids']) && is_array($_POST['selected_product_ids'])
            ? array_values(array_unique(array_filter(array_map('intval', $_POST['selected_product_ids']), function ($id) {
                return $id > 0;
            })))
            : array();

        if (empty($selected_ids)) {
            $error = 'Vui lòng chọn sản phẩm cần xóa';
        } else {
            foreach ($selected_ids as $selected_product_id) {
                $CartModel->delete_product_in_cart($selected_product_id, $user_id);
            }
            $success = 'Đã xóa ' . count($selected_ids) . ' sản phẩm';
        }
    }
?>


<?php 
    if(isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['id'];
        $list_carts = $CartModel->select_all_carts($user_id);
        $count_carts = count($CartModel->count_cart($user_id));

        // Đồng bộ giỏ với trạng thái/tồn kho hiện tại của sản phẩm.
        foreach ($list_carts as $item) {
            $product_info = $ProductModel->select_products_by_id($item['product_id']);
            if (!$product_info || (int)$product_info['quantity'] <= 0) {
                $CartModel->delete_product_in_cart($item['product_id'], $user_id);
                continue;
            }

            $valid_qty = (int)$item['product_quantity'];
            if ($valid_qty > (int)$product_info['quantity']) {
                $valid_qty = (int)$product_info['quantity'];
            }

            $CartModel->update_cart($valid_qty, $item['product_id'], $user_id);
        }

        $list_carts = $CartModel->select_all_carts($user_id);
        $count_carts = count($CartModel->count_cart($user_id));
    }
    
?>

<?php if(isset($_SESSION['user'])) { ?>
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
                    <a href="index.php?url=cua-hang"> Cửa hàng</a>
                    <span>Giỏ hàng</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kiểm tra giỏ hàng có sản phẩm không -->
<?php if(count($list_carts) > 0) { ?>
<!-- Shop Cart Section Begin -->
<section class="shop-cart spad">
    <div class="container">
        <form action="" method="post">
            <div class="row">
                <div class="col-lg-12">
                    <!-- <form action="" method="post"> -->
                    <div class="shop__cart__table">
                        <?=$alert = $BaseModel->alert_error_success($error, $success)?>
                        <div id="checkout-warning-top" class="checkout-warning-message" style="display:none;"></div>
                        <table>
                            <thead>
                                <tr>
                                    <th>CHỌN</th>
                                    <th>SẢN PHẨM</th>
                                    <th>GIÁ</th>
                                    <th>SỐ LƯỢNG</th>
                                    <th>TỔNG</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $totalPayment = 0;
                                    foreach ($list_carts as $value) {
                                        extract($value);
                                        $totalPrice = ($product_price * $product_quantity);
                                        //Tổn thanh toán
                                        $totalPayment += $totalPrice;
                                        // Lấy id danh mục của sản phẩm để hiện thị đường dẫn sang trang ctsp
                                        $product = $ProductModel->select_cate_in_product($product_id);
                
                                    ?>
                                <tr class="cart-row" data-product-id="<?=$product_id?>" data-unit-price="<?=$product_price?>">
                                    <td>
                                        <label class="tick-box-wrap">
                                            <input type="checkbox" class="checkout-product-checkbox" value="<?=$product_id?>">
                                            <span class="tick-box"></span>
                                        </label>
                                    </td>
                                    <td class="cart__product__item">
                                        <a
                                            href="chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$product['category_id']?>">
                                            <img src="upload/<?=$product_image?>" alt="">
                                        </a>
                                        <div class="cart__product__item__title">
                                            <h6 class="text-truncate-1">
                                                <a href="chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$product['category_id']?>"
                                                    class="text-dark">
                                                    <?=$product_name?>
                                                </a>
                                            </h6>
                                            <div class="rating">
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="cart__price"><?=number_format($product_price)?>đ</td>
                                    <input type="hidden" name="product_id[]" value="<?=$product_id?>">
                                    <td class="cart__quantity">
                                        <!-- <div class="pro-qty">
                                                <input type="text" value="1">
                                            </div> -->
                                        <div class="input-group float-left">
                                            <div class="input-next-cart-custom d-flex ">
                                                <input type="button" value="-" class="button-minus"
                                                    data-field="quantity">
                                                <input type="number" readonly step="1" min="1"
                                                    value="<?=$product_quantity?>" name="quantity[]"
                                                    class="quantity-field-cart">
                                                <input type="button" value="+" class="button-plus"
                                                    data-field="quantity">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="cart__total row-total"><?=number_format($totalPrice)?>đ</td>
                                    <td class="cart__close">
                                        <a href="index.php?url=gio-hang&xoa=<?=$cart_id?>">
                                            <span class="icon_close"></span>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                    ?>

                            </tbody>
                        </table>
                    </div>
                    <!-- </form> -->
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="cart__btn cart-actions-inline">
                        <a href="index.php?url=cua-hang">Tiếp tục mua sắm</a>
                        <button type="button" id="select-all-btn" class="action-btn-like-link">CHỌN TẤT CẢ</button>
                        <button type="button" id="delete-selected-btn" class="action-btn-like-link delete-btn" style="display:none;">XÓA SẢN PHẨM</button>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6"></div>
            </div>
        </form>
        <div class="row">
            <div class="col-lg-6">
                <!-- <div class="discount__content">
                        <h6>MÃ GIẢM GIÁ</h6>
                        <form action="#">
                            <input type="text" placeholder="Nhập mã">
                            <button type="submit" class="site-btn">áp dụng</button>
                        </form>
                    </div> -->
            </div>
            <div class="col-lg-4 offset-lg-2">
                <div class="cart__total__procced">
                    <h6>Tổng tiền</h6>
                    <ul>
                        <li>Số lượng <span id="selected-count">0 sản phẩm</span></li>
                        <!-- Tổng thanh toán -->
                        <li>Tổng <span id="selected-total">0đ</span></li>
                    </ul>
                    <button type="button" id="checkout-selected-btn" class="primary-btn w-100">THANH TOÁN</button>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Shop Cart Section End -->
<?php }else { ?>
<div class="row" style="margin-bottom: 400px;">
    <div class="col-lg-12 col-md-12">
        <div class="container-fluid mt-5">
            <div class="row rounded justify-content-center mx-0 pt-5">
                <div class="col-md-6 text-center">
                    <h4 class="mb-4">Chưa có sản phẩm nào trong giỏ hàng</h4>
                    <a class="btn btn-primary rounded-pill py-3 px-5" href="index.php?url=cua-hang">Xem sản phẩm</a>
                    <a class="btn btn-secondary rounded-pill py-3 px-5" href="index.php">Trang chủ</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php }else {?>
<div class="row" style="margin-bottom: 400px;">
    <div class="col-lg-12 col-md-12">
        <div class="container-fluid mt-5">
            <div class="row rounded justify-content-center mx-0 pt-5">
                <div class="col-md-6 text-center">
                    <h4 class="mb-4">Vui lòng đăng nhập để có thể mua hàng</h4>
                    <a class="btn btn-primary rounded-pill py-3 px-5" href="index.php?url=dang-nhap">Đăng nhập</a>
                    <a class="btn btn-secondary rounded-pill py-3 px-5" href="index.php">Trang chủ</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php }?>


<style>
.cart__btn a:hover {
    background-color: #0A68FF;
    color: #fff;
    transition: 0.2s;
}

.cart__btn button:hover {
    background-color: #0A68FF;
    color: #fff;
    transition: 0.2s;
}

.cart-actions-inline {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.action-btn-like-link {
    display: inline-block;
    font-size: 14px;
    color: #111111;
    font-weight: 600;
    text-transform: uppercase;
    background: #f5f5f5;
    padding: 14px 30px 12px;
    border: none;
    cursor: pointer;
}

.action-btn-like-link.delete-btn {
    background: #ffe8ea;
    color: #b42318;
}

.cart__total__procced {
    max-width: 430px;
    margin-left: auto;
    margin-right: auto;
}

.checkout-warning-message {
    margin-bottom: 10px;
    padding: 8px 10px;
    font-size: 13px;
    color: #b42318;
    background: #fff1f3;
    border: 1px solid #fecdca;
    border-radius: 6px;
}

.tick-box-wrap {
    display: inline-flex;
    cursor: pointer;
    align-items: center;
    justify-content: center;
}

.tick-box-wrap input {
    display: none;
}

.tick-box {
    width: 18px;
    height: 18px;
    border: 2px solid #bfc7d4;
    border-radius: 5px;
    display: inline-block;
    position: relative;
    background: #fff;
    transition: all 0.2s ease;
}

.tick-box-wrap input:checked + .tick-box {
    background: #0a68ff;
    border-color: #0a68ff;
}

.tick-box-wrap input:checked + .tick-box:after {
    content: '';
    position: absolute;
    left: 5px;
    top: 1px;
    width: 5px;
    height: 10px;
    border: solid #fff;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}
</style>

<script>
(function() {
    var checkoutBtn = document.getElementById('checkout-selected-btn');
    var warningEl = document.getElementById('checkout-warning-top');
    var selectedCountEl = document.getElementById('selected-count');
    var selectedTotalEl = document.getElementById('selected-total');
    var selectAllBtn = document.getElementById('select-all-btn');
    var deleteSelectedBtn = document.getElementById('delete-selected-btn');
    var checkboxes = Array.prototype.slice.call(document.querySelectorAll('.checkout-product-checkbox'));
    var cartRows = Array.prototype.slice.call(document.querySelectorAll('tr.cart-row'));
    if (!checkoutBtn) return;

    function formatMoney(num) {
        return Number(num || 0).toLocaleString('en-US') + 'đ';
    }

    function recalcRow(row) {
        var unit = Number(row.getAttribute('data-unit-price') || 0);
        var qtyInput = row.querySelector('.quantity-field-cart');
        var qty = Number(qtyInput ? qtyInput.value : 0);
        var rowTotal = unit * qty;
        var totalCell = row.querySelector('.row-total');
        if (totalCell) totalCell.textContent = formatMoney(rowTotal);
    }

    function recalcSummary() {
        var selectedItems = 0;
        var selectedTotal = 0;
        var checkedRows = 0;
        cartRows.forEach(function(row) {
            var checkbox = row.querySelector('.checkout-product-checkbox');
            var qtyInput = row.querySelector('.quantity-field-cart');
            var unit = Number(row.getAttribute('data-unit-price') || 0);
            var qty = Number(qtyInput ? qtyInput.value : 0);
            if (checkbox && checkbox.checked) {
                checkedRows += 1;
                selectedItems += qty;
                selectedTotal += unit * qty;
            }
        });
        if (selectedCountEl) selectedCountEl.textContent = selectedItems + ' sản phẩm';
        if (selectedTotalEl) selectedTotalEl.textContent = formatMoney(selectedTotal);
        if (deleteSelectedBtn) {
            deleteSelectedBtn.style.display = checkedRows > 0 ? 'inline-block' : 'none';
        }
        if (selectAllBtn) {
            var allChecked = checkboxes.length > 0 && checkboxes.every(function(c) { return c.checked; });
            selectAllBtn.textContent = allChecked ? 'BỎ CHỌN TẤT CẢ' : 'CHỌN TẤT CẢ';
        }
    }

    function syncQtyToServer(productId, quantity, row) {
        var body = new URLSearchParams();
        body.append('ajax_update_qty', '1');
        body.append('product_id', String(productId));
        body.append('quantity', String(quantity));
        fetch(window.location.href, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
            body: body.toString()
        }).then(function(res) {
            return res.json();
        }).then(function(data) {
            if (!data || !data.ok || !row) return;
            var qtyInput = row.querySelector('.quantity-field-cart');
            if (qtyInput && Number(qtyInput.value) !== Number(data.quantity)) {
                qtyInput.value = Number(data.quantity);
                recalcRow(row);
                recalcSummary();
            }
        }).catch(function() {});
    }

    cartRows.forEach(function(row) {
        var productId = Number(row.getAttribute('data-product-id') || 0);
        var minusBtn = row.querySelector('.button-minus');
        var plusBtn = row.querySelector('.button-plus');
        var qtyInput = row.querySelector('.quantity-field-cart');
        var checkbox = row.querySelector('.checkout-product-checkbox');

        if (checkbox) {
            checkbox.addEventListener('change', function() {
                if (warningEl && checkbox.checked) warningEl.style.display = 'none';
                recalcSummary();
            });
        }

        if (minusBtn && qtyInput) {
            minusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var qty = Number(qtyInput.value || 1);
                qty = Math.max(1, qty - 1);
                qtyInput.value = qty;
                recalcRow(row);
                recalcSummary();
                if (productId > 0) syncQtyToServer(productId, qty, row);
            });
        }

        if (plusBtn && qtyInput) {
            plusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var qty = Number(qtyInput.value || 1) + 1;
                qtyInput.value = qty;
                recalcRow(row);
                recalcSummary();
                if (productId > 0) syncQtyToServer(productId, qty, row);
            });
        }
    });

    recalcSummary();

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            var allChecked = checkboxes.length > 0 && checkboxes.every(function(c) { return c.checked; });
            checkboxes.forEach(function(c) { c.checked = !allChecked; });
            recalcSummary();
        });
    }

    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            var checked = document.querySelectorAll('.checkout-product-checkbox:checked');
            if (!checked.length) return;
            var form = document.createElement('form');
            form.method = 'post';
            form.action = 'index.php?url=gio-hang';

            var actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'delete_selected_products';
            actionInput.value = '1';
            form.appendChild(actionInput);

            checked.forEach(function(item) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_product_ids[]';
                input.value = item.value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        });
    }

    checkoutBtn.addEventListener('click', function() {
        var checked = document.querySelectorAll('.checkout-product-checkbox:checked');
        if (!checked.length) {
            if (warningEl) {
                warningEl.textContent = 'Vui lòng chọn ít nhất 1 sản phẩm...';
                warningEl.style.display = 'block';
            }
            window.scrollTo({ top: warningEl ? warningEl.offsetTop - 120 : 0, behavior: 'smooth' });
            return;
        }
        if (warningEl) {
            warningEl.style.display = 'none';
        }

        var form = document.createElement('form');
        form.method = 'post';
        form.action = 'index.php?url=thanh-toan';

        checked.forEach(function(item) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_product_ids[]';
            input.value = item.value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    });
})();
</script>