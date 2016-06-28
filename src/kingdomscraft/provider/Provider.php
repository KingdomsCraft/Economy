<?php

/**
 * Provider.php Interface
 *
 * Created on 13/06/2016 at 5:57 PM
 *
 * @author Jack
 */

namespace kingdomscraft\provider;

interface Provider {

	public function init();
	
	public function loadAccount($name);
	
	public function updateAccount($info, $name);
	
	public function deleteAccount($name);

}