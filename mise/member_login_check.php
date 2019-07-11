<?php

require_once '../common/common.php';

try {
	$member_email = $_POST['email'];
	$member_pass = $_POST['password'];

	#$member_email = htmlspecialchars($member_email);
	#$member_pass = htmlspecialchars($member_pass);

	$member_pass = md5($member_pass);

	$db = connect_db();
	$db->query('set names utf8');

	$sql = 'select code, name from order_member where email = ? and password = ?';
	$stmt = $db->prepare($sql);
	$data = [$member_email, $member_pass];
	$stmt->execute($data);

	$db = null;

	$rec = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rec == false) {
		print 'メールアドレスかパスワードが間違っています';
		print '<a href="member_login.html">戻る</a>';
	} else {
		var_dump("expression");
		session_start();
		$_SESSION['member_login'] = 1;
		$_SESSION['member_email'] = $member_email;
		$_SESSION['member_name'] = $rec['name'];
		$_SESSION['member_code'] = $rec['code'];
		var_dump($_SESSION);
		header('Location:mise_list.php');
	}
} catch (Exception $e) {
	print 'it sysetem error!!!';
	exit();
}

?>