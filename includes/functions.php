<?php 

function pacmec(){
	return new PACMEC();
};

function pacmec_extract_info($file){
	if(is_dir($file)){
		return [];
	} else {
		if(is_file($file) && file_exists($file)){
			$texto = @file_get_contents($file);
			$input_line = nl2br($texto);
			preg_match_all('/[*\s]+([a-zA-Z\s\i]+)[:]+[\s]+([a-zA-Z0-9]+[^<]+)/mi', $input_line, $detect_array);
			$detect = [];
			// validar si es plugin
			foreach($detect_array[1] as $i=>$lab){ $detect[str_replace(['  ', ' ', '+'], '_', strtolower($lab))] = $detect_array[2][$i]; }
			
			$detect['dir'] = dirname($file);
			$detect['path'] = $file;
			if((isset($detect['plugin_name']) && isset($detect['version'])) || (isset($detect['theme_name']) && isset($detect['version']))){
				return $detect;
			}
		}
	}
	return [];
}

function pacmec_validate_file($file){
	if(is_dir($file) && $is_file($file)){
		return "directory";
	} else {
		if(is_file($file) && file_exists($file) && !is_dir($file)){
			$texto = @file_get_contents($file);
			$input_line = nl2br($texto);
			
			preg_match_all('/[*\s]+([a-zA-Z\s\i]+)[:]+[\s]+([a-zA-Z0-9]+[^<]+)/mi', $input_line, $detect_array);
			$detect = [];
			
			// validar si es plugin
			foreach($detect_array[1] as $i=>$lab){ $detect[str_replace(['  ', ' ', '+'], '_', strtolower($lab))] = $detect_array[2][$i]; }		
			if(isset($detect['plugin_name']) && isset($detect['version'])){
				return "plugin";
			}
			// validar si es tema
			foreach($detect_array[1] as $i=>$lab){ $detect[str_replace(['  ', ' ', '+'], '_', strtolower($lab))] = $detect_array[2][$i]; }		
			if(isset($detect['theme_name']) && isset($detect['version'])){
				return "theme";
			}
		}
	}
	return "undefined";
}

function php_file_tree_dir_JSON($directory, $return_link, $extensions = array(), $first_call = true, $step=0, $limit=1) {
	if( function_exists("scandir") ) $file = scandir($directory); else $file = php4_scandir($directory);
	natcasesort($file);
	$files = $dirs = array();
	foreach($file as $this_file) {
		if( is_dir("$directory/$this_file" ) ) $dirs[] = $this_file; else $files[] = $this_file;
	}
	$file = array_merge($dirs, $files);
	if( !empty($extensions) ) {
		foreach( array_keys($file) as $key ) {
			if( !is_dir("$directory/$file[$key]") ) {
				$ext = substr($file[$key], strrpos($file[$key], ".") + 1); 
				if( !in_array($ext, $extensions) ) unset($file[$key]);
			}
		}
	}
	
	$php_file_tree_array = [];
	if( count($file) > 2 ) {
		foreach( $file as $this_file ) {
			if( $this_file != "." && $this_file != ".." ) {
				$item = new stdClass();
				$item->isFile = is_dir("$directory/$this_file") ? false : true;
				$item->name = is_dir("$directory/$this_file") ? $this_file : str_replace([substr($this_file, strrpos($this_file, "."))], '', htmlspecialchars($this_file));
				#$item->ext = substr($this_file, strrpos($this_file, ".") + 1);
				$item->directory = $directory;
				$item->link = "{$directory}/{$this_file}";
				$item->child = [];
				
				if( is_dir("$directory/$this_file") && $step>$limit) {
					// $php_file_tree = php_file_tree_dir_JSON("$directory/$this_file", $return_link ,$extensions, false);
					$item->child = php_file_tree_dir_JSON("$directory/$this_file", $return_link ,$extensions, false, $step+1, $limit);
				}
				
				$php_file_tree_array[] = $item;
				
			}
		}
	}
	return $php_file_tree_array;
}

