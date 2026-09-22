<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = $_SERVER['REQUEST_URI'];
?>

<style>
    /* ================= COLLAPSIBLE SIDEBAR STYLES ================= */
    .sidebar {
        width: 240px;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        background: linear-gradient(180deg, #1b5e20, #43a047);
        padding: 20px 15px;
        color: white;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1001;
        overflow-y: auto;
    }
    .main {
        margin-left: 240px;
        padding: 30px;
        min-height: 100vh;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Collapsed State */
    body.sidebar-collapsed .sidebar {
        left: -240px;
    }
    body.sidebar-collapsed .main {
        margin-left: 0;
    }

    /* Sidebar Toggle Buttons */
    .sidebar-toggle-btn {
        position: absolute;
        top: 20px;
        right: 15px;
        background: rgba(255,255,255,0.15);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
    }
    .sidebar-toggle-btn:hover {
        background: rgba(255,255,255,0.3);
    }

    .floating-toggle-btn {
        position: fixed;
        top: 20px;
        left: 20px;
        background: #1b5e20;
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        z-index: 1000;
        transition: 0.2s;
    }
    .floating-toggle-btn:hover {
        background: #2e7d32;
        transform: scale(1.05);
    }

    body.sidebar-collapsed .floating-toggle-btn {
        display: flex;
    }

    .sidebar .logo {
        font-size: 28px;
        text-align: center;
        margin-bottom: 10px;
    }

    .sidebar h2 {
        text-align: center;
        font-size: 18px;
        margin-bottom: 25px;
        font-weight: 700;
        color: white;
        border-bottom: 1px solid rgba(255,255,255,0.15);
        padding-bottom: 12px;
    }

    .sidebar a {
        display: flex;
        align-items: center;
        gap: 12px;
        color: rgba(255,255,255,0.85);
        text-decoration: none;
        padding: 12px 15px;
        margin-bottom: 8px;
        border-radius: 8px;
        background: rgba(255,255,255,0.06);
        transition: all 0.2s ease;
        font-weight: 500;
        font-size: 14px;
    }

    .sidebar a:hover {
        background: rgba(255,255,255,0.18);
        color: white;
        transform: translateX(4px);
    }

    .sidebar a.active {
        background: rgba(255,255,255,0.25);
        color: white;
        font-weight: 700;
        box-shadow: inset 0 0 5px rgba(0,0,0,0.1);
    }
</style>

<!-- Floating Toggle Button for collapsed state -->
<button class="floating-toggle-btn" onclick="toggleSidebar()">
    <i data-lucide="menu"></i>
</button>

<div class="sidebar">
    <button class="sidebar-toggle-btn" onclick="toggleSidebar()">
        <i data-lucide="chevron-left" id="toggle-chevron"></i>
    </button>

    <div class="logo">🌿</div>
    <h2>Nursery Admin</h2>

    <a href="/admin/dashboard/" class="<?php echo (strpos($current_page, '/admin/dashboard/') !== false) ? 'active' : ''; ?>">
        <i data-lucide="layout-dashboard"></i> Dashboard
    </a>
    <a href="/admin/products/" class="<?php echo (strpos($current_page, '/admin/products/') !== false || strpos($current_page, '/admin/edit-product/') !== false || strpos($current_page, '/admin/add-product/') !== false) ? 'active' : ''; ?>">
        <i data-lucide="sprout"></i> Products
    </a>
    <a href="/admin/orders/" class="<?php echo (strpos($current_page, '/admin/orders/') !== false || strpos($current_page, '/admin/view-order/') !== false) ? 'active' : ''; ?>">
        <i data-lucide="package"></i> Orders
    </a>
    <a href="/admin/blogs.php" class="<?php echo (strpos($current_page, '/admin/blogs.php') !== false || strpos($current_page, '/admin/add-blog/') !== false || strpos($current_page, '/admin/edit-blog/') !== false || strpos($current_page, '/admin/manage-blog/') !== false) ? 'active' : ''; ?>">
        <i data-lucide="book-open"></i> Blogs
    </a>
    <a href="/admin/reviews/" class="<?php echo (strpos($current_page, '/admin/reviews/') !== false) ? 'active' : ''; ?>">
        <i data-lucide="star"></i> Reviews
    </a>
    <a href="/admin/newsletter/" class="<?php echo (strpos($current_page, '/admin/newsletter/') !== false) ? 'active' : ''; ?>">
        <i data-lucide="mail"></i> Newsletter
    </a>
    <a href="/admin/settings/" class="<?php echo (strpos($current_page, '/admin/settings/') !== false) ? 'active' : ''; ?>">
        <i data-lucide="settings"></i> Settings
    </a>
    <a href="/admin/contacte/" class="<?php echo (strpos($current_page, '/admin/contacte/') !== false) ? 'active' : ''; ?>">
        <i data-lucide="contact"></i> contacte
    </a>
    

    <a href="/admin/logout/" style="color: #ffcdd2; margin-top: 20px;">
        <i data-lucide="log-out"></i> Logout
    </a>
</div>

<!-- Lucide CDN + script -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    function toggleSidebar() {
        document.body.classList.toggle("sidebar-collapsed");
        let isCollapsed = document.body.classList.contains("sidebar-collapsed");
        localStorage.setItem("admin_sidebar_collapsed", isCollapsed ? "1" : "0");
        updateChevron();
    }

    function updateChevron() {
        let chev = document.getElementById("toggle-chevron");
        if (chev) {
            if (document.body.classList.contains("sidebar-collapsed")) {
                chev.setAttribute("data-lucide", "chevron-right");
            } else {
                chev.setAttribute("data-lucide", "chevron-left");
            }
            if (window.lucide) {
                window.lucide.createIcons();
            }
        }
    }

    // Restore sidebar state immediately before rendering body to avoid flash
    (function() {
        let collapsed = localStorage.getItem("admin_sidebar_collapsed");
        if (collapsed === "1") {
            document.body.classList.add("sidebar-collapsed");
        }
    })();

    document.addEventListener("DOMContentLoaded", () => {
        updateChevron();
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>
