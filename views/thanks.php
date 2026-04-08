<div class="d-flex justify-content-center align-items-center" style="height: 400px; margin-bottom: 300px;">
    <div>
        <div class="mb-4 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="text-success" width="75" height="75" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
            </svg>
        </div>
        <div class="text-center">
            <h3 class="mb-2">Cảm ơn quý khách hàng đã mua hàng !</h3>
            <h5 class="mb-3">Rất mong quý khách hàng sẽ luôn luôn tin tưởng và ủng hồ FAHASA</h5>
            <?php if (!empty($_SESSION['last_order_summary'])) {
                $order_summary = $_SESSION['last_order_summary'];
                $payment_text = 'COD';
                if (($order_summary['payment_method'] ?? '') === 'bank') {
                    $payment_text = 'Chuyển khoản ngân hàng';
                } elseif (($order_summary['payment_method'] ?? '') === 'momo') {
                    $payment_text = 'Ví MoMo';
                }
            ?>
            <div class="mb-3 text-left" style="max-width: 460px; margin: 0 auto;">
                <p><strong>Mã đơn:</strong> #<?=$order_summary['order_id']?></p>
                <p><strong>Địa chỉ:</strong> <?=$order_summary['address']?></p>
                <p><strong>SĐT:</strong> <?=$order_summary['phone']?></p>
                <p><strong>Thanh toán:</strong> <?=$payment_text?></p>
                <p><strong>Tổng tiền:</strong> <?=number_format((int)$order_summary['total'])?>đ</p>
            </div>
            <?php unset($_SESSION['last_order_summary']); } ?>
            <a href="don-hang" class="btn btn-primary">Xem đơn hàng</a>
        </div>
    </div>
</div>