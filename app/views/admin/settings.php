<div class="space-y-6">
    <?php if (!empty($success)): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">
            ✅ <?php echo $success; ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
            ❌ <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- 基本设置 -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-5">⚙️ 基本设置</h2>

        <form method="post" action="">
            <input type="hidden" name="action" value="update_settings">
            <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">

            <div class="space-y-4 max-w-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">网站名称</label>
                    <input type="text" name="site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? "Yunman's Diariy"); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">网站描述</label>
                    <input type="text" name="site_description" value="<?php echo htmlspecialchars($settings['site_description'] ?? ''); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">每页显示条数</label>
                    <input type="number" name="posts_per_page" min="1" max="50" value="<?php echo htmlspecialchars($settings['posts_per_page'] ?? '10'); ?>"
                        class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">备案号</label>
                    <input type="text" name="icp_beian" value="<?php echo htmlspecialchars($settings['icp_beian'] ?? ''); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                        placeholder="例如：粤ICP备XXXXXXXX号-1">
                </div>
            </div>

            <button type="submit"
                class="mt-5 bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                💾 保存
            </button>
        </form>
    </div>

    <!-- 网站访问密码 -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-5">🔐 网站访问密码</h2>

        <!-- 开关 -->
        <form method="post" action="" class="mb-6">
            <input type="hidden" name="action" value="toggle_site_password">
            <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">启用访问密码：</span>
                <button type="submit" name="enable" value="<?php echo ($settings['site_password_enabled'] ?? '1') === '1' ? '0' : '1'; ?>"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                    style="background: <?php echo ($settings['site_password_enabled'] ?? '1') === '1' ? '#4f46e5' : '#d1d5db'; ?>;">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm"
                        style="transform: translateX(<?php echo ($settings['site_password_enabled'] ?? '1') === '1' ? '24px' : '2px'; ?>);"></span>
                </button>
                <span class="text-xs <?php echo ($settings['site_password_enabled'] ?? '1') === '1' ? 'text-green-600' : 'text-gray-400'; ?>">
                    <?php echo ($settings['site_password_enabled'] ?? '1') === '1' ? '已启用' : '已关闭'; ?>
                </span>
            </div>
        </form>

        <!-- 修改访问密码 -->
        <form method="post" action="">
            <input type="hidden" name="action" value="change_site_password">
            <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
            <div class="space-y-4 max-w-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">新访问密码</label>
                    <input type="password" name="new_site_password" required minlength="6"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                        placeholder="至少6位">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">确认新密码</label>
                    <input type="password" name="confirm_site_password" required minlength="6"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                </div>
            </div>
            <button type="submit"
                class="mt-5 bg-orange-500 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 transition">
                🔄 修改访问密码
            </button>
        </form>
    </div>

    <!-- 修改管理员密码 -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-5">🔐 修改管理员密码</h2>

        <form method="post" action="">
            <input type="hidden" name="action" value="change_password">
            <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">

            <div class="space-y-4 max-w-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">旧密码</label>
                    <input type="password" name="old_password" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">新密码</label>
                    <input type="password" name="new_password" required minlength="6"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">确认新密码</label>
                    <input type="password" name="confirm_password" required minlength="6"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                </div>
            </div>

            <button type="submit"
                class="mt-5 bg-orange-500 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 transition">
                🔄 修改密码
            </button>
        </form>
    </div>
</div>