function php_file_tree_dir_JSON_exts($directory, $return_link, $extensions = array(), $first_call = true, $step=0, $limit=1) {
	if( function_exists("scandir") ) $file = scandir($directory); else $file = php4_scandir($directory);
	natcasesort($file);
	$files = $dirs = array();
	foreach($file as $this_file) {
		if( is_dir("$directory/$this_file" ) ) $dirs[] = $this_file; else $files[] = $this_file;
	}
	$file = array_merge($dirs, $files);
	if( !empty($extensions) ) {
		foreach( array_keys($file) as $key ) {
			if( !is_dir("$directory/$file[$key]") ) {
				$ext = substr($file[$key], strrpos($file[$key], ".") + 1); 
				if( in_array($ext, $extensions) ) unset($file[$key]);
			}
		}
	}
	
	$php_file_tree_array = [];
	if( count($file) > 2 ) {
		foreach( $file as $this_file ) {
			if( $this_file != "." && $this_file != ".." ) {
				$item = new stdClass();
				$item->isFile = is_dir("$directory/$this_file") ? false : true;
				$item->name = is_dir("$directory/$this_file") ? $this_file : str_replace([substr($this_file, strrpos($this_file, "."))], '', htmlspecialchars($this_file));
				#$item->ext = substr($this_file, strrpos($this_file, ".") + 1);
				$item->directory = $directory;
				$item->link = "{$directory}/{$this_file}";
				$item->child = [];
				
				if( is_dir("$directory/$this_file") && $step>$limit) {
					$php_file_tree = php_file_tree_dir_JSON("$directory/$this_file", $return_link ,$extensions, false);
					$item->child = php_file_tree_dir_JSON("$directory/$this_file", $return_link ,$extensions, false, $step+1, $limit);
				}
				
				$php_file_tree_array[] = $item;
				/*
				
				$item->link = str_replace("[link]", "$directory/" . urlencode($this_file), $return_link);
				# $item->more = [];
				
				if( is_dir("$directory/$this_file") ) {
					$item->isFolder = true;
					$item->more = php_file_tree_dir_JSON("$directory/$this_file", $return_link ,$extensions, false, $adapter);
				} else {
					$item->isFile = true;
				}*/
				
			}
		}
	}
	return $php_file_tree_array;
}

function pacmec_load_plugins($path){
	$r = [];
	$folder_JSON = php_file_tree_dir_JSON_exts($path, true, [], true, 0, 1);
	foreach($folder_JSON as $file){
		if(is_dir($file->link)){
			$r = array_merge($r, pacmec_load_plugins($file->link));
		} else {
			$type = pacmec_validate_file($file->link);
			if($type == "plugin"){
				$info = pacmec_extract_info($file->link);
				if(isset($info['plugin_name'])){
					$info['active'] = false;
					$info['text_domain'] = strtolower(isset($info['text_domain']) ? $info['text_domain'] : str_replace(['  ',' '], ['-','-'], $info['plugin_name']));
					$r[$info['text_domain']] = $info;
				}
			}
		}
	}
	return $r;
}

function pacmec_load_theme($path){
	$r = [];
	$folder_JSON = php_file_tree_dir_JSON_exts($path, true, [], true, 0, 0);
	foreach($folder_JSON as $file){
		if(is_dir($file->link)){
			$r = array_merge($r, pacmec_load_theme($file->link));
		} else {
			$type = pacmec_validate_file($file->link);
			if($type == "theme"){
				$info = pacmec_extract_info($file->link);
				if(isset($info['theme_name'])){
					$info['text_domain'] = strtolower(isset($info['text_domain']) ? $info['text_domain'] : str_replace(['  ',' '], ['-','-'], $info['theme_name']));
					$r[] = $info;
				}
			}
		}
	}
	return isset($r[0]) ? $r[0] : [];
}

function siteinfo($option_name){
	if(!isset($GLOBALS['PACMEC']['options'][$option_name])){
		return "-NaN-";
	}
	return $GLOBALS['PACMEC']['options'][$option_name];
}

function set_route($route){
	if(!isset($GLOBALS['PACMEC']['routes'][$route])){ return false; }
	$GLOBALS['PACMEC']['route'] = $GLOBALS['PACMEC']['routes'][$route];
	return $GLOBALS['PACMEC']['routes'][$route];
}

function add_route(string $route, string $title, string $content, string $template, $permission_access=null){
	$GLOBALS['PACMEC']['routes'][$route] = (object) [
		'is_actived' => 1,
		'permission_access'=>$permission_access,
		'title'=>$title,
		'request_uri'=>$route,
		'template'=>$template,
		'content'=>$content,
	];
	return true;
}

function get_route_uri($route){
	return isset($GLOBALS['PACMEC']['routes'][$route]) ? siteinfo('siteurl').$GLOBALS['PACMEC']['routes'][$route]->request_uri : "#";
}

function thisRoute(){
	if(isset($GLOBALS['PACMEC']['route'])){
		return $GLOBALS['PACMEC']['route'];
	}
	return false;
}

