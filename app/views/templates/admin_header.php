<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理后台 - <?php echo isset($site_name) ? $site_name : '日记'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- 顶部导航栏 -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-14 items-center">
                <div class="flex items-center gap-4">
                    <a href="<?php echo site_url('admin/dashboard'); ?>" class="text-lg font-bold text-indigo-600">
                        📔 <?php echo isset($site_name) ? $site_name : '日记管理'; ?>
                    </a>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <span>👤 <?php echo $this->session->userdata('admin_username'); ?></span>
                    <a href="<?php echo site_url('/'); ?>" target="_blank" class="text-indigo-500 hover:text-indigo-700">查看前台</a>
                    <a href="<?php echo site_url('admin/logout'); ?>" class="text-red-500 hover:text-red-700">退出</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-6 flex gap-6">
        <!-- 侧边栏 -->
        <aside class="w-48 flex-shrink-0">
            <nav class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <a href="<?php echo site_url('admin/dashboard'); ?>" class="block px-4 py-3 text-sm <?php echo current_url() == site_url('admin/dashboard') ? 'bg-indigo-50 text-indigo-700 font-medium border-l-2 border-indigo-600' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    🏠 仪表盘
                </a>
                <a href="<?php echo site_url('admin/diary'); ?>" class="block px-4 py-3 text-sm <?php echo strpos(current_url(), 'admin/diary') !== false ? 'bg-indigo-50 text-indigo-700 font-medium border-l-2 border-indigo-600' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    📝 日记管理
                </a>
                <a href="<?php echo site_url('admin/settings'); ?>" class="block px-4 py-3 text-sm <?php echo strpos(current_url(), 'admin/settings') !== false ? 'bg-indigo-50 text-indigo-700 font-medium border-l-2 border-indigo-600' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    ⚙️ 网站设置
                </a>
            </nav>
        </aside>

        <!-- 主内容区 -->
        <main class="flex-1 min-w-0">
