<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('riji');
        $this->load->model('diary_model');
        $this->load->model('setting_model');

        // 加载 Markdown 解析器
        if (!class_exists('Parsedown')) {
            require_once APPPATH . 'libraries/Parsedown.php';
        }
        $this->parsedown = new Parsedown();
        $this->parsedown->setSafeMode(true);

        // 网站访问密码检查（AJAX 和后台页面除外）
        $this->_check_site_password();
    }

    /**
     * 检查网站访问密码
     */
    private function _check_site_password()
    {
        // 跳过 AJAX 请求
        if ($this->uri->segment(1) === 'ajax') {
            return;
        }
        // 如果已通过密码验证
        if ($this->session->userdata('site_authenticated')) {
            return;
        }
        // 如果功能未启用，跳过
        if ($this->setting_model->get('site_password_enabled', '1') !== '1') {
            return;
        }
        // 密码门页面本身不拦截
        if ($this->uri->segment(1) === 'gate') {
            return;
        }

        // 未验证，跳转到密码门
        redirect('gate');
    }

    /**
     * 密码门页面
     */
    public function gate()
    {
        $data = $this->_common_data();
        $data['error'] = '';

        // 已登录则直接跳首页
        if ($this->session->userdata('site_authenticated')) {
            redirect('/');
            return;
        }
        // 未启用也跳首页
        if ($this->setting_model->get('site_password_enabled', '1') !== '1') {
            redirect('/');
            return;
        }

        if ($this->input->post()) {
            $input_password = $this->input->post('password', TRUE);
            $stored_hash = $this->setting_model->get('site_password', '');

            if (password_verify($input_password, $stored_hash)) {
                $this->session->set_userdata('site_authenticated', TRUE);
                redirect('/');
            } else {
                $data['error'] = '密码错误，请重试';
            }
        }

        $this->load->view('templates/header', $data);
        $this->load->view('home/password_gate', $data);
        $this->load->view('templates/footer');
    }

    /**
     * 获取公共数据
     */
    private function _common_data()
    {
        return array(
            'site_name' => $this->setting_model->get('site_name', "Wishnet's Diary"),
            'site_description' => $this->setting_model->get('site_description', '记录生活的点点滴滴'),
            'monthly_archive' => $this->diary_model->get_monthly_archive(),
            'sidebar_tags' => $this->diary_model->get_all_tags_with_count(),
        );
    }

    /**
     * 首页 —— 时间轴展示
     */
    public function index()
    {
        $data = $this->_common_data();

        $page = (int) $this->input->get('page') ?: 1;
        $per_page = (int) $this->setting_model->get('posts_per_page', 10);
        $offset = ($page - 1) * $per_page;
        $month = $this->input->get('month', TRUE);

        if (!empty($month) && preg_match('/^\d{4}-\d{2}$/', $month)) {
            $diaries = $this->diary_model->get_diaries_by_month($month, $per_page, $offset);
            $total = $this->diary_model->get_diary_count_by_month($month);
            $base_url = site_url('?month=' . $month);
            $data['month_filter'] = $month;
        } else {
            $diaries = $this->diary_model->get_diaries($per_page, $offset);
            $total = $this->diary_model->get_diary_count();
            $base_url = site_url('/');
        }

        // 按月份分组
        $data['grouped_diaries'] = $this->_group_by_month($diaries);

        // 分页
        $this->load->library('pagination');
        $config['base_url'] = $base_url;
        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['use_page_numbers'] = TRUE;
        $config['full_tag_open'] = '<div class="flex justify-center items-center gap-1 mt-10">';
        $config['full_tag_close'] = '</div>';
        $config['first_link'] = '首页';
        $config['last_link'] = '末页';
        $config['next_link'] = '下一页 →';
        $config['prev_link'] = '← 上一页';
        $config['attributes'] = array('class' => 'px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100 transition text-sm');
        $config['cur_tag_open'] = '<span class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium">';
        $config['cur_tag_close'] = '</span>';
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['total'] = $total;
        $data['current_page'] = $page;

        $this->load->view('templates/header', $data);
        $this->load->view('home/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * AJAX 获取日记详情（返回 JSON）
     */
    public function ajax_detail($id)
    {
        $diary = $this->diary_model->get_diary($id);
        if (!$diary) {
            header('内容-Type: application/json; charset=utf-8');
            echo json_encode(array('error' => '日记不存在'));
            return;
        }

        $diary['content_html'] = $this->parsedown->text($diary['content']);
        $diary['mood_emoji'] = mood_emoji($diary['mood']);
        $diary['weather_emoji'] = weather_emoji($diary['weather']);
        $diary['date_cn'] = format_date_cn($diary['diary_date']);
        $diary['tags_array'] = parse_tags($diary['tags']);
        $diary['title'] = htmlspecialchars($diary['title']);

        header('内容-Type: application/json; charset=utf-8');
        echo json_encode($diary);
    }

    /**
     * 日记详情页（直接访问，也渲染 Markdown）
     */
    public function detail($id)
    {
        $data = $this->_common_data();
        $data['diary'] = $this->diary_model->get_diary($id);

        if (!$data['diary']) {
            show_404();
        }

        $data['content_html'] = $this->parsedown->text($data['diary']['content']);

        $this->load->view('templates/header', $data);
        $this->load->view('home/detail', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Markdown 解析（使用 Parsedown）
     */
    private function _parse_markdown($text)
    {
        return $this->parsedown->text($text);
    }

    /**
     * 搜索页
     */
    public function search()
    {
        $data = $this->_common_data();

        $keyword = $this->input->get('q', TRUE);
        $data['keyword'] = $keyword;

        $page = (int) $this->input->get('page') ?: 1;
        $per_page = (int) $this->setting_model->get('posts_per_page', 10);
        $offset = ($page - 1) * $per_page;

        $diaries = $this->diary_model->search_diaries($keyword, $per_page, $offset);
        $total = $this->diary_model->search_diary_count($keyword);

        $data['grouped_diaries'] = $this->_group_by_month($diaries);

        // 分页
        $this->load->library('pagination');
        $config['base_url'] = site_url('search?q=' . urlencode($keyword));
        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['use_page_numbers'] = TRUE;
        $config['full_tag_open'] = '<div class="flex justify-center items-center gap-1 mt-10">';
        $config['full_tag_close'] = '</div>';
        $config['first_link'] = '首页';
        $config['last_link'] = '末页';
        $config['next_link'] = '下一页 →';
        $config['prev_link'] = '← 上一页';
        $config['attributes'] = array('class' => 'px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100 transition text-sm');
        $config['cur_tag_open'] = '<span class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium">';
        $config['cur_tag_close'] = '</span>';
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['total'] = $total;

        $this->load->view('templates/header', $data);
        $this->load->view('home/search', $data);
        $this->load->view('templates/footer');
    }

    /**
     * 标签筛选页
     */
    public function tag($tag)
    {
        $data = $this->_common_data();

        // 先解码 URL 编码的标签
        $tag = urldecode($tag);
        $data['current_tag'] = $tag;

        $page = (int) $this->input->get('page') ?: 1;
        $per_page = (int) $this->setting_model->get('posts_per_page', 10);
        $offset = ($page - 1) * $per_page;

        $diaries = $this->diary_model->get_diaries_by_tag($tag, $per_page, $offset);
        $total = $this->diary_model->get_diary_count_by_tag($tag);

        $data['grouped_diaries'] = $this->_group_by_month($diaries);

        // 分页
        $this->load->library('pagination');
        $config['base_url'] = site_url('tag/' . urlencode($tag));
        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['use_page_numbers'] = TRUE;
        $config['full_tag_open'] = '<div class="flex justify-center items-center gap-1 mt-10">';
        $config['full_tag_close'] = '</div>';
        $config['first_link'] = '首页';
        $config['last_link'] = '末页';
        $config['next_link'] = '下一页 →';
        $config['prev_link'] = '← 上一页';
        $config['attributes'] = array('class' => 'px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100 transition text-sm');
        $config['cur_tag_open'] = '<span class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium">';
        $config['cur_tag_close'] = '</span>';
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['total'] = $total;

        $this->load->view('templates/header', $data);
        $this->load->view('home/search', $data);
        $this->load->view('templates/footer');
    }

    /**
     * 按月份分组日记
     */
    private function _group_by_month($diaries)
    {
        $grouped = array();
        foreach ($diaries as $diary) {
            $ym = date('Y-m', strtotime($diary['diary_date']));
            if (!isset($grouped[$ym])) {
                $grouped[$ym] = array(
                    'label' => format_date_ym($diary['diary_date']),
                    'diaries' => array(),
                );
            }
            $grouped[$ym]['diaries'][] = $diary;
        }
        return $grouped;
    }

}
