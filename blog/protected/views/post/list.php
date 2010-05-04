<h2>Post List</h2>

<div class="actionBar">
[<?php echo CHtml::link('New Post',array('create')); ?>]
[<?php echo CHtml::link('Manage Post',array('admin')); ?>]
</div>

<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>

<?php foreach($models as $n=>$model): ?>
<div class="item">
<?php echo CHtml::encode($model->getAttributeLabel('id')); ?>:
<?php echo CHtml::link($model->id,array('show','id'=>$model->id)); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('title')); ?>:
<?php echo CHtml::encode($model->title); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('content')); ?>:
<?php echo CHtml::encode($model->content); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('contentDisplay')); ?>:
<?php echo CHtml::encode($model->contentDisplay); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('tags')); ?>:
<?php echo CHtml::encode($model->tags); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('status')); ?>:
<?php echo CHtml::encode($model->status); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('createTime')); ?>:
<?php echo CHtml::encode($model->createTime); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('updateTime')); ?>:
<?php echo CHtml::encode($model->updateTime); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('commentCount')); ?>:
<?php echo CHtml::encode($model->commentCount); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('authorId')); ?>:
<?php echo CHtml::encode($model->authorId); ?>
<br/>

</div>
<?php endforeach; ?>
<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>