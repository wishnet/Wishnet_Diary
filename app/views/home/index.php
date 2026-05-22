<!-- 头部导航 -->
<header class="sticky top-0 z-40 bg-white/70 backdrop-blur-lg border-b border-orange-100">
    <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="<?php echo site_url('/'); ?>" class="text-2xl font-bold" style="color: #e76f51;">
            📔 <?php echo $site_name; ?>
        </a>
        <form action="<?php echo site_url('search'); ?>" method="get" class="flex">
            <input type="text" name="q"
                class="w-40 md:w-56 px-3 py-1.5 text-sm border border-orange-200 rounded-l-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none bg-white/80"
                placeholder="搜索标题或标签...">
            <button type="submit" class="text-white px-4 py-1.5 text-sm rounded-r-lg transition" style="background: #e76f51;">🔍</button>
        </form>
    </div>
</header>

<!-- 主体：内容 + 侧边栏 -->
<div class="max-w-5xl mx-auto px-4 py-10 flex gap-8">
    <!-- 主内容 -->
    <main class="flex-1 min-w-0">
        <?php if (isset($month_filter)): ?>
            <div class="mb-6 flex items-center justify-between p-3 rounded-xl" style="background: rgba(244,162,97,0.08); border: 1px solid rgba(244,162,97,0.2);">
                <span class="text-sm" style="color: #6b4423;">
                    📅 筛选：<strong><?php echo date('Y年n月', strtotime($month_filter . '-01')); ?></strong>
                </span>
                <a href="<?php echo site_url('/'); ?>" class="text-xs px-3 py-1 rounded-lg transition" style="color: #e76f51; background: rgba(231,111,81,0.08);">
                    清除筛选
                </a>
            </div>
        <?php endif; ?>
        <?php if (empty($grouped_diaries)): ?>
            <div class="text-center py-20">
                <div class="text-6xl mb-6">📝</div>
                <p class="text-lg" style="color: #a08972;">No 篇日记 yet, 期待第一篇...</p>
            </div>
        <?php else: ?>
            <div class="relative">
                <div class="hidden md:block timeline-line"></div>
                <?php foreach ($grouped_diaries as $ym => $group): ?>
                <div class="text-center mb-8 relative z-10">
                    <span class="inline-block text-white text-sm font-medium px-5 py-1.5 rounded-full shadow-md"
                        style="background: linear-gradient(135deg, #e76f51, #f4a261);">
                        <?php echo $group['label']; ?>
                    </span>
                </div>
                <?php foreach ($group['diaries'] as $diary): ?>
                <div class="relative mb-8">
                    <div class="flex flex-col md:flex-row items-start gap-4">
                        <div class="hidden md:flex md:w-1/2 justify-end pr-10 pt-1">
                            <div class="text-right">
                                <div class="text-xs" style="color: #d4a574;"><?php echo date('Y', strtotime($diary['diary_date'])); ?></div>
                                <div class="text-2xl font-bold" style="color: #5c3d2e;"><?php echo date('j', strtotime($diary['diary_date'])); ?></div>
                                <div class="text-sm" style="color: #c17f59;"><?php echo date('m', strtotime($diary['diary_date'])); ?>月</div>
                            </div>
                        </div>
                        <div class="hidden md:flex absolute left-1/2 transform -translate-x-1/2 timeline-dot"></div>
                        <div class="md:w-1/2 md:pl-10 w-full">
                            <div class="md:hidden text-sm mb-1" style="color: #c17f59;"><?php echo format_date_cn($diary['diary_date']); ?></div>
                            <div class="cursor-pointer" onclick="openDiary(<?php echo $diary['id']; ?>)">
                                <div class="rounded-xl shadow-sm border border-orange-100 p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg bg-white/75 backdrop-blur-sm"
                                    style="border-left: 3px solid #f4a261;">
                                    <h3 class="text-lg font-semibold mb-2" style="color: #5c3d2e;"><?php echo htmlspecialchars($diary['title']); ?></h3>
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <?php if (!empty($diary['mood'])): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(231,111,81,0.1);color:#e76f51;"><?php echo mood_emoji($diary['mood']); ?> <?php echo $diary['mood']; ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($diary['weather'])): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(244,162,97,0.1);color:#c17f59;"><?php echo weather_emoji($diary['weather']); ?> <?php echo $diary['weather']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-sm leading-relaxed" style="color:#8b7355;"><?php echo text_excerpt($diary['content'], 150); ?></p>
                                    <?php $tags = parse_tags($diary['tags']); if (!empty($tags)): ?>
                                    <div class="flex flex-wrap gap-1 mt-3">
                                        <?php foreach ($tags as $t): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(212,184,150,0.2);color:#a08972;">#<?php echo htmlspecialchars($t); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
            <?php if ($total > ($per_page ?? 10)): ?>
                <div class="text-center text-sm mt-4" style="color: #a08972;">共 <?php echo $total; ?>  篇日记, page <?php echo $current_page ?? 1; ?></div>
                <?php echo $pagination; ?>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <!-- 侧边栏 -->
    <?php $this->load->view('templates/sidebar'); ?>
