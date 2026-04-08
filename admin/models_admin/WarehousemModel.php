<?php
    class WarehousemModel {
        public function ensure_import_schema() {
            $sql_receipts = "CREATE TABLE IF NOT EXISTS warehouse_receipts (
                receipt_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                receipt_code VARCHAR(50) NOT NULL UNIQUE,
                import_date DATE NOT NULL,
                note TEXT NULL,
                status TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0: nhap, 1: hoan thanh',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
            pdo_execute($sql_receipts);

            $sql_items = "CREATE TABLE IF NOT EXISTS warehouse_receipt_items (
                item_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                receipt_id INT NOT NULL,
                product_id INT NOT NULL,
                import_price INT NOT NULL,
                import_quantity INT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_receipt_items_receipt FOREIGN KEY (receipt_id) REFERENCES warehouse_receipts(receipt_id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
            pdo_execute($sql_items);

            $sql_index = "CREATE INDEX idx_receipt_items_product ON warehouse_receipt_items(product_id)";
            try {
                pdo_execute($sql_index);
            } catch (Exception $e) {
            }
        }

        public function next_receipt_code() {
            $this->ensure_import_schema();
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
            $this->ensure_import_schema();
            $sql = "INSERT INTO warehouse_receipts(receipt_code, import_date, note, status) VALUES(?,?,?,0)";
            pdo_execute($sql, $receipt_code, $import_date, $note);
            $receipt_id = (int)pdo_query_value("SELECT LAST_INSERT_ID()");

            foreach ($items as $item) {
                $insert_item_sql = "INSERT INTO warehouse_receipt_items(receipt_id, product_id, import_price, import_quantity)
                                    VALUES(?,?,?,?)";
                pdo_execute($insert_item_sql, (int)$receipt_id, (int)$item['product_id'], (int)$item['import_price'], (int)$item['import_quantity']);
            }

            return $receipt_id;
        }

        public function update_receipt($receipt_id, $import_date, $note, $items) {
            $this->ensure_import_schema();
            $receipt = $this->select_receipt_by_id($receipt_id);
            if (!$receipt || (int)$receipt['status'] === 1) {
                return false;
            }

            $sql = "UPDATE warehouse_receipts SET import_date = ?, note = ? WHERE receipt_id = ?";
            pdo_execute($sql, $import_date, $note, $receipt_id);

            pdo_execute("DELETE FROM warehouse_receipt_items WHERE receipt_id = ?", $receipt_id);
            foreach ($items as $item) {
                $insert_item_sql = "INSERT INTO warehouse_receipt_items(receipt_id, product_id, import_price, import_quantity)
                                    VALUES(?,?,?,?)";
                pdo_execute($insert_item_sql, (int)$receipt_id, (int)$item['product_id'], (int)$item['import_price'], (int)$item['import_quantity']);
            }

            return true;
        }

        public function complete_receipt($receipt_id) {
            $this->ensure_import_schema();
            $receipt = $this->select_receipt_by_id($receipt_id);
            if (!$receipt || (int)$receipt['status'] === 1) {
                return false;
            }

            $sql = "UPDATE warehouse_receipts SET status = 1 WHERE receipt_id = ?";
            pdo_execute($sql, $receipt_id);

            return true;
        }

        public function select_receipts($keyword = '', $status = -1) {
            $this->ensure_import_schema();
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
            $this->ensure_import_schema();
            $sql = "SELECT * FROM warehouse_receipts WHERE receipt_id = ?";

            return pdo_query_one($sql, $receipt_id);
        }

        public function select_receipt_items($receipt_id) {
            $this->ensure_import_schema();
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