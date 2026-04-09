<?php
if (!isset($_SESSION['user'])) {
    header("Location: index.php?url=dang-nhap");
    exit();
}

$user_id = (int)$_SESSION['user']['id'];
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_SESSION['last_order_summary']['order_id'] ?? 0);
if ($order_id <= 0) {
    header("Location: index.php?url=don-hang");
    exit();
}

$OrderModel->cancel_expired_bank_transfer_orders($user_id);
$order_row = $OrderModel->get_order_by_id($user_id, $order_id);
if (!$order_row) {
    header("Location: index.php?url=don-hang");
    exit();
}
$session_is_bank_order = isset($_SESSION['last_order_summary']['order_id'], $_SESSION['last_order_summary']['payment_method'])
    && (int)$_SESSION['last_order_summary']['order_id'] === $order_id
    && (string)$_SESSION['last_order_summary']['payment_method'] === 'bank';
$order_is_bank = (($order_row['payment_method'] ?? 'cod') === 'bank');
$allow_bank_page = $order_is_bank || $session_is_bank_order;
if (!$allow_bank_page) {
    header("Location: index.php?url=don-hang");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_bank_transfer'])) {
    $OrderModel->cancel_expired_bank_transfer_orders($user_id);
    $OrderModel->mark_bank_transfer_submitted($user_id, $order_id);
    // Đảm bảo DB luôn chuyển sang "Đã xác nhận" kể cả khi thiếu cột payment_*.
    $OrderModel->force_update_order_status($user_id, $order_id, 2);
    header("Location: index.php?url=chi-tiet-don-hang&id=" . $order_id);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_bank_transfer'])) {
    $OrderModel->cancel_expired_bank_transfer_orders($user_id);
    $OrderModel->cancel_pending_bank_transfer_order($user_id, $order_id);
    // Đảm bảo DB luôn chuyển sang "Đã hủy" kể cả khi thiếu cột payment_*.
    $OrderModel->force_update_order_status($user_id, $order_id, 5);
    header("Location: index.php?url=chi-tiet-don-hang&id=" . $order_id);
    exit();
}

$deadline_ts = !empty($order_row['payment_deadline']) ? strtotime((string)$order_row['payment_deadline']) : 0;
if ($deadline_ts > 0 && $deadline_ts <= time() && (int)$order_row['status'] === 1 && ($order_row['payment_status'] ?? '') === 'pending') {
    $OrderModel->cancel_expired_bank_transfer_orders($user_id);
    header("Location: index.php?url=don-hang");
    exit();
}
// Chỉ chặn khi DB có luồng payment_status rõ ràng.
// Nếu DB cũ chưa có cột/payment_status khác chuẩn thì vẫn cho vào QR để thanh toán.
$has_tracking_status = in_array((string)($order_row['payment_status'] ?? ''), array('pending', 'submitted', 'expired', 'cancelled'), true);
if ($has_tracking_status && (string)$order_row['payment_status'] !== 'pending') {
    header("Location: index.php?url=don-hang");
    exit();
}

$order_summary = $_SESSION['last_order_summary'] ?? array();
$total = isset($order_summary['order_id']) && (int)$order_summary['order_id'] === $order_id
    ? (int)($order_summary['total'] ?? 0)
    : (int)$order_row['total'];
