<?php
if (isset($_FILES['image'])) {
	$image = $_FILES['image'];
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
		// 在页面上输出原始图片 URL 和代理后图片 URL
		echo 'https://telegra.ph' . $json[0]['src'] . "\n";
		echo 'https://' . $_SERVER['HTTP_HOST']. $json[0]['src'];
	} else {
		http_response_code(500);
		echo '上传失败';
	}
}
?>