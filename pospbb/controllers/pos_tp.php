<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pos_tp extends CI_Controller {
    // MAPPING 
    // kd_kanwil: kd_kanwil_bank
    // kd_kantor: kd_kppbb_bank
    
    private function pos_field() {
        $fields = explode(',', POS_FIELD);
        $new_ps = '';
        foreach ($fields as $f) {
            if ($f == 'kd_kanwil_bank')
                $new_ps.= 'kd_kanwil,';
            else if ($f == 'kd_kppbb_bank')
                $new_ps.= 'kd_kppbb,';
            else
                $new_ps.= "{$f},";
        }
        $new_ps = substr($new_ps, 0, -1);
        return $new_ps;
    }
    
	function __construct() {
		parent::__construct();
		if(!is_login() || !is_super_admin()) {
			$this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
			redirect('login');
			exit;
		}

		$this->load->model(array('apps_model'));
		$this->load->model(array('pos_tp_model'));
	}
    
	public function index() {
		$data['current'] = 'master';
		$data['apps']    = $this->apps_model->get_active_only();
		$this->load->view('vpos_tp', $data);
	}

  function grid() {
		$i=0;
    $responce="";
    $query = $this->pos_tp_model->get_all();
    if($query) {
			foreach($query as $row) {
				$responce->aaData[$i][]=$row->id;
        if (DEF_POS_TYPE == 1){
            $responce->aaData[$i][]=$row->kd_kanwil . $row->kd_kantor . $row->kd_tp;
        }
        else{
            $responce->aaData[$i][]= $row->kd_bank_tunggal . $row->kd_bank_persepsi . 
                                     $row->kd_kanwil . $row->kd_kppbb . $row->kd_tp;
				}
        $responce->aaData[$i][]=$row->nm_tp;
				$responce->aaData[$i][]=$row->alamat_tp;
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
  
	// admin
	private function fvalidation() {
		$this->form_validation->set_error_delimiters('<span>', '</span>');
    if (DEF_POS_TYPE==1){
        $this->form_validation->set_rules('kd_kantor', 'Kode', 'trim|required');
    }else{
        $this->form_validation->set_rules('kd_bank_tunggal', 'Kode', 'trim|required');
        $this->form_validation->set_rules('kd_bank_persepsi', 'Kode', 'trim|required');
        $this->form_validation->set_rules('kd_kppbb', 'Kode', 'trim|required');
    }
    $this->form_validation->set_rules('kd_kanwil', 'Kode', 'trim|required');
    $this->form_validation->set_rules('kd_tp', 'Kode', 'trim|required');

		$this->form_validation->set_rules('nm_tp', 'Nama', 'trim|required');
		$this->form_validation->set_rules('alamat_tp', 'Alamat', 'trim|required');
	}

	private function fpost() {
			$data = array(
				'id' => $this->input->post('id'),
				'kd_kanwil' => $this->input->post('kd_kanwil'),
        'kd_tp' => $this->input->post('kd_tp'),
        'nm_tp' => $this->input->post('nm_tp'),
				'alamat_tp' => $this->input->post('alamat_tp'),
				'no_rek_tp' => $this->input->post('no_rek_tp'),
			);
      if (DEF_POS_TYPE==2){
         $data['kd_bank_tunggal'] = $this->input->post('kd_bank_tunggal');
         $data['kd_bank_persepsi'] = $this->input->post('kd_bank_persepsi');
         $data['kd_kppbb'] = $this->input->post('kd_kppbb');
         
      }else{
         $data['kd_kantor'] = $this->input->post('kd_kantor');
      }
		return $data;
	}

	public function add() {
		$data['current']     = 'master';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction']     = active_module_url('pos_tp/add');
		$data['dt']          = $this->fpost();
                
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {
			$data = array(
				//'id' => $this->input->post('id'),
				'kd_kanwil' => $this->input->post('kd_kanwil'),
        'kd_tp' => $this->input->post('kd_tp'),
        'nm_tp' => $this->input->post('nm_tp'),
				'alamat_tp' => $this->input->post('alamat_tp'),
				'no_rek_tp' => $this->input->post('no_rek_tp'),
			);
      if (DEF_POS_TYPE==2){
         $data['kd_bank_tunggal'] = $this->input->post('kd_bank_tunggal');
         $data['kd_bank_persepsi'] = $this->input->post('kd_bank_persepsi');
         $data['kd_kppbb'] = $this->input->post('kd_kppbb');
         
      }else{
         $data['kd_kantor'] = $this->input->post('kd_kantor');
      }
      $user_id = $this->pos_tp_model->save($data);
      
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');
			redirect(active_module_url('pos_tp'));
		}
		$this->load->view('vpos_tp_form',$data);
	}  

	public function edit() {
		$data['current']   = 'master';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction']   = active_module_url('pos_tp/update');

		$id = $this->uri->segment(4);
		if($id && $get = $this->pos_tp_model->get($id)) {
			$data['dt']['id'] = $get->id;
			if (DEF_POS_TYPE==2){
          $data['dt']['kd_bank_tunggal'] = $get->kd_bank_tunggal;
          $data['dt']['kd_bank_persepsi'] = $get->kd_bank_persepsi;
          $data['dt']['kd_kppbb'] = $get->kd_kppbb;
      }
      else{
          $data['dt']['kd_kantor'] = $get->kd_kantor;
      }
      $data['dt']['kd_kanwil'] = $get->kd_kanwil;
      $data['dt']['kd_tp'] = $get->kd_tp;
      
			$data['dt']['nm_tp'] = $get->nm_tp;
			$data['dt']['alamat_tp'] = $get->alamat_tp;
			$data['dt']['no_rek_tp'] = $get->no_rek_tp;

			$this->load->view('vpos_tp_form',$data);
		} else {
			show_404();
		}
	}

	public function update() {
		$data['current'] = 'master';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction'] = active_module_url('pos_tp/update');
		$data['dt'] = $this->fpost();
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'id' => $this->input->post('id'),
				'kd_kanwil' => $this->input->post('kd_kanwil'),
        'kd_tp' => $this->input->post('kd_tp'),
        'nm_tp' => $this->input->post('nm_tp'),
				'alamat_tp' => $this->input->post('alamat_tp'),
				'no_rek_tp' => $this->input->post('no_rek_tp'),
			);
      if (DEF_POS_TYPE==2){
         $data['kd_bank_tunggal'] = $this->input->post('kd_bank_tunggal');
         $data['kd_bank_persepsi'] = $this->input->post('kd_bank_persepsi');
         $data['kd_kppbb'] = $this->input->post('kd_kppbb');
         
      }else{
         $data['kd_kantor'] = $this->input->post('kd_kantor');
      }
      
      $user_id = $this->pos_tp_model->update($this->input->post('id'), $data);
      
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');
      redirect(active_module_url('pos_tp'));
		}
		$this->load->view('vpos_tp_form',$data);
	}

	public function delete() {
		$id = $this->uri->segment(4);
		if($id && $this->pos_tp_model->get($id)) {
      if($id==1) return;

      if($this->pos_tp_model->delete($id))
          $this->session->set_flashdata('msg_success', 'Data telah dihapus');
      else
          $this->session->set_flashdata('msg_error', 'Gagal');

			redirect(active_module_url('pos_tp'));
		} else {
			show_404();
		}
	}
}
