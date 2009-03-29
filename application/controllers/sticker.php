<?php

class Sticker extends Controller {
	function sticker() {
		parent::Controller();
		$this->load->database();
	}
	function index() {
		$this->load->helper('gfx');
		if (!checkAuth(false, false, 'flashdata')) {
			header('Location: ' . base_url());
			exit();
		}
		$user = $this->db->query('SELECT * FROM users WHERE `id` = ' . $this->session->userdata('id') . ' LIMIT 1');
		if ($user->num_rows() === 0) {
			//Rare cases where session exists but got deleted.
			session_data_unset(false);
			flashdata_message('no_such_user');
			header('Location: ' . base_url());
			exit();
		}
		$U = $user->row_array();
		$user->free_result();
		$F = array();
		for ($i = 0; $i < 3; $i++) {
			$feature = $this->db->query('SELECT name, title, description FROM features ' 
			. 'WHERE `id` = ' . $U['feature_' . $i] . ';');
			$F[] = $feature->row_array();
			$feature->free_result();
		}
		unset($feature);
		if (substr($U['name'], 0, 8) === '__temp__') {
			flashdata_message('sticker_nopage');
			header('Location: ' . site_url('editor'));
			exit();
		}
		$data = array(
			'meta' => $this->load->view($this->config->item('language') . '/sticker/meta.php', $U, true),
			'content' => $this->load->view($this->config->item('language') . '/sticker/content.php', array_merge($U, array('features' => $F)), true),
			'db' => 'content '
		);
		$this->load->library('parser');
		$this->parser->page($data, $this->session->userdata('id'), $U);
	}
}