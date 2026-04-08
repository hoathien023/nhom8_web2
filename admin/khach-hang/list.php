<?php
    $error = '';
    $success = '';
    $default_reset_password = '12345678';

    if (isset($_GET['action']) && isset($_GET['id'])) {
        $action = $_GET['action'];
        $user_id = (int)$_GET['id'];
        $user = $CustomerModel->get_user_by_id($user_id);

        if (!$user) {
            $error = 'Không tìm thấy tài khoản.';
        } else {
            $is_current_admin = isset($_SESSION['user_admin']['id']) && (int)$_SESSION['user_admin']['id'] === $user_id;

            if (($action === 'lock' || $action === 'unlock') && $is_current_admin) {
                $error = 'Không thể khóa tài khoản đang đăng nhập.';
            } elseif ($action === 'lock') {
                $CustomerModel->update_user_active($user_id, 0);
                $success = 'Đã khóa tài khoản thành công.';
            } elseif ($action === 'unlock') {
                $CustomerModel->update_user_active($user_id, 1);
                $success = 'Đã mở khóa tài khoản thành công.';
            } elseif ($action === 'reset-password') {
                $hashed_password = password_hash($default_reset_password, PASSWORD_DEFAULT);
                $CustomerModel->update_user_password($user_id, $hashed_password);
                $success = 'Đã reset mật khẩu về mặc định: ' . $default_reset_password;
            } else {
                $error = 'Hành động không hợp lệ.';
            }
        }
    }

    $list_users = $CustomerModel->select_all_users();
    $html_alert = $BaseModel->alert_error_success($error, $success);
?>

<!-- LIST PRODUCTS -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Danh sách tài khoản</h6>
            <a href="them-tai-khoan" class="btn btn-custom"><i class="fa fa-plus"></i> Thêm tài khoản</a>

        </div>
        <?=$html_alert?>

        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0" id="users-list">
                <thead>
                    <tr class="text-dark">

                        <th scope="col">#</th>
                        <th scope="col">Ảnh</th> 
                        <th scope="col">Họ tên</th> 
                        <th scope="col">Email</th> 
                        <th scope="col">Số điện thoại</th>   
                        <th scope="col">Vai trò</th>      
                        <th scope="col">Trạng thái</th>
                        <th scope="col">Chức năng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($list_users as $value) {
                        extract($value);
                        $i++;
                    
                    ?>
                    <tr>
                        <td><?=$i?></td>
                        <td>
                            <img style="max-width: 50px;" src="../upload/<?=$image?>" alt="">
                        </td>
                        <td><?=$full_name?></td>
                        <td>
                            <?=$email?>
                        </td>
                        <td> <?=$phone?> </td>
                        <td> <?php
                            if($role == 0) {
                                echo "Khách hàng";
                            }elseif($role == 1) {
                                echo "Nhân viên";
                            }
                            ?> 
                        </td>
                        <td>
                            <?php if ((int)$active === 1): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Đã khóa</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a
                                class="btn btn-sm btn-warning mb-1"
                                href="index.php?quanli=danh-sach-khach-hang&action=reset-password&id=<?=$user_id?>"
                                onclick="return confirm('Reset mật khẩu tài khoản này về mặc định 12345678?');"
                            >
                                Reset mật khẩu
                            </a>
                            <?php if ((int)$active === 1): ?>
                                <a
                                    class="btn btn-sm btn-danger mb-1"
                                    href="index.php?quanli=danh-sach-khach-hang&action=lock&id=<?=$user_id?>"
                                    onclick="return confirm('Bạn có chắc muốn khóa tài khoản này?');"
                                >
                                    Khóa tài khoản
                                </a>
                            <?php else: ?>
                                <a
                                    class="btn btn-sm btn-success mb-1"
                                    href="index.php?quanli=danh-sach-khach-hang&action=unlock&id=<?=$user_id?>"
                                    onclick="return confirm('Bạn có chắc muốn mở khóa tài khoản này?');"
                                >
                                    Mở khóa
                                </a>
                            <?php endif; ?>
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
<style>
    td {
        height: 50px;
    }
</style>