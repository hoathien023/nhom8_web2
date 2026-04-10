<?php
if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    $order_id = isset($_GET['id']) && $_GET['id'] > 0 ? (int)$_GET['id'] : 0;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order']) && $order_id > 0) {
        $cancel_kind = $OrderModel->cancel_order_by_user($user_id, $order_id);
        if ($cancel_kind === 'bank') {
            $_SESSION['flash_order_cancel'] = 'bank';
        } elseif ($cancel_kind === 'cod') {
            $_SESSION['flash_order_cancel'] = 'cod';
        }
        header("Location: index.php?url=chi-tiet-don-hang&id=" . $order_id);
        exit();
    }
    $list_orders = $OrderModel->getFullOrderInformation($user_id, $order_id);

    if (!is_array($list_orders) || count($list_orders) === 0) {
        header("Location: index.php?url=don-hang");
        exit();
    }

    $oh = $list_orders[0];
    $status = (int)($oh['order_row_status'] ?? 0);
    $payment_method = strtolower(trim((string)($oh['order_payment_method'] ?? '')));
    if ($payment_method === '') {
        $payment_method = 'cod';
    }
    $payment_status = (string)($oh['order_payment_status'] ?? 'none');
    $payment_deadline = $oh['order_payment_deadline'] ?? null;
    $note = (string)($oh['order_note'] ?? '');
    $order_date = $oh['order_date'] ?? '';
    $total = (int)($oh['total'] ?? 0);
    $order_address = (string)($oh['order_address'] ?? '');
    $order_phone = (string)($oh['order_phone'] ?? '');
    $full_name = (string)($oh['full_name'] ?? '');

    $can_cancel_cod_pending = ((int)$status === 1 && $payment_method !== 'bank');
    $can_cancel_bank_pending = ((int)$status === 1 && $payment_method === 'bank' && $payment_status === 'pending');
    $can_cancel_bank_confirmed = ((int)$status === 2 && $payment_method === 'bank' && $payment_status === 'submitted');
    $can_user_cancel_order = $can_cancel_cod_pending || $can_cancel_bank_pending || $can_cancel_bank_confirmed;
    $rebuy_product_ids = array_values(array_unique(array_map('intval', array_column($list_orders, 'product_id'))));
    $rebuy_query = implode(',', $rebuy_product_ids);

    $order_status = 'Chưa xác nhận';
    $status_class = 'pending';
    $is_waiting_bank_payment = ((string)($payment_method ?? '') === 'bank')
        && ((string)($payment_status ?? '') === 'pending')
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
?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
                    <a href="index.php?url=thong-tin-tai-khoan">Tài khoản</a>
                    <a href="index.php?url=don-hang">Đơn mua</a>
                    <span>Chi tiết đơn hàng</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="spad pt-4">
    <div class="container order-detail-wrap">
        <article class="order-detail-card">
            <?php
            if (!empty($_SESSION['flash_order_cancel'])) {
                $flash_cancel = $_SESSION['flash_order_cancel'];
                unset($_SESSION['flash_order_cancel']);
                if ($flash_cancel === 'bank') {
                    echo '<div class="alert alert-info order-cancel-flash mb-3" role="alert"><strong>Đơn hàng đã được hủy.</strong> Quý khách vui lòng chờ, <strong>tiền sẽ hoàn lại trong 24h</strong>!</div>';
                } else {
                    echo '<div class="alert alert-success order-cancel-flash mb-3" role="alert">Đơn hàng của bạn đã được <strong>hủy thành công</strong>.</div>';
                }
            }
            ?>
            <div class="order-detail-head">
                <div>
                    <h4>Chi tiết đơn hàng #<?=$order_id?></h4>
                    <p class="mb-0 text-muted">Theo dõi trạng thái và thông tin giao nhận đơn hàng.</p>
                </div>
                <span class="order-status-badge <?=$status_class?>"><?=$order_status?></span>
            </div>

            <?php
                $booking_date = $BaseModel->date_format($order_date, '');
                $delivery_date = $BaseModel->date_format($order_date, 5);
            ?>

            <div class="order-meta-grid">
                <div class="meta-item">
                    <p class="meta-label">Thời gian đặt hàng</p>
                    <p class="meta-value"><?=$booking_date?></p>
                </div>
                <div class="meta-item">
                    <p class="meta-label">Dự kiến giao hàng</p>
                    <p class="meta-value"><?=$delivery_date?></p>
                </div>
                <div class="meta-item">
                    <p class="meta-label">Trạng thái hiện tại</p>
                    <p class="meta-value"><?=$order_status?></p>
                </div>
            </div>

            <div class="track">
                <div class="step active"> <span class="icon"> <i class="fa fa-check text-black"></i> </span> <span class="text">Chờ xác nhận</span> </div>
                <div class="step <?php if($status == 2 || $status == 3 || $status == 4) echo 'active'?>">
                    <span class="icon"> <i class="fa fa-user text-black"></i> </span>
                    <span class="text text-black">Đã xác nhận</span>
                </div>
                <div class="step <?php if($status == 3 || $status == 4) echo 'active'?>">
                    <span class="icon"> <i class="fa fa-truck text-black"></i> </span> <span class="text text-black">Trên đường giao</span>
                </div>
                <div class="step <?php if($status == 4) echo 'active'?>">
                    <span class="icon"> <i class="fa fa-check text-black"></i> </span> <span class="text text-black">Giao thành công</span>
                </div>
            </div>

            <div class="order-detail-body row">
                <div class="col-lg-7">
                    <h5 class="section-title-order">Sản phẩm trong đơn</h5>
                    <?php foreach ($list_orders as $value):
                        extract($value);
                    ?>
                    <div class="order-detail-item">
                        <img src="upload/<?=$product_image?>" class="order-detail-item-img" alt="<?=$product_name?>">
                        <div class="order-detail-item-info">
                            <h6><?=$product_name?></h6>
                            <p class="mb-0">Số lượng: x<?=$quantity?></p>
                            <?php if ((int)$status === 4 && (int)$product_id > 0 && (int)$product_category_id > 0): ?>
                                <a href="index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$product_category_id?>#tabs-2" class="order-item-review-link">Đánh giá sản phẩm này</a>
                            <?php endif; ?>
                        </div>
                        <div class="order-detail-item-price"><?=number_format($price)?>₫</div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="col-lg-5">
                    <h5 class="section-title-order">Thông tin thanh toán</h5>
                    <div class="summary-card">
                        <div class="summary-row">
                            <span>Họ và tên</span>
                            <strong><?=$full_name?></strong>
                        </div>
                        <div class="summary-row">
                            <span>Địa chỉ giao hàng</span>
                            <strong><?=$order_address?></strong>
                        </div>
                        <div class="summary-row">
                            <span>Số điện thoại</span>
                            <strong><?=$order_phone?></strong>
                        </div>
                        <div class="summary-row">
                            <span>Tổng tiền hàng</span>
                            <strong><?=number_format($total)?>₫</strong>
                        </div>
                        <div class="summary-row">
                            <span>Phí vận chuyển</span>
                            <strong>Miễn phí</strong>
                        </div>
                        <div class="summary-row">
                            <span>Ghi chú</span>
                            <strong><?=($note !== '' ? $note : '-')?></strong>
                        </div>
                        <div class="summary-row">
                            <span>Phương thức thanh toán</span>
                            <strong><?=($payment_method === 'bank') ? 'Chuyển khoản ngân hàng' : 'Thanh toán khi nhận hàng (COD)'?></strong>
                        </div>
                        <?php if ($payment_method === 'bank' && (int)$status !== 5 && (int)$status !== 4): ?>
                        <div class="summary-row order-bank-refund-hint">
                            <span>Khi hủy đơn chuyển khoản: <strong>Quý khách vui lòng chờ, tiền sẽ hoàn lại trong 24h!</strong></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($payment_method === 'bank'): ?>
                        <div class="summary-row">
                            <span>Trạng thái thanh toán</span>
                            <strong>
                                <?php
                                    if ((string)($payment_status ?? '') === 'pending' && (int)$status === 1) {
                                        echo 'Đang chờ thanh toán';
                                    } elseif ((string)($payment_status ?? '') === 'submitted') {
                                        echo 'Đã gửi xác nhận chuyển khoản';
                                    } elseif ((int)$status === 5 && (string)$payment_status === 'cancelled' && $payment_method === 'bank') {
                                        echo 'Đã hủy — tiền sẽ hoàn lại trong 24h';
                                    } elseif ((string)($payment_status ?? '') === 'expired') {
                                        echo 'Quá hạn thanh toán';
                                    } elseif ((int)$status === 5) {
                                        echo 'Đã hủy';
                                    } else {
                                        echo 'Đang xử lý';
                                    }
                                ?>
                            </strong>
                        </div>
                        <?php if (!empty($payment_deadline)): ?>
                        <div class="summary-row">
                            <span>Hạn thanh toán</span>
                            <strong><?=$BaseModel->date_format($payment_deadline, '')?></strong>
                        </div>
                        <?php endif; ?>
                        <?php if ($is_waiting_bank_payment): ?>
                        <div class="summary-row">
                            <span>Thanh toán đơn hàng</span>
                            <strong><a href="index.php?url=thanh-toan-ngan-hang&id=<?=$order_id?>">Mở trang QR để thanh toán</a></strong>
                        </div>
                        <?php endif; ?>
                        <?php if ((int)$status === 5 && $payment_method === 'bank' && (string)$payment_status === 'cancelled'): ?>
                        <div class="summary-row order-refund-notice">
                            <span><strong>Quý khách vui lòng chờ, tiền sẽ hoàn lại trong 24h!</strong></span>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                        <div class="summary-row total">
                            <span>Thành tiền</span>
                            <strong><?=number_format($total)?>₫</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-detail-actions">
                <a href="index.php?url=don-hang" class="btn order-btn-light"><i class="fa fa-chevron-left"></i> Quay lại đơn mua</a>
                <div class="order-detail-actions-right">
                    <?php if ($is_waiting_bank_payment): ?>
                        <a href="index.php?url=thanh-toan-ngan-hang&id=<?=$order_id?>" class="site-btn">Thanh toán đơn hàng</a>
                    <?php endif; ?>
                    <?php if (!empty($can_user_cancel_order)): ?>
                        <?php
                        $cancel_confirm_msg = ($payment_method === 'bank')
                            ? 'Bạn có chắc chắn muốn hủy đơn hàng? Quý khách vui lòng chờ, tiền sẽ hoàn lại trong 24h!'
                            : 'Bạn có chắc chắn muốn hủy đơn hàng?';
                        ?>
                        <form method="post" action="" class="d-inline-block mb-0" onsubmit="return confirm(<?=json_encode($cancel_confirm_msg, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)?>);">
                            <button type="submit" name="cancel_order" value="1" class="site-btn order-btn-cancel-payment">Hủy đơn hàng</button>
                        </form>
                    <?php endif; ?>
                    <?php if ((int)$status === 4): ?>
                        <a href="index.php?url=lien-he&from_order=<?=$order_id?>" class="site-btn order-btn-complaint">Khiếu nại</a>
                    <?php endif; ?>
                    <?php if (!empty($rebuy_query)): ?>
                        <a href="index.php?url=tim-kiem&product_ids=<?=$rebuy_query?>" class="site-btn order-btn-rebuy">Mua lại sản phẩm</a>
                    <?php endif; ?>
                    <a href="index.php?url=cua-hang" class="site-btn">Mua thêm sản phẩm</a>
                </div>
            </div>
        </article>
    </div>
