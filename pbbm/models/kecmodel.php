<?php
class kecModel extends CI_Model {

  var $tables ='ref_kecamatan';
  var $keys   ='username';
  
	function __construct()
	{
		parent::__construct();
	}

	function getRecord($kec='000')
	{
	  $sql="select * from ref_kecamatan 
 		      where kd_propinsi='".KD_PROPINSI."' and kd_dati2='".KD_DATI2."'";
 		if ($kec!='000')
 		    $sql.=" and kd_kecamatan='$kec'";
//    die($sql);
 		$qry=$this->db->query($sql);
  	return $qry->result();
	}
	
}
 