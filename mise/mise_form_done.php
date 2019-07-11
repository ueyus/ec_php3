<?php
	session_start();
	session_regenerate_id(true);

	// Csrf対策
	/*
	if ($_POST['csrf_token'] != $_SESSION['csrf_token']) {
		print '不正な操作が行われました。';
		exit();
	}
	*/
	unset($_SESSION['csrf_token']);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>商品購入完了</title>
	<link href="../common/css/font-awesome/css/all.css" rel="stylesheet"> 
	<link rel="stylesheet" href="../css/normalize.css">
	<link rel="stylesheet" href="../common/css/mise_header.css">
	<link rel="stylesheet" href="../common/css/footer.css">
	<link rel="stylesheet" href="../common/css/mise_navi.css">
	<link rel="stylesheet" href="../common/css/mise_side.css">
	<link rel="stylesheet" href="../css/pro_disp.css">
</head>
<body>
	<?php
		require_once('../common/common.php');
		require_once('../common/html/mise_header.php');
		require_once('../common/html/mise_navi.php');
	?>
	<div class="main">
		<div class="main-container">

			<h3 class="main-title">購入手続き</h3>
			<?php
				require_once('../class/Mise_db.php');

				try {	

					$member_info;
					$is_login = isset($_SESSION['member_login']) && $_SESSION['member_login'] == 1;
					
					$mise_db = new Mise_db();
					$sql;

					if ($is_login) {
						$member_info = $mise_db->get_order($_SESSION['member_code']);
					} else {
						$member_info = sanitize($_SESSION['inputs']);	
					}

					$onamae = $member_info['name'];
					//　メールヘッダーインジェクションように
					// seven pay式？
					$email = $_POST['email'];
					// $email = $member_info['email'];
					$postal1 = $member_info['postal1'];
					$postal2 = $member_info['postal2'];
					$address = $member_info['address'];
					$tel = $member_info['tel'];
					$chumon = $member_info['chumon'];
					$pass = $member_info['password'];
					$danjo = $member_info['danjo'];
					$birth = $member_info['birth'];


					print $onamae . '様<br>';
					print 'ご注文ありがとう<br>';
					print $email . 'にメールを送りましたので、ご確認お願いします。<br>';
					print '商品は以下の住所へ発送させていただきます。<br>';
					print $postal1 . '-' . $postal2 . '<br>';
					print $address . '<br>';
					print $tel . '<br>';

					$honbun = '';
					$honbun .= $onamae . "様　\nこのたびはご注文ありがとうございました。\n";
					$honbun .= "\n";
					$honbun .= "ご注文商品\n";
					$honbun .= "-------------\n";

					$cart = $_SESSION['cart'];
					$count = $_SESSION['count'];
					$max = count($cart);

					for ($i = 0; $i < $max; $i++) {
						$sql = 'select name, price from mst_product where code=?';
						$stmt = $dbh->prepare($sql);
						$data[0] = $cart[$i];
						$stmt->execute($data);

						$rec = $stmt->fetch(PDO::FETCH_ASSOC);

						$name = $rec['name'];
						$price = $rec['price'];
						$kakaku[] = $price;
						$suryo = $count[$i];
						$shokei = $price * $suryo;

						$honbun .= $name . '';
						$honbun .= $price . '円　x';
						$honbun .= $suryo . '個　=';
						$honbun .= $shokei . "円\n";
					}

					$sql = 'lock tables order_tbl, order_product_tbl write';
					$stmt = $dbh->prepare($sql);
					$stmt->execute();

					$last_member = 0;
					if (!$is_login && $chumon == 'chumontouroku') {
						
						$sql = 'insert into order_member(password, name, email, postal1, postal2, address, tel, danjo, born) values(?,?,?,?,?,?,?,?,?)';
						$stmt = $dbh->prepare($sql);
						$data = [];
						$data[] = md5($pass);
						$data[] = $onamae;
						$data[] = $email;
						$data[] = $postal1;
						$data[] = $postal2;
						$data[] = $address;
						$data[] = $tel;
						$data[] = $danjo == 'man' ? 1 : 2;
						$data[] = $birth;			
						$stmt->execute($data);

						$sql = 'select last_insert_id()';
						$stmt = $dbh->prepare($sql);
						$stmt->execute();
						$rec = $stmt->fetch(PDO::FETCH_ASSOC);
						$last_member = $rec['LAST_INSERT_ID()'];
					}

					$sql = 'insert into order_tbl(code_member, name, email, postal1, postal2, address, tel) values(?,?,?,?,?,?,?)';
					$stmt = $dbh->prepare($sql);
					$data = [];
					$data[] = $last_member;
					$data[] = $onamae;
					$data[] = $email;
					$data[] = $postal1;
					$data[] = $postal2;
					$data[] = $address;
					$data[] = $tel;
					$stmt->execute($data);

					$sql = 'select last_insert_id()';
					$stmt = $dbh->prepare($sql);
					$stmt->execute();
					$rec = $stmt->fetch(PDO::FETCH_ASSOC);
					$lastcode = $rec['LAST_INSERT_ID()'];

					for ($i = 0; $i < $max; $i++) {
						$sql = 'insert into order_product_tbl(code_sale, code_product, price, quantity) values(?,?,?,?)';
						$stmt = $dbh->prepare($sql);
						$data = [];
						$data[] = $lastcode;
						$data[] = $cart[$i];
						$data[] = $kakaku[$i];
						$data[] = $count[$i];
						$stmt->execute($data);
					}

					$sql = 'unlock tables';
					$stmt = $dbh->prepare($sql);
					$stmt->execute();

					$dbh = null;
					$chumontoroku = '';

					if (!$is_login && $chumon == 'chumontouroku') {
						print '会員登録が完了しました。<br>';
						print '次回からメールアドレスとパスワードでログインください<br>';
						print 'ご注文が簡単にできるようなります<br>';
						print '<br>';
					}

					$honbun .= "送料は無料です。\n";
					$honbun .= "-------------------\n";
					$honbun .= "\n";
					$honbun .= "代金は以下の口座にお振込みください。\n";
					$honbun .= "テスト銀行　テスト支店　口座1111111111\n";
					$honbun .= "入金確認が取れ次第、梱包、発送させていただきます。\n";
					$honbun .= "\n";
					$honbun .= "◇◇◇◇◇◇◇◇◇◇◇◇◇◇◇◇◇◇◇\n";
					$honbun .= "　～安心野菜の〇〇農園～　\n";
					$honbun .= "\n";
					$honbun .= "〇〇県〇市\n";
					$honbun .= "電話：　22222222\n";
					$honbun .= "FAX：　33333333333\n";
					$honbun .= "\n";

					if (!$is_login && $chumon == 'chumontouroku') {
						$honbun .= "会員登録が完了しました。\n";
						$honbun .= "次回からメールアドレスとパスワードでログインください\n";
						$honbun .= "ご注文が簡単にできるようなります\n";
						$honbun .= "\n";
					}

					$honbun .= "◇◇◇◇◇◇◇◇◇◇◇◇◇◇◇◇◇◇◇\n";


					$title = 'ご注文ありがとうございます。';
					$header = 'From:info@';
					$custom_honbun = html_entity_decode($honbun, ENT_QUOTES, 'UTF-8');
					mb_language('Japanese');
					mb_internal_encoding('UTF-8');
					mb_send_mail($email, $title, $custom_honbun, $header);

					// miseあて
					$title = '注文がありました。';
					$header = 'From:info@';
					$mise_honbun = html_entity_decode($honbun, ENT_QUOTES, 'UTF-8');
					mb_language('Japanese');
					mb_internal_encoding('UTF-8');
					// miseあてemail
					mb_send_mail('test@email.cocooom.jpp', $title, $mise_honbun, $header);


				} catch (Exception $e) {
					print $e;
					print 'ただいま障害によりご迷惑をおかけしています。';
					exit();
				}

			?>
			<a href="mise_list.php">商品画面へ</a>
			</div>
		<?php require_once('../common/html/mise_side.php'); ?>
	</div>
	<?php require_once('../common/html/footer.php'); ?>
</body>
</html>