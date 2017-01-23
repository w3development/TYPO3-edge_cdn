<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Edge CDN',
	'description' => 'EdgeCDN is a plugin that allows edge clients to clear their browser cache from the TYPO3 Backend.',
	'category' => 'plugin',
	'author' => 'Elvis Tavasja - w3development.net',
	'author_email' => 'support@w3development.net',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => '1',
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '0.0.1',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.0 - 6.2.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);