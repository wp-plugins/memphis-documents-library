<?php
function mdocs_file_info($the_mdoc) {
	$upload_dir = wp_upload_dir();
	$the_mdoc_permalink = get_permalink($the_mdoc['parent']);
	$the_post = get_post($the_mdoc['parent']);
	$post_date = strtotime($the_post->post_date);
	$last_modified = gmdate('F jS Y \a\t g:i A',$post_date+MDOCS_TIME_OFFSET);
	?>
	<div class="mdocs-post-button-box">
		<h2><a href="<?php echo $the_mdoc_permalink; ?>" title="<?php echo $the_mdoc['name']; ?> "><?php echo $the_mdoc['name']; ?></a>
		<input type="button" onclick="mdocs_download_file('<?php echo $the_mdoc['id']; ?>');" class="mdocs-download-btn" value="<?php echo __('Download'); ?>"</h2>
	</div>
	<div class="mdocs-post-file-info">
		<!--<p><i class="icon-star"></i> 4.4 Stars (102)</p>-->
		<p class="mdocs-file-info"><i class="icon-cloud-download"></i> <b class="mdocs-orange"><?php echo $the_mdoc['downloads'].' '.__('Downloads'); ?></b></p>
		<p><i class="icon-pencil"></i> <?php _e('Author'); ?>: <i class="mdocs-green"><?php echo $the_mdoc['owner']; ?></i></p>
		<p><i class="icon-off"></i> <?php _e('Version') ?>:  <b class="mdocs-blue"><?php echo $the_mdoc['version']; ?></b></p>
		<p><i class="icon-calendar"></i> <?php _e('Last Updated'); ?>: <b class="mdocs-red"><?php echo $last_modified; ?></b></p>
	</div>
<?php
}

function mdocs_edit_file($the_mdocs, $index, $current_cat) {
	?>
	<div class="mdocs-edit-file">
		<span class="update" id="<?php echo $index ?>">
			<i class="icon-pencil"></i> <a href="<?php echo 'admin.php?page=memphis-documents.php&cat='.$current_cat.'&action=update-doc&mdocs-index='.$index; ?>" title="Update this file" class="edit"><?php _e('Update'); ?></a> |
		</span>
		<span class='delete'>
			<i class="icon-remove"></i> <a class='submitdelete' onclick="return showNotice.warn();" href="<?php echo 'admin.php?mdocs-nonce='.$_SESSION['mdocs-nonce'].'&page=memphis-documents.php&cat='.$current_cat.'&action=delete-doc&mdocs-index='.$index; ?>"><?php _e('Delete'); ?></a> |
		</span>
		<span class="versions">
			<i class="icon-off"></i> <a href="<?php echo 'admin.php?page=memphis-documents.php&cat='.$current_cat.'&mdocs-index='.$index; ?>&action=mdocs-versions" title="<?php _e('Versions'); ?>" class="edit"><?php _e('Versions'); ?></a></span>
	</div>
	<?php
}

function mdocs_post_page() {
	global $post;
	$post_category = get_the_category( $post->ID );
	if($post_category[0]->slug == 'mdocs-media') {
		$mdocs = get_option('mdocs-list');
		foreach($mdocs as $files => $value) {
			if($value['parent'] == $post->ID) { $the_mdoc = $value; break; }
		}
		$query = new WP_Query('post_type=attachment&post_status=inherit&post_parent='.$post->ID);
		$user_info = get_userdata($post->post_author);
		$mdocs_file = $query->post;
		$upload_dir = wp_upload_dir();
		$file = substr(strrchr($mdocs_file->post_excerpt, '/'), 1 );
		$filesize = filesize($upload_dir['basedir'].'/mdocs/'.$file);
		$last_modified = gmdate('F jS Y \a\t g:i A',filemtime($upload_dir['basedir'].'/mdocs/'.$file)+MDOCS_TIME_OFFSET);
		$query = new WP_Query('pagename=mdocuments-library');	
		$permalink = get_permalink($query->post->ID);
		if( strrchr($permalink, '?page_id=')) $mdocs_link = '/'.strrchr($permalink, '?page_id=');
		else $mdocs_link = '/'.$query->post->post_name.'/';
		$mdocs_desc = apply_filters('the_content', $post->post_excerpt);
		?>
		<div class="mdocs-post">
			<?php mdocs_file_info($the_mdoc); ?>
			<div class="mdocs-clear-both"></div>
			<?php mdocs_social($the_mdoc); ?>
		</div>
		<div class="mdocs-clear-both"></div>
		<h3>Description</h3>
		<p><?php echo $mdocs_desc; ?></p>
		<div class="mdocs-clear-both"></div>
		</div>
		<?php
	} else {
		print do_shortcode(nl2br(get_the_content('Continue Reading &rarr;')));
	}
	
}

