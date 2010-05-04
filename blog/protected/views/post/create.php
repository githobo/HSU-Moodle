<h2>New Post</h2>

<div class="actionBar">
[<?php echo CHtml::link('Post List',array('list')); ?>]
[<?php echo CHtml::link('Manage Post',array('admin')); ?>]
</div>

<?php echo $this->renderPartial('_form', array(
	'model'=>$model,
	'update'=>false,
)); ?>