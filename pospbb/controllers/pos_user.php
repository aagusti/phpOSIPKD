<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pos_user extends CI_Controller {
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
		$this->load->model(array('pos_user_model', 'tp_model'));
	}
    
	public function index() {
		$data['current'] = 'master';
		$data['apps']    = $this->apps_model->get_active_only();
		$this->load->view('vpos_user', $data);
	}

	function grid() {
		$i=0;
        $responce="";
        $query = $this->pos_user_model->get_all();
		if($query) {
			foreach($query as $row) {
				$responce->aaData[$i][]=$row->id;
				$responce->aaData[$i][]=$row->userid;
				$responce->aaData[$i][]=$row->nama;
				$responce->aaData[$i][]=$row->nip;
				$responce->aaData[$i][]=$row->jabatan;
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
		$this->form_validation->set_rules('userid', 'userid', 'trim|required|min_length[1]');
		$this->form_validation->set_rules('nip', 'NIP', 'trim|required|min_length[1]');
		$this->form_validation->set_rules('nama', 'Uraian', 'trim|required');
		$this->form_validation->set_rules('passwd', 'Password', 'trim|required');
		$this->form_validation->set_rules('tp', 'Tempat Pembayaran', 'trim|required');
	}

	private function fpost() {
		$data['id'] = $this->input->post('id');
		$data['userid'] = $this->input->post('userid');
		$data['nama'] = $this->input->post('nama');
		$data['passwd'] = $this->input->post('passwd');
		$data['nip'] = $this->input->post('nip');
		$data['jabatan'] = $this->input->post('jabatan');
		$data['disabled'] = $this->input->post('disabled') ? 'checked' :'';
		$data['tp'] = $this->input->post('tp');

		return $data;
	}

	public function add() {
		$data['current']     = 'master';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction']     = active_module_url('pos_user/add');
		$data['dt']          = $this->fpost();
                
        $tp = '';
        $select_data  = $this->tp_model->get_all();
        if($select_data)
            foreach ($select_data as $row) {
                $fields = explode(',', $this->pos_field());
                $tp_val = '';
                foreach ($fields as $f) {
                    $tp_val .= $row->{$f}."|";
                }
                $tp_val = substr($tp_val, 0, -1);
                
                $tp .= "<option value=\"{$tp_val}\">{$row->nm_tp}</option>";
            }
                
		$data['select_tp'] = $tp;

		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'userid' => $this->input->post('userid'),
				'nama' => $this->input->post('nama'),
				'passwd' => $this->input->post('passwd'),
				'nip' => $this->input->post('nip'),
				'jabatan' => $this->input->post('jabatan'),
				'disabled' => $this->input->post('disabled') ? 1 : 0,
				// 'updated' => date('Y-m-d')
			);
			if($user_id = $this->pos_user_model->save($data)) {
                // masukin ke group pospbb - kalo ada hmmmmm
                $group_id = $this->db->query("SELECT id FROM groups WHERE kode='pospbb'")->row()->id;  // grp "pospbb" <---------------------------
                if(!empty($group_id)) {
                    $data = array(
                        'user_id'  => $user_id,
                        'group_id' => $group_id,
                    );
                    $this->db->insert('user_groups',$data);
                }
                
                // masukin ke user pbb
                $tp = explode('|',$this->input->post('tp'));
                $fields = explode(',', $this->pos_field());
                $data = array(); $i=0;
                
                foreach ($fields as $f) {
                    $data[$f] = $tp[$i];
                    $i++;
                }
                
                $data['user_id'] = $user_id;
                $this->db->insert('user_pbb',$data);
            }

			$this->session->set_flashdata('msg_success', 'Data telah disimpan');
			redirect(active_module_url('pos_user'));
		}
		$this->load->view('vpos_user_form',$data);
	}

	public function edit() {
		$data['current']   = 'master';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction']   = active_module_url('pos_user/update');

		$id = $this->uri->segment(4);
		if($id && $get = $this->pos_user_model->get($id)) {
			$data['dt']['id'] = $get->id;
			$data['dt']['userid'] = $get->userid;
			$data['dt']['nama'] = $get->nama;
			$data['dt']['passwd'] = $get->passwd;
			$data['dt']['nip'] = $get->nip;
			$data['dt']['jabatan'] = $get->jabatan;
			$data['dt']['disabled'] = $get->disabled ? 'checked' : '';

            $select_data  = $this->tp_model->get_all();
            $tp = '';
            if($select_data)
                foreach ($select_data as $row) {
                    $fields = explode(',', $this->pos_field());
                    $tp_val = ''; $get_val = '';                    
                    foreach ($fields as $f) {
                        $tp_val  .= $row->{$f}."|";
                        $get_val .= $get->{$f}."|";
                    }
                    $tp_val  = substr($tp_val,  0, -1);
                    $get_val = substr($get_val, 0, -1);
                    
                    if($tp_val == $get_val)
                        $tp .= "<option value=\"{$tp_val}\" selected>{$row->nm_tp}</option>";
                    else
                        $tp .= "<option value=\"{$tp_val}\">{$row->nm_tp}</option>";
                }
            $data['select_tp'] = $tp;

			$this->load->view('vpos_user_form',$data);
		} else {
			show_404();
		}
	}

	public function update() {
		$data['current'] = 'master';
		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction'] = active_module_url('pos_user/update');
		$data['dt'] = $this->fpost();

        $tp = '';
        $select_data  = $this->tp_model->get_all();
        if($select_data)
            foreach ($select_data as $row) {
                $fields  = explode(',', $this->pos_field());
                $tp_post = $this->input->post('tp');
                $tp_val  = '';
                foreach ($fields as $f) {
                    $tp_val  .= $row->{$f}."|";
                }
                           
                if($tp_val == $tp_post)
                    $tp .= "<option value=\"{$tp_val}\" selected>{$row->nm_tp}</option>";
                else
                    $tp .= "<option value=\"{$tp_val}\">{$row->nm_tp}</option>";
            }
        $data['select_tp'] = $tp;

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
			$this->pos_user_model->update($this->input->post('id'), $data);

            $user_id = $this->input->post('id');
            $up_cnt = $this->db->get_where('user_pbb', array('user_id'=>$user_id))->num_rows();
            if($up_cnt>0) {
                // update ke user pbb
                $tp = explode('|',$this->input->post('tp'));
                $fields = explode(',', $this->pos_field());
                $data = array(); $i=0;
                foreach ($fields as $f) {
                    $data[$f] = $tp[$i];
                    $i++;
                }
                $this->db->update('user_pbb',$data, array('user_id'=>$user_id));
            } else {
                // masukin ke user pbb
                $tp = explode('|',$this->input->post('tp'));
                $fields = explode(',', $this->pos_field());
                $data = array(); $i=0;
                foreach ($fields as $f) {
                    $data[$f] = $tp[$i];
                    $i++;
                }
                $data['user_id'] = $user_id;
                $this->db->insert('user_pbb',$data);
            }
            
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');
			redirect(active_module_url('pos_user'));
		}
		$this->load->view('vpos_user_form',$data);
	}

	public function delete() {
		$id = $this->uri->segment(4);
		if($id && $this->pos_user_model->get($id)) {
            if($id==1) return;

            $this->db->delete('user_pbb',array('user_id' => $id));
            $this->db->delete('user_groups',array('user_id' => $id));

            if($this->pos_user_model->delete($id))
                $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            else
                $this->session->set_flashdata('msg_error', 'Gagal');

			redirect(active_module_url('pos_user'));
		} else {
			show_404();
		}
	}
}
