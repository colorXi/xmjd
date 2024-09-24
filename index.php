<?php
$progressFile = 'progress.json';

if (file_exists($progressFile)) {
    $progressData = json_decode(file_get_contents($progressFile), true);
} else {
    $progressData = ['progress' => []];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>进度展示</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(to right, #74ebd5, #ACB6E5);
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: flex-start; /* 修改为flex-start以向上对齐 */
        min-height: 100vh; /* 确保内容至少占满整个视口高度 */
        padding: 15px; /* 添加内边距 */
    }
    .container {
        background-color: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        max-width: 600px;
        width: 90%;
        margin: 15px; /* 上下左右留白 */
        animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    h1 {
        font-size: 28px;
        margin-bottom: 25px;
        text-align: center;
        color: #333;
    }
    ul {
        list-style-type: none;
        padding: 0;
    }
    li {
        background-color: #f9f9f9;
        margin-bottom: 15px;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    li:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    .progress-step {
        font-weight: bold;
        color: #007bff;
    }
    .progress-status {
        color: #555;
    }
    .progress-percentage {
        font-size: 16px;
        color: #666;
    }
    .progress-bar-container {
        background-color: #e0e0e0;
        border-radius: 5px;
        overflow: hidden;
        margin-top: 10px;
    }
    .progress-bar {
        height: 20px;
        background-color: #76c7c0; /* 单一颜色 */
        width: 0;
        transition: width 0.4s ease;
    }
</style>
</head>
<body>
<div class="container">
    <h1>开发进度展示</h1>
    <ul>
        <?php foreach ($progressData['progress'] as $progress): ?>
            <li>
                <div class="progress-step">项目: <?php echo htmlspecialchars($progress['step']); ?></div>
                <div class="progress-status">状态: <?php echo htmlspecialchars($progress['status']); ?></div>
                <div class="progress-percentage">
                    进度: <?php echo isset($progress['percentage']) ? $progress['percentage'] : '未设置'; ?>%
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: <?php echo isset($progress['percentage']) ? $progress['percentage'] : 0; ?>%;">
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
