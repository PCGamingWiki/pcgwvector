<?php
/*
 * Largely based on Vector.
 * Made to fit the needs of PCGamingWiki
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup Skins
 */

class SkinPCGWVector extends SkinVector {

	protected static $bodyClasses = array( 'vector-animateLayout' );

	var $skinname = 'pcgwvector', $stylename = 'pcgwvector',
		$template = 'PCGWVectorTemplate', $useheadelement = true;

	/**
	 * Initializes output page and sets up skin-specific parameters
	 * @param $out OutputPage object to initialize
	 */
	public function initPage( OutputPage $out ) {
		global $wgLocalStylePath;

		parent::initPage( $out );

		// Append CSS which includes IE only behavior fixes for hover support -
		// this is better than including this in a CSS file since it doesn't
		// wait for the CSS file to load before fetching the HTC file.
		$min = $this->getRequest()->getFuzzyBool( 'debug' ) ? '' : '.min';
		$out->addHeadItem( 'csshover',
			'<!--[if lt IE 7]><style type="text/css">body{behavior:url("' .
				htmlspecialchars( $wgLocalStylePath ) .
				"/vector/csshover{$min}.htc\")}</style><![endif]-->"
		);

		$out->addModules( array( 'skins.vector.js', 'skins.vector.collapsibleNav' ) );
	}

	/**
	 * Loads skin and user CSS files.
	 * @param $out OutputPage object
	 */
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );

		$styles = array( 'mediawiki.skinning.interface', 'skins.vector.styles' );
		wfRunHooks( 'SkinVectorStyleModules', array( $this, &$styles ) );
		$out->addModuleStyles( $styles );
	}

	/**
	 * Adds classes to the body element.
	 *
	 * @param $out OutputPage object
	 * @param &$bodyAttrs Array of attributes that will be set on the body element
	 */
	function addToBodyAttributes( $out, &$bodyAttrs ) {
		if ( isset( $bodyAttrs['class'] ) && strlen( $bodyAttrs['class'] ) > 0 ) {
			$bodyAttrs['class'] .= ' ' . implode( ' ', static::$bodyClasses );
		} else {
			$bodyAttrs['class'] = implode( ' ', static::$bodyClasses );
		}
	}
}

class PCGWVectorTemplate extends BaseTemplate {

	/* Functions */

	/**
	 * Outputs the entire contents of the (X)HTML page
	 */
	public function execute() {
		global $wgVectorUseIconWatch;

		// Build additional attributes for navigation urls
		$nav = $this->data['content_navigation'];

		if ( $wgVectorUseIconWatch ) {
			$mode = $this->getSkin()->getUser()->isWatched( $this->getSkin()->getRelevantTitle() ) ? 'unwatch' : 'watch';
			if ( isset( $nav['actions'][$mode] ) ) {
				$nav['views'][$mode] = $nav['actions'][$mode];
				$nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
				$nav['views'][$mode]['primary'] = true;
				unset( $nav['actions'][$mode] );
			}
		}

		$xmlID = '';
		foreach ( $nav as $section => $links ) {
			foreach ( $links as $key => $link ) {
				if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
					$link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
				}

				$xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
				$nav[$section][$key]['attributes'] =
					' id="' . Sanitizer::escapeId( $xmlID ) . '"';
				if ( $link['class'] ) {
					$nav[$section][$key]['attributes'] .=
						' class="' . htmlspecialchars( $link['class'] ) . '"';
					unset( $nav[$section][$key]['class'] );
				}
				if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
					$nav[$section][$key]['key'] =
						Linker::tooltip( $xmlID );
				} else {
					$nav[$section][$key]['key'] =
						Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
				}
			}
		}
		$this->data['namespace_urls'] = $nav['namespaces'];
		$this->data['view_urls'] = $nav['views'];
		$this->data['action_urls'] = $nav['actions'];
		$this->data['variant_urls'] = $nav['variants'];

		// Reverse horizontally rendered navigation elements
		if ( $this->data['rtl'] ) {
			$this->data['view_urls'] =
				array_reverse( $this->data['view_urls'] );
			$this->data['namespace_urls'] =
				array_reverse( $this->data['namespace_urls'] );
			$this->data['personal_urls'] =
				array_reverse( $this->data['personal_urls'] );
		}

		$user = $this->getSkin()->getUser();
		$toggleGoogleAds = $user->getOption( 'pcgwvector-googleads' );
		$toggleIPBSidebar = $user->getOption( 'pcgwvector-sidebaripb' );
		$togglePPWidget = $user->getOption( 'pcgwvector-headerpaypal' );
		$toggleSocialWidgets = $user->getOption( 'pcgwvector-headersocial' );
		$toggleQuotations = $user->getOption( 'pcgwvector-headerquotes' );

		// Output HTML Page
		$this->html( 'headelement' );
