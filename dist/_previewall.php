<!-- ================================================================ load PHP controller -->
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
<!-- ================================================================ /load PHP controller -->
<!DOCTYPE html>

<html lang="ja">

<head>
<title><?php echo($titlePage); ?></title>
<meta charset="utf-8">

<!-- ================================================================ load CSS -->
<style>
@charset "utf-8";

.clr { display: table; }
.clr:after { clear: both; }
a,
a:visited { color: #447; }
ul { margin: 0; padding: 0; list-style: none; }
li { padding: 0; }

body { font-size: 65%; }
#title-page { font-size: 400%; }
#contents { width: 100%; }
.list-data { margin: 30px 0 0; width: 100%; border-collapse: collapse; }
.list-data .cell-num { text-align: right; white-space: nowrap; }
.list-data th,
.list-data td { border: 1px solid #ccc; padding: 7px 10px; word-break: break-all; white-space: normal; word-wrap: normal; }
.list-data th { font-weight: bold; background: #f6f6f2; text-align: left; font-size: 117¥%; }
.list-data th a { text-decoration: none; }
.list-data .cell-title-row { width: 300px; }

.list-data .current-selected th { background: #ddd; }
.list-data .current-selected td { background: #eee; }

.menu-drawal .ctrl-drawal { position: absolute;  left: -50px; top: 10px; }
.menu-drawal .ctrl-drawal .content-ctrl { display: block; max-width: 30px; min-height: 80px; padding: 10px; background: #888; color: #fff; border-radius: 5px 0 0 5px; text-decoration: none; }
.menu-drawal[data-openclose=open] .content-closed,
.menu-drawal[data-openclose=closed] .content-open { display: none; }

#section-manualinput-param { position: fixed; right: 0; top: 100px; width: 40%; padding: 10px; background: #fff; border: solid #888; border-width: 3px 0 3px 3px; border-radius: 8px 0 0 8px; }
#section-manualinput-param #section-dd-url { padding: 10px; border: dashed 5px #ccc; font-size: 200%;}
#section-manualinput-param #section-dd-url.dropping { background: #dfd; }
#section-manualinput-param #section-input-url { margin-top: 30px }
#section-manualinput-param #section-input-url #ctrl-input-url { width: calc(100% - 45px); border: 1px solid #ccc; border-radius: 8px; font-size: 300%; padding: 30px 20px; }

</style>
<!-- ================================================================ /load CSS -->

<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>

<!-- ================================================================ load JS -->
<script>
(function($) {
	$(function() {
		var $elemList = $('#list-url');

		$elemList.find('a').each(function() {
			var classnameClicked  = 'current-selected';

			var $elemCtrl = $(this);
			$elemCtrl.on('click.markClicked', function(ev) {
				var $elemCtrl = $(this);
				var $elemRow = $elemCtrl.closest('tr');

				$elemList.find('.' + classnameClicked).removeClass(classnameClicked);
				$elemRow.addClass(classnameClicked);
			});

		});

		$('#list-url').find('.cell-title-row a').triggerOpenWinAll();

		$('#list-url').find('.col-data a').each(function() {
			var $elemCtrl = $(this);
			$elemCtrl.on('click.openLink', function(ev) {
				openBlankWinWithPopUp($(this).attr('href'), $(this).parent().data('colidx'));
				ev.preventDefault();
				return false;
			});
		});

		$('#section-manualinput-param').find('.content-ctrl').each(function(ix) {
			var settings = {
				easing: 'easeOutExpo',
				duration: 700
			};

			var $elemCtrl = $(this);

			var $elemMenu = $('#section-manualinput-param');

			$elemCtrl.on('click.openclose', function(ev) {
				var statusElemMenu = $elemMenu.get(0).getAttribute('data-openclose');
				if(statusElemMenu == 'open') {
					$elemMenu.animate({ 'right': (0 - $elemMenu.width()) + 'px' }, settings.duration, settings.easing, function() {
						$elemMenu.get(0).setAttribute('data-openclose', 'closed');
					});
				}
				else {
					$elemMenu.animate({ 'right': 0 - ''}, settings.duration, settings.easing, function() {
						$elemMenu.get(0).setAttribute('data-openclose', 'open');
					});
				}

				return false;
			});
		});

		$('#section-dd-url').recieveDragAndDrop();
		$('#form-input-url').on('submit', function() { return $(this).submitUrl(); })
	});


	// Create Blank Popup
	function openBlankWinWithPopUp(hrefTarg, nameWindow) {
		var win = window.open(
			hrefTarg
			, nameWindow
			,'width=1024\
			,height=768\
			,toolbar=yes\
			,menubar=yes\
			,resizable=yes\
			,scrollbars=yes\
			,status=yes\
			,location=yes'
		);

		if(win) { 
			win.blur();
			win.focus();
		}
	};

	$.fn.triggerOpenWinAll = function() {
		var $listElemCtrl = $(this);

		$listElemCtrl.each(function(ix) {
			var $elemCtrl = $(this);

			$elemCtrl.on('click.openLink', function(ev) {
				var $elemCtrl = $(this);

				var $seriesElemTarg = $elemCtrl.closest('tr').find('.col-data a');	
				$seriesElemTarg.each(function() {
					openBlankWinWithPopUp($(this).attr('href'), $(this).parent().data('colidx'));
					return true;
				});

				ev.preventDefault();
				return false;
			});
		});
	};

	$.fn.recieveDragAndDrop = function(options) {
		var settings = {
			selectorCtrlInputParam: '#form-input-url #ctrl-input-url',
			selectorFormInputParam: '#form-input-url'
		};

		var $elemRecieve = this;
		var objectRecieved;
		var contentRecieved;

		$elemRecieve.on('dragover', function(ev) {
			ev.preventDefault();
			$(this).addClass('dropping');
		});
		$elemRecieve.on('dragleave', function(ev) {
			ev.preventDefault();
			$(this).removeClass('dropping');
		});

		$elemRecieve.on('drop', function(ev) {
			ev.preventDefault();
			$(this).removeClass('dropping');

			objectRecieved = ev.originalEvent.dataTransfer.getData("url");

			if(! objectRecieved) { alert('Not URL'); return true; }

			$(settings.selectorCtrlInputParam).val(objectRecieved);

			$(settings.selectorFormInputParam).trigger('submit');
		});

		return this;
	};

	$.fn.submitUrl = function() {
		var $elemForm = $(this);

		var $elemInputParam = $elemForm.find('#ctrl-input-url');

		var valueParam = $elemInputParam.val();
		var seriesUrl = [];	
		var $seriesDomain = [];
		var pathUrl;

		$('.cell-header-domain').each(function(ix) {
			var $elemTarg = $(this);
			pathUrl = valueParam.replace(/^.*?\/\/.*?\//, '')
			seriesUrl.push($elemTarg.text() + pathUrl);
		})

		$.each(seriesUrl, function(ix, value) {
			openBlankWinWithPopUp(value, ix + 1);
			return true;
		});

		return false;
	};

})(jQuery);
</script>
<!-- ================================================================ load JS -->
</head>

<body>

<h1 id="title-page"><?php echo($titlePage); ?></h1>

<div class="menu-drawal" id="section-manualinput-param" data-openclose="open">
<div class="ctrl-drawal">
<a href="javascript: void(0);" class="content-ctrl content-open">Close</a>
<a href="javascript: void(0);" class="content-ctrl content-closed">Input URL</a>
</div>
<div id="section-dd-url">
<p class="content-dd">D &amp; D URL Here</p>
<!-- /section-dd-url --></div>

<div id="section-input-url">
<form id="form-input-url">
<input type="text" id="ctrl-input-url" name="input-url" class="content-dd" placeholder="Enter URL or Path">
</form>
<!-- /section-input-url --></div>
<!-- /section-manualinput-param --></div>

<div id="contents">

<table id="list-url" class="list-data">
<thead>
<tr>
<th>No</th>
<th>右記サイト全リロード<br>（要ポップアップ許可）</th>
<?php foreach($listUrlPrefix as $urlPrefix) : ?>
<th class="cell-header-domain"><?php echo($urlPrefix . '/'); ?></th>
<?php endforeach ?>
</tr>
</thead>

<?php $ixRow = 1; ?>
<?php foreach(listFile(getcwd() . $dirFilter) as $file) : ?>
<?php
$htmlTarg = str_replace('.smc', '', $file);
if(! file_exists($htmlTarg)) { continue; }
$contHtml = file_get_contents($htmlTarg);
if(empty($contHtml)) { continue; }
$titleTarg = preg_replace('/^.*<title>(.*?)<\/title>.*$/si', '${1}', $contHtml);
if($titleTarg == $contHtml ) { $titleTarg = 'No &lt;Title&gt;'; }
?>

<tr>
<th class="cell-num"><?php echo($ixRow); ?></th>
<th class="cell-title-row"><a href="ctrl-totalof-row-<?php echo($ixRow); ?>" target="_blank"><span class="cont-title-row"><?php echo(preg_replace('/ \|.*?$/', '', $titleTarg)); ?> </span>→</a></th>

<?php $ixDataCol = 1; ?>
<?php foreach($listUrlPrefix as $urlPrefix) : ?>
<?php $urlTarg = str_replace('.smc', '', str_replace($pathPrefix, $urlPrefix, $file)); ?>
<td class="col-data" data-colidx="<?php echo($ixDataCol); ?>"><a href="<?php echo($urlTarg); ?>" target="_blank"><?php echo($urlTarg); ?></a></td>
<?php $ixDataCol ++; ?>
<?php endforeach ?>
</tr>
<?php $ixRow ++; ?>
<?php endforeach ?>

</table>

<!-- /contents --></div>
</body>
</html>