function get_route(){
	if(isset($GLOBALS['PACMEC']['route'])){
		return $GLOBALS['PACMEC']['route'];
	}
	return false;
}

function add_action(string $tag,$function_to_add,int $priority = 50,string $include_path = null) : bool {
	return $GLOBALS['PACMEC']['hooks']->add_action( $tag, $function_to_add, $priority, $include_path );
}

function do_action(string $tag, $arg = ''): bool {
	return $GLOBALS['PACMEC']['hooks']->do_action( $tag, $arg );
}

function do_shortcode(string $content) : string {
	return $GLOBALS['PACMEC']['hooks']->do_shortcode( $content );
}

function add_shortcode($tag, $callback) : bool {
	if($GLOBALS['PACMEC']['hooks']->shortcode_exists($tag) == false){
		/*
		if(!isset($_GET['editor_front'])){
		} else {
			return $GLOBALS['PACMEC']['hooks']->add_shortcode( $tag, function() use ($tag) { echo "[{$tag}]"; } );
			return true;
		};*/
		return $GLOBALS['PACMEC']['hooks']->add_shortcode( $tag, $callback );
	} else {
		return false;
	}
}

function shortcode_atts($pairs, $atts, $shortcode = ''): array {
	return $GLOBALS['PACMEC']['hooks']->shortcode_atts($pairs, $atts, $shortcode);
}

function plugin_exists($plugin): bool {
	return isset($GLOBALS['PACMEC']['plugins'][$plugin]) ? true : false;
}

function plugin_is_activated($plugin): bool {
	return (isset($GLOBALS['PACMEC']['plugins'][$plugin])) ? (bool) $GLOBALS['PACMEC']['plugins'][$plugin]['active'] : false;
}

function plugin_exists_and_is_activated($plugin) : bool {
	return (plugin_exists($plugin) && plugin_is_activated($plugin)) ? true : false;
}

function pacmec_option_update_for_label($label, $value){
	try {
		return $GLOBALS['PACMEC']['DB']->FetchObject("UPDATE IGNORE `{$GLOBALS['PACMEC']['DB']->getPrefix()}options` SET `option_value`=? WHERE `option_name`= ?", [$value,$label]);
	}
	catch(Exception $e){
		#echo $e->getMessage();
		return false;
	}
}

function add_style_head($src="",$add_in_list=false){
    if ( $src ) {
		if($add_in_list == true) $GLOBALS['PACMEC']['website']['styles']['list'][] = $src;
		
		$GLOBALS['PACMEC']['website']['styles']['head'][] = [
			"src"=>$src,
			"rel"=>"stylesheet",
			"type"=>"text/css",
		];
		return true;
	}
	return false;
}

function add_style_foot($src="",$add_in_list=false){
    if ( $src ) {
		if($add_in_list == true) $GLOBALS['PACMEC']['website']['styles']['list'][] = $src;
		$GLOBALS['PACMEC']['website']['styles']['foot'][] = [
			"src"=>$src,
			"rel"=>"stylesheet",
			"type"=>"text/css",
		];
		return true;
	}
	return false;
}

function add_scripts_lib_head($src="",$add_in_list=false){
	add_script_head("{$src}", $add_in_list);
}

function add_scripts_lib_foot($src="",$add_in_list=false){
	add_script_foot("{$src}", $add_in_list);
}

function get_template_directory_uri(){
	return "{$GLOBALS['PACMEC']['options']['siteurl']}/content/themes/{$GLOBALS['PACMEC']['theme']['text_domain']}";
}

function add_script_head($src="",$add_in_list=false){
    if ( $src ) {
		if($add_in_list == true) $GLOBALS['PACMEC']['website']['scripts']['list'][] = $src;
		$GLOBALS['PACMEC']['website']['scripts']['head'][] = [
			"src"=>$src,
			"rel"=>"",
			"type"=>"text/javascript",
		];
		return true;
	}
	return false;
}

function add_script_foot($src="",$add_in_list=false){
    if ( $src ) {
		if($add_in_list == true) $GLOBALS['PACMEC']['website']['scripts']['list'][] = $src;
		$GLOBALS['PACMEC']['website']['scripts']['foot'][] = [
			"src"=>$src,
			"rel"=>"",
			"type"=>"text/javascript",
		];
		return true;
	}
	return false;
}

function is_option($option_name){
	if(!isset($GLOBALS['PACMEC']['options'][$option_name])){
		return false;
	}
	return true;
}

function get_option($option_name){
	return isset($GLOBALS['PACMEC']['options'][$option_name]) ? $GLOBALS['PACMEC']['options'][$option_name] : false;
}

