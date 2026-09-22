<?php
require_once 'auth_check.php';
require_once __DIR__ . '/../config/db.php';

// Handle Actions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete') {
        $slug = $_POST['slug'];
        $stmt = $pdo->prepare("DELETE FROM posts WHERE slug = ?");
        $stmt->execute([$slug]);
        $message = "Article permanently purged from repository.";
    } else if ($_POST['action'] === 'create_author') {
        $author_name = trim($_POST['author_name'] ?? '');
        $author_image = '';
        if (isset($_FILES['author_image']) && $_FILES['author_image']['error'] === UPLOAD_ERR_OK) {
            $img = $_FILES['author_image'];
            $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($ext, $allowed)) {
                $newName = md5(time() . rand()) . '.' . $ext;
                $uploadDir = __DIR__ . '/../uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                if (move_uploaded_file($img['tmp_name'], $uploadDir . $newName)) {
                    $author_image = '/uploads/' . $newName;
                }
            }
        } elseif (!empty($_POST['author_image_url'])) {
            $author_image = trim($_POST['author_image_url']);
        }
        
        if (!empty($author_name)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO authors (name, image) VALUES (?, ?)");
                $stmt->execute([$author_name, $author_image]);
                $message = "Author '$author_name' successfully registered.";
            } catch (Exception $e) {
                $message = "Error: Author name already exists or database issue.";
            }
        }
    }
}