function mdocs_rename_file($upload, $file_name) {
	$upload_dir = wp_upload_dir();
	$index = 0;
	$org_filename = $file_name;
	while(file_exists($upload_dir['basedir'].'/mdocs/'.$file_name)) {
		$index++;
		$explode = explode('.',$org_filename);
		$tail = $index.'.'.$explode[count($explode)-1];
		array_pop($explode);
		$file_name = implode('',$explode).$tail;
	}
	$upload['url'] = $upload_dir['baseurl'].'/mdocs/'.$file_name;
	$upload['file'] = $upload_dir['basedir'].'/mdocs/'.$file_name;
	$upload['filename'] = $file_name;
	$name = substr($file_name, 0, strrpos($file_name, '.') );
	if($_POST['mdocs-name'] == '') $upload['name'] = $name;
	else $upload['name'] = $_POST['mdocs-name'];
	return $upload;
}

function mdocs_process_file($file, $import=false) {
	global $current_user;
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$mdocs_type = $_POST['mdocs-type'];
	if($_POST['mdocs-desc'] == '') $desc = MDOCS_DEFAULT_DESC;
	else $desc = $_POST['mdocs-desc'];
	if($import) $desc = $file['desc'];
	$upload_dir = wp_upload_dir();
	if($import == false) {
		$upload['url'] = $upload_dir['baseurl'].'/mdocs/'.$file['name'];
		$upload['file'] = $upload_dir['basedir'].'/mdocs/'.$file['name'];
		$upload['filename'] = $file['name'];
		if(file_exists($upload_dir['basedir'].'/mdocs/'.$file['name'])) $upload = mdocs_rename_file($upload, $file['name']);
		else {
			$name = substr($file['name'], 0, strrpos($file['name'], '.') );
			if($_POST['mdocs-name'] == '') $upload['name'] = $name;
			else $upload['name'] = $_POST['mdocs-name'];
		}
		move_uploaded_file($file['tmp_name'], $upload['file']);
	} else {
		$upload['url'] = $upload_dir['baseurl'].'/mdocs/'.$file['filename'];
		$upload['file'] = $upload_dir['basedir'].'/mdocs/'.$file['filename'];
		$upload['filename'] = $file['filename'];
		$upload['name'] = $file['name'];
	}
	$wp_filetype = wp_check_filetype($upload['file'], null );
	$mdocs_post_cat = get_category_by_slug( 'mdocs-media' );
	if($mdocs_type == 'mdocs-add' || $import == true) {
		$mdocs_post = array(
			'post_title' => $upload['name'],
			'post_status' => 'publish',
			'post_content' => '[mdocs_post_page]',
			'post_author' => $current_user->ID,
			'post_category' => array($mdocs_post_cat->cat_ID),
			'post_excerpt' => $desc,
		);
		$mdocs_post_id = wp_insert_post( $mdocs_post );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', basename($upload['name'])),
			'post_content' => '',
			'post_author' => $current_user->ID,
			'post_status' => 'inherit',
			'post_excerpt' => $upload['url'],
			'comment_status' => 'closed',
		 );
		$mdocs_attach_id = wp_insert_attachment( $attachment, $upload['file'], $mdocs_post_id );
		$mdocs_attach_data = wp_generate_attachment_metadata( $mdocs_attach_id, $upload['file'] );
		wp_update_attachment_metadata( $mdocs_attach_id, $mdocs_attach_data );
		$upload['parent_id'] = $mdocs_post_id;
		$upload['attachment_id'] = $mdocs_attach_id;
		wp_set_post_tags( $mdocs_post_id, 'memphis documents library,memphis,documents,library,media,'.$wp_filetype['type'] );
	} elseif($mdocs_type == 'mdocs-update') {
		$mdocs_post = array(
			'ID' => $file['parent'],
			'post_title' => $upload['name'],
			'post_status' => 'publish',
			'post_content' => '[mdocs_post_page]',
			'post_author' => $current_user->ID,
			'post_category' => array($mdocs_post_cat->cat_ID),
			'post_excerpt' => $desc,
			'post_date' => gmdate('Y-m-d H:i:s', time()),
		);
		$mdocs_post_id = wp_update_post( $mdocs_post );
		$attachment = array(
			'ID' => $file['id'],
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => $upload['name'],
			'post_content' => '',
			'post_author' => $current_user->ID,
			'post_status' => 'inherit',
			'post_excerpt' => $upload['url'],
			'post_date' => gmdate('Y-m-d H:i:s', time()),
		 );
		update_attached_file( $file['id'], $upload['file'] );
		$mdocs_attach_id = wp_update_post( $attachment );
		$mdocs_attach_data = wp_generate_attachment_metadata( $mdocs_attach_id, $upload['file'] );
		wp_update_attachment_metadata( $mdocs_attach_id, $mdocs_attach_data );
		wp_set_post_tags( $mdocs_post_id, 'memphis documents library,memphis,documents,library,media,'.$wp_filetype['type'] );
	}
	$upload['desc'] = $desc;
	return $upload;
}

