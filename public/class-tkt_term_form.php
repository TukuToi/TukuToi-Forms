<?php

class Tkt_Term_Form extends Tkt_Form{

	public function get_form(){
		ob_start();
		?>This is the Term Form<?php
		return ob_get_clean();
	}
	
}
