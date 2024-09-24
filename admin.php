<?php
session_start(); // 启动会话

$progressFile = 'progress.json';
$correctPassword = '123'; // 设置一个安全的密码

if (isset($_POST['password'])) {
    if ($_POST['password'] === $correctPassword) {
        $_SESSION['logged_in'] = true; // 设置登录状态
    } else {
        $error = "安全码错误！";
    }
}

if (isset($_POST['new_password'])) {
    $correctPassword = $_POST['new_password']; // 修改密码
    $successMessage = "安全码已更新！";
}

if (isset($_GET['logout'])) {
    session_destroy(); // 退出登录
    header('Location: admin.php'); // 重定向到登录页
    exit();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // 如果用户未登录，显示登录表单
    ?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>登录</title>
        <style>
            body {
                font-family: 'Roboto', sans-serif;
                background: linear-gradient(135deg, #f0f4f8, #e8eaf6);
                color: #333;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .container {
                background: #fff;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                width: 300px;
            }
            h1 {
                text-align: center;
                color: #007BFF;
                margin-bottom: 20px;
            }
            form {
                display: flex;
                flex-direction: column;
            }
            label {
                margin-bottom: 5px;
            }
            input[type="password"] {
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                margin-bottom: 15px;
                transition: border-color 0.3s;
            }
            input[type="password"]:focus {
                border-color: #007BFF;
            }
            button {
                padding: 10px;
                background-color: #007BFF;
                color: #fff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s;
            }
            button:hover {
                background-color: #0056b3;
            }
            p {
                color: red;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>请登录</h1>
            <?php if (isset($error)) echo "<p>$error</p>"; ?>
            <form action="admin.php" method="post">
                <label for="password">安全码:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit">登录</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}

if (file_exists($progressFile)) {
    $progressData = json_decode(file_get_contents($progressFile), true);
} else {
    $progressData = ['progress' => []];
}

// 处理新增项目的表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && isset($_POST['status'])) {
    $newProgress = [
        'step' => $_POST['step'],
        'status' => $_POST['status']
    ];
    $progressData['progress'][] = $newProgress;
    file_put_contents($progressFile, json_encode($progressData));
    header('Location: admin.php'); // 避免重新提交表单
    exit();
}

// 处理删除请求
if (isset($_GET['delete'])) {
    $deleteIndex = intval($_GET['delete']);
    if (isset($progressData['progress'][$deleteIndex])) {
        array_splice($progressData['progress'], $deleteIndex, 1);
        file_put_contents($progressFile, json_encode($progressData));
    }
    header('Location: admin.php'); // 避免重新提交表单
    exit();
}

// 更新百分比
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_percentage'])) {
    $index = intval($_POST['index']);
    $percentage = intval($_POST['percentage']);
    if (isset($progressData['progress'][$index])) {
        $progressData['progress'][$index]['percentage'] = $percentage;
        file_put_contents($progressFile, json_encode($progressData));
    }
    header('Location: admin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>开发程序进度管理</title>
<style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f4f4f9;
        color: #333;
        margin: 0;
        padding: 0;
    }
    .container {
        width: 90%;
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        margin-top: 20px;
    }
    h1, h2 {
        text-align: center;
        color: #333;
    }
    form {
        margin-bottom: 20px;
    }
    label, input {
        display: block;
        margin: 5px 0;
    }
    input[type="text"], input[type="number"], input[type="password"] {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
        border-radius: 4px;
        border: 1px solid #ccc;
        margin-bottom: 10px;
    }
    button {
        padding: 10px 15px;
        background-color: #007BFF;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    button:hover {
        background-color: #0056b3;
    }
    ul {
        list-style: none;
        padding: 0;
    }
    li {
        background: #f9f9f9;
        margin: 10px 0;
        padding: 10px;
        border-left: 5px solid #007BFF;
        position: relative;
    }
    li form {
        display: inline-block;
        margin-left: 10px;
    }
    li a {
        color: #dc3545;
        text-decoration: none;
        position: absolute;
        right: 10px;
        top: 10px;
    }
    li a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
<div class="container">
    <h1>进度管理项目开发</h1>

    <form action="admin.php" method="post">
        <label for="step">步骤:</label>
        <input type="text" id="step" name="step" required>
        <label for="status">状态:</label>
        <input type="text" id="status" name="status" required>
        <button type="submit">添加进度项</button>
    </form>

    <h2>进度列表</h2>
    <ul>
        <?php foreach ($progressData['progress'] as $index => $progress): ?>
            <li>
                Step: <?php echo htmlspecialchars($progress['step']); ?>,
                Status: <?php echo htmlspecialchars($progress['status']); ?>,
                Percentage: <?php echo isset($progress['percentage']) ? $progress['percentage'] : '未设置'; ?>%
                <form action="admin.php" method="post" style="display: inline;">
                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                    <input type="number" name="percentage" value="<?php echo isset($progress['percentage']) ? $progress['percentage'] : 0; ?>" min="0" max="100">
                    <button type="submit" name="update_percentage">更新百分比</button>
                </form>
                <a href="admin.php?delete=<?php echo $index; ?>">删除</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>修改安全码</h2>
    <form action="admin.php" method="post">
        <label for="new_password">新安全码:</label>
        <input type="password" id="new_password" name="new_password" required>
        <button type="submit">修改安全码</button>
    </form>

    <form action="admin.php?logout=true" method="post" style="margin-top: 20px;">
        <button type="submit">退出登录</button
<?php if (isset($successMessage)) echo "<p style='color: green;'>$successMessage</p>"; ?>
</div>
</body>
</html>