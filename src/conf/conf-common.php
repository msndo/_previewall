<?php
// ********************************************************** 設定

// ========================================================== 一括表示対象サイト
$listUrlPrefix = array(
	'http://localhost',  //本番ドメイン
	'http://localhost',  //テストドメイン。いくつでも設定可能
);


// ========================================================== ファイルリスト除外条件があればこの関数を編集 
function listFile($dir) {
	$rootDir = $dir;

	if(empty($dir) || !file_exists($dir)) { return false; }

	$fileSearch = new ListFile;
	$listFileSrc = $fileSearch -> getFileListLinear($rootDir);

	$listFile = array();
	foreach($listFileSrc as $fileSrc) {
		// 除外条件集
		if(! preg_match('/\.html$/', $fileSrc)) { continue; }
		if(! preg_match('/<!DOCTYPE/i', file_get_contents($fileSrc))) { continue; }
		if(preg_match('/\/app\//', $fileSrc)) { continue; }

		array_push($listFile, $fileSrc);
	}

	return($listFile);
}
?>
