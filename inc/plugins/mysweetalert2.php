<?php

/**
	* MySweetAlert2 Plugin
	* Author: Skryptec
	* Website: https://skryptec.net/
	* Copyright: © 2014 - 2019 Skryptec
	*
	* Replaces JQuery's jGrowl with SweetAlert2.
*/

if(!defined("IN_MYBB")) {
	die("Direct initialization of this file is not allowed.");
}

function mysweetalert2_info() {
	return [
		'name' 			=> 'MySweetAlert2',
		'description' 	=> 'Replaces JQuery\'s jGrowl with SweetAlert2.',
		'website' 		=> 'https://skryptec.net/',
		'author' 		=> 'Skryptec',
		'authorsite' 	=> 'https://skryptec.net/',
		'version' 		=> '1.0',
		'compatibility' => '18*',
		'codename'      => 'skryptec_myswal',
	];
}

function mysweetalert2_activate() {
	require_once MYBB_ROOT . '/inc/plugins/Skryptec/MySweetAlert2/class.functions.php';
	require_once MYBB_ROOT . '/inc/adminfunctions_templates.php';
	
	if($mySwalFunctions->getFiles('')) {
		$mySwalFunctions->createBackupForRevert();

		find_replace_templatesets(
			"headerinclude",
			"#" . preg_quote('{$stylesheets}') . "#i",
			'{$stylesheets}<script type="text/javascript" src="{$mybb->asset_url}/jscripts/sweetalert2.js"></script>'
		);
	}
}

function mysweetalert2_deactivate() {
	require_once MYBB_ROOT . '/inc/plugins/Skryptec/MySweetAlert2/class.functions.php';
	require_once MYBB_ROOT . '/inc/adminfunctions_templates.php';

	$mySwalFunctions->revertSwal();

	find_replace_templatesets(
		"headerinclude",
		"#" . preg_quote('<script type="text/javascript" src="{$mybb->asset_url}/jscripts/sweetalert2.js"></script>') . "#i",
		''
	);
}

function mysweetalert2_is_installed() {
	return file_exists('../jscripts/mysweetalert2_backup');
}
