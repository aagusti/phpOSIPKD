<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class apps extends CI_Controller {

	function __construct() {
		parent::__construct();
		if(!is_login() || !is_super_admin()) {
			$this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
			redirect('login');
			exit;
		}
		
		$this->load->model(array('apps_model'));
	}

	public function index() {
		$data['current'] = 'pengaturan';		
		$data['apps']    = $this->apps_model->get_active_only();
		$this->load->view('vapps', $data);
	}
	
	function grid() {
		$i=0;
		if($query = $this->apps_model->get_all()) {
			foreach($query as $row) {
				$responce->aaData[$i][]=$row->id;
				$responce->aaData[$i][]=$row->nama;
				$responce->aaData[$i][]=$row->app_path;
				$responce->aaData[$i][]='<input type="checkbox" onchange="update_stat('.$row->id.',this.checked);" name="disabled" '.($row->disabled?'checked':'').'>';
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

	function update_stat() {
		$id  = $this->uri->segment(4);
		$val = $this->uri->segment(5);
		if($id && $this->apps_model->get($id)) {
			$data = array('disabled' => $val);
			$this->db->where('id', $id);
			$this->db->update('apps',$data);
		}
		
        if ($rows = $this->apps_model->get_active_only()) {
			$apps = "<option value=\"admin\" selected=\"selected\">ADMIN</option>";
            foreach ($rows as $row) {
				$apps .= "<option value={$row->app_path}>{$row->nama}</option>";
            }
            echo $apps;
        }
	}
		
	//admin
	private function fvalidation() {
		$this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('nama', 'Nama Aplikasi', 'required');
		$this->form_validation->set_rules('app_path', 'Direktori', 'required');
	}
	
	private function fpost() {
		$data['id'] = $this->input->post('id');
		$data['nama'] = $this->input->post('nama');
		$data['app_path'] = $this->input->post('app_path');
		$data['disabled'] = $this->input->post('disabled')? 'checked': '';
		
		return $data;
	}
	
	public function add() {
		$data['current']     = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction']     = active_module_url('apps/add');
		$data['dt']          = $this->fpost();
		
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'nama' => $this->input->post('nama'),
				'app_path' => $this->input->post('app_path'),
				'disabled' => $this->input->post('disabled')? 1:0
			);
			$this->apps_model->save($data);
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');		
			redirect(active_module_url('apps'));
		}
		$this->load->view('vapps_form',$data);
	}
	
	public function edit() {
		$data['current']   = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction']   = active_module_url('apps/update');
			
		$id = $this->uri->segment(4);
		if($id && $get = $this->apps_model->get($id)) {
			$data['dt']['id'] = $get->id;
			$data['dt']['nama'] = $get->nama;
			$data['dt']['app_path'] = $get->app_path;
			$data['dt']['disabled'] = $get->disabled? 'checked' :'' ;
			
			$this->load->view('vapps_form',$data);
		} else {
			show_404();
		}
	}
	
	public function update() {
		$data['current'] = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction'] = active_module_url('apps/update');
		$data['dt'] = $this->fpost();
				
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {	
			$data = array(
				'nama' => $this->input->post('nama'),
				'app_path' => $this->input->post('app_path'),
				'disabled' => $this->input->post('disabled')? 1: 0
			);
			$this->apps_model->update($this->input->post('id'), $data);
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');
			redirect(active_module_url('apps'));
		}
		$this->load->view('vapps_form',$data);
	}
	
	public function delete() {		
		$id = $this->uri->segment(4);
		if($id && $this->apps_model->get($id)) {
			$this->apps_model->delete($id);
			$this->session->set_flashdata('msg_success', 'Data telah dihapus');
			redirect(active_module_url('apps'));
		} else {
			show_404();
		}
	} 
}