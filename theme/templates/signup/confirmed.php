<?php
global $action;
global $model;

$IC = new Items();
$page_item = $IC->getItem(array("tags" => "page:signup-confirmed", "status" => 1, "extend" => array("user" => true, "tags" => true, "mediae" => true)));
if($page_item) {
	$this->sharingMetaData($page_item);
}

// get signup email
$username = session()->value("signup_email");
session()->reset("signup_email");

?>
<div class="scene signup i:scene">

<? if($page_item):
	$media = $IC->sliceMediae($page_item, "single_media"); ?>
	<div class="article i:article id:<?= $page_item["item_id"] ?>" itemscope itemtype="http://schema.org/Article">

		<? if($media): ?>
		<div class="image item_id:<?= $page_item["item_id"] ?> format:<?= $media["format"] ?> variant:<?= $media["variant"] ?>"></div>
		<? endif; ?>


		<?= $HTML->articleTags($page_item, [
			"context" => false
		]) ?>


		<h1 itemprop="headline"><?= $page_item["name"] ?></h1>

		<? if($page_item["subheader"]): ?>
		<h2 itemprop="alternativeHeadline"><?= $page_item["subheader"] ?></h2>
		<? endif; ?>


		<?= $HTML->articleInfo($page_item, "/signup/confirm/receipt", [
			"media" => $media,
		]) ?>


		<? if($page_item["html"]): ?>
		<div class="articlebody" itemprop="articleBody">
			<?= preg_replace("/{username}/", $username, $page_item["html"]) ?>
		</div>
		<? endif; ?>
	</div>

<? else:?>

	<h1>Tak!</h1>

		<?= $HTML->serverMessages(["type" => "error"]) ?>

		<p>Du kan nu <a href="/login">logge ind</a>.</p>

<? endif; ?>


</div>
