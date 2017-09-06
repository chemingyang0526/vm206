<?php
/**
 * @package htvcenter
 */
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
require_once "$RootDir/include/user.inc.php";
require_once "$RootDir/class/event.class.php";
require_once "$RootDir/class/resource.class.php";
require_once "$RootDir/class/image.class.php";
require_once "$RootDir/class/image_authentication.class.php";
require_once "$RootDir/class/storage.class.php";
require_once "$RootDir/class/deployment.class.php";
require_once "$RootDir/class/appliance.class.php";
require_once "$RootDir/class/htvcenter_server.class.php";
require_once "$RootDir/include/htvcenter-server-config.php";

/**
 * @package htvcenter
 * @author Matt Rechenburg <mattr_sf@users.sourceforge.net>
 * @version 1.0
 */

global $htvcenter_SERVER_BASE_DIR;
global $htvcenter_EXEC_PORT;
global $IMAGE_AUTHENTICATION_TABLE;
$htvcenter_server = new htvcenter_server();
$htvcenter_SERVER_IP_ADDRESS=$htvcenter_server->get_ip_address();
global $htvcenter_SERVER_IP_ADDRESS;
global $htvcenter_server;
$event = new event();
global $event;


	//--------------------------------------------------
	/**
	* authenticates the storage volume for the appliance resource
	* <code>
	* storage_auth_function("start", 2);
	* </code>
	* @access public
	*/
	//--------------------------------------------------
	function storage_auth_function($cmd, $appliance_id) {
		global $event;
		global $htvcenter_SERVER_BASE_DIR;
		global $htvcenter_SERVER_IP_ADDRESS;
		global $htvcenter_EXEC_PORT;
		global $IMAGE_AUTHENTICATION_TABLE;
		global $htvcenter_server;

		$appliance = new appliance();
		$appliance->get_instance_by_id($appliance_id);

		$image = new image();
		$image->get_instance_by_id($appliance->imageid);
		$image_name=$image->name;
		$image_rootdevice=$image->rootdevice;
		// parse the volume group info in the identifier
		$ident_separate=strpos($image_rootdevice, ":");
		$volume_group=substr($image_rootdevice, 0, $ident_separate);
		$root_device=substr($image_rootdevice, $ident_separate);
		$image_location=dirname($root_device);
		$image_location_name=basename($image_location);

		$storage = new storage();
		$storage->get_instance_by_id($image->storageid);
		$storage_resource = new resource();
		$storage_resource->get_instance_by_id($storage->resource_id);
		$storage_ip = $storage_resource->ip;

		$deployment = new deployment();
		$deployment->get_instance_by_type($image->type);
		$deployment_type = $deployment->type;
		$deployment_plugin_name = $deployment->storagetype;

		$resource = new resource();
		$resource->get_instance_by_id($appliance->resources);
		$resource_mac=$resource->mac;
		$resource_ip=$resource->ip;

		switch($cmd) {
			case "start":
				// authenticate the rootfs / needs htvcenter user + pass
				$htvcenter_admin_user = new user("htvcenter");
				$htvcenter_admin_user->set_user();

				// generate a password for the image
				$image_password = $image->generatePassword(12);
				$image_deployment_parameter = $image->deployment_parameter;
				$image->set_deployment_parameters("IMAGE_ISCSI_AUTH", $image_password);
				$event->log("storage_auth_function", $_SERVER['REQUEST_TIME'], 5, "htvcenter-lvm-iscsi-deployment-auth-hook.php", "Authenticating $image_name / $image_location_name to resource $resource_mac", "", "", 0, 0, $appliance_id);
				$auth_start_cmd = $htvcenter_SERVER_BASE_DIR."/htvcenter/plugins/".$deployment_plugin_name."/bin/htvcenter-".$deployment_plugin_name." auth -n ".$image_name." -r /dev/".$volume_group."/".$image_location_name." -i ".$image_password." -t lvm-iscsi-deployment -u ".$htvcenter_admin_user->name." -p ".$htvcenter_admin_user->password." --htvcenter-cmd-mode background";
				$resource->send_command($storage_ip, $auth_start_cmd);

				// authenticate the install-from-nfs export
				$run_disable_deployment_export=0;
				$install_from_nfs_param = trim($image->get_deployment_parameter("IMAGE_INSTALL_FROM_NFS"));
				if (strlen($install_from_nfs_param)) {

					// storage -> resource -> auth
					$ip_storage_id=$deployment->parse_deployment_parameter("id", $install_from_nfs_param);
					$ip_storage_ip=$deployment->parse_deployment_parameter("ip", $install_from_nfs_param);
					$ip_image_rootdevice=$deployment->parse_deployment_parameter("path", $install_from_nfs_param);

					$ip_storage = new storage();
					$ip_storage->get_instance_by_id($ip_storage_id);
					$ip_storage_resource = new resource();
					$ip_storage_resource->get_instance_by_id($ip_storage->resource_id);
					$op_storage_ip = $ip_storage_resource->ip;

					$ip_deployment = new deployment();
					$ip_deployment->get_instance_by_id($ip_storage->type);
					$ip_deployment_type = $ip_deployment->type;
					$ip_deployment_plugin_name = $ip_deployment->storagetype;

					$event->log("storage_auth_function", $_SERVER['REQUEST_TIME'], 5, "htvcenter-lvm-iscsi-deployment-auth-hook.php", "Install-from-NFS: Authenticating $resource_ip on storage id $ip_storage_id:$ip_storage_ip:$ip_image_rootdevice", "", "", 0, 0, $appliance_id);
					$auth_install_from_nfs_start_cmd = $htvcenter_SERVER_BASE_DIR."/htvcenter/plugins/".$ip_deployment_plugin_name."/bin/htvcenter-".$ip_deployment_plugin_name." auth -r ".$ip_image_rootdevice." -i ".$resource_ip." -t ".$ip_deployment_type." --htvcenter-cmd-mode background";
					$resource->send_command($ip_storage_ip, $auth_install_from_nfs_start_cmd);

					$run_disable_deployment_export=1;
				}

				// authenticate the transfer-to-nfs export
				$transfer_from_nfs_param = trim($image->get_deployment_parameter("IMAGE_TRANSFER_TO_NFS"));
				if (strlen($transfer_from_nfs_param)) {
					// storage -> resource -> auth
					$tp_storage_id=$deployment->parse_deployment_parameter("id", $transfer_from_nfs_param);
					$tp_storage_ip=$deployment->parse_deployment_parameter("ip", $transfer_from_nfs_param);
					$tp_image_rootdevice=$deployment->parse_deployment_parameter("path", $transfer_from_nfs_param);

					$tp_storage = new storage();
					$tp_storage->get_instance_by_id($tp_storage_id);
					$tp_storage_resource = new resource();
					$tp_storage_resource->get_instance_by_id($tp_storage->resource_id);
					$op_storage_ip = $tp_storage_resource->ip;

					$tp_deployment = new deployment();
					$tp_deployment->get_instance_by_id($tp_storage->type);
					$tp_deployment_type = $tp_deployment->type;
					$tp_deployment_plugin_name = $tp_deployment->storagetype;

					$event->log("storage_auth_function", $_SERVER['REQUEST_TIME'], 5, "htvcenter-lvm-iscsi-deployment-auth-hook.php", "Transfer-to-NFS: Authenticating $resource_ip on storage id $tp_storage_id:$tp_storage_ip:$tp_image_rootdevice", "", "", 0, 0, $appliance_id);
					$auth_install_from_nfs_start_cmd = $htvcenter_SERVER_BASE_DIR."/htvcenter/plugins/".$tp_deployment_plugin_name."/bin/htvcenter-".$tp_deployment_plugin_name." auth -r ".$tp_image_rootdevice." -i ".$resource_ip." -t ".$tp_deployment_type." --htvcenter-cmd-mode background";
					$resource->send_command($tp_storage_ip, $auth_install_from_nfs_start_cmd);

					$run_disable_deployment_export=1;
				}

				// do we need to disable the install-from/transfer-to-nfs exports ?
				if ($run_disable_deployment_export == 1) {
					$image_authentication = new image_authentication();
					$ia_id = (int)str_replace(".", "", str_pad(microtime(true), 15, "0"));
					$image_auth_ar = array(
						'ia_id' => $ia_id,
						'ia_image_id' => $appliance->imageid,
						'ia_resource_id' => $appliance->resources,
						'ia_auth_type' => 1,
					);
					$image_authentication->add($image_auth_ar);
					$event->log("storage_auth_function", $_SERVER['REQUEST_TIME'], 5, "htvcenter-lvm-iscsi-deployment-auth-hook.php", "Registered image $appliance->imageid for de-authentication the deployment exports when resource $appliance->resources is fully up.", "", "", 0, 0, $appliance_id);
				}

				break;

			case "stop":
				$image_authentication = new image_authentication();
				$ia_id = (int)str_replace(".", "", str_pad(microtime(true), 15, "0"));
				$image_auth_ar = array(
					'ia_id' => $ia_id,
					'ia_image_id' => $appliance->imageid,
					'ia_resource_id' => $appliance->resources,
					'ia_auth_type' => 0,
				);
				$image_authentication->add($image_auth_ar);
				$event->log("storage_auth_function", $_SERVER['REQUEST_TIME'], 5, "htvcenter-lvm-iscsi-deployment-auth-hook.php", "Registered image $appliance->imageid for de-authentication the root-fs exports when resource $appliance->resources is idle again.", "", "", 0, 0, $appliance_id);
				break;

		}

	}



	//--------------------------------------------------
	/**
	* de-authenticates the storage volume for the appliance resource
	* (runs via the image_authentication class)
	* <code>
	* storage_auth_stop(2);
	* </code>
	* @access public
	*/
	//--------------------------------------------------
	function storage_auth_stop($image_id) {

		global $event;
		global $htvcenter_SERVER_BASE_DIR;
		global $htvcenter_SERVER_IP_ADDRESS;
		global $htvcenter_EXEC_PORT;

		$image = new image();
		$image->get_instance_by_id($image_id);
		$image_name=$image->name;
		$image_rootdevice=$image->rootdevice;
		// generate a password for the image
		$image_password = $image->generatePassword(12);
		$image_deployment_parameter = $image->deployment_parameter;
		// parse the volume group info in the identifier
		$ident_separate=strpos($image_rootdevice, ":");
		$volume_group=substr($image_rootdevice, 0, $ident_separate);
		$root_device=substr($image_rootdevice, $ident_separate);
		$image_location=dirname($root_device);
		$image_location_name=basename($image_location);

		$storage = new storage();
		$storage->get_instance_by_id($image->storageid);
		$storage_resource = new resource();
		$storage_resource->get_instance_by_id($storage->resource_id);
		$storage_ip = $storage_resource->ip;

		$deployment = new deployment();
		$deployment->get_instance_by_type($image->type);
		$deployment_type = $deployment->type;
		$deployment_plugin_name = $deployment->storagetype;

		$auth_stop_cmd = $htvcenter_SERVER_BASE_DIR."/htvcenter/plugins/".$deployment_plugin_name."/bin/htvcenter-".$deployment_plugin_name." auth -r /dev/".$volume_group."/".$image_location_name." -i ".$image_password." -t lvm-iscsi-deployment --htvcenter-cmd-mode background";
		$resource = new resource();
		$resource->send_command($storage_ip, $auth_stop_cmd);
		// and update the image params
		$image->set_deployment_parameters("IMAGE_ISCSI_AUTH", $image_password);

	}





	//--------------------------------------------------
	/**
	* de-authenticates the storage deployment volumes for the appliance resource
	* (runs via the image_authentication class)
	* <code>
	* storage_auth_deployment_stop(2);
	* </code>
	* @access public
	*/
	//--------------------------------------------------
	function storage_auth_deployment_stop($image_id) {

		global $event;
		global $htvcenter_SERVER_BASE_DIR;
		global $htvcenter_SERVER_IP_ADDRESS;
		global $htvcenter_EXEC_PORT;

		$image = new image();
		$image->get_instance_by_id($image_id);
		$image_name=$image->name;
		$image_rootdevice=$image->rootdevice;

		$storage = new storage();
		$storage->get_instance_by_id($image->storageid);
		$storage_resource = new resource();
		$storage_resource->get_instance_by_id($storage->resource_id);
		$storage_ip = $storage_resource->ip;

		$deployment = new deployment();
		$deployment->get_instance_by_type($image->type);
		$deployment_type = $deployment->type;
		$deployment_plugin_name = $deployment->storagetype;

		// just for sending the commands
		$resource = new resource();

		// get install deployment params
		$install_from_nfs_param = trim($image->get_deployment_parameter("IMAGE_INSTALL_FROM_NFS"));
		if (strlen($install_from_nfs_param)) {
			// storage -> resource -> auth
			$ip_storage_id=$deployment->parse_deployment_parameter("id", $install_from_nfs_param);
			$ip_storage_ip=$deployment->parse_deployment_parameter("ip", $install_from_nfs_param);
			$ip_image_rootdevice=$deployment->parse_deployment_parameter("path", $install_from_nfs_param);

			$ip_storage = new storage();
			$ip_storage->get_instance_by_id($ip_storage_id);
			$ip_storage_resource = new resource();
			$ip_storage_resource->get_instance_by_id($ip_storage->resource_id);
			$op_storage_ip = $ip_storage_resource->ip;

			$ip_deployment = new deployment();
			$ip_deployment->get_instance_by_id($ip_storage->type);
			$ip_deployment_type = $ip_deployment->type;
			$ip_deployment_plugin_name = $ip_deployment->storagetype;

			$event->log("storage_auth_function", $_SERVER['REQUEST_TIME'], 5, "htvcenter-lvm-iscsi-deployment-auth-hook.php", "Install-from-NFS: Authenticating $resource_ip on storage id $ip_storage_id:$ip_storage_ip:$ip_image_rootdevice", "", "", 0, 0, $appliance_id);
			$auth_install_from_nfs_start_cmd = $htvcenter_SERVER_BASE_DIR."/htvcenter/plugins/".$ip_deployment_plugin_name."/bin/htvcenter-".$ip_deployment_plugin_name." auth -r ".$ip_image_rootdevice." -i ".$htvcenter_SERVER_IP_ADDRESS." -t ".$ip_deployment_type." --htvcenter-cmd-mode background";
			$resource->send_command($ip_storage_ip, $auth_install_from_nfs_start_cmd);
		}

		// get transfer deployment params
		$transfer_from_nfs_param = trim($image->get_deployment_parameter("IMAGE_TRANSFER_TO_NFS"));
		if (strlen($transfer_from_nfs_param)) {
			// storage -> resource -> auth
			$tp_storage_id=$deployment->parse_deployment_parameter("id", $transfer_from_nfs_param);
			$tp_storage_ip=$deployment->parse_deployment_parameter("ip", $transfer_from_nfs_param);
			$tp_image_rootdevice=$deployment->parse_deployment_parameter("path", $transfer_from_nfs_param);

			$tp_storage = new storage();
			$tp_storage->get_instance_by_id($tp_storage_id);
			$tp_storage_resource = new resource();
			$tp_storage_resource->get_instance_by_id($tp_storage->resource_id);
			$op_storage_ip = $tp_storage_resource->ip;

			$tp_deployment = new deployment();
			$tp_deployment->get_instance_by_id($tp_storage->type);
			$tp_deployment_type = $tp_deployment->type;
			$tp_deployment_plugin_name = $tp_deployment->storagetype;

			$event->log("storage_auth_function", $_SERVER['REQUEST_TIME'], 5, "htvcenter-lvm-iscsi-deployment-auth-hook.php", "Install-from-NFS: Authenticating $resource_ip on storage id $tp_storage_id:$tp_storage_ip:$tp_image_rootdevice", "", "", 0, 0, $appliance_id);
			$auth_install_from_nfs_start_cmd = $htvcenter_SERVER_BASE_DIR."/htvcenter/plugins/".$tp_deployment_plugin_name."/bin/htvcenter-".$tp_deployment_plugin_name." auth -r ".$tp_image_rootdevice." -i ".$htvcenter_SERVER_IP_ADDRESS." -t ".$tp_deployment_type." --htvcenter-cmd-mode background";
			$resource->send_command($tp_storage_ip, $auth_install_from_nfs_start_cmd);
		}

	}



?>


