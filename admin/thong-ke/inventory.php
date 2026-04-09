<?php
$list_categories = $CategoryModel->select_all_categories();
$products_for_lookup = $ProductModel->select_products_for_inventory_lookup();

$lookup_result = null;
$lookup_error = '';

$report_rows = [];
$report_error = '';

$low_stock_rows = [];
$low_stock_error = '';

// 1) Tra cứu tồn kho tại một thời điểm
$lookup_product_id = isset($_GET['lookup_product_id']) ? (int)$_GET['lookup_product_id'] : 0;
$lookup_date = isset($_GET['lookup_date']) ? trim($_GET['lookup_date']) : date('Y-m-d');
if (isset($_GET['do_lookup'])) {
    if ($lookup_product_id <= 0 || $lookup_date === '') {
        $lookup_error = 'Vui lòng chọn sản phẩm và ngày cần tra cứu.';
    } else {
        $lookup_result = $ProductModel->estimate_stock_at_date($lookup_product_id, $lookup_date);
        if (!$lookup_result) {
            $lookup_error = 'Không tìm thấy sản phẩm để tra cứu tồn kho.';
        }
    }
}

// 2) Báo cáo nhập - xuất theo khoảng thời gian
$report_from = isset($_GET['report_from']) ? trim($_GET['report_from']) : date('Y-m-01');
$report_to = isset($_GET['report_to']) ? trim($_GET['report_to']) : date('Y-m-d');
$report_keyword = isset($_GET['report_keyword']) ? trim($_GET['report_keyword']) : '';
$report_category_id = isset($_GET['report_category_id']) ? (int)$_GET['report_category_id'] : 0;
if (isset($_GET['do_report'])) {
    if ($report_from === '' || $report_to === '') {
        $report_error = 'Vui lòng nhập đầy đủ khoảng thời gian báo cáo.';
    } elseif ($report_from > $report_to) {
        $report_error = 'Ngày bắt đầu không được lớn hơn ngày kết thúc.';
    } else {
        $report_rows = $ProductModel->get_import_export_report($report_from, $report_to, $report_keyword, $report_category_id);
    }
}

