<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('riji');
        $this->load->model('diary_model');
        $this->load->model('setting_model');
        $this->load->model('admin_model');
        $this->load->library('session');
    }

    /**
     * 检查是否已登录
     */
    private function check_login()
    {
        if (!$this->session->userdata('admin_logged_in')) {
            redirect('admin/login');
        }
    }

    // ==================== 登录相关 ====================

    public function login()
    {
        // 已登录则跳转
        if ($this->session->userdata('admin_logged_in')) {
            redirect('admin/dashboard');
        }

        $data['error'] = '';
        $data['site_name'] = $this->setting_model->get('site_name', "Yunman's Diariy");
        $data['csrf_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();

        if ($this->input->post()) {
            $username = $this->input->post('username', TRUE);
            $password = $this->input->post('password', TRUE);
            $captcha_input = $this->input->post('captcha', TRUE);

            // 验证码检查
            $captcha_session = $this->session->userdata('admin_captcha');
            if (empty($captcha_input) || strtoupper($captcha_input) !== strtoupper($captcha_session)) {
                $data['error'] = '验证码错误';
            } else {
                $admin = $this->admin_model->verify($username, $password);
                if ($admin) {
                    $this->session->unset_userdata('admin_captcha');
                    $this->session->set_userdata(array(
                        'admin_logged_in' => TRUE,
                        'admin_username' => $admin['username'],
                        'admin_id' => $admin['id'],
                    ));
                    redirect('admin/dashboard');
                } else {
                    $data['error'] = '用户名或密码错误';
                }
            }
        }

        $this->load->view('admin/login', $data);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('admin/login');
    }

    /**
     * 生成验证码图片
     */
    public function captcha()
    {
        // 排除易混淆字符：0/O, 1/l/I
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < 4; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        $this->session->set_userdata('admin_captcha', $code);

        // 创建图片
        $width = 120;
        $height = 44;
        $image = imagecreatetruecolor($width, $height);

        // 背景色：暖白纸张色
        $bg = imagecolorallocate($image, 253, 246, 227);
        imagefilledrectangle($image, 0, 0, $width, $height, $bg);

        // 干扰：暖色系噪点
        for ($i = 0; $i < 80; $i++) {
            $noise_color = imagecolorallocate($image,
                random_int(210, 240),
                random_int(180, 210),
                random_int(150, 180)
            );
            imagesetpixel($image, random_int(0, $width), random_int(0, $height), $noise_color);
        }

        // 干扰线
        for ($i = 0; $i < 3; $i++) {
            $line_color = imagecolorallocate($image,
                random_int(180, 210),
                random_int(150, 180),
                random_int(120, 150)
            );
            imageline($image,
                random_int(0, 30), random_int(5, $height - 5),
                random_int($width - 30, $width), random_int(5, $height - 5),
                $line_color
            );
        }

        // 写入文字（尝试多个常见字体路径）
        $font_size = 20;
        $text_color = imagecolorallocate($image, 92, 61, 46);
        $font_paths = array(
            'C:\\Windows\\Fonts\\arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/TTF/DejaVuSans.ttf',
            '/System/Library/Fonts/Helvetica.ttc',
        );
        $font_file = '';
        foreach ($font_paths as $fp) {
            if (file_exists($fp)) { $font_file = $fp; break; }
        }

        $x = 12;
        for ($i = 0; $i < 4; $i++) {
            $y = random_int(10, 18);
            $angle = random_int(-15, 15);
            if ($font_file) {
                imagettftext($image, $font_size, $angle, $x, 28 + $y,
                    $text_color, $font_file, $code[$i]);
            } else {
                // 无 TTF 字体时回退到内置字体
                imagestring($image, 5, $x, 8 + $y, $code[$i], $text_color);
            }
            $x += 25;
        }

        header('内容-Type: image/png');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        imagepng($image);
        imagedestroy($image);
    }

    // ==================== 仪表盘 ====================

    public function dashboard()
    {
        $this->check_login();

        $data['diary_count'] = $this->diary_model->get_diary_count();
        $data['site_name'] = $this->setting_model->get('site_name', "Yunman's Diariy");
        $data['username'] = $this->session->userdata('admin_username');

        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('templates/admin_footer');
    }

    // ==================== 日记 CRUD ====================

    public function diary_list()
    {
        $this->check_login();

        $page = (int) $this->input->get('page') ?: 1;
        $per_page = 15;
        $offset = ($page - 1) * $per_page;

        $data['diaries'] = $this->diary_model->get_diaries($per_page, $offset);
        $total = $this->diary_model->get_diary_count();

        // 分页
        $this->load->library('pagination');
        $config['base_url'] = site_url('admin/diary');
        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['use_page_numbers'] = TRUE;
        $config['full_tag_open'] = '<div class="flex justify-center gap-2 mt-6">';
        $config['full_tag_close'] = '</div>';
        $config['first_link'] = '首页';
        $config['last_link'] = '末页';
        $config['next_link'] = '下一页';
        $config['prev_link'] = '上一页';
        $config['attributes'] = array('class' => 'px-3 py-1 rounded border border-gray-300 hover:bg-gray-100 text-sm');
        $config['cur_tag_open'] = '<span class="px-3 py-1 rounded bg-blue-600 text-white text-sm">';
        $config['cur_tag_close'] = '</span>';

        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['site_name'] = $this->setting_model->get('site_name', "Yunman's Diariy");
        $data['csrf_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();

        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/diary_list', $data);
        $this->load->view('templates/admin_footer');
    }

    public function diary_create()
    {
        $this->check_login();

        $data['action'] = 'create';
        $data['diary'] = array(
            'title' => '',
            'content' => '',
            'mood' => '平静',
            'weather' => '晴',
            'tags' => '',
            'diary_date' => date('Y-m-d'),
        );
        $data['error'] = '';
        $data['moods'] = mood_options();
        $data['weathers'] = weather_options();
        $data['site_name'] = $this->setting_model->get('site_name', "Yunman's Diariy");
        $data['csrf_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();

        if ($this->input->post()) {
            $post_data = array(
                'title' => $this->input->post('title', TRUE),
                'content' => $this->input->post('content'),
                'mood' => $this->input->post('mood', TRUE),
                'weather' => $this->input->post('weather', TRUE),
                'tags' => $this->input->post('tags', TRUE),
                'diary_date' => $this->input->post('diary_date', TRUE),
            );

            if (empty($post_data['title']) || empty($post_data['content'])) {
                $data['error'] = '标题和内容不能为空';
                $data['diary'] = $post_data;
            } else {
                $this->diary_model->insert_diary($post_data);
                $this->session->set_flashdata('success', '日记创建成功');
                redirect('admin/diary');
            }
        }

        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/diary_form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function diary_edit($id)
    {
        $this->check_login();

        $diary = $this->diary_model->get_diary($id);
        if (!$diary) {
            show_404();
        }

        $data['action'] = 'edit';
        $data['diary'] = $diary;
        $data['error'] = '';
        $data['moods'] = mood_options();
        $data['weathers'] = weather_options();
        $data['site_name'] = $this->setting_model->get('site_name', "Yunman's Diariy");
        $data['csrf_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();

        if ($this->input->post()) {
            $post_data = array(
                'title' => $this->input->post('title', TRUE),
                'content' => $this->input->post('content'),
                'mood' => $this->input->post('mood', TRUE),
                'weather' => $this->input->post('weather', TRUE),
                'tags' => $this->input->post('tags', TRUE),
                'diary_date' => $this->input->post('diary_date', TRUE),
            );

            if (empty($post_data['title']) || empty($post_data['content'])) {
                $data['error'] = '标题和内容不能为空';
                $data['diary'] = array_merge($diary, $post_data);
            } else {
                $this->diary_model->update_diary($id, $post_data);
                $this->session->set_flashdata('success', '日记更新成功');
                redirect('admin/diary');
            }
        }

        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/diary_form', $data);
        $this->load->view('templates/admin_footer');
    }

    public function diary_delete($id)
    {
        $this->check_login();

        $diary = $this->diary_model->get_diary($id);
        if ($diary) {
            $this->diary_model->delete_diary($id);
            $this->session->set_flashdata('success', '日记已删除');
        }

        redirect('admin/diary');
    }

    /**
     * 批量操作
     */
    public function diary_batch()
    {
        $this->check_login();

        $action = $this->input->post('action');
        $ids = $this->input->post('ids');

        if (empty($ids) || !is_array($ids)) {
            $this->session->set_flashdata('success', 'No 篇日记 selected');
            redirect('admin/diary');
        }

        if ($action === 'delete') {
            $count = 0;
            foreach ($ids as $id) {
                if ($this->diary_model->delete_diary((int)$id)) {
                    $count++;
                }
            }
            $this->session->set_flashdata('success', "已删除 {$count} 篇日记");
        }

        redirect('admin/diary');
    }

    // ==================== 网站设置 ====================

    public function settings()
    {
        $this->check_login();

        $data['settings'] = $this->setting_model->get_all();
        $data['error'] = '';
        $data['success'] = '';
        $data['site_name'] = $this->setting_model->get('site_name', "Yunman's Diariy");
        $data['csrf_name'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();

        if ($this->input->post()) {
            $action = $this->input->post('action');

            if ($action === 'update_settings') {
                // 更新基本设置
                $setting_data = array(
                    'site_name' => $this->input->post('site_name', TRUE),
                    'site_description' => $this->input->post('site_description', TRUE),
                    'posts_per_page' => $this->input->post('posts_per_page', TRUE),
                    'icp_beian' => $this->input->post('icp_beian', TRUE),
                );
                $this->setting_model->update_batch($setting_data);
                $data['settings'] = $this->setting_model->get_all();
                $data['success'] = '设置已保存';
            } elseif ($action === 'change_password') {
                $old_password = $this->input->post('old_password', TRUE);
                $new_password = $this->input->post('new_password', TRUE);
                $confirm_password = $this->input->post('confirm_password', TRUE);
                $username = $this->session->userdata('admin_username');

                if (empty($old_password) || empty($new_password)) {
                    $data['error'] = '请填写所有密码字段';
                } elseif ($new_password !== $confirm_password) {
                    $data['error'] = '两次输入的新密码不一致';
                } elseif (strlen($new_password) < 6) {
                    $data['error'] = '新密码长度不能少于6位';
                } else {
                    $result = $this->admin_model->update_password($username, $old_password, $new_password);
                    if ($result) {
                        $data['success'] = '密码修改成功';
                    } else {
                        $data['error'] = '旧密码不正确';
                    }
                }
            } elseif ($action === 'toggle_site_password') {
                $new_value = $this->input->post('enable');
                $this->setting_model->set('site_password_enabled', $new_value);
                $data['settings'] = $this->setting_model->get_all();
                $data['success'] = $new_value === '1' ? '网站访问密码已启用' : '网站访问密码已关闭';
            } elseif ($action === 'change_site_password') {
                $new_password = $this->input->post('new_site_password', TRUE);
                $confirm_password = $this->input->post('confirm_site_password', TRUE);

                if (empty($new_password)) {
                    $data['error'] = '请输入新密码';
                } elseif ($new_password !== $confirm_password) {
                    $data['error'] = '两次输入的新密码不一致';
                } elseif (strlen($new_password) < 6) {
                    $data['error'] = '密码长度不能少于6位';
                } else {
                    $hash = password_hash($new_password, PASSWORD_BCRYPT);
                    $this->setting_model->set('site_password', $hash);
                    $data['settings'] = $this->setting_model->get_all();
                    $data['success'] = '访问密码修改成功';
                }
            }
        }

        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/settings', $data);
        $this->load->view('templates/admin_footer');
    }
}