function is_home(){
	$page_slug = str_replace(['index.php/','index.php',$GLOBALS['PACMEC']['options']['siteurl']], '', $_SERVER['REQUEST_URI']);
	$page_slug = (strtok($page_slug, '?'));
	$page_slug = !empty($page_slug) ? $page_slug :  str_replace($GLOBALS['PACMEC']['options']['siteurl'], '', $GLOBALS['PACMEC']['options']['homeurl']);
	$page_names = explode("/", $page_slug);
	$page_names_c = count($page_names);
	$page_name = !isset($page_names[$page_names_c-1]) && !empty($page_names[$page_names_c-1]) ? $page_names[$page_names_c-1] : null;
	if(siteinfo('siteurl') . $page_slug == siteinfo('homeurl')){
		return true;
	} else {
		return false;
	}
}

function get_header(){
	if(!is_file("{$GLOBALS['PACMEC']['theme']['dir']}/header.php") || !file_exists("{$GLOBALS['PACMEC']['theme']['dir']}/header.php")){
		exit("Error critico en tema, no existe archivo {$GLOBALS['PACMEC']['theme']['dir']}/header.php");
	}
	// require_once "system/admin-nav.php";
	require_once "{$GLOBALS['PACMEC']['theme']['dir']}/header.php";
}

function get_banner($file){
	if(!is_file("{$GLOBALS['PACMEC']['theme']['dir']}/{$file}.php") || !file_exists("{$GLOBALS['PACMEC']['theme']['dir']}/{$file}.php")){
		exit("Error critico en tema, no existe archivo {$file}");
	}
	/*
	if(isAdmin()){
		$r = "<div style=\"display: none\">";
				$r .= "<div class=\"gjs-logo-cont\" style=\"margin-top: -90px;\">";
					$r .= "<a href=\"//grapesjs.com\"><img style=\"height: 75px;\" class=\"gjs-logo\" src=\"/pacmec/docs/logo/4395195275_e92c82c6-12d1-4098-afd0-e24a29c06e37.png\"></a>";
					$r .= "<div class=\"gjs-logo-version\"></div>";
				$r .= "</div>";
			$r .= "</div>";
			$r .= "<div class=\"ad-cont\">";
				$r .= "<div id=\"native-carbon\"></div>";
			$r .= "</div>";
			$r .= "<div id=\"gjs\" >";
			echo $r;
	}*/
	require_once "{$GLOBALS['PACMEC']['theme']['dir']}/{$file}.php";
}

function language_attributes(){
	return "class=\"".siteinfo('html_type')."\" lang=\"".siteinfo('html_lang')."\"";
}

function pacmec_head(){
	foreach($GLOBALS['PACMEC']['website']['styles']['head'] as $file){
		echo "<link href=\"{$file['src']}\" rel=\"{$file['rel']}\" type=\"{$file['type']}\">";
	}
	do_action( "head" );
	foreach($GLOBALS['PACMEC']['website']['scripts']['head'] as $file){
		echo "<script src=\"{$file['src']}\" rel=\"{$file['rel']}\" type=\"{$file['type']}\"></script>";
	}
	
	if(isAdmin()){
		echo "<script>\n";
			echo "const route = ".json_encode($GLOBALS['PACMEC']['route'], JSON_PRETTY_PRINT)."; \n";
			echo "const styles_list = ".json_encode($GLOBALS['PACMEC']['website']['styles']['list'], JSON_PRETTY_PRINT)."; \n";
			echo "const scripts_list = ".json_encode($GLOBALS['PACMEC']['website']['scripts']['list'], JSON_PRETTY_PRINT)."; \n";
		echo "</script>\n";
	}
	return true;
}

function pageinfo($key){
	return isset($GLOBALS['PACMEC']['route']->{$key}) ? "{$GLOBALS['PACMEC']['route']->{$key}} | ".siteinfo('sitename') : siteinfo('sitename');
}

function body_class(){
	return do_action( "body_classes" );
}

function get_template_part($file){
	if(!is_file("{$GLOBALS['PACMEC']['theme']['dir']}/{$file}.php") || !file_exists("{$GLOBALS['PACMEC']['theme']['dir']}/{$file}.php")){
		exit("Error critico en tema, no existe archivo {$file}");
	}
	require_once "{$GLOBALS['PACMEC']['theme']['dir']}/{$file}.php";
}