</div>

<!-- 弹窗 -->
<div id="diary-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-stone-900/60 backdrop-blur-sm" onclick="closeDiary()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="book-paper rounded-lg w-full max-w-2xl max-h-[85vh] overflow-y-auto relative z-10" onclick="event.stopPropagation()">
            <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-lg" style="background: linear-gradient(180deg, #d4a574, #e8c9a0, #d4a574);"></div>
            <button onclick="closeDiary()" class="absolute top-4 right-5 text-2xl leading-none z-20" style="color: #c17f59;">&times;</button>
            <div id="diary-modal-loading" class="flex items-center justify-center py-20">
                <div class="animate-pulse" style="color: #c17f59;">📖 翻阅中...</div>
            </div>
            <div id="diary-modal-content" class="hidden p-8 pl-10">
                <h1 id="modal-title" class="text-2xl font-bold pr-8 mb-4" style="color: #5c3d2e;"></h1>
                <div id="modal-meta" class="flex flex-wrap items-center gap-3 text-sm mb-4" style="color: #a08972;"></div>
                <hr class="book-divider">
                <div id="modal-body" class="book-content"></div>
                <div id="modal-tags" class="mt-6 pt-4 border-t" style="border-color: #d4b896;"></div>
            </div>
        </div>
    </div>
</div>

<script>
function openDiary(id) {
    var modal = document.getElementById('diary-modal');
    document.getElementById('diary-modal-loading').classList.remove('hidden');
    document.getElementById('diary-modal-content').classList.add('hidden');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    fetch('<?php echo site_url('ajax/diary/'); ?>' + id)
        .then(function(r){ return r.json(); })
        .then(function(d){
            document.getElementById('diary-modal-loading').classList.add('hidden');
            document.getElementById('diary-modal-content').classList.remove('hidden');
            if(d.error){ document.getElementById('modal-title').text内容='错误'; document.getElementById('modal-body').innerHTML='<p style="color:#c0392b;">'+d.error+'</p>'; return; }
            document.getElementById('modal-title').text内容 = d.title;
            var m = '<span>📅 '+d.date_cn+'</span>';
            if(d.mood) m+='<span class="px-2.5 py-0.5 rounded-full" style="background:rgba(231,111,81,0.1);color:#e76f51;">'+d.mood_emoji+' '+d.mood+'</span>';
            if(d.weather) m+='<span class="px-2.5 py-0.5 rounded-full" style="background:rgba(244,162,97,0.1);color:#c17f59;">'+d.weather_emoji+' '+d.weather+'</span>';
            document.getElementById('modal-meta').innerHTML = m;
            document.getElementById('modal-body').innerHTML = d.content_html;
            var t='';
            if(d.tags_array&&d.tags_array.length>0){ t='<div class="flex flex-wrap gap-2">'; d.tags_array.forEach(function(tg){ t+='<a href="<?php echo site_url('tag/'); ?>'+encodeURIComponent(tg)+'" class="text-sm px-3 py-1 rounded-full" style="background:rgba(212,184,150,0.25);color:#8b6914;">#'+tg+'</a>'; }); t+='</div>'; }
            document.getElementById('modal-tags').innerHTML = t;
        }).catch(function(){
            document.getElementById('diary-modal-loading').classList.add('hidden');
            document.getElementById('diary-modal-content').classList.remove('hidden');
            document.getElementById('modal-title').text内容='加载失败';
            document.getElementById('modal-body').innerHTML='<p style="color:#c0392b;">无法加载日记</p>';
        });
}
function closeDiary(){ document.getElementById('diary-modal').classList.add('hidden'); document.body.style.overflow=''; }
</script>
