<?php
	session_start();
	session_regenerate_id(true);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>	</title>
</head>
<body>
<?php
		try {

			$pro_code = $_GET['pro_code'];
			//　ここでサニタイジング
			// $pro_code = htmlspecialchars($pro_code);
			if (isset($_SESSION['cart']) == true) {
				$cart = $_SESSION['cart'];
				$count = $_SESSION['count'];
			}

			if (isset($cart[$pro_code])) {
				var_dump("expression");
				/*
				print 'その商品はすでにカートに入っています。';
				print '<a href="mise_list.php">商品一覧に戻る</a>';
				exit();
				*/
				$cart[$pro_code] = $cart[$pro_code]+1;
			} else {
				$cart[$pro_code] = 1;
			}

			/*
			$cart[] = $pro_code;
			$count[] = 1;
			*/
			$_SESSION['cart'] = $cart;
			// $_SESSION['count'] = $count;
			foreach ($cart as $key => $value) {
				print $value;
				print '<br>';
			}
		} catch (Exception $e) {
				print 'system error !!!';
				print $e;
				exit();
		} 
?>
		カートに追加しました。<br>
		<br>
		<a href="mise_list.php">商品一覧に戻る</a>
		
</body>
</html>