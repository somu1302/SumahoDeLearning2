add_filter('style_loader_tag', 'add_timestamp_param_on_enqueue', 10, 3);
add_filter('script_loader_tag', 'add_timestamp_param_on_enqueue', 10, 3);

function add_timestamp_param_on_enqueue($html, $handle, $href){
  $varkey = 'timestamp';

  // ソースファイルのURLとサイトURLと先頭が一致しない場合は処理しない
  $siteUrl = site_url();
  if(strncmp($siteUrl, $href, strlen($siteUrl))===0){
    $isProc = true; // 処理するか否かのフラグ

    // $varkey の名前が別途指定されている場合は処理しない。
    $query = parse_url($href, PHP_URL_QUERY);
    if($query!==null){
      foreach(explode('&', $query) as $param){
        $a = explode('=', $param);
        if($a[0]===$varkey){
          $isProc = false;
          break;
        }
      }
    }

    // 処理する場合
    if($isProc){
      $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/').'/';
      $path = $docRoot . $uri;
      if(file_exists($path)){
        $newVar = filemtime($path);
        $symbol = $query===null ? '?' : '&';
        $newHref = $href.$symbol.$varkey.'='.$newVar;
        $html = str_replace($href, $newHref, $html);
      }
    }
  }

  return $html;
}
function the_timestamped_style($cssPath){
  echo add_timestamp_param_on_enqueue('<link rel="stylesheet" href="'.$cssPath.'">', 0, $cssPath);
}
function the_timestamped_script($jsPath){
  echo add_timestamp_param_on_enqueue('<script src="'.$jsPath.'"></script>', 0, $jsPath);
}