</section>

<?php
}
?>

<style>
.order-detail-wrap {
    margin-bottom: 80px;
}

.order-detail-card {
    background: #fff;
    border: 1px solid #e7edf5;
    border-radius: 14px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    padding: 20px;
}

.order-detail-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 14px;
}

.order-detail-head h4 {
    margin-bottom: 4px;
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

.order-meta-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 16px;
}

.meta-item {
    border: 1px solid #edf1f6;
    border-radius: 10px;
    padding: 12px;
    background: #fafcff;
}

.meta-label {
    margin-bottom: 4px;
    font-size: 13px;
    color: #6c7d8f;
}

.meta-value {
    margin: 0;
    font-weight: 600;
}

.section-title-order {
    margin: 16px 0 12px;
    font-size: 22px;
}

.order-detail-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px dashed #edf1f6;
}

.order-detail-item:last-child {
    border-bottom: none;
}

.order-detail-item-img {
    width: 64px;
    height: 64px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #ebeff5;
}

.order-detail-item-info {
    flex: 1;
}

.order-detail-item-info h6 {
    margin-bottom: 4px;
}

.order-detail-item-price {
    min-width: 100px;
    text-align: right;
    font-weight: 700;
    color: #0a68ff;
}

.summary-card {
    border: 1px solid #edf1f6;
    border-radius: 12px;
    background: #fcfdff;
    padding: 8px 14px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid #edf1f6;
}

