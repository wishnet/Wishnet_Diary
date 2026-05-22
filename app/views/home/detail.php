<header class="sticky top-0 z-40 bg-white/70 backdrop-blur-lg border-b border-orange-100">
    <div class="max-w-3xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="<?php echo site_url('/'); ?>" class="text-lg font-bold" style="color: #e76f51;">← <?php echo $site_name; ?></a>
    </div>
</header>

<main class="max-w-3xl mx-auto px-4 py-10">
    <article class="book-paper rounded-lg p-8 md:p-10 relative">
        <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-lg" style="background: linear-gradient(180deg, #d4a574, #e8c9a0, #d4a574);"></div>
        <div class="pl-3">
            <h1 class="text-3xl font-bold mb-4" style="color: #5c3d2e;"><?php echo htmlspecialchars($diary['title']); ?></h1>
            <div class="flex flex-wrap items-center gap-3 text-sm mb-4" style="color: #a08972;">
                <span>📅 <?php echo format_date_cn($diary['diary_date']); ?></span>
                <?php if (!empty($diary['mood'])): ?><span class="px-2.5 py-0.5 rounded-full" style="background:rgba(231,111,81,0.1);color:#e76f51;"><?php echo mood_emoji($diary['mood']); ?> <?php echo $diary['mood']; ?></span><?php endif; ?>
                <?php if (!empty($diary['weather'])): ?><span class="px-2.5 py-0.5 rounded-full" style="background:rgba(244,162,97,0.1);color:#c17f59;"><?php echo weather_emoji($diary['weather']); ?> <?php echo $diary['weather']; ?></span><?php endif; ?>
            </div>
            <hr class="book-divider">
            <div class="book-content"><?php echo $content_html; ?></div>
            <?php $tags = parse_tags($diary['tags']); if (!empty($tags)): ?>
            <div class="mt-6 pt-4" style="border-top:1px solid #d4b896;"><div class="flex flex-wrap gap-2">
                <?php foreach ($tags as $t): ?><a href="<?php echo site_url('tag/'.urlencode($t)); ?>" class="text-sm px-3 py-1 rounded-full" style="background:rgba(212,184,150,0.25);color:#8b6914;">#<?php echo htmlspecialchars($t); ?></a><?php endforeach; ?>
            </div></div>
            <?php endif; ?>
        </div>
    </article>
    <div class="text-center mt-8"><a href="<?php echo site_url('/'); ?>" class="inline-flex items-center gap-2" style="color:#c17f59;">← 返回首页</a></div>
</main>
