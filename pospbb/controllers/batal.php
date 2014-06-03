<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class batal extends CI_Controller {

	function __construct() {
		parent::__construct();
		if(!$this->session->userdata('login')) {
			$this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
			redirect('login');
			exit;
		}
        
        if(!is_super_admin() && !isset($this->session->userdata['tpnm'])) {
            show_404();
            exit;
        }
		
		$module = 'POSB';
		$this->load->library('module_auth',array('module'=>$module));

		$this->load->model(array('apps_model'));
		$this->load->model(array('payment_model'));
	}
		
	public function index() {
		if(!$this->module_auth->read) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
			redirect('info');
		}

		$filter = $this->session->userdata('pos_filter');
		$filter = isset($filter) ? $filter : '';
		$data['filter']  = $filter;
    $data['tpnm'] = isset($this->session->userdata['tpnm']) ? $this->session->userdata['tpnm'] : '';

		$data['apps']    = $this->apps_model->get_active_only();
		$data['current'] = 'stts';
		$this->load->view('batalv', $data);
	}
	
	public function cari() {
		if(!$this->module_auth->read) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
			redirect('info');
		}

		$nop = $this->uri->segment(4);
		$thn = $this->uri->segment(5);
		$ke = $this->uri->segment(6);

		if($nop && $thn && $ke && $query = $this->payment_model->get_by_nop_thn_ke($nop, $thn, $ke)) {
		  $terbilang=terbilang($query->jml_sppt_yg_dibayar);
		  //die($terbilang);
			$query =  (object) array_merge((array)$query, array('found'=>1, 'terbilang'=>$terbilang));
			echo json_encode($query);
		} else {
			$result['found'] = 0;
			echo json_encode($result);
		}
	}
	
	public function proses() {
		if(!$this->module_auth->update) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
			redirect('info');
		}

		$nop = $this->uri->segment(4);
		$thn = $this->uri->segment(5);
		$ke = $this->uri->segment(6);

		if($nop && $thn && $ke)
		{
		  $query = $this->payment_model->cancel_nop_thn_ke($nop, $thn, $ke); 
			echo 'yes';
		} else {
			echo 'no';
		}
	}
}