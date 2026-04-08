<?php
    class WarehousemModel {
        public function next_receipt_code() {
            $today = date('Ymd');
            $prefix = "PNK-" . $today . "-";
            $sql = "SELECT receipt_code 
                    FROM warehouse_receipts 
                    WHERE receipt_code LIKE ? 
                    ORDER BY receipt_id DESC 
                    LIMIT 1";
            $last = pdo_query_one($sql, $prefix . "%");

            if (!$last) {
                return $prefix . "001";
            }

            $parts = explode('-', $last['receipt_code']);
            $running = isset($parts[2]) ? (int)$parts[2] : 0;
            $running++;

            return $prefix . str_pad((string)$running, 3, '0', STR_PAD_LEFT);
        }

        public function create_receipt($receipt_code, $import_date, $note, $items) {
            $conn = pdo_get_connection();
            try {
                $conn->beginTransaction();

                $sql = "INSERT INTO warehouse_receipts(receipt_code, import_date, note, status) VALUES(?,?,?,0)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$receipt_code, $import_date, $note]);
                $receipt_id = (int)$conn->lastInsertId();

                $item_sql = "INSERT INTO warehouse_receipt_items(receipt_id, product_id, import_price, import_quantity)
                             VALUES(?,?,?,?)";
                $item_stmt = $conn->prepare($item_sql);

                foreach ($items as $item) {
                    $item_stmt->execute([(int)$receipt_id, (int)$item['product_id'], (int)$item['import_price'], (int)$item['import_quantity']]);
                }

                $conn->commit();
                return $receipt_id;
            } catch (Exception $e) {
                if ($conn->inTransaction()) {
                    $conn->rollBack();
                }
                throw $e;
            }
        }

        public function update_receipt($receipt_id, $import_date, $note, $items) {
            $conn = pdo_get_connection();
            try {
                $conn->beginTransaction();

                $check_stmt = $conn->prepare("SELECT status FROM warehouse_receipts WHERE receipt_id = ? FOR UPDATE");
                $check_stmt->execute([$receipt_id]);
                $receipt = $check_stmt->fetch(PDO::FETCH_ASSOC);
                if (!$receipt || (int)$receipt['status'] === 1) {
                    $conn->rollBack();
                    return false;
                }

                $update_stmt = $conn->prepare("UPDATE warehouse_receipts SET import_date = ?, note = ? WHERE receipt_id = ?");
                $update_stmt->execute([$import_date, $note, $receipt_id]);

                $delete_stmt = $conn->prepare("DELETE FROM warehouse_receipt_items WHERE receipt_id = ?");
                $delete_stmt->execute([$receipt_id]);

                $item_stmt = $conn->prepare("INSERT INTO warehouse_receipt_items(receipt_id, product_id, import_price, import_quantity)
                                             VALUES(?,?,?,?)");
                foreach ($items as $item) {
                    $item_stmt->execute([(int)$receipt_id, (int)$item['product_id'], (int)$item['import_price'], (int)$item['import_quantity']]);
                }

                $conn->commit();
                return true;
            } catch (Exception $e) {
                if ($conn->inTransaction()) {
                    $conn->rollBack();
                }
                throw $e;
            }
        }

        public function complete_receipt($receipt_id, $ProductModel) {
            $conn = pdo_get_connection();
            try {
                $conn->beginTransaction();

                $check_stmt = $conn->prepare("SELECT status FROM warehouse_receipts WHERE receipt_id = ? FOR UPDATE");
                $check_stmt->execute([$receipt_id]);
                $receipt = $check_stmt->fetch(PDO::FETCH_ASSOC);
                if (!$receipt || (int)$receipt['status'] === 1) {
                    $conn->rollBack();
                    return false;
                }

                $items_stmt = $conn->prepare("SELECT product_id, import_price, import_quantity FROM warehouse_receipt_items WHERE receipt_id = ?");
                $items_stmt->execute([$receipt_id]);
                $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
                if (empty($items)) {
                    $conn->rollBack();
                    return false;
                }

                foreach ($items as $item) {
                    $product_stmt = $conn->prepare("SELECT quantity, cost_price, profit_rate, sale_price FROM products WHERE product_id = ? FOR UPDATE");
                    $product_stmt->execute([(int)$item['product_id']]);
                    $product = $product_stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$product) {
                        throw new Exception("Sản phẩm trong phiếu nhập không tồn tại.");
                    }

                    $old_qty = (int)$product['quantity'];
                    $old_cost = (float)$product['cost_price'];
                    $import_qty = (int)$item['import_quantity'];
                    $import_cost = (float)$item['import_price'];
                    $new_qty = $old_qty + $import_qty;

                    if ($new_qty <= 0) {
                        $new_cost = $import_cost;
                    } elseif ($old_cost <= 0 || $old_qty <= 0) {
                        $new_cost = $import_cost;
                    } else {
                        $new_cost = (($old_qty * $old_cost) + ($import_qty * $import_cost)) / $new_qty;
                    }

                    $profit_rate = isset($product['profit_rate']) ? (float)$product['profit_rate'] : 0;
                    $new_price = $ProductModel->calculate_sale_price_from_cost($new_cost, $profit_rate);
                    $old_sale_price = isset($product['sale_price']) ? (int)$product['sale_price'] : 0;
                    $new_sale_price = ($old_sale_price > 0 && $old_sale_price <= $new_price) ? $old_sale_price : $new_price;

                    $update_product_stmt = $conn->prepare("UPDATE products SET quantity = ?, cost_price = ?, price = ?, sale_price = ?, create_date = NOW() WHERE product_id = ?");
                    $update_product_stmt->execute([$new_qty, $new_cost, $new_price, $new_sale_price, (int)$item['product_id']]);
                }

                $complete_stmt = $conn->prepare("UPDATE warehouse_receipts SET status = 1 WHERE receipt_id = ?");
                $complete_stmt->execute([$receipt_id]);

                $conn->commit();
                return true;
            } catch (Exception $e) {
                if ($conn->inTransaction()) {
                    $conn->rollBack();
                }
                throw $e;
            }
        }

        public function select_receipts($keyword = '', $status = -1) {
            $sql = "SELECT wr.*, 
                        COUNT(wri.item_id) AS item_count,
                        COALESCE(SUM(wri.import_quantity), 0) AS total_quantity
                    FROM warehouse_receipts wr
                    LEFT JOIN warehouse_receipt_items wri ON wri.receipt_id = wr.receipt_id
                    WHERE 1";
            $args = [];

            if ($keyword !== '') {
                $sql .= " AND (wr.receipt_code LIKE ? OR DATE_FORMAT(wr.import_date, '%d-%m-%Y') LIKE ?)";
                $args[] = "%" . $keyword . "%";
                $args[] = "%" . $keyword . "%";
            }

            if ($status === 0 || $status === 1) {
                $sql .= " AND wr.status = ?";
                $args[] = $status;
            }

            $sql .= " GROUP BY wr.receipt_id ORDER BY wr.receipt_id DESC";

            return pdo_query($sql, ...$args);
        }

        public function select_receipt_by_id($receipt_id) {
            $sql = "SELECT * FROM warehouse_receipts WHERE receipt_id = ?";

            return pdo_query_one($sql, $receipt_id);
        }

        public function select_receipt_items($receipt_id) {
            $sql = "SELECT wri.*, p.name AS product_name
                    FROM warehouse_receipt_items wri
                    LEFT JOIN products p ON p.product_id = wri.product_id
                    WHERE wri.receipt_id = ?
                    ORDER BY wri.item_id ASC";

            return pdo_query($sql, $receipt_id);
        }
    }

    $WarehousemModel = new WarehousemModel();
?>