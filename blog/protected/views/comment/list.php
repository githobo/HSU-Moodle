<h2>Comment List</h2>

<div class="actionBar">
[<?php echo CHtml::link('New Comment',array('create')); ?>]
[<?php echo CHtml::link('Manage Comment',array('admin')); ?>]
</div>

<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>

<?php foreach($models as $n=>$model): ?>
<div class="item">
<?php echo CHtml::encode($model->getAttributeLabel('id')); ?>:
<?php echo CHtml::link($model->id,array('show','id'=>$model->id)); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('content')); ?>:
<?php echo CHtml::encode($model->content); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('contentDisplay')); ?>:
<?php echo CHtml::encode($model->contentDisplay); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('status')); ?>:
<?php echo CHtml::encode($model->status); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('createTime')); ?>:
<?php echo CHtml::encode($model->createTime); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('author')); ?>:
<?php echo CHtml::encode($model->author); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('email')); ?>:
<?php echo CHtml::encode($model->email); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('url')); ?>:
<?php echo CHtml::encode($model->url); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('postId')); ?>:
<?php echo CHtml::encode($model->postId); ?>
<br/>

</div>
<?php endforeach; ?>
<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>