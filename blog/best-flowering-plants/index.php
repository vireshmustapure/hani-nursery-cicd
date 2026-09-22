<?php
$meta = [
    'title' => '7 Best Flowering Plants You Should Invest In Today',
    'meta_title' => '7 Best Flowering Plants You Should Invest In Today',
    'meta_description' => '7 Best Flowering Plants You Should Invest In Today',
    'banner' => '/uploads/c0b8e00827278fda8536c470e3a3afa6.webp',
    'author_id' => '2',
    'published_at' => '2026-06-24T09:27',
    'category' => 'General'
];
?>
<?php
$base = "../../";
include '../../config/db.php';
$settings_res = mysqli_query($conn, "SELECT * FROM settings");
$settings = [];
if ($settings_res) {
    while ($row = mysqli_fetch_assoc($settings_res)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($meta['meta_title'] ?: $meta['title']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta['meta_description']); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body>
<?php include '../../includes/header.php'; ?>
<main class="py-12 bg-slate-50 min-h-screen">
  <div class="max-w-6xl mx-auto px-6">
    <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">
      <a href="../../" class="hover:text-primary transition-colors">Home</a>
      <span>/</span>
      <a href="../" class="hover:text-primary transition-colors">Blog</a>
      <span>/</span>
      <span class="text-slate-600"><?php echo htmlspecialchars($meta['category']); ?></span>
    </div>
    <h1 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight mb-8 leading-tight">
      <?php echo htmlspecialchars($meta['title']); ?>
    </h1>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <div class="lg:col-span-8 bg-white p-6 md:p-8 rounded-3xl border border-slate-200/60 shadow-sm space-y-8">
        <?php if (!empty($meta['banner'])): 
            $banner = $meta['banner'];
            if (strpos($banner, 'http') === false) {
                $banner = '../../' . ltrim($banner, '/');
            }
        ?>
            <div class="w-full rounded-2xl overflow-hidden border border-slate-100 flex justify-center bg-slate-50">
                <img src="<?php echo htmlspecialchars($banner); ?>" class="max-h-[380px] w-auto object-contain" alt="Blog Banner">
            </div>
        <?php endif; ?>
        <div class="flex items-center gap-6 border-y border-slate-100 py-4 text-xs font-semibold text-slate-500">
            <span>Published: <?php echo date('F d, Y', strtotime($meta['published_at'])); ?></span>
            <span>•</span>
            <span>Category: <span class="text-emerald-700 font-bold"><?php echo htmlspecialchars($meta['category']); ?></span></span>
        </div>
        <article class="prose max-w-none text-slate-600 leading-relaxed">
            <?php include 'body.php'; ?>
        </article>
      </div>
      <aside class="lg:col-span-4 space-y-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4 pb-2 border-b border-slate-100">Categories</h3>
            <div class="flex flex-col gap-2">
                <?php
                try {
                    $cat_res = mysqli_query($conn, "SELECT DISTINCT category FROM posts WHERE status='published' AND category IS NOT NULL AND category != ''");
                    if ($cat_res && mysqli_num_rows($cat_res) > 0) {
                        while ($cat_row = mysqli_fetch_assoc($cat_res)) {
                            $cat_name = $cat_row['category'];
                            echo '<a href="../?category=' . urlencode($cat_name) . '" class="text-xs font-bold text-slate-600 hover:text-emerald-750 transition-colors flex items-center justify-between py-2 px-3 hover:bg-slate-50 rounded-xl">';
                            echo '<span>🌿 ' . htmlspecialchars($cat_name) . '</span>';
                            echo '</a>';
                        }
                    } else {
                        echo '<span class="text-xs text-slate-400 font-medium">No categories found.</span>';
                    }
                } catch (Exception $e) {
                    echo '<span class="text-xs text-slate-400 font-medium">No categories found.</span>';
                }
                ?>
            </div>
        </div>
        <div class="bg-gradient-to-br from-emerald-800 to-emerald-950 p-6 rounded-3xl text-white shadow-xl text-center space-y-4">
            <h4 class="font-black text-lg">Visit Hani Nursery 🌿</h4>
            <p class="text-xs text-emerald-100 leading-relaxed">Bring nature and harmony home today. Check out our physical store for exclusive counter discounts!</p>
            <a href="../../contact/" class="inline-block bg-white text-emerald-900 px-6 py-2.5 rounded-full text-xs font-black uppercase tracking-wider hover:bg-emerald-50 transition-colors shadow-md">Get Directions ➔</a>
        </div>
      </aside>
    </div>
  </div>
</main>
<?php include '../../includes/footer.php'; ?>
</body>
</html>