<?php
class loaddata extends CI_Controller {    
    function __construct()
    {
        parent::__construct();
        if (active_module() != 'pospbb') {
            show_404();
            exit;
        }
        
        $this->load->model("pbbm_model");
    }
    
    // PINDAHAN DARI PBBM :D
    // beberapa function dihilangkan
    
    function transaksi1()
    {
        // ob_start("ob_gzhandler");
        
        $kec_kd = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        if (get_user_kec_kd() != '000' && get_user_kec_kd() != $kec_kd)
            $kec_kd = get_user_kec_kd();
        
        $kel_kd = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';
        if (get_user_kel_kd() != '000' && get_user_kel_kd() != $kel_kd)
            $kec_kd = get_user_kel_kd();
        
        $buku    = (isset($_GET['buku'])) ? $_GET['buku'] : '11';
        $bukumin = $this->pbbm_model->rangebuku[substr($buku, 0, 1)][0];
        $bukumax = $this->pbbm_model->rangebuku[substr($buku, 1, 1)][1];
        
        $tglm = (isset($_GET['tglawal'])) ? $_GET['tglawal'] : date('d-m-Y');
        $tgls = (isset($_GET['tglakhir'])) ? $_GET['tglakhir'] : date('d-m-Y');
        $tglm = substr($tglm, 6, 4) . '-' . substr($tglm, 3, 2) . '-' . substr($tglm, 0, 2);
        $tgls = substr($tgls, 6, 4) . '-' . substr($tgls, 3, 2) . '-' . substr($tgls, 0, 2);
        
        $tahun_sppt1 = (isset($_GET['tahun_sppt1'])) ? $_GET['tahun_sppt1'] : date('Y');
        $tahun_sppt2 = (isset($_GET['tahun_sppt2'])) ? $_GET['tahun_sppt2'] : date('Y');
        
        $path_to_root = active_module_url();
        
        $aColumns     = array(
            'kode',
            'uraian',
            'thn_pajak_sppt',
            'pokok',
            'denda',
            'bayar',
            'tanggal',
            'nm_tp',
        );
        $sIndexColumn = "kode";
        
        $iDisplayLength = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $iDisplayStart  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $iSortCol_0     = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $iSortingCols   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $sSortDir_0     = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        $sEcho          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $sSearch        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $sSearch_0      = (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $sSearch_1      = (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $sSearch_2      = (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $sSearch_3      = (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $sSearch_4      = (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        
        /*
         * Limit
         */
        
        $sLimit     = "";
        $pageSize   = (isset($_GET['iDisplayLength']) && $_GET['iDisplayLength'] != '-1' ? $_GET['iDisplayLength'] : 15);
        $pageNumber = (isset($_GET['iDisplayStart']) && $_GET['iDisplayStart'] != '-1' ? $_GET['iDisplayStart'] : 1);
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            // $sLimit = "LIMIT $pageSize OFFSET $pageNumber";
        }
        
        
        /*
         * Ordering
         */
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    if ($aColumns[intval($_GET['iSortCol_' . $i])] == "bphtbno" || $aColumns[intval($_GET['iSortCol_' . $i])] == "tanggal") {
                        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . $_GET['sSortDir_' . $i] . ", ";
                    } else {
                        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . ' ' . $_GET['sSortDir_' . $i] . ", ";
                    }
                }
            }
            
            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY ") {
                $sOrder = "";
            }
        }
        
        
        /* 
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        
        
        $where = "WHERE p.tgl_pembayaran_sppt BETWEEN '$tglm' AND '$tgls'
            AND k.kd_propinsi='" . KD_PROPINSI . "' 
            AND k.kd_dati2='" . KD_DATI2 . "' 
            AND p.thn_pajak_sppt BETWEEN '$tahun_sppt1' AND '$tahun_sppt2'
            AND k.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax ";
        
        //die($where);    
        if ($kec_kd != "000") {
            $where .= " AND k.kd_kecamatan='$kec_kd'";
            if ($kel_kd != "000")
                $where .= " AND k.kd_kelurahan='$kel_kd'";
        }
        
        $search = '';
        if ($sSearch)
            $search .= " AND k.nm_wp_sppt ilike '%$sSearch%'";
        
        $iTotal    = 0;
        $iFiltered = 0;
        /*
        $sql_query = "SELECT count(*) c FROM sppt k 
            INNER JOIN pembayaran_sppt p 
            ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2 
            AND k.kd_kecamatan = p.kd_kecamatan 
            AND k.kd_kelurahan = p.kd_kelurahan 
            AND k.kd_blok = p.kd_blok 
            AND k.no_urut = p.no_urut 
            AND k.kd_jns_op = p.kd_jns_op 
            AND k.thn_pajak_sppt = p.thn_pajak_sppt 
            $where ";
        $qry       = $this->db->query($sql_query);
        $row       = $qry->row();
        $iTotal    = $row->c;
        
        if ($search) {
            $sql_query .= $search;
            $qry       = $this->db->query($sql_query);
            $row       = $qry->row();
            $iFiltered = $row->c;
        } else
            $iFiltered = $iTotal;
        */
        
        /*
         * Output
         */
         
        /// -- DARI SINI ..
        $fields     = explode(',', POS_FIELD);
        $pos_fld    = '';
        $pos_join   = '';
        $pos_uraian = '';
        $fs = '';
        foreach ($fields as $f) {
            $fs = $f;
            if ($f == 'kd_kanwil_bank')
                $fs = 'kd_kanwil';
            else if ($f == 'kd_kppbb_bank')
                $fs = 'kd_kppbb';
                
            $pos_fld .= "p.{$f}, ";
            $pos_join .= "p.{$f}=tp.{$fs} and ";
            $pos_uraian .= "tp.{$fs}||";
        }
        $pos_fld = substr($pos_fld, 0, -2);
        $pos_join = substr($pos_join, 0, -4);
        $pos_uraian = substr($pos_uraian, 0, -2);
        
        $tp_kd = (isset($_GET['tp_kd'])) ? $_GET['tp_kd'] : '';
        if ($tp_kd != "")
            $where .= " AND {$pos_uraian} = '{$tp_kd}'";
            
        $sql_query_r = "SELECT  
            k.kd_propinsi||'.'||k.kd_dati2||'-'||k.kd_kecamatan||'.'||k.kd_kelurahan ||'-'|| k.kd_blok ||'.'||k.no_urut||'.'|| k.kd_jns_op ||' '|| k.thn_pajak_sppt kode, 
            k.nm_wp_sppt uraian, {$pos_uraian}||':'||tp.nm_tp nm_tp, p.thn_pajak_sppt,
            (p.jml_sppt_yg_dibayar - p.denda_sppt) pokok, p.denda_sppt denda, p.jml_sppt_yg_dibayar bayar, to_char(p.tgl_pembayaran_sppt,'dd-mm-yyyy') tanggal
            FROM sppt k 
            INNER JOIN pembayaran_sppt p 
            ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2 
            AND k.kd_kecamatan = p.kd_kecamatan 
            AND k.kd_kelurahan = p.kd_kelurahan 
            AND k.kd_blok = p.kd_blok 
            AND k.no_urut = p.no_urut 
            AND k.kd_jns_op = p.kd_jns_op 
            AND k.thn_pajak_sppt = p.thn_pajak_sppt 
            LEFT JOIN tempat_pembayaran tp ON {$pos_join}
            $where $search 
            ORDER BY 1,2,3 ";
        
        $sql_query_r .= "$sOrder $sLimit";
        
        $qry = $this->db->query($sql_query_r);
        
        $output = array(
            "sEcho" => $sEcho,
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFiltered,
            "iDisplayStart" => $iDisplayStart,
            "iDisplayLength" => $iDisplayLength,
            
            // "SQL Query" => $sql_query_r,
            "aaData" => array()
        );
        
        $pg_pokok = 0;
        $pg_denda = 0;
        $pg_total = 0;
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($i > 2 && $i < 5)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                else
                    $row[] = $aRow->$aColumns[$i];
            }
            $output['aaData'][] = $row;
            
