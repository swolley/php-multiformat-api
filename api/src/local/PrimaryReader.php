<?php

trait PrimaryReader {
	function getPrimaryName() {
		return array_column($this->db->procedure('GetPrimaryName', ['table' => 'product']), 'Column_name');
	}
}