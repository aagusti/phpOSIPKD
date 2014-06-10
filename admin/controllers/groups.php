<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class groups extends CI_Controller {

	function __construct() {
		parent::__construct();
		if(!is_login() || !is_super_admin()) {
			$this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
			redirect('login');
			exit;
		}
		
		$this->load->model(array('apps_model'));
		$this->load->model(array('groups_model'));
	}

	public function index() {
		$data['current'] = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$this->load->view('vgroups', $data);
	}
	
	function grid() {
		$i=0;
		if($query = $this->groups_model->get_all()) {
			foreach($query as $row) {
				$responce->aaData[$i][]=$row->id;
                $responce->aaData[$i][]=$row->kode;
				$responce->aaData[$i][]=$row->nama;
				$i++;
			}
		} else {
			$responce->sEcho=1;
			$responce->iTotalRecords="0";
			$responce->iTotalDisplayRecords="0";
			$responce->aaData=array();
		}
		echo json_encode($responce);
	}

	//admin
	private function fvalidation() {
		$this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('nama', 'Nama Group', 'required');
		$this->form_validation->set_rules('kode', 'Kode', 'required');
	}
	
	private function fpost() {
		$data['id'] = $this->input->post('id');
		$data['kode'] = $this->input->post('kode');
		$data['nama'] = $this->input->post('nama');
		$data['locked'] = $this->input->post('locked');
		
		return $data;
	}
	
	public function add() {
		$data['current']     = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction']     = active_module_url('groups/add');
		$data['dt']          = $this->fpost();
		
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'kode' => $this->input->post('kode'),
				'nama' => $this->input->post('nama'),
				'kode' => $this->input->post('kode'),
				'locked' => $this->input->post('locked')
			);
			$this->groups_model->save($data);
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');		
			redirect(active_module_url('groups'));
		}
		$this->load->view('vgroups_form',$data);
	}
	
	public function edit() {
		$data['current']   = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction']   = active_module_url('groups/update');
			
		$id = $this->uri->segment(4);
		
		if($id && $this->groups_model->is_locked($id)) {	
			$this->session->set_flashdata('msg_warning', 'Data terkunci, tidak dapat diedit atau dihapus');
			redirect(active_module_url('groups'));
		}
		
		if($id && $get = $this->groups_model->get($id)) {
			$data['dt']['id'] = $get->id;
			$data['dt']['kode'] = $get->kode;
            $data['dt']['nama'] = $get->nama;
			$data['dt']['locked'] = $get->locked;
			
			$this->load->view('vgroups_form',$data);
		} else {
			show_404();
		}
	}
	
	public function update() {
		$data['current'] = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction'] = active_module_url('groups/update');
		$data['dt'] = $this->fpost();
				
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {	
			$data = array(
				'kode' => $this->input->post('kode'),
				'nama' => $this->input->post('nama'),
				'kode' => $this->input->post('kode'),
				'locked' => $this->input->post('locked')
			);
			$this->groups_model->update($this->input->post('id'), $data);
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');
			redirect(active_module_url('groups'));
		}
		$this->load->view('vgroups_form',$data);
	}
	
	public function delete() {
		$id = $this->uri->segment(4);	
		if($id && $this->groups_model->is_locked($id)) {	
			$this->session->set_flashdata('msg_warning', 'Data terkunci, tidak dapat diedit atau dihapus');
			redirect(active_module_url('groups'));
		}
		
		if($id && $this->groups_model->get($id)) {
            $this->db->delete('group_modules',array('group_id' => $id));
            
			$this->groups_model->delete($id);
			$this->session->set_flashdata('msg_success', 'Data telah dihapus');
			redirect(active_module_url('groups'));
		} else {
			show_404();
		}
	}
}