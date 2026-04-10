

<?php
if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_list'])) {
        $cancel_oid = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        if ($cancel_oid > 0) {
            $cancel_kind = $OrderModel->cancel_order_by_user($user_id, $cancel_oid);
            if ($cancel_kind === 'bank') {
                $_SESSION['flash_order_cancel'] = 'bank';
            } elseif ($cancel_kind === 'cod') {
                $_SESSION['flash_order_cancel'] = 'cod';
            }
            header('Location: index.php?url=chi-tiet-don-hang&id=' . $cancel_oid);
            exit();
        }
    }
    $list_orders = $OrderModel->select_list_orders($user_id);
?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
                    <a href="index.php?url=thong-tin-tai-khoan">Tài khoản</a>
                    <span>Đơn mua</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="spad pt-4">
    <div class="container">
        <div class="order-page-head d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Đơn mua của bạn</h4>
            <span class="order-total-badge"><?=count($list_orders)?> đơn hàng</span>
        </div>

        <?php if (empty($list_orders)): ?>
            <div class="order-empty-card">
                <h5>Bạn chưa có đơn hàng nào</h5>
                <p class="mb-3">Hãy khám phá cửa hàng và chọn sản phẩm yêu thích của bạn.</p>
                <a href="index.php?url=cua-hang" class="site-btn">Mua sắm ngay</a>
            </div>
        <?php else: ?>
            <?php foreach ($list_orders as $value):
                extract($value);
                $list_products_buyed = $OrderModel->select_orderdetails_and_products($order_id);

                $pm_db = strtolower(trim((string)($payment_method ?? '')));
                if ($pm_db === '') {
                    $pm_db = 'cod';
                }
                $ps_list = (string)($payment_status ?? 'none');
                $deadline_row = $value['payment_deadline'] ?? null;
                $is_bank_flow = ($pm_db === 'bank')
                    || !empty($deadline_row)
                    || in_array($ps_list, array('pending', 'submitted', 'expired'), true);
                $pm_list = $is_bank_flow ? 'bank' : 'cod';

                $order_status = 'Chưa xác nhận';
                $status_class = 'pending';
                $is_waiting_bank_payment = ($pm_list === 'bank')
                    && ($ps_list === 'pending')
                    && ((int)$status === 1);
                if ($is_waiting_bank_payment) {
                    $order_status = 'Đang chờ thanh toán';
                    $status_class = 'pending';
                } elseif ($status == 2) {
                    $order_status = 'Đã xác nhận';
                    $status_class = 'confirmed';
                } elseif ($status == 3) {
                    $order_status = 'Đang giao';
                    $status_class = 'shipping';
                } elseif ($status == 4) {
                    $order_status = 'Giao thành công';
                    $status_class = 'success';
                } elseif ($status == 5) {
                    $order_status = 'Đã hủy';
                    $status_class = 'cancelled';
                }

                $can_cancel_cod = ((int)$status === 1 && $pm_list !== 'bank');
                $can_cancel_bank_pend = ((int)$status === 1 && $pm_list === 'bank' && $ps_list === 'pending');
                $can_cancel_bank_ok = ((int)$status === 2 && $pm_list === 'bank' && $ps_list === 'submitted');
                $can_cancel_on_list = $can_cancel_cod || $can_cancel_bank_pend || $can_cancel_bank_ok;
                $cancel_list_confirm_msg = ($pm_list === 'bank')
                    ? 'Bạn có chắc chắn muốn hủy đơn hàng? Quý khách vui lòng chờ, tiền sẽ hoàn lại về tài khoản trong vòng 24h làm việc.'
                    : 'Bạn có chắc chắn muốn hủy đơn hàng?';

                $date_formated = $BaseModel->date_format($date, '');
            ?>
            <article class="order-card">
                <div class="order-card-head">
                    <div class="order-head-left">
                        <span class="order-code">Mã đơn #<?=$order_id?></span>
                        <span class="order-date"><i class="fa fa-calendar-o"></i> <?=$date_formated?></span>
                    </div>
                    <span class="order-status-badge <?=$status_class?>"><?=$order_status?></span>
                </div>

                <div class="order-card-body">
                    <?php foreach ($list_products_buyed as $item): ?>
                        <div class="order-item-row">
                            <img src="upload/<?=$item['image']?>" alt="<?=$item['product_name']?>" class="order-item-image">
                            <div class="order-item-info">
                                <h6 class="mb-1"><?=$item['product_name']?></h6>
                                <span class="order-item-meta">Số lượng: x<?=$item['quantity']?></span>
                            </div>
                            <div class="order-item-price"><?=number_format($item['product_price'])?>₫</div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="order-card-foot">
                    <a href="index.php?url=cua-hang" class="btn order-btn-light">Mua thêm</a>
                    <div class="order-foot-right">
                        <span class="order-total-text">Thành tiền:</span>
                        <span class="order-total-value"><?=number_format($total)?>₫</span>
                        <?php if ($is_waiting_bank_payment): ?>
                            <a href="index.php?url=thanh-toan-ngan-hang&id=<?=$order_id?>" class="site-btn order-btn-detail">Thanh toán đơn hàng</a>
                        <?php endif; ?>
                        <?php if (!empty($can_cancel_on_list)): ?>
                            <form method="post" action="index.php?url=don-hang" class="d-inline-block mb-0" onsubmit="return confirm(<?=json_encode($cancel_list_confirm_msg, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)?>);">
                                <input type="hidden" name="order_id" value="<?=$order_id?>">
                                <button type="submit" name="cancel_order_list" value="1" class="site-btn order-btn-cancel-list">Hủy đơn hàng</button>
                            </form>
                        <?php endif; ?>
                        <a href="index.php?url=chi-tiet-don-hang&id=<?=$order_id?>" class="site-btn order-btn-detail">Xem chi tiết</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php } else { ?>
<div class="row" style="margin-bottom: 400px;">
    <div class="col-lg-12 col-md-12">
        <div class="container-fluid mt-5">
            <div class="row rounded justify-content-center mx-0 pt-5">
                <div class="col-md-6 text-center">
                    <h4 class="mb-4">Vui lòng đăng nhập để có thể sử dụng chức năng</h4>
                    <a class="btn btn-primary rounded-pill py-3 px-5" href="index.php?url=dang-nhap">Đăng nhập</a>
                    <a class="btn btn-secondary rounded-pill py-3 px-5" href="index.php">Trang chủ</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div style="margin-bottom: 80px;"></div>
<style>
.order-page-head h4 {
    font-size: 30px;
    margin: 0;
}

.order-total-badge {
    background: #eaf2ff;
    color: #0a68ff;
    border-radius: 999px;
    padding: 6px 14px;
    font-weight: 600;
    font-size: 14px;
}

.order-empty-card {
    background: #fff;
    border: 1px solid #e9eef5;
    border-radius: 14px;
    padding: 28px;
    text-align: center;
}

.order-card {
    background: #fff;
    border: 1px solid #e7edf5;
    border-radius: 14px;
    margin-bottom: 16px;
    overflow: hidden;
    box-shadow: 0 8px 22px rgba(0, 0, 0, 0.05);
}

.order-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 18px;
    border-bottom: 1px solid #edf1f6;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
}