$bank_code = 'MB';
$bank_name = 'Ngân hàng Quân Đội (MB)';
$account_no = '123456789';
$account_name = '8 Fruits';
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
            <div class="col-lg-9">
                <div class="bank-transfer-card">
                    <div class="bank-transfer-head">
                        <h3>Thanh toán chuyển khoản ngân hàng</h3>
                        <p>Mã đơn #<?=$order_id?> - Vui lòng chuyển đúng nội dung để hệ thống đối soát nhanh.</p>
                        <div class="payment-deadline-box">
                            <span>Thời gian thanh toán còn lại:</span>
                            <strong id="bank-payment-countdown">10:00</strong>
                        </div>
                    </div>

                    <div class="bank-transfer-grid">
                        <div class="bank-qr-box">
                            <p class="small-title">Quét mã VietQR</p>
                            <img src="<?=$vietqr_url?>" alt="VietQR thanh toan" class="vietqr-image">
                            <p class="qr-caption">Mở app ngân hàng -> Quét mã -> Kiểm tra thông tin -> Xác nhận.</p>
                        </div>

                        <div class="bank-info-box">
                            <p class="small-title">Thông tin chuyển khoản</p>
                            <ul class="bank-info">
                                <li>
                                    <span>Ngân hàng</span>
                                    <strong><?=$bank_name?></strong>
                                </li>
                                <li>
                                    <span>Số tài khoản</span>
                                    <strong>
                                        <?=$account_no?>
                                        <button type="button" class="copy-btn js-copy-btn" data-copy="<?=$account_no?>">Copy</button>
                                    </strong>
                                </li>
                                <li>
                                    <span>Chủ tài khoản</span>
                                    <strong><?=$account_name?></strong>
                                </li>
                                <li>
                                    <span>Nội dung chuyển khoản</span>
                                    <strong class="text-primary">
                                        <?=$transfer_note?>
                                        <button type="button" class="copy-btn js-copy-btn" data-copy="<?=$transfer_note?>">Copy</button>
                                    </strong>
                                </li>
                                <li>
                                    <span>Số tiền cần chuyển</span>
                                    <strong class="text-danger">
                                        <?=$amount_text?>
                                        <button type="button" class="copy-btn js-copy-btn" data-copy="<?=$total?>">Copy</button>
                                    </strong>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="secure-note">
                        Bảo mật thanh toán: Chỉ chuyển tiền đúng thông tin trên màn hình này. Không cung cấp OTP, mã PIN, mật khẩu internet banking cho bất kỳ ai. Nếu quá 5 phút chưa thấy cập nhật, vui lòng liên hệ hỗ trợ kèm mã đơn hàng.
                    </div>
                    <div class="mt-3 d-flex gap-2 bank-actions">
                        <form method="post" action="" class="d-inline-block mb-0">
                            <button type="submit" name="confirm_bank_transfer" value="1" class="site-btn">Tôi đã chuyển khoản</button>
                        </form>
                        <form method="post" action="" class="d-inline-block mb-0">
                            <button type="submit" name="cancel_bank_transfer" value="1" class="site-btn btn-light">Hủy thanh toán</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.bank-transfer-card {
    border: 1px solid #e8edf5;
    border-radius: 16px;
    padding: 24px 22px;
    background: #fff;
    box-shadow: 0 12px 28px rgba(21, 40, 74, 0.08);
}
.bank-transfer-head h3 {
    margin-bottom: 6px;
}
.bank-transfer-head p {
    color: #52657c;
    margin-bottom: 16px;
}
.payment-deadline-box {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 10px;
    border: 1px solid #ffd6a8;
    background: #fff7ed;
    color: #9a3412;
    font-size: 14px;
    font-weight: 600;
}
.payment-deadline-box strong {
    font-size: 18px;
    color: #dc2626;
    letter-spacing: 0.5px;
}
.bank-transfer-grid {
    display: grid;
    grid-template-columns: 310px 1fr;
    gap: 14px;
    margin-bottom: 12px;
}
.bank-qr-box,
.bank-info-box {
    border: 1px solid #e8edf5;
    border-radius: 12px;
    background: #fbfdff;
    padding: 12px;
}
.small-title {
    margin-bottom: 10px;
    font-weight: 700;
    color: #24364d;
}
.vietqr-image {
    max-width: 280px;
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 8px;
    background: #fff;
}
.qr-caption {
    margin: 10px 0 0;
    font-size: 13px;
    color: #5d7087;
}
.bank-info {
    list-style: none;
    padding-left: 0;
    margin-bottom: 0;
    color: #111827;
}
.bank-info li {
    margin-bottom: 8px;
    border-bottom: 1px dashed #e8edf5;
    padding-bottom: 8px;
    display: flex;
    justify-content: space-between;
    gap: 10px;
}
.bank-info li:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
.bank-info li span {
    color: #5e7289;
}
.copy-btn {
    margin-left: 8px;
    border: 1px solid #cfe0ff;
    background: #edf4ff;
    color: #0a68ff;
    border-radius: 6px;
    padding: 2px 8px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
}
.copy-btn:hover {
    background: #dbeaff;
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
.bank-actions .site-btn {
    border-radius: 10px;
    font-weight: 700;
}
.btn-light {
    background: #f3f4f6;
    color: #111827;
    border: 1px solid #d9e2ef;
}
@media (max-width: 991px) {
    .bank-transfer-grid {
        grid-template-columns: 1fr;
    }
    .bank-qr-box {
        text-align: center;
    }
}
</style>

<script>
(function() {
    function showCopyToast(message, isError) {
        var old = document.getElementById('copy-toast');
        if (old) old.remove();
        var toast = document.createElement('div');
        toast.id = 'copy-toast';
        toast.textContent = message || 'Đã sao chép';
        toast.style.position = 'fixed';
        toast.style.top = '88px';
        toast.style.right = '18px';
        toast.style.zIndex = '99999';
        toast.style.padding = '10px 14px';
        toast.style.borderRadius = '10px';
        toast.style.fontWeight = '600';
        toast.style.color = '#fff';
        toast.style.boxShadow = '0 10px 24px rgba(0,0,0,.16)';
        toast.style.background = isError ? 'linear-gradient(90deg,#ef4444,#dc2626)' : 'linear-gradient(90deg,#16a34a,#22c55e)';
        document.body.appendChild(toast);
        setTimeout(function() {
            if (toast && toast.parentNode) toast.parentNode.removeChild(toast);
        }, 1400);
    }

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.js-copy-btn');
        if (!btn) return;
        e.preventDefault();
        var value = btn.getAttribute('data-copy') || '';
        if (!value) return;
        if (!navigator.clipboard || !navigator.clipboard.writeText) {
            showCopyToast('Trình duyệt không hỗ trợ copy tự động.', true);
            return;
        }
        navigator.clipboard.writeText(value).then(function() {
            showCopyToast('Đã sao chép: ' + value, false);
        }).catch(function() {
            showCopyToast('Không thể sao chép.', true);
        });
    });
})();

(function() {
    var countdownEl = document.getElementById('bank-payment-countdown');
    if (!countdownEl) return;

    var deadlineTs = <?=json_encode($deadline_ts > 0 ? $deadline_ts : (time() + 10 * 60))?>;
    function pad(num) {
        return num < 10 ? '0' + num : String(num);
    }
    function render() {
        var now = Math.floor(Date.now() / 1000);
        var remain = Math.max(0, deadlineTs - now);
        var mm = Math.floor(remain / 60);
        var ss = remain % 60;
        countdownEl.textContent = pad(mm) + ':' + pad(ss);
        if (remain <= 0) {
            countdownEl.textContent = '00:00';
            countdownEl.style.color = '#991b1b';
            setTimeout(function() {
                window.location.href = 'index.php?url=don-hang';
            }, 600);
            return;
        }
        setTimeout(render, 1000);
    }
    render();
})();
</script>
