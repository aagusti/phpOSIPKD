<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class users extends CI_Controller {

	function __construct() {
		parent::__construct();
		if(!is_login() || !is_super_admin()) {
			$this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
			redirect('login');
			exit;
		}
		
		$this->load->model(array('apps_model'));
		$this->load->model(array('users_model', 'groups_model'));
	}

	public function index() {
		$data['current'] = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$this->load->view('vusers', $data);
	}
	
	function grid() {
		$i=0;
        $responce = new stdClass();
        $query = $this->users_model->get_all();
		if($query) {
			foreach($query as $row) {
				$responce->aaData[$i][]=$row->id;
				$responce->aaData[$i][]=$row->userid;
				$responce->aaData[$i][]=$row->nama;
				$responce->aaData[$i][]=$row->jabatan;
				$responce->aaData[$i][]='<input type="checkbox" onchange="disable_user('.$row->id.',this.checked);" name="disabled" '.($row->disabled?'checked':'').'>';
				$responce->aaData[$i][]=date('d-m-Y',strtotime($row->created));
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
	
	function grid2() {
		$id = $this->uri->segment(4);
		$in = $this->uri->segment(5);
		$ingroup = $in? true : false;
		
		$i=0;
        $responce = new stdClass();
		if($id && $query = $this->users_model->get_by_group($id, $ingroup)) {
			foreach($query as $row) {
				$responce->aaData[$i][]=$row->id;
				$responce->aaData[$i][]='<input type="checkbox" onchange="update_stat('.$row->group_id.','.$row->id.',this.checked);" name="ingroup" '.($row->in_group?'checked':'').'>';
				$responce->aaData[$i][]=$row->userid;
				$responce->aaData[$i][]=$row->nama;
				$responce->aaData[$i][]='<input type="checkbox" disabled="disabled" name="disabled" '.($row->disabled?'checked':'').'>';
				$responce->aaData[$i][]=date('d-m-Y',strtotime($row->created));
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

	function update_unit() {
		$id  = $this->uri->segment(4);
		$val = $this->uri->segment(5);
		if($id && $this->users_model->get($id)) {
			$data = array('allunit' => $val);
			$this->db->where('id', $id);
			$this->db->update('users',$data);
		}
	}
	
	function disable_user() {
		$id  = $this->uri->segment(4);
		$val = $this->uri->segment(5);
		if($id && $this->users_model->get($id)) {
			$data = array('disabled' => $val);
			$this->db->where('id', $id);
			$this->db->update('users',$data);
		}
	}
	
	// admin
	
	// state of group 
	function update_stat() {
		$gid = $this->uri->segment(4);
		$uid = $this->uri->segment(5);
		$val = $this->uri->segment(6);
		if($val==0) {
			if($uid==sipkd_user_id() && $gid==sipkd_group_id()) {
				// ga bisa
			} else {				
				$this->db->where('group_id', $gid);
				$this->db->where('user_id',  $uid);			
				$this->db->delete('user_groups');
			}
		} else {			
			$data = array('group_id' => $gid, 'user_id' => $uid);
			$this->db->insert('user_groups',$data);
		}
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
	public function add() {
		$data['current']     = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction']     = active_module_url('users/add');
		$data['dt']          = $this->fpost();
		
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'userid' => $this->input->post('userid'),
				'nama' => $this->input->post('nama'),
				'passwd' => $this->input->post('passwd'),
				'nip' => $this->input->post('nip'),
				'jabatan' => $this->input->post('jabatan'),
				'disabled' => $this->input->post('disabled') ? 1 : 0,
				'created' => date('Y-m-d')
			);
			$this->users_model->save($data);
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');		
			redirect(active_module_url('users'));
		}
		$this->load->view('vusers_form',$data);
	}
	
	public function edit() {
		$data['current']   = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction']   = active_module_url('users/update');
			
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
		$data['current'] = 'pengaturan';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction'] = active_module_url('users/update');
		$data['dt'] = $this->fpost();
					
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {	
			$data = array(
				'userid' => $this->input->post('userid'),
				'nama' => $this->input->post('nama'),
				'passwd' => $this->input->post('passwd'),
				'nip' => $this->input->post('nip'),
				'jabatan' => $this->input->post('jabatan'),
				'disabled' => $this->input->post('disabled') ? 1 : 0
			);
			$this->users_model->update($this->input->post('id'), $data);
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');
			redirect(active_module_url('users'));
		}
		$this->load->view('vusers_form',$data);
	}
	
	public function delete() {
		$id = $this->uri->segment(4);
		if($id && $this->users_model->get($id)) {
            $this->db->delete('user_groups',array('user_id' => $id));
            
            if($this->users_model->delete($id))
                $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            else
                $this->session->set_flashdata('msg_error', 'Gagal');
        
			redirect(active_module_url('users'));
		} else {
			show_404();
		}
	}
	
	public function changepwd() {
		if ($this->uri->segment(4) == sipkd_user_id())
			$this->edit();
		else
			show_404();
	}
}