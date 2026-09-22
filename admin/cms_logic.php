<?php

class CMS
{
    private $rootDir;
    private $seoFile;
    private $excludedDirs = ['admin', 'components', 'assets', '_next', 'node_modules', '.git', '.gemini'];

    public function __construct()
    {
        $this->rootDir = str_replace('\\', '/', dirname(__DIR__));

        $this->seoFile = $this->rootDir . '/components/seo.json';
        
        // Ensure components folder exists
        if (!is_dir($this->rootDir . '/components')) {
            mkdir($this->rootDir . '/components', 0777, true);
        }
        if (!file_exists($this->seoFile)) {
            file_put_contents($this->seoFile, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    public function scanPages()
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->rootDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $pages = [];
        foreach ($files as $file) {
            if ($file->isFile() && $file->getFilename() === 'index.php') {
                $path = str_replace([$this->rootDir, '\\'], ['', '/'], $file->getPathname());

                $parts = explode('/', trim($path, '/'));
                if (in_array($parts[0], $this->excludedDirs))
                    continue;

                $uri = str_replace('/index.php', '/', $path);
                if ($uri === '/index.php')
                    $uri = '/';
                elseif (substr($uri, -1) !== '/')
                    $uri .= '/';

                $pages[] = $uri;
            }
        }
        sort($pages);
        return array_unique($pages);
    }

    public function getSeoStatus()
    {
        global $pdo;
        if (!isset($pdo)) {
            require_once $this->rootDir . '/config/db.php';
        }

        $detectedPages = $this->scanPages();
        
        $seoData = [];
        try {
            $stmt = $pdo->query("SELECT * FROM `seo_metadata`");
            while ($row = $stmt->fetch()) {
                $seoData[$row['uri']] = $row;
            }
        } catch (Exception $e) {
            $seoData = json_decode(file_get_contents($this->seoFile), true) ?: [];
        }

        $report = [];

        foreach ($detectedPages as $uri) {
            $status = isset($seoData[$uri]) ? 'OK' : 'MISSING';
            $report[$uri] = [
                'status' => $status,
                'title' => $seoData[$uri]['title'] ?? '',
                'description' => $seoData[$uri]['description'] ?? '',
                'image' => $seoData[$uri]['image'] ?? ''
            ];
        }

        foreach ($seoData as $uri => $data) {
            if (!isset($report[$uri])) {
                $report[$uri] = [
                    'status' => 'ORPHAN',
                    'title' => $data['title'] ?? '',
                    'description' => $data['description'] ?? '',
                    'image' => $data['image'] ?? ''
                ];
            }
        }

        return $report;
    }

    public function saveSeoData($uri, $title, $description, $image, $publishedAt = null, $authorId = null)
    {
        global $pdo;
        if (!isset($pdo)) {
            require_once $this->rootDir . '/config/db.php';
        }

        $data = json_decode(file_get_contents($this->seoFile), true) ?: [];

        $existing = $data[$uri] ?? [];
        $publishedAt = $publishedAt ?? ($existing['published_at'] ?? date('Y-m-d H:i:s'));
        $updatedAt = date('Y-m-d H:i:s');
        $authorId = $authorId ?? (!empty($existing['author_id']) ? intval($existing['author_id']) : null);

        $data[$uri] = [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'published_at' => $publishedAt,
            'updated_at' => $updatedAt,
            'author_id' => $authorId
        ];
        file_put_contents($this->seoFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        try {
            $stmt = $pdo->prepare("INSERT INTO `seo_metadata` 
                (`uri`, `title`, `description`, `image`, `published_at`, `updated_at`, `author_id`) 
                VALUES (:uri, :title, :description, :image, :published_at, :updated_at, :author_id)
                ON DUPLICATE KEY UPDATE 
                `title` = VALUES(`title`),
                `description` = VALUES(`description`),
                `image` = VALUES(`image`),
                `published_at` = VALUES(`published_at`),
                `updated_at` = VALUES(`updated_at`),
                `author_id` = VALUES(`author_id`)");
            
            $stmt->execute([
                ':uri' => $uri,
                ':title' => $title,
                ':description' => $description,
                ':image' => $image,
                ':published_at' => $publishedAt,
                ':updated_at' => $updatedAt,
                ':author_id' => $authorId,
            ]);
        } catch (Exception $e) {
            error_log("Database SEO save error: " . $e->getMessage());
        }
    }

    public function createBlogPost($slug, $title, $description, $content, $isCustomHtml = false, $author = null, $image = null, $publishedAt = null, $category = 'General', $meta_title = '')
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug), '-'));
        $dir = $this->rootDir . "/blog/$slug";

        if (!is_dir($dir))
            mkdir($dir, 0777, true);

        $filePath = "$dir/index.php";
        $bodyPath = "$dir/body.php";

        $finalImage = $image ?: "https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=800&q=80";

        $bodyContent = "";
        // Fix any uploaded image links inside the blog content to use the relative path (../../uploads/)
        $fixed_content = preg_replace('/src=["\']\/uploads\//', 'src="../../uploads/', $content);
        $fixed_content = preg_replace('/src=["\']uploads\//', 'src="../../uploads/', $fixed_content);

        if ($isCustomHtml) {
            $bodyContent .= "<!-- Custom HTML Mode -->\n";
            $bodyContent .= $fixed_content;
        } else {
            $bodyContent .= "<div class=\"text-slate-600 leading-relaxed\">\n";
            $bodyContent .= $fixed_content . "\n";
            $bodyContent .= "</div>";
        }
        file_put_contents($bodyPath, $bodyContent);

        $fileContent = "<?php\n";
        $fileContent .= "\$meta = [\n";
        $fileContent .= "    'title' => " . var_export($title, true) . ",\n";
        $fileContent .= "    'meta_title' => " . var_export($meta_title ?: $title, true) . ",\n";
        $fileContent .= "    'meta_description' => " . var_export($description, true) . ",\n";
        $fileContent .= "    'banner' => " . var_export($finalImage, true) . ",\n";
        $fileContent .= "    'author_id' => " . var_export($author ? $author['id'] : null, true) . ",\n";
        $fileContent .= "    'published_at' => " . var_export($publishedAt ?: date('Y-m-d H:i:s'), true) . ",\n";
        $fileContent .= "    'category' => " . var_export($category, true) . "\n";
        $fileContent .= "];\n";
        $fileContent .= "?>\n";

        $fileContent .= "<?php\n";
        $fileContent .= "\$base = \"../../\";\n";
        $fileContent .= "include '../../config/db.php';\n";
        $fileContent .= "\$settings_res = mysqli_query(\$conn, \"SELECT * FROM settings\");\n";
        $fileContent .= "\$settings = [];\n";
        $fileContent .= "if (\$settings_res) {\n";
        $fileContent .= "    while (\$row = mysqli_fetch_assoc(\$settings_res)) {\n";
        $fileContent .= "        \$settings[\$row['setting_key']] = \$row['setting_value'];\n";
        $fileContent .= "    }\n";
        $fileContent .= "}\n";
        $fileContent .= "?>\n";
        $fileContent .= "<!DOCTYPE html>\n";
        $fileContent .= "<html lang=\"en\">\n";
        $fileContent .= "<head>\n";
        $fileContent .= "    <meta charset=\"UTF-8\">\n";
        $fileContent .= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
        $fileContent .= "    <title><?php echo htmlspecialchars(\$meta['meta_title'] ?: \$meta['title']); ?></title>\n";
        $fileContent .= "    <meta name=\"description\" content=\"<?php echo htmlspecialchars(\$meta['meta_description']); ?>\">\n";
        $fileContent .= "    <script src=\"https://cdn.tailwindcss.com\"></script>\n";
        $fileContent .= "    <link href=\"https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800;900&display=swap\" rel=\"stylesheet\">\n";
        $fileContent .= "    <style>body { font-family: 'Inter', sans-serif; }</style>\n";
        $fileContent .= "</head>\n";
        $fileContent .= "<body>\n";
        $fileContent .= "<?php include '../../includes/header.php'; ?>\n";

        $fileContent .= "<main class=\"py-12 bg-slate-50 min-h-screen\">\n";
        $fileContent .= "  <div class=\"max-w-6xl mx-auto px-6\">\n";
        $fileContent .= "    <div class=\"flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-6\">\n";
        $fileContent .= "      <a href=\"../../\" class=\"hover:text-primary transition-colors\">Home</a>\n";
        $fileContent .= "      <span>/</span>\n";
        $fileContent .= "      <a href=\"../\" class=\"hover:text-primary transition-colors\">Blog</a>\n";
        $fileContent .= "      <span>/</span>\n";
        $fileContent .= "      <span class=\"text-slate-600\"><?php echo htmlspecialchars(\$meta['category']); ?></span>\n";
        $fileContent .= "    </div>\n";

        $fileContent .= "    <h1 class=\"text-3xl md:text-5xl font-black text-slate-900 tracking-tight mb-8 leading-tight\">\n";
        $fileContent .= "      <?php echo htmlspecialchars(\$meta['title']); ?>\n";
        $fileContent .= "    </h1>\n";

        $fileContent .= "    <div class=\"grid grid-cols-1 lg:grid-cols-12 gap-8 items-start\">\n";
        $fileContent .= "      <div class=\"lg:col-span-8 bg-white p-6 md:p-8 rounded-3xl border border-slate-200/60 shadow-sm space-y-8\">\n";

        $fileContent .= "        <?php if (!empty(\$meta['banner'])): \n";
        $fileContent .= "            \$banner = \$meta['banner'];\n";
        $fileContent .= "            if (strpos(\$banner, 'http') === false) {\n";
        $fileContent .= "                \$banner = '../../' . ltrim(\$banner, '/');\n";
        $fileContent .= "            }\n";
        $fileContent .= "        ?>\n";
        $fileContent .= "            <div class=\"w-full rounded-2xl overflow-hidden border border-slate-100 flex justify-center bg-slate-50\">\n";
        $fileContent .= "                <img src=\"<?php echo htmlspecialchars(\$banner); ?>\" class=\"max-h-[380px] w-auto object-contain\" alt=\"Blog Banner\">\n";
        $fileContent .= "            </div>\n";
        $fileContent .= "        <?php endif; ?>\n";

        $fileContent .= "        <div class=\"flex items-center gap-6 border-y border-slate-100 py-4 text-xs font-semibold text-slate-500\">\n";
        $fileContent .= "            <span>Published: <?php echo date('F d, Y', strtotime(\$meta['published_at'])); ?></span>\n";
        $fileContent .= "            <span>•</span>\n";
        $fileContent .= "            <span>Category: <span class=\"text-emerald-700 font-bold\"><?php echo htmlspecialchars(\$meta['category']); ?></span></span>\n";
        $fileContent .= "        </div>\n";

        $fileContent .= "        <article class=\"prose max-w-none text-slate-600 leading-relaxed\">\n";
        $fileContent .= "            <?php include 'body.php'; ?>\n";
        $fileContent .= "        </article>\n";
        $fileContent .= "      </div>\n";

        $fileContent .= "      <aside class=\"lg:col-span-4 space-y-6\">\n";
        $fileContent .= "        <div class=\"bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm\">\n";
        $fileContent .= "            <h3 class=\"text-sm font-black text-slate-900 uppercase tracking-widest mb-4 pb-2 border-b border-slate-100\">Categories</h3>\n";
        $fileContent .= "            <div class=\"flex flex-col gap-2\">\n";
        $fileContent .= "                <?php\n";
        $fileContent .= "                try {\n";
        $fileContent .= "                    \$cat_res = mysqli_query(\$conn, \"SELECT DISTINCT category FROM posts WHERE status='published' AND category IS NOT NULL AND category != ''\");\n";
        $fileContent .= "                    if (\$cat_res && mysqli_num_rows(\$cat_res) > 0) {\n";
        $fileContent .= "                        while (\$cat_row = mysqli_fetch_assoc(\$cat_res)) {\n";
        $fileContent .= "                            \$cat_name = \$cat_row['category'];\n";
        $fileContent .= "                            echo '<a href=\"../?category=' . urlencode(\$cat_name) . '\" class=\"text-xs font-bold text-slate-600 hover:text-emerald-750 transition-colors flex items-center justify-between py-2 px-3 hover:bg-slate-50 rounded-xl\">';\n";
        $fileContent .= "                            echo '<span>🌿 ' . htmlspecialchars(\$cat_name) . '</span>';\n";
        $fileContent .= "                            echo '</a>';\n";
        $fileContent .= "                        }\n";
        $fileContent .= "                    } else {\n";
        $fileContent .= "                        echo '<span class=\"text-xs text-slate-400 font-medium\">No categories found.</span>';\n";
        $fileContent .= "                    }\n";
        $fileContent .= "                } catch (Exception \$e) {\n";
        $fileContent .= "                    echo '<span class=\"text-xs text-slate-400 font-medium\">No categories found.</span>';\n";
        $fileContent .= "                }\n";
        $fileContent .= "                ?>\n";
        $fileContent .= "            </div>\n";
        $fileContent .= "        </div>\n";
        $fileContent .= "        <div class=\"bg-gradient-to-br from-emerald-800 to-emerald-950 p-6 rounded-3xl text-white shadow-xl text-center space-y-4\">\n";
        $fileContent .= "            <h4 class=\"font-black text-lg\">Visit Hani Nursery 🌿</h4>\n";
        $fileContent .= "            <p class=\"text-xs text-emerald-100 leading-relaxed\">Bring nature and harmony home today. Check out our physical store for exclusive counter discounts!</p>\n";
        $fileContent .= "            <a href=\"../../contact/\" class=\"inline-block bg-white text-emerald-900 px-6 py-2.5 rounded-full text-xs font-black uppercase tracking-wider hover:bg-emerald-50 transition-colors shadow-md\">Get Directions ➔</a>\n";
        $fileContent .= "        </div>\n";
        $fileContent .= "      </aside>\n";
        $fileContent .= "    </div>\n";
        $fileContent .= "  </div>\n";
        $fileContent .= "</main>\n";

        $fileContent .= "<?php include '../../includes/footer.php'; ?>\n";
        $fileContent .= "</body>\n";
        $fileContent .= "</html>";

        file_put_contents($filePath, $fileContent);

        $this->saveSeoData(
            "/blog/$slug/",
            $title,
            $description,
            $finalImage,
            $publishedAt ?: date('Y-m-d H:i:s'),
            $author ? $author['id'] : null
        );

        return "/blog/$slug/";
    }
}
?>
