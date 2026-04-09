<?php
    $error = array(
        'name' => '',
        'unit' => '',
        'image' => '',
        'quantity' => '',
        'cost_price' => '',
        'profit_rate' => '',
        'sale_price' => '',
    );
    $list_categories = $CategoryModel->select_all_categories();
    $list_products = $ProductModel->select_products();

    if(isset($_GET['id'])) {
        $product_id = $_GET['id'];

        $product = $ProductModel->select_product_by_id($product_id);
        if (!$product) {
            header("Location: index.php?quanli=danh-sach-san-pham");
            exit();
        }
        extract($product);
    }else {
        header("Location: index.php?quanli=danh-sach-san-pham");
        exit();
    }

    

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_product"])) {
        $name = trim($_POST["name"]);
        $category_id = $_POST["category_id"];
        $new_image = $_FILES["image"]['name'];

        $quantity = $_POST["quantity"];
        $cost_price = $_POST["cost_price"];
        $profit_rate = $_POST["profit_rate"];
        $sale_price = isset($_POST["sale_price"]) ? $_POST["sale_price"] : '';
        // Đơn vị tính cố định theo yêu cầu: Kg.
        $unit = 'Kg';
        $status = isset($_POST["status"]) ? (int)$_POST["status"] : 1;
        $details = isset($_POST["details"]) ? $_POST["details"] : '';
        $short_description = isset($_POST["short_description"]) ? $_POST["short_description"] : '';
        $price = $ProductModel->calculate_sale_price_from_cost($cost_price, $profit_rate);

        if(strlen($name) > 255) {
            $error['name']= 'Tên sản phẩm tối đa 255 ký tự';
        }

        if($cost_price <0 ) {
            $error['cost_price']= 'Giá vốn phải lớn hơn 0';
        }
        if($quantity <0 ) {
            $error['quantity']= 'Số lượng phải lớn hơn 0';
        }
        if($profit_rate < 0 ) {
            $error['profit_rate']= 'Tỉ lệ lợi nhuận phải lớn hơn hoặc bằng 0';
        }
        if($sale_price === '' || $sale_price < 0 ) {
            $error['sale_price']= 'Giá khuyến mãi phải lớn hơn hoặc bằng 0';
        } elseif ($sale_price > $price) {
            $error['sale_price']= 'Giá khuyến mãi không được lớn hơn giá thường';
        }
        

        if(empty(array_filter($error))) {
            $target_dir = "../upload/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {

            }

            try {
                $image_to_save = $new_image;
                if (empty($image_to_save)) {
                    $image_to_save = $product['image'];
                }
                $ProductModel->update_product($category_id, $name, $unit, $image_to_save, $quantity, $cost_price, $profit_rate, $price, $sale_price, $details, $short_description, $status, $product_id);

                setcookie('success_update', 'Cập nhật sản phẩm thành công', time() + 5, '/');
                header("Location: index.php?quanli=cap-nhat-san-pham&id=".$product_id);
                exit();
            } catch (Exception $e) {
                $error_message = $e->getMessage();
                echo 'Thêm sản phẩm thất bại: ' . $error_message;

            }

        }
        

    }

    $current_status = (int)$product['status'];
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = isset($name) ? $name : $product['name'];
        $unit = 'Kg';
        $cost_price = isset($cost_price) ? $cost_price : ($product['cost_price'] ?? 0);
        $profit_rate = isset($profit_rate) ? $profit_rate : ($product['profit_rate'] ?? 0);
        $price = isset($price) ? $price : $product['price'];
        $sale_price = isset($sale_price) ? $sale_price : $product['sale_price'];
        $quantity = isset($quantity) ? $quantity : $product['quantity'];
        $details = isset($details) ? $details : $product['details'];
        $short_description = isset($short_description) ? $short_description : $product['short_description'];
        $image = $product['image'];
        $current_status = isset($status) ? (int)$status : (int)$product['status'];
    }
    $success = '';
    
    if(isset($_COOKIE['success_update']) && !empty($_COOKIE['success_update'])) {
        $success = $_COOKIE['success_update'];

    }

    $html_alert = $BaseModel->alert_error_success('', $success);

