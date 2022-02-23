<?php
class User_model extends CI_Model {
	
	
	function addUserDetail($p1=null,$p2=null,$p3=null,$p4=null,$p5=null) {
		$data = array (
				'ip_address' => $p1,
				'username' => $p2,
				'password' => $p3,
				'email' => $p4,
				'mobile' => $p5,
				'active' => '1',
				
				'created_on' => date ( 'Y-m-d H:i:s' ),
				'created_at' => date ( 'Y-m-d H:i:s' )
	
		);
		$this->db->insert ( 'users', $data );
		return $this->db->insert_id ();
	}
	
	

	public function getSpecificUserDetail( $id, $flag=null ) {
		$this->db->select ( "fut.*" );
		$this->db->from ( "users fut" );
		if($flag == 1){
			$this->db->where ( 'fut.id', $id );
		}else if($flag == 2){
			$this->db->where ( 'fut.email', $id );
		}
		
		$result = $this->db->get ();
		$type = array ();
		foreach ( $result->result () as $row ) {
				
			$type ['id'] = $row->id;
			$type ['ip_address'] = $row->ip_address;
			$type ['username'] = $row->username;
			$type ['password'] = $row->password;
			$type ['email'] = $row->email;
			$type ['mobile'] = $row->mobile;
			$type ['last_login'] = $row->last_login;
				
		}
		return $type;
	}
	
	public function getSpecificUserAddressDetail( $addressId ) {
		$this->db->select ( "addr.*,are.name as area, cit.name as city" );
		$this->db->from ( "addresses addr" );
		$this->db->join ( 'areas are', 'are.id = addr.area_id', 'left' );
		$this->db->join ( 'cities cit', 'cit.id = addr.city_id', 'left' );
		
		$this->db->where ( 'addr.id', $addressId );
		
		$result = $this->db->get ();
		$type = array ();
		foreach ( $result->result () as $row ) {
	
			$type ['id'] = $row->id;
			$type ['name'] = $row->name;
			$type ['mobile'] = $row->mobile;
			$type ['address'] = $row->address;
			$type ['area'] = $row->area;
			$type ['city'] = $row->city;
			$type ['pincode'] = $row->pincode;
			$type ['state'] = $row->state;
			$type ['country'] = $row->country;
	
		}
		return $type;
	}
	
	
}