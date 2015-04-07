<?php namespace App\Algorithm;
use App\User;

class ProSave {
	
	static function user ( $user = array() ) {
		//user_id, email, password
		if ( isset($user['user_id']) ) {
			$user_obj = User::find( $user['user_id'] );
		} else {
			 $user_obj = new User;
		}
		
		if ( isset($user['email']) ) {
			$user_obj->email = $user['email'];
		}
		
		if ( isset($user['password']) ) {
			$user_obj->password = $user['password'];
		}
		$user_obj->save();
		return $user_obj;
	}
}