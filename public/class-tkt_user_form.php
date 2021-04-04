<?php 

class Tkt_User_Form extends Tkt_Form{
	public function get_form(){
		ob_start();
		?>This is the User Form<?php
		return ob_get_clean();
	}	
}