<?php
    class CustomerModel {

        public function select_users() {
            $sql = "SELECT username, full_name, email, phone FROM users";

            return pdo_query($sql);
        }

        public function select_all_users() {
            $sql = "SELECT * FROM users ORDER BY user_id DESC";

            return pdo_query($sql);
        }

        public function user_insert($username, $password, $full_name, $image, $email, $phone, $address, $role) {
            $role = (int)$role;
            if (!in_array($role, [0, 1], true)) {
                throw new Exception("Vai trò tài khoản không hợp lệ.");
            }

            $sql = "INSERT INTO users(username, password, full_name, image, email, phone, address, role) VALUES(?,?,?,?,?,?,?,?)";

            pdo_execute($sql, $username, $password, $full_name, $image, $email, $phone, $address, $role);
        }

        public function get_user_admin($username) {
            $sql = "SELECT * FROM users WHERE username = ? AND role = 1";

            return pdo_query($sql, $username);
        }

        public function get_user_by_id($user_id) {
            $sql = "SELECT * FROM users WHERE user_id = ?";

            return pdo_query_one($sql, $user_id);
        }

        public function update_user_active($user_id, $active) {
            $sql = "UPDATE users SET active = ? WHERE user_id = ?";

            pdo_execute($sql, $active, $user_id);
        }

        public function update_user_password($user_id, $password) {
            $sql = "UPDATE users SET password = ? WHERE user_id = ?";

            pdo_execute($sql, $password, $user_id);
        }

    }

    $CustomerModel = new CustomerModel();
?>