function get_plugin_part($a, $file, $atts=[]){
	if(!isset($GLOBALS['PACMEC']['plugins'][$a])){
		exit("NO existe el plugin {$a}.");
		return false;
	}
	if(!file_exists("{$GLOBALS['PACMEC']['plugins'][$a]['dir']}/{$file}.php") || !is_file("{$GLOBALS['PACMEC']['plugins'][$a]['dir']}/{$file}.php")){
		exit("NO existe el archivo {$file} en el plugin.\n "."{$GLOBALS['PACMEC']['plugins'][$a]['dir']}/{$file}.php");
		return false;
	}
	foreach ($atts as $id_assoc => $valor) {
		if(!isset(${$id_assoc}) || ${$id_assoc} !== $valor){
			${$id_assoc}=$valor;
		}
	}
	require_once "{$GLOBALS['PACMEC']['plugins'][$a]['dir']}/{$file}.php";
		/*
		require_once INCLUDES_PATH.'Helper.php';
		$helper=new Helper();
		*/
	return true;
}

function has_nav_menu($slug){
	if(!isset($GLOBALS['PACMEC']['menus'][$slug])){
		return false;
	}
	return true;
}

function is_front_page(){
	if(is_home()){
		return true;
	} else {
		return false;
	}
}

function has_custom_logo(){
	if(!isset($GLOBALS['PACMEC']['options']['logo_default']) || empty($GLOBALS['PACMEC']['options']['logo_default'])){
		return false;
	}
	return true;
}

function pacmec_nav_menu($args) {
	$r = "";
	if(isset($args['name']) && has_nav_menu($args['name'])){
		$args['container_id'] = isset($args['container_id']) ? "id=\"{$args['container_id']}\"" : "";
		$args['container_class'] = isset($args['container_class']) ? "class=\"{$args['container_class']}\"" : "";
		$args['items_class'] = isset($args['items_class']) ? $args['items_class'] : "nav-item";
		$args['link_class'] = isset($args['link_class']) ? $args['link_class'] : "nav-link";
		$args['menu_class'] = isset($args['menu_class']) ? $args['menu_class'] : "menu-wrapper";
		$args['menu_childs'] = isset($args['menu_childs']) ? $args['menu_childs'] : [];
		$args['childs'] = isset($args['childs']) ? $args['childs'] : false;
		$info_menu = (array) pacmec_menu_info($args['name'], $args);
		
		$r .= !empty($args['container_id']) || !empty($args['container_class']) ? "<div {$args['container_id']} {$args['container_class']}>" : "";
			$r .= build_menu($info_menu['items'], $args);
		$r .= !empty($args['container_id']) || !empty($args['container_class']) ? "</div>" : "";
	}
	echo $r;
	#return $r;
}

function pacmec_menu_info($menu = ""){
	if(!has_nav_menu($menu)){
		return [];
	}
	return $GLOBALS['PACMEC']['menus'][$menu];
}

function build_menu($rows,$args=[]) {
	$rows = (array) $rows;
	$result = sprintf('<ul class="%1$s">', isset($args['menu_class']) ? $args['menu_class'] : "");
    foreach ($rows as $row) {
		$row = (array) $row;
		$result .= "<li class=\"{$args['items_class']}\"><a class=\"{$args['link_class']}\" href=\"$row[tag_href]\">$row[title]</a>";
			if (isset($args['childs']) && count($row['childs'])>0 && $args['childs'] == true) $result.= build_menu($row['childs'], $args['menu_childs']);
		$result .= "</li>";
    }
    $result.= "</ul>"."\n\t";
    return ($result);
}

function route_active(){
	if(isset($GLOBALS['PACMEC']['route']->is_actived) && isset($GLOBALS['PACMEC']['route']->request_uri)){
		return true;
	} else {
		return false;
	}
}

function route_editable(){
	return isset($GLOBALS['PACMEC']['route']->id) ? true : false;
}

function the_content(){
	if(isset($_GET['editor_front'])){ pacmec_start_editor(); };
	$r = do_action('page_body');
	if(isset($_GET['editor_front'])){ pacmec_end_editor(); };
	return $r;
}

function get_footer(){
	if(!is_file("{$GLOBALS['PACMEC']['theme']['dir']}/footer.php") || !file_exists("{$GLOBALS['PACMEC']['theme']['dir']}/footer.php")){
		exit("Error critico en tema, no existe archivo {$GLOBALS['PACMEC']['theme']['dir']}/footer.php");
	}
	
	require_once "{$GLOBALS['PACMEC']['theme']['dir']}/footer.php";
}

function the_custom_logo(){
	if(!has_custom_logo()){
		return "";
	}
	return $GLOBALS['PACMEC']['options']['siteurl'].$GLOBALS['PACMEC']['options']['logo_default'];
}

