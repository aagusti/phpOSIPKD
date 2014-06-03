<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class app_status extends CI_Controller {

	function __construct() {
		parent::__construct();
		if(!is_login() || !is_super_admin()) {
			$this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
			redirect('login');
			exit;
		}

		$this->load->model(array('app_status_model'));
	}

	function grid() {
		$id = $this->uri->segment(4);
		
		$i=0;
		if($id && $query = $this->app_status_model->get_all($id)) {
			foreach($query as $row) {
				$responce->aaData[$i][]=$row->id;
				$responce->aaData[$i][]=$row->tahun;
				$responce->aaData[$i][]=$row->step;
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
		$this->form_validation->set_rules('app_id', 'Modul', 'required');
		$this->form_validation->set_rules('tahun', 'Tahun', 'required');
		$this->form_validation->set_rules('step', 'Step', 'required');
	}
	
	private function fpost() {
		$data['id'] = $this->input->post('id');
		$data['tahun'] = $this->input->post('tahun');
		$data['step'] = $this->input->post('step');
		
		return $data;
	}
	
	public function update_step() {
		$app_id = $this->uri->segment(4);
		
		$data['current']     = 'pengaturan';
		$data['faction']     = active_module_url('app_status/update_step/'.$app_id);
		$data['dt']          = $this->fpost();
		$data['dt']['app_id'] = $app_id;
		
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {
			$app_id = $this->input->post('app_id');
			$tahun  = $this->input->post('tahun');
			$step   = $this->input->post('step');
			
			if($this->app_status_model->cek_tahun($app_id, $tahun)) 
				$this->app_status_model->update($app_id, $tahun, $step);
			else
				$this->app_status_model->save($app_id, $tahun, $step);
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');		
			redirect(active_module_url('apps'));
		}
		$this->load->view('vapp_status_form',$data);
	}
}