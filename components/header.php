<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $finalTitle = $pageTitle ?? ($meta['meta_title'] ?? ($meta['title'] ?? 'Hani Nursery'));
    $finalDesc = $pageDescription ?? ($meta['meta_description'] ?? ($meta['description'] ?? ''));
    ?>
    <title><?php echo htmlspecialchars($finalTitle); ?></title>
    <?php if (!empty($finalDesc)): ?>
        <meta name="description" content="<?php echo htmlspecialchars($finalDesc); ?>">
    <?php endif; ?>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#1b5e20',
                            foreground: '#ffffff',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
