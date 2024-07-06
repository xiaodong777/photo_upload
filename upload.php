<?php
// upload.php

// 禁用缓存
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new SQLite3('photo_upload.db');
    
    $timestamp = date("Y-m-d H:i:s");
    $remark = $_POST['remark'] ?? '';
    $computer_config = $_POST['computer_config'] ?? '';
    $usage_years = $_POST['usage_years'] ?? '';
    $asset_number = $_POST['asset_number'] ?? '';
    
    $target_dir = "uploads/";
    $file_name = time() . "_" . basename($_FILES["photo"]["name"]);
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    // 检查文件是否为实际图像
    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo "文件不是图像。";
        $uploadOk = 0;
    }
    
    // 允许特定的文件格式
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "抱歉，只允许 JPG, JPEG, PNG 和 GIF 文件。";
        $uploadOk = 0;
    }
    
    if ($uploadOk == 0) {
        echo "抱歉，您的文件未被上传。";
    } else {
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_url = "http://183.237.196.106:4435/photo_upload/" . $target_file;
            
            $stmt = $db->prepare('INSERT INTO uploads (photo, remark, timestamp, computer_config, usage_years, asset_number) VALUES (:photo, :remark, :timestamp, :computer_config, :usage_years, :asset_number)');
            $stmt->bindValue(':photo', $photo_url, SQLITE3_TEXT);
            $stmt->bindValue(':remark', $remark, SQLITE3_TEXT);
            $stmt->bindValue(':timestamp', $timestamp, SQLITE3_TEXT);
            $stmt->bindValue(':computer_config', $computer_config, SQLITE3_TEXT);
            $stmt->bindValue(':usage_years', $usage_years, SQLITE3_TEXT);
            $stmt->bindValue(':asset_number', $asset_number, SQLITE3_TEXT);
            $stmt->execute();
            
            $db->close();
            
            header("Location: index.php");
            exit();
        } else {
            echo "抱歉，上传文件时出错。";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>上传照片</title>
    <style>
        body { font-family: Arial, sans-serif; }
        form { max-width: 500px; margin: 0 auto; }
        input, textarea { width: 100%; margin-bottom: 10px; }
        input[type="submit"] { background-color: #4CAF50; color: white; border: none; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; }
    </style>
</head>
<body>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="photo" accept="image/*" capture="camera" required>
        <textarea name="remark" placeholder="备注"></textarea>
        <input type="text" name="computer_config" placeholder="电脑配置">
        <input type="text" name="usage_years" placeholder="使用年限">
        <input type="text" name="asset_number" placeholder="资产编号">
        <input type="submit" value="上传">
    </form>
</body>
</html>
