<?php
    $from_date = isset($_GET['from_date']) ? trim($_GET['from_date']) : '';
    $to_date = isset($_GET['to_date']) ? trim($_GET['to_date']) : '';
    $status_filter = isset($_GET['status']) ? (int)$_GET['status'] : -1;
    $ward_keyword = isset($_GET['ward']) ? trim($_GET['ward']) : '';
    $list_orders = $OrderModel->select_list_orders_admin($from_date, $to_date, $status_filter, $ward_keyword);
?>
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Danh sách đơn hàng</h6>
            <div class="d-flex align-items-center">
                <span style="margin-right: 10px; color: #111;">Xuất Exel:</span>
                <a href="xuat-exel" style="margin-right: 5px;" class="btn btn-custom ml-3"><i class="fas fa-download"></i> Tất cả</a>
            </div>
        </div>


        <form method="get" class="row g-2 mb-3">
            <input type="hidden" name="quanli" value="danh-sach-don-hang">
            <div class="col-lg-2 col-md-4">
                <input type="date" name="from_date" value="<?=$from_date?>" class="form-control">
            </div>
            <div class="col-lg-2 col-md-4">
                <input type="date" name="to_date" value="<?=$to_date?>" class="form-control">
            </div>
            <div class="col-lg-2 col-md-4">
                <select name="status" class="form-select">
                    <option value="-1" <?=$status_filter === -1 ? 'selected' : ''?>>Tất cả trạng thái</option>
                    <option value="1" <?=$status_filter === 1 ? 'selected' : ''?>>Chờ xác nhận</option>
                    <option value="2" <?=$status_filter === 2 ? 'selected' : ''?>>Đã xác nhận</option>
                    <option value="3" <?=$status_filter === 3 ? 'selected' : ''?>>Đang giao</option>
                    <option value="4" <?=$status_filter === 4 ? 'selected' : ''?>>Giao thành công</option>
                    <option value="5" <?=$status_filter === 5 ? 'selected' : ''?>>Đã hủy</option>
                </select>
            </div>
            <div class="col-lg-3 col-md-6">
                <input type="text" name="ward" value="<?=$ward_keyword?>" class="form-control" placeholder="Lọc theo phường (từ địa chỉ)">
            </div>
            <div class="col-lg-3 col-md-6 d-flex">
                <button type="submit" class="btn btn-custom me-2">Lọc</button>
                <a href="index.php?quanli=danh-sach-don-hang" class="btn btn-outline-secondary">Bỏ lọc</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0" id="orders-list">
                <thead>
                    <tr class="text-dark">

                        <th scope="col">#</th>
                        <th scope="col">Tên khách hàng</th>
                        <th scope="col">Ngày đặt</th>
                        <th scope="col">Tổng tiền</th>
                        <th scope="col">Trạng Thái</th>
                        <th scope="col">Chỉnh sửa</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $i = 0;
                    foreach ($list_orders as $value) {
                        extract($value);
                        $i++;
                        $formatted_date = $BaseModel->date_format($order_date, '');

                        //Trang thái đơn hàng
                        $order_status = '<a href="" class="btn btn-small btn-danger">Chờ xác nhận</a>';
                        if($status == 2) {
                            $order_status = '<a href="" class="btn btn-small btn-warning">Đã xác nhận</a>';
                        }elseif($status == 3) {
                            $order_status = '<a href="" class="btn btn-small btn-success">Đang giao</a>';
                        }elseif($status == 4) {
                            $order_status = '<a href="" class="btn btn-small btn-success">Giao thành công</a>';
                        }elseif($status == 5) {
                            $order_status = '<a href="" class="btn btn-small btn-secondary">Đã hủy</a>';
                        }
                    ?>
                    <tr>
                        <td><?=$i?></td>
                        <td class="td-name">
                            <?=$full_name?>
                        </td>
                        <td class="td-date">
                            <?=$formatted_date?>
                        </td>
                        <td class="text-dark" style="font-weight: 600;">
                            <?=number_format($total)?>₫
                        </td>
                        <td class="td-responsive-2"> 
                            <?=$order_status?>
                        </td>
                        <td class="td-responsive-2">
                        
                            <a class="btn-sm btn-success" href="index.php?quanli=cap-nhat-don-hang&id=<?=$order_id?>">Xem</a>
                            <a class="btn-sm btn-secondary" href="index.php?quanli=cap-nhat-don-hang&id=<?=$order_id?>">Sửa</a>                          
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
<style>
    td {
        height: 50px;
    }
</style>