// Fetch from DB - Prioritize Pinned, then Date
$posts = $pdo->query("SELECT p.*, a.name as author_name, a.image as author_img 
                      FROM posts p 
                      LEFT JOIN authors a ON p.author_name = a.name 
                      ORDER BY p.is_pinned DESC, p.published_at DESC")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Intelligence Hub | East-IND Admin';
include __DIR__ . '/../components/header.php';
include __DIR__ . '/sidebar.php';
?>

<style>
    .repo-card { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
    .repo-card:hover { transform: translateY(-4px); box-shadow: 0 40px 100px -20px rgba(0,0,0,0.05); border-color: rgba(79, 70, 229, 0.1); }
    .status-pill { padding: 4px 12px; border-radius: 12px; font-size: 10px; font-weight: 900; letter-spacing: 0.1em; }
    .search-glow:focus-within { box-shadow: 0 0 40px -10px rgba(79, 70, 229, 0.2); }
    .action-btn:hover { background: #0f172a; color: white; border-color: #0f172a; }
</style>

<div class="main">
<main class="flex-1 p-6 md:p-12 bg-slate-50/50 min-h-screen">
    <div class="max-w-[1400px] mx-auto">
        
        <!-- Expanded Header -->
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16">
            <div class="max-w-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-1 bg-indigo-600 rounded-full"></div>
                    <span class="text-[11px] font-black uppercase tracking-[0.4em] text-indigo-600/60">Intel Control Node</span>
                </div>
                <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tighter leading-tight mb-4">Content Dashboard.</h1>
                <p class="text-lg text-slate-500 font-medium leading-relaxed">Orchestrating the institutional archive with precision-driven content architecture.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <button type="button" onclick="openAuthorModal()" class="w-full sm:w-auto flex items-center justify-center gap-4 px-8 py-5 bg-indigo-600 text-white rounded-[2rem] font-black shadow-2xl hover:bg-indigo-750 hover:-translate-y-2 transition-all group">
                    <i data-lucide="user-plus" class="w-5 h-5"></i> Create Author
                </button>
                <a href="blog-editor.php" class="w-full sm:w-auto flex items-center justify-center gap-4 px-10 py-5 bg-slate-900 text-white rounded-[2rem] font-black shadow-2xl hover:bg-indigo-700 hover:-translate-y-2 transition-all group">
                    <i data-lucide="zap" class="w-5 h-5 fill-current"></i> Initialize Asset
                </a>
            </div>
        </header>

        <!-- Dynamic Control Bar -->
        <div class="grid lg:grid-cols-12 gap-8 mb-16 items-center">
            
            <!-- Statistics Cluster -->
            <div class="lg:col-span-12 xl:col-span-7 grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col justify-center">
                    <span class="text-[9px] font-black text-slate-300 uppercase mb-1 tracking-widest">Total Nodes</span>
                    <span class="text-3xl font-black text-slate-950"><?php echo count($posts); ?></span>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col justify-center">
                    <span class="text-[9px] font-black text-slate-300 uppercase mb-1 tracking-widest">Spotlight</span>
                    <span class="text-3xl font-black text-slate-950"><?php echo count(array_filter($posts, fn($p) => $p['is_pinned'])); ?></span>
                </div>
                <div class="bg-slate-900 p-6 rounded-[2rem] border border-slate-800 shadow-2xl flex flex-col justify-center text-white">
                    <span class="text-[9px] font-black text-slate-400 uppercase mb-1 tracking-widest">System Health</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-black italic tracking-tighter uppercase text-indigo-400">Optimal</span>
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center justify-center">
                    <button onclick="window.location.reload()" class="w-full h-full text-slate-400 hover:text-indigo-600 transition-colors flex items-center justify-center gap-2 font-black text-[10px] uppercase tracking-widest">
                        <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                        Sync
                    </button>
                </div>
            </div>

            <!-- Intelligent Repository Filter -->
            <div class="lg:col-span-12 xl:col-span-5">
                <div class="search-glow relative group">
                    <div class="absolute inset-y-0 left-8 flex items-center text-slate-300 group-focus-within:text-indigo-600 transition-colors">
                        <i data-lucide="search" class="w-6 h-6"></i>
                    </div>
                    <input type="text" id="postSearch" placeholder="Filter intellectual assets..." 
                        class="w-full h-20 pl-20 pr-8 rounded-[2.5rem] bg-white border border-slate-100 text-lg font-black text-slate-900 placeholder:text-slate-300 focus:ring-8 focus:ring-indigo-600/5 focus:border-indigo-600/20 transition-all outline-none">
                </div>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="mb-12 p-8 bg-emerald-950 text-emerald-400 rounded-[2.5rem] border border-emerald-900 flex items-center gap-6 animate-in slide-in-from-bottom-8">
                <div class="w-12 h-12 rounded-2xl bg-emerald-900/50 flex items-center justify-center"><i data-lucide="check-check" class="w-6 h-6"></i></div>
                <span class="text-sm font-black uppercase tracking-widest"><?php echo $message; ?></span>
            </div>
        <?php endif; ?>

        <!-- Elite Repository Feed -->
        <div id="postTableBody" class="space-y-6">
            <?php foreach ($posts as $post): ?>
                <div class="post-row repo-card bg-white p-8 md:p-10 rounded-[3rem] border border-slate-100 flex flex-col lg:flex-row items-center gap-10 group"
                    data-title="<?php echo strtolower($post['title']); ?>"
                    data-slug="<?php echo strtolower($post['slug']); ?>">
                    
                    <!-- Visual Identity -->
                    <div class="relative flex-shrink-0">
                        <div class="w-40 h-28 rounded-3xl overflow-hidden shadow-xl ring-1 ring-black/5 bg-slate-50">
                            <?php if ($post['image']): 
                                $rowImg = $post['image'];
                                if (strpos($rowImg, 'http') === false) {
                                    $rowImg = '../' . ltrim($rowImg, '/');
                                }
                            ?>
                                <img src="<?php echo htmlspecialchars($rowImg); ?>" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-slate-200"><i data-lucide="binary" class="w-10 h-10"></i></div>
                            <?php endif; ?>
                        </div>
                        <?php if ($post['is_pinned']): ?>
                            <div class="absolute -top-4 -right-4 w-12 h-12 rounded-3xl bg-amber-500 text-white flex items-center justify-center shadow-2xl border-4 border-white animate-in zoom-in-125 duration-500">
                                <i data-lucide="crown" class="w-5 h-5 fill-current"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Core Metadata -->
                    <div class="flex-1 text-center lg:text-left">
                        <div class="flex flex-col sm:flex-row items-center gap-4 mb-4">
                            <span class="px-4 py-2 bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase tracking-widest rounded-xl">
                                <?php echo htmlspecialchars($post['category']); ?>
                            </span>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full <?php echo $post['status'] === 'published' ? 'bg-emerald-500' : 'bg-amber-500'; ?> animate-pulse"></div>
                                <span class="text-[11px] font-black text-slate-900 uppercase tracking-widest"><?php echo $post['status']; ?></span>
                            </div>
                            <span class="text-[10px] font-bold text-slate-300 uppercase">/<?php echo $post['slug']; ?></span>
                        </div>
                        <h2 class="text-3xl font-black text-slate-950 tracking-tighter mb-4 leading-none group-hover:text-indigo-600 transition-colors">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </h2>
                        <div class="flex flex-wrap justify-center lg:justify-start items-center gap-6">
                            <div class="flex items-center gap-3">
                                <img src="<?php echo $post['author_img'] ?: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=80&h=80&q=80'; ?>" class="w-8 h-8 rounded-xl object-cover ring-2 ring-white">
                                <span class="text-xs font-bold text-slate-500"><?php echo htmlspecialchars($post['author_name'] ?: 'Team Intel'); ?></span>
                            </div>
                            <span class="text-xs font-bold text-slate-300">•</span>
                            <span class="text-xs font-bold text-slate-400"><?php echo date('F d, Y', strtotime($post['published_at'])); ?></span>
                            <span class="text-xs font-bold text-slate-300">•</span>
                            <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <i data-lucide="<?php echo $post['content_type'] === 'code' ? 'code-2' : 'grid-3x3'; ?>" class="w-4 h-4 text-indigo-600"></i>
                                <?php echo $post['content_type'] === 'code' ? 'Raw Injection' : 'Architecture Build'; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Accelerated Actions -->
                    <div class="flex items-center gap-4 border-t lg:border-t-0 lg:border-l border-slate-100 pt-10 lg:pt-0 lg:pl-10 w-full lg:w-auto justify-center">
                        <a href="/blog/<?php echo $post['slug']; ?>/" target="_blank" class="w-14 h-14 flex items-center justify-center rounded-2xl border border-slate-100 text-slate-400 hover:bg-slate-900 hover:text-white transition-all shadow-sm" title="Synchronize Preview">
                            <i data-lucide="arrow-up-right" class="w-6 h-6"></i>
                        </a>
                        <a href="blog-editor.php?slug=<?php echo urlencode($post['slug']); ?>" class="w-14 h-14 flex items-center justify-center rounded-2xl border border-slate-100 text-slate-400 hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Refine Architecture">
                            <i data-lucide="brush" class="w-6 h-6"></i>
                        </a>
                        <form method="POST" onsubmit="return confirm('Archive permanently?');" class="inline">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="slug" value="<?php echo $post['slug']; ?>">
                            <button type="submit" class="w-14 h-14 flex items-center justify-center rounded-2xl border border-slate-100 text-slate-400 hover:bg-rose-600 hover:text-white transition-all shadow-sm" title="Decommission">
                                <i data-lucide="trash-2" class="w-6 h-6"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- System Narrative Footer -->
        <footer class="mt-24 text-center">
            <div class="inline-flex items-center gap-3 px-8 py-3 bg-white rounded-2xl border border-slate-50 shadow-sm">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">End of Intelligence Feed</span>
                <div class="w-1.5 h-1.5 rounded-full bg-slate-200"></div>
                <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest">Nursery Core Control</span>
            </div>
        </footer>

    </div>
</main>
</div>

<!-- Premium Create Author Modal Dialog -->
<dialog id="author-modal" class="border-none p-0 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 max-w-md w-full bg-white overflow-hidden">
    <form method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
        <input type="hidden" name="action" value="create_author">
        <div class="flex justify-between items-center border-b border-slate-100 pb-4">
            <h3 class="text-xl font-black text-slate-900">Create Domain Expert</h3>
            <button type="button" onclick="closeAuthorModal()" class="text-slate-400 hover:text-rose-500 transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        
        <div class="space-y-4">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Author Name *</label>
                <input type="text" name="author_name" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-600 outline-none text-sm font-bold text-slate-700 transition-all" placeholder="e.g. Dr. Jane Smith">
            </div>
            
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Author Image (Upload)</label>
                <input type="file" name="author_image" accept="image/*" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-600 outline-none text-sm font-bold text-slate-700 transition-all">
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Or Image URL</label>
                <input type="text" name="author_image_url" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-600 outline-none text-sm font-bold text-slate-700 transition-all" placeholder="https://...">
            </div>
        </div>

        <button type="submit" class="w-full py-5 bg-slate-900 text-white rounded-[2rem] font-black uppercase text-xs tracking-[0.2em] shadow-2xl hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3">
            Register Author <i data-lucide="user-check" class="w-4 h-4 text-indigo-400"></i>
        </button>
    </form>
</dialog>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
lucide.createIcons();
document.getElementById('postSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase().trim();
    document.querySelectorAll('.post-row').forEach(row => {
        const text = row.dataset.title + ' ' + row.dataset.slug;
        row.style.display = text.includes(term) ? 'flex' : 'none';
    });
});

function openAuthorModal() {
    document.getElementById('author-modal').showModal();
    lucide.createIcons();
}
function closeAuthorModal() {
    document.getElementById('author-modal').close();
}
</script>
</body>
</html>
