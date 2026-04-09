<?php   
    $list_categories = $CategoryModel->select_all_categories();
    $list_products = $ProductModel->select_products();

    $error = array(
        'name' => '',
        'sku' => '',
        'unit' => '',
        'image' => '',
        'quantity' => '',
        'cost_price' => '',
        'profit_rate' => '',
        'sale_price' => '',
    );

    $temp = array(
        'name' => '',
        'sku' => '',
        'image' => '',
        'unit' => 'Kg',
        'quantity' => '',
        'cost_price' => '',
        'profit_rate' => '20',
        'price' => '',
        'sale_price' => '',
        'status' => 1,
        'details' => '',   
        'short_description' => '',   
    );

    $success = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["themsanpham"])) {
        $name = trim($_POST["name"]);
        $sku = isset($_POST["sku"]) ? strtoupper(trim($_POST["sku"])) : '';
        $unit = trim($_POST["unit"]);
        $category_id = $_POST["category_id"];
        $image = $_FILES["image"]['name'];

        $quantity = $_POST["quantity"];
        $cost_price = $_POST["cost_price"];
        $profit_rate = $_POST["profit_rate"];
        $sale_price = isset($_POST["sale_price"]) ? $_POST["sale_price"] : '';
        $status = isset($_POST["status"]) ? (int)$_POST["status"] : 1;
        $details = isset($_POST["details"]) ? $_POST["details"] : '';
        $short_description = isset($_POST["short_description"]) ? $_POST["short_description"] : '';
        $price = $ProductModel->calculate_sale_price_from_cost($cost_price, $profit_rate);

        
        
        //Kiểm tra tên sản phẩm đã tồn tại chưa
        foreach ($list_products as $value) {
            if ($value['name'] == $name) {
                $error['name']= 'Tên sản phẩm đã tồn tại.<br>';
                break; 
            }
        }

        if(empty($name)) {
            $error['name']= 'Tên sản phẩm không được để trống';
        }

        if(strlen($name) > 255) {
            $error['name']= 'Tên sản phẩm tối đa 255 ký tự';
        }

        if (empty($sku)) {
            $error['sku'] = 'Mã sản phẩm (SKU) không được để trống';
        } elseif (strlen($sku) > 64) {
            $error['sku'] = 'SKU tối đa 64 ký tự';
        } elseif (!preg_match('/^[A-Z0-9\-_]+$/', $sku)) {
            $error['sku'] = 'SKU chỉ gồm chữ in hoa, số, dấu gạch nối hoặc gạch dưới';
        } elseif ($ProductModel->is_sku_exists($sku)) {
            $error['sku'] = 'SKU đã tồn tại';
        }

        if(empty($unit)) {
            $error['unit']= 'Đơn vị tính không được để trống';
        }

        if(strlen($unit) > 50) {
            $error['unit']= 'Đơn vị tính tối đa 50 ký tự';
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

        if(empty($image)) {
            $image = "default-product.jpg";
        }
        

        if(empty(array_filter($error))) {
                $target_dir = "../upload/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);

                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
  
                }

                try {
                    $ProductModel->insert_product($category_id, $name, $sku, $unit, $image, $quantity, $cost_price, $profit_rate, $price, $sale_price, $details, $short_description, $status);
                    $success = 'Thêm sản phẩm thành công';
                } catch (Exception $e) {
                    $error_message = $e->getMessage();
                    echo 'Thêm sản phẩm thất bại: ' . $error_message;

                    $success = 'Thêm sản phẩm thất bại';
                }

        }else {
            $temp['name'] = $name;
            $temp['sku'] = $sku;
            $temp['unit'] = $unit;
            $temp['cost_price'] = $cost_price;
            $temp['profit_rate'] = $profit_rate;
            $temp['price'] = $price;
            $temp['sale_price'] = $sale_price;
            $temp['quantity'] = $quantity;
            $temp['short_description'] = $short_description;
            $temp['details'] = $details;
            $temp['status'] = $status;

        }

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
                    / Thêm sản phẩm
                </h6>
                <?=$html_alert?>
                <label for="floatingInput">Tên sản phẩm</label>
                <div class="form-floating mb-3">
                    <input type="text" name="name" value="<?=$temp['name']?>" class="form-control" id="floatingInput" placeholder="Tên sản phẩm">
                    
                    <span class="text-danger" ><?=$error['name']?></span>
                </div>

                <label for="floatingSku">Mã sản phẩm (SKU)</label>
                <div class="form-floating mb-3">
                    <input type="text" name="sku" value="<?=$temp['sku']?>" class="form-control" id="floatingSku" placeholder="Ví dụ: SP-TAO-001">
                    <span class="text-danger"><?=$error['sku']?></span>
                </div>
                

                <label for="floatingInput">Giá bán thường (đ)</label>
                <div class="form-floating mb-3">
                    <input type="number" value="<?=$temp['price']?>" class="form-control" id="floatingInput" placeholder="Giá bán thường (đ)" readonly>
                </div>

                <label for="floatingInput">Giá khuyến mãi (đ)</label>
                <div class="form-floating mb-3">
                    <input type="number" name="sale_price" value="<?=$temp['sale_price']?>" class="form-control" id="floatingInput" placeholder="Giá khuyến mãi (đ)">
                    <span class="text-danger" ><?=$error['sale_price']?></span>
                </div>
                <small class="text-muted d-block mb-2">Giá bán tự tính = Giá vốn x (1 + % lợi nhuận).</small>
                <label for="floatingInput">Số lượng (nhập số)</label>
                <div class="form-floating mb-3">
                    <input type="number" value="<?=$temp['quantity']?>" name="quantity" class="form-control" id="floatingInput" placeholder="Số lượng">
                    <span class="text-danger" ><?=$error['quantity']?></span>
                </div>
                <label for="floatingInput">Giá vốn (đ)</label>
                <div class="form-floating mb-3">
                    <input type="number" name="cost_price" value="<?=$temp['cost_price']?>" class="form-control" id="floatingInput" placeholder="Giá vốn">
                    <span class="text-danger" ><?=$error['cost_price']?></span>
                </div>
                <label for="floatingInput">Tỉ lệ lợi nhuận (%)</label>
                <div class="form-floating mb-3">
                    <input type="number" name="profit_rate" value="<?=$temp['profit_rate']?>" class="form-control" id="floatingInput" placeholder="Tỉ lệ lợi nhuận">
                    <span class="text-danger" ><?=$error['profit_rate']?></span>
                </div>
                <label for="floatingInput">Đơn vị tính</label>
                <div class="form-floating mb-3">
                    <input type="text" name="unit" value="<?=$temp['unit']?>" class="form-control" id="floatingInput" placeholder="Đơn vị tính">
                    <span class="text-danger" ><?=$error['unit']?></span>
                </div>
                <label for="text-dark">Mô tả ngắn</label>
                <div class="form-floating mb-3">
                    <textarea name="short_description" class="form-control" placeholder="Mô tả ngắn" id="short_description">
                        <?=$temp['short_description']?>
                    </textarea>

                </div>
                <label for="floatingTextarea">Chi tiết sản phẩm</label>
                <div class="form-floating">
                    <textarea name="details" class="form-control" placeholder="Mô tả" id="product_details" style="height: 300px;">
                        <?=$temp['details']?>
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
                        <img src="./img/testimonial-1.jpg" style="width: 100%;" class="img-fluid" alt="">
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <select name="category_id" class="form-select" id="floatingSelect" required>
                        
                        <?php foreach ($list_categories as $value) :?>

                            <option value="<?=$value['category_id']?>"><?=$value['name']?></option>
                        <?php endforeach ?>
                       
                        
                    </select>
                    <label for="floatingSelect">Chọn danh mục</label>
                </div>

                <div class="form-floating mb-3">
                    <select name="status" class="form-select" id="floatingSelectStatus">
                        <option value="1" <?=$temp['status'] == 1 ? 'selected' : ''?>>Hiển thị</option>
                        <option value="0" <?=$temp['status'] == 0 ? 'selected' : ''?>>Ẩn</option>
                    </select>
                    <label for="floatingSelectStatus">Hiện trạng</label>
                </div>
                <h6 class="mb-4">
                    <input name="themsanpham" type="submit" value="Đăng" class="btn btn-custom">

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
