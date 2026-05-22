<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-6">
        <?php echo $action === 'create' ? '✏️ 写新日记' : '✏️ 编辑日记'; ?>
    </h2>

    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-6 text-sm">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- SimpleMDE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <style>
        .editor-toolbar { border-radius: 0.5rem 0.5rem 0 0; border-color: #d1d5db; }
        .CodeMirror { border-radius: 0 0 0.5rem 0.5rem; border-color: #d1d5db; }
        .editor-toolbar.fullscreen { border-radius: 0; }
        .CodeMirror-fullscreen { border-radius: 0; }
        .editor-preview { background: #fafafa; }
    </style>

    <form method="post" action="" id="diary-form">
        <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- 标题 -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">标题 <span class="text-red-500">*</span></label>
                <input type="text" name="title" required value="<?php echo htmlspecialchars($diary['title'] ?? ''); ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                    placeholder="给日记起个标题">
            </div>

            <!-- 日期 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">日期 <span class="text-red-500">*</span></label>
                <input type="date" name="diary_date" required value="<?php echo $diary['diary_date'] ?? date('Y-m-d'); ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
            </div>

            <!-- 心情 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">心情</label>
                <select name="mood"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-white">
                    <?php foreach ($moods as $m): ?>
                        <option value="<?php echo $m; ?>" <?php echo ($diary['mood'] ?? '') === $m ? 'selected' : ''; ?>>
                            <?php echo mood_emoji($m); ?> <?php echo $m; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 天气 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">天气</label>
                <select name="weather"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-white">
                    <?php foreach ($weathers as $w): ?>
                        <option value="<?php echo $w; ?>" <?php echo ($diary['weather'] ?? '') === $w ? 'selected' : ''; ?>>
                            <?php echo weather_emoji($w); ?> <?php echo $w; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 标签 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">标签</label>
                <input type="text" name="tags" value="<?php echo htmlspecialchars($diary['tags'] ?? ''); ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                    placeholder="多个标签用逗号分隔">
            </div>
        </div>

        <!-- 内容 -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">内容 <span class="text-red-500">*</span></label>
            <textarea name="content" id="content-editor"><?php echo htmlspecialchars($diary['content'] ?? ''); ?></textarea>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition shadow-sm">
                💾 保存
            </button>
            <a href="<?php echo site_url('admin/diary'); ?>"
                class="bg-white text-gray-700 px-6 py-2.5 rounded-lg font-medium border border-gray-300 hover:bg-gray-50 transition">
                取消
            </a>
        </div>
    </form>
</div>

<!-- SimpleMDE JS -->
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<script>
    var simplemde = new SimpleMDE({
        element: document.getElementById("content-editor"),
        spellChecker: false,
        placeholder: "用 Markdown 记录今天的点滴...",
        status: false,
        toolbar: [
            "bold", "italic", "heading", "|",
            "quote", "unordered-list", "ordered-list", "|",
            "link", "image", "|",
            "preview", "side-by-side", "fullscreen", "|",
            "guide"
        ],
        renderingConfig: {
            singleLineBreaks: true,
        }
    });

    // 表单提交前同步编辑器内容到 textarea
    document.getElementById("diary-form").addEventListener("submit", function(e) {
        // 把编辑器内容写回隐藏的 textarea
        var content = simplemde.value();
        if (!content || content.trim() === "") {
            e.preventDefault();
            alert("请填写日记内容");
            return false;
        }
        document.getElementById("content-editor").value = content;
    });
</script>
