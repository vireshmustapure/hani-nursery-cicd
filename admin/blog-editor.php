<?php
require_once 'auth_check.php';
require_once __DIR__ . '/../config/db.php';

$message = '';
$editMode = false;
$postData = [
    'title' => '',
    'slug' => '',
    'meta_title' => '',
    'meta_description' => '',
    'intro_content' => '',
    'body_sections' => '[]',
    'authorId' => '',
    'author_name' => '',
    'image' => '',
    'published_at' => date('Y-m-d H:i'),
    'category' => 'General',
    'status' => 'published',
    'is_pinned' => 0,
    'content_type' => 'form',
    'raw_html' => ''
];

function create_seo_slug($string) {
    if (empty($string)) return '';
    $slug = preg_replace('~[^\pL\d]+~u', '-', $string);
    $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
    $slug = preg_replace('~[^-\w]+~', '', $slug);
    $slug = trim($slug, '-');
    $slug = preg_replace('~-+~', '-', $slug);
    $slug = strtolower($slug);
    return $slug;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $slug = create_seo_slug($_POST['slug'] ?: $title);
    $meta_title = $_POST['meta_title'] ?: $title;
    $meta_description = $_POST['meta_description'];
    $intro_content = $_POST['intro_content'];
    
    $authorId = $_POST['authorId'] ?? null;
    $author_name = $_POST['author_name'] ?? null;
    
    $published_at = $_POST['published_at'] ?: date('Y-m-d H:i:s');
    $category = $_POST['category'] ?? 'General';
    $status = $_POST['status'] ?? 'published';
    $is_pinned = isset($_POST['is_pinned']) ? 1 : 0;
    
    $image = $_POST['image'] ?? '';
    if (isset($_FILES['banner_file']) && $_FILES['banner_file']['error'] === UPLOAD_ERR_OK) {
        $img = $_FILES['banner_file'];
        $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $newName = md5(time() . rand()) . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            if (move_uploaded_file($img['tmp_name'], $uploadDir . $newName)) {
                $image = '/uploads/' . $newName;
            }
        }
    }
    
    $content_type = $_POST['content_type'] ?? 'form';
    $raw_html = $_POST['raw_html'] ?? '';

    $sections = [];
    if (isset($_POST['section_heading'])) {
        foreach ($_POST['section_heading'] as $index => $heading) {
            if (empty($heading) && empty($_POST['section_content'][$index])) continue;
            $sections[] = [
                'heading' => $heading,
                'type' => $_POST['section_type'][$index] ?? 'h2',
                'content' => $_POST['section_content'][$index] ?? ''
            ];
        }
    }
    $body_sections = json_encode($sections);

    $full_html = '';
    if ($content_type === 'code') {
        $full_html = $raw_html;
    } else {
        if (!empty($intro_content) && $intro_content !== '<p><br></p>') {
            $full_html .= '<div class="blog-intro">' . $intro_content . '</div>';
        }
        $sections_data = json_decode($body_sections, true);
        foreach ($sections_data as $s) {
            $full_html .= '<div class="blog-section mb-12">';
            if (!empty($s['heading'])) {
                $tag = in_array($s['type'], ['h2', 'h3', 'h4']) ? $s['type'] : 'h2';
                $full_html .= '<' . $tag . ' class="section-title">' . htmlspecialchars($s['heading']) . '</' . $tag . '>';
            }
            $full_html .= '<div class="section-body">' . ($s['content'] ?? '') . '</div>';
            $full_html .= '</div>';
        }
    }

    $series_values = [];
    $series_updates = [];
    $series_placeholders = [];
    for ($i = 1; $i <= 15; $i++) {
        $idx = $i - 1;
        $h = isset($sections[$idx]) ? $sections[$idx]['heading'] : null;
        $t = isset($sections[$idx]) ? $sections[$idx]['type'] : 'h2';
        $c = isset($sections[$idx]) ? $sections[$idx]['content'] : null;
        $series_values[] = $h; $series_values[] = $t; $series_values[] = $c;
        $series_placeholders[] = "?, ?, ?";
        $series_updates[] = "s{$i}_h=?, s{$i}_t=?, s{$i}_c=?";
    }

    $isUpdate = !empty($_POST['original_slug']);
    if ($isUpdate) {
        $sql = "UPDATE posts SET title=?, slug=?, meta_title=?, meta_description=?, intro_content=?, body_sections=?, authorId=?, author_name=?, published_at=?, category=?, status=?, is_pinned=?, image=?, content_type=?, raw_html=?, content_html=?, " . implode(", ", $series_updates) . " WHERE slug=?";
        $params = array_merge([$title, $slug, $meta_title, $meta_description, $intro_content, $body_sections, $authorId, $author_name, $published_at, $category, $status, $is_pinned, $image, $content_type, $raw_html, $full_html], $series_values, [$_POST['original_slug']]);
        $pdo->prepare($sql)->execute($params);
    } else {
        $sql = "INSERT INTO posts (title, slug, meta_title, meta_description, intro_content, body_sections, authorId, author_name, published_at, category, status, is_pinned, image, content_type, raw_html, content_html, " . 
                implode(", ", array_map(fn($i) => "s{$i}_h, s{$i}_t, s{$i}_c", range(1, 15))) . 
               ") VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, " . implode(", ", $series_placeholders) . ")";
        $params = array_merge([$title, $slug, $meta_title, $meta_description, $intro_content, $body_sections, $authorId, $author_name, $published_at, $category, $status, $is_pinned, $image, $content_type, $raw_html, $full_html], $series_values);
        $pdo->prepare($sql)->execute($params);
    }
    
    // Generate physical blog files using CMS logic
    $author = null;
    if (!empty($authorId)) {
        $auth_stmt = $pdo->prepare("SELECT * FROM authors WHERE id = ?");
        $auth_stmt->execute([$authorId]);
        $author = $auth_stmt->fetch();
    }
    require_once 'cms_logic.php';
    $cms = new CMS();
    $cms->createBlogPost(
        $slug,
        $title,
        $meta_description,
        $full_html,
        ($content_type === 'code'),
        $author,
        $image,
        $published_at,
        $category,
        $meta_title
    );
    
    header("Location: blog-editor.php?slug=" . urlencode($slug) . "&saved=1");
    exit;
}

