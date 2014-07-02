<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class privileges extends CI_Controller {

	function __construct() {
		parent::__construct();
		if(!is_login() || !is_super_admin()) {
			$this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
			redirect('login');
			exit;
		}
		
		$this->load->model(array('apps_model'));
		$this->load->model(array('privileges_model', 'modules_model'));
	}

	public function index() {
		$data['current'] = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();

		$select = '';
		$rows   = $this->apps_model->get_active_only();
		if($rows) {
			foreach($rows as $row) {
				if ($this->session->userdata('selected_app') == $row->id)
					$select .= '<option value="'.$row->id.'" selected="selected">'.$row->nama.'</option>';
				else
					$select .= '<option value="'.$row->id.'">'.$row->nama.'</option>';
			}
		} else 
			$select = '<option value="">Tidak ada data!</option>';
		
		$data['app_data'] = $select;
		$this->load->view('vprivileges', $data);
	}
	
	function grid() {
		$app_id = $this->uri->segment(4);
		$grp_id = $this->uri->segment(5);
		
        $this->session->set_userdata('selected_app', $app_id);
		
		$i=0;
        $responce = new stdClass();
		if($app_id && $grp_id && $query = $this->privileges_model->get_by_app($app_id, $grp_id)) {
			foreach($query as $row) {
				$responce->aaData[$i][]=$row->module_id;
				$responce->aaData[$i][]=$row->module_nm;
				$responce->aaData[$i][]='<input type="checkbox" onchange="update_stat('.$row->group_id.','.$row->module_id.',\'reads\', this.checked);"   '.($row->reads?'checked':'').' name="a">';
				$responce->aaData[$i][]='<input type="checkbox" onchange="update_stat('.$row->group_id.','.$row->module_id.',\'inserts\', this.checked);" '.($row->inserts?'checked':'').' name="b">';
				$responce->aaData[$i][]='<input type="checkbox" onchange="update_stat('.$row->group_id.','.$row->module_id.',\'writes\', this.checked);"  '.($row->writes?'checked':'').' name="c">';
				$responce->aaData[$i][]='<input type="checkbox" onchange="update_stat('.$row->group_id.','.$row->module_id.',\'deletes\', this.checked);" '.($row->deletes?'checked':'').' name="d">';
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
		$gid = $this->uri->segment(4);
		$mid = $this->uri->segment(5);
		$fld = $this->uri->segment(6);
		$val = $this->uri->segment(7);
		if($gid && $mid && $fld) {
			$this->privileges_model->upd_auth($gid, $mid, $fld, $val);
		} 
	}
	
	
	//admin - modules
	private function fvalidation() {
		$this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('nama', 'Nama Module', 'required');
		// $this->form_validation->set_rules('kode', 'Kode', 'required');
	}
	
	private function fpost() {
		$data['id'] = $this->input->post('id');
		$data['kode'] = $this->input->post('kode');
		$data['nama'] = $this->input->post('nama');
		$data['app_id'] = $this->input->post('app_id');
		
		return $data;
	}
	
	public function add() {
		$data['current']      = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction']      = active_module_url('privileges/add');
		$data['dt']           = $this->fpost();
		$data['dt']['app_id'] = $this->uri->segment(4);
		
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'kode' => $this->input->post('kode'),
				'nama' => $this->input->post('nama'),
				'app_id' => $this->input->post('app_id'),
			);
			$this->modules_model->save($data);
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');		
			redirect(active_module_url('privileges'));
		}
		$this->load->view('vmodules_form',$data);
	}
	
	public function edit() {
		$data['current']   = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction']   = active_module_url('privileges/update');
			
		$id = $this->uri->segment(4);
		if($id && $get = $this->modules_model->get($id)) {
			$data['dt']['id'] = $get->id;
			$data['dt']['kode'] = $get->kode;
			$data['dt']['nama'] = $get->nama;
			$data['dt']['app_id'] = $get->app_id;
			
			$this->load->view('vmodules_form',$data);
		} else {
			show_404();
		}
	}
	
	public function update() {
		$data['current'] = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction'] = active_module_url('privileges/update');
		$data['dt'] = $this->fpost();
				
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {	
			$data = array(
				'kode' => $this->input->post('kode'),
				'nama' => $this->input->post('nama'),
				'app_id' => $this->input->post('app_id'),
			);
			$this->modules_model->update($this->input->post('id'), $data);
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');
			redirect(active_module_url('privileges'));
		}
		$this->load->view('vmodules_form',$data);
	}
	
	public function delete() {		
		$id = $this->uri->segment(4);
		if($id && $this->modules_model->get($id)) {
			$this->modules_model->delete($id);
			$this->session->set_flashdata('msg_success', 'Data telah dihapus');
			redirect(active_module_url('privileges'));
		} else {
			show_404();
		}
	} 
}