function esc_attr(string $str):string{
	return (string) $str;
}

function esc_html(string $str):string{
	return nl2br($str);
}

function esc_html__(string $str):string{
	return esc_html($str);
}

function esc_html_e(string $str):string{
	return esc_html__($str);
}

function _e(string $str):string{
	return esc_html_e($str);
}

function esc_attr__(string $str):string{
	return esc_attr($str);
}

function esc_url(string $str):string{
	return ($str);
}

function pacmec_foot(){
	do_action( "footer-scripts" );
	
	foreach($GLOBALS['PACMEC']['website']['styles']['foot'] as $file){
		echo "<link href=\"{$file['src']}\" rel=\"{$file['rel']}\" type=\"{$file['type']}\">";
	}
	foreach($GLOBALS['PACMEC']['website']['scripts']['foot'] as $file){
		echo "<script src=\"{$file['src']}\" rel=\"{$file['rel']}\" type=\"{$file['type']}\"></script>";
	}
	return true;
}

function pacmec_footer(){
	do_action( "footer-scripts" );
	
	if(isAdmin()){
		$r = "</div>";
		$r .= '<div id="info-panel" style="display:none">
			<br/>
			<svg class="info-panel-logo" xmlns="//www.w3.org/2000/svg" version="1"><g id="gjs-logo"><path d="M40 5l-12.9 7.4 -12.9 7.4c-1.4 0.8-2.7 2.3-3.7 3.9 -0.9 1.6-1.5 3.5-1.5 5.1v14.9 14.9c0 1.7 0.6 3.5 1.5 5.1 0.9 1.6 2.2 3.1 3.7 3.9l12.9 7.4 12.9 7.4c1.4 0.8 3.3 1.2 5.2 1.2 1.9 0 3.8-0.4 5.2-1.2l12.9-7.4 12.9-7.4c1.4-0.8 2.7-2.2 3.7-3.9 0.9-1.6 1.5-3.5 1.5-5.1v-14.9 -12.7c0-4.6-3.8-6-6.8-4.2l-28 16.2" style="fill:none;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-width:10;stroke:#fff"/></g></svg>
			<br/>
			<div class="info-panel-label">
				<b>GrapesJS Webpage Builder</b> is a simple showcase of what is possible to achieve with the
				<a class="info-panel-link gjs-four-color" target="_blank" href="https://github.com/artf/grapesjs">GrapesJS</a> core library <br/><br/>
				For any hint about the demo check the <a class="info-panel-link gjs-four-color" target="_blank" href="https://github.com/artf/grapesjs-preset-webpage">Webpage Preset repository</a>
				and open an issue. For problems with the builder itself, open an issue on the main <a class="info-panel-link gjs-four-color" target="_blank" href="https://github.com/artf/grapesjs">GrapesJS repository</a> 
				<br/><br/>
				Being a free and open source project contributors and supporters are extremely welcome.
				If you like the project support it with a donation of your choice or become a backer/sponsor via
				<a class="info-panel-link gjs-four-color" target="_blank" href="https://opencollective.com/grapesjs">Open Collective</a>
			</div>
		</div>';
		echo $r;
	}
	foreach($GLOBALS['PACMEC']['website']['styles']['foot'] as $file){
		echo "<link href=\"{$file['src']}\" rel=\"{$file['rel']}\" type=\"{$file['type']}\">";
	}
	foreach($GLOBALS['PACMEC']['website']['scripts']['foot'] as $file){
		echo "<script src=\"{$file['src']}\" rel=\"{$file['rel']}\" type=\"{$file['type']}\"></script>";
	}
	return true;
}

function pacmec_start_editor(){
	if(isAdmin()){
		$r = "<div style=\"display: none\">";
				$r .= "<div class=\"gjs-logo-cont\" style=\"margin-top: -90px;\">";
					$r .= "<a href=\"//grapesjs.com\"><img style=\"height: 75px;\" class=\"gjs-logo\" src=\"/pacmec/docs/logo/4395195275_e92c82c6-12d1-4098-afd0-e24a29c06e37.png\"></a>";
					$r .= "<div class=\"gjs-logo-version\"></div>";
				$r .= "</div>";
			$r .= "</div>";
			$r .= "<div class=\"ad-cont\">";
				$r .= "<div id=\"native-carbon\"></div>";
			$r .= "</div>";
			$r .= "<div id=\"gjs\" >";
			echo $r;
	}
	return true;
}

