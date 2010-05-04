<h2>View Comment <?php echo $model->id; ?></h2>

<div class="actionBar">
[<?php echo CHtml::link('Comment List',array('list')); ?>]
[<?php echo CHtml::link('New Comment',array('create')); ?>]
[<?php echo CHtml::link('Update Comment',array('update','id'=>$model->id)); ?>]
[<?php echo CHtml::linkButton('Delete Comment',array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure?')); ?>
]
[<?php echo CHtml::link('Manage Comment',array('admin')); ?>]
</div>

<table class="dataGrid">
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('content')); ?>
</th>
    <td><?php echo CHtml::encode($model->content); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('contentDisplay')); ?>
</th>
    <td><?php echo CHtml::encode($model->contentDisplay); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('status')); ?>
</th>
    <td><?php echo CHtml::encode($model->status); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('createTime')); ?>
</th>
    <td><?php echo CHtml::encode($model->createTime); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('author')); ?>
</th>
    <td><?php echo CHtml::encode($model->author); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('email')); ?>
</th>
    <td><?php echo CHtml::encode($model->email); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('url')); ?>
</th>
    <td><?php echo CHtml::encode($model->url); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('postId')); ?>
</th>
    <td><?php echo CHtml::encode($model->postId); ?>
</td>
</tr>
</table>
