<?php
$wishlist_error = '';
$wishlist_success = '';

if (!empty($_SESSION['wishlist_error'])) {
    $wishlist_error = $_SESSION['wishlist_error'];
    unset($_SESSION['wishlist_error']);
}
if (!empty($_SESSION['wishlist_success'])) {
    $wishlist_success = $_SESSION['wishlist_success'];
    unset($_SESSION['wishlist_success']);
}
?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
                    <span>Danh sách yêu thích</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="shop-cart spad">
    <div class="container">
        <?php if ($wishlist_error !== ''): ?>
            <div class="alert alert-danger"><?=$wishlist_error?></div>
        <?php endif; ?>
        <?php if ($wishlist_success !== ''): ?>
            <div class="alert alert-success"><?=$wishlist_success?></div>
        <?php endif; ?>

        <?php if (!isset($_SESSION['user'])): ?>
            <div class="alert alert-warning">Bạn cần đăng nhập để xem danh sách yêu thích.</div>
            <a href="index.php?url=dang-nhap" class="site-btn">Đăng nhập ngay</a>
        <?php else: ?>
            <?php
                $user_id = (int)$_SESSION['user']['id'];
                $wishlist_ids = isset($_SESSION['wishlist'][$user_id]) && is_array($_SESSION['wishlist'][$user_id])
                    ? $_SESSION['wishlist'][$user_id]
                    : array();

                $wishlist_products = array();
                $valid_ids = array();
                foreach ($wishlist_ids as $pid) {
                    $pid = (int)$pid;
                    if ($pid <= 0) {
                        continue;
                    }
                    $item = $ProductModel->select_products_by_id($pid);
                    if ($item && (int)$item['status'] === 1) {
                        $wishlist_products[] = $item;
                        $valid_ids[] = $pid;
                    }
                }
                $_SESSION['wishlist'][$user_id] = $valid_ids;
            ?>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Danh sách yêu thích (<?=count($wishlist_products)?>)</h5>
                <?php if (!empty($wishlist_products)): ?>
                    <form id="wishlist-bulk-remove-form" action="index.php?url=yeu-thich" method="post" style="display:none;">
                        <input type="hidden" name="wishlist_action" value="bulk_remove">
                        <input type="hidden" name="redirect_to" value="index.php?url=yeu-thich">
                        <div id="wishlist-bulk-inputs"></div>
                        <button type="submit" class="site-btn" style="background:#dc3545;">Xóa yêu thích</button>
                    </form>
                <?php endif; ?>
            </div>

            <?php if (empty($wishlist_products)): ?>
                <div class="alert alert-info">Danh sách yêu thích của bạn đang trống.</div>
                <a href="index.php?url=cua-hang" class="site-btn">Tiếp tục mua sắm</a>
            <?php else: ?>
                <div class="shop__cart__table">
                    <table>
                        <thead>
                            <tr>
                                <th class="wishlist-col-check">
                                    <label class="wishlist-checkbox-wrap mb-0">
                                        <input type="checkbox" id="wishlist-check-all">
                                        <span class="wishlist-checkmark"></span>
                                        <span class="wishlist-check-label">Chọn tất cả</span>
                                    </label>
                                </th>
                                <th>Sản phẩm</th>
                                <th>Giá bán</th>
                                <th class="wishlist-col-action">Xem</th>
                                <th class="wishlist-col-action">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($wishlist_products as $item): ?>
                                <tr>
                                    <td class="wishlist-col-check">
                                        <label class="wishlist-checkbox-wrap mb-0">
                                            <input type="checkbox" class="wishlist-item-check" value="<?=$item['product_id']?>">
                                            <span class="wishlist-checkmark"></span>
                                        </label>
                                    </td>
                                    <td class="cart__product__item">
                                        <img src="upload/<?=$item['image']?>" alt="" style="width:90px; height:auto;">
                                        <div class="cart__product__item__title">
                                            <h6><?=$item['name']?></h6>
                                        </div>
                                    </td>
                                    <td class="cart__price"><?=$ProductModel->formatted_price($item['sale_price'])?></td>
                                    <td class="wishlist-col-action">
                                        <a class="site-btn" href="index.php?url=chitietsanpham&id_sp=<?=$item['product_id']?>&id_dm=<?=$item['category_id']?>">Chi tiết</a>
                                    </td>
                                    <td class="cart__close wishlist-col-action">
                                        <form action="index.php?url=yeu-thich" method="post">
                                            <input type="hidden" name="wishlist_action" value="remove">
                                            <input type="hidden" name="product_id" value="<?=$item['product_id']?>">
                                            <input type="hidden" name="redirect_to" value="index.php?url=yeu-thich">
                                            <button type="submit" style="border:none;background:none;">
                                                <span class="icon_close"></span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<style>
.wishlist-col-check {
    width: 170px;
    text-align: left;
}

.wishlist-col-action {
    width: 170px;
    text-align: center;
    vertical-align: middle;
}

.shop__cart__table table tbody tr td.wishlist-col-action,
.shop__cart__table table thead tr th.wishlist-col-action {
    text-align: center;
}

.cart__close.wishlist-col-action form {
    display: flex;
    justify-content: center;
}

.wishlist-checkbox-wrap {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    user-select: none;
}

.wishlist-checkbox-wrap input {
    display: none;
}

.wishlist-checkmark {
    width: 24px;
    height: 24px;
    border-radius: 7px;
    border: 2px solid #c6d0de;
    background: #fff;
    position: relative;
    transition: all .2s ease;
}

.wishlist-checkbox-wrap input:checked + .wishlist-checkmark {
    background: #0a68ff;
    border-color: #0a68ff;
}

.wishlist-checkbox-wrap input:checked + .wishlist-checkmark::after {
    content: "";
    position: absolute;
    left: 7px;
    top: 2px;
    width: 6px;
    height: 12px;
    border: solid #fff;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.wishlist-check-label {
    font-weight: 600;
    color: #2e3a48;
}
</style>

<script>
(function() {
    var checkAll = document.getElementById('wishlist-check-all');
    var bulkForm = document.getElementById('wishlist-bulk-remove-form');
    var bulkInputs = document.getElementById('wishlist-bulk-inputs');
    if (!checkAll || !bulkForm || !bulkInputs) return;

    var itemChecks = Array.prototype.slice.call(document.querySelectorAll('.wishlist-item-check'));
    if (!itemChecks.length) return;

    function updateBulkButton() {
        var checkedItems = itemChecks.filter(function(item) { return item.checked; });
        bulkForm.style.display = checkedItems.length > 0 ? 'block' : 'none';
        bulkInputs.innerHTML = '';
        checkedItems.forEach(function(item) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'product_ids[]';
            input.value = item.value;
            bulkInputs.appendChild(input);
        });
    }

    checkAll.addEventListener('change', function() {
        itemChecks.forEach(function(item) {
            item.checked = checkAll.checked;
        });
        updateBulkButton();
    });

    itemChecks.forEach(function(item) {
        item.addEventListener('change', function() {
            var allChecked = itemChecks.every(function(checkbox) { return checkbox.checked; });
            checkAll.checked = allChecked;
            updateBulkButton();
        });
    });

    updateBulkButton();
})();
</script>
