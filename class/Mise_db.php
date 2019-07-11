<?php

require_once('../common/common.php');

class Mise_db {
	
	private $db;

	function __construct() {
		$this->db = connect_db();
		$this->db->query('set names utf8');
	}

	function get_shohins($order = null) {
		$columns = [
			'code',
			'name',
			'price',
			'file_name',
			'file_path',
			'category',
		];

		$str_column = implode(', ', $columns);

		if ($order == null) {
			// デフォルトの並び
			$order = 'datetime desc';
		}

		$sql = sprintf('select %s from mst_product order by %s', $str_column, $order);
		$stmt = $this->db->prepare($sql);
		$stmt->execute();

		$ret = [];
		while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$tmp = [];
			foreach ($columns as $key => $col) {
				$tmp[$col] = $rec[$col];
			}
			$ret[] = $tmp;
		}
		return $ret;
	}

	function get_category() {

		$columns = [
			'id',
			'text'
		];

		$str_column = implode(', ', $columns);

		$sql = sprintf('select %s from pro_category order by id', $str_column);
		$stmt = $this->db->prepare($sql);
		$stmt->execute();

		$ret = [];
		while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$tmp = [];
			foreach ($columns as $key => $col) {
				$tmp[$col] = $rec[$col];
			}
			$ret[] = $tmp;
		}
		return $ret;
	}

	function get_order($id) {
		$sql = 'select * from order_tbl where code = ?';
		$stmt = $dbh->prepare($sql);
		$stmt->execute([$id]);

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}



	function __destruct() {
		$this->db = null;
	}
}