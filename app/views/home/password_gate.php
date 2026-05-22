<div class="min-h-[80vh] flex items-center justify-center">
    <div class="text-center">
        <!-- 图标 -->
        <div class="text-6xl mb-6">🔐</div>
        <h1 class="text-2xl font-bold mb-2" style="color: #5c3d2e;"><?php echo $site_name; ?></h1>
        <p class="text-sm mb-8" style="color: #a08972;">请输入访问密码以继续</p>

        <?php if (!empty($error)): ?>
            <div class="inline-block text-sm px-4 py-2 rounded-lg mb-4" style="background: rgba(231,111,81,0.1); color: #e76f51;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo site_url('gate'); ?>" class="flex flex-col items-center gap-4">
            <input type="password" name="password" required autofocus
                class="w-64 px-4 py-3 border rounded-xl text-center text-lg outline-none transition"
                style="border-color: #d4b896; background: rgba(255,255,255,0.8); color: #5c3d2e;"
                placeholder="输入密码"
                onfocus="this.style.borderColor='#e76f51';this.style.boxShadow='0 0 0 3px rgba(231,111,81,0.15)'"
                onblur="this.style.borderColor='#d4b896';this.style.boxShadow='none'">

            <button type="submit"
                class="w-64 py-3 rounded-xl text-white font-medium text-lg transition shadow-md"
                style="background: linear-gradient(135deg, #e76f51, #f4a261);">
                进入日记
            </button>
        </form>
    </div>
</div>
