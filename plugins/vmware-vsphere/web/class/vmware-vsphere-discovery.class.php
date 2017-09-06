<?php
/**
 * vSphere Host discovery
 *
    HyperTask Enterprise developed by HyperTask Enterprise GmbH.

    All source code and content (c) Copyright 2014, HyperTask Enterprise GmbH unless specifically noted otherwise.

    This source code is released under the HyperTask Enterprise Server and Client License, unless otherwise agreed with HyperTask Enterprise GmbH.
    The latest version of this license can be found here: http://htvcenter-enterprise.com/license

    By using this software, you acknowledge having read this license and agree to be bound thereby.

                http://htvcenter-enterprise.com

    Copyright 2014, HyperTask Enterprise GmbH <info@htvcenter-enterprise.com>
 */


// This class represents a cloud user in HyperTask

$RootDir = $_SERVER["DOCUMENT_ROOT"].'/htvcenter/base/';
require_once "$RootDir/include/htvcenter-database-functions.php";
require_once "$RootDir/class/event.class.php";

class vmware_vsphere_discovery {

	var $vmw_vsphere_ad_id = '';
	var $vmw_vsphere_ad_ip = '';
	var $vmw_vsphere_ad_mac = '';
	var $vmw_vsphere_ad_hostname = '';
	var $vmw_vsphere_ad_user = '';
	var $vmw_vsphere_ad_password = '';
	var $vmw_vsphere_ad_comment = '';
	var $vmw_vsphere_ad_is_integrated = '';


	//--------------------------------------------------
	/**
	* Constructor
	*/
	//--------------------------------------------------
	function __construct() {
		global $htvcenter_SERVER_BASE_DIR;
		$VMWARE_VSPHERE_DISCOVERY_TABLE="vmw_vsphere_auto_discovery";
		$this->event = new event();
		$this->_db_table = $VMWARE_VSPHERE_DISCOVERY_TABLE;
		$this->_base_dir = $htvcenter_SERVER_BASE_DIR;
	}

// ---------------------------------------------------------------------------------
// methods to create an instance of a vmware_vsphere_discovery object filled from the db
// ---------------------------------------------------------------------------------

	// returns an vmware_vsphere_discovery object from the db selected by id, mac or ip
	function get_instance($id, $mac, $ip) {

		$db=htvcenter_get_db_connection();
		if ("$id" != "") {
			$vmware_vsphere_discovery_array = $db->Execute("select * from $this->_db_table where vmw_vsphere_ad_id=$id");
		} else if ("$mac" != "") {
			$vmware_vsphere_discovery_array = $db->Execute("select * from $this->_db_table where vmw_vsphere_ad_mac='$mac'");
		} else if ("$ip" != "") {
			$vmware_vsphere_discovery_array = $db->Execute("select * from $this->_db_table where vmw_vsphere_ad_ip='$ip'");
		} else {
			$this->event->log("get_instance", $_SERVER['REQUEST_TIME'], 2, "vmware-vsphere-discovery.class.php", "Could not create instance of vmware_vsphere_discovery without data", "", "", 0, 0, 0);
			return;
		}
		foreach ($vmware_vsphere_discovery_array as $index => $vmware_vsphere_discovery) {
			$this->vmw_vsphere_ad_id = $vmware_vsphere_discovery["vmw_vsphere_ad_id"];
			$this->vmw_vsphere_ad_ip = $vmware_vsphere_discovery["vmw_vsphere_ad_ip"];
			$this->vmw_vsphere_ad_mac = $vmware_vsphere_discovery["vmw_vsphere_ad_mac"];
			$this->vmw_vsphere_ad_hostname = $vmware_vsphere_discovery["vmw_vsphere_ad_hostname"];
			$this->vmw_vsphere_ad_user = $vmware_vsphere_discovery["vmw_vsphere_ad_user"];
			$this->vmw_vsphere_ad_password = $vmware_vsphere_discovery["vmw_vsphere_ad_password"];
			$this->vmw_vsphere_ad_comment = $vmware_vsphere_discovery["vmw_vsphere_ad_comment"];
			$this->vmw_vsphere_ad_is_integrated = $vmware_vsphere_discovery["vmw_vsphere_ad_is_integrated"];
		}
		return $this;
	}


