<?php
// index.php

// 禁用缓存
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$db = new SQLite3('photo_upload.db');

if (isset($_POST['clear'])) {
    $db->exec('DELETE FROM uploads');
    header("Location: index.php");
    exit();
}

if (isset($_POST['download'])) {
    // 生成HTML表格
    $html = '<table border="1"><tr><th>照片</th><th>备注</th><th>时间</th><th>电脑配置</th><th>使用年限</th><th>资产编号</th></tr>';
    $result = $db->query('SELECT * FROM uploads ORDER BY timestamp DESC');
    while ($row = $result->fetchArray()) {
        $html .= '<tr>';
        $html .= '<td><img src="' . htmlspecialchars($row['photo']) . '" alt="上传的照片" style="max-width:100px; max-height:100px;"></td>';
        $html .= '<td>' . htmlspecialchars($row['remark']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['timestamp']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['computer_config']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['usage_years']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['asset_number']) . '</td>';
        $html .= '</tr>';
    }
    $html .= '</table>';

    // 设置响应头，使浏览器下载文件
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="table.html"');
    header('Content-Length: ' . strlen($html));
    echo $html;
    exit;
}

$result = $db->query('SELECT * FROM uploads ORDER BY timestamp DESC');
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>照片上传系统</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        img { max-width: 100px; max-height: 100px; }
        .button { background-color: #4CAF50; color: white; border: none; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>照片上传系统</h1>
    <a href="upload.php" class="button">上传新照片</a>
    <form method="post" style="display: inline;">
        <input type="submit" name="clear" value="清理数据" class="button" onclick="return confirm('确定要清空所有数据吗？');">
        <input type="submit" name="download" value="下载HTML表格" class="button">
    </form>
    <table>
        <tr>
            <th>照片</th>
            <th>备注</th>
            <th>时间</th>
            <th>电脑配置</th>
            <th>使用年限</th>
            <th>资产编号</th>
            <th>操作</th>
        </tr>
        <?php while ($row = $result->fetchArray()): ?>
        <tr>
            <td><img src="<?php echo htmlspecialchars($row['photo']); ?>" alt="上传的照片"></td>
            <td><?php echo htmlspecialchars($row['remark']); ?></td>
            <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
            <td><?php echo htmlspecialchars($row['computer_config']); ?></td>
            <td><?php echo htmlspecialchars($row['usage_years']); ?></td>
            <td><?php echo htmlspecialchars($row['asset_number']); ?></td>
            <td><a href="edit.php?id=<?php echo $row['id']; ?>" class="button">编辑</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php
$db->close();
?>