function pacmec_end_editor(){
	if(isAdmin()){
		$r = "</div>";
		$r .= '<div id="info-panel" style="display:none">
			<br/>
			<svg class="info-panel-logo" xmlns="//www.w3.org/2000/svg" version="1"><g id="gjs-logo"><path d="M40 5l-12.9 7.4 -12.9 7.4c-1.4 0.8-2.7 2.3-3.7 3.9 -0.9 1.6-1.5 3.5-1.5 5.1v14.9 14.9c0 1.7 0.6 3.5 1.5 5.1 0.9 1.6 2.2 3.1 3.7 3.9l12.9 7.4 12.9 7.4c1.4 0.8 3.3 1.2 5.2 1.2 1.9 0 3.8-0.4 5.2-1.2l12.9-7.4 12.9-7.4c1.4-0.8 2.7-2.2 3.7-3.9 0.9-1.6 1.5-3.5 1.5-5.1v-14.9 -12.7c0-4.6-3.8-6-6.8-4.2l-28 16.2" style="fill:none;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-width:10;stroke:#fff"/></g></svg>
			<br/>
			<div class="info-panel-label">
				<b>GrapesJS Webpage Builder</b> is a simple showcase of what is possible to achieve with the
				<a class="info-panel-link gjs-four-color" target="_blank" href="https://github.com/artf/grapesjs">GrapesJS</a> core library <br/><br/>
				For any hint about the demo check the <a class="info-panel-link gjs-four-color" target="_blank" href="https://github.com/artf/grapesjs-preset-webpage">Webpage Preset repository</a>
				and open an issue. For problems with the builder itself, open an issue on the main <a class="info-panel-link gjs-four-color" target="_blank" href="https://github.com/artf/grapesjs">GrapesJS repository</a> 
				<br/><br/>
				Being a free and open source project contributors and supporters are extremely welcome.
				If you like the project support it with a donation of your choice or become a backer/sponsor via
				<a class="info-panel-link gjs-four-color" target="_blank" href="https://opencollective.com/grapesjs">Open Collective</a>
			</div>
		</div>';
		echo $r;
	}
	return true;
}

function errorHtml(string $error_message="Ocurrio un problema.", $error_title="Error"){
	// '<a href="/pacmec/hola-mundo">CONTÁCTENOS <i class="fa fa-angle-right" aria-hidden="true"></i></a>'
	return sprintf("<h1>%s</h1><p>%s</p>", $error_title, $error_message);
}

function isAdmin(){
	return isUser() && validate_permission('super_user') ? true : false;
}

function isUser(){
	return !isGuest() ? true : false;
}

function isGuest(){
	return !isset($_SESSION['user']) ? true : false;
}

function is_session_started(){
    if ( php_sapi_name() !== 'cli' ) {
        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
            return session_id() === '' ? FALSE : TRUE;
        }
    }
    return FALSE;
}

function is_login(){
	return (isset($GLOBALS['PACMEC']['route']->request_uri) && $GLOBALS['PACMEC']['route']->request_uri == '/login') ? true : false;
}

function is_register(){
	return (isset($GLOBALS['PACMEC']['route']->request_uri) && $GLOBALS['PACMEC']['route']->request_uri == '/register') ? true : false;
}

function userinfo($option_name){
	return isset($GLOBALS['PACMEC']['session']->{$option_name}) ? $GLOBALS['PACMEC']['session']->{$option_name} : "";
}

function meinfo(){
	return isset($GLOBALS['PACMEC']['session']) ? $GLOBALS['PACMEC']['session'] : [];
}

function validate_permission($permission_label){
	$permission_label = $permission_label == null ? 'guest' : $permission_label;
	if($permission_label == "guest"){ return true; }
	if(!isset(userinfo('user')->id) || userinfo('user')->id == "" || userinfo('user')->id <= 0){ return false; }
	$permissions = userinfo('permissions_items');
	// $permissions = userinfo('permissions');
	if(isset($permissions[$permission_label])){ return true; }	
	return false;
}

function add_permission(string $tag, $obj=null):bool{
	return meinfo()->add_permission($tag, $obj);
}

function my_alerts(): array {
	return $GLOBALS['PACMEC']['session']->alerts;
}

