<?php
/**
 * Wishnet's Diary 安装程序
 * 
 * 使用方法：访问 http://your-domain/install.php
 * 安装完成后请删除此文件
 */

// 检查是否已安装
if (file_exists(__DIR__ . '/public/data/riji.db')) {
    $already_installed = true;
} else {
    $already_installed = false;
}

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = '';
$success = '';

// ==================== 系统要求检查 ====================
function check_requirements()
{
    $checks = array();

    // PHP 版本
    $checks[] = array(
        'name' => 'PHP 版本 >= 7.2',
        'pass' => version_compare(PHP_VERSION, '7.2.0', '>='),
        'value' => PHP_VERSION,
    );

    // PDO
    $checks[] = array(
        'name' => 'PDO 扩展',
        'pass' => extension_loaded('pdo'),
        'value' => extension_loaded('pdo') ? '已启用' : '未启用',
    );

    // SQLite
    $checks[] = array(
        'name' => 'PDO SQLite 驱动',
        'pass' => extension_loaded('pdo_sqlite'),
        'value' => extension_loaded('pdo_sqlite') ? '已启用' : '未启用',
    );

    // Session
    $checks[] = array(
        'name' => 'Session 支持',
        'pass' => extension_loaded('session') || function_exists('session_start'),
        'value' => function_exists('session_start') ? '可用' : '不可用',
    );

    // GD (验证码需要)
    $checks[] = array(
        'name' => 'GD 图形库',
        'pass' => extension_loaded('gd'),
        'value' => extension_loaded('gd') ? '已启用' : '未启用（验证码将使用回退方案）',
    );

    // 目录写入权限
    $writable_dirs = array(
        'public/data/',
        'app/cache/',
        'app/cache/sessions/',
        'app/logs/',
    );

    foreach ($writable_dirs as $dir) {
        $full_path = __DIR__ . '/' . $dir;
        $writable = is_writable($full_path) || is_writable(__DIR__ . '/public/data/');
        $checks[] = array(
            'name' => "目录可写: {$dir}",
            'pass' => is_dir($full_path) || @mkdir($full_path, 0755, true),
            'value' => is_writable($full_path) ? '可写' : '不可写',
        );
    }

    return $checks;
}

// ==================== 生成随机密钥 ====================
function generate_key($length = 32)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+';
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $key;
}

