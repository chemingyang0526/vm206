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


// error_reporting(E_ALL);

$RootDir = $_SERVER["DOCUMENT_ROOT"].'/htvcenter/base/';
require_once "$RootDir/include/htvcenter-database-functions.php";
require_once "$RootDir/include/user.inc.php";
require_once "$RootDir/include/htvcenter-server-config.php";
require_once "$RootDir/class/storage.class.php";
require_once "$RootDir/class/resource.class.php";
require_once "$RootDir/class/event.class.php";
require_once "$RootDir/class/htvcenter_server.class.php";
global $htvcenter_SERVER_BASE_DIR;

// global event for logging
$event = new event();
global $event;


function get_opsi_deployment_image_rootdevice_identifier($local_storage_storage_id) {
	global $htvcenter_SERVER_BASE_DIR;
	global $htvcenter_ADMIN;
	global $event;
	$rootdevice_identifier_array[] = array("value" => "sda", "label" => "Local SCSI Disk 1");
	return $rootdevice_identifier_array;
}

function get_opsi_deployment_image_default_rootfs() {
	return "local fileystem";
}

function get_opsi_deployment_rootfs_transfer_methods() {
	return false;
}

function get_opsi_deployment_rootfs_set_password_method() {
	return false;
}

function get_opsi_deployment_is_network_deployment() {
	return true;
}

function get_opsi_deployment_local_deployment_enabled() {
	return true;
}

?>


