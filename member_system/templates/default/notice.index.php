<?php defined('In33hao') or exit('Access Invalid!');?>
<link href="<?php echo MEMBER_SYSTEM_TEMPLATES_URL; ?>/css/layout.css" rel="stylesheet" type="text/css">
<div class="nch-article-con" style="margin:0 auto 20px; width:960px">
	<h1><?php echo $output['notice']['notice_title']; ?></h1>
  	<h2><?php echo date('Y-m-d H:i', $output['notice']['notice_addtime']); ?></h2>
  	<div class="default">
    	<?php echo html_entity_decode(nl2br($output['notice']['notice_content'])); ?>
  	</div>
</div>