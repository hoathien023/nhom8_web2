<?php
    class ProductModel {
        public function calculate_sale_price_from_cost($cost_price, $profit_rate) {
            $cost_price = (float)$cost_price;
            $profit_rate = (float)$profit_rate;
            if ($cost_price < 0) {
                $cost_price = 0;
            }
            if ($profit_rate < 0) {
                $profit_rate = 0;
            }

            return (int)round($cost_price * (1 + ($profit_rate / 100)));
        }

        public function format_cost_price($cost_price) {
            return number_format((float)$cost_price, 3, ',', '.');
        }

        public function insert_product($category_id, $name, $unit, $image, $quantity, $cost_price, $profit_rate, $price, $sale_price, $details, $short_description, $status) {
           
           $sql = "INSERT INTO products 
           (category_id, name, unit, image, quantity, cost_price, profit_rate, price, sale_price, details, short_description, status)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

            pdo_execute($sql, $category_id, $name, $unit, $image, $quantity, $cost_price, $profit_rate, $price, $sale_price, $details, $short_description, $status);
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
            $sql = "SELECT product_id, name, quantity, cost_price FROM products WHERE status = 1 ORDER BY name ASC";

            return pdo_query($sql);
        }

        public function select_products_price_management($keyword = '', $category_id = 0, $status = -1) {
            $sql = "SELECT p.product_id, p.name, p.cost_price, p.profit_rate, p.price, p.sale_price, p.status, c.name AS category_name
                    FROM products p
                    LEFT JOIN categories c ON c.category_id = p.category_id
                    WHERE 1";
            $args = [];

            if ($keyword !== '') {
                $sql .= " AND p.name LIKE ?";
                $args[] = "%" . $keyword . "%";
            }
            if ((int)$category_id > 0) {
                $sql .= " AND p.category_id = ?";
                $args[] = (int)$category_id;
            }
            if ($status === 0 || $status === 1) {
                $sql .= " AND p.status = ?";
                $args[] = (int)$status;
            }

            $sql .= " ORDER BY p.product_id DESC";

            return pdo_query($sql, ...$args);
        }

        public function select_products_for_inventory_lookup() {
            $sql = "SELECT product_id, category_id, name, unit, quantity
                    FROM products
                    WHERE status = 1
                    ORDER BY name ASC";

            return pdo_query($sql);
        }

        public function estimate_stock_at_date($product_id, $target_date) {
            $product = $this->select_product_by_id($product_id);
            if (!$product) {
                return null;
            }

            $current_qty = (int)$product['quantity'];

            $import_after_sql = "SELECT COALESCE(SUM(wri.import_quantity), 0)
                                 FROM warehouse_receipt_items wri
                                 INNER JOIN warehouse_receipts wr ON wr.receipt_id = wri.receipt_id
                                 WHERE wri.product_id = ? AND wr.status = 1 AND wr.import_date > ?";
            $import_after = (int)pdo_query_value($import_after_sql, $product_id, $target_date);

            $export_after_sql = "SELECT COALESCE(SUM(od.quantity), 0)
                                 FROM orderdetails od
                                 INNER JOIN orders o ON o.order_id = od.order_id
                                 WHERE od.product_id = ? AND o.status = 4 AND DATE(o.date) > ?";
            $export_after = (int)pdo_query_value($export_after_sql, $product_id, $target_date);

            $estimated_qty = $current_qty + $export_after - $import_after;
            if ($estimated_qty < 0) {
                $estimated_qty = 0;
            }

            return [
                'product_id' => (int)$product['product_id'],
                'product_name' => $product['name'],
                'unit' => isset($product['unit']) ? $product['unit'] : '-',
                'target_date' => $target_date,
                'estimated_quantity' => $estimated_qty,
                'current_quantity' => $current_qty
            ];
        }

        public function get_import_export_report($from_date, $to_date, $keyword = '', $category_id = 0) {
            $sql = "SELECT
                        p.product_id,
                        p.name,
                        p.unit,
                        p.quantity AS current_quantity,
                        c.name AS category_name,
                        COALESCE(imp.total_import, 0) AS total_import,
                        COALESCE(exp.total_export, 0) AS total_export
                    FROM products p
                    LEFT JOIN categories c ON c.category_id = p.category_id
                    LEFT JOIN (
                        SELECT wri.product_id, SUM(wri.import_quantity) AS total_import
                        FROM warehouse_receipt_items wri
                        INNER JOIN warehouse_receipts wr ON wr.receipt_id = wri.receipt_id
                        WHERE wr.status = 1 AND wr.import_date BETWEEN ? AND ?
                        GROUP BY wri.product_id
                    ) imp ON imp.product_id = p.product_id
                    LEFT JOIN (
                        SELECT od.product_id, SUM(od.quantity) AS total_export
                        FROM orderdetails od
                        INNER JOIN orders o ON o.order_id = od.order_id
                        WHERE o.status = 4 AND DATE(o.date) BETWEEN ? AND ?
                        GROUP BY od.product_id
                    ) exp ON exp.product_id = p.product_id
                    WHERE 1";
            $args = [$from_date, $to_date, $from_date, $to_date];

            if ($keyword !== '') {
                $sql .= " AND p.name LIKE ?";
                $args[] = "%" . $keyword . "%";
            }
            if ((int)$category_id > 0) {
                $sql .= " AND p.category_id = ?";
                $args[] = (int)$category_id;
            }

            $sql .= " ORDER BY p.name ASC";

            return pdo_query($sql, ...$args);
        }

        public function get_low_stock_products($threshold, $keyword = '', $category_id = 0, $product_id = 0) {
            $sql = "SELECT p.product_id, p.name, p.unit, p.quantity, p.status, c.name AS category_name
                    FROM products p
                    LEFT JOIN categories c ON c.category_id = p.category_id
                    WHERE p.status = 1 AND p.quantity <= ?";
            $args = [(int)$threshold];

            if ((int)$product_id > 0) {
                $sql .= " AND p.product_id = ?";
                $args[] = (int)$product_id;
            }
            if ($keyword !== '') {
                $sql .= " AND p.name LIKE ?";
                $args[] = "%" . $keyword . "%";
            }
            if ((int)$category_id > 0) {
                $sql .= " AND p.category_id = ?";
                $args[] = (int)$category_id;
            }

            $sql .= " ORDER BY p.quantity ASC, p.name ASC";

            return pdo_query($sql, ...$args);
        }

        public function update_pricing($product_id, $cost_price, $profit_rate, $sale_price) {
            $product = $this->select_product_by_id($product_id);
            if (!$product) {
                return false;
            }

            $cost_price = (float)$cost_price;
            if ($cost_price < 0) {
                $cost_price = 0;
            }
            $profit_rate = (float)$profit_rate;
            if ($profit_rate < 0) {
                $profit_rate = 0;
            }
            $new_price = $this->calculate_sale_price_from_cost($cost_price, $profit_rate);
            $sale_price = (int)$sale_price;
            if ($sale_price <= 0 || $sale_price > $new_price) {
                $sale_price = $new_price;
            }

            $sql = "UPDATE products SET cost_price = ?, profit_rate = ?, price = ?, sale_price = ? WHERE product_id = ?";
            pdo_execute($sql, $cost_price, $profit_rate, $new_price, $sale_price, $product_id);
            return true;
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
            $order_sql = "SELECT COUNT(*) FROM orderdetails WHERE product_id = ?";
            $order_count = (int)pdo_query_value($order_sql, $product_id);
            if ($order_count > 0) {
                return true;
            }

            // Nếu sản phẩm đã từng xuất hiện trong phiếu nhập hoàn thành
            // thì không được xóa hẳn, chỉ chuyển sang ẩn.
            $import_sql = "SELECT COUNT(*)
                           FROM warehouse_receipt_items wri
                           INNER JOIN warehouse_receipts wr ON wr.receipt_id = wri.receipt_id
                           WHERE wri.product_id = ? AND wr.status = 1";
            $import_count = (int)pdo_query_value($import_sql, $product_id);

            return $import_count > 0;
        }

        public function update_product($category_id, $name, $unit, $image, $quantity, $cost_price, $profit_rate, $price, $sale_price, $details, $short_description, $status, $product_id) {
            $sql = "UPDATE products SET 
            category_id = '".$category_id."', 
            name = '".$name."',";
    
            if ($image != '') {
                $sql .= " image = '".$image."',";
            }

            $sql .= " unit = '".$unit."',
                    quantity = '".$quantity."', 
                    cost_price = '".$cost_price."',
                    profit_rate = '".$profit_rate."',
                    price = '".$price."', 
                    sale_price = '".$sale_price."', 
                    details = '".$details."', 
                    short_description = '".$short_description."',
                    status = '".$status."' 
                    WHERE product_id = ".$product_id;
            
            
            pdo_execute($sql);
        }

        public function apply_import_item($product_id, $import_qty, $import_cost) {
            $product = $this->select_product_by_id($product_id);

            if (!$product) {
                return;
            }

            $old_qty = (int)$product['quantity'];
            $old_cost = isset($product['cost_price']) ? (float)$product['cost_price'] : 0;
            $import_qty = (int)$import_qty;
            $import_cost = (float)$import_cost;
            $new_qty = $old_qty + $import_qty;

            if ($new_qty <= 0) {
                $new_cost = $import_cost;
            } elseif ($old_cost <= 0 || $old_qty <= 0) {
                $new_cost = $import_cost;
            } else {
                $new_cost = (($old_qty * $old_cost) + ($import_qty * $import_cost)) / $new_qty;
            }

            $profit_rate = isset($product['profit_rate']) ? (float)$product['profit_rate'] : 0;
            $new_price = $this->calculate_sale_price_from_cost($new_cost, $profit_rate);
            $old_sale_price = isset($product['sale_price']) ? (int)$product['sale_price'] : 0;
            $new_sale_price = ($old_sale_price > 0 && $old_sale_price <= $new_price) ? $old_sale_price : $new_price;

            $sql = "UPDATE products 
                    SET quantity = ?, cost_price = ?, price = ?, sale_price = ?, create_date = NOW()
                    WHERE product_id = ?";
            pdo_execute($sql, $new_qty, $new_cost, $new_price, $new_sale_price, $product_id);
        }
    }

    $ProductModel = new ProductModel();
?>