// ==================== 处理安装 ====================
if ($step === 3 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_username = trim($_POST['admin_username'] ?? 'admin');
    $admin_password = $_POST['admin_password'] ?? '';
    $admin_password2 = $_POST['admin_password2'] ?? '';
    $site_name = trim($_POST['site_name'] ?? "Wishnet's Diary");
    $site_access_password = $_POST['site_access_password'] ?? '';

    // 验证
    if (empty($admin_username)) {
        $error = '请输入管理员用户名';
    } elseif (empty($admin_password)) {
        $error = '请输入管理员密码';
    } elseif (strlen($admin_password) < 6) {
        $error = '管理员密码至少6位';
    } elseif ($admin_password !== $admin_password2) {
        $error = '两次输入的密码不一致';
    } elseif (empty($site_access_password) || strlen($site_access_password) < 6) {
        $error = '网站访问密码至少6位';
    } else {
        // 执行安装
        try {
            $db_dir = __DIR__ . '/public/data';
            if (!is_dir($db_dir)) {
                mkdir($db_dir, 0755, true);
            }

            $db = new PDO('sqlite:' . $db_dir . '/riji.db');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 创建表
            $db->exec("CREATE TABLE IF NOT EXISTS diaries (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                mood VARCHAR(50) DEFAULT '',
                weather VARCHAR(50) DEFAULT '',
                tags VARCHAR(500) DEFAULT '',
                diary_date DATE NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");

            $db->exec("CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                key VARCHAR(100) NOT NULL UNIQUE,
                value TEXT,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");

            $db->exec("CREATE TABLE IF NOT EXISTS admin (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");

            // 插入管理员
            $admin_hash = password_hash($admin_password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
            $stmt->execute([$admin_username, $admin_hash]);

            // 插入设置
            $stmt = $db->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)");
            $stmt->execute(['site_name', $site_name]);
            $stmt->execute(['site_description', '记录生活的点点滴滴']);
            $stmt->execute(['posts_per_page', '10']);
            $stmt->execute(['site_password_enabled', '1']);
            $stmt->execute(['site_password', password_hash($site_access_password, PASSWORD_BCRYPT)]);

            // 更新 config.php 中的加密密钥
            $config_file = __DIR__ . '/app/config/config.php';
            $config_content = file_get_contents($config_file);
            $new_key = generate_key(32);
            $config_content = preg_replace(
                "/\\\$config\['encryption_key'\]\s*=\s*'[^']*';/",
                "\$config['encryption_key'] = '{$new_key}';",
                $config_content
            );
            file_put_contents($config_file, $config_content);

            // 确保 session 目录存在
            $session_dir = __DIR__ . '/app/cache/sessions';
            if (!is_dir($session_dir)) {
                mkdir($session_dir, 0755, true);
            }

            $success = true;
        } catch (Exception $e) {
            $error = '安装失败：' . $e->getMessage();
        }
    }
}

// ====================面渲染 ====================
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>安装向导 - Wishnet's Diary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #fef3e2 0%, #fde2e4 25%, #fce4ec 50%, #fff3e0 75%, #fef9f0 100%);
            background-attachment: fixed;
            color: #5c3d2e;
        }
        .card {
            background: linear-gradient(180deg, #fefcf5 0%, #faf5eb 50%, #f7f0e0 100%);
            border: 1px solid #d4b896;
            box-shadow: 0 8px 30px rgba(139,109,69,0.15);
        }
        .btn-primary {
            background: linear-gradient(135deg, #e76f51, #f4a261);
            transition: all 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(231,111,81,0.3);
        }
        .input-style {
            border: 1px solid #d4b896;
            background: rgba(255,255,255,0.85);
            color: #5c3d2e;
        }
        .input-style:focus {
            outline: none;
            border-color: #e76f51;
            box-shadow: 0 0 0 3px rgba(231,111,81,0.12);
        }
        .step-active {
            background: linear-gradient(135deg, #e76f51, #f4a261);
            color: white;
        }
        .step-done {
            background: #7aa874;
            color: white;
        }
        .step-pending {
            background: #e8d5c4;
            color: #8b7355;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="card rounded-2xl w-full max-w-2xl p-8 md:p-10">

        <!-- 头部 -->
        <div class="text-center mb-8">
            <div class="text-4xl mb-3">📔</div>
            <h1 class="text-2xl font-bold">Wishnet's Diary · 安装向导</h1>
        </div>

        <?php if ($already_installed && $step !== 3): ?>
            <!-- 已安装提示 -->
            <div class="text-center py-8">
                <div class="text-5xl mb-4">✅</div>
                <p class="text-lg mb-2" style="color: #7aa874;">系统已安装</p>
                <p class="text-sm mb-6" style="color: #a08972;">数据库文件已存在，无需重复安装。</p>
                <div class="flex gap-3 justify-center">
                    <a href="index.php" class="btn-primary text-white px-6 py-2.5 rounded-xl font-medium">进入前台</a>
                    <a href="index.php/admin" class="px-6 py-2.5 rounded-xl font-medium border" style="border-color: #d4b896; color: #6b4423;">管理后台</a>
                </div>
                <p class="mt-6 text-xs" style="color: #c4a88b;">如需重新安装，请先删除 public/data/riji.db</p>
            </div>

        <?php elseif ($step === 1): ?>
            <!-- 步骤1：欢迎 -->
            <div class="text-center mb-8">
                <p class="text-sm mb-6" style="color: #8b7355;">
                    安装程序将引导您完成数据库创建和基本配置。整个过程只需 1 分钟。
                </p>
            </div>

            <div class="space-y-3 mb-8">
                <div class="flex items-center gap-3 text-sm p-3 rounded-lg" style="background: rgba(122,168,116,0.08);">
                    <span>1️⃣</span>
                    <span style="color: #4a6741;">检查服务器环境是否满足运行要求</span>
                </div>
                <div class="flex items-center gap-3 text-sm p-3 rounded-lg" style="background: rgba(244,162,97,0.08);">
                    <span>2️⃣</span>
                    <span style="color: #8b6914;">配置管理员账号和网站基本设置</span>
                </div>
                <div class="flex items-center gap-3 text-sm p-3 rounded-lg" style="background: rgba(231,111,81,0.08);">
                    <span>3️⃣</span>
                    <span style="color: #a0522d;">一键完成安装</span>
                </div>
            </div>

            <div class="text-center">
                <a href="?step=2" class="btn-primary inline-block text-white px-8 py-3 rounded-xl font-medium text-lg">
                    开始安装 →
                </a>
            </div>

        <?php elseif ($step === 2): ?>
            <!-- 步骤2：环境检查 -->
            <h2 class="text-lg font-bold mb-4 text-center">🔍 环境检查</h2>

            <?php $checks = check_requirements(); ?>
            <?php $all_pass = true; ?>
            <div class="space-y-2 mb-6">
                <?php foreach ($checks as $check): ?>
                    <?php if (!$check['pass']) $all_pass = false; ?>
                    <div class="flex items-center justify-between p-3 rounded-lg text-sm <?php echo $check['pass'] ? 'bg-green-50' : 'bg-red-50'; ?>">
                        <span class="<?php echo $check['pass'] ? 'text-green-700' : 'text-red-700'; ?>">
                            <?php echo $check['pass'] ? '✅' : '❌'; ?> <?php echo $check['name']; ?>
                        </span>
                        <span class="<?php echo $check['pass'] ? 'text-green-600' : 'text-red-600'; ?> text-xs">
                            <?php echo $check['value']; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($all_pass): ?>
                <div class="text-center">
                    <p class="text-green-600 text-sm mb-4">✅ 环境检查全部通过</p>
                    <a href="?step=3" class="btn-primary inline-block text-white px-8 py-3 rounded-xl font-medium">
                        下一步：配置 →
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700 mb-4">
                    请先解决以上红色标记的问题，然后刷新页面重试。
                </div>
                <a href="?step=2" class="block text-center text-sm" style="color: #c17f59;">🔄 重新检查</a>
            <?php endif; ?>

        <?php elseif ($step === 3 && !$success): ?>
            <!-- 步骤3：配置 -->
            <h2 class="text-lg font-bold mb-6 text-center">⚙️ 基本配置</h2>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
                    ❌ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="?step=3" class="space-y-5">
                <!-- 管理员设置 -->
                <div class="p-4 rounded-xl" style="background: rgba(244,162,97,0.06); border: 1px solid rgba(212,184,150,0.3);">
                    <h3 class="font-medium mb-3" style="color: #6b4423;">👤 管理员账号</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs mb-1" style="color: #a08972;">用户名</label>
                            <input type="text" name="admin_username" value="admin" required
                                class="input-style w-full px-3 py-2.5 rounded-lg text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs mb-1" style="color: #a08972;">密码（至少6位）</label>
                                <input type="password" name="admin_password" value="admin123" required minlength="6"
                                    class="input-style w-full px-3 py-2.5 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs mb-1" style="color: #a08972;">确认密码</label>
                                <input type="password" name="admin_password2" value="admin123" required minlength="6"
                                    class="input-style w-full px-3 py-2.5 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 网站设置 -->
                <div class="p-4 rounded-xl" style="background: rgba(122,168,116,0.06); border: 1px solid rgba(180,200,160,0.3);">
                    <h3 class="font-medium mb-3" style="color: #4a6741;">🌐 网站设置</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs mb-1" style="color: #a08972;">网站名称</label>
                            <input type="text" name="site_name" value="Wishnet's Diary" required
                                class="input-style w-full px-3 py-2.5 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs mb-1" style="color: #a08972;">网站访问密码（前台需要输入此密码才能浏览）</label>
<<<<<<< HEAD
                            <input type="password" name="site_access_password" value="123456" required minlength="6"
=======
                            <input type="password" name="site_access_password" value="Wishnet_diary_2026" required minlength="6"
>>>>>>> 686fb4ee3c9d00d7daad0b6e45d8d7f8e2084162
                                class="input-style w-full px-3 py-2.5 rounded-lg text-sm">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full text-white py-3 rounded-xl font-medium text-base">
                    🚀 开始安装
                </button>
            </form>

        <?php elseif ($step === 3 && $success): ?>
            <!-- 安装完成 -->
            <div class="text-center py-4">
                <div class="text-6xl mb-4">🎉</div>
                <h2 class="text-xl font-bold mb-2" style="color: #7aa874;">安装完成！</h2>
                <p class="text-sm mb-6" style="color: #8b7355;">Wishnet's Diary 已成功安装。</p>

                <div class="p-4 rounded-xl mb-6 text-left text-sm space-y-2" style="background: rgba(244,162,97,0.06); border: 1px solid rgba(212,184,150,0.3);">
                    <div class="flex justify-between">
                        <span style="color: #a08972;">管理员用户名</span>
                        <span class="font-medium" style="color: #5c3d2e;"><?php echo htmlspecialchars($admin_username); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span style="color: #a08972;">网站名称</span>
                        <span class="font-medium" style="color: #5c3d2e;"><?php echo htmlspecialchars($site_name); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span style="color: #a08972;">前台访问密码</span>
                        <span class="font-medium" style="color: #5c3d2e;"><?php echo htmlspecialchars($site_access_password); ?></span>
                    </div>
                </div>

                <div class="flex gap-3 justify-center mb-6">
                    <a href="index.php" class="btn-primary text-white px-6 py-2.5 rounded-xl font-medium">📖 进入前台</a>
                    <a href="index.php/admin" class="px-6 py-2.5 rounded-xl font-medium border" style="border-color: #d4b896; color: #6b4423;">⚙️ 管理后台</a>
                </div>

                <div class="bg-orange-50 border border-orange-200 rounded-xl p-3 text-xs" style="color: #c17f59;">
                    ⚠️ <strong>安全提示：</strong>请立即删除 <code class="bg-orange-100 px-1 rounded">install.php</code> 文件，防止被他人利用。
                </div>
            </div>
        <?php endif; ?>

        <!-- 步骤指示器 -->
        <?php if (!$success && !$already_installed): ?>
        <div class="flex justify-center gap-2 mt-8 pt-6 border-t" style="border-color: rgba(212,184,150,0.3);">
            <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold <?php echo $step >= 1 ? 'step-active' : 'step-pending'; ?>">1</span>
            <span class="flex items-center text-xs" style="color: #c4a88b;">───</span>
            <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold <?php echo $step >= 2 ? ($step > 2 ? 'step-done' : 'step-active') : 'step-pending'; ?>">2</span>
            <span class="flex items-center text-xs" style="color: #c4a88b;">───</span>
            <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold <?php echo $step >= 3 ? 'step-active' : 'step-pending'; ?>">3</span>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
