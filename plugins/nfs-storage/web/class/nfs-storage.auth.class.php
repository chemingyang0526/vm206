<?php
/**
 * NFS-Storage Auth Volume(s)
 *
    htvcenter Enterprise developed by htvcenter Enterprise GmbH.

    All source code and content (c) Copyright 2014, htvcenter Enterprise GmbH unless specifically noted otherwise.

    This source code is released under the htvcenter Enterprise Server and Client License, unless otherwise agreed with htvcenter Enterprise GmbH.
    The latest version of this license can be found here: http://htvcenter-enterprise.com/license

    By using this software, you acknowledge having read this license and agree to be bound thereby.

                http://htvcenter-enterprise.com

    Copyright 2014, htvcenter Enterprise GmbH <info@htvcenter-enterprise.com>
 */

class nfs_storage_auth
{
/**
* name of action buttons
* @access public
* @var string
*/
var $actions_name = 'nfs_storage_action';
/**
* message param
* @access public
* @var string
*/
var $message_param = "nfs_storage_msg";
/**
* identifier name
* @access public
* @var string
*/
var $identifier_name = 'nfs_identifier';
/**
* htvcenter rootdir
* @access public
* @var string
*/
var $rootdir;
/**
* id for tabs
* @access public
* @var string
*/
var $prefix_tab = 'nfs_tab';
/**
* path to templates
* @access public
* @var string
*/
var $tpldir;
/**
* translation
* @access public
* @var array
*/
var $lang = array();

	//--------------------------------------------
	/**
	 * Constructor
	 *
	 * @access public
	 * @param htvcenter $htvcenter
	 * @param htmlobject_response $response
	 */
	//--------------------------------------------
	function __construct($htvcenter, $response) {
		$this->response   = $response;
		$this->htvcenter = $htvcenter;
		$this->user	    = $htvcenter->user();
		$this->file = $this->htvcenter->file();
		$this->volume = $this->response->html->request()->get('volume');
		$this->response->params['volume'] = $this->volume;
	}

	//--------------------------------------------
	/**
	 * Action
	 *
	 * @access public
	 * @return htmlobject_template
	 */
	//--------------------------------------------
	function action() {
		$response = $this->auth();
		if(isset($response->msg)) {
			$this->response->redirect(
				$this->response->get_url($this->actions_name, 'edit', $this->message_param, $response->msg)
			);
		}
		if(isset($response->error)) {
			$_REQUEST[$this->message_param] = $response->error;
		}
		$t = $this->response->html->template($this->tpldir.'/nfs-storage-auth.tpl.php');
		$t->add($this->response->html->thisfile, "thisfile");
		$t->add(sprintf($this->lang['label'], $this->volume), 'label');
		// explanation for auth
		$t->add($this->lang['auth_explanation'], 'auth_explanation');
		$t->add($response->form);
		$t->add($this->htvcenter->get('baseurl'), 'baseurl');
		$t->group_elements(array('param_' => 'form'));
		return $t;
	}

	//--------------------------------------------
	/**
	 * Auth
	 *
	 * @access public
	 * @return htmlobject_response
	 */
	//--------------------------------------------
	function auth() {
		$response = $this->get_response();
		$export   = $response->html->request()->get('volume');
		$form     = $response->form;
		if( $export !== '' ) {
			if(!$form->get_errors() && $response->submit()) {
				// set ENV
				$storage_id = $this->response->html->request()->get('storage_id');
				$storage    = new storage();
				$resource   = new resource();
				$storage->get_instance_by_id($storage_id);
				$resource->get_instance_by_id($storage->resource_id);

				$errors  = array();
				$message = array();
				$auths   = $form->get_request('ip');
				$statfile = $this->htvcenter->get('basedir').'/plugins/nfs-storage/web/storage/'.$resource->id.'.nfs.stat';

				$error = '';
				$command  = $this->htvcenter->get('basedir').'/plugins/nfs-storage/bin/htvcenter-nfs-storage auth';
				$command .= ' -n '.$export.' -i '.$auths;
				$command .= ' -u '.$this->htvcenter->admin()->name.' -p '.$this->htvcenter->admin()->password;
				$command .= ' --htvcenter-ui-user '.$this->user->name;
				$command .= ' --htvcenter-cmd-mode background';
				if($this->file->exists($statfile)) {
					$this->file->remove($statfile);
				}
				$resource->send_command($resource->ip, $command);
				while (!$this->file->exists($statfile)) {
	  				usleep(10000); // sleep 10ms to unload the CPU
	  				clearstatcache();
				}
				$message[] = sprintf($this->lang['msg_authd'], $export);
				if(count($errors) === 0) {
					$response->msg = join('<br>', $message);
				} else {
					$msg = array_merge($errors, $message);
					$response->error = join('<br>', $msg);
				}
			}
		} else {
			$response->msg = '';
		}
		return $response;
	}

	//--------------------------------------------
	/**
	 * Get Response
	 *
	 * @access public
	 * @param string $mode
	 * @return htmlobject_response
	 */
	//--------------------------------------------
	function get_response() {
		$response = $this->response;
		$form = $response->get_form($this->actions_name, 'auth');

		$submit = $form->get_elements('submit');
		$submit->handler = 'onclick="wait();"';
		$form->add($submit, 'submit');

		$submit = $form->get_elements('cancel');
		$submit->handler = 'onclick="cancel();"';
		$form->add($submit, 'cancel');

		$regex_ip  = '~^([1-9]|[0-9][0-9]|[1][0-9][0-9]|[2][0-5][0-4])';
		$regex_ip .= '\\.([0-9]|[0-9][0-9]|[1][0-9][0-9]|[2][0-5][0-5])';
		$regex_ip .= '\\.([0-9]|[0-9][0-9]|[1][0-9][0-9]|[2][0-5][0-5])';
		$regex_ip .= '\\.([1-9]|[0-9][0-9]|[1][0-9][0-9]|[2][0-5][0-4])+$~i';
	
		$d['ip']['label']                         = $this->lang['form_ip'];
		$d['ip']['required']                      = true;
		$d['ip']['validate']['regex']             = '/^[a-z0-9. *_-]+$/i';
		$d['ip']['validate']['errormsg']          = $this->lang['error_ip'];
		$d['ip']['object']['type']                = 'htmlobject_input';
		$d['ip']['object']['attrib']['name']      = 'ip';
		$d['ip']['object']['attrib']['type']      = 'text';
		$d['ip']['object']['attrib']['value']     = '';
		$d['ip']['object']['attrib']['maxlength'] = 50;

		$form->add($d);
		$response->form = $form;
		return $response;
	}



}
?>