?>
<!-- Form Start -->
<div class="container-fluid pt-4">
    <form class="row g-4" action="" method="post" enctype="multipart/form-data">

        <div class="col-sm-12 col-xl-9">

            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">
                    <a href="index.php?quanli=danh-sach-san-pham" class="link-not-hover">Sản phẩm</a>
                    / Cập nhật sản phẩm
                </h6>
                <?=$html_alert?>
                <label for="floatingInput">Tên sản phẩm</label>
                <div class="form-floating mb-3">
                    <input type="text" name="name" value="<?=$name?>" class="form-control" id="floatingInput" placeholder="Tên sản phẩm">
                    
                    <span class="text-danger" ><?=$error['name']?></span>
                </div>
                

                <label for="floatingInput">Giá bán thường (đ)</label>
                <div class="form-floating mb-3">
                    <input type="number" name="price" value="<?=$price?>" class="form-control" id="floatingInput" placeholder="Giá bán thường (đ)" readonly>
                </div>

                <label for="floatingInput">Giá khuyến mãi (đ)</label>
                <div class="form-floating mb-3">
                    <input type="number" name="sale_price" value="<?=$sale_price?>" class="form-control" id="floatingInput" placeholder="Giá khuyến mãi (đ)">
                    <span class="text-danger" ><?=$error['sale_price']?></span>
                </div>
                <small class="text-muted d-block mb-2">Giá bán tự tính = Giá vốn x (1 + % lợi nhuận).</small>
                <label for="floatingInput">Số lượng (nhập số)</label>
                <div class="form-floating mb-3">
                    <input type="number" value="<?=$quantity?>" name="quantity" class="form-control" id="floatingInput" placeholder="Số lượng">
                    <span class="text-danger" ><?=$error['quantity']?></span>
                </div>
                <label for="floatingInput">Giá vốn (đ)</label>
                <div class="form-floating mb-3">
                    <input type="number" value="<?=$cost_price?>" name="cost_price" class="form-control" id="floatingInput" placeholder="Giá vốn">
                    <span class="text-danger" ><?=$error['cost_price']?></span>
                </div>
                <label for="floatingInput">Tỉ lệ lợi nhuận (%)</label>
                <div class="form-floating mb-3">
                    <input type="number" value="<?=$profit_rate?>" name="profit_rate" class="form-control" id="floatingInput" placeholder="Tỉ lệ lợi nhuận">
                    <span class="text-danger" ><?=$error['profit_rate']?></span>
                </div>
                <label for="floatingInput">Đơn vị tính</label>
                <div class="form-floating mb-3">
                    <input type="text" value="Kg" class="form-control" id="floatingInput" placeholder="Đơn vị tính" readonly>
                    <input type="hidden" name="unit" value="Kg">
                </div>
                <label for="text-dark">Mô tả ngắn</label>
                <div class="form-floating mb-3">
                    <textarea name="short_description" class="form-control" placeholder="Mô tả ngắn" id="short_description">
                        <?=$short_description?>
                    </textarea>

                </div>
                <label for="floatingTextarea">Chi tiết sản phẩm</label>
                <div class="form-floating">
                    <textarea name="details" class="form-control" placeholder="Mô tả" id="product_details" style="height: 300px;">
                        <?=$details?>
                    </textarea>

                </div>


            </div>
        </div>


        <div class="col-sm-12 col-xl-3">
            <div class="bg-light rounded h-100 p-4">
                
                <div class="mb-3">
                    <label for="formFileSm" class="form-label">Hình ảnh (JPG, PNG, )</label>
                    <input style="background-color: #fff" class="form-control form-control-sm" name="image" id="formFileSm" type="file">
                    <div class="my-2">
                        <img src="../upload/<?=$image?>" style="width: 100%;" class="img-fluid" alt="">
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <select name="category_id" class="form-select" id="floatingSelect" required>
                        <?php
                            foreach ($list_categories as $cate) {
                                extract($cate);
                                if($cate['category_id'] == $product['category_id']) 
                                echo '<option value="'.$category_id.'" selected >'.$name.'</option>';
                                else
                                echo '<option value="'.$category_id.'">'.$name.'</option>';
                            }
                        ?>
                       
                        
                    </select>
                    <label for="floatingSelect">Chọn danh mục</label>
                </div>
                <div class="form-floating mb-3">
                    <select name="status" class="form-select" id="floatingSelectStatus">
                        <option value="1" <?=$current_status === 1 ? 'selected' : ''?>>Hiển thị</option>
                        <option value="0" <?=$current_status === 0 ? 'selected' : ''?>>Ẩn</option>
                    </select>
                    <label for="floatingSelectStatus">Hiện trạng</label>
                </div>
                <h6 class="mb-4">
                    <input name="update_product" type="submit" value="Cập nhật" class="btn btn-custom">
                    <a href="index.php?quanli=danh-sach-san-pham&xoa=<?=$product_id?>" onclick="return confirmDeletionTemp();" class="btn btn-custom">Xóa</a>            
                </h6>           


            </div>
        </div>

    </form>
</div>
<!-- Form End -->
<style>
    .ck-editor__editable[role="textbox"]:first-child {
        /* editing area */
        min-height: 300px;
    }


    .ck-content .image {
        /* block images */
        max-width: 80%;
        margin: 20px auto;
    }
</style>