function pacmec_init_vars(){
	global $PACMEC;
	$GLOBALS['PACMEC'] = [];
	$GLOBALS['PACMEC']['hooks'] = Hooks::getInstance();
	$GLOBALS['PACMEC']['DB'] = new Conectar();
	$GLOBALS['PACMEC']['method'] = isset($_SERVER['REQUEST_METHOD']) ? $method = $_SERVER['REQUEST_METHOD'] : $method = 'GET';
	$GLOBALS['PACMEC']['req_url'] = "";
	$GLOBALS['PACMEC']['route'] = null;
	$GLOBALS['PACMEC']['routes'] = [];
	$GLOBALS['PACMEC']['website'] = ["scripts" => ["head"=>[],"foot"=>[],"list"=>[]], "styles" => ["head"=>[],"foot"=>[],"list"=>[]]];
	$GLOBALS['PACMEC']['session'] = null;
	$GLOBALS['PACMEC']['alerts'] = [];
	$GLOBALS['PACMEC']['menus'] = [];
	$GLOBALS['PACMEC']['options'] = [];
}

function pacmec_init_header(){
	foreach(glob(PACMEC_PATH."includes/init/*.php") as $file){
		require_once $file;
		$classNameFile = basename($file);
		$className = str_replace([".php"],'', $classNameFile);
		if(!class_exists($className) && !interface_exists($className)){
			echo "Clase no encontrada ";
			echo $className;
			exit();
		}
	}

	foreach(glob(PACMEC_PATH."includes/models/*.php") as $file){
		require_once $file;
		$classNameFile = basename($file);
		$className = str_replace([".php"],'', $classNameFile);
		if(!class_exists($className) && !interface_exists($className)){
			echo "Clase no encontrada ";
			echo $className;
			exit();
		}
	}
}

function pacmec_init_options(){
	foreach($GLOBALS['PACMEC']['DB']->FetchAllObject("SELECT * FROM {$GLOBALS['PACMEC']['DB']->getPrefix()}options", []) as $option){
		$GLOBALS['PACMEC']['options'][$option->option_name] = $option->option_value;
	};

	foreach($GLOBALS['PACMEC']['DB']->FetchAllObject("SELECT * FROM {$GLOBALS['PACMEC']['DB']->getPrefix()}routes WHERE `is_actived`=1", []) as $route){ 
		$GLOBALS['PACMEC']['routes'][$route->request_uri] = $route;
	};
	foreach(Menus::allLoad() as $menu){ $GLOBALS['PACMEC']['menus'][$menu->slug] = $menu; }

	$GLOBALS['PACMEC']['plugins'] = pacmec_load_plugins(PACMEC_PATH."content/plugins");
	$GLOBALS['PACMEC']['theme'] = pacmec_load_theme(PACMEC_PATH."content/themes/{$GLOBALS['PACMEC']['options']['theme']}");
	
	$GLOBALS['PACMEC']['routes']['/errors'] = (object) [
		'is_actived'=>1,
		'permission_access'=>null,
		'title'=>"Ups, Error",
		'request_uri'=>'/errors',
		'template'=>'error',
		'content'=>"",
	];

}

function pacmec_init_session(){
	session_set_save_handler(new SysSession(), true);
	if(is_session_started() === FALSE || session_id() === "") session_start();
	$GLOBALS['PACMEC']['session'] = new Session();
}

function pacmec_init_route(){
	$site_url = siteinfo('siteurl');
	$currentUrl = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : $_SERVER['REQUEST_URI'];
	$currentUrl = (strtok($currentUrl, '?'));
	$reqUrl = str_replace([$site_url], '', $currentUrl);
	$detectAPI = explode('/', $reqUrl);
	$GLOBALS['PACMEC']['req_url'] = $reqUrl;
	if(!isset($GLOBALS['PACMEC']['routes'][$GLOBALS['PACMEC']['req_url']])){
		$GLOBALS['PACMEC']['routes']['/errors']->title = "Ruta no encontrada.";
		$GLOBALS['PACMEC']['routes']['/errors']->content = "\t\tRoute no encontrado: {$reqUrl}\n";
		$GLOBALS['PACMEC']['route'] = $GLOBALS['PACMEC']['routes']['/errors'];
	}
}

function pacmec_init_plugins_actives(){
	$plugs = [];
	foreach(explode(',', siteinfo('plugins_activated')) as $plug){
		// echo json_encode($plug);
		if(isset($GLOBALS['PACMEC']['plugins'][$plug])){
			// echo "\t\t\t\tExiste \n";
			$GLOBALS['PACMEC']['plugins'][$plug]['active'] = true;
			$plugs[] = $plug;
			require_once ($GLOBALS['PACMEC']['plugins'][$plug]['path']);
		}
	}
		
	if(implode(',', $plugs) !== siteinfo('plugins_activated')){ pacmec_option_update_for_label('plugins_activated', implode(',', $plugs)); }
	// echo "\t--- plugins validados ---\n";
}