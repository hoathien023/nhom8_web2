<?php
if (!isset($_SESSION['user'])) {
    header("Location: index.php?url=dang-nhap");
    exit();
}

$order_summary = $_SESSION['last_order_summary'] ?? null;
if (!$order_summary || empty($order_summary['order_id'])) {
    header("Location: index.php?url=thanh-toan");
    exit();
}

$order_id = (int)$order_summary['order_id'];
$total = (int)($order_summary['total'] ?? 0);
$bank_code = 'MB';
$bank_name = 'Ngân hàng Quân Đội (MB)';
$account_no = '123456789';
$account_name = 'KLEVER FRUIT';
$transfer_note = 'DH' . $order_id;
$amount_text = number_format($total) . ' VND';

$vietqr_url = 'https://img.vietqr.io/image/' . $bank_code . '-' . $account_no . '-compact2.png?amount=' . $total . '&addInfo=' . urlencode($transfer_note) . '&accountName=' . urlencode($account_name);
?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
                    <span>Thanh toán chuyển khoản</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="checkout spad">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bank-transfer-card">
                    <h4>Quét mã QR để thanh toán</h4>
                    <p class="text-muted mb-3">Mã đơn #<?=$order_id?> - Số tiền cần thanh toán: <strong><?=$amount_text?></strong></p>
                    <div class="text-center mb-3">
                        <img src="<?=$vietqr_url?>" alt="VietQR thanh toan" class="vietqr-image">
                    </div>
                    <ul class="bank-info">
                        <li><strong>Ngân hàng:</strong> <?=$bank_name?></li>
                        <li><strong>Số tài khoản:</strong> <?=$account_no?></li>
                        <li><strong>Chủ tài khoản:</strong> <?=$account_name?></li>
                        <li><strong>Nội dung CK:</strong> <?=$transfer_note?></li>
                    </ul>
                    <div class="secure-note">
                        Bảo mật thanh toán: Chỉ chuyển tiền đúng thông tin trên màn hình này. Không cung cấp OTP, mã PIN, mật khẩu internet banking cho bất kỳ ai.
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <a href="index.php?url=cam-on" class="site-btn">Tôi đã chuyển khoản</a>
                        <a href="index.php?url=don-hang" class="site-btn btn-light">Xem đơn hàng</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.bank-transfer-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    background: #fff;
}
.vietqr-image {
    max-width: 320px;
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 8px;
    background: #fff;
}
.bank-info {
    padding-left: 16px;
    color: #111827;
}
.bank-info li {
    margin-bottom: 6px;
}
.secure-note {
    margin-top: 10px;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px dashed #93c5fd;
    background: #eff6ff;
    color: #1d4ed8;
    font-size: 13px;
}
.btn-light {
    background: #f3f4f6;
    color: #111827;
}
</style>
