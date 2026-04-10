<?php
    class OrderModel{
        private $orders_column_cache = array();

        private function has_orders_column($column_name) {
            if (array_key_exists($column_name, $this->orders_column_cache)) {
                return $this->orders_column_cache[$column_name];
            }
            $sql = "SELECT COUNT(*) AS total
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE()
                      AND TABLE_NAME = 'orders'
                      AND COLUMN_NAME = ?";
            $row = pdo_query_one($sql, $column_name);
            $exists = ((int)($row['total'] ?? 0) > 0);
            $this->orders_column_cache[$column_name] = $exists;
            return $exists;
        }

        public function create_order_with_stock_validation($user_id, $total, $address, $phone, $note, $items, $payment_method = 'cod') {
            $conn = pdo_get_connection();
            try {
                $conn->beginTransaction();

                if (empty($items)) {
                    throw new Exception("Giỏ hàng trống.");
                }

                foreach ($items as $item) {
                    $product_id = (int)$item['product_id'];
                    $quantity = (int)$item['quantity'];
                    if ($product_id <= 0 || $quantity <= 0) {
                        throw new Exception("Dữ liệu sản phẩm không hợp lệ.");
                    }

                    $stmt = $conn->prepare("SELECT product_id, name, quantity, status, sale_price FROM products WHERE product_id = ? FOR UPDATE");
                    $stmt->execute([$product_id]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$product || (int)$product['status'] !== 1) {
                        throw new Exception("Có sản phẩm đã bị ẩn hoặc không tồn tại.");
                    }

                    if ((int)$product['quantity'] < $quantity) {
                        throw new Exception("Sản phẩm '" . $product['name'] . "' không đủ tồn kho.");
                    }
                }

                $columns = array('user_id', 'total', 'address', 'phone', 'note');
                $values = array($user_id, $total, $address, $phone, $note);

                $payment_method = strtolower(trim((string)$payment_method));
                if (!in_array($payment_method, array('cod', 'bank'), true)) {
                    $payment_method = 'cod';
                }

                if ($this->has_orders_column('payment_method')) {
                    $columns[] = 'payment_method';
                    $values[] = $payment_method;
                }
                if ($this->has_orders_column('payment_status')) {
                    $columns[] = 'payment_status';
                    $values[] = ($payment_method === 'bank') ? 'pending' : 'none';
                }
                if ($this->has_orders_column('payment_deadline')) {
                    $columns[] = 'payment_deadline';
                    $values[] = ($payment_method === 'bank') ? date('Y-m-d H:i:s', time() + 10 * 60) : null;
                }

                $placeholders = implode(',', array_fill(0, count($columns), '?'));
                $stmtOrder = $conn->prepare("INSERT INTO orders(" . implode(',', $columns) . ") VALUES($placeholders)");
                $stmtOrder->execute($values);
                $order_id = (int)$conn->lastInsertId();

                $stmtDetail = $conn->prepare("INSERT INTO orderdetails(order_id, product_id, quantity, price) VALUES(?,?,?,?)");
                $stmtQty = $conn->prepare("UPDATE products SET quantity = quantity - ?, sell_quantity = sell_quantity + ? WHERE product_id = ?");

                foreach ($items as $item) {
                    $product_id = (int)$item['product_id'];
                    $quantity = (int)$item['quantity'];
                    $price = (int)$item['price'];

                    $stmtDetail->execute([$order_id, $product_id, $quantity, $price]);
                    $stmtQty->execute([$quantity, $quantity, $product_id]);
                }

                // Chỉ xóa các sản phẩm đã mua trong lần checkout này, không xóa toàn bộ giỏ.
                $purchased_product_ids = array_values(array_unique(array_map(function ($item) {
                    return (int)$item['product_id'];
                }, $items)));
                $purchased_product_ids = array_values(array_filter($purchased_product_ids, function ($id) {
                    return $id > 0;
                }));
                if (!empty($purchased_product_ids)) {
                    $placeholders = implode(',', array_fill(0, count($purchased_product_ids), '?'));
                    $stmtDeleteCart = $conn->prepare("DELETE FROM carts WHERE user_id = ? AND product_id IN ($placeholders)");
                    $stmtDeleteCart->execute(array_merge([(int)$user_id], $purchased_product_ids));
                }

                $conn->commit();
                return $order_id;
            } catch (Exception $e) {
                if ($conn->inTransaction()) {
                    $conn->rollBack();
                }
                throw $e;
            }
        }

        public function select_order_id() {
            $sql = "SELECT order_id FROM orders ORDER BY date DESC LIMIT 1";

            return pdo_query_one($sql);
        }


        // Select thông tin đon hàng
        public function select_list_orders($user_id) {
            $this->cancel_expired_bank_transfer_orders((int)$user_id);
            $payment_method_select = $this->has_orders_column('payment_method') ? "payment_method" : "'cod' AS payment_method";
            $payment_status_select = $this->has_orders_column('payment_status') ? "payment_status" : "'none' AS payment_status";
            $deadline_select = $this->has_orders_column('payment_deadline') ? "payment_deadline" : "NULL AS payment_deadline";
            $sql = "SELECT *, {$payment_method_select}, {$payment_status_select}, {$deadline_select}
                    FROM orders
                    WHERE user_id = ?
                    ORDER BY order_id DESC";

            return pdo_query($sql, $user_id);
        }

        public function select_orderdetails_and_products($order_id) {
            $sql = "
                    SELECT
                    products.product_id,
                    products.name AS product_name,
                    products.image,
                    orderdetails.quantity,
                    orderdetails.price AS product_price
                FROM
                    products
                JOIN
                    orderdetails ON products.product_id = orderdetails.product_id
                WHERE order_id = ?;
            ";

            return pdo_query($sql, $order_id);
        }

        public function getFullOrderInformation($user_id, $order_id) {
            $this->cancel_expired_bank_transfer_orders((int)$user_id);
            // Alias toàn bộ cột cấp đơn để tránh trùng tên với bảng join (PDO chỉ giữ một key, dễ lấy nhầm).
            $payment_method_select = $this->has_orders_column('payment_method') ? "orders.payment_method AS order_payment_method" : "'cod' AS order_payment_method";
            $payment_status_select = $this->has_orders_column('payment_status') ? "orders.payment_status AS order_payment_status" : "'none' AS order_payment_status";
            $deadline_select = $this->has_orders_column('payment_deadline') ? "orders.payment_deadline AS order_payment_deadline" : "NULL AS order_payment_deadline";
            $sql = "
                    SELECT
                    orders.order_id,
                    orders.user_id,
                    orders.date AS order_date,
                    orders.total,
                    orders.address AS order_address,
                    orders.phone AS order_phone,
                    orders.note AS order_note,
                    orders.status AS order_row_status,
                    {$payment_method_select},
                    {$payment_status_select},
                    {$deadline_select},
                    users.full_name,
                    users.email,
                    users.phone AS user_phone,
                    orderdetails.product_id,
                    orderdetails.quantity,
                    orderdetails.price,
                    products.name AS product_name,
                    products.image AS product_image,
                    products.category_id AS product_category_id
                FROM
                    orders
                JOIN
                    users ON orders.user_id = users.user_id
                JOIN
                    orderdetails ON orders.order_id = orderdetails.order_id
                JOIN
                    products ON orderdetails.product_id = products.product_id
                WHERE orders.user_id = ? AND orders.order_id = ?
                
            ";

            return pdo_query($sql, $user_id, $order_id);
        }

        public function get_order_by_id($user_id, $order_id) {
            $payment_method_select = $this->has_orders_column('payment_method') ? "payment_method" : "'cod' AS payment_method";
            $payment_status_select = $this->has_orders_column('payment_status') ? "payment_status" : "'none' AS payment_status";
            $deadline_select = $this->has_orders_column('payment_deadline') ? "payment_deadline" : "NULL AS payment_deadline";
            $sql = "SELECT order_id, user_id, total, status, {$payment_method_select}, {$payment_status_select}, {$deadline_select}
                    FROM orders
                    WHERE user_id = ? AND order_id = ?
                    LIMIT 1";
            return pdo_query_one($sql, (int)$user_id, (int)$order_id);
        }

        public function mark_bank_transfer_submitted($user_id, $order_id) {
            $has_method = $this->has_orders_column('payment_method');
            $has_status = $this->has_orders_column('payment_status');
            $has_deadline = $this->has_orders_column('payment_deadline');

            if ($has_method && $has_status) {
                $sql = "UPDATE orders
                        SET status = 2, payment_method = 'bank', payment_status = 'submitted'
                        WHERE user_id = ?
                          AND order_id = ?
                          AND status = 1";
                if ($has_deadline) {
                    $sql .= " AND (payment_deadline IS NULL OR payment_deadline >= NOW())";
                }
                pdo_execute($sql, (int)$user_id, (int)$order_id);
                return true;
            }

            // Fallback cho DB cũ chưa có cột payment_*: vẫn xác nhận đơn bình thường.
            $sql = "UPDATE orders
                    SET status = 2
                    WHERE user_id = ?
                      AND order_id = ?
                      AND status = 1";
            pdo_execute($sql, (int)$user_id, (int)$order_id);
            return true;
        }

        public function cancel_pending_bank_transfer_order($user_id, $order_id) {
            $has_method = $this->has_orders_column('payment_method');
            $has_status = $this->has_orders_column('payment_status');

            if ($has_method && $has_status) {
                $sql = "UPDATE orders
                        SET status = 5, payment_method = 'bank', payment_status = 'cancelled'
                        WHERE user_id = ?
                          AND order_id = ?
                          AND status = 1";
                pdo_execute($sql, (int)$user_id, (int)$order_id);
                return true;
            }

            // Fallback cho DB cũ chưa có cột payment_*: vẫn hủy đơn bình thường.
            $sql = "UPDATE orders
                    SET status = 5
                    WHERE user_id = ?
                      AND order_id = ?
                      AND status = 1";
            pdo_execute($sql, (int)$user_id, (int)$order_id);
            return true;
        }

        /**
         * Khách hủy đơn ở trạng thái chờ (status = 1).
         * @return false|'cod'|'bank'
         */
        public function cancel_pending_order_by_user($user_id, $order_id) {
            $user_id = (int)$user_id;
            $order_id = (int)$order_id;
            $row = pdo_query_one("SELECT * FROM orders WHERE user_id = ? AND order_id = ?", $user_id, $order_id);
            if (!$row || (int)$row['status'] !== 1) {
                return false;
            }

            $has_method = $this->has_orders_column('payment_method');
            $has_pay_status = $this->has_orders_column('payment_status');
            $pm = $has_method ? strtolower(trim((string)($row['payment_method'] ?? ''))) : '';
            if ($pm === '') {
                $pm = 'cod';
            }
            $ps = $has_pay_status ? (string)($row['payment_status'] ?? 'none') : 'none';

            $has_deadline_col = $this->has_orders_column('payment_deadline');
            $deadline_val = $has_deadline_col ? ($row['payment_deadline'] ?? null) : null;
            $is_bank_pending = ($ps === 'pending' && ((int)$row['status'] === 1)
                && ($pm === 'bank' || !empty($deadline_val)));
            if ($is_bank_pending) {
                $this->cancel_pending_bank_transfer_order($user_id, $order_id);
                return 'bank';
            }

            if ($pm !== 'bank') {
                if ($has_method && $has_pay_status) {
                    $sql = "UPDATE orders
                            SET status = 5, payment_status = 'cancelled'
                            WHERE user_id = ?
                              AND order_id = ?
                              AND status = 1
                              AND (payment_method = 'cod' OR payment_method IS NULL OR payment_method = '')";
                    pdo_execute($sql, $user_id, $order_id);
                } elseif ($has_method && !$has_pay_status) {
                    $sql = "UPDATE orders
                            SET status = 5
                            WHERE user_id = ?
                              AND order_id = ?
                              AND status = 1
                              AND (payment_method = 'cod' OR payment_method IS NULL OR payment_method = '')";
                    pdo_execute($sql, $user_id, $order_id);
                } else {
                    $sql = "UPDATE orders
                            SET status = 5
                            WHERE user_id = ?
                              AND order_id = ?
                              AND status = 1";
                    pdo_execute($sql, $user_id, $order_id);
                }
                return 'cod';
            }

            return false;
        }

        /**
         * Khách hủy đơn chuyển khoản đã xác nhận thanh toán (status = 2, submitted) trước khi giao.
         * @return false|'bank'
         */
        public function cancel_confirmed_bank_order_by_user($user_id, $order_id) {
            $user_id = (int)$user_id;
            $order_id = (int)$order_id;
            if (!$this->has_orders_column('payment_method') || !$this->has_orders_column('payment_status')) {
                return false;
            }
            $row = pdo_query_one(
                "SELECT status, payment_method, payment_status FROM orders WHERE user_id = ? AND order_id = ?",
                $user_id,
                $order_id
            );
            if (!$row || (int)$row['status'] !== 2) {
                return false;
            }
            if ((string)$row['payment_status'] !== 'submitted') {
                return false;
            }
            // submitted chỉ dùng cho luồng CK; bỏ phụ thuộc payment_method vì có thể từng ghi nhầm cod
            $sql = "UPDATE orders
                    SET status = 5, payment_status = 'cancelled', payment_method = 'bank'
                    WHERE user_id = ?
                      AND order_id = ?
                      AND status = 2
                      AND payment_status = 'submitted'";
            pdo_execute($sql, $user_id, $order_id);
            return 'bank';
        }

        /**
         * Hủy đơn (chờ COD / CK chờ thanh toán / CK đã xác nhận chuyển khoản).
         * @return false|'cod'|'bank'
         */
        public function cancel_order_by_user($user_id, $order_id) {
            $kind = $this->cancel_pending_order_by_user($user_id, $order_id);
            if ($kind !== false) {
                return $kind;
            }
            return $this->cancel_confirmed_bank_order_by_user($user_id, $order_id);
        }

        public function force_update_order_status($user_id, $order_id, $status_value) {
            $sql = "UPDATE orders SET status = ? WHERE user_id = ? AND order_id = ?";
            pdo_execute($sql, (int)$status_value, (int)$user_id, (int)$order_id);
        }

        public function cancel_expired_bank_transfer_orders($user_id = 0) {
            if (!$this->has_orders_column('payment_method') || !$this->has_orders_column('payment_status') || !$this->has_orders_column('payment_deadline')) {
                return;
            }
            $sql = "UPDATE orders
                    SET status = 5, payment_status = 'expired'
                    WHERE status = 1
                      AND payment_method = 'bank'
                      AND payment_status = 'pending'
                      AND payment_deadline IS NOT NULL
                      AND payment_deadline < NOW()";
            $args = array();
            if ((int)$user_id > 0) {
                $sql .= " AND user_id = ?";
                $args[] = (int)$user_id;
            }
            pdo_execute($sql, ...$args);
        }


        public function insert_orders($user_id, $total, $address, $phone, $note) {
            $sql = "INSERT INTO orders(user_id, total, address, phone, note) VALUES(?,?,?,?,?)";

            pdo_execute($sql, $user_id, $total, $address, $phone, $note);
        }

        public function insert_orderdetails($order_id, $product_id, $quantity, $price) {
            $sql = "INSERT INTO orderdetails(order_id, product_id, quantity, price) VALUES(?,?,?,?)";

            pdo_execute($sql, $order_id, $product_id , $quantity, $price);
        }

        public function delete_cart_by_user_id($user_id) {
            $sql = "DELETE FROM carts WHERE user_id = ?";
            pdo_execute($sql, $user_id);
        }

        public function delete_cart_by_product_ids($user_id, $product_ids) {
            $product_ids = array_values(array_filter(array_map('intval', (array)$product_ids), function ($id) {
                return $id > 0;
            }));

            if (empty($product_ids)) {
                return;
            }

            $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
            $sql = "DELETE FROM carts WHERE user_id = ? AND product_id IN ($placeholders)";
            $params = array_merge(array((int)$user_id), $product_ids);
            pdo_execute($sql, ...$params);
        }
    }

    $OrderModel = new OrderModel();
?>