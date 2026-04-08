<?php
$error = '';
$success = '';
$list_categories = $CategoryModel->select_all_categories();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_profit_rate'])) {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $cost_price = isset($_POST['cost_price']) ? (float)$_POST['cost_price'] : -1;
    $profit_rate = isset($_POST['profit_rate']) ? (float)$_POST['profit_rate'] : -1;
    $sale_price = isset($_POST['sale_price']) ? (int)$_POST['sale_price'] : 0;

    if ($product_id <= 0) {
        $error = 'Sản phẩm không hợp lệ.';
    } elseif ($cost_price < 0) {
        $error = 'Giá vốn phải lớn hơn hoặc bằng 0.';
    } elseif ($profit_rate < 0) {
        $error = 'Tỉ lệ lợi nhuận phải lớn hơn hoặc bằng 0.';
    } else {
        $updated = $ProductModel->update_pricing($product_id, $cost_price, $profit_rate, $sale_price);
        if ($updated) {
            $success = 'Cập nhật giá vốn, tỉ lệ lợi nhuận và giá bán thành công.';
        } else {
            $error = 'Không tìm thấy sản phẩm để cập nhật.';
        }
    }
}

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$status = isset($_GET['status']) ? (int)$_GET['status'] : -1;

$list_products = $ProductModel->select_products_price_management($keyword, $category_id, $status);
$html_alert = $BaseModel->alert_error_success($error, $success);
?>

<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Quản lý giá bán theo sản phẩm</h6>
        </div>
        <?=$html_alert?>

        <form method="get" class="row g-2 mb-3">
            <input type="hidden" name="quanli" value="quan-ly-gia-ban">
            <div class="col-lg-4">
                <input type="search" name="keyword" value="<?=$keyword?>" class="form-control" placeholder="Tìm theo tên sản phẩm">
            </div>
            <div class="col-lg-3">
                <select class="form-select" name="category_id">
                    <option value="0">Tất cả danh mục</option>
                    <?php foreach ($list_categories as $cate): ?>
                        <option value="<?=$cate['category_id']?>" <?=$category_id === (int)$cate['category_id'] ? 'selected' : ''?>>
                            <?=$cate['name']?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-3">
                <select class="form-select" name="status">
                    <option value="-1" <?=$status === -1 ? 'selected' : ''?>>Tất cả trạng thái</option>
                    <option value="1" <?=$status === 1 ? 'selected' : ''?>>Hiển thị</option>
                    <option value="0" <?=$status === 0 ? 'selected' : ''?>>Ẩn</option>
                </select>
            </div>
            <div class="col-lg-2 d-grid">
                <button type="submit" class="btn btn-custom">Tra cứu</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead>
                    <tr class="text-dark">
                        <th>#</th>
                        <th>Sản phẩm</th>
                        <th>Danh mục</th>
                        <th>ĐVT</th>
                        <th>Giá vốn</th>
                        <th>% lợi nhuận</th>
                        <th>Giá bán</th>
                        <th>Giá khuyến mãi</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list_products)): ?>
                        <tr>
                            <td colspan="10" class="text-center">Không có dữ liệu phù hợp.</td>
                        </tr>
                    <?php else: ?>
                        <?php $stt = 0; foreach ($list_products as $item): $stt++; ?>
                            <?php
                                $display_unit = isset($item['unit']) && $item['unit'] !== '' ? $item['unit'] : '-';
                                $form_id = 'price-form-' . (int)$item['product_id'];
                            ?>
                            <tr>
                                <td><?=$stt?></td>
                                <td><?=$item['name']?></td>
                                <td><?=$item['category_name']?></td>
                                <td><?=$display_unit?></td>
                                <td>
                                    <input type="number" step="0.001" min="0" name="cost_price" value="<?=$item['cost_price']?>" class="form-control" style="min-width:120px;" form="<?=$form_id?>" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="profit_rate" value="<?=$item['profit_rate']?>" class="form-control" style="min-width:100px;" form="<?=$form_id?>" required>
                                </td>
                                <td><?=number_format((int)$item['price'])?>₫</td>
                                <td>
                                    <input type="number" min="0" name="sale_price" value="<?=$item['sale_price']?>" class="form-control" style="min-width:120px;" form="<?=$form_id?>" required>
                                </td>
                                <td><?=((int)$item['status'] === 1) ? 'Hiển thị' : 'Ẩn'?></td>
                                <td>
                                    <form method="post" id="<?=$form_id?>">
                                        <input type="hidden" name="product_id" value="<?=$item['product_id']?>">
                                        <button type="submit" name="update_profit_rate" class="btn btn-sm btn-success">Cập nhật</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
