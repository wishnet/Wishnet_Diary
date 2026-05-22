<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Diary_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取日记列表（分页）
     */
    public function get_diaries($limit, $offset, $order_by = 'diary_date DESC')
    {
        $this->db->order_by('diary_date', 'DESC');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('diaries', $limit, $offset);
        return $query->result_array();
    }

    /**
     * 获取日记总数
     */
    public function get_diary_count()
    {
        return $this->db->count_all('diaries');
    }

    /**
     * 按年月获取日记列表（用于时间轴分组）
     */
    public function get_diaries_grouped($year_month, $limit, $offset)
    {
        $this->db->like('diary_date', $year_month, 'after');
        $this->db->order_by('diary_date', 'DESC');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('diaries', $limit, $offset);
        return $query->result_array();
    }

    /**
     * 搜索日记（标题和标签）
     */
    public function search_diaries($keyword, $limit, $offset)
    {
        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('title', $keyword);
            $this->db->or_like('tags', $keyword);
            $this->db->group_end();
        }
        $this->db->order_by('diary_date', 'DESC');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('diaries', $limit, $offset);
        return $query->result_array();
    }

    /**
     * 搜索日记总数
     */
    public function search_diary_count($keyword)
    {
        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('title', $keyword);
            $this->db->or_like('tags', $keyword);
            $this->db->group_end();
        }
        return $this->db->count_all_results('diaries');
    }

    /**
     * 按标签获取日记
     */
    public function get_diaries_by_tag($tag, $limit, $offset)
    {
        $this->db->like('tags', $tag);
        $this->db->order_by('diary_date', 'DESC');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('diaries', $limit, $offset);
        return $query->result_array();
    }

    /**
     * 按标签获取日记总数
     */
    public function get_diary_count_by_tag($tag)
    {
        $this->db->like('tags', $tag);
        return $this->db->count_all_results('diaries');
    }

    /**
     * 获取每年的月份列表
     */
    public function get_year_months()
    {
        $this->db->select("DISTINCT strftime('%Y-%m', diary_date) as year_month");
        $this->db->from('diaries');
        $this->db->order_by('year_month', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 获取所有标签及每个标签下的日记数量
     */
    public function get_all_tags_with_count()
    {
        $diaries = $this->db->select('tags')->get('diaries')->result_array();
        $tag_count = array();
        foreach ($diaries as $d) {
            if (!empty($d['tags'])) {
                $tags = explode(',', $d['tags']);
                foreach ($tags as $t) {
                    $t = trim($t);
                    if (!empty($t)) {
                        if (!isset($tag_count[$t])) {
                            $tag_count[$t] = 0;
                        }
                        $tag_count[$t]++;
                    }
                }
            }
        }
        arsort($tag_count);
        return $tag_count;
    }

    /**
     * 获取月份归档（每月日记数量）
     */
    public function get_monthly_archive()
    {
        $this->db->select("strftime('%Y-%m', diary_date) as year_month, COUNT(*) as count");
        $this->db->from('diaries');
        $this->db->group_by('year_month');
        $this->db->order_by('year_month', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 获取单篇日记
     */
    public function get_diary($id)
    {
        $query = $this->db->get_where('diaries', array('id' => $id));
        return $query->row_array();
    }

    /**
     * 按月份筛选日记
     */
    public function get_diaries_by_month($year_month, $limit, $offset)
    {
        $this->db->like('diary_date', $year_month, 'after');
        $this->db->order_by('diary_date', 'DESC');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('diaries', $limit, $offset);
        return $query->result_array();
    }

    /**
     * 按月份筛选日记总数
     */
    public function get_diary_count_by_month($year_month)
    {
        $this->db->like('diary_date', $year_month, 'after');
        return $this->db->count_all_results('diaries');
    }

    /**
     * 新增日记
     */
    public function insert_diary($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert('diaries', $data);
        return $this->db->insert_id();
    }

    /**
     * 更新日记
     */
    public function update_diary($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update('diaries', $data);
    }

    /**
     * 删除日记
     */
    public function delete_diary($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('diaries');
    }
}
