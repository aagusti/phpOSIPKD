<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class user_pbbms extends CI_Controller
{
    private $module = 'pbbm_user';
    private $controller = 'user_pbbms';
    
    function __construct()
    {
        parent::__construct();
        if (!is_login()) {
            $this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
            redirect('login');
            exit;
        }
        
        $this->load->model(array(
            'apps_model'
        ));
        $this->load->model('user_pbbms_model', 'upm');
        $this->load->model('kecModel', 'kec');
        $this->load->model('kelModel', 'kel');
        $this->load->model('users_model', 'u');
        $this->load->library('module_auth', array(
            'module' => $this->module
        ));
    }
    
    public function index()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect(active_module_url(''));
        }
        
        $data['current'] = 'ref';
        $data['apps']    = $this->apps_model->get_active_only();
        $this->load->view('vuser_pbbms', $data);
    }
    
    function grid()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect(active_module_url(''));
        }
        
        $i        = 0;
        $responce = "";
        $query    = $this->upm->get_all();
        if ($query) {
            foreach ($query as $row) {
                $responce->aaData[$i][] = $row->user_id;
                $responce->aaData[$i][] = $row->nama;
                
                $responce->aaData[$i][] = $row->kd_kecamatan . "|" . (!empty($row->nm_kecamatan) ? $row->nm_kecamatan : 'SEMUA KECAMATAN');
                $responce->aaData[$i][] = $row->kd_kelurahan . "|" . (!empty($row->nm_kelurahan) ? $row->nm_kelurahan : 'SEMUA KELURAHAN');
                // $responce->aaData[$i][]='<input type="checkbox" onchange="disable_user('.$row->user_id.',this.checked);" name="disabled" '.($row->disabled?'checked':'').'>';
                // $responce->aaData[$i][]=date('d-m-Y',strtotime($row->created));
                $i++;
            }
        } else {
            $responce->sEcho                = 1;
            $responce->iTotalRecords        = "0";
            $responce->iTotalDisplayRecords = "0";
            $responce->aaData               = array();
        }
        echo json_encode($responce);
    }
    
    function disable_user()
    {
        if (!$this->module_auth->write) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_write);
            redirect(active_module_url(''));
        }
        
        $id  = $this->uri->segment(4);
        $val = $this->uri->segment(5);
        if ($id && $this->upm->get($id)) {
            $data = array(
                'disabled' => $val
            );
            $this->db->where('user_id', $id);
            $this->db->update('users_pbbms', $data);
        }
    }
    
    private function fvalidation()
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        $this->form_validation->set_rules('user_id', 'User', 'required|min_length[1]');
        $this->form_validation->set_rules('kd_kecamatan', 'Kecamatan', 'required');
        $this->form_validation->set_rules('kd_kelurahan', 'Kelurahan', 'required');
    }
    
    private function fpost()
    {
        
        $data['user_id']      = $this->input->post('user_id');
        $data['kd_kecamatan'] = $this->input->post('kd_kecamatan');
        $data['kd_kelurahan'] = $this->input->post('kd_kelurahan');
        // $data['disabled'] = $this->input->post('disabled') ? 'checked' :'';
        return $data;
    }
    
    public function get_lurah()
    {
        $kec_kd             = $this->uri->segment(4);
        $data['kelurahans'] = $this->kel->getRecord($kec_kd, '000');
        if ($data['kelurahans'])
            $data['success'] = true;
        else
            $data['success'] = false;
        echo json_encode($data);
        
    }
    
    
    //admin
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_insert);
            redirect(active_module_url(''));
        }
        $data['current']    = 'ref';
        $data['apps']       = $this->apps_model->get_active_only();
        $data['faction']    = active_module_url('user_pbbms/add');
        $data['dt']         = $this->fpost();
        $data['dt']['mode'] = 0;
        $data['users']      = $this->u->get_all();
        $data['kecamatan']  = $this->kec->getRecord('000');
        $data['kelurahan']  = $this->kel->getRecord($data['dt']['kd_kecamatan'], '000');
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'user_id' => $this->input->post('user_id'),
                'kd_propinsi' => KD_PROPINSI,
                'kd_dati2' => KD_DATI2,
                'kd_kelurahan' => $this->input->post('kd_kelurahan'),
                'kd_kecamatan' => $this->input->post('kd_kecamatan'),
                // 'disabled' => $this->input->post('disabled') ? 1 : 0,
                'created' => date('Y-m-d')
            );
            $this->upm->save($data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('user_pbbms'));
        }
        $this->load->view('vuser_pbbms_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect(active_module_url(''));
        }
        
        $data['current'] = 'ref';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('user_pbbms/update');
        
        $id = $this->uri->segment(4);
        if ($id && $get = $this->upm->get($id)) {
            $data['dt']['mode']         = 1;
            $data['dt']['user_id']      = $get->user_id;
            $data['dt']['kd_kecamatan'] = $get->kd_kecamatan;
            $data['dt']['kd_kelurahan'] = $get->kd_kelurahan;
            
            $data['users']     = $this->u->get_all();
            $data['kecamatan'] = $this->kec->getRecord('000');
            $data['kelurahan'] = $this->kel->getRecord($data['dt']['kd_kecamatan'], '000');
            //$data['dt']['passwd'] = $get->passwd;
            // $data['dt']['unit_id'] = $get->unit_id;
            // $data['dt']['allunit'] = $get->allunit ? 'checked' : '';
            // $data['dt']['disabled'] = $get->disabled ? 'checked' : '';
            
            $this->load->view('vuser_pbbms_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_edit);
            redirect(active_module_url(''));
        }
        $data['current'] = 'ref';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('user_pbbms/update');
        $data['dt']      = $this->fpost();
        
        //$data['units']   = $this->unit_kerja_model->get_all();
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'user_id' => $this->input->post('user_id'),
                'kd_kecamatan' => $this->input->post('kd_kecamatan'),
                'kd_kelurahan' => $this->input->post('kd_kelurahan')
                // 'disabled' => $this->input->post('disabled') ? 1 : 0
            );
            $this->upm->update($this->input->post('user_id'), $data);
            
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url('user_pbbms'));
        }
        $this->load->view('vuser_pbbms_form', $data);
    }
    
    public function delete()
    {
        if (!$this->module_auth->delete) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
            redirect(active_module_url(''));
        }
        $id = $this->uri->segment(4);
        if ($id && $this->upm->get($id)) {
            $this->upm->delete($id);
            $this->session->set_flashdata('msg_success', 'Data telah dihapus');
            redirect(active_module_url('user_pbbms'));
        } else {
            show_404();
        }
    }
}