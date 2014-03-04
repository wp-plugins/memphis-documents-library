<?php
header("Content-type: text/css; charset: UTF-8");
$raw_path = $_SERVER['SCRIPT_FILENAME'];
$explode_path = explode('/wp-content/', $raw_path);
$doc_root = $explode_path[0];
//echo $doc_root;
require( $doc_root.'/wp-load.php' );

$num_show = 0;
if(get_option('mdocs-show-downloads')==1) $num_show++;
if(get_option('mdocs-show-author')==1) $num_show++;
if(get_option('mdocs-show-version')==1) $num_show++;
if(get_option('mdocs-show-update')==1) $num_show++;
if(get_option('mdocs-show-ratings')==1) $num_show++;
if($num_show==5) $title_width = '35%';
if($num_show==4) $title_width = '45%';
if($num_show==3) $title_width = '55%';
if($num_show==2) $title_width = '65%';
if($num_show==1) $title_width = '75%';
?>
/* COLOURS */
.gold { color: #ffc002;}

/* PAGE STYLE */
.mdocs-post { font-family: 'Bitter', Verdana, Arial, sans-serif !important; background: #fcfcfc; padding: 0 !important; border: solid 1px #e2e2e2; width: 100% !important; margin: 0px auto 40px 0; text-shadow: none !important; }
/*.mdocs-post h1 { border-top: solid 1px #e2e2e2; border-bottom: solid 1px #e2e2e2; padding: 5px 0; margin: 5px !important; font-size: 21px; color: #444 !important;}*/
.mdocs-post h2 { font-family: 'Bitter', Verdana, Arial, sans-serif !important; padding: 0px; margin: 5px !important; background: none !important; box-shadow: none !important; color: #444 !important; font-size: 22px !important; font-weight: bold !important;}
.mdocs-post h3 { font-family: 'Bitter', Verdana, Arial, sans-serif !important; padding: 0px !important; margin: 5px !important; background: none !important; box-shadow: none !important; color: #444 !important; font-size: 20px !important; font-weight: bold !important; border-bottom: solid 1px #E2E2E2; line-height: 26px !important;}
.mdocs-post p { padding: 0px !important; margin: 10px !important; font-size: 12px;  color: #444 !important; }
.mdocs-post ul, .mdocs-post ol { padding: 0 0 0 25px !important; margin: 5px !important; font-size: 12px;  color: #444 !important; }
.mdocs-post a, .mdocs-list-table a { text-decoration: none; color: #21759B !important; }
.mdocs-post a:hover, .mdocs-list-table a:hover { color: #BC360A !important; }
.mdocs-post span { font-weight: normal; float: right; position: relative; right: 10px; color: #444 !important;}
.mdocs-post-file-info { margin: 5px 0; }
.mdocs-post-file-info p { margin: 0 5px !important; padding: 0 !important; border: none !important; }
.mdocs-post-button-box { line-height: 24px; }
/*.mdocs-post-button-box input[type='button'] { font-size: 14px !important; float: right; margin: 5px 0; }*/
.mdocs-post-button-box a { margin-left: 5px; font-family: 'Bitter', "HelveticaNeue-Light",sans-serif !important; font-weight: normal !important; font-size: 20px !important; }
.mdocs-new, .mdocs-updated { width: 100%; text-align: center;  margin: 0 auto 5px auto !important; padding: 8px 0px; font-family: 'Bitter', Verdana, Arial, sans-serif !important; font-size: 18px; }
.mdocs-new-small, .mdocs-updated-small { float: right; width: 55px; text-align: center;  margin: 0 auto !important; padding: 0px 0px; border: solid 1px #E2E2E2; border-bottom: none; font-size: 11px; font-family: Arial, sans-serif; color: #f4f4f4 !important;}
.mdocs-new, .mdocs-new-small { background: #91B52D; color: #fff; }
.mdocs-updated, .mdocs-updated-small { background: #3C9DD0; color: #fff; }
.nav-single .mdocs-new, .nav-single .mdocs-updated, .widget-area .mdocs-new, .widget-area .mdocs-updated { float: left; padding: 0 5px; margin: 0 10px 0 0; width: 60px; text-align: center;}
.mdocs-post .mdocs-download-btn { margin-right: 10px !important; }

.mdocs-download-btn, .mdocs-download-btn:active {
	float: right !important;
	font-size: 14px !important;
	font-family: 'Bitter', Verdana, Arial, sans-serif !important;
	cursor: pointer !important;
	color: #fff !important;
	border: none !important;
	margin-right: 5px !important; 
	padding: 10px !important;
	font-weight: normal !important;
	text-shadow: none !important;
	height: 38px !important;
	box-shadow: none !important;
	background: #D14836 !important;
}
.mdocs-download-btn:hover { background: #c34131 !important; }


.small { font-size: 12px !important; padding: 5px 5px 1px 5px !important; margin: 0 !important; position: relative !important; top: 0px !important; right: 12px !important;}
/*
.right { float: right; position: relative !important; top: 0px !important; right: 12px !important;}
*/
.mdocs-social { padding: 10px 5px 30px 5px !important; margin: 0 !important;  overflow: hidden; border: none !important; background: #f0f0f0;}

.mdocs-tweet { float: left; height: 20px; width: 90px; }
.mdocs-like { float: left; height: 20px;  width: 90px;}
.mdocs-plusone { float: left; width: 70px !important; height: 22px; }
.mdocs-share { float: left; margin: 0 10px 0 0; cursor: pointer; }
.mdocs-share p { width: 60px !important; border: solid 1px #CCC; background: #F8F8F8; border-radius: 5px; margin: 0 0 0 5px !important; padding: 1px 3px !important; font-size: 11px !important; font-weight: bold; background: rgb(252,252,252); /* Old browsers */
	background: #fcfcfc; /* Old browsers */
	background: -moz-linear-gradient(top,  #fcfcfc 0%, #dbdbdb 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fcfcfc), color-stop(100%,#dbdbdb)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  #fcfcfc 0%,#dbdbdb 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #fcfcfc 0%,#dbdbdb 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #fcfcfc 0%,#dbdbdb 100%); /* IE10+ */
	background: linear-gradient(to bottom,  #fcfcfc 0%,#dbdbdb 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fcfcfc', endColorstr='#dbdbdb',GradientType=0 ); /* IE6-9 */

 }
 .mdocs-share p:hover { background: rgb(238,238,238); /* Old browsers */
background: -moz-linear-gradient(top,  rgba(238,238,238,1) 0%, rgba(224,224,224,1) 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(238,238,238,1)), color-stop(100%,rgba(224,224,224,1))); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  rgba(238,238,238,1) 0%,rgba(224,224,224,1) 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  rgba(238,238,238,1) 0%,rgba(224,224,224,1) 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  rgba(238,238,238,1) 0%,rgba(224,224,224,1) 100%); /* IE10+ */
background: linear-gradient(to bottom,  rgba(238,238,238,1) 0%,rgba(224,224,224,1) 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#eeeeee', endColorstr='#e0e0e0',GradientType=0 ); /* IE6-9 */

 }
.mdocs-share-link { clear: both; 
    border:1px dashed #a6ca8a;
	background: #e9ffd9;
	border-radius:5px;
    padding:10px 10px 10px 36px;
    margin: 40px 10px 10px 10px;
	text-align: center;
	font-size: 17px;
	font-weight: bold;
	}
.mdoc-desc img { margin: 5px !important; padding: 0 !important; border: solid 1px #E2E2E2 !important;}
.mdocs-container { font-family: 'Bitter', "HelveticaNeue-Light",sans-serif !important; width: 100% !important; }
.mdocs-container h2 { margin: 5px 10px; padding:0; font-weight: normal; }
.mdocs-nav-wrapper { padding: 0 15px 0px 0 !important; line-height: 29px; display: block; overflow: hidden; margin: 0 !important; width: 100% !important; clear: both !important;}
.mdocs-nav-tab { font-family: 'Bitter', "HelveticaNeue-Light",sans-serif !important; font-weight: normal !important; font-size: 20px !important; background: #e7e7e7; font-size: 17px !important; border-top:  solid 1px #DCDCDC !important; border-left:  solid 1px #DCDCDC; border-right:  solid 1px #DCDCDC; line-height: 24px; display: inline-block; padding: 4px 10px 6px; margin: 4px 2px 0px 2px !important; border-radius: 3px 3px 0 0; text-decoration: none; color: #AAA !important; text-shadow: none !important; white-space:nowrap !important; cursor: pointer !important; }
.mdocs-nav-tab span { display: inline-block; }
.mdocs-nav-tab:link, .mdocs-nav-tab:visited { color: #CCC; } 
.mdocs-nav-tab:hover { color: #FFBF40 !important; border-top:  solid 1px #DCDCDC !important; border-left:  solid 1px #DCDCDC !important; border-right:  solid 1px #DCDCDC !important;}
.mdocs-nav-tab-active { color: #086FA1 !important; background: #fff !important;  }
.mdocs-nav-wrapper hr { padding: 0; margin: 0;  position: relative; left: -20px; bottom: -7px;  background:  none; overflow: hidden; border: none; border-bottom:  solid 1px #DCDCDC !important; }
.mdocs-bg-even { background: #FFF; }
.mdocs-bg-odd { background: #f9f9f9; }
.mdocs-container table { border: none !important; border-top: solid 1px #dcdcdc !important; border-left: solid 1px #dcdcdc !important;  border-right: solid 1px #dcdcdc !important; width: 100%; margin: 0px auto !important; background: #fff !important;}
.mdocs-container table th { border: none !important; border-bottom: solid 1px #DCDCDC !important;}
.mdocs-container table td { border:  none !important; }
.mdocs-td-social { padding: 0 !important; margin: 0 !important; border: none !important; }
.mdocs-float-right { float: right; }
.mdocs-table-header { font-size: 13px !important; background: #FCFCFC !important; padding: 4px 10px 6px; font-weight: normal !important; text-shadow: 1px 1px #AAA; border-top: solid 1px #dcdcdc !important; color: #636363 !important;}
.mdocs-file-info { white-space: nowrap; padding: 9px 0; border-top: solid 1px #DCDCDC !important; color: #333; width: 25% !important; }
.mdocs-file-info a { text-decoration: none; color: #21759B !important;text-decoration: none !important;}
.mdocs-file-info a:hover { color: #BC360A !important; text-decoration: none !important; border: none !important; }
.mdocs-file-info strong { font-size: 18px; font-weight: normal;  padding: 0 10px; margin: 0; }
/*.mdocs-file-info i { color: #779F00;}*/
.mdocs-file-info div { margin: 3px 20px !important; padding: 0 5px !important; border-left: solid 1px #CCC; }
.mdocs-file-info div p { margin: 0 !important; padding: 0 !important; font-size:  13px; }
.mdocs-desc { vertical-align: top; margin: 15px 0 !important; padding: 0px 0 !important; border-top: solid 1px #D1E5EE; color: #333; width: 75% !important; }
.mdocs-desc div { margin: 10px 0; border-left: solid 1px #ccc; }
.mdocs-desc p { margin: 0 !important; padding: 0  5px !important; overflow: auto; font-size:  13px !important;}
.mdocs-desc ul { margin: 0 !important; padding: 0 !important; overflow: auto; font-size:  13px;}
.mdocs-desc ul br { padding: 0 !important; margin: 0 !important; }
.mdocs-desc li { margin: 0 10px !important; padding: 0 !important; list-style: disc !important; list-style-position: inside !important;}
.mdocs-desc a:link, .mdocs-desc a:visited { color: #21759B !important; text-decoration: none !important; }
.mdocs-desc a:hover { color: #BC360A !important; text-decoration: none !important; border: none !important; }
.mdocs-blue { color: #0066FF !important; }
.mdocs-orange { color: #FF9140 !important; }
.mdocs-red { color: #990000 !important; }
.mdocs-green { color: #779F00 !important; }
.mdocs-olive { color: #808000 !important; }
.mdocs-salmon { color: #FA8072 !important; }
.mdocs-clear-both { clear: both !important; }
.mdocs-nofiles { font-size: 14px !important; font-weight: bold; text-align: center; padding: 20px !important;}
#icon-edit-pages { width: 32px; height: 34px; background-position: -312px -5px; margin: 2px 8px 0 0; float: left; }
.icon32 {  background: url('assets/imgs/icons32-vs.png?ver=20121105/') no-repeat transparent; }
.mdocs-line { border-bottom: solid 1px #E2E2E2; height: 1px; width: 99%; margin: auto; }
.mdocs-login-msg { border: solid 1px #ccc; float: right; font-size: 13px !important; padding: 10px; margin: 5px; font-weight: normal; background:  #f0f0f0; text-align: center; }


.mdocs-list-table, .mdocs-list-table tr, .mdocs-list-table td { width: 100% !important; border: solid 1px #ccc !important; vertical-align: middle; border-collapse: collapse !important; padding: 3px 0 !important; margin: 0 !important; font-size: 13px !important;}
.mdocs-list-table td { border: none !important;  padding: 0 !important; height: 28px !important;}
.mdocs-list-table { margin-top: 10px !important; }
.mdocs-list-table #title { padding: 0 0 0 5px !important; margin: 0; width: <?php echo $title_width; ?> !important; }
.mdocs-list-table #downloads { padding: 0; width: auto !important; text-align: center;}
.mdocs-list-table #version { padding: 0; width: auto !important; text-align: center;}
.mdocs-list-table #owner { padding: 0; width: auto !important; text-align: center;}
.mdocs-list-table #update { padding: 0; width: auto !important; text-align: center;}
.mdocs-list-table #rating { padding: 0; width: auto !important; text-align: center;}
.mdocs-list-table #download { padding: 0; width: auto !important;  text-align: right; padding: 0 5px 0 0 !important; }

.mdocs-sort { position: relative; float: right; border: solid 1px #e2e2e2 !important; padding: 5px !important; background: #fcfcfc; clear: both !important;}
.mdocs-sort label { font-size: 12px !important; }
.mdocs-sort input[type="submit"] { padding: 2px !important; color: #5e5e5e !important; background: #ebebeb !important; border: solid 1px #d2d2d2 !important; cursor: pointer !important; border-radius: 3px; box-shadow: 0 1px 2px #c0c0c0 !important;}
.mdocs-sort input[type="submit"]:hover { box-shadow: 0 1px 2px #9d9d9d !important;}

.mdocs-show-social { cursor: pointer; }

/* DASHBOARD STYLE */
#icon-mdocs { background: url('assets/imgs/kon32.png') no-repeat;  } 
.mdocs-uploader-bg { position: fixed; top: 0px; left: 0px; width: 100%; height: 100%; background: #000; z-index: 25; opacity: 0.7;}
.mdocs-uploader { position: absolute; top: 10px; left: 10%; width: 80%; background: #FFF; z-index: 26; border-radius: 5px; padding-bottom: 20px;}
.mdocs-uploader-header { background: #000; color: #FFF;  padding: 5px; margin:0; font-weight: normal; font-size: 12px; border: solid 1px #CCC; }
.mdocs-uploader-header .close { float: right; padding:0; margin: 0; cursor: pointer;}
.mdocs-uploader-form h2 { font-size: 20px; color: #086FA1;padding: 0; margin: 0 0 5px 0; }
.mdocs-uploader-form h3 { padding: 2px 0; margin: 5px 0 5px 0; color: #086FA1; font-size: 24px;}
.mdocs-uploader-form .mdocs-form-box { border-bottom: dashed 1px #E2E2E2; margin: 0px 0 0px 0; padding: 8px; background: #F0F0F0; box-shadow: 0 2px 5px #888;  }
.mdocs-uploader-form .current-name { display: inline; color: #900; }
/*.mdocs-uploader-form p { font-size: 16px; font-weight: bold; font-style: italic; padding: 0; margin: 5px 0px;}*/
.mdocs-uploader-form input[type="text"] { font-weight: normal; }
.mdocs-uploader-form input[type="radio"] { margin: 5px; }
.mdocs-uploader-form select  { font-weight: normal; }
.mdocs-uploader-form label { font-size: 12px; margin: 0 5px; font-weight: bold; color: #013F71; }
.mdocs-uploader-content { background: #FFF; padding: 0; margin: 15px; }
.mdocs-grey-btn { font-family: sans-serif; font-size: 12px; padding: 5px 10px; text-decoration: none; position: relative; top: -3px; text-shadow: #fff 0 1px 0; }
.mdocs-grey-btn:link, .mdocs-add-new:visited { background: #F1F1F1; border-radius: 5px; color: #21759B;}
.mdocs-grey-btn:hover { color: #D54E21; }
#mdocs-export .updated { width: 150px !important; }
#mdocs-cats input[type="text"] { font-size: 22px !important; width: 100%; }
.mdocs-ds-container table #name { width: 97%; }
.mdocs-ds-container table #order { width: 3%; }
.mdocs-ds-container table #remove { vertical-align: middle; }
.mdocs-ds-container table #file { width: 40%; padding-left: 10px; font-weight: bold;}
.mdocs-ds-container table #version { text-align: center; font-weight: bold;}
.mdocs-ds-container table #date { width: 40%; padding-left: 10px; font-weight: bold;}
.mdocs-ds-container table #download { padding-bottom:  5px; text-align: center; }
.mdocs-ds-container table #current { text-align: center;}
.mdocs-admin-desc ul { padding: 0 0 0 25px !important; margin: 5px !important; font-size: 12px;  color: #444 !important; list-style: disc !important;}
.mdocs-admin-desc ol { padding: 0 0 0 25px !important; margin: 5px !important; font-size: 12px;  color: #444 !important; }
.mdocs-admin-desc p { padding: 0 !important; margin: 10px 0 !important; }
.mdocs-admin-share { border: solid 1px #E6DB55; background: lightYellow; padding: 5px; color: #333; }
.mdocs-edit-file { background: #F0F0F0; border-top: solid 1px #E2E2E2; overflow: auto; padding: 10px; margin: 0 !important; }
.mdocs-edit-file span { float: left !important; padding: 0 !important; margin: 0 0 0 5px !important; }
.mdocs-edit-file .submitdelete { color: #BC0B0B !important; }
.mdocs-edit-file .submitdelete:hover { color: #f00000; }
.mdocs-settings-table td { vertical-align: top !important; text-align: left !important;}
.mdocs-setting-form .width-30 { width: 30px; }
.mdocs-filesystem-cleanup { border: solid 1px #E6DB55; background: #FFFFE0; overflow: auto; padding: 10px;}
.mdocs-filesystem-cleanup .cleanup-files { float: left; padding-right: 20px; }
.mdocs-filesystem-cleanup .cleanup-data { float: left; }
#the-list { text-shadow: none !important; }
/* RATING STYLE */
.mdocs-rating-container { float: right; height: 10px;}
.mdocs-rating-container-small { cursor: pointer; clear: both; float: right; padding: 0 10px 10px 10px; }
.mdocs-rating-container-small div:first-child { font-size: 11px; margin: auto; text-align: center;}
.mdocs-rating-container-small div:last-child { font-size: 20px; margin: auto; text-align: center; color: #9d9d9d;}
.mdocs-rating-container-small span { font-size: 11px; }
.mdocs-big-star { text-shadow: 1px 2px 1px #474747; padding: 0 1px;}
/* BUTTON STYLES */
.mdocs-download-btn-config:hover { background: <?php echo get_option('mdocs-download-color-hover'); ?> !important; }
.mdocs-close-btn { position: absolute; float: right; right: 20px; top: 30px; border: solid 1px #0074a2; box-shadow: inset 0 1px 0 #005c81; background: #2ea2cc; padding: 3px 5px; color: #fff; border-radius: 3px; margin: 15px !important; font-size: 13px !important; font-family: 'Open Sans', sans-serif; padding: 1px 10px 0px 10px; cursor: pointer; }
.mdocs-close-btn:hover { background: #1E8CBE; }
.wp-picker-holder { position: absolute !important; z-index: 2000;}
.mdocs-download-btn-config {
	text-align: left !important;
	width: 70px !important;
	font-size: 14px !important;
	font-family: 'Bitter', Verdana, Arial, sans-serif !important;
	cursor: pointer !important;
	color: #fff !important;
	border: none !important;
	margin: 5px 0 !important;
	padding: 10px !important;
	font-weight: normal !important;
	text-shadow: none !important;
	height: 20px !important;
	box-shadow: none !important;
	background: <?php echo get_option('mdocs-download-color-normal'); ?> ;
}


/* FONTS */
@font-face {
  font-family: 'Bitter';
  font-style: normal;
  font-weight: 400;
  src: local('Bitter-Regular'), url(https://themes.googleusercontent.com/static/fonts/bitter/v4/B2Nuzzqgk0xdMJ132boli-vvDin1pK8aKteLpeZ5c0A.woff) format('woff');
}
@font-face {
  font-family: 'Bitter';
  font-style: normal;
  font-weight: 700;
  src: local('Bitter-Bold'), url(https://themes.googleusercontent.com/static/fonts/bitter/v4/JGVZEP92dXgoQBG1CnQcfLO3LdcAZYWl9Si6vvxL-qU.woff) format('woff');
}

/* PREVIEW WINDOWS */
.mdocs-wp-preview { display: none; background: #e6e6e6; opacity: 1; width: 99%; height: 200%; position: absolute; top: 0; left: 10px; text-align: center;}
.mdocs-wp-preview h1 { text-align: left; padding: 0;  margin: 0; padding-top: 20px; }
.mdocs-google-doc { width: 100% !important; height: 100% !important; margin: 0px; padding: 0; }
.mdocs-admin-preview { position: absolute !important; top: 0; left: 0;  background: #e6e6e6; width: 99%; height: 200%; z-index: 99999; display: none;}
.mdocs-admin-preview h1 { text-align: left; padding: 0;  margin: 0 10px 24px 10px; padding-top: 20px; font-size: 21px !important;}
.mdocs-admin-preview .mdocs-google-doc { width: 100% !important; height: 140% !important; margin: 0px; padding: 0; }
.mdocs-admin-preview .mdocs-close-btn { top: -10px; }
.mdocs-preview-icon { cursor:  pointer !important; }
.mdocs-img-preview { text-align: center;  width: 100%; height: 100%; }
.mdocs-show-container { border-top: solid 1px #e2e2e2; }

/* JQUERY UI TOOLTIP SYTEL */
.ui-tooltip, .arrow:after { background: black !important; border: 2px solid white; }
.ui-tooltip { padding: 10px 20px; color: white; border-radius: 20px; font: bold 14px "Helvetica Neue", Sans-Serif; text-transform: uppercase; box-shadow: 0 0 7px black; min-width: 800px !important; }
.ui-tooltip p { padding: 0 0 10px 0 !important; margin: 0 !important; font-size: 12px !important;}
.ui-tooltip a { color: #cc0000; }
.arrow { width: 70px; height: 16px; overflow: hidden; position: absolute; left: 50%; margin-left: -35px; bottom: -16px; }
.arrow.top { top: -16px; bottom: auto; }
.arrow.left { left: 5%; }
.arrow:after { content: ""; position: absolute; left: 20px; top: -20px; width: 25px; height: 25px; box-shadow: 6px 5px 9px -9px black; -webkit-transform: rotate(45deg); -moz-transform: rotate(45deg); -ms-transform: rotate(45deg); -o-transform: rotate(45deg); tranform: rotate(45deg); }
.arrow.top:after { bottom: -20px; top: auto; }

/* THEME FIXES */
.art-content { width: 100% !important; }