// 3) Cảnh báo sắp hết hàng theo ngưỡng người dùng chỉ định
$low_threshold = isset($_GET['low_threshold']) ? (int)$_GET['low_threshold'] : 5;
$low_keyword = isset($_GET['low_keyword']) ? trim($_GET['low_keyword']) : '';
$low_product_id = isset($_GET['low_product_id']) ? (int)$_GET['low_product_id'] : 0;
$low_category_id = isset($_GET['low_category_id']) ? (int)$_GET['low_category_id'] : 0;
if (isset($_GET['do_low_stock'])) {
    if ($low_threshold < 0) {
        $low_stock_error = 'Ngưỡng cảnh báo không được âm.';
    } else {
        $low_stock_rows = $ProductModel->get_low_stock_products($low_threshold, $low_keyword, $low_category_id, $low_product_id);
    }
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="bg-light rounded p-4 mb-4">
        <h6 class="mb-3">Tra cứu tồn kho tại thời điểm</h6>
        <form method="get" class="row g-2 align-items-end">
            <input type="hidden" name="quanli" value="thong-ke-ton-kho">
            <input type="hidden" name="do_lookup" value="1">
            <div class="col-lg-5">
                <label class="mb-1">Sản phẩm</label>
                <select name="lookup_product_id" class="form-select" required>
                    <option value="">-- Chọn sản phẩm --</option>
                    <?php foreach ($products_for_lookup as $p): ?>
                        <option value="<?=$p['product_id']?>" <?=$lookup_product_id === (int)$p['product_id'] ? 'selected' : ''?>>
                            <?=$p['name']?> (Tồn hiện tại: <?=$p['quantity']?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-3">
                <label class="mb-1">Thời điểm (ngày)</label>
                <input type="date" name="lookup_date" value="<?=$lookup_date?>" class="form-control" required>
            </div>
            <div class="col-lg-2">
                <button type="submit" class="btn btn-custom w-100">Tra cứu</button>
            </div>
        </form>
        <?php if ($lookup_error !== ''): ?>
            <div class="alert alert-danger mt-3 mb-0"><?=$lookup_error?></div>
        <?php endif; ?>
        <?php if ($lookup_result): ?>
            <div class="alert alert-info mt-3 mb-0">
                <strong><?=$lookup_result['product_name']?></strong>:
                tồn ước tính tại ngày <strong><?=$lookup_result['target_date']?></strong> là
                <strong><?=$lookup_result['estimated_quantity']?></strong> <?=$lookup_result['unit']?>.
                (Tồn hiện tại: <?=$lookup_result['current_quantity']?>)
            </div>
        <?php endif; ?>
    </div>

    <div class="bg-light rounded p-4 mb-4">
        <h6 class="mb-3">Báo cáo tổng nhập - xuất theo khoảng thời gian</h6>
        <form method="get" class="row g-2 align-items-end mb-3">
            <input type="hidden" name="quanli" value="thong-ke-ton-kho">
            <input type="hidden" name="do_report" value="1">
            <div class="col-lg-2">
                <label class="mb-1">Từ ngày</label>
                <input type="date" name="report_from" value="<?=$report_from?>" class="form-control" required>
            </div>
            <div class="col-lg-2">
                <label class="mb-1">Đến ngày</label>
                <input type="date" name="report_to" value="<?=$report_to?>" class="form-control" required>
            </div>
            <div class="col-lg-3">
                <label class="mb-1">Sản phẩm</label>
                <input type="search" name="report_keyword" value="<?=$report_keyword?>" class="form-control" placeholder="Tên sản phẩm">
            </div>
            <div class="col-lg-3">
                <label class="mb-1">Danh mục</label>
                <select class="form-select" name="report_category_id">
                    <option value="0">Tất cả danh mục</option>
                    <?php foreach ($list_categories as $cate): ?>
                        <option value="<?=$cate['category_id']?>" <?=$report_category_id === (int)$cate['category_id'] ? 'selected' : ''?>>
                            <?=$cate['name']?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-2">
                <button type="submit" class="btn btn-custom w-100">Lập báo cáo</button>
            </div>
        </form>

        <?php if ($report_error !== ''): ?>
            <div class="alert alert-danger"><?=$report_error?></div>
        <?php endif; ?>

        <?php if (isset($_GET['do_report']) && $report_error === ''): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr class="text-dark">
                            <th>#</th>
                            <th>Sản phẩm</th>
                            <th>Danh mục</th>
                            <th>ĐVT</th>
                            <th>Tổng nhập</th>
                            <th>Tổng xuất (đơn đã giao)</th>
                            <th>Tồn hiện tại</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($report_rows)): ?>
                            <tr><td colspan="7" class="text-center">Không có dữ liệu trong khoảng thời gian đã chọn.</td></tr>
                        <?php else: ?>
                            <?php $i = 0; foreach ($report_rows as $row): $i++; ?>
                                <tr>
                                    <td><?=$i?></td>
                                    <td><?=$row['name']?></td>
                                    <td><?=$row['category_name']?></td>
                                    <td><?=isset($row['unit']) && $row['unit'] !== '' ? $row['unit'] : '-'?></td>
                                    <td><?=number_format((int)$row['total_import'])?></td>
                                    <td><?=number_format((int)$row['total_export'])?></td>
                                    <td><?=number_format((int)$row['current_quantity'])?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="bg-light rounded p-4">
        <h6 class="mb-3">Cảnh báo sản phẩm sắp hết hàng</h6>
        <form method="get" class="row g-2 align-items-end mb-3">
            <input type="hidden" name="quanli" value="thong-ke-ton-kho">
            <input type="hidden" name="do_low_stock" value="1">
            <div class="col-lg-2">
                <label class="mb-1">Ngưỡng cảnh báo</label>
                <input type="number" min="0" name="low_threshold" value="<?=$low_threshold?>" class="form-control" required>
            </div>
            <div class="col-lg-4">
                <label class="mb-1">Sản phẩm</label>
                <select class="form-select" name="low_product_id" id="low_product_id">
                    <option value="0">Tất cả sản phẩm</option>
                    <?php foreach ($products_for_lookup as $p): ?>
                        <option value="<?=$p['product_id']?>" data-category-id="<?=$p['category_id']?>" <?=$low_product_id === (int)$p['product_id'] ? 'selected' : ''?>>
                            <?=$p['name']?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-4">
                <label class="mb-1">Danh mục</label>
                <select class="form-select" name="low_category_id" id="low_category_id">
                    <option value="0">Tất cả danh mục</option>
                    <?php foreach ($list_categories as $cate): ?>
                        <option value="<?=$cate['category_id']?>" <?=$low_category_id === (int)$cate['category_id'] ? 'selected' : ''?>>
                            <?=$cate['name']?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-2">
                <button type="submit" class="btn btn-warning w-100">Cảnh báo</button>
            </div>
        </form>

        <?php if ($low_stock_error !== ''): ?>
            <div class="alert alert-danger"><?=$low_stock_error?></div>
        <?php endif; ?>

        <?php if (isset($_GET['do_low_stock']) && $low_stock_error === ''): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr class="text-dark">
                            <th>#</th>
                            <th>Sản phẩm</th>
                            <th>Danh mục</th>
                            <th>ĐVT</th>
                            <th>Tồn hiện tại</th>
                            <th>Mức cảnh báo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($low_stock_rows)): ?>
                            <tr><td colspan="6" class="text-center">Không có sản phẩm nào dưới ngưỡng cảnh báo.</td></tr>
                        <?php else: ?>
                            <?php $j = 0; foreach ($low_stock_rows as $row): $j++; ?>
                                <tr>
                                    <td><?=$j?></td>
                                    <td><?=$row['name']?></td>
                                    <td><?=$row['category_name']?></td>
                                    <td><?=isset($row['unit']) && $row['unit'] !== '' ? $row['unit'] : '-'?></td>
                                    <td><span class="badge bg-danger"><?=number_format((int)$row['quantity'])?></span></td>
                                    <td><?=number_format($low_threshold)?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    const categorySelect = document.getElementById('low_category_id');
    const productSelect = document.getElementById('low_product_id');
    if (!categorySelect || !productSelect) return;

    const allOptions = Array.from(productSelect.options).map(function(opt) {
        return {
            value: opt.value,
            text: opt.text,
            categoryId: opt.getAttribute('data-category-id') || '0'
        };
    });

    function rebuildProductOptions(selectedCategoryId, selectedProductId) {
        const allowAll = selectedCategoryId === '0';
        productSelect.innerHTML = '';
        allOptions.forEach(function(item) {
            if (item.value === '0' || allowAll || item.categoryId === selectedCategoryId) {
                const option = document.createElement('option');
                option.value = item.value;
                option.text = item.text;
                if (item.categoryId && item.categoryId !== '0') {
                    option.setAttribute('data-category-id', item.categoryId);
                }
                if (item.value === selectedProductId) {
                    option.selected = true;
                }
                productSelect.appendChild(option);
            }
        });

        if (!Array.from(productSelect.options).some(function(o) { return o.value === selectedProductId; })) {
            productSelect.value = '0';
        }
    }

    categorySelect.addEventListener('change', function() {
        rebuildProductOptions(this.value, productSelect.value);
    });

    productSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const categoryId = selected ? (selected.getAttribute('data-category-id') || '0') : '0';
        if (this.value !== '0' && categoryId !== '0') {
            categorySelect.value = categoryId;
            rebuildProductOptions(categoryId, this.value);
        } else if (this.value === '0') {
            categorySelect.value = '0';
            rebuildProductOptions('0', '0');
        } else {
            categorySelect.value = '0';
        }
    });

    rebuildProductOptions(categorySelect.value, productSelect.value);
})();
</script>
