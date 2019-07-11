<?php
	session_start();
	session_regenerate_id(true);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>商品編集確認</title>
	<link rel="stylesheet" href="../common/css/font-awesome/css/all.css"> 
	<link rel="stylesheet" href="../css/normalize.css">
	<link rel="stylesheet" href="../common/css/kaiin_header.css">
	<link rel="stylesheet" href="../common/css/footer.css">
	<link rel="stylesheet" href="../common/css/kaiin_navi.css">
	<link rel="stylesheet" href="../common/css/kaiin_side.css">
	<link rel="stylesheet" href="../css/pro_edit.css">
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
			<h3 class="main-title">商品編集確認</h3>

			<?php
				$pro_code = $_POST['code'];
				$pro_name = $_POST['name'];
				$pro_price = $_POST['price'];
				$pro_file_name = $_FILES['pro_img_file']['name'];
				$pro_file_tmp = $_FILES['pro_img_file']['tmp_name'];
				$pro_file_path = getUpFileTmpName($_FILES['pro_img_file']['name']);

				$pro_code = h($pro_code);
				$pro_name = h($pro_name);
				$pro_price = h($pro_price);
				$pro_file_name = h($pro_file_name);
				$pro_img_dir = getUpFileDir('product');

				$ok_flag = true;
				$file_flag = false;

				if ($pro_code == '') {
					print checkGamenDispFieldError('商品コードがありません');
					$ok_flag = false;
				} else {
					print checkGamenDispField('商品コード：　' . $pro_code);
				}

				if ($pro_name == '') {
					print checkGamenDispFieldError('名前が入力されていません');
					$ok_flag = false;
				} else {
					print checkGamenDispField('商品名：　' . $pro_name);
				}

				if ($pro_price == '') {
					print checkGamenDispFieldError('価格が入力されていません');
					$ok_flag = false;
				} else {
					print checkGamenDispField('価格：　' . $pro_price . '円');
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

				if (!$ok_flag) {
					print '<form>';
					print '<input type="button" onclick="history.back()" value="戻る" class="btn">';
					print '</form>';
				} else {
					print '<form method="post" action="pro_edit_done.php">';
					$_SESSION['pro_code'] = $pro_code;
					$_SESSION['pro_name'] = $pro_name;
					$_SESSION['pro_price'] = $pro_price;
					$_SESSION['pro_file_name'] = $pro_file_name;
					$_SESSION['pro_file_path'] = $pro_file_path;
					print '<input type="hidden" id="url" value="' . $pro_img_dir . $pro_file_path . '">';
					print '<input type="button" onclick="history.back()" value="戻る" class="btn">';
					print '<input type="submit" value="OK" class="btn">';
					print '</form>';
				}

			?>
		</div>
		<?php require_once('../common/html/kaiin_side.php'); ?>
	</div>
	<?php require_once('../common/html/footer.php'); ?>
</body>
</html>