?>
			<!-- custom CSS -->
			<style>
				body{ 
					background: #E5ECF9;
				}

				/* Header */
				#header_area{
					position: absolute;
					top: 2.25em;
				}
				
				#wrapper{
					margin: 0 auto;
					margin-bottom: 80px;
				}
				
				.main_width{
					width: 94% !important;
					min-width: 960px;
					margin: 0 auto;
				}

				#content_box{
					width: 94% !important;
					min-width: 960px;
					margin: 0 auto;
					position: relative;
					padding: 0 10px;
				}
				
				#mw-page-base{
					background: none;
				}

				#login_bar{
					background: #323232; 
					width: 100%;
					height: 2.25em;
					position: absolute;
					top: 0;
					padding: 0;
				}

				/* Log in  */
				#p-personal{
					position: inherit;
					float: right;
					margin-top: 0.3em;
				}

				#p-personal li a, #p-personal li a:hover{
					color: white;
				}

				li#pt-openidlogin{
					display: none;
					background: none;
					padding-left: 0;
					margin-left: 3px;
					text-transform: none;
					color: white;
				}

				/* Content height */
				#mw-head-base{
					margin-top: 6em;
					height: 2.5em;
				}
				
				div#mw-head, div#mw-panel{
					top: 0;
				}
				
				#left-navigation, #right-navigation{
					margin-top: 0;
				}
				
				#left-navigation{
					margin-left: 11.65em;
				}
				
				div#content{
					padding: 0.8em 0 0.8em 0.75em;
					margin-left: 11.02em;
				}
				
				#p-search{
					margin-right: 0.7em
				}

				/* Content box */
				#content_box{ 
					-webkit-box-shadow: 0 5px 9px rgba(0,0,0,0.1);
					-moz-box-shadow: 0 5px 9px rgba(0,0,0,0.1);
					box-shadow: 0 5px 9px rgba(0,0,0,0.1);
					-moz-border-radius: 3px;
					border-radius: 3px;
					background: white;
				}

				/* Navigation */
				#primary_nav{
					width: 100%;
					height: 1.9em;
					position: absolute;
					top: 9.2em;
					left: 0em;
				}
				
				#community_app_menu{
					position: absolute;
					font-size: 13px;
					min-width: 400px;
					border-bottom: 1px solid white;
				}
				
				ul#community_app_menu.ipsList_inline{
					line-height: 1.5em;
					margin: 0;
					padding: 0;
					list-style-image: none;
					list-style-type: none;
				}
				
				#community_app_menu>li{
					margin: 0px 3px 0 0;
					position: relative;
					float: left;
				}
				
				#community_app_menu>li>a{
					color: #C5D5E2;
					background: #1C3B5F;
					display: block;
					padding: 5px 15px 6px;
					text-shadow: 0px 1px 1px rgba(0,0,0,0.5);
					border-top-left-radius: 2px;
					border-top-right-radius: 2px;
				}
				
				#community_app_menu>li>a:hover, #community_app_menu>li>a.menu_active{
					text-decoration: none;
					background: #173455;
					color: white;
				}

				/* Sidebar */
				div#mw-panel{
					padding-top: 0.75em;
					width: 11.6em;
					padding-left: 0;
				}
				
				div#mw-panel div.portal div.body ul li{
					line-height: 0.8em;
				}
				
				.sidebar_header{
					color: #4D4D4D;
					font-size: 12px;
					margin-left: 10px;
					margin-top: 10px;
				}

				/* Print removal */
				#t-print{
					display:none
				}

			</style>
			<!-- /custom CSS -->
			
			<?php if($toggleIPBSidebar == true) { ?>
			<!-- IPB stuff -->
			<script type='text/javascript'>var _ccsjQ = jQuery;</script>
			<link rel='stylesheet' media='screen' type='text/css' href='http://community.pcgamingwiki.com/public/ipc_blocks/compiled.css' />
			<link rel='stylesheet' media='screen' type='text/css' href='http://community.pcgamingwiki.com/public/style_css/css_1/ipb_common.css' />
			<!-- /IPB stuff -->
			<?php } ?>
		
	<!-- custom header -->
	<div style="background: #323232; width: 100%; padding:0; height:2.25em; position:absolute; top:0">
		<div class="main_width">
			<?php $this->renderNavigation( 'PERSONAL' ); ?>
		</div>
	</div>
	
	<div style="position:absolute; top:2.25em; width:100%">
		<div class="main_width" style="margin:5px auto">
			<a href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>"><img src="http://pcgamingwiki.com/images/BigWikiLogo.png" width="160" height="100" alt="Logo"></a>
			
			<div class='ipsList_inline right' style='margin-top:20px; margin-right:; float:right'>
				<a href='http://twitter.com/PCGamingWiki' title='@PCGamingWiki'><img src='http://pcgamingwiki.com/images/1/1e/Header_Twitter_icon.svg' alt='Twitter' style="height:32px; width:32px" /></a>
				<a href='https://plus.google.com/+PCGamingWiki' title='+PCGamingWiki'><img src='http://pcgamingwiki.com/images/0/0d/Header_Google+_icon.svg' alt='Google+' style="height:32px; width:32px" /></a>
				<a href='https://www.facebook.com/PCGamingWiki' title='Facebook'><img src='http://pcgamingwiki.com/images/3/37/Header_Facebook_icon.svg' alt='Facebook' style="height:32px; width:32px" /></a>
				<a href='http://steamcommunity.com/groups/pcgamingwiki' title='Steam'><img src='http://pcgamingwiki.com/images/8/84/Header_Steam_icon.svg' alt='Steam' style="height:32px; width:32px" /></a>
			</div>
			<?php if($toggleSocialWidgets == true) { ?>
			<div class='ipsList_inline right' style='margin-top:20px; margin-right:30px; float:right; width:100px'>
				<!-- AddThis Button BEGIN -->
				<a class="addthis_button_tweet" addthis:url="http://pcgamingwiki.com/wiki/Home" tw:via="PCGamingWiki"></a>
				<div style="margin-top:3px"><a class="addthis_button_facebook_like" fb:like:href="https://www.facebook.com/PCGamingWiki"></a></div>
				<script type="text/javascript">var addthis_config = {"data_track_addressbar":false};</script>
				<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-508ad2cd70837a5f"></script>
				<!-- AddThis Button END -->
			</div>
			<?php }
			if($togglePPWidget == true) { ?>
			<div class='ipsList_inline right' style='margin-top:18px; margin-right:44px; float:right'>
				<div style="font-size:10px; color:#5a5a5a; margin-bottom:-7px">
					Found us useful? Help by <a href="http://pcgamingwiki.com/wiki/PCGamingWiki:Donate">donating</a>
				</div>
				<center><form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="N624F8XPU7MT2">
				<table style="width:136px">
				<tr><td><input type="hidden" name="on0" value=""></td></tr><tr><td><select name="os0" style="font-size:10px">
					<option value="Donate1">$1.00 USD - monthly</option>
					<option value="Donate2">$2.00 USD - monthly</option>
					<option value="Donate5">$5.00 USD - monthly</option>
					<option value="Donate10">$10.00 USD - monthly</option>
					<option value="Donate15">$15.00 USD - monthly</option>
					<option value="Donate20">$20.00 USD - monthly</option>
					<option value="Donate25">$25.00 USD - monthly</option>
					<option value="Donate50">$50.00 USD - monthly</option>
					<option value="Donate100">$100.00 USD - monthly</option>
				</select> </td></tr>
				</table>
				<input type="hidden" name="currency_code" value="USD"></center>
				<center><div style="margin-top:-3px"><input style="width:60px" type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal � The safer, easier way to pay online.">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1"></center>
				</form></div>
			</div>
			<?php } 
			if($toggleQuotations == true) { ?>
			<div style="position: relative; height: 0px; margin: 5px auto;" class="main_width">
				<div style="margin-right: 5px; text-align: right;">
					<span style="font-size: 16px; text-align: right; text-transform: uppercase; font-family: Courier, serif; font-weight: 400;">
						<script language="JavaScript">
							var Quotation=new Array() 
							Quotation[0]  = "PC gaming since 2012";
							Quotation[1]  = "Welcome to PCGamingWiki, it's safer here";
							Quotation[2]  = "To be this good will take Sega ages";
							Quotation[3]  = "PCGamingWiki does what Nintendon't";
							Quotation[4]  = "The right file in the wrong place can make all the difference in the world";
							Quotation[5]  = "I'm Commander Shepard, and this is my favorite wiki on the citadel";
							Quotation[6]  = "When life gives you lemons, post games fixes";
							Quotation[7]  = "You must construct additional game fixes";
							Quotation[8]  = "Look buddy, I'm an Engineer - that means I solve practical problems";
							Quotation[9]  = "PC gaming, up to 6 billion players";
							Quotation[10] = "Now you're playing with DESKTOP power";
							Quotation[11] = "Our game fixes are Super Effective!";
							Quotation[12] = "Now you're thinking with game fixes";
							Quotation[13] = "...Reticulating Splines...";
							Quotation[14] = "Hey cousin! Let's go play some PC Games";
							Quotation[15] = "It's dangerous to go alone, take this";
							Quotation[16] = "If you look anywhere else for game fixes, you're gonna have a bad time";
							Quotation[17] = "Broken PC games? 'Aint nobody got time for that";
							Quotation[18] = "PCGamingWiki, it's what PCs crave";
							Quotation[19] = "Performing game calibrations";
							Quotation[20] = "Yeah, we're still waiting for Half-Life 3 too";
							Quotation[21] = "First you will fix your game, then there will be cake";
							Quotation[22] = "Beep.  Beep.  Beep.  Beep.";
							Quotation[23] = "Have you tried turning it off and on again?";
							Quotation[24] = "It costs 400,000 dollars to power this wiki for 12 seconds";
							Quotation[25] = "The Citizen Kane of video game wikis";
							var Q = Quotation.length;
							var whichQuotation=Math.round(Math.random()*(Q-1));
							function showQuotation(){document.write(Quotation[whichQuotation]);}
							showQuotation();
						</script>
					</span>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
	
	<div style="clear:both"></div>
	
	<div id="primary_nav" class="clearfix">
		<div class="main_width">			
			
			<ul class="ipsList_inline" id="community_app_menu">
				<li class="left"><a href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>" style="background:#FFF; color:#0b5794; font-weight:bold; text-shadow:none">Wiki</a></li>
				<li class="left"><a href="http://community.pcgamingwiki.com/blog">Blog</a></li>
				<li class="left"><a href="http://community.pcgamingwiki.com/index">Forums</a></li>
				<li class="left"><a href="http://community.pcgamingwiki.com/files">Files</a></li>
				<li class="left"><a href="http://community.pcgamingwiki.com/gallery">Gallery</a></li>
				<li class="left"><a href="http://community.pcgamingwiki.com/page/irc">IRC</a></li>
			</ul>
		</div>
	</div>
	<!-- /custom header -->

	<!-- DoubleClick code -->
	<script type='text/javascript'>
	var googletag = googletag || {};
	googletag.cmd = googletag.cmd || [];
	(function() {
	var gads = document.createElement('script');
	gads.async = true;
	gads.type = 'text/javascript';
	var useSSL = 'https:' == document.location.protocol;
	gads.src = (useSSL ? 'https:' : 'http:') + 
	'//www.googletagservices.com/tag/js/gpt.js';
	var node = document.getElementsByTagName('script')[0];
	node.parentNode.insertBefore(gads, node);
	})();
	</script>

	<script type='text/javascript'>
	googletag.cmd.push(function() {
	googletag.defineSlot('/6928793/PCGW_leader1', [728, 90], 'div-gpt-ad-1382563418767-0').addService(googletag.pubads());
	googletag.defineSlot('/6928793/PCGW_leader2', [728, 90], 'div-gpt-ad-1382563418767-1').addService(googletag.pubads());
	googletag.defineSlot('/6928793/PCGW_MPU', [300, 250], 'div-gpt-ad-1382563418767-2').addService(googletag.pubads());
	googletag.pubads().enableSingleRequest();
	googletag.enableServices();
	});
	</script>
	<!-- /DoubleClick code -->

	<div id="wrapper"> <!-- wrapper -->
		<div id="mw-page-base" class="noprint"></div>  
	<div id="content_box">
		<div id="mw-head-base" class="noprint"></div>
		<div id="content" class="mw-body" role="main">
			<a id="top"></a>
			<div id="mw-js-message" style="display:none;"<?php $this->html( 'userlangattributes' ) ?>></div>
			<?php if ( $this->data['sitenotice'] ) { ?>
			<div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div>
			<?php } ?>
			<h1 id="firstHeading" class="firstHeading" lang="<?php
				$this->data['pageLanguage'] = $this->getSkin()->getTitle()->getPageViewLanguage()->getHtmlCode();
				$this->text( 'pageLanguage' );
			?>"><span dir="auto"><?php $this->html( 'title' ) ?></span></h1>
			<?php $this->html( 'prebodyhtml' ) ?>
			<div id="bodyContent">
				<?php if ( $this->data['isarticle'] ) { ?>
				<div id="siteSub"><?php $this->msg( 'tagline' ) ?></div>
				<?php } ?>
				<div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
				<?php if ( $this->data['undelete'] ) { ?>
				<div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
				<?php } ?>
				<?php if ( $this->data['newtalk'] ) { ?>
				<div class="usermessage"><?php $this->html( 'newtalk' ) ?></div>
				<?php } ?>
				<div id="jump-to-nav" class="mw-jump">
					<?php $this->msg( 'jumpto' ) ?>
					<a href="#mw-navigation"><?php $this->msg( 'jumptonavigation' ) ?></a><?php $this->msg( 'comma-separator' ) ?>
					<a href="#p-search"><?php $this->msg( 'jumptosearch' ) ?></a>
				</div>
				<!-- ad horizontal banner -->
				<?php if(!$this->data['loggedin'] || $toggleGoogleAds == true) { ?>
					<div style="height:90px; width:728px; margin-left:auto; margin-right:auto; margin-top:0px; margin-bottom:16px; background-image: url(http://pcgamingwiki.com/images/8/86/Message.gif); background-repeat:no-repeat">
						<div>

						<!-- ad google -->
							<script type="text/javascript"><!--
							google_ad_client = "ca-pub-0027458528988311";
							/* PCGamingWiki banner */
							google_ad_slot = "2657560885";
							google_ad_width = 728;
							google_ad_height = 90;
							//-->
							</script>
							
						<!-- PCGW_leader1 -->
							<div id='div-gpt-ad-1382563418767-0' style='width:728px; height:90px;'>
							<script type='text/javascript'>
							googletag.cmd.push(function() { googletag.display('div-gpt-ad-1382563418767-0'); });
							</script>
							</div>
							
						</div>
					</div>
				<?php } ?>
				<!-- /ad horizontal banner -->
				<!-- ad mpu -->
				<?php if(!$this->data['loggedin']) { ?>
					<div id="mpu" style="width:300px; height:250px; float:right; margin:0 0 7px 7px;">
						<!-- PCGW_MPU -->
						<div id='div-gpt-ad-1382563418767-2' style='width:300px; height:250px;'>
						<script type='text/javascript'>
						googletag.cmd.push(function() { googletag.display('div-gpt-ad-1382563418767-2'); });
						</script>
						</div>
					</div>
				<?php } ?>
				<!-- /ad mpu -->
				<?php $this->html( 'bodycontent' ) ?>
				<?php if ( $this->data['printfooter'] ) { ?>
				<div class="printfooter">
				<?php $this->html( 'printfooter' ); ?>
				</div>
				<?php } ?>
				<?php if ( $this->data['catlinks'] ) { ?>
				<?php $this->html( 'catlinks' ); ?>
				<?php } ?>
				<!-- ad footer banner -->
				<?php if(!$this->data['loggedin'] || $toggleGoogleAds == true) { ?>			
					<div style="width: 728px; height:90px; margin:0 auto 0 auto; padding:14px 0 0 0; clear:both">
						<!-- PCGW_leader2 -->
						<div id='div-gpt-ad-1382563418767-1' style='width:728px; height:90px;'>
						<script type='text/javascript'>
						googletag.cmd.push(function() { googletag.display('div-gpt-ad-1382563418767-1'); });
						</script>
						</div>
					</div>
				<?php } ?>
				<!-- /ad footer banner -->
				<?php if ( $this->data['dataAfterContent'] ) { ?>
				<?php $this->html( 'dataAfterContent' ); ?>
				<?php } ?>
				<div class="visualClear"></div>
				<?php $this->html( 'debughtml' ); ?>
			</div>
		</div>
		<div id="mw-navigation">
			<h2><?php $this->msg( 'navigation-heading' ) ?></h2>
			<div id="mw-head">
				<div id="left-navigation">
					<?php $this->renderNavigation( array( 'NAMESPACES', 'VARIANTS' ) ); ?>
				</div>
				<div id="right-navigation">
					<?php $this->renderNavigation( array( 'VIEWS', 'ACTIONS', 'SEARCH' ) ); ?>
				</div>
			</div>
			<div id="mw-panel">
				<?php $this->renderPortals( $this->data['sidebar'] ); ?>
				<?php if($toggleIPBSidebar == true) { ?>
				<!-- IPB feeds -->
					<!-- Wiki News -->
					<div class="sidebar_header">News</div>
					<div class="sidebar_feed">
						<script type='text/javascript' src='http://community.pcgamingwiki.com/external.php?id=54&amp;k=2762865910875e1720e6fb0d32e5215a&amp;method=div' id='block-2762865910875e1720e6fb0d32e5215a'></script>
					</div>
					<!-- Wiki Replies -->
					<div class="sidebar_header">Replies</div>
					<div class="sidebar_feed" style="min-height:30px; margin-bottom:16px">
						<script type='text/javascript' src='http://community.pcgamingwiki.com/external.php?id=52&amp;k=c320a2c3ddaeda32ffc28f023d51b84d&amp;method=div' id='block-c320a2c3ddaeda32ffc28f023d51b84d'></script>
					</div>
					<!-- Wiki Giveaways -->
					<div class="sidebar_header">Giveaways</div>
					<div class="sidebar_feed" style="min-height:30px">
						<script type='text/javascript' src='http://community.pcgamingwiki.com/external.php?id=55&amp;k=eb5dcba8844f1b25d6c89550a931b2f0&amp;method=div' id='block-eb5dcba8844f1b25d6c89550a931b2f0'></script>
					</div>
					<!-- Wiki Assignments -->
					<div class="sidebar_header">Review codes</div>
					<div class="sidebar_feed" style="min-height:30px">
						<script type='text/javascript' src='http://community.pcgamingwiki.com/external.php?id=56&amp;k=becd540a09e5c075c9e36adcafbc8bc0&amp;method=div' id='block-becd540a09e5c075c9e36adcafbc8bc0'></script>
					</div>
				<!-- /IPB feeds -->
				<?php } ?>
				<!-- ad sidebar -->
				<?php if(!$this->data['loggedin'] || $toggleGoogleAds == true) { ?>				
					<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIwAAAABCAMAAAA7MLYKAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAEtQTFRF29vb2tra4ODg6urq5OTk4uLi6+vr7e3t7Ozs8PDw5+fn4+Pj4eHh3d3d39/f6Ojo5eXl6enp8fHx8/Pz8vLy7+/v3Nzc2dnZ2NjYnErj7QAAAD1JREFUeNq0wQUBACAMALDj7hf6JyUFGxzEnYhC9GaNPG1xVffGDErk/iCigLl1XV2xM49lfAxEaSM+AQYA9HMKuv4liFQAAAAASUVORK5CYII=" />
					<div style="margin-top:8px; margin-left:0px; padding-bottom:3px">
						<!-- ad google -->
							<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
							<!-- PCGamingWiki wide skyscraper link -->
							<ins class="adsbygoogle"
								 style="display:inline-block;width:160px;height:90px"
								 data-ad-client="ca-pub-0027458528988311"
								 data-ad-slot="8567157073"></ins>
							<script>
							(adsbygoogle = window.adsbygoogle || []).push({});
							</script>
					</div>
				<?php } ?>
				<!-- /ad sidebar -->
			</div>
		</div>
		<div id="footer" role="contentinfo"<?php $this->html( 'userlangattributes' ) ?>>
			<?php foreach ( $this->getFooterLinks() as $category => $links ) { ?>
				<ul id="footer-<?php echo $category ?>">
					<?php foreach ( $links as $link ) { ?>
						<li id="footer-<?php echo $category ?>-<?php echo $link ?>"><?php $this->html( $link ) ?></li>
					<?php } ?>
				</ul>
			<?php } ?>
			<?php $footericons = $this->getFooterIcons( "icononly" );
			if ( count( $footericons ) > 0 ) { ?>
				<ul id="footer-icons" class="noprint">
<?php			foreach ( $footericons as $blockName => $footerIcons ) { ?>
					<li id="footer-<?php echo htmlspecialchars( $blockName ); ?>ico">
<?php				foreach ( $footerIcons as $icon ) { ?>
						<?php echo $this->getSkin()->makeFooterIcon( $icon ); ?>

<?php				} ?>
					</li>
<?php			} ?>
				</ul>
			<?php } ?>
			<div style="clear:both"></div>
		
		<!-- custom footer -->
		<div style="margin-top:20px; line-height:1.5em; font-size:12px; overflow:auto">
			<div style="float:left">
				<div style="margin-bottom:4px; font-weight:bold">PCGamingWiki</div>
				<a href="http://pcgamingwiki.com">Wiki</a><br/>
				<a href="http://community.pcgamingwiki.com/index">Forums</a><br/>
				<a href="http://pcgamingwiki.com/wiki/PCGamingWiki:About">About us</a><br/>
				<a href="http://pcgamingwiki.com/wiki/PCGamingWiki:About#Contact">Contact us</a><br/>
				<a href="http://pcgamingwiki.com/wiki/PCGamingWiki:About#Advertising">Advertising</a>
			</div>
			<div style="float:left; margin-left:30px">
				<div style="margin-bottom:4px; font-weight:bold">Network</div>
				<a href="http://ftlwiki.com">FTL Wiki</a><br/>
				<a href="http://gunpointwiki.net">Gunpoint Wiki</a><br/>
				<a href="http://prisonarchitectwiki.com">Prison Architect Wiki</a><br/>
				<a href="http://siryouarebeinghuntedwiki.com">Sir, You Are Being Hunted Wiki</a><br/>
				</div>
			<div style="float:left; margin-left:30px">
				<div style="margin-bottom:4px; font-weight:bold">Friends</div>
				<a href="http://cheapshark.com">CheapShark</a><br/>
				<a href="http://gamingonlinux.com">GamingOnLinux</a><br/>
				<a href="http://pcgamesn.com">PCGamesN</a><br/>
				<a href="http://rockpapershotgun.com">Rock Paper Shotgun</a><br/>
			</div>
			<div style="float:left; margin-left:30px">
				<div style="height:22px"></div>
				<a href="http://truepcgaming.com">True PC Gaming</a><br/>
				<a href="http://wsgf.org">Widescreen Gaming Forum</a><br/>
				<a href="http://andrewtsai.co.uk">Andrew Tsai</a><br/>
			</div>
		</div>
		<!-- /custom footer -->
		</div>
		<?php $this->printTrail(); ?>
	</div> <!-- /content_box -->
	</div> <!-- /Wrapper -->

	</body>
</html>
<?php
	}

	/**
	 * Render a series of portals
	 *
	 * @param $portals array
	 */
	protected function renderPortals( $portals ) {
		// Force the rendering of the following portals
		if ( !isset( $portals['SEARCH'] ) ) {
			$portals['SEARCH'] = true;
		}
		if ( !isset( $portals['TOOLBOX'] ) ) {
			$portals['TOOLBOX'] = true;
		}
		if ( !isset( $portals['LANGUAGES'] ) ) {
			$portals['LANGUAGES'] = true;
		}
		// Render portals
		foreach ( $portals as $name => $content ) {
			if ( $content === false ) {
				continue;
			}

			switch ( $name ) {
				case 'SEARCH':
					break;
				case 'TOOLBOX':
					$this->renderPortal( 'tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
					break;
				case 'LANGUAGES':
					if ( $this->data['language_urls'] !== false ) {
						$this->renderPortal( 'lang', $this->data['language_urls'], 'otherlanguages' );
					}
					break;
				default:
					$this->renderPortal( $name, $content );
				break;
			}
		}
	}

	/**
	 * @param $name string
	 * @param $content array
	 * @param $msg null|string
	 * @param $hook null|string|array
	 */
	protected function renderPortal( $name, $content, $msg = null, $hook = null ) {
		if ( $msg === null ) {
			$msg = $name;
		}
		$msgObj = wfMessage( $msg );
		?>
<div class="portal" role="navigation" id='<?php echo Sanitizer::escapeId( "p-$name" ) ?>'<?php echo Linker::tooltip( 'p-' . $name ) ?> aria-labelledby='<?php echo Sanitizer::escapeId( "p-$name-label" ) ?>'>
	<h3<?php $this->html( 'userlangattributes' ) ?> id='<?php echo Sanitizer::escapeId( "p-$name-label" ) ?>'><?php echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg ); ?></h3>
	<div class="body">
<?php
		if ( is_array( $content ) ) { ?>
		<ul>
<?php
			foreach ( $content as $key => $val ) { ?>
			<?php echo $this->makeListItem( $key, $val ); ?>

<?php
			}
			if ( $hook !== null ) {
				wfRunHooks( $hook, array( &$this, true ) );
			}
			?>
		</ul>
<?php
		} else { ?>
		<?php
			echo $content; /* Allow raw HTML block to be defined by extensions */
		}

		$this->renderAfterPortlet( $name );
		?>
	</div>
</div>
<?php
	}

	/**
	 * Render one or more navigations elements by name, automatically reveresed
	 * when UI is in RTL mode
	 *
	 * @param $elements array
	 */
	protected function renderNavigation( $elements ) {
		global $wgVectorUseSimpleSearch;

		// If only one element was given, wrap it in an array, allowing more
		// flexible arguments
		if ( !is_array( $elements ) ) {
			$elements = array( $elements );
		// If there's a series of elements, reverse them when in RTL mode
		} elseif ( $this->data['rtl'] ) {
			$elements = array_reverse( $elements );
		}
		// Render elements
		foreach ( $elements as $name => $element ) {
			switch ( $element ) {
				case 'NAMESPACES':
?>
<div id="p-namespaces" role="navigation" class="vectorTabs<?php if ( count( $this->data['namespace_urls'] ) == 0 ) { echo ' emptyPortlet'; } ?>" aria-labelledby="p-namespaces-label">
	<h3 id="p-namespaces-label"><?php $this->msg( 'namespaces' ) ?></h3>
	<ul<?php $this->html( 'userlangattributes' ) ?>>
		<?php foreach ( $this->data['namespace_urls'] as $link ) { ?>
			<li <?php echo $link['attributes'] ?>><span><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></span></li>
		<?php } ?>
	</ul>
</div>
<?php
				break;
				case 'VARIANTS':
?>
<div id="p-variants" role="navigation" class="vectorMenu<?php if ( count( $this->data['variant_urls'] ) == 0 ) { echo ' emptyPortlet'; } ?>" aria-labelledby="p-variants-label">
	<h3 id="mw-vector-current-variant">
	<?php foreach ( $this->data['variant_urls'] as $link ) { ?>
		<?php if ( stripos( $link['attributes'], 'selected' ) !== false ) { ?>
			<?php echo htmlspecialchars( $link['text'] ) ?>
		<?php } ?>
	<?php } ?>
	</h3>
	<h3 id="p-variants-label"><span><?php $this->msg( 'variants' ) ?></span><a href="#"></a></h3>
	<div class="menu">
		<ul>
			<?php foreach ( $this->data['variant_urls'] as $link ) { ?>
				<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" lang="<?php echo htmlspecialchars( $link['lang'] ) ?>" hreflang="<?php echo htmlspecialchars( $link['hreflang'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
			<?php } ?>
		</ul>
	</div>
</div>
<?php
				break;
				case 'VIEWS':
?>
<div id="p-views" role="navigation" class="vectorTabs<?php if ( count( $this->data['view_urls'] ) == 0 ) { echo ' emptyPortlet'; } ?>" aria-labelledby="p-views-label">
	<h3 id="p-views-label"><?php $this->msg( 'views' ) ?></h3>
	<ul<?php $this->html( 'userlangattributes' ) ?>>
		<?php foreach ( $this->data['view_urls'] as $link ) { ?>
			<li<?php echo $link['attributes'] ?>><span><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php
				// $link['text'] can be undefined - bug 27764
				if ( array_key_exists( 'text', $link ) ) {
					echo array_key_exists( 'img', $link ) ? '<img src="' . $link['img'] . '" alt="' . $link['text'] . '" />' : htmlspecialchars( $link['text'] );
				}
				?></a></span></li>
		<?php } ?>
	</ul>
</div>
<?php
				break;
				case 'ACTIONS':
?>
<div id="p-cactions" role="navigation" class="vectorMenu<?php if ( count( $this->data['action_urls'] ) == 0 ) { echo ' emptyPortlet'; } ?>" aria-labelledby="p-cactions-label">
	<h3 id="p-cactions-label"><span><?php $this->msg( 'actions' ) ?></span><a href="#"></a></h3>
	<div class="menu">
		<ul<?php $this->html( 'userlangattributes' ) ?>>
			<?php foreach ( $this->data['action_urls'] as $link ) { ?>
				<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
			<?php } ?>
		</ul>
	</div>
</div>
<?php
				break;
				case 'PERSONAL':
?>
<div id="p-personal" role="navigation" class="<?php if ( count( $this->data['personal_urls'] ) == 0 ) { echo ' emptyPortlet'; } ?>" aria-labelledby="p-personal-label">
	<h3 id="p-personal-label"><?php $this->msg( 'personaltools' ) ?></h3>
	<ul<?php $this->html( 'userlangattributes' ) ?>>
<?php
					$personalTools = $this->getPersonalTools();
					foreach ( $personalTools as $key => $item ) {
						echo $this->makeListItem( $key, $item );
					}
?>
	</ul>
</div>
<?php
				break;
				case 'SEARCH':
?>
<div id="p-search" role="search">
	<h3<?php $this->html( 'userlangattributes' ) ?>><label for="searchInput"><?php $this->msg( 'search' ) ?></label></h3>
	<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
		<?php if ( $wgVectorUseSimpleSearch ) { ?>
			<div id="simpleSearch">
		<?php } else { ?>
			<div>
		<?php } ?>
			<?php
			echo $this->makeSearchInput( array( 'id' => 'searchInput' ) );
			echo Html::hidden( 'title', $this->get( 'searchtitle' ) );
			// We construct two buttons (for 'go' and 'fulltext' search modes), but only one will be
			// visible and actionable at a time (they are overlaid on top of each other in CSS).
			// * Browsers will use the 'fulltext' one by default (as it's the first in tree-order), which
			//   is desirable when they are unable to show search suggestions (either due to being broken
			//   or having JavaScript turned off).
			// * The mediawiki.searchSuggest module, after doing tests for the broken browsers, removes
			//   the 'fulltext' button and handles 'fulltext' search itself; this will reveal the 'go'
			//   button and cause it to be used.
			echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton mw-fallbackSearchButton' ) );
			echo $this->makeSearchButton( 'go', array( 'id' => 'searchButton', 'class' => 'searchButton' ) );
			?>
		</div>
	</form>
</div>
<?php

				break;
			}
		}
	}
}