function mdocs_nonce() {
	session_start();
	define('MDOCS_NONCE',$_SESSION['mdocs-nonce']);
	//var_dump(MDOCS_NONCE);
	if(!isset($_SESSION['mdocs-nonce']) || isset($_REQUEST['mdocs-nonce'])) $_SESSION['mdocs-nonce'] = md5(rand(0,1000000));
	session_write_close();	
}

function mdocs_array_sort($the_array, $orderby, $sort_types=SORT_ASC) {
    if($the_array != null) {
		foreach($the_array as $a){ 
			foreach($a as $key=>$value){ 
				if(!isset($sortArray[$key])){ 
					$sortArray[$key] = array(); 
				} 
				$sortArray[$key][] = $value; 
			} 
		}
		
		$array_lowercase = array_map('strtolower', $sortArray[$orderby]);
		array_multisort($array_lowercase, SORT_ASC, SORT_STRING,$the_array);
		return $the_array;
	} else return null;
}

function mdocs_export_file_status() {
	$upload_dir = wp_upload_dir();
	$mdocs_zip = get_option('mdocs-zip');
	if(file_exists($upload_dir['basedir'].'/mdocs/'.$mdocs_zip)) {
		mdocs_errors(MDOCS_ZIP_STATUS_OK);
	} else mdocs_errors(MDOCS_ZIP_STATUS_FAIL,'error');
}


function mdocs_errors($error, $type='updated') {
	if($type == 'error') $error = '<b>'.__('Memphis Error').': </b>'.$error;
	else $error = '<b>'.__('Memphis Info').': </b>'.$error;
	?>
	<div class="<?php echo $type; ?>">
		<div id="mdocs-error">
		<p><?php _e($error); ?></p>
		</div>
	</div>
    <?php
}

function mdocs_social($the_mdoc) {
	?>
	<div class="mdocs-social"  id="mdocs-social-<?php echo $the_mdoc['id']; ?>">
		<div class="mdocs-share" onclick="mdocs_share('<?php echo site_url().'/?mdocs-file='.$the_mdoc['id']; ?>', 'mdocs-social-<?php echo $the_mdoc['id']; ?>');"><p><i class="icon-share-sign mdocs-green"></i> Share</p></div>
		<div class="mdocs-tweet"><a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo site_url().'/?mdocs-file='.$the_mdoc['id'];?>" data-counturl="<?php echo site_url().'/?mdocs-file='.$the_mdoc['id'];?>" width="50">Tweet</a></div>
		<div class="mdocs-like"><iframe src="//www.facebook.com/plugins/like.php?href=<?php echo site_url().'/?mdocs-file='.$the_mdoc['id'];?>&amp;width=450&amp;height=21&amp;colorscheme=light&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;send=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe></div>
		<div class="mdocs-plusone"><div class="g-plusone" data-size="medium" data-href="<?php echo site_url().'/?mdocs-file='.$the_mdoc['id'];?>"</div></div>
	</div>
	<?php
}

