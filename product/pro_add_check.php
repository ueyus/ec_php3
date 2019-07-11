<?php
	session_start();
	session_regenerate_id(true);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>商品追加確認</title>
	<link rel="stylesheet" href="../common/css/font-awesome/css/all.css"> 
	<link rel="stylesheet" href="../css/normalize.css">
	<link rel="stylesheet" href="../common/css/kaiin_header.css">
	<link rel="stylesheet" href="../common/css/footer.css">
	<link rel="stylesheet" href="../common/css/kaiin_navi.css">
	<link rel="stylesheet" href="../common/css/kaiin_side.css">
	<link rel="stylesheet" href="../css/pro_add.css">
	<script src="../common/js/common.js"></script>
	<script>
		(function () {
			window.addEventListener('load', disp_upfile);
		})();
	</script>
</head>
<body>
	<?php
		require_once('../common/html/kaiin_header.php');
		require_once('../common/html/kaiin_navi.php');
		require_once('../common/common.php');
	?>

	<div class="main">
		<div class="main-container">
			<h3 class="main-title">商品追加確認</h3>
			<?php

				$post = sanitize($_POST);
				$pro_name = $post['name'];
				$pro_price = $post['price'];
				$pro_cate = $post['category'];
				$pro_file_name = $_FILES['pro_img']['name'];
				$pro_file_tmp = $_FILES['pro_img']['tmp_name'];
			
				$pro_file_path = getUpFileTmpName($pro_file_name);
				$pro_img_dir = getUpFileDir('product');

				$ok_flag = true;
				$file_flag = false;

				if ($pro_name == '') {
					print checkGamenDispFieldError('商品名が入力されていません');
					$ok_flag = false;
				} else {
					print checkGamenDispField('商品名：　' . $pro_name);
				}

				if (preg_match('/^\d+$/', $pro_price) == 0) {
					print checkGamenDispFieldError('価格が正しく入力されていません');
					$ok_flag = false;
				} else {
					print checkGamenDispField('価格：　' . $pro_price);
				}

				if (preg_match('/^\d+$/', $pro_cate) == 0) {
					print checkGamenDispFieldError('価格が正しく入力されていません');
					$ok_flag = false;
				} else {
					print checkGamenDispField('カテゴリー：　' . $pro_cate);
				}

				if (is_uploaded_file($pro_file_tmp)) {
					$file_flag = true;
				}

				if ($file_flag && !move_uploaded_file($pro_file_tmp, $pro_img_dir . $pro_file_path)) {
					print checkGamenDispFieldError('ファイルのアップロードに失敗しました。');
					$ok_flag = false;
				} else if ($file_flag) {
					print checkGamenDispField('ファイル名：　' . $pro_file_name);
					print '<div id="image-zone" class="image-zone"></div>';
				}

				/*
				if ($_FILES['pro_img']['size'] > 0) {
					if ($_FILES['pro_img']['size'] > 1000000) {
						print '<div class="field file error">画像が大きすぎます</div>';
					} else {
						$pro_file_path = sprintf("%s_%s", time(), $pro_file_name);
						move_uploaded_file($file_tmp, $pro_img_dir . $pro_file_path);
					}
				}
				*/

				if (!$ok_flag) {
					print '<form>';
					print '<input type="button" onclick="history.back()" value="戻る" class="btn">';
					print '</form>';
				} else {
					print '<form method="post" action="pro_add_done.php">';
					// 脆弱性を残すため、hiddenで送る
					print '<input type="hidden" name="pro_name" value="' . $pro_name . '">';
					print '<input type="hidden" name="pro_price" value="' . $pro_price . '">';
					print '<input type="hidden" name="pro_category" value="' . $pro_cate . '">';
					print '<input type="hidden" name="pro_file_name" value="' . $pro_file_name . '">';
					print '<input type="hidden" name="pro_file_path" value="' . $pro_file_path . '">';
					print '<input type="hidden" id="url" value="' . $pro_img_dir . $pro_file_path . '">';
					print '<input type="button" onclick="history.back()" value="戻る" class="btn">';
					print '<input type="submit" value="OK" class="btn"	>';
					print '</form>';
				}
			?>
		</div>

		<?php require_once('../common/html/kaiin_side.php'); ?>
	</div>
	<?php require_once('../common/html/footer.php'); ?>
</body>
</html>

