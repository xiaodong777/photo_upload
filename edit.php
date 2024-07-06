<?php
// edit.php

// 禁用缓存
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$db = new SQLite3('photo_upload.db');

$id = $_GET['id'] ?? null;

if (!$id) {
    die("没有指定要编辑的记录");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $remark = $_POST['remark'] ?? '';
    $computer_config = $_POST['computer_config'] ?? '';
    $usage_years = $_POST['usage_years'] ?? '';
    $asset_number = $_POST['asset_number'] ?? '';

    $stmt = $db->prepare('UPDATE uploads SET remark = :remark, computer_config = :computer_config, usage_years = :usage_years, asset_number = :asset_number WHERE id = :id');
    $stmt->bindValue(':remark', $remark, SQLITE3_TEXT);
    $stmt->bindValue(':computer_config', $computer_config, SQLITE3_TEXT);
    $stmt->bindValue(':usage_years', $usage_years, SQLITE3_TEXT);
    $stmt->bindValue(':asset_number', $asset_number, SQLITE3_TEXT);
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();

    // 处理新图片上传
    if ($_FILES['photo']['size'] > 0) {
        $target_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_url = "http://183.237.196.106:4435/photo_upload/" . $target_file;
            
            $stmt = $db->prepare('UPDATE uploads SET photo = :photo WHERE id = :id');
            $stmt->bindValue(':photo', $photo_url, SQLITE3_TEXT);
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->execute();
        }
    }

    header("Location: index.php");
    exit();
}

$result = $db->query("SELECT * FROM uploads WHERE id = $id");
$row = $result->fetchArray();

if (!$row) {
    die("找不到指定的记录");
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑记录</title>
    <style>
        body { font-family: Arial, sans-serif; }
        form { max-width: 500px; margin: 0 auto; }
        input, textarea { width: 100%; margin-bottom: 10px; }
        input[type="submit"] { background-color: #4CAF50; color: white; border: none; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; }
        img { max-width: 200px; max-height: 200px; }
    </style>
</head>
<body>
    <h1>编辑记录</h1>
    <form action="edit.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
        <img src="<?php echo htmlspecialchars($row['photo']); ?>" alt="当前照片"><br>
        <input type="file" name="photo" accept="image/*">
        <textarea name="remark" placeholder="备注"><?php echo htmlspecialchars($row['remark']); ?></textarea>
        <input type="text" name="computer_config" placeholder="电脑配置" value="<?php echo htmlspecialchars($row['computer_config']); ?>">
        <input type="text" name="usage_years" placeholder="使用年限" value="<?php echo htmlspecialchars($row['usage_years']); ?>">
        <input type="text" name="asset_number" placeholder="资产编号" value="<?php echo htmlspecialchars($row['asset_number']); ?>">
        <input type="submit" value="保存更改">
    </form>
    <a href="index.php">返回主页</a>
</body>
</html>

<?php
$db->close();
?>
