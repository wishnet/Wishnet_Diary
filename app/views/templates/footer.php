<!--脚 -->
<footer class="mt-16 border-t" style="border-color: rgba(212,184,150,0.3);">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-xs" style="color: #a08972;">
            <div class="flex items-center gap-2">
                <span>📔</span>
                <span><?php echo isset($site_name) ? htmlspecialchars($site_name) : "Yunman's Diariy"; ?></span>
                <span>© <?php echo date('Y'); ?></span>
            </div>
            <div class="flex items-center gap-4">
                <span>记录生活的点点滴滴</span>
                <span style="color: #c4a88b;">|</span>
                <span><?php echo htmlspecialchars($this->setting_model->get('icp_beian', '')); ?></span>
            </div>
            <div>
                Powered by <span style='color: #e76f51;'>Yunman's Diariy</span>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