if (isset($_GET['saved'])) $message = "Intelligence committed to repository.";

if (isset($_GET['slug'])) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = ?");
    $stmt->execute([$_GET['slug']]);
    $row = $stmt->fetch();
    if ($row) {
        $editMode = true;
        foreach ($row as $k => $v) if (array_key_exists($k, $postData)) $postData[$k] = $v;
    }
}

$authors = $pdo->query("SELECT * FROM authors")->fetchAll();
$categories = $pdo->query("SELECT DISTINCT category FROM posts WHERE category IS NOT NULL AND category != '' ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);
if (empty($categories)) {
    $categories = ['General'];
}
$pageTitle = 'Journal Architect | Nursery Admin';
include __DIR__ . '/../components/header.php';
include __DIR__ . '/sidebar.php';
?>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .sticky-sidebar { position: sticky; top: 5rem; height: calc(100vh - 7rem); overflow-y: auto; }
    .quill-editor { height: 350px; background: white; border-radius: 0 0 1.5rem 1.5rem; }
    .ql-toolbar.ql-snow { border-radius: 1.5rem 1.5rem 0 0; background: #f8fafc; border-color: #e2e8f0; padding: 12px; }
    .ql-container.ql-snow { border-color: #e2e8f0; border-radius: 0 0 1.5rem 1.5rem; }
    
    .ql-editor img { border-radius: 1rem; transition: all 0.3s ease; display: inline-block; }
    .ql-editor img.img-align-center { display: block; margin: 2rem auto; }
    .ql-editor img.img-align-left { float: left; margin: 0 2rem 1rem 0; max-width: 50%; }
    .ql-editor img.img-align-right { float: right; margin: 0 0 1rem 2rem; max-width: 50%; }
    .ql-editor img.img-full-width { width: 100%; height: auto; display: block; margin: 2rem 0; }
    
    .no-scrollbar::-webkit-scrollbar { display: none; }
    
    #image-modal {
        border: none;
        padding: 0;
        border-radius: 2rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        max-width: 480px;
        width: 90%;
        background: white;
        overflow: hidden;
    }
    #image-modal::backdrop { background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(8px); }
    .modal-tab-btn.active { background: #0f172a; color: white; }
    .alignment-option.active { border-color: #4f46e5; background: #eef2ff; color: #4f46e5; }

    .ql-editor { font-size: 15px; line-height: 1.7; color: #334155; }
    .ql-editor p { margin-bottom: 1.25rem !important; }
    .ql-editor ul, .ql-editor ol { margin-bottom: 1.25rem !important; }
    .ql-editor h2, .ql-editor h3, .ql-editor h4 { margin-top: 1.5rem !important; margin-bottom: 0.75rem !important; font-weight: 800; color: #0f172a; }

    #schedule-modal {
        border: none;
        padding: 0;
        border-radius: 2.5rem;
        box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.4);
        max-width: 500px;
        width: 95%;
        background: white;
        overflow: hidden;
    }
    #schedule-modal::backdrop { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(12px); }
</style>

<div class="main">
<main class="flex-1 p-8 bg-slate-50 min-h-screen">
    <form method="POST" id="advancedPostForm" enctype="multipart/form-data" class="max-w-7xl mx-auto">
        <input type="hidden" name="original_slug" value="<?php echo $editMode ? htmlspecialchars($postData['slug']) : ''; ?>">
        
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Content Orchestration</h1>
                <p class="text-slate-500 mt-1">Sculpting high-performance digital narratives.</p>
            </div>
            <div class="flex items-center gap-4">
                <button type="button" onclick="openScheduleModal()" class="bg-white border border-slate-200 text-slate-900 px-8 py-4 rounded-2xl font-black shadow-xl hover:bg-slate-50 active:scale-95 transition-all flex items-center gap-2">
                    <i data-lucide="calendar-clock" class="w-5 h-5 text-indigo-600"></i> Draft & Orchestrate
                </button>
                <button type="submit" class="bg-primary text-primary-foreground px-10 py-4 rounded-2xl font-black shadow-2xl hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-5 h-5"></i> Commit Intelligence
                </button>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <div class="lg:col-span-8 space-y-12">
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] block text-center">Master Heading (H1)</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($postData['title']); ?>" required 
                           class="w-full text-4xl font-black text-center bg-transparent border-b-2 border-slate-200 focus:border-indigo-500 outline-none pb-4 transition-all placeholder-slate-200" placeholder="Title of Research...">
                </div>

                <div class="flex justify-center">
                    <div class="bg-white p-1.5 rounded-2xl border border-slate-200 shadow-sm flex gap-1">
                        <button type="button" onclick="switchMode('form')" id="mode-btn-form" 
                                class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all <?php echo $postData['content_type'] === 'form' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50'; ?>">
                            Card Builder
                        </button>
                        <button type="button" onclick="switchMode('code')" id="mode-btn-code" 
                                class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all <?php echo $postData['content_type'] === 'code' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50'; ?>">
                            HTML Injection
                        </button>
                    </div>
                    <input type="hidden" name="content_type" id="content_type_input" value="<?php echo $postData['content_type']; ?>">
                </div>

                <div id="builder-mode-container" class="space-y-12 <?php echo $postData['content_type'] === 'code' ? 'hidden' : ''; ?>">
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-8 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            Introduction Segment
                        </div>
                        <div id="intro-editor" class="quill-editor"><?php echo $postData['intro_content']; ?></div>
                        <input type="hidden" name="intro_content" id="intro-input">
                    </div>

                    <div id="sections-container" class="space-y-8"></div>

                    <div class="pt-8 text-center">
                        <button type="button" onclick="addNewSection()" 
                                class="group inline-flex items-center gap-4 px-10 py-5 bg-white border-2 border-dashed border-slate-200 rounded-3xl hover:border-indigo-500 hover:bg-indigo-50 transition-all">
                            <i data-lucide="plus" class="w-6 h-6 text-indigo-600 group-hover:rotate-90 transition-transform"></i>
                            <span class="font-black text-slate-700 uppercase text-xs tracking-widest">Append Intelligence Card</span>
                        </button>
                    </div>
                </div>

                <div id="code-mode-container" class="<?php echo $postData['content_type'] === 'form' ? 'hidden' : ''; ?>">
                    <div class="bg-slate-900 rounded-[3rem] border border-slate-800 shadow-2xl p-10">
                        <textarea name="raw_html" rows="25" class="w-full bg-transparent border-0 text-indigo-300 font-mono text-sm outline-none resize-none" placeholder="<!-- Direct HTML Payload -->"><?php echo htmlspecialchars($postData['raw_html']); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-4">
                <div class="sticky-sidebar space-y-6 no-scrollbar pb-20">
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-2xl shadow-slate-200/50 space-y-8 border border-slate-100">
                        <div class="flex items-center gap-4 border-b border-slate-100 pb-6">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <i data-lucide="shield" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-slate-900 tracking-tight uppercase text-xs">Authority Engine</h3>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-1">Relational Intelligence</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <label class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-100 rounded-2xl cursor-pointer hover:bg-amber-100 transition-all">
                                <input type="checkbox" name="is_pinned" value="1" <?php echo ($postData['is_pinned'] ?? 0) ? 'checked' : ''; ?> class="w-5 h-5 rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                                <div>
                                    <span class="block text-[10px] font-black text-amber-900 uppercase">Spotlight Pin</span>
                                    <p class="text-[9px] text-amber-600 font-bold uppercase tracking-tight">Feature at the summit of listing</p>
                                </div>
                            </label>

                            <div>
                                <div class="flex justify-between items-center mb-3">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Domain Architect</label>
                                    <a href="blogs.php" target="_blank" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline">+ Create Author</a>
                                </div>
                                <select name="authorId" id="authorSelector" onchange="updateAuthorName()" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-600 outline-none text-sm font-bold text-slate-700 transition-all">
                                    <option value="">Select Domain Expert...</option>
                                    <?php foreach ($authors as $a): ?>
                                        <option value="<?php echo $a['id']; ?>" data-name="<?php echo htmlspecialchars($a['name']); ?>" <?php echo $postData['authorId'] == $a['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($a['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="author_name" id="author_name_hidden" value="<?php echo htmlspecialchars($postData['author_name']); ?>">
                                <div class="mt-2 text-[9px] font-black text-indigo-400 uppercase tracking-widest">
                                    Linked To: <span id="authorNameLabel" class="text-slate-900"><?php echo htmlspecialchars($postData['author_name'] ?: 'External Entity'); ?></span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Namespace (Category)</label>
                                <select name="category_select" id="categorySelect" onchange="handleCategoryChange()" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-indigo-600 outline-none text-xs font-black uppercase text-slate-700 transition-all">
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo strtolower($cat) === strtolower($postData['category']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat); ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <option value="__NEW__">+ ADD NEW CATEGORY...</option>
                                </select>
                                <div id="newCategoryContainer" class="hidden mt-2">
                                    <input type="text" id="newCategoryInput" placeholder="Enter New Category Name" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-black uppercase focus:ring-2 focus:ring-indigo-500 transition-all">
                                </div>
                                <input type="hidden" name="category" id="categoryHidden" value="<?php echo htmlspecialchars($postData['category']); ?>">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">SEO Slug Identity</label>
                                <input type="text" name="slug" value="<?php echo htmlspecialchars($postData['slug']); ?>" placeholder="auto-generated-slug" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-mono text-indigo-600 font-black tracking-tight">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Visibility</label>
                                    <select name="status" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-[10px] font-black uppercase">
                                        <option value="published" <?php echo $postData['status'] === 'published' ? 'selected' : ''; ?>>Active</option>
                                        <option value="draft" <?php echo $postData['status'] === 'draft' ? 'selected' : ''; ?>>Archived</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Primary Publication Point</label>
                                    <input type="datetime-local" name="published_at" id="main_published_at" value="<?php echo date('Y-m-d\TH:i', strtotime($postData['published_at'])); ?>" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-[10px] font-black">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Media Asset URL (Banner)</label>
                                <div class="space-y-3">
                                    <input type="text" name="image" id="banner-url-input" value="<?php echo htmlspecialchars($postData['image']); ?>" placeholder="https://..." class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold text-slate-500">
                                    <div class="flex items-center gap-4 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Or Upload:</span>
                                        <input type="file" name="banner_file" accept="image/*" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    </div>
                                    <?php if (!empty($postData['image'])): 
                                        $bannerImg = $postData['image'];
                                        if (strpos($bannerImg, 'http') === false) {
                                            $bannerImg = '../' . ltrim($bannerImg, '/');
                                        }
                                    ?>
                                        <div class="mt-2 w-full h-32 rounded-2xl overflow-hidden border border-slate-100 bg-slate-50">
                                            <img src="<?php echo htmlspecialchars($bannerImg); ?>" class="w-full h-full object-cover">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-indigo-950 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-indigo-900/40 space-y-6">
                        <div class="flex items-center gap-4 border-b border-white/10 pb-6">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center border border-white/10"><i data-lucide="trending-up" class="w-6 h-6 text-indigo-400"></i></div>
                            <div>
                                <h3 class="font-black text-white tracking-tight uppercase text-xs">Search Intelligence</h3>
                                <p class="text-[10px] text-indigo-400 font-black uppercase tracking-widest mt-1">SEO Matrix</p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-3">SERP Meta Title</label>
                                <input type="text" name="meta_title" value="<?php echo htmlspecialchars($postData['meta_title']); ?>" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-medium">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-3">SERP Meta Description</label>
                                <textarea name="meta_description" rows="5" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-xs leading-relaxed resize-none font-medium"><?php echo htmlspecialchars($postData['meta_description']); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>
</div>

<dialog id="image-modal">
    <div class="p-8 space-y-6">
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-black text-slate-900">Intelligence Asset Orchestrator</h3>
            <button type="button" onclick="closeImageModal()" class="text-slate-400 hover:text-rose-500 transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        
        <div class="flex gap-2 p-1 bg-slate-100 rounded-2xl">
            <button type="button" onclick="switchImageTab('url')" id="tab-url" class="modal-tab-btn flex-1 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest active transition-all">Remote Link (URL)</button>
            <button type="button" onclick="switchImageTab('upload')" id="tab-upload" class="modal-tab-btn flex-1 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Direct Upload</button>
        </div>

        <div id="url-pane" class="space-y-4">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Target Resource Locator</label>
            <input type="text" id="img-url-input" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-medium transition-all" placeholder="https://cdn.example.com/asset.webp">
        </div>

        <div id="upload-pane" class="hidden space-y-4">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Binary Package Ingestion</label>
            <div class="relative group cursor-pointer">
                <input type="file" id="img-upload-input" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer">
                <div class="border-2 border-dashed border-slate-200 rounded-3xl p-8 text-center group-hover:border-indigo-500 group-hover:bg-indigo-50 transition-all border-indigo-100 bg-indigo-50/10">
                    <i data-lucide="image-plus" class="w-10 h-10 text-slate-300 mx-auto mb-4 group-hover:text-indigo-500 group-hover:scale-110 transition-transform"></i>
                    <p id="upload-label" class="text-[11px] font-black text-slate-500 uppercase tracking-widest">Select Intelligence Asset</p>
                </div>
            </div>
        </div>

        <div class="space-y-4 border-t border-slate-100 pt-6">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest text-center mb-4">Structural Orientation</label>
            <div class="grid grid-cols-4 gap-3">
                <button type="button" onclick="setImageAlign('left')" class="alignment-option p-4 border-2 border-slate-50 rounded-2xl flex flex-col items-center gap-2 hover:border-indigo-200 hover:bg-slate-50 transition-all">
                    <i data-lucide="align-left" class="w-5 h-5"></i>
                    <span class="text-[8px] font-black uppercase">Left</span>
                </button>
                <button type="button" onclick="setImageAlign('center')" class="alignment-option p-4 border-2 border-slate-50 rounded-2xl flex flex-col items-center gap-2 hover:border-indigo-200 hover:bg-slate-50 transition-all active">
                    <i data-lucide="align-center" class="w-5 h-5"></i>
                    <span class="text-[8px] font-black uppercase">Center</span>
                </button>
                <button type="button" onclick="setImageAlign('right')" class="alignment-option p-4 border-2 border-slate-50 rounded-2xl flex flex-col items-center gap-2 hover:border-indigo-200 hover:bg-slate-50 transition-all">
                    <i data-lucide="align-right" class="w-5 h-5"></i>
                    <span class="text-[8px] font-black uppercase">Right</span>
                </button>
                <button type="button" onclick="setImageAlign('full')" class="alignment-option p-4 border-2 border-slate-50 rounded-2xl flex flex-col items-center gap-2 hover:border-indigo-200 hover:bg-slate-50 transition-all">
                    <i data-lucide="maximize" class="w-5 h-5"></i>
                    <span class="text-[8px] font-black uppercase">Full</span>
                </button>
            </div>
        </div>

        <button type="button" onclick="confirmImageInjection()" class="w-full py-5 bg-slate-900 text-white rounded-[2rem] font-black uppercase text-xs tracking-[0.2em] shadow-2xl hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3">
            Commit Asset <i data-lucide="zap" class="w-4 h-4 text-indigo-400"></i>
        </button>
    </div>
</dialog>

<dialog id="schedule-modal">
    <div class="p-10 space-y-8">
        <div class="flex justify-between items-center border-b border-slate-100 pb-6">
            <div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Orchestration Control</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">System State Management</p>
            </div>
            <button type="button" onclick="closeScheduleModal()" class="text-slate-400 hover:text-rose-500 transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>

        <div class="grid gap-4">
            <button type="button" onclick="submitAsDraft()" class="w-full p-6 bg-slate-50 border border-slate-200 rounded-[2rem] flex items-center gap-6 group hover:bg-slate-900 hover:border-slate-900 transition-all">
                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 group-hover:bg-white/10 group-hover:border-white/10 group-hover:text-white transition-all">
                    <i data-lucide="archive" class="w-6 h-6"></i>
                </div>
                <div class="text-left">
                    <span class="block text-xs font-black text-slate-900 uppercase group-hover:text-white">Save as Pure Draft</span>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight group-hover:text-slate-500">Archive in current state without publication</p>
                </div>
            </button>

            <div class="p-6 bg-indigo-50 border border-indigo-100 rounded-[2rem] space-y-6">
                <div class="flex items-center gap-6">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-200">
                        <i data-lucide="calendar-clock" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <span class="block text-xs font-black text-indigo-900 uppercase">Schedule Publication</span>
                        <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-tight">Time-Locked Activation Engine</p>
                    </div>
                </div>
                <input type="datetime-local" id="schedule-time-picker" value="<?php echo date('Y-m-d\TH:i'); ?>" class="w-full px-6 py-4 bg-white border border-indigo-200 rounded-2xl text-xs font-black text-indigo-600 focus:ring-2 focus:ring-indigo-500 transition-all">
                <button type="button" onclick="submitAsScheduled()" class="w-full py-5 bg-indigo-600 text-white rounded-[1.5rem] font-black uppercase text-xs tracking-widest shadow-xl hover:scale-[1.02] active:scale-[0.98] transition-all">Commit Schedule</button>
            </div>
        </div>
    </div>
</dialog>

<template id="section-template">
    <div class="section-card bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden p-10 space-y-8 relative group transition-all hover:shadow-2xl">
        <button type="button" onclick="this.closest('.section-card').remove()" class="absolute top-8 right-8 p-3 text-slate-200 hover:text-rose-500 hover:bg-rose-50 rounded-2xl transition-all opacity-0 group-hover:opacity-100"><i data-lucide="trash-2" class="w-5 h-5"></i></button>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
            <div class="md:col-span-10">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Section Identity (Heading)</label>
                <input type="text" name="section_heading[]" class="w-full text-xl font-black bg-transparent border-b border-slate-100 focus:border-indigo-500 outline-none pb-2 transition-all" placeholder="Card Title...">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Hierarchy Tag</label>
                <select name="section_type[]" class="w-full px-4 py-3 bg-slate-50 rounded-xl text-[10px] font-black border-0 outline-none uppercase tracking-widest">
                    <option value="h2">H2 Insight</option><option value="h3">H3 Sub-Intel</option><option value="h4">H4 Detail</option>
                </select>
            </div>
        </div>
        <div class="section-editor-container">
            <div class="quill-editor"></div>
            <input type="hidden" name="section_content[]">
        </div>
    </div>
</template>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
    const sectionsContainer = document.getElementById('sections-container');
    const template = document.getElementById('section-template');
    
    const fullToolbar = [
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [{ 'font': [] }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'script': 'sub'}, { 'script': 'super' }],
        ['blockquote', 'code-block'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'indent': '-1'}, { 'indent': '+1' }],
        [{ 'direction': 'rtl' }],
        [{ 'align': [] }],
        ['link', 'image', 'video'],
        ['clean']
    ];

    let currentQuill = null;
    let selectedImageAlign = 'center';
    let imageTab = 'url';

    function showImageModal(quill) {
        currentQuill = quill;
        document.getElementById('image-modal').showModal();
        lucide.createIcons();
    }

    function closeImageModal() {
        document.getElementById('image-modal').close();
        document.getElementById('img-url-input').value = '';
        document.getElementById('img-upload-input').value = '';
        document.getElementById('upload-label').innerText = 'Select Intelligence Asset';
    }

    function switchImageTab(tab) {
        imageTab = tab;
        document.getElementById('url-pane').classList.toggle('hidden', tab !== 'url');
        document.getElementById('upload-pane').classList.toggle('hidden', tab !== 'upload');
        document.getElementById('tab-url').classList.toggle('active', tab === 'url');
        document.getElementById('tab-upload').classList.toggle('active', tab === 'upload');
    }

    function setImageAlign(align) {
        selectedImageAlign = align;
        document.querySelectorAll('.alignment-option').forEach(opt => {
            opt.classList.toggle('active', opt.outerHTML.includes("setImageAlign('" + align + "')"));
        });
    }

    document.getElementById('img-upload-input').onchange = function(e) {
        if (e.target.files && e.target.files[0]) {
            document.getElementById('upload-label').innerText = e.target.files[0].name;
            document.getElementById('upload-label').className = "text-[11px] font-black text-indigo-600 uppercase tracking-widest";
        }
    };

    async function confirmImageInjection() {
        let imageUrl = '';
        if (imageTab === 'url') {
            imageUrl = document.getElementById('img-url-input').value;
        } else {
            const fileInput = document.getElementById('img-upload-input');
            if (fileInput.files.length > 0) {
                const formData = new FormData();
                formData.append('image', fileInput.files[0]);
                try {
                    const resp = await fetch('upload.php', { method: 'POST', body: formData });
                    const res = await resp.json();
                    if (res.success) imageUrl = res.url;
                    else { alert(res.message); return; }
                } catch (e) { alert('Intelligence transfer failed.'); return; }
            }
        }

        if (imageUrl) {
            const range = currentQuill.getSelection(true);
            currentQuill.insertEmbed(range.index, 'image', imageUrl);
            
            setTimeout(() => {
                const imgs = currentQuill.root.querySelectorAll('img');
                const lastInserted = Array.from(imgs).find(img => img.src.includes(imageUrl));
                if (lastInserted) {
                    lastInserted.className = selectedImageAlign === 'full' ? 'img-full-width' : 'img-align-' + selectedImageAlign;
                }
            }, 100);
            
            closeImageModal();
        }
    }

    function createEditor(id) {
        const q = new Quill(id, { 
            theme: 'snow', 
            modules: { 
                toolbar: {
                    container: fullToolbar,
                    handlers: {
                        image: function() { showImageModal(this.quill); }
                    }
                }
            } 
        });
        return q;
    }

    const introQuill = createEditor('#intro-editor');
    const sectionQuills = [];

    function updateAuthorName() {
        const select = document.getElementById('authorSelector');
        const hidden = document.getElementById('author_name_hidden');
        const label = document.getElementById('authorNameLabel');
        const selectedOption = select.options[select.selectedIndex];
        const name = selectedOption.dataset.name || "External Entity";
        hidden.value = name;
        label.innerText = name;
    }

    function handleCategoryChange() {
        const select = document.getElementById('categorySelect');
        const container = document.getElementById('newCategoryContainer');
        const hidden = document.getElementById('categoryHidden');
        const input = document.getElementById('newCategoryInput');
        
        if (select.value === '__NEW__') {
            container.classList.remove('hidden');
            hidden.value = input.value;
            input.required = true;
            input.focus();
        } else {
            container.classList.add('hidden');
            hidden.value = select.value;
            input.required = false;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('newCategoryInput');
        const hidden = document.getElementById('categoryHidden');
        if (input) {
            input.addEventListener('input', function() {
                if (document.getElementById('categorySelect').value === '__NEW__') {
                     hidden.value = this.value;
                }
            });
        }
    });

    function addNewSection(data = null) {
        const clone = template.content.cloneNode(true);
        const card = clone.querySelector('.section-card');
        const editorContainer = card.querySelector('.quill-editor');
        const headingInput = card.querySelector('input[name="section_heading[]"]');
        const typeSelect = card.querySelector('select');
        
        const editorId = 'editor-' + Math.random().toString(36).substr(2, 9);
        editorContainer.id = editorId;
        
        sectionsContainer.appendChild(card);
        const quill = createEditor('#' + editorId);
        
        sectionQuills.push({ card: card, quill: quill });

        if (data) {
            headingInput.value = data.heading || '';
            typeSelect.value = data.type || 'h2';
            quill.root.innerHTML = data.content || '';
        }
        lucide.createIcons();
    }

    const initialSections = <?php echo $postData['body_sections']; ?>;
    initialSections.forEach(s => addNewSection(s));

    function switchMode(mode) {
        document.getElementById('content_type_input').value = mode;
        const b = document.getElementById('builder-mode-container');
        const c = document.getElementById('code-mode-container');
        const btnB = document.getElementById('mode-btn-form');
        const btnC = document.getElementById('mode-btn-code');
        if (mode === 'form') {
            b.classList.remove('hidden'); c.classList.add('hidden');
            btnB.className = "px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest bg-slate-900 text-white shadow-xl transition-all";
            btnC.className = "px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-all";
        } else {
            c.classList.remove('hidden'); b.classList.add('hidden');
            btnC.className = "px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest bg-slate-900 text-white shadow-xl transition-all";
            btnB.className = "px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-all";
        }
    }

    function openScheduleModal() {
        document.getElementById('schedule-modal').showModal();
        lucide.createIcons();
    }

    // --- ORCHESTRATION LOGIC ---
    function closeScheduleModal() {
        document.getElementById('schedule-modal').close();
    }

    function submitAsDraft() {
        const form = document.getElementById('advancedPostForm');
        const statusSelect = form.querySelector('select[name="status"]');
        statusSelect.value = 'draft';
        form.requestSubmit();
    }

    function submitAsScheduled() {
        const form = document.getElementById('advancedPostForm');
        const statusSelect = form.querySelector('select[name="status"]');
        const mainDateInput = document.getElementById('main_published_at');
        const modalDateInput = document.getElementById('schedule-time-picker');
        
        statusSelect.value = 'published';
        mainDateInput.value = modalDateInput.value;
        form.requestSubmit();
    }

    document.getElementById('advancedPostForm').onsubmit = function() {
        document.getElementById('intro-input').value = introQuill.root.innerHTML;
        
        const form = this;
        const existingHiddenInputs = form.querySelectorAll('input[name="section_content[]"]');
        existingHiddenInputs.forEach(input => input.remove());

        document.querySelectorAll('.section-card').forEach((card) => {
            const entry = sectionQuills.find(q => q.card === card);
            if (entry) {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'section_content[]';
                hidden.value = entry.quill.root.innerHTML;
                form.appendChild(hidden);
            }
        });
    };
</script>
</body>
</html>
