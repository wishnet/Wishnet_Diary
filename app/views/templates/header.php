<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($site_name) ? $site_name : "Wishnet's Diary"; ?></title>
    <meta name="description" content="<?php echo isset($site_description) ? htmlspecialchars($site_description) : ''; ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #fef3e2 0%, #fde2e4 25%, #fce4ec 50%, #fff3e0 75%, #fef9f0 100%);
            background-attachment: fixed;
        }
        body::before {
            content: '';
            position: fixed;
            top: -30%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,183,128,0.25) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -20%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,154,139,0.2) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
        .timeline-line {
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #f4a261, #e76f51, #e9c46a, #f4a261);
            transform: translateX(-50%);
        }
        .timeline-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 3px solid #e76f51;
            background: white;
            z-index: 10;
        }
        @media (max-width: 768px) {
            .timeline-line { left: 20px; }
            .timeline-dot { left: 13px !important; }
        }
        .book-paper {
            background: linear-gradient(180deg, #fdf6e3 0%, #f9f0da 30%, #f5e6c8 60%, #faf3e0 100%),
                repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(139,109,69,0.03) 2px, rgba(139,109,69,0.03) 4px);
            border: 1px solid #d4b896;
            box-shadow: 0 4px 20px rgba(139,109,69,0.25), 0 1px 3px rgba(0,0,0,0.1), inset 0 0 30px rgba(139,109,69,0.04);
        }
        .book-paper::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(ellipse at 50% 0%, rgba(255,255,240,0.6) 0%, transparent 70%);
            pointer-events: none;
            border-radius: inherit;
        }
        .book-content { color: #4a3728; line-height: 1.9; }
        .book-content h1 { color: #5c3d2e; }
        .book-content h2 { color: #6b4423; }
        .book-content h3 { color: #7a5230; }
        .book-content strong { color: #3e2723; }
        .book-content a { color: #c17f59; }
        .book-content blockquote { border-left: 3px solid #d4a574; padding-left: 1rem; color: #6d4c41; }
        .book-content code { background: rgba(139,109,69,0.08); color: #8d5524; }
        .book-divider { border: none; height: 1px; background: linear-gradient(to right, transparent, #d4b896, transparent); margin: 1.5rem 0; }
    </style>
</head>
<body class="min-h-screen relative z-10">
