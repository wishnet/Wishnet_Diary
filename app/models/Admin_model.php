<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 验证管理员登录
     */
    public function verify($username, $password)
    {
        $query = $this->db->get_where('admin', array('username' => $username));
        $admin = $query->row_array();

        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }
        return false;
    }

    /**
     * 更新管理员密码
     */
    public function update_password($username, $old_password, $new_password)
    {
        $admin = $this->verify($username, $old_password);
        if (!$admin) {
            return false;
        }

        $hash = password_hash($new_password, PASSWORD_BCRYPT);
        $this->db->where('username', $username);
        return $this->db->update('admin', array('password' => $hash));
    }

    /**
     * 更新管理员用户名
     */
    public function update_username($old_username, $new_username)
    {
        $this->db->where('username', $old_username);
        return $this->db->update('admin', array('username' => $new_username));
    }
}
