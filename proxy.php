<? php
// 获取原始 URL 参数
$url = $_SERVER['REQUEST_URI'];
// 检查 URL 是否以 /file/ 开头
if (strpos($url, '/file/') === 0) {
	// 构造图片 URL
	$image_url = 'https://telegra.ph'.$url;
	// 输出图片内容
	header('Content-Type: image/'.pathinfo($url, PATHINFO_EXTENSION));
	readfile($image_url);
} else {
	// 发送代理请求并输出响应内容
	$options = array(
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_HEADER => false,
	        CURLOPT_FOLLOWLOCATION => true,
	        CURLOPT_ENCODING => '',
	        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3',
	        CURLOPT_AUTOREFERER => true,
	        CURLOPT_CONNECTTIMEOUT => 120,
	        CURLOPT_TIMEOUT => 120,
	        CURLOPT_MAXREDIRS => 10,
	        CURLOPT_SSL_VERIFYHOST => false,
	        CURLOPT_SSL_VERIFYPEER => false
	    );
	$ch = curl_init($url);
	curl_setopt_array($ch, $options);
	$content = curl_exec($ch);
	$err = curl_errno($ch);
	$errmsg = curl_error($ch);
	$header = curl_getinfo($ch);
	curl_close($ch);
	if ($errmsg != '') {
		echo "Error: $errmsg";
	} else {
		header('Content-Type: '.$header['content-type']);
		echo $content;
	}
}
?>