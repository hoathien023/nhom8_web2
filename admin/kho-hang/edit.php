<?php
    $error = '';
    $success = '';
    $products = $ProductModel->select_products_for_import();

    if (!isset($_GET['id'])) {
        header("Location: index.php?quanli=kho-hang");
        exit();
    }

    $receipt_id = (int)$_GET['id'];
    $receipt = $WarehousemModel->select_receipt_by_id($receipt_id);
    if (!$receipt) {
        header("Location: index.php?quanli=kho-hang");
        exit();
    }

    if (isset($_GET['created'])) {
        $success = 'Tạo phiếu nhập thành công.';
    }

    if (isset($_GET['done'])) {
        $success = 'Hoàn thành phiếu nhập kho và đã cập nhật tồn kho, giá vốn.';
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save_receipt'])) {
        if ((int)$receipt['status'] === 1) {
            $error = 'Phiếu đã hoàn thành, không thể chỉnh sửa.';
        } else {
            $import_date = trim($_POST['import_date']);
            $note = trim($_POST['note']);
            $product_ids = isset($_POST['product_id']) ? $_POST['product_id'] : [];
            $import_prices = isset($_POST['import_price']) ? $_POST['import_price'] : [];
            $import_quantities = isset($_POST['import_quantity']) ? $_POST['import_quantity'] : [];

            $items = [];
            for ($i = 0; $i < count($product_ids); $i++) {
                $product_id = (int)$product_ids[$i];
                $import_price = (int)$import_prices[$i];
                $import_quantity = (int)$import_quantities[$i];
                if ($product_id <= 0 || $import_price <= 0 || $import_quantity <= 0) {
                    continue;
                }
                $items[] = [
                    'product_id' => $product_id,
                    'import_price' => $import_price,
                    'import_quantity' => $import_quantity
                ];
            }

            if ($import_date === '' || count($items) === 0) {
                $error = 'Vui lòng nhập ngày nhập và ít nhất 1 sản phẩm.';
            } else {
                $updated = $WarehousemModel->update_receipt($receipt_id, $import_date, $note, $items);
                if ($updated) {
                    $success = 'Cập nhật phiếu nhập thành công.';
                    $receipt = $WarehousemModel->select_receipt_by_id($receipt_id);
                } else {
                    $error = 'Không thể cập nhật phiếu nhập.';
                }
            }
        }
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['complete_receipt'])) {
        if ((int)$receipt['status'] === 1) {
            $error = 'Phiếu đã hoàn thành trước đó.';
        } else {
            try {
                $done = $WarehousemModel->complete_receipt($receipt_id, $ProductModel);
                if ($done) {
                    header("Location: index.php?quanli=sua-hoa-don&id=" . $receipt_id . "&done=1");
                    exit();
                }
                $error = 'Không thể hoàn thành phiếu nhập.';
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }

    $receipt_items = $WarehousemModel->select_receipt_items($receipt_id);
    $html_alert = $BaseModel->alert_error_success($error, $success);
?>

<div class="container-fluid pt-4" style="margin-bottom: 110px;">
    <form class="row g-4" action="" method="post">
        <div class="col-sm-12 col-xl-12">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">
                    <a href="index.php?quanli=kho-hang" class="link-not-hover">Kho hàng</a>
                    / Sửa phiếu nhập
                </h6>
                <?=$html_alert?>

                <div class="row mb-2">
                    <div class="col-md-4 mb-2">
                        <label class="mb-1">Mã phiếu</label>
                        <input type="text" class="form-control" value="<?=$receipt['receipt_code']?>" readonly>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="mb-1">Ngày nhập</label>
                        <input name="import_date" type="date" class="form-control" value="<?=$receipt['import_date']?>" <?=((int)$receipt['status'] === 1) ? 'readonly' : ''?> required>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="mb-1">Trạng thái</label>
                        <input type="text" class="form-control" value="<?=((int)$receipt['status'] === 1) ? 'Hoàn thành' : 'Nháp'?>" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="mb-1">Ghi chú</label>
                    <input name="note" type="text" class="form-control" value="<?=$receipt['note']?>" <?=((int)$receipt['status'] === 1) ? 'readonly' : ''?>>
                </div>

                <div class="border rounded p-3 mt-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Chi tiết sản phẩm</h6>
                        <?php if ((int)$receipt['status'] === 0): ?>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="addItemRow()">+ Thêm sản phẩm</button>
                        <?php endif; ?>
                    </div>
                    <div id="receipt-items"></div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <?php if ((int)$receipt['status'] === 0): ?>
                        <input type="submit" name="save_receipt" value="Lưu chỉnh sửa" class="btn btn-custom">
                        <button type="submit" name="complete_receipt" class="btn btn-success" onclick="return confirm('Xác nhận hoàn thành phiếu nhập?');">Hoàn thành phiếu nhập</button>
                    <?php endif; ?>
                    <a href="index.php?quanli=kho-hang" class="btn btn-secondary">Quay lại</a>
                </div>
            </div>
        </div>
    </form>
</div>

<template id="item-row-template">
    <div class="row g-2 align-items-end item-row border-top pt-3 mt-2">
        <div class="col-md-4">
            <label class="mb-1">Sản phẩm</label>
            <input type="text" class="form-control form-control-sm product-search mb-1" placeholder="Tìm sản phẩm..." <?=((int)$receipt['status'] === 1) ? 'disabled' : ''?>>
            <select name="product_id[]" class="form-select product-select" <?=((int)$receipt['status'] === 1) ? 'disabled' : 'required'?>>
                <option value="">-- Chọn sản phẩm --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?=$product['product_id']?>"><?=$product['name']?> (Tồn: <?=$product['quantity']?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="mb-1">Giá nhập</label>
            <input name="import_price[]" type="number" class="form-control" min="1" <?=((int)$receipt['status'] === 1) ? 'readonly' : 'required'?>>
        </div>
        <div class="col-md-3">
            <label class="mb-1">Số lượng nhập</label>
            <input name="import_quantity[]" type="number" class="form-control" min="1" <?=((int)$receipt['status'] === 1) ? 'readonly' : 'required'?>>
        </div>
        <div class="col-md-2">
            <?php if ((int)$receipt['status'] === 0): ?>
                <button type="button" class="btn btn-danger w-100" onclick="removeItemRow(this)">Xóa dòng</button>
            <?php endif; ?>
        </div>
    </div>
</template>

<script>
const initialItems = <?=json_encode($receipt_items)?>;
const isDone = <?=((int)$receipt['status'] === 1) ? 'true' : 'false'?>;

function addItemRow(item = null) {
    const container = document.getElementById('receipt-items');
    const tpl = document.getElementById('item-row-template');
    const clone = tpl.content.cloneNode(true);
    container.appendChild(clone);
    const row = container.lastElementChild;
    const select = row.querySelector('.product-select');
    const priceInput = row.querySelector('input[name="import_price[]"]');
    const qtyInput = row.querySelector('input[name="import_quantity[]"]');
    if (item) {
        select.value = item.product_id;
        priceInput.value = item.import_price;
        qtyInput.value = item.import_quantity;
    }
    bindSearchForRow(row);
}

function removeItemRow(button) {
    const row = button.closest('.item-row');
    if (row) row.remove();
}

function bindSearchForRow(row) {
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
                if (opt.value === current) option.selected = true;
                select.appendChild(option);
            }
        });
    });
}

if (initialItems.length > 0) {
    initialItems.forEach(item => addItemRow(item));
} else if (!isDone) {
    addItemRow();
}
</script>
