<?php
    $error = '';
    $success = '';
    $products = $ProductModel->select_products_for_import();
    $default_code = $WarehousemModel->next_receipt_code();

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create_receipt"])) {
        $receipt_code = trim($_POST["receipt_code"]);
        $import_date = trim($_POST["import_date"]);
        $note = trim($_POST["note"]);
        $product_ids = isset($_POST['product_id']) ? $_POST['product_id'] : [];
        $import_prices = isset($_POST['import_price']) ? $_POST['import_price'] : [];
        $import_quantities = isset($_POST['import_quantity']) ? $_POST['import_quantity'] : [];

        $items = [];
        $selected_product_ids = [];
        $has_duplicate_product = false;
        for ($i = 0; $i < count($product_ids); $i++) {
            $product_id = (int)$product_ids[$i];
            $import_price = (int)$import_prices[$i];
            $import_quantity = (int)$import_quantities[$i];

            if ($product_id <= 0 || $import_price <= 0 || $import_quantity <= 0) {
                continue;
            }
            if (isset($selected_product_ids[$product_id])) {
                $has_duplicate_product = true;
                continue;
            }
            $selected_product_ids[$product_id] = true;

            $items[] = [
                'product_id' => $product_id,
                'import_price' => $import_price,
                'import_quantity' => $import_quantity
            ];
        }

        if ($receipt_code === '' || $import_date === '' || count($items) === 0) {
            $error = 'Vui lòng nhập mã phiếu, ngày nhập và ít nhất 1 sản phẩm hợp lệ.';
        } elseif ($has_duplicate_product) {
            $error = 'Mỗi sản phẩm chỉ được xuất hiện 1 dòng trong cùng phiếu nhập.';
        } else {
            try {
                $new_id = $WarehousemModel->create_receipt($receipt_code, $import_date, $note, $items);
                header("Location: index.php?quanli=sua-hoa-don&id=" . $new_id . "&created=1");
                exit();
            } catch (Exception $e) {
                $error = 'Không tạo được phiếu nhập. Mã phiếu có thể đã tồn tại.';
            }
        }
    }

    $html_alert = $BaseModel->alert_error_success($error, $success);
?>

<div class="container-fluid pt-4" style="margin-bottom: 110px;">
    <form class="row g-4" action="" method="post">
        <div class="col-sm-12 col-xl-12">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">
                    <a href="index.php?quanli=kho-hang" class="link-not-hover">Kho hàng</a>
                    / Tạo phiếu nhập kho
                </h6>
                <?=$html_alert?>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="mb-1">Mã phiếu nhập</label>
                        <input name="receipt_code" type="text" class="form-control" value="<?=isset($_POST['receipt_code']) ? $_POST['receipt_code'] : $default_code?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="mb-1">Ngày nhập</label>
                        <input name="import_date" type="date" class="form-control" value="<?=isset($_POST['import_date']) ? $_POST['import_date'] : date('Y-m-d')?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="mb-1">Ghi chú</label>
                        <input name="note" type="text" class="form-control" value="<?=isset($_POST['note']) ? $_POST['note'] : ''?>" placeholder="Tùy chọn">
                    </div>
                </div>

                <div class="border rounded p-3 mt-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Chi tiết nhập hàng</h6>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="addItemRow()">+ Thêm sản phẩm</button>
                    </div>
                    <small class="text-muted d-block mb-2">Có ô tìm kiếm để lọc nhanh sản phẩm khi tạo phiếu.</small>
                    <div id="receipt-items"></div>
                </div>

                <div class="mt-3">
                    <input type="submit" name="create_receipt" value="Tạo phiếu nhập" class="btn btn-custom">
                </div>
            </div>
        </div>
    </form>
</div>

<template id="item-row-template">
    <div class="row g-2 align-items-end item-row border-top pt-3 mt-2">
        <div class="col-md-4">
            <label class="mb-1">Sản phẩm</label>
            <input type="text" class="form-control form-control-sm product-search mb-1" placeholder="Tìm sản phẩm...">
            <select name="product_id[]" class="form-select product-select" required>
                <option value="">-- Chọn sản phẩm --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?=$product['product_id']?>"><?=$product['name']?> (Tồn: <?=$product['quantity']?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="mb-1">Giá nhập</label>
            <input name="import_price[]" type="number" class="form-control" min="1" required>
        </div>
        <div class="col-md-3">
            <label class="mb-1">Số lượng nhập</label>
            <input name="import_quantity[]" type="number" class="form-control" min="1" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger w-100" onclick="removeItemRow(this)">Xóa dòng</button>
        </div>
    </div>
</template>

<script>
function addItemRow() {
    const container = document.getElementById('receipt-items');
    const tpl = document.getElementById('item-row-template');
    container.appendChild(tpl.content.cloneNode(true));
    bindSearchForLastRow();
}

function removeItemRow(button) {
    const row = button.closest('.item-row');
    if (row) row.remove();
}

function bindSearchForLastRow() {
    const rows = document.querySelectorAll('.item-row');
    const row = rows[rows.length - 1];
    if (!row) return;

    const input = row.querySelector('.product-search');
    const select = row.querySelector('.product-select');
    const options = Array.from(select.options).map(opt => ({ value: opt.value, text: opt.text, selected: opt.selected }));

    input.addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        const current = select.value;
        select.innerHTML = '';
        options.forEach(opt => {
            if (opt.value === '' || opt.text.toLowerCase().indexOf(keyword) !== -1) {
                const option = document.createElement('option');
                option.value = opt.value;
                option.text = opt.text;
                if (opt.value === current) {
                    option.selected = true;
                }
                select.appendChild(option);
            }
        });
    });
}

addItemRow();
</script>