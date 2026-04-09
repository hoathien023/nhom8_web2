<?php
    class ProductModel {
        public function select_products_limit($limit) {
           $sql = "SELECT * FROM products WHERE status = 1 ORDER BY product_id DESC LIMIT $limit";

           return pdo_query($sql);
        }

        public function select_products_by_id($id) {
            $sql = "SELECT * FROM products WHERE product_id = ? AND status = 1";
 
            return pdo_query_one($sql, $id);
        }

        public function select_products_order_by($limit, $order_by) {
            $sql = "SELECT * FROM products WHERE status = 1 ORDER BY product_id $order_by LIMIT $limit";
 
            return pdo_query($sql);
        }

        public function select_cate_in_product($product_id) {
            $sql = "SELECT category_id FROM products WHERE product_id = ? AND status = 1";
 
            return pdo_query_one($sql, $product_id);
        }

        public function select_products_similar($id) {
            $sql = "SELECT * FROM products WHERE category_id = ? AND status = 1 ORDER BY product_id LIMIT 4";
 
            return pdo_query($sql, $id);
        }

        public function search_products($query) {
            $sql = "SELECT * FROM products WHERE name LIKE ? AND status = 1 ORDER BY product_id DESC";
 
            return pdo_query($sql, "%" . $query . "%");
        }

        public function search_products_by_price($from_price, $to_price) {
            $sql = "SELECT * FROM products WHERE sale_price BETWEEN ? AND ? AND status = 1 ORDER BY product_id DESC";
 
            return pdo_query($sql, $from_price, $to_price);
        }

        public function search_products_advanced($query, $category_id, $from_price, $to_price, $page = 1, $perPage = 9) {
            $sql = "SELECT * FROM products WHERE status = 1";
            $params = array();

            if ($query !== '') {
                $sql .= " AND name LIKE ?";
                $params[] = "%" . $query . "%";
            }

            if ((int)$category_id > 0) {
                $sql .= " AND category_id = ?";
                $params[] = (int)$category_id;
            }

            if ($from_price !== null) {
                $sql .= " AND sale_price >= ?";
                $params[] = (int)$from_price;
            }

            if ($to_price !== null) {
                $sql .= " AND sale_price <= ?";
                $params[] = (int)$to_price;
            }

            $start = max(0, ((int)$page - 1) * (int)$perPage);
            $sql .= " ORDER BY product_id DESC LIMIT " . $start . ", " . (int)$perPage;

            return pdo_query($sql, ...$params);
        }

        public function count_products_advanced($query, $category_id, $from_price, $to_price) {
            $sql = "SELECT COUNT(*) AS total FROM products WHERE status = 1";
            $params = array();

            if ($query !== '') {
                $sql .= " AND name LIKE ?";
                $params[] = "%" . $query . "%";
            }

            if ((int)$category_id > 0) {
                $sql .= " AND category_id = ?";
                $params[] = (int)$category_id;
            }

            if ($from_price !== null) {
                $sql .= " AND sale_price >= ?";
                $params[] = (int)$from_price;
            }

            if ($to_price !== null) {
                $sql .= " AND sale_price <= ?";
                $params[] = (int)$to_price;
            }

            return pdo_query_one($sql, ...$params);
        }

        public function select_products_by_ids_paginated($product_ids, $page = 1, $perPage = 9) {
            $ids = array_values(array_filter(array_map('intval', (array)$product_ids), function ($id) {
                return $id > 0;
            }));
            if (empty($ids)) {
                return array();
            }

            $page = max(1, (int)$page);
            $perPage = max(1, (int)$perPage);
            $start = ($page - 1) * $perPage;
            $placeholders = implode(',', array_fill(0, count($ids), '?'));

            $sql = "SELECT * FROM products
                    WHERE status = 1 AND product_id IN ($placeholders)
                    ORDER BY product_id DESC
                    LIMIT $start, $perPage";

            return pdo_query($sql, ...$ids);
        }

        public function count_products_by_ids($product_ids) {
            $ids = array_values(array_filter(array_map('intval', (array)$product_ids), function ($id) {
                return $id > 0;
            }));
            if (empty($ids)) {
                return array('total' => 0);
            }

            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql = "SELECT COUNT(*) AS total FROM products WHERE status = 1 AND product_id IN ($placeholders)";
            return pdo_query_one($sql, ...$ids);
        }

        public function get_min_max_prices() {
            $sql = "SELECT MIN(sale_price) AS min_price, MAX(sale_price) AS max_price FROM products WHERE status = 1";
        
            return pdo_query_one($sql);
        }

        public function select_all_products() {
            $sql = "SELECT * FROM products WHERE status = 1 ORDER BY product_id DESC";

            return pdo_query($sql);
        }

        public function select_products_by_cate($category_id) {
            $sql = "SELECT * FROM products WHERE category_id = ? AND status = 1";
 
            return pdo_query($sql, $category_id);
        }

        public function select_products_by_cate_paginated($category_id, $page, $perPage) {
            $page = max(1, (int)$page);
            $perPage = max(1, (int)$perPage);
            $start = ($page - 1) * $perPage;

            $sql = "SELECT * 
                    FROM products 
                    WHERE category_id = ? AND status = 1
                    ORDER BY product_id DESC
                    LIMIT $start, $perPage";

            return pdo_query($sql, $category_id);
        }

        public function count_products_by_cate($category_id) {
            $sql = "SELECT COUNT(*) AS total 
                    FROM products 
                    WHERE category_id = ? AND status = 1";

            return pdo_query_one($sql, $category_id);
        }

        function select_list_products($page, $perPage) {
            // Tính toán vị trí bắt đầu của kết quả trên trang hiện tại
            $start = ($page - 1) * $perPage;
        
            // Bắt đầu câu truy vấn SQL
            $sql = "SELECT * FROM products WHERE 1";
            
        
            // Sắp xếp theo id giảm dần
            $sql .= " AND status = 1 ORDER BY product_id DESC";
        
            // Thêm phần phân trang
            $sql .= " LIMIT " . $start . ", " . $perPage;
        
            return pdo_query($sql);
        }

        // Đếm sản phẩm
        public function count_products() {
            $sql = "SELECT COUNT(*) AS total FROM products WHERE status = 1";
            return pdo_query_one($sql);
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

        public function update_views($product_id ) {
            $sql = "UPDATE products SET views = views + 1 WHERE product_id  = ?";
            pdo_execute($sql, $product_id );
            
        }

        public function update_quantity_product($product_id, $quantity) {
            $sql = "UPDATE products SET quantity = quantity - ? WHERE product_id  = ?";
            pdo_execute($sql, $quantity , $product_id );
        }

        public function update_sell_quantity_product($product_id, $quantity) {
            $sql = "UPDATE products SET sell_quantity = sell_quantity + ? WHERE product_id  = ?";
            pdo_execute($sql, $quantity , $product_id );
        }

    }

    $ProductModel = new ProductModel();

?>