function mdocs_social_scripts() {
	?>
<div id="fb-root"></div>
<script type="text/javascript">
//FACEBOOK LIKE
(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&status=0";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
//TWITTER TWEET
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
//GOOGLE +1
(function() {
  var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
  po.src = 'https://apis.google.com/js/plusone.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();
</script>
	<?php
}

function mdocs_is_bot() {
    $spiders = array(
        "abot",
        "dbot",
        "ebot",
        "hbot",
        "kbot",
        "lbot",
        "mbot",
        "nbot",
        "obot",
        "pbot",
        "rbot",
        "sbot",
        "tbot",
        "vbot",
        "ybot",
        "zbot",
        "bot.",
        "bot/",
        "_bot",
        ".bot",
        "/bot",
        "-bot",
        ":bot",
        "(bot",
        "crawl",
        "slurp",
        "spider",
        "seek",
        "accoona",
        "acoon",
        "adressendeutschland",
        "ah-ha.com",
        "ahoy",
        "altavista",
        "ananzi",
        "anthill",
        "appie",
        "arachnophilia",
        "arale",
        "araneo",
        "aranha",
        "architext",
        "aretha",
        "arks",
        "asterias",
        "atlocal",
        "atn",
        "atomz",
        "augurfind",
        "backrub",
        "bannana_bot",
        "baypup",
        "bdfetch",
        "big brother",
        "biglotron",
        "bjaaland",
        "blackwidow",
        "blaiz",
        "blog",
        "blo.",
        "bloodhound",
        "boitho",
        "booch",
        "bradley",
        "butterfly",
        "calif",
        "cassandra",
        "ccubee",
        "cfetch",
        "charlotte",
        "churl",
        "cienciaficcion",
        "cmc",
        "collective",
        "comagent",
        "combine",
        "computingsite",
        "csci",
        "curl",
        "cusco",
        "daumoa",
        "deepindex",
        "delorie",
        "depspid",
        "deweb",
        "die blinde kuh",
        "digger",
        "ditto",
        "dmoz",
        "docomo",
        "download express",
        "dtaagent",
        "dwcp",
        "ebiness",
        "ebingbong",
        "e-collector",
        "ejupiter",
        "emacs-w3 search engine",
        "esther",
        "evliya celebi",
        "ezresult",
        "falcon",
        "felix ide",
        "ferret",
        "fetchrover",
        "fido",
        "findlinks",
        "fireball",
        "fish search",
        "fouineur",
        "funnelweb",
        "gazz",
        "gcreep",
        "genieknows",
        "getterroboplus",
        "geturl",
        "glx",
        "goforit",
        "golem",
        "grabber",
        "grapnel",
        "gralon",
        "griffon",
        "gromit",
        "grub",
        "gulliver",
        "hamahakki",
        "harvest",
        "havindex",
        "helix",
        "heritrix",
        "hku www octopus",
        "homerweb",
        "htdig",
        "html index",
        "html_analyzer",
        "htmlgobble",
        "hubater",
        "hyper-decontextualizer",
        "ia_archiver",
        "ibm_planetwide",
        "ichiro",
        "iconsurf",
        "iltrovatore",
        "image.kapsi.net",
        "imagelock",
        "incywincy",
        "indexer",
        "infobee",
        "informant",
        "ingrid",
        "inktomisearch.com",
        "inspector web",
        "intelliagent",
        "internet shinchakubin",
        "ip3000",
        "iron33",
        "israeli-search",
        "ivia",
        "jack",
        "jakarta",
        "javabee",
        "jetbot",
        "jumpstation",
        "katipo",
        "kdd-explorer",
        "kilroy",
        "knowledge",
        "kototoi",
        "kretrieve",
        "labelgrabber",
        "lachesis",
        "larbin",
        "legs",
        "libwww",
        "linkalarm",
        "link validator",
        "linkscan",
        "lockon",
        "lwp",
        "lycos",
        "magpie",
        "mantraagent",
        "mapoftheinternet",
        "marvin/",
        "mattie",
        "mediafox",
        "mediapartners",
        "mercator",
        "merzscope",
        "microsoft url control",
        "minirank",
        "miva",
        "mj12",
        "mnogosearch",
        "moget",
        "monster",
        "moose",
        "motor",
        "multitext",
        "muncher",
        "muscatferret",
        "mwd.search",
        "myweb",
        "najdi",
        "nameprotect",
        "nationaldirectory",
        "nazilla",
        "ncsa beta",
        "nec-meshexplorer",
        "nederland.zoek",
        "netcarta webmap engine",
        "netmechanic",
        "netresearchserver",
        "netscoop",
        "newscan-online",
        "nhse",
		"ning",
        "nokia6682/",
        "nomad",
        "noyona",
        "nutch",
        "nzexplorer",
        "objectssearch",
        "occam",
        "omni",
        "open text",
        "openfind",
        "openintelligencedata",
        "orb search",
        "osis-project",
        "pack rat",
        "pageboy",
        "pagebull",
        "page_verifier",
        "panscient",
        "parasite",
        "partnersite",
        "patric",
        "pear.",
        "pegasus",
        "peregrinator",
        "pgp key agent",
        "phantom",
        "phpdig",
        "picosearch",
        "piltdownman",
        "pimptrain",
        "pinpoint",
        "pioneer",
        "piranha",
        "plumtreewebaccessor",
        "pogodak",
        "poirot",
        "pompos",
        "poppelsdorf",
        "poppi",
        "popular iconoclast",
        "psycheclone",
        "publisher",
        "python",
        "rambler",
        "raven search",
        "roach",
        "road runner",
        "roadhouse",
        "robbie",
        "robofox",
        "robozilla",
        "rules",
        "salty",
        "sbider",
        "scooter",
        "scoutjet",
        "scrubby",
        "search.",
        "searchprocess",
        "semanticdiscovery",
        "senrigan",
        "sg-scout",
        "shai'hulud",
        "shark",
        "shopwiki",
        "sidewinder",
        "sift",
        "silk",
        "simmany",
        "site searcher",
        "site valet",
        "sitetech-rover",
        "skymob.com",
        "sleek",
        "smartwit",
        "sna-",
        "snappy",
        "snooper",
        "sohu",
        "speedfind",
        "sphere",
        "sphider",
        "spinner",
        "spyder",
        "steeler/",
        "suke",
        "suntek",
        "supersnooper",
        "surfnomore",
        "sven",
        "sygol",
        "szukacz",
        "tach black widow",
        "tarantula",
        "templeton",
        "/teoma",
        "t-h-u-n-d-e-r-s-t-o-n-e",
        "theophrastus",
        "titan",
        "titin",
        "tkwww",
        "toutatis",
        "t-rex",
        "tutorgig",
		"tweetmemebot",
        "twiceler",
        "twisted",
        "ucsd",
        "udmsearch",
        "url check",
        "updated",
        "vagabondo",
        "valkyrie",
        "verticrawl",
        "victoria",
        "vision-search",
        "volcano",
        "voyager/",
        "voyager-hc",
        "w3c_validator",
        "w3m2",
        "w3mir",
        "walker",
        "wallpaper",
        "wanderer",
        "wauuu",
        "wavefire",
        "web core",
        "web hopper",
        "web wombat",
        "webbandit",
        "webcatcher",
        "webcopy",
        "webfoot",
        "weblayers",
        "weblinker",
        "weblog monitor",
        "webmirror",
        "webmonkey",
        "webquest",
        "webreaper",
        "websitepulse",
        "websnarf",
        "webstolperer",
        "webvac",
        "webwalk",
        "webwatch",
        "webwombat",
        "webzinger",
        "wget",
        "whizbang",
        "whowhere",
        "wild ferret",
        "worldlight",
        "wwwc",
        "wwwster",
        "xenu",
        "xget",
        "xift",
        "xirq",
        "yandex",
        "yanga",
        "yeti",
        "yodao",
        "zao/",
        "zippp",
        "zyborg",
        "...."
    );

    foreach($spiders as $spider) {
        //If the spider text is found in the current user agent, then return true
        if ( stripos($_SERVER['HTTP_USER_AGENT'], $spider) !== false ) return true;
    }
    //If it gets this far then no bot was found!
    return false;

}

function mdocs_send_bot_alert() {
	$to      = 'ian@howatson.net';
	$subject = 'Bot Alert';
	$message = 'User Agent Info: '.$_SERVER['HTTP_USER_AGENT']."\r\nIs this a bot: ".mdocs_is_bot();
	$headers = 'From: '.get_bloginfo('admin_email') . "\r\n" .
		'Reply-To: '.get_bloginfo('admin_email') . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
	mail($to, $subject, $message, $headers);
}