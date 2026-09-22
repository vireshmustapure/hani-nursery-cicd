<header class="mb-10 pt-8">
    <div class="flex items-center gap-3 mb-4">
        <span class="px-3 py-1 bg-emerald-50 text-emerald-800 text-[10px] font-black uppercase tracking-widest rounded-full">
            <?php echo htmlspecialchars($meta['category'] ?? 'General'); ?>
        </span>
        <span class="text-xs text-slate-400 font-medium">
            <?php echo date('F d, Y', strtotime($meta['published_at'] ?? 'now')); ?>
        </span>
    </div>
    
    <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-6">
        <?php echo htmlspecialchars($meta['title'] ?? ''); ?>
    </h1>

    <?php if (!empty($meta['banner'])): 
        $banner = $meta['banner'];
        if (strpos($banner, 'http') === false) {
            $banner = '../../' . ltrim($banner, '/');
        }
    ?>
        <div class="w-full h-96 rounded-3xl overflow-hidden shadow-lg mb-8">
            <img src="<?php echo htmlspecialchars($banner); ?>" class="w-full h-full object-cover">
        </div>
    <?php endif; ?>
</header>