	// returns an appliance from the db selected by id
	function get_instance_by_id($id) {
		$this->get_instance($id, "", "");
		return $this;
	}

	// returns an appliance from the db selected by mac
	function get_instance_by_mac($mac) {
		$this->get_instance("", $mac, "");
		return $this;
	}

	// returns an appliance from the db selected by ip
	function get_instance_by_ip($ip) {
		$this->get_instance("", "", $ip);
		return $this;
	}


	// ---------------------------------------------------------------------------------
	// general vmware_vsphere_discovery methods
	// ---------------------------------------------------------------------------------




	// checks if given vmware_vsphere_discovery id is free in the db
	function is_id_free($vmware_vsphere_discovery_id) {

		$db=htvcenter_get_db_connection();
		$rs = $db->Execute("select vmw_vsphere_ad_id from $this->_db_table where vmw_vsphere_ad_id=$vmware_vsphere_discovery_id");
		if (!$rs)
			$this->event->log("is_id_free", $_SERVER['REQUEST_TIME'], 2, "vmware-vsphere-discovery.class.php", $db->ErrorMsg(), "", "", 0, 0, 0);
		else
		if ($rs->EOF) {
			return true;
		} else {
			return false;
		}
	}


	// checks if given vmware_vsphere_discovery mac is already in the db
	function mac_discoverd_already($vmware_vsphere_discovery_mac) {

		$db=htvcenter_get_db_connection();

		$rs = $db->Execute("select vmw_vsphere_ad_id from $this->_db_table where vmw_vsphere_ad_mac='$vmware_vsphere_discovery_mac'");
		if (!$rs)
			$this->event->log("mac_discoverd_already", $_SERVER['REQUEST_TIME'], 2, "vmware-vsphere-discovery.class.php", $db->ErrorMsg(), "", "", 0, 0, 0);
		else
		if ($rs->EOF) {
			$resource = new resource();
			$resource->get_instance_by_mac($vmware_vsphere_discovery_mac);
			if ($resource->id > 0) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	// checks if given vmware_vsphere_discovery ip is already in the db
	function ip_discoverd_already($vmware_vsphere_discovery_ip) {

		$db=htvcenter_get_db_connection();

		$rs = $db->Execute("select vmw_vsphere_ad_id from $this->_db_table where vmw_vsphere_ad_ip='$vmware_vsphere_discovery_ip'");
		if (!$rs)
			$this->event->log("ip_discoverd_already", $_SERVER['REQUEST_TIME'], 2, "vmware-vsphere-discovery.class.php", $db->ErrorMsg(), "", "", 0, 0, 0);
		else
		if ($rs->EOF) {
			$resource = new resource();
			$resource->get_instance_by_ip($vmware_vsphere_discovery_ip);
			if ($resource->id > 0) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}


	// adds vmware_vsphere_discovery to the database
	function add($vmware_vsphere_discovery_fields) {

		if (!is_array($vmware_vsphere_discovery_fields)) {
			$this->event->log("add", $_SERVER['REQUEST_TIME'], 2, "vmware-vsphere-discovery.class.php", "vmware_vsphere_discoverygroup_fields not well defined", "", "", 0, 0, 0);
			return 1;
		}
		if (!isset($vmware_vsphere_discovery_fields['vmw_vsphere_ad_id'])) {
			$vmware_vsphere_discovery_fields['vmw_vsphere_ad_id'] = (int)str_replace(".", "", str_pad(microtime(true), 15, "0"));
		}
		// set stop time and status to now
		$now=$_SERVER['REQUEST_TIME'];
		$db=htvcenter_get_db_connection();
		$result = $db->AutoExecute($this->_db_table, $vmware_vsphere_discovery_fields, 'INSERT');
		if (! $result) {
			$this->event->log("add", $_SERVER['REQUEST_TIME'], 2, "vmware-vsphere-discovery.class.php", "Failed adding new vmware_vsphere_discoverygroup to database", "", "", 0, 0, 0);
		}
	}


	// updates vmware_vsphere_discovery in the database
	function update($vmware_vsphere_discovery_id, $vmware_vsphere_discovery_fields) {

		if (!is_array($vmware_vsphere_discovery_fields)) {
			$this->event->log("update", $_SERVER['REQUEST_TIME'], 2, "vmware-vsphere-discovery.class.php", "Unable to update vmware_vsphere_discoverygroup $vmware_vsphere_discovery_id", "", "", 0, 0, 0);
			return 1;
		}
		$db=htvcenter_get_db_connection();
		unset($vmware_vsphere_discovery_fields["vmw_vsphere_ad_id"]);
		$result = $db->AutoExecute($this->_db_table, $vmware_vsphere_discovery_fields, 'UPDATE', "vmw_vsphere_ad_id = $vmware_vsphere_discovery_id");
		if (! $result) {
			$this->event->log("update", $_SERVER['REQUEST_TIME'], 2, "vmware-vsphere-discovery.class.php", "Failed updating vmware_vsphere_discoverygroup $vmware_vsphere_discovery_id", "", "", 0, 0, 0);
		}
	}


	// removes vmware_vsphere_discovery from the database
	function remove($vmware_vsphere_discovery_id) {
		$this->get_instance_by_id($vmware_vsphere_discovery_id);
		$db=htvcenter_get_db_connection();
		$rs = $db->Execute("delete from $this->_db_table where vmw_vsphere_ad_id=$vmware_vsphere_discovery_id");
	}

	// removes vmware_vsphere_discovery from the database by vmware_vsphere_discovery_mac
	function remove_by_name($vmware_vsphere_discovery_mac) {
		$db=htvcenter_get_db_connection();
		$rs = $db->Execute("delete from $this->_db_table where vmw_vsphere_ad_mac='$vmware_vsphere_discovery_mac'");
	}


	// returns the number of vmware_vsphere_discoverys for an vmware_vsphere_discovery type
	function get_count() {
		$count=0;
		$db=htvcenter_get_db_connection();
		$rs = $db->Execute("select count(vmw_vsphere_ad_mac) as num from $this->_db_table");
		if (!$rs) {
			print $db->ErrorMsg();
		} else {
			$count = $rs->fields["num"];
		}
		return $count;
	}



	// returns a list of all vmware_vsphere_discovery ids
	function get_all_ids() {

		$vmware_vsphere_discovery_list = array();
		$query = "select vmw_vsphere_ad_mac from $this->_db_table";
		$db=htvcenter_get_db_connection();
		$rs = $db->Execute($query);
		if (!$rs)
			$this->event->log("get_list", $_SERVER['REQUEST_TIME'], 2, "vmware-vsphere-discovery.class.php", $db->ErrorMsg(), "", "", 0, 0, 0);
		else
		while (!$rs->EOF) {
			$vmware_vsphere_discovery_list[] = $rs->fields;
			$rs->MoveNext();
		}
		return $vmware_vsphere_discovery_list;

	}



	// displays the vmware_vsphere_discovery-overview
	function display_overview($offset, $limit, $sort, $order) {

		$db=htvcenter_get_db_connection();
		$recordSet = $db->SelectLimit("select * from $this->_db_table order by $sort $order", $limit, $offset);
		$vmware_vsphere_discovery_array = array();
		if (!$recordSet) {
			$this->event->log("display_overview", $_SERVER['REQUEST_TIME'], 2, "vmware-vsphere-discovery.class.php", $db->ErrorMsg(), "", "", 0, 0, 0);
		} else {
			while (!$recordSet->EOF) {
				array_push($vmware_vsphere_discovery_array, $recordSet->fields);
				$recordSet->MoveNext();
			}
			$recordSet->Close();
		}
		return $vmware_vsphere_discovery_array;
	}









// ---------------------------------------------------------------------------------

}

?>
