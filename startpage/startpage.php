<?php
/**
 * Name: Start Page
 * Description: Set a preferred page to load on login from home page
 * Version: 1.0
 * Author: Mike Macgirvin <http://macgirvin.com/profile/mike>
 * 
 */


function startpage_load() {
//	register_hook('home_init', 'addon/startpage/startpage.php', 'startpage_home_init');
	register_hook('feature_settings', 'addon/startpage/startpage.php', 'startpage_settings');
	register_hook('feature_settings_post', 'addon/startpage/startpage.php', 'startpage_settings_post');
}


function startpage_unload() {
	unregister_hook('home_init', 'addon/startpage/startpage.php', 'startpage_home_init');
	unregister_hook('feature_settings', 'addon/startpage/startpage.php', 'startpage_settings');
	unregister_hook('feature_settings_post', 'addon/startpage/startpage.php', 'startpage_settings_post');
}



function startpage_home_init($a, $b) {

	return;
	if(! local_user())
		return;

	$page = get_pconfig(local_user(),'system','startpage');
	if(strlen($page)) {		
		$slash = ((strpos($page,'/') === 0) ? true : false);
		if(stristr($page,'://'))
			goaway($page);
		goaway($a->get_baseurl() . (($slash) ? '' : '/') . $page);
	}
	return;
}

/**
 *
 * Callback from the settings post function.
 * $post contains the $_POST array.
 * We will make sure we've got a valid user account
 * and if so set our configuration setting for this person.
 *
 */

function startpage_settings_post($a,$post) {
	if(! local_user())
		return;
	$channel = $a->get_channel();

	if($_POST['startpage-submit']) {
		$page = strip_tags(trim($_POST['startpage']));
		$page = trim($page,'/');

		if($page == 'channel')
			$page = z_root() .'/channel/' . $channel['channel_address'];
		elseif($page == '')
			$page = '';
		else
			if(strpos($page,'http') !== 0)
				$page = z_root() . '/' . $page;

		set_pconfig(local_user(),'system','startpage',$page);
	}
}


/**
 *
 * Called from the Plugin Setting form. 
 * Add our own settings info to the page.
 *
 */



function startpage_settings(&$a,&$s) {

	if(! local_user())
		return;

	/* Add our stylesheet to the page so we can make our settings look nice */

	head_add_css('/addon/startpage/startpage.css');

	/* Get the current state of our config variable */

	$page = get_pconfig(local_user(),'system','startpage');


	/* Add some HTML to the existing form */

	$s .= '<div class="settings-block">';
	$s .= '<h3>' . t('Startpage Settings') . '</h3>';
	$s .= '<div id="startpage-page-wrapper">';
	$s .= '<label id="startpage-page-label" for="startpage-page">' . t('Home page to load after login  - leave blank for matrix page') . '</label>';
	$s .= '<input id="startpage-page" type="text" name="startpage" value="' . $page . '" />';
	$s .= '</div><div class="clear"></div>';
	$s .= '<div id="startpage-desc">' . t('Examples: &quot;channel&quot; or &quot;notifications/system&quot;') . '</div>';

	/* provide a submit button */

	$s .= '<div class="settings-submit-wrapper" ><input type="submit" name="startpage-submit" class="settings-submit" value="' . t('Submit') . '" /></div></div>';

}
