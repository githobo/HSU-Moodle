<h2>View Post <?php echo $model->id; ?></h2>

<div class="actionBar">
[<?php echo CHtml::link('Post List',array('list')); ?>]
[<?php echo CHtml::link('New Post',array('create')); ?>]
[<?php echo CHtml::link('Update Post',array('update','id'=>$model->id)); ?>]
[<?php echo CHtml::linkButton('Delete Post',array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure?')); ?>
]
[<?php echo CHtml::link('Manage Post',array('admin')); ?>]
</div>

<table class="dataGrid">
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('title')); ?>
</th>
    <td><?php echo CHtml::encode($model->title); ?>
</td>
</tr>
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
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('tags')); ?>
</th>
    <td><?php echo CHtml::encode($model->tags); ?>
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
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('updateTime')); ?>
</th>
    <td><?php echo CHtml::encode($model->updateTime); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('commentCount')); ?>
</th>
    <td><?php echo CHtml::encode($model->commentCount); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('authorId')); ?>
</th>
    <td><?php echo CHtml::encode($model->authorId); ?>
</td>
</tr>
</table>
