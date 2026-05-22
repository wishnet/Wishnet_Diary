<!-- 侧边栏 -->
<aside class="w-64 flex-shrink-0 hidden lg:block">
    <div class="sticky top-20 space-y-6">

        <!-- 月份归档 -->
        <div class="rounded-xl p-5" style="background: rgba(255,255,255,0.7); border: 1px solid rgba(212,184,150,0.4);">
            <h3 class="text-sm font-bold mb-3 flex items-center gap-2" style="color: #6b4423;">
                📅 月份归档
            </h3>
            <?php if (empty($monthly_archive)): ?>
                <p class="text-xs" style="color: #c4a88b;">暂无归档</p>
            <?php else: ?>
                <ul class="space-y-1.5">
                    <?php foreach ($monthly_archive as $m): ?>
                        <li>
                            <a href="<?php echo site_url('?month=' . $m['year_month']); ?>"
                                class="flex justify-between items-center text-xs py-1 px-2 rounded-lg transition-all hover:bg-orange-50 group"
                                style="color: #8b7355;">
                                <span class="group-hover:text-orange-600 transition">
                                    <?php echo date('Y年n月', strtotime($m['year_month'] . '-01')); ?>
                                </span>
                                <span class="rounded-full px-2 py-0.5 text-xs"
                                    style="background: rgba(244,162,97,0.12); color: #c17f59;">
                                    <?php echo $m['count']; ?>
                                </span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- 标签云 -->
        <div class="rounded-xl p-5" style="background: rgba(255,255,255,0.7); border: 1px solid rgba(212,184,150,0.4);">
            <h3 class="text-sm font-bold mb-3 flex items-center gap-2" style="color: #6b4423;">
                🏷️ 标签
            </h3>
            <?php if (empty($sidebar_tags)): ?>
                <p class="text-xs" style="color: #c4a88b;">暂无标签</p>
            <?php else: ?>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($sidebar_tags as $tag => $count): ?>
                        <a href="<?php echo site_url('tag/' . $tag); ?>"
                            class="inline-block px-2.5 py-1 rounded-full text-xs transition-all hover:shadow-sm"
                            style="background: rgba(231,111,81,0.08); color: #e76f51; border: 1px solid rgba(231,111,81,0.12);">
                            <?php echo htmlspecialchars($tag); ?>
                            <span style="color: #d4a574;">(<?php echo $count; ?>)</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 返回顶部 -->
        <div class="text-center">
            <a href="#" class="text-xs transition" style="color: #c4a88b;">
                ↑ 回到顶部
            </a>
        </div>
    </div>
</aside>
