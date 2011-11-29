<?php

if (is_login()){
	add_action('login_head', 'login_scripts', 0);
}

if (is_admin()){
	add_action('admin_menu', 'create_theme_options_page');
	add_action('admin_init', 'init_theme_options');
	add_action('admin_menu', 'create_issuu_preview_page');
}

/**
 * Prints out additional login scripts, called by the login_head action
 *
 * @return void
 * @author Jared Lang
 **/
function login_scripts(){
	ob_start();?>
	<link rel="stylesheet" href="<?=THEME_CSS_URL?>/admin.css" type="text/css" media="screen" charset="utf-8" />
	<?php 
	$out = ob_get_clean();
	print $out;
}


/**
 * Called on admin init, initialize admin theme here.
 *
 * @return void
 * @author Jared Lang
 **/
function init_theme_options(){
	register_setting(THEME_OPTIONS_GROUP, THEME_OPTIONS_NAME, 'theme_options_sanitize');
}


/**
 * Registers the theme options page with wordpress' admin.
 *
 * @return void
 * @author Jared Lang
 **/
function create_theme_options_page() {
	add_utility_page(
		__(THEME_OPTIONS_PAGE_TITLE),
		__(THEME_OPTIONS_PAGE_TITLE),
		'edit_theme_options',
		'theme-options',
		'theme_options_page',
		THEME_IMG_URL.'/pegasus.png'
	);
}

function create_issuu_preview_page() {
	add_utility_page(
		__(ISSUU_PREVIEW_PAGE_TITLE),
		__(ISSUU_PREVIEW_PAGE_TITLE),
		'',
		'issue-preview',
		'issuu_preview_page',
		THEME_IMG_URL.'/pegasus.png'
	);	
}

/**
 * Outputs the theme options page html
 *
 * @return void
 * @author Jared Lang
 **/
function theme_options_page(){
	# Check for settings updated or updated, varies between wp versions
	$updated = (bool)($_GET['settings-updated'] or $_GET['updated']);
	?>
	
	<form method="post" action="options.php" id="theme-options">
		<div class="wrap">
			<h2><?=__(THEME_OPTIONS_PAGE_TITLE)?></h2>
			
			<?php if ($updated):?>
			<div class="updated fade"><p><strong><?=__( 'Options saved' ); ?></strong></p></div>
			<?php endif; ?>
			
			<?php settings_fields(THEME_OPTIONS_GROUP);?>
			<table class="form-table">
				<?php foreach(Config::$theme_settings as $key=>$setting):?>
				<?php if(is_array($setting)): $section = $setting;?>
				<tr class="section">
					<td colspan="2">
						<h3><?=$key?></h3>
						<table class="form-table">
							<?php foreach($section as $setting):?>
							<tr valign="top">
								<th scope="row"><label for="<?=htmlentities($setting->id)?>"><?=$setting->name?></label></th>
								<td class="field"><?=$setting->html()?></td>
							</tr>
							<?php endforeach;?>
						</table>
					</td>
				</tr>
				<?php else:?>
				<tr valign="top">
					<th scope="row"><label for="<?=htmlentities($setting->id)?>"><?=$setting->name?></label></th>
					<td class="field"><?=$setting->html()?></td>
				</tr>
				<?php endif;?>
				<?php endforeach;?>
			</table>
			<div class="submit">
				<input type="submit" class="button-primary" value="<?= __('Save Options')?>" />
			</div>
		</div>
	</form>
	
	<?php
}


/**
 * Stub, processing on theme options input
 *
 * @return void
 * @author Jared Lang
 **/
function theme_options_sanitize($input){
	return $input;
}

function issuu_preview_page() {
	
	$api_key    = Config::$theme_settings['Issuu'][0]->value;
	$api_secret = Config::$theme_settings['Issuu'][1]->value;

	?>
		<h2><?=__(ISSUU_PREVIEW_PAGE_TITLE)?></h2>
		<div class="wrap" id="issuu-preview">
	<?
	
	if($api_secret == '' || $api_key == '') {

		?>
		<div class="error">
			<p>
				<strong>
					The Issuu API key and/or secret are not set.
					Please set them on the theme options page.
				</strong>
			</p>
		</div>
		<?php		
	} else {
		
		$folders_result   = issuu_get_folders($api_key, $api_secret);
		$documents_result = issuu_get_documents($api_key, $api_secret);
		
		$image_location = 'http://image.issuu.com/%s/jpg/page_1_thumb_large.jpg';

		if($folders_result['success'] !== True || $documents_result['success'] !== True) {
			$error = (isset($folder_result['message'])) ? $folder_result['message'] : $documents_result['message'];
			?>
				<div class="error">
					<p>
						<strong>
							An error occurred when requesting data from 
							the Issuu API: <?=$error?>
						</strong>
					</p>
				</div>
			<?
		} else {
			?>
			<ul id="documents">
			<?
			$count = 0;
			foreach($documents_result['results'] as $document_wrap) {
				#print_r($folder_wrap->folder->folderId);
				#print_r($document_wrap->document->folders);

				//if(isset($document_wrap->document->folders) &&
				//	in_array($folder_wrap->folder->folderId, $document_wrap->document->folders)) {
				//	
				//}
				$css = '';
				if($count == 0 || ($count % 4) == 0) {
					$css = ' class="first" ';
				}
				if( (($count + 1) % 4) == 0) {
					$css = ' class="last" ';
				}
				?>
				<li<?=$css?>>
					
					<div class="thumb">
						<img src="<?=sprintf($image_location, $document_wrap->document->documentId)?>" />
					</div>
					<div class="title"><?=$document_wrap->document->title?></div>
					<div class="details">
						<a>Preview</a> - <a>Embed Code</a>
					</div>
				</li>
				<?
				$count++;
			}
			?></ul><?
		}
	}

	print '</div>';
}
