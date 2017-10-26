<?php
header('Access-Control-Allow-Origin: *');

if (isset($_GET['label'])) {
	
	$filenamepath = '/etc/mfs/mfschunkserver.cfg';

	$file = file_get_contents($filenamepath);
	$matches = array();
	$pattern = '/^\s*(#)?\s*(LABEL =)+\s*(.*)?$/m';

	preg_match($pattern, $file, $matches);

	if (count($matches) > 0) {
		$newlabel = $_GET['label'];
		$replacement = '$2 '.$newlabel;
		$file = preg_replace($pattern, $replacement, $file);
		$write_success = file_put_contents($filenamepath, $file, LOCK_EX);
	} else {
		$write_success = file_put_contents($filenamepath, 'LABEL = '.$_GET['label'], FILE_APPEND | LOCK_EX);
	}

	if ($write_success) {
		$run_success = shell_exec('sudo /etc/init.d/lizardfs-chunkserver restart 2>&1');
		
		if ($run_success) {
			echo json_encode('server restarted');
		} else {
			echo json_encode('restart failed');
		}
	} else {
		echo json_encode('write failed');
	}
} else {
	echo json_encode("invalid label");
}
?>
