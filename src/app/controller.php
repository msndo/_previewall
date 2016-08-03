//=require ../conf/conf-common.php

<?php
// ========================================================== GETパラメータ 
$pathPrefix = preg_replace('/\/$/', '', $_SERVER['DOCUMENT_ROOT']) ;
$dirFilter = !empty($_GET['d']) ? urldecode(htmlspecialchars($_GET['d'], ENT_QUOTES, 'utf-8')) : '';
if(preg_match('/\.\./', $dirFilter)) { $dirFilter = ''; }

$titlePage = '本番・テストまとめて開く' . ($dirFilter ? ' - ' . $dirFilter : '');
?>

<?php
// ========================================================== ファイルリスト取得メソッド集
class ListFile {

	// 配下全階層にあるファイルの入れ子リスト
	function getFileListRecursive($dir) {
    	$files = glob(rtrim($dir, '/') . '/*');
    	$list = array();
    	foreach ($files as $file) {
        	if (is_file($file) && preg_match('/\.html$/', $file)) {
            	$list[$file] = $file;
        	}
        	if (is_dir($file)) {
            	$list[$file . '/']  = $this -> getFileListRecursive($file);
        	}
    	}

    	return $list;
	}


	// 配下全階層にあるファイルの直列リスト
	function getFileListLinear($dir) {
    	$files = glob(rtrim($dir, '/') . '/*');
    	$list = array();
    	foreach ($files as $file) {
        	if (is_file($file)) {
            	$list[] = $file;
        	}
        	if (is_dir($file)) {
            	$list = array_merge($list, $this -> getFileListLinear($file));
        	}
    	}

    	return $list;
	}

	// 直下階層分、単一レベルのファイルリストを返す
	function getFileListSingleLevel($dir) {
    	$files = glob(rtrim($dir, '/') . '/*');
    	$list = array();
    	foreach ($files as $file) {
            $list[] = $file;
    	}

    	return $list;
	}
}
?>