<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理后台 - <?php echo $site_name; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #fef3e2 0%, #fde2e4 25%, #fce4ec 50%, #fff3e0 75%, #fef9f0 100%);
            background-attachment: fixed;
        }
        /* 装饰光晕 */
        body::before {
            content: '';
            position: fixed;
            top: -20%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(244,162,97,0.2) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -15%;
            left: -8%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(231,111,81,0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* 登录卡片 */
        .login-card {
            background:
                linear-gradient(180deg, #fefcf5 0%, #faf5eb 30%, #f7f0e0 60%, #faf5eb 100%),
                repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(139,109,69,0.02) 2px, rgba(139,109,69,0.02) 4px);
            border: 1px solid #d4b896;
            box-shadow:
                0 8px 40px rgba(139,109,69,0.2),
                0 1px 3px rgba(0,0,0,0.08),
                inset 0 0 40px rgba(139,109,69,0.03);
        }
        .login-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(ellipse at 50% 0%, rgba(255,255,240,0.5) 0%, transparent 70%);
            pointer-events: none;
            border-radius: inherit;
        }

        /* 装订线 */
        .binding-line {
            background: linear-gradient(180deg, #d4a574, #e8c9a0, #d4a574);
        }

        /* 输入框 */
        .input-field {
            background: rgba(255,255,255,0.85);
            border: 1px solid #d4b896;
            color: #5c3d2e;
            transition: all 0.3s ease;
        }
        .input-field:focus {
            outline: none;
            border-color: #e76f51;
            box-shadow: 0 0 0 3px rgba(231,111,81,0.12);
            background: #fff;
        }
        .input-field::placeholder {
            color: #c4a88b;
        }

        /* 登录按钮 */
        .login-btn {
            background: linear-gradient(135deg, #e76f51 0%, #f4a261 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(231,111,81,0.3);
        }
        .login-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(231,111,81,0.4);
        }
        .login-btn:active {
            transform: translateY(0);
        }

        /* 加载动画 */
        .login-btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        /* 错误提示动画 */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-4px); }
            40%, 80% { transform: translateX(4px); }
        }
        .error-shake {
            animation: shake 0.5s ease;
        }

        /* 羽毛笔装饰动画 */
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(-5deg); }
            50% { transform: translateY(-8px) rotate(0deg); }
        }
        .quill-float {
            animation: float 4s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative z-10">

    <!-- 登录卡片 -->
    <div class="login-card rounded-2xl w-full max-w-md mx-4 relative overflow-hidden">
        <!-- 装订线 -->
        <div class="absolute left-0 top-0 bottom-0 w-1.5 rounded-l-2xl binding-line"></div>

        <div class="relative z-10 p-10 pl-12">
            <!-- 头部：羽毛笔图标 -->
            <div class="text-center mb-8">
                <div class="quill-float inline-block text-5xl mb-3 select-none">🖊️</div>
                <h1 class="text-2xl font-bold mb-1" style="color: #5c3d2e;"><?php echo $site_name; ?></h1>
                <p style="color: #a08972; font-size: 0.9rem;">✧ 管理后台 · 记录时光 ✧</p>
                <!-- 装饰分割线 -->
                <div class="mt-4 mb-1" style="height: 1px; background: linear-gradient(to right, transparent, #d4a574, transparent);"></div>
            </div>

            <?php if (!empty($error)): ?>
            <div class="error-shake rounded-lg px-4 py-3 mb-6 text-sm" style="background: rgba(231,111,81,0.08); color: #c0392b; border: 1px solid rgba(231,111,81,0.2);">
                ⚠️ <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form method="post" action="<?php echo site_url('admin/login'); ?>" id="login-form">
                <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">

                <!-- 用户名 -->
                <div class="mb-5">
                    <label class="block text-sm font-medium mb-2" style="color: #6b4423;">👤 用户名</label>
                    <input type="text" name="username" required autocomplete="username"
                        class="input-field w-full px-4 py-3 rounded-xl text-base"
                        placeholder="请输入用户名">
                </div>

                <!-- 密码 -->
                <div class="mb-5">
                    <label class="block text-sm font-medium mb-2" style="color: #6b4423;">🔑 密码</label>
                    <input type="password" name="password" required autocomplete="current-password"
                        class="input-field w-full px-4 py-3 rounded-xl text-base"
                        placeholder="请输入密码">
                </div>

                <!-- 验证码 -->
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2" style="color: #6b4423;">🔏 验证码</label>
                    <div class="flex gap-3 items-center">
                        <input type="text" name="captcha" required maxlength="4" autocomplete="off"
                            class="input-field flex-1 px-4 py-3 rounded-xl text-base uppercase tracking-widest text-center"
                            placeholder="输入验证码"
                            style="letter-spacing: 0.5em;">
                        <img src="<?php echo site_url('admin/captcha'); ?>" 
                             alt="验证码" title="点击刷新"
                             class="rounded-lg cursor-pointer border hover:opacity-80 transition"
                             style="border-color: #d4b896; height: 48px; width: 110px;"
                             onclick="this.src='<?php echo site_url('admin/captcha'); ?>?'+日期.now()">
                    </div>
                </div>

                <!-- 登录按钮 -->
                <button type="submit" class="login-btn w-full text-white py-3 rounded-xl font-medium text-base tracking-wider">
                    进 入 后 台
                </button>
            </form>

            <!-- 底部链接 -->
            <div class="mt-6 text-center">
                <a href="<?php echo site_url('/'); ?>" class="text-sm inline-flex items-center gap-1 transition hover:opacity-70" style="color: #c17f59;">
                    ← 返回日记
                </a>
            </div>
        </div>
    </div>

    <script>
    // 提交时按钮显示加载态
    document.getElementById('login-form').addEventListener('submit', function() {
        var btn = this.querySelector('button[type="submit"]');
        btn.classList.add('loading');
        btn.innerHTML = '验证中...';
    });
    </script>

</body>
</html>
