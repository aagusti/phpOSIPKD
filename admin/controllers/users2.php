<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class users2 extends CI_Controller {

	function __construct() {
		parent::__construct();
		if(!is_login()) {
			$this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
			redirect('login');
			exit;
		}
		
		$this->load->model(array('apps_model'));
		$this->load->model(array('users_model', 'groups_model'));
	}

	public function index() {
		show_404();
	}
	
	private function fvalidation() {
		$this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('userid', 'userid', 'required|min_length[1]');
		$this->form_validation->set_rules('nama', 'Uraian', 'required');
		$this->form_validation->set_rules('passwd', 'Password', 'required');
	}
	
	private function fpost() {
		$data['id'] = $this->input->post('id');
		$data['userid'] = $this->input->post('userid');
		$data['nama'] = $this->input->post('nama');
		$data['passwd'] = $this->input->post('passwd');
		$data['nip'] = $this->input->post('nip');
		$data['jabatan'] = $this->input->post('jabatan');
		$data['disabled'] = $this->input->post('disabled') ? 'checked' :'';
		
		return $data;
	}
	
	//admin	
	public function edit() {
		$data['current']   = 'beranda';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction']   = base_url('admin/users2/update');
			
		$id = $this->uri->segment(4);
		if($id && $get = $this->users_model->get($id)) {
			$data['dt']['id'] = $get->id;
			$data['dt']['userid'] = $get->userid; 
			$data['dt']['nama'] = $get->nama;
			$data['dt']['passwd'] = $get->passwd;
			$data['dt']['nip'] = $get->nip;
			$data['dt']['jabatan'] = $get->jabatan;
			$data['dt']['disabled'] = $get->disabled ? 'checked' : '';
			
			$this->load->view('vusers_form',$data);
		} else {
			show_404();
		}
	}
	
	public function update() {
		$data['current'] = 'beranda';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction'] = base_url('admin/users2/update');
		$data['dt'] = $this->fpost();
					
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {	
			$data = array(
				// 'userid' => $this->input->post('userid'),
				'nama' => $this->input->post('nama'),
				'passwd' => $this->input->post('passwd'),
				'nip' => $this->input->post('nip'),
				'jabatan' => $this->input->post('jabatan'),
				// 'disabled' => $this->input->post('disabled') ? 1 : 0
			);
			$this->users_model->update($this->input->post('id'), $data);
			
			$this->session->sess_destroy();
			$this->session->set_flashdata('msg_success', 'Data user telah berhasil di ubah, silahkan login kembali.');
			redirect(base_url('login'));
		}
		$this->load->view('vusers_form',$data);
	}
	
	public function changepwd() {
		if ($this->uri->segment(4) == sipkd_user_id())
			$this->edit();
		else
			show_404();
	}
}