.order-head-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.order-code {
    font-weight: 700;
    color: #1f2d3d;
}

.order-date {
    color: #5f7183;
    font-size: 14px;
}

.order-status-badge {
    border-radius: 999px;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 700;
}

.order-status-badge.pending { background: #fff4db; color: #b26a00; }
.order-status-badge.confirmed { background: #e9f2ff; color: #0a68ff; }
.order-status-badge.shipping { background: #e7fbff; color: #0e7a8f; }
.order-status-badge.success { background: #e8f9ee; color: #1c8d45; }
.order-status-badge.cancelled { background: #ffe9e9; color: #c62828; }

.order-card-body {
    padding: 12px 18px 4px;
}

.order-item-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 10px 0;
    border-bottom: 1px dashed #edf1f6;
}

.order-item-row:last-child {
    border-bottom: none;
}

.order-item-image {
    width: 64px;
    height: 64px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #ebeff5;
}

.order-item-info {
    flex: 1;
    min-width: 0;
}

.order-item-info h6 {
    font-size: 16px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.order-item-meta {
    font-size: 13px;
    color: #6c7d8f;
}

.order-item-price {
    font-weight: 700;
    color: #0a68ff;
    min-width: 100px;
    text-align: right;
}

.order-card-foot {
    padding: 14px 18px;
    border-top: 1px solid #edf1f6;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.order-foot-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.order-total-text {
    color: #4f6276;
}

.order-total-value {
    color: #d12a2a;
    font-size: 22px;
    font-weight: 700;
    margin-right: 4px;
}

.order-btn-light {
    background: #f3f6fb;
    border: 1px solid #e2e9f2;
    color: #4e6072;
    border-radius: 8px;
    padding: 8px 14px;
}

.order-btn-light:hover {
    background: #fff;
}

.order-btn-detail {
    border-radius: 8px;
    padding: 9px 14px;
    min-width: 120px;
    text-align: center;
}

.order-btn-cancel-list {
    background: #ef4444;
    border: none;
    border-radius: 8px;
    padding: 9px 14px;
    min-width: 120px;
    text-align: center;
    color: #fff;
    cursor: pointer;
}

.order-btn-cancel-list:hover {
    filter: brightness(0.95);
    color: #fff;
}

@media (max-width: 768px) {
    .order-card-head,
    .order-card-foot {
        flex-direction: column;
        align-items: flex-start;
    }

    .order-foot-right {
        justify-content: flex-start;
    }

    .order-item-price {
        min-width: auto;
    }
}
</style>