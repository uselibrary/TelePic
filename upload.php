<?php

// Connect to the SQLite database
$db = new SQLite3('/path/to/uploads.db');

// Create the uploads table if it doesn't exist
$db->exec('CREATE TABLE IF NOT EXISTS uploads (id INTEGER PRIMARY KEY, url TEXT)');

if (isset($_FILES['image'])) {
  $image = $_FILES['image'];
  
  // 若图片大小超过5M，则进行压缩
  if ($image['size'] > 5 * 1024 * 1024) {
    $compressed_image = compress_image($image);
    if (!$compressed_image) {
      http_response_code(500);
      echo '上传失败';
      exit;
    }
    $image = $compressed_image;
  }

  // 构建 POST 请求内容
  $data = array(
    'file' => curl_file_create($image['tmp_name'], $image['type'], $image['name'])
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://telegra.ph/upload');
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  // 执行请求，获取响应
  $response = curl_exec($ch);
  curl_close($ch);

  $json = json_decode($response, true);
  if ($json && isset($json[0]['src'])) {
    // 将图片 URL 存入数据库
    $url = 'https://telegra.ph' . $json[0]['src'];
    // Save the URL to the database
    $stmt = $db->prepare('INSERT INTO uploads (url) VALUES (:url)');
    $stmt->bindValue(':url', $url, SQLITE3_TEXT);
    $stmt->execute();

    // 在页面上输出原始图片 URL 和代理后图片 URL
    echo 'https://telegra.ph' . $json[0]['src'] . "\n";
    echo 'https://' . $_SERVER['HTTP_HOST']. $json[0]['src'];
  } else {
    http_response_code(500);
    echo '上传失败';
    echo '上传失败';
  }
}

/**
 * 压缩图片至5M以下
 *
 * @param array $image 上传的图片信息数组
 * @return mixed 返回压缩后的图片信息数组，若压缩失败则返回 false
 */
function compress_image($image) {
  $max_size = 5 * 1024 * 1024; // 最大大小为5M
  $quality = 80; // 初始压缩质量为80

  do {
    // 以JPG格式导出图片至临时文件
    $temp_file = tempnam(sys_get_temp_dir(), 'image');
    if (!$temp_file) {
      return false;
    }
    imagejpeg(imagecreatefromstring(file_get_contents($image['tmp_name'])), $temp_file, $quality);
    $compressed_size = filesize($temp_file);

    // 若压缩后大小小于等于最大大小，则返回压缩后的图片信息数组
    if ($compressed_size <= $max_size) {
      $compressed_image = array(
        'name' => $image['name'],
        'type' => 'image/jpeg',
        'tmp_name' => $temp_file,
        'error' => 0,
        'size' => $compressed_size
      );
      return $compressed_image;
    }

    // 调整压缩质量并重试
    $quality -= 10;
  } while ($quality >= 10);

  // 若调整质量后仍无法压缩至5M以下，则返回 false
  unlink($temp_file);
  return false;
}
