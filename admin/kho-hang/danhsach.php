<?php
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$status_filter = isset($_GET['status']) ? (int)$_GET['status'] : -1;
$list_warehouse = $WarehousemModel->select_receipts($keyword, $status_filter);
?>

<!-- LIST PRODUCTS -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Quản lý phiếu nhập kho</h6>
            <a href="them-hoa-don" class="btn btn-custom"><i class="fa fa-plus"></i> Tạo phiếu nhập</a>


        </div>

        <div class="row align-items-center mb-2">
            <form action="" method="get" class="col-lg-12 d-flex">
                <input type="hidden" name="quanli" value="kho-hang">
                <input type="search" name="keyword" class="form-control me-2" style="max-width: 350px;" placeholder="Tìm mã phiếu / ngày nhập" value="<?=$keyword?>">
                <select name="status" class="form-select me-2" style="max-width: 220px;">
                    <option value="-1" <?=$status_filter === -1 ? 'selected' : ''?>>Tất cả trạng thái</option>
                    <option value="0" <?=$status_filter === 0 ? 'selected' : ''?>>Nháp</option>
                    <option value="1" <?=$status_filter === 1 ? 'selected' : ''?>>Hoàn thành</option>
                </select>
                <button type="submit" class="btn btn-custom">Tìm kiếm</button>
            </form>
        </div>


        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0" id="khohang-list">
                <thead>
                    <tr class="text-dark">

                        <th scope="col">#</th>
                        <th scope="col">Mã phiếu</th>
                        <th scope="col">Ngày nhập</th>
                        <th scope="col">Số dòng SP</th>
                        <th scope="col">Tổng SL nhập</th>
                        <th scope="col">Trạng thái</th>
                        <th scope="col">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $index = 0; 
                    foreach ($list_warehouse as $value) {
                        $index++;
                       $date_formated = $BaseModel->date_format($value['import_date'], '');
                    ?>
                    <tr>

                        <td class="text-dark"><?=$index?></td>
                        <td class="text-dark" style="min-width: 200px;"><?=$value['receipt_code']?></td>
                        
                        <td class="text-dark"><?=$date_formated?></td>
                        <td class="text-dark"><?=$value['item_count']?></td>
                        <td class="text-dark"><?=$value['total_quantity']?></td>
                        <td class="text-dark">
                            <?php
                                if ((int)$value['status'] === 1) {
                            ?>
                            <span class="badge bg-success">Hoàn thành</span>
                            <?php
                                } else {
                            ?>
                            <span class="badge bg-warning">Nháp</span>
                            <?php
                                }
                            ?>

                        </td>
                        <td>
                            <a href="index.php?quanli=sua-hoa-don&id=<?=$value['receipt_id']?>" class="btn btn-sm btn-primary">Xem / Sửa</a>
                        </td>
                    </tr>
                    <?php 
                    }
                    ?>



                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- LIST PRODUCTS END -->