.summary-row:last-child {
    border-bottom: none;
}

.summary-row span {
    color: #5b6d80;
}

.summary-row strong {
    color: #1f2d3d;
    text-align: right;
}

.summary-row.total strong {
    color: #d12a2a;
    font-size: 20px;
}

.order-detail-actions {
    margin-top: 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.order-detail-actions-right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.order-btn-review {
    background: #17a2b8;
}

.order-item-review-link {
    display: inline-block;
    margin-top: 8px;
    color: #17a2b8;
    font-size: 13px;
    font-weight: 700;
    text-decoration: underline;
}

.order-btn-complaint {
    background: #f59e0b;
}

.order-btn-cancel-payment {
    background: #ef4444;
}

.order-cancel-flash {
    border-radius: 10px;
    border: none;
}

.summary-row.order-refund-notice {
    flex-direction: column;
    align-items: flex-start;
    background: #f0f7ff;
    border-radius: 8px;
    padding: 12px;
    margin-top: 4px;
    border: 1px solid #cfe8ff;
}

.summary-row.order-refund-notice span {
    color: #1e3a5f;
    line-height: 1.5;
}

.summary-row.order-bank-refund-hint {
    flex-direction: column;
    align-items: flex-start;
    background: #f8f5ff;
    border-radius: 8px;
    padding: 10px 12px;
    margin-top: 2px;
    border: 1px solid #e4dcfa;
}

.summary-row.order-bank-refund-hint span {
    color: #4a3d6b;
    font-size: 14px;
    line-height: 1.45;
}

.order-btn-rebuy {
    background: #7c3aed;
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

@media (max-width: 992px) {
    .order-meta-grid {
        grid-template-columns: 1fr;
    }

    .order-detail-actions-right {
        justify-content: flex-start;
    }
}
</style>