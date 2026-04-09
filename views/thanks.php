<?php
    $order_summary = $_SESSION['last_order_summary'] ?? null;
    $payment_text = 'Thanh toán khi nhận hàng (COD)';
    $payment_badge = 'COD';
    if (($order_summary['payment_method'] ?? '') === 'bank') {
        $payment_text = 'Chuyển khoản ngân hàng';
        $payment_badge = 'BANK';
    } elseif (($order_summary['payment_method'] ?? '') === 'momo') {
        $payment_text = 'Ví MoMo';
        $payment_badge = 'MOMO';
    }
?>

<section class="thanks-page">
    <div class="container">
        <article class="thanks-card">
            <div class="thanks-icon-wrap">
                <span class="thanks-icon">
                    <i class="fa fa-check"></i>
                </span>
            </div>

            <h2 class="thanks-title">Đặt hàng thành công!</h2>
            <p class="thanks-subtitle">
                Cảm ơn bạn đã tin tưởng KLEVER FRUIT. Đơn hàng của bạn đã được ghi nhận và đang chờ xử lý.
            </p>

            <?php if (is_array($order_summary)): ?>
                <div class="thanks-summary-grid">
                    <div class="summary-item">
                        <p class="label">Mã đơn hàng</p>
                        <p class="value">#<?= (int)$order_summary['order_id'] ?></p>
                    </div>
                    <div class="summary-item">
                        <p class="label">Phương thức</p>
                        <p class="value">
                            <span class="payment-pill"><?=$payment_badge?></span> <?=$payment_text?>
                        </p>
                    </div>
                    <div class="summary-item full">
                        <p class="label">Địa chỉ nhận hàng</p>
                        <p class="value"><?=htmlspecialchars((string)$order_summary['address'], ENT_QUOTES)?></p>
                    </div>
                    <div class="summary-item">
                        <p class="label">Số điện thoại</p>
                        <p class="value"><?=htmlspecialchars((string)$order_summary['phone'], ENT_QUOTES)?></p>
                    </div>
                    <div class="summary-item">
                        <p class="label">Tổng thanh toán</p>
                        <p class="value text-primary"><?=number_format((int)$order_summary['total'])?>đ</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="thanks-empty-note">
                    Thông tin tóm tắt đơn hàng không còn trong phiên làm việc. Bạn có thể vào mục đơn mua để xem chi tiết.
                </div>
            <?php endif; ?>

            <div class="thanks-actions">
                <a href="index.php?url=don-hang" class="site-btn">Xem đơn hàng</a>
                <a href="index.php?url=cua-hang" class="site-btn thanks-btn-light">Tiếp tục mua sắm</a>
            </div>
        </article>
    </div>
</section>

<?php if (isset($_SESSION['last_order_summary'])) { unset($_SESSION['last_order_summary']); } ?>

<style>
.thanks-page {
    padding: 28px 0 64px;
}

.thanks-card {
    max-width: 860px;
    margin: 0 auto;
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 18px;
    box-shadow: 0 16px 36px rgba(21, 40, 74, 0.08);
    padding: 28px 24px 24px;
    text-align: center;
}

.thanks-icon-wrap {
    margin-bottom: 12px;
}

.thanks-icon {
    width: 84px;
    height: 84px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 40px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    box-shadow: 0 12px 24px rgba(34, 197, 94, 0.28);
}

.thanks-title {
    font-size: 42px;
    margin-bottom: 8px;
}

.thanks-subtitle {
    margin: 0 auto 18px;
    color: #4d6077;
    max-width: 680px;
    font-size: 17px;
    line-height: 1.6;
}

.thanks-summary-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    text-align: left;
    margin-bottom: 18px;
}

.summary-item {
    border: 1px solid #e7edf6;
    border-radius: 12px;
    background: #fbfdff;
    padding: 12px 14px;
}

.summary-item.full {
    grid-column: 1 / -1;
}

.summary-item .label {
    margin: 0 0 2px;
    font-size: 13px;
    color: #647b95;
}

.summary-item .value {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #1f334a;
    line-height: 1.45;
}

.payment-pill {
    display: inline-block;
    vertical-align: middle;
    margin-right: 6px;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    color: #0a68ff;
    background: #e9f1ff;
}

.thanks-empty-note {
    border: 1px dashed #c8d7ea;
    border-radius: 12px;
    background: #f9fbff;
    color: #49607a;
    padding: 14px;
    margin-bottom: 18px;
}

.thanks-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
}

.thanks-btn-light {
    background: #f5f8fc;
    color: #1f334a;
    border: 1px solid #dbe5f2;
}

.thanks-btn-light:hover {
    background: #fff;
    color: #0a68ff;
}

@media (max-width: 767px) {
    .thanks-card {
        border-radius: 14px;
        padding: 20px 14px;
    }
    .thanks-title {
        font-size: 32px;
    }
    .thanks-summary-grid {
        grid-template-columns: 1fr;
    }
}
</style>