<h2>New Comment</h2>

<div class="actionBar">
[<?php echo CHtml::link('Comment List',array('list')); ?>]
[<?php echo CHtml::link('Manage Comment',array('admin')); ?>]
</div>

<?php echo $this->renderPartial('_form', array(
	'model'=>$model,
	'update'=>false,
)); ?>