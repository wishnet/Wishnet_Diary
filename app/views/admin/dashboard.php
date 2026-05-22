<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-6">📊 仪表盘</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-6 border border-indigo-200">
            <div class="text-3xl mb-2">📝</div>
            <div class="text-3xl font-bold text-indigo-700"><?php echo $diary_count; ?></div>
            <div class="text-sm text-indigo-500 mt-1">日记总数</div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
            <div class="text-3xl mb-2">👤</div>
            <div class="text-xl font-bold text-green-700"><?php echo $username; ?></div>
            <div class="text-sm text-green-500 mt-1">当前管理员</div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
            <div class="text-3xl mb-2">⚙️</div>
            <div class="text-xl font-bold text-purple-700"><?php echo $site_name; ?></div>
            <div class="text-sm text-purple-500 mt-1">网站名称</div>
        </div>
    </div>

    <div class="mt-8 flex gap-4">
        <a href="<?php echo site_url('admin/diary/create'); ?>"
            class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition shadow-sm">
            ✏️ 写新日记
        </a>
        <a href="<?php echo site_url('admin/diary'); ?>"
            class="inline-flex items-center gap-2 bg-white text-gray-700 px-5 py-2.5 rounded-lg font-medium border border-gray-300 hover:bg-gray-50 transition shadow-sm">
            📋 管理日记
        </a>
    </div>
</div>
