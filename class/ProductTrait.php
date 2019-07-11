<?php

trait ProductTrait {

	public function get_shohin($code) {
		$sql = 'select code, name, price, file_name, file_path from mst_product where code = ?';
		$stmt = $this->db->prepare($sql);
		$data = [$code];
		$stmt->execute($data);

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
}