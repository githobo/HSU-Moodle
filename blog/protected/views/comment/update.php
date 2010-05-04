<h2>Update Comment <?php echo $model->id; ?></h2>

<div class="actionBar">
[<?php echo CHtml::link('Comment List',array('list')); ?>]
[<?php echo CHtml::link('New Comment',array('create')); ?>]
[<?php echo CHtml::link('Manage Comment',array('admin')); ?>]
</div>

<?php echo $this->renderPartial('_form', array(
	'model'=>$model,
	'update'=>true,
)); ?>