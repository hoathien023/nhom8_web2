<?php
    class ProductModel {
        public function ensure_cost_price_column() {
            $check_sql = "SELECT COUNT(*) FROM information_schema.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'products' 
                AND COLUMN_NAME = 'cost_price'";
            $exists = (int)pdo_query_value($check_sql);

            if ($exists === 0) {
                $alter_sql = "ALTER TABLE products ADD COLUMN cost_price INT NOT NULL DEFAULT 0 AFTER sale_price";
                pdo_execute($alter_sql);
            }
        }

        public function insert_product($category_id, $name, $image, $quantity, $price, $sale_price, $details, $short_description, $status) {
           
           $sql = "INSERT INTO products 
           (category_id, name, image, quantity, price, sale_price, details, short_description, status)
            VALUES (?,?,?,?,?,?,?,?,?)";

            pdo_execute($sql, $category_id, $name, $image, $quantity, $price, $sale_price, $details, $short_description, $status);
        }

        public function select_products() {
            $sql = "SELECT name FROM products WHERE status = 1";

            return pdo_query($sql);
        }

        public function update_product_not_active($product_id) {
            $sql = "UPDATE products SET status = 0 WHERE product_id = ?";

            pdo_execute($sql, $product_id);
        }

        public function update_product_active($product_id) {
            $sql = "UPDATE products SET status = 1 WHERE product_id = ?";

            pdo_execute($sql, $product_id);
        }

        function select_list_products($keyword, $id_danhmuc, $page, $perPage) {
            // Tính toán vị trí bắt đầu của kết quả trên trang hiện tại
            $start = ($page - 1) * $perPage;
        
            // Bắt đầu câu truy vấn SQL
            $sql = "SELECT * FROM products WHERE 1";
            
            // Thêm điều kiện tìm kiếm theo keyword
            if($keyword != '') {
                $sql .= " AND name LIKE '%" . $keyword . "%'";
            }
        
            // Thêm điều kiện tìm kiếm theo id_danhmuc
            if($id_danhmuc > 0) {
                $sql .= " AND category_id ='" . $id_danhmuc . "'";
            }
        
            // Sắp xếp theo id giảm dần
            $sql .= " AND status = 1 ORDER BY product_id DESC";
        
            // Thêm phần phân trang
            $sql .= " LIMIT " . $start . ", " . $perPage;
        
            return pdo_query($sql);
        }

        public function select_recycle_products() {
            $sql = "SELECT * FROM products WHERE status = 0 ORDER BY product_id DESC";

            return pdo_query($sql);
        }

        public function select_product_by_id($product_id) {
            $sql = "SELECT * FROM products WHERE product_id =?";

            return pdo_query_one($sql, $product_id);
        }

        public function select_products_for_import() {
            $this->ensure_cost_price_column();
            $sql = "SELECT product_id, name, quantity, cost_price FROM products WHERE status = 1 ORDER BY name ASC";

            return pdo_query($sql);
        }

        public function discount_percentage($price, $sale_price) {
            $discount_percentage = ($price - $sale_price) / $price * 100;

            $round__percentage = round($discount_percentage, 0)."%";
            return $round__percentage;
        }

        public function formatted_price($price) {
            $format = number_format($price, 0, ',', '.') . 'đ';
            return $format;
        }

        // Delete
        public function delete_product($product_id) {
            $sql = "DELETE FROM products WHERE product_id = ?";
            pdo_execute($sql, $product_id);
        }

        public function has_product_related_orders($product_id) {
            $sql = "SELECT COUNT(*) FROM orderdetails WHERE product_id = ?";
            $count = (int)pdo_query_value($sql, $product_id);

            return $count > 0;
        }

        public function update_product($category_id, $name, $image, $quantity, $price, $sale_price, $details, $short_description, $status, $product_id) {
            $sql = "UPDATE products SET 
            category_id = '".$category_id."', 
            name = '".$name."',";
    
            if ($image != '') {
                $sql .= " image = '".$image."',";
            }

            $sql .= " quantity = '".$quantity."', 
                    price = '".$price."', 
                    sale_price = '".$sale_price."', 
                    details = '".$details."', 
                    short_description = '".$short_description."',
                    status = '".$status."' 
                    WHERE product_id = ".$product_id;
            
            
            pdo_execute($sql);
        }

        public function apply_import_item($product_id, $import_qty, $import_cost) {
            $this->ensure_cost_price_column();
            $product = $this->select_product_by_id($product_id);

            if (!$product) {
                return;
            }

            $old_qty = (int)$product['quantity'];
            $old_cost = isset($product['cost_price']) ? (int)$product['cost_price'] : 0;
            $import_qty = (int)$import_qty;
            $import_cost = (int)$import_cost;
            $new_qty = $old_qty + $import_qty;

            if ($new_qty <= 0) {
                $new_cost = $import_cost;
            } elseif ($old_cost <= 0 || $old_qty <= 0) {
                $new_cost = $import_cost;
            } else {
                $new_cost = (int)round((($old_qty * $old_cost) + ($import_qty * $import_cost)) / $new_qty);
            }

            $sql = "UPDATE products 
                    SET quantity = ?, cost_price = ?, create_date = NOW()
                    WHERE product_id = ?";
            pdo_execute($sql, $new_qty, $new_cost, $product_id);
        }
    }

    $ProductModel = new ProductModel();
?>