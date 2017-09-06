<?php
/*
    htvcenter Enterprise developed by htvcenter Enterprise GmbH.

    All source code and content (c) Copyright 2014, htvcenter Enterprise GmbH unless specifically noted otherwise.

    This source code is released under the htvcenter Enterprise Server and Client License, unless otherwise agreed with htvcenter Enterprise GmbH.
    The latest version of this license can be found here: http://htvcenter-enterprise.com/license

    By using this software, you acknowledge having read this license and agree to be bound thereby.

                http://htvcenter-enterprise.com

    Copyright 2014, htvcenter Enterprise GmbH <info@htvcenter-enterprise.com>
*/

function get_network_manager_appliance_edit($appliance_id, $htvcenter, $response) {
	$appliance = new appliance();
	$appliance->get_instance_by_id($appliance_id);
	$virtualization = new virtualization();
	$virtualization->get_instance_by_id($appliance->virtualization);
	// choose only not vm	
	if(stripos($virtualization->type, '-vm-') === false) {
		$plugin_title = "Network Manager on ".$appliance->name;
		$a = $response->html->a();
		$a->label = '<image height="24" width="24" alt="'.$plugin_title.'" title="'.$plugin_title.'" src="'.$htvcenter->get('baseurl').'/plugins/network-manager/img/plugin.png">';
		$a->href  = $htvcenter->get('baseurl').'/index.php?base=appliance&appliance_action=load_edit&aplugin=network-manager&appliance_id='.$appliance_id;
		return $a;
	}
}
?>

