<?php
ob_start();

$sessionPath = __DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}

if (is_dir($sessionPath) && is_writable($sessionPath)) {
    session_save_path($sessionPath);
}

session_start();


require_once "models/pdo_library.php";
require_once "models/BaseModel.php";
require_once "models/ProductModel.php";
require_once "models/CategoryModel.php";
require_once "models/CustomerModel.php";
require_once "models/CommentModel.php";
require_once "models/CartModel.php";
require_once "models/OrderModel.php";
require_once "models/PostModel.php";
require_once "models/AddressModel.php";
define('BASE_URL', 'index.php?url=');
define('URL_MOMO', 'http://localhost/DUAN_TRAICAY/cam-on');
define('URL_ORDER', 'http://localhost/DUAN_TRAICAY/don-hang');

// Đồng bộ trạng thái khóa tài khoản theo DB cho mọi request phía user.
if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
    $current_user = $CustomerModel->get_user_by_id((int)$_SESSION['user']['id']);
    if (!$current_user || (int)$current_user['active'] !== 1) {
        unset($_SESSION['user']);
        $_SESSION['locked_message'] = 'Tài khoản đã bị khóa';
        header("Location: index.php?url=dang-nhap");
        exit();
    }
}

// AJAX cart handlers must run before rendering HTML.
if (
    isset($_GET['url']) && $_GET['url'] === 'gio-hang' &&
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    (isset($_POST['ajax_update_qty']) || isset($_POST['ajax_add_to_cart']))
) {
    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_SESSION['user'])) {
        echo json_encode(array('ok' => false, 'message' => 'Vui lòng đăng nhập'));
        exit();
    }

    $user_id = (int)$_SESSION['user']['id'];

    if (isset($_POST['ajax_update_qty'])) {
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if ($quantity < 1) $quantity = 1;

        $product_info = $ProductModel->select_products_by_id($product_id);
        if (!$product_info || (int)$product_info['quantity'] <= 0) {
            echo json_encode(array('ok' => false, 'message' => 'Sản phẩm không còn khả dụng'));
            exit();
        }
        if ($quantity > (int)$product_info['quantity']) {
            $quantity = (int)$product_info['quantity'];
        }
        $CartModel->update_cart($quantity, $product_id, $user_id);
        echo json_encode(array('ok' => true, 'quantity' => $quantity));
        exit();
    }

    if (isset($_POST['ajax_add_to_cart'])) {
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $product_quantity = isset($_POST['product_quantity']) ? (int)$_POST['product_quantity'] : 1;
        if ($product_quantity < 1) $product_quantity = 1;

        $product_info = $ProductModel->select_products_by_id($product_id);
        if (!$product_info) {
            echo json_encode(array('ok' => false, 'message' => 'Sản phẩm không tồn tại hoặc đã bị ẩn.'));
            exit();
        }
        if ((int)$product_info['quantity'] <= 0) {
            echo json_encode(array('ok' => false, 'message' => 'Sản phẩm đã hết hàng.'));
            exit();
        }

        $product_name = $product_info['name'];
        $product_price = $product_info['sale_price'];
        $product_image = $product_info['image'];
        $max_quantity = (int)$product_info['quantity'];

        $product = $CartModel->select_cart_by_id($product_id, $user_id);
        if ($product && is_array($product)) {
            $new_quantity = (int)$product['product_quantity'] + $product_quantity;
            if ($new_quantity > $max_quantity) {
                $new_quantity = $max_quantity;
            }
            $CartModel->update_cart($new_quantity, $product_id, $user_id);
        } else {
            if ($product_quantity > $max_quantity) {
                $product_quantity = $max_quantity;
            }
            $CartModel->insert_cart($product_id, $user_id, $product_name, $product_price, $product_quantity, $product_image);
        }

        echo json_encode(array('ok' => true, 'message' => 'Bạn đã thêm sản phẩm vào giỏ hàng thành công :3'));
        exit();
    }
}

require_once "components/head.php";
require_once "components/header.php";


if (!isset($_GET['url'])) {
    require_once "views/home.php";
} else {
    switch ($_GET['url']) {
        case 'trang-chu':

            require_once "views/home.php";
            break;
        case 'cua-hang':

            require_once "views/shop.php";
            break;
        case 'chitietsanpham':

            require_once "views/productdetail.php";
            break;
        case 'danh-muc-san-pham':

            require_once "views/shop-by-category.php";
            break;
        case 'lien-he':
            require_once "views/contact.php";
            break;
        case 'gio-hang':
            require_once "views/cart.php";
            break;
        case 'thanh-toan':
            require_once "views/checkout.php";
            break;
        case 'thanh-toan-2':
            require_once "views/checkout-address.php";
            break;
        case 'thanh-toan-momo':
            require_once "views/checkout/checkout_momo.php";
            break;
        case 'thanh-toan-momo-address':
            require_once "views/checkout/momo-address.php";
            break;
        case 'thanh-toan-momo-address-2':
            require_once "views/checkout/momo-address-2.php";
            break;
        case 'thanh-toan-ngan-hang':
            require_once "views/checkout/bank-transfer.php";
            break;
        case 'thanh-toan-dia-chi2':
            require_once "views/thanh-toan-dia-chi.php";
            break;

        case 'remove-address':
            require_once "views/remove-address.php";
            break;
        case 'cam-on':
            require_once "views/thanks.php";
            break;
        case 'don-hang':
            require_once "views/my-order.php";
            break;
        case 'chi-tiet-don-hang':
            require_once "views/my-orderdetails.php";
            break;
        // User
        case 'dang-nhap':
            require_once "views/user/login.php";
            break;
        case 'dang-ky':
            require_once "views/user/register.php";
            break;
        case 'dang-xuat':
            unset($_SESSION['user']);
            header("Location: index.php");
            break;
        case 'thong-tin-tai-khoan':
            require_once "views/user/user-infor.php";
            break;
        case 'ho-so':
            require_once "views/user/edit-profile.php";
            break;
        case 'them-dia-chi':
            require_once "views/user/add-address.php";
            break;
        case 'doi-mat-khau':
            require_once "views/user/change-password.php";
            break;
        case 'quen-mat-khau':
            require_once "views/user/forgot-password.php";
            break;
        case 'khoi-phuc-mat-khau':
            require_once "views/user/password-recovery.php";
            break;

        //Bài viết
        case 'bai-viet':
            require_once "views/blog/blogs.php";
            break;
        case 'chi-tiet-bai-viet':
            require_once "views/blog/blog-details.php";
            break;
        case 'danh-muc-bai-viet':
            require_once "views/blog/blog-by-category.php";
            break;
        //Bài viết
        case 'tim-kiem':
            require_once "views/search.php";
            break;

        default:
            require_once "views/not-page.php";
            break;
    }
}

require_once "components/minicart.php";

require_once "components/footer.php";



ob_end_flush();
?>
<br>
