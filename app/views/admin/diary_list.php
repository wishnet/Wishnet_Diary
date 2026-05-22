<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">📝 日记管理</h2>
        <a href="<?php echo site_url('admin/diary/create'); ?>"
            class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
            ✏️ 写新日记
        </a>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-4 text-sm">
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($diaries)): ?>
        <div class="text-center py-12 text-gray-400">
            <div class="text-5xl mb-4">📝</div>
            <p>No 篇日记 yet, <a href="<?php echo site_url('admin/diary/create'); ?>" class="text-indigo-500 hover:underline">写一篇吧</a></p>
        </div>
    <?php else: ?>
        <form id="batch-form" method="post" action="<?php echo site_url('admin/diary/batch'); ?>">
            <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
            <!-- 批量操作栏 -->
            <div id="batch-bar" class="hidden bg-indigo-50 border border-indigo-200 rounded-lg px-4 py-3 mb-4 flex items-center gap-4">
                <span class="text-sm text-indigo-700">
                    已选择 <strong id="selected-count">0</strong> 篇
                </span>
                <button type="button" onclick="batch删除()"
                    class="bg-red-500 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-red-600 transition">
                    🗑 批量删除
                </button>
                <button type="button" onclick="clearSelection()"
                    class="text-gray-500 text-xs hover:text-gray-700">
                    取消选择
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-gray-500">
                            <th class="pb-3 w-10">
                                <input type="checkbox" id="select-all" onclick="toggleAll(this)" class="rounded">
                            </th>
                            <th class="pb-3 font-medium">ID</th>
                            <th class="pb-3 font-medium">标题</th>
                            <th class="pb-3 font-medium">日期</th>
                            <th class="pb-3 font-medium">心情</th>
                            <th class="pb-3 font-medium">天气</th>
                            <th class="pb-3 font-medium">标签</th>
                            <th class="pb-3 font-medium">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($diaries as $diary): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3">
                                <input type="checkbox" name="ids[]" value="<?php echo $diary['id']; ?>" class="diary-check rounded" onclick="updateBatchBar()">
                            </td>
                            <td class="py-3 text-gray-400">#<?php echo $diary['id']; ?></td>
                            <td class="py-3 font-medium text-gray-800 max-w-xs truncate">
                                <?php echo htmlspecialchars($diary['title']); ?>
                            </td>
                            <td class="py-3 text-gray-500"><?php echo $diary['diary_date']; ?></td>
                            <td class="py-3"><?php echo mood_emoji($diary['mood']); ?> <?php echo $diary['mood']; ?></td>
                            <td class="py-3"><?php echo weather_emoji($diary['weather']); ?> <?php echo $diary['weather']; ?></td>
                            <td class="py-3">
                                <?php foreach (parse_tags($diary['tags']) as $t): ?>
                                    <span class="inline-block bg-gray-100 text-gray-600 rounded-full px-2 py-0.5 text-xs mr-1"><?php echo htmlspecialchars($t); ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td class="py-3">
                                <div class="flex gap-2">
                                    <a href="<?php echo site_url('admin/diary/edit/' . $diary['id']); ?>"
                                        class="text-indigo-500 hover:text-indigo-700 text-xs">编辑</a>
                                    <a href="javascript:void(0)" onclick="confirm删除(<?php echo $diary['id']; ?>, '<?php echo htmlspecialchars(addslashes($diary['title'])); ?>')"
                                        class="text-red-500 hover:text-red-700 text-xs">删除</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
        <?php echo $pagination; ?>
    <?php endif; ?>
</div>

<script>
// 全选/反选
function toggleAll(checkbox) {
    var checks = document.querySelectorAll('.diary-check');
    checks.forEach(function(c) { c.checked = checkbox.checked; });
    updateBatchBar();
}

// 更新批量操作栏
function updateBatchBar() {
    var checked = document.querySelectorAll('.diary-check:checked');
    var count = checked.length;
    var bar = document.getElementById('batch-bar');
    var all = document.getElementById('select-all');

    if (count > 0) {
        bar.classList.remove('hidden');
        document.getElementById('selected-count').text内容 = count;
    } else {
        bar.classList.add('hidden');
    }

    // 更新全选状态
    var total = document.querySelectorAll('.diary-check').length;
    all.checked = (count === total && total > 0);
    all.indeterminate = (count > 0 && count < total);
}

// 批量删除
function batch删除() {
    var checked = document.querySelectorAll('.diary-check:checked');
    if (checked.length === 0) return;

    if (!confirm('确定要删除选中的 ' + checked.length + ' 篇日记吗？此操作不可撤销。')) return;

    var form = document.getElementById('batch-form');
    var actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'delete';
    form.appendChild(actionInput);
    form.submit();
}

// 清除选择
function clearSelection() {
    document.querySelectorAll('.diary-check').forEach(function(c) { c.checked = false; });
    document.getElementById('select-all').checked = false;
    document.getElementById('select-all').indeterminate = false;
    updateBatchBar();
}

// 单个删除
function confirm删除(id, title) {
    if (confirm('确定要删除日记「' + title + '」吗？此操作不可撤销。')) {
        window.location.href = '<?php echo site_url('admin/diary/delete/'); ?>' + id;
    }
}
</script>
