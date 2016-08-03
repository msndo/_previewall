<!-- ================================================================ load PHP controller -->
//=require app/controller.php
<!-- ================================================================ /load PHP controller -->
<!DOCTYPE html>

<html lang="ja">

<head>
<title><?php echo($titlePage); ?></title>
<meta charset="utf-8">

<!-- ================================================================ load CSS -->
<style>
//=include css/screen.css
</style>
<!-- ================================================================ /load CSS -->

<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>

<!-- ================================================================ load JS -->
<script>
//=include js/screen.js
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

