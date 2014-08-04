<?php
/* 
 * @file
 * @ingroup Skins
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}

$wgExtensionCredits['skin'][] = array (
	'path'				=> __FILE__,
	'name'				=> 'PCGW Vector',
	'descriptionmsg'	=> 'pcgwvector-desc',
	'author'			=> array( 'Stanisław Gackowski', 'Andrew Tsai'),
	'url'				=> "https://github.com/PCGamingWiki/pcgwvector",
	'license-name'		=> 'GPL-2.0+',
);

$wgValidSkinNames['pcgwvector'] = 'PCGWVector';

$wgHooks['GetPreferences'][] = 'wfPrefHook';

function wfPrefHook( $user, &$preferences ) {
	$preferences['pcgwvector-googleads'] = array(
		'type' => 'toggle',
		'label-message' => 'tog-googleads',
		'section' => 'rendering/skin',
	);
	$preferences['pcgwvector-sidebaripb'] = array(
		'type' => 'toggle',
		'label-message' => 'tog-sidebaripb',
		'section' => 'rendering/skin',
	);
	$preferences['pcgwvector-headerpaypal'] = array(
		'type' => 'toggle',
		'label-message' => 'tog-headerpaypal',
		'section' => 'rendering/skin',
	);
	$preferences['pcgwvector-headersocial'] = array(
		'type' => 'toggle',
		'label-message' => 'tog-headersocial',
		'section' => 'rendering/skin',
	);
	$preferences['pcgwvector-headerquotes'] = array(
		'type' => 'toggle',
		'label-message' => 'tog-headerquotes',
		'section' => 'rendering/skin',
	);

	return true;
}

$wgDefaultUserOptions['pcgwvector-googleads'] = 0;
$wgDefaultUserOptions['pcgwvector-sidebaripb'] = 1;
$wgDefaultUserOptions['pcgwvector-headerpaypal'] = 1;
$wgDefaultUserOptions['pcgwvector-headersocial'] = 1;
$wgDefaultUserOptions['pcgwvector-headerquotes'] = 1;

$wgAutoloadClasses['SkinPCGWVector'] = dirname(__FILE__).'/PCGWVector.skin.php';
$wgMessagesDirs['PCGWVector'] = __DIR__ . '/i18n';

$wgResourceModules['skins.pcgwvector'] = array(
	'styles' => array(
		'pcgwvector/css/screen.css' => array( 'media' => 'screen' ),
	),
	'remoteBasePath' => &$GLOBALS['wgStylePath'],
	'localBasePath' => &$GLOBALS['wgStyleDirectory'],
);
