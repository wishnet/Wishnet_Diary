<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取所有设置（返回 key => value 数组）
     */
    public function get_all()
    {
        $query = $this->db->get('settings');
        $result = array();
        foreach ($query->result_array() as $row) {
            $result[$row['key']] = $row['value'];
        }
        return $result;
    }

    /**
     * 获取单个设置值
     */
    public function get($key, $default = '')
    {
        $query = $this->db->get_where('settings', array('key' => $key));
        $row = $query->row_array();
        return $row ? $row['value'] : $default;
    }

    /**
     * 设置单个值
     */
    public function set($key, $value)
    {
        $existing = $this->db->get_where('settings', array('key' => $key))->row_array();
        if ($existing) {
            $this->db->where('key', $key);
            return $this->db->update('settings', array(
                'value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ));
        } else {
            return $this->db->insert('settings', array(
                'key' => $key,
                'value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * 批量更新设置
     */
    public function update_batch($data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }
}