            $pg_pokok += $aRow->$aColumns[3];
            $pg_denda += $aRow->$aColumns[4];
            $pg_total += $aRow->$aColumns[5];
            
        }
        /*
        $row = array();
        $row[]              = '';
        $row[]              = 'Jumlah Halaman';
        $row[]              = number_format($pg_pokok, 0, ',', '.');
        $row[]              = number_format($pg_denda, 0, ',', '.');
        $row[]              = number_format($pg_total, 0, ',', '.');
        $row[]              = '';
        $output['aaData'][] = $row;
        
        if ($iDisplayStart + $iDisplayLength + 1 >= $iFiltered) {
            // $sql_query_r = "SELECT  '' kode, 'TOTAL' uraian, sum(k.pbb_yg_harus_dibayar_sppt) pokok, 
            $sql_query_r = "SELECT  '' kode, 'TOTAL' uraian, sum(p.jml_sppt_yg_dibayar - p.denda_sppt) pokok, 
                sum(p.denda_sppt) denda, sum(p.jml_sppt_yg_dibayar) bayar, '' as tanggal
                FROM sppt k 
                INNER JOIN pembayaran_sppt p 
                ON k.kd_propinsi = p.kd_propinsi
                AND k.kd_dati2 = p.kd_dati2 
                AND k.kd_kecamatan = p.kd_kecamatan 
                AND k.kd_kelurahan = p.kd_kelurahan 
                AND k.kd_blok = p.kd_blok 
                AND k.no_urut = p.no_urut 
                AND k.kd_jns_op = p.kd_jns_op 
                AND k.thn_pajak_sppt = p.thn_pajak_sppt 
                $where $search ";
            
            $qry = $this->db->query($sql_query_r);
            foreach ($qry->result() as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if ($i > 1 && $i < 5)
                        $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                    else
                        $row[] = $aRow->$aColumns[$i];
                }
                $output['aaData'][] = $row;
            }
        }
        */
        
        $output['pokok'] = number_format($pg_pokok, 0, ',', '.');
        $output['denda'] = number_format($pg_denda, 0, ',', '.');
        $output['total'] = number_format($pg_total, 0, ',', '.');
        
        echo json_encode($output);
    }
    
    function transaksi2()
    {
        // ob_start("ob_gzhandler");
        
        $buku        = (isset($_GET['buku'])) ? $_GET['buku'] : '11';
        $bukumin     = $this->pbbm_model->rangebuku[substr($buku, 0, 1)][0];
        $bukumax     = $this->pbbm_model->rangebuku[substr($buku, 1, 1)][1];
        $tahun_sppt1 = (isset($_GET['tahun_sppt1'])) ? $_GET['tahun_sppt1'] : date('Y');
        $tahun_sppt2 = (isset($_GET['tahun_sppt2'])) ? $_GET['tahun_sppt2'] : date('Y');
        $kec_kd      = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        if (get_user_kec_kd() != '000' && get_user_kec_kd() != $kec_kd)
            $kec_kd = get_user_kec_kd();
        
        $kel_kd = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';
        
        if (get_user_kel_kd() != '000' && get_user_kel_kd() != $kel_kd)
            $kec_kd = get_user_kel_kd();
        
        $path_to_root = active_module_url();
        
        $aColumns     = array(
            'kode',
            'uraian',
            'thn_pajak_sppt',
            'pokok',
            'denda',
            'bayar'
        );
        $sIndexColumn = "kode";
        
        $iDisplayLength = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $iDisplayStart  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        
        $iSortCol_0   = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $iSortingCols = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $sSortDir_0   = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        
        $sEcho = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        
        $sSearch   = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $sSearch_0 = (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $sSearch_1 = (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $sSearch_2 = (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $sSearch_3 = (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $sSearch_4 = (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        
        $tglm = (isset($_GET['tglawal'])) ? $_GET['tglawal'] : date('d-m-Y');
        $tgls = (isset($_GET['tglakhir'])) ? $_GET['tglakhir'] : date('d-m-Y');
        
        $tglm = substr($tglm, 6, 4) . '-' . substr($tglm, 3, 2) . '-' . substr($tglm, 0, 2);
        $tgls = substr($tgls, 6, 4) . '-' . substr($tgls, 3, 2) . '-' . substr($tgls, 0, 2);
        
        /*
         * Limit
         */
        
        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            // $sLimit = "LIMIT " . $_GET['iDisplayLength'] . " OFFSET " . $_GET['iDisplayStart'];
        }
        
        
        /*
         * Ordering
         */
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    if ($aColumns[intval($_GET['iSortCol_' . $i])] == "bphtbno" || $aColumns[intval($_GET['iSortCol_' . $i])] == "tanggal") {
                        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . $_GET['sSortDir_' . $i] . ", ";
                    } else {
                        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . ' ' . $_GET['sSortDir_' . $i] . ", ";
                    }
                }
            }
            
            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY ") {
                $sOrder = "";
            }
        }
        
        /* 
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $where = "WHERE p.tgl_pembayaran_sppt BETWEEN '$tglm' AND '$tgls'
            AND p.kd_propinsi='" . KD_PROPINSI . "' 
            AND p.kd_dati2='" . KD_DATI2 . "' 
            AND p.thn_pajak_sppt BETWEEN '$tahun_sppt1' AND '$tahun_sppt2'
            AND k.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax ";
        
        if ($kec_kd != "000")
            $where .= " AND p.kd_kecamatan='$kec_kd'";
        if ($kel_kd != "000")
            $where .= " AND p.kd_kelurahan='$kel_kd'";
        
        $search = '';
        if ($sSearch)
            $search .= " AND tp.nm_tp ilike '%$sSearch%'";
        
        $iTotal    = 0;
        $iFiltered = 0;
        
        /// -- DARI SINI ..
        $fields     = explode(',', POS_FIELD);
        $pos_fld    = '';
        $pos_join   = '';
        $pos_uraian = '';
        $fs = '';
        foreach ($fields as $f) {
            $fs = $f;
            if ($f == 'kd_kanwil_bank')
                $fs = 'kd_kanwil';
            else if ($f == 'kd_kppbb_bank')
                $fs = 'kd_kppbb';
                
            $pos_fld .= "p.{$f}, ";
            $pos_join .= "p.{$f}=tp.{$fs} and ";
            $pos_uraian .= "tp.{$fs}||";
        }
        $pos_fld = substr($pos_fld, 0, -2);
        $pos_join = substr($pos_join, 0, -4);
        $pos_uraian = substr($pos_uraian, 0, -2);
        
        $tp_kd = (isset($_GET['tp_kd'])) ? $_GET['tp_kd'] : '';
        if ($tp_kd != "")
            $where .= " AND {$pos_uraian} = '{$tp_kd}'";
            
        /*
        // $sql_query = "SELECT count(*) c FROM  (SELECT DISTINCT p.kd_kanwil, p.kd_kantor, p.kd_tp, tp.nm_tp, p.tgl_pembayaran_sppt 
        $sql_query = "SELECT count(*) c FROM  (SELECT DISTINCT {$pos_fld}, tp.nm_tp, p.tgl_pembayaran_sppt 
            FROM pembayaran_sppt p 
            LEFT JOIN tempat_pembayaran tp 
            -- ON p.kd_kanwil=tp.kd_kanwil and p.kd_kantor=tp.kd_kantor AND p.kd_tp=tp.kd_tp 
            ON {$pos_join}
            INNER JOIN SPPT k ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2 
            AND k.kd_kecamatan = p.kd_kecamatan 
            AND k.kd_kelurahan = p.kd_kelurahan 
            AND k.kd_blok = p.kd_blok 
            AND k.no_urut = p.no_urut 
            AND k.kd_jns_op = p.kd_jns_op 
            AND k.thn_pajak_sppt = p.thn_pajak_sppt 

            $where AND (1=1)
            )z";
        $qry       = $this->db->query($sql_query);
        $row       = $qry->row();
        $iTotal    = $row->c;
        
        if ($search) {
            $sql_query = str_replace('AND (1=1)', $search, $sql_query);
            $qry       = $this->db->query($sql_query);
            $row       = $qry->row();
            $iFiltered = $row->c;
        } else
            $iFiltered = $iTotal;
        */
        
        /*
         * Output
         */
        // $sql_query_r = "SELECT  tgl_pembayaran_sppt kode,tp.kd_kanwil||tp.kd_kantor||tp.kd_tp||':'||tp.nm_tp uraian, 
        $sql_query_r = "SELECT  tgl_pembayaran_sppt kode,{$pos_uraian}||':'||tp.nm_tp uraian, p.thn_pajak_sppt,
            sum(p.jml_sppt_yg_dibayar - p.denda_sppt)  pokok, sum(p.denda_sppt) denda, 
            sum(p.jml_sppt_yg_dibayar) bayar
            FROM sppt k 
            INNER JOIN pembayaran_sppt p 
            ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2 
            AND k.kd_kecamatan = p.kd_kecamatan 
            AND k.kd_kelurahan = p.kd_kelurahan 
            AND k.kd_blok = p.kd_blok 
            AND k.no_urut = p.no_urut 
            AND k.kd_jns_op = p.kd_jns_op 
            AND k.thn_pajak_sppt = p.thn_pajak_sppt 
            LEFT JOIN tempat_pembayaran tp ON {$pos_join}
            $where $search 
            GROUP BY 1,2,3
            ORDER BY 1,2,3 ";
        $sql_query_r .= "$sOrder $sLimit";
        
        $output = array(
            "sEcho" => $sEcho,
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFiltered,
            "iDisplayStart" => $iDisplayStart,
            "iDisplayLength" => $iDisplayLength,
            // "SQL Query" => $sql_query_r,
            "aaData" => array()
        );
        
        $pg_pokok = 0;
        $pg_denda = 0;
        $pg_total = 0;
        
        $qry = $this->db->query($sql_query_r);
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($i > 2)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                elseif($i == 0)
                    $row[] = date('d-m-Y', strtotime($aRow->$aColumns[$i]));
                else
                    $row[] = $aRow->$aColumns[$i];
            }
            
            $pg_pokok += $aRow->$aColumns[3];
            $pg_denda += $aRow->$aColumns[4];
            $pg_total += $aRow->$aColumns[5];
            
            $output['aaData'][] = $row;
        }
        /*
        $row                = array();
        $row[]              = '';
        $row[]              = 'Jumlah Halaman';
        $row[]              = number_format($pg_pokok, 0, ',', '.');
        $row[]              = number_format($pg_denda, 0, ',', '.');
        $row[]              = number_format($pg_total, 0, ',', '.');
        $output['aaData'][] = $row;
        
        if ($iDisplayStart + $iDisplayLength + 1 >= $iFiltered) {
            $sql_query_r = "SELECT  '' kode, 'TOTAL' uraian, 
                -- sum(k.pbb_yg_harus_dibayar_sppt)  pokok, sum(p.denda_sppt) denda, 
                sum(p.jml_sppt_yg_dibayar - p.denda_sppt)  pokok, sum(p.denda_sppt) denda, 
                sum(p.jml_sppt_yg_dibayar) bayar
                FROM sppt k 
                INNER JOIN pembayaran_sppt p 
                ON k.kd_propinsi = p.kd_propinsi
                AND k.kd_dati2 = p.kd_dati2 
                AND k.kd_kecamatan = p.kd_kecamatan 
                AND k.kd_kelurahan = p.kd_kelurahan 
                AND k.kd_blok = p.kd_blok 
                AND k.no_urut = p.no_urut 
                AND k.kd_jns_op = p.kd_jns_op 
                AND k.thn_pajak_sppt = p.thn_pajak_sppt 
                LEFT JOIN tempat_pembayaran tp 
                -- ON p.kd_kanwil=tp.kd_kanwil and p.kd_kantor=tp.kd_kantor AND p.kd_tp=tp.kd_tp
                ON {$pos_join}
                $where $search ";
            
            $qry = $this->db->query($sql_query_r);
            foreach ($qry->result() as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if ($i > 1)
                        $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                    else
                        $row[] = $aRow->$aColumns[$i];
                }
                $output['aaData'][] = $row;
            }
        }
        */
        
        $output['pokok'] = number_format($pg_pokok, 0, ',', '.');
        $output['denda'] = number_format($pg_denda, 0, ',', '.');
        $output['total'] = number_format($pg_total, 0, ',', '.');
        
        echo json_encode($output);
    }
    
    function tranmonths()
    {
        // ob_start("ob_gzhandler");
        
        
        $buku        = (isset($_GET['buku'])) ? $_GET['buku'] : '11';
        $bukumin     = $this->pbbm_model->rangebuku[substr($buku, 0, 1)][0];
        $bukumax     = $this->pbbm_model->rangebuku[substr($buku, 1, 1)][1];
        $tahun       = (isset($_GET['tahun'])) ? $_GET['tahun'] : date('Y');
        $tahun_sppt1 = (isset($_GET['tahun_sppt1'])) ? $_GET['tahun_sppt1'] : date('Y');
        $tahun_sppt2 = (isset($_GET['tahun_sppt2'])) ? $_GET['tahun_sppt2'] : date('Y');
        $kec_kd      = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        $kel_kd      = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';
        
        if (get_user_kec_kd() != '000' && get_user_kec_kd() != $kec_kd)
            $kec_kd = get_user_kec_kd();
        
        if (get_user_kel_kd() != '000' && get_user_kel_kd() != $kel_kd)
            $kec_kd = get_user_kel_kd();
        
        $path_to_root = active_module_url();
        
        $aColumns     = array(
            'kode',
            'uraian',
            'thn_pajak_sppt',
            'pokok',
            'denda',
            'bayar'
        );
        $sIndexColumn = "kode";
        
        $iDisplayLength = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $iDisplayStart  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        
        $iSortCol_0   = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $iSortingCols = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $sSortDir_0   = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        
        $sEcho = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        
        $sSearch   = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $sSearch_0 = (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $sSearch_1 = (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $sSearch_2 = (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $sSearch_3 = (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $sSearch_4 = (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        
        /*
         * Limit
         */
        
        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            // $sLimit = "LIMIT " . $_GET['iDisplayLength'] . " OFFSET " . $_GET['iDisplayStart'];
        }
        
        
        /*
         * Ordering
         */
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    if ($aColumns[intval($_GET['iSortCol_' . $i])] == "bphtbno" || $aColumns[intval($_GET['iSortCol_' . $i])] == "tanggal") {
                        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . $_GET['sSortDir_' . $i] . ", ";
                    } else {
                        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . ' ' . $_GET['sSortDir_' . $i] . ", ";
                    }
                }
            }
            
            $sOrder = substr_replace($sOrder, "", -2) . ", kode";
            if ($sOrder == "ORDER BY ") {
                $sOrder = "";
            }
        }
        
        
        /* 
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        
        
        $search = '';
        if ($sSearch)
            $search .= " AND tp.nm_tp ilike '%$sSearch%'";
        
        $where = "WHERE extract(year FROM p.tgl_pembayaran_sppt)= $tahun 
            AND p.kd_propinsi='" . KD_PROPINSI . "' 
            AND p.kd_dati2='" . KD_DATI2 . "'
            AND k.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax 
            AND p.thn_pajak_sppt between '$tahun_sppt1' AND '$tahun_sppt2' ";
        if ($kec_kd != "000")
            $where .= " AND p.kd_kecamatan='$kec_kd'";
        if ($kel_kd != "000")
            $where .= " AND p.kd_kelurahan='$kel_kd'";
        
        // POS_FIELD
        $fields     = explode(',', POS_FIELD);
        $pos_fld    = '';
        $pos_join   = '';
        $pos_uraian = '';
        $fs = '';
        foreach ($fields as $f) {
            $fs = $f;
            if ($f == 'kd_kanwil_bank')
                $fs = 'kd_kanwil';
            else if ($f == 'kd_kppbb_bank')
                $fs = 'kd_kppbb';

            $pos_fld .= "p.{$f}, ";
            $pos_join .= "p.{$f}=tp.{$fs} and ";
            $pos_uraian .= "tp.{$fs}||";
        }
        $pos_fld = substr($pos_fld, 0, -2);
        $pos_join = substr($pos_join, 0, -4);
        $pos_uraian = substr($pos_uraian, 0, -2);
        
        $tp_kd = (isset($_GET['tp_kd'])) ? $_GET['tp_kd'] : '';
        if ($tp_kd != "")
            $where .= " AND {$pos_uraian} = '{$tp_kd}'";
            
        /*
        $iTotal    = 0;
        $iFiltered = 0;

        $sql_query = "SELECT count(*) c FROM  (
            -- SELECT DISTINCT p.kd_kanwil, p.kd_kantor, p.kd_tp, tp.nm_tp, extract(month FROM p.tgl_pembayaran_sppt) 
            SELECT DISTINCT {$pos_fld}, tp.nm_tp, extract(month FROM p.tgl_pembayaran_sppt) 
            FROM pembayaran_sppt p 
            LEFT JOIN tempat_pembayaran tp 
            -- ON p.kd_kanwil=tp.kd_kanwil and p.kd_kantor=tp.kd_kantor AND p.kd_tp=tp.kd_tp 
            ON {$pos_join}
            INNER JOIN sppt k ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2 
            AND k.kd_kecamatan = p.kd_kecamatan 
            AND k.kd_kelurahan = p.kd_kelurahan 
            AND k.kd_blok = p.kd_blok 
            AND k.no_urut = p.no_urut 
            AND k.kd_jns_op = p.kd_jns_op 
            AND k.thn_pajak_sppt = p.thn_pajak_sppt 

            $where AND (1=1) )a";
        $qry       = $this->db->query($sql_query);
        $row       = $qry->row();
        $iTotal    = $row->c;
        
        if ($search) {
            $sql_query = str_replace('AND (1=1)', $search, $sql_query);
            $qry       = $this->db->query($sql_query);
            $row       = $qry->row();
            $iFiltered = $row->c;
        } else
            $iFiltered = $iTotal;
        */
        
        /*
         * Output
         */
        $sql_query_r = "SELECT  Extract(month FROM tgl_pembayaran_sppt) kode,
            {$pos_uraian}||':'||tp.nm_tp uraian, p.thn_pajak_sppt,
            sum(p.jml_sppt_yg_dibayar - p.denda_sppt)  pokok, 
            sum(p.denda_sppt) denda, sum(p.jml_sppt_yg_dibayar) bayar
            FROM sppt k 
            INNER JOIN pembayaran_sppt p 
            ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2 
            AND k.kd_kecamatan = p.kd_kecamatan 
            AND k.kd_kelurahan = p.kd_kelurahan 
            AND k.kd_blok = p.kd_blok 
            AND k.no_urut = p.no_urut 
            AND k.kd_jns_op = p.kd_jns_op 
            AND k.thn_pajak_sppt = p.thn_pajak_sppt 
            LEFT JOIN tempat_pembayaran tp ON {$pos_join}
            $where $search
            GROUP BY 1,2,3
            ORDER BY 1,2,3 ";
        $sql_query_r .= "$sOrder $sLimit";
        
        $output = array(
            "sEcho" => $sEcho,
            // "iTotalRecords" => $iTotal,
            // "iTotalDisplayRecords" => $iFiltered,
            // "SQL Query" => $sql_query_r,
            "aaData" => array()
        );
        
        $qry      = $this->db->query($sql_query_r);
        $pg_pokok = 0;
        $pg_denda = 0;
        $pg_total = 0;
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($i == 0) {
                    $row[] = $aRow->$aColumns[$i] . ':' . namabulan($aRow->$aColumns[$i]);
                } else if ($i > 2) {
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                } else
                    $row[] = $aRow->$aColumns[$i];
            }
            $pg_pokok += $aRow->$aColumns[3];
            $pg_denda += $aRow->$aColumns[4];
            $pg_total += $aRow->$aColumns[5];
            
            $output['aaData'][] = $row;
        }
        /*
        $row                = array();
        $row[]              = '';
        $row[]              = 'TOTAL';
        $row[]              = number_format($pg_pokok, 0, ',', '.');
        $row[]              = number_format($pg_denda, 0, ',', '.');
        $row[]              = number_format($pg_total, 0, ',', '.');
        $output['aaData'][] = $row;
        */
        
        $output['pokok'] = number_format($pg_pokok, 0, ',', '.');
        $output['denda'] = number_format($pg_denda, 0, ',', '.');
        $output['total'] = number_format($pg_total, 0, ',', '.');
        
        echo json_encode($output);
    }
}
