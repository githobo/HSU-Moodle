<h2>Managing Comment</h2>

<div class="actionBar">
[<?php echo CHtml::link('Comment List',array('list')); ?>]
[<?php echo CHtml::link('New Comment',array('create')); ?>]
</div>

<table class="dataGrid">
  <thead>
  <tr>
    <th><?php echo $sort->link('id'); ?></th>
    <th><?php echo $sort->link('status'); ?></th>
    <th><?php echo $sort->link('createTime'); ?></th>
    <th><?php echo $sort->link('author'); ?></th>
    <th><?php echo $sort->link('email'); ?></th>
    <th><?php echo $sort->link('url'); ?></th>
    <th><?php echo $sort->link('postId'); ?></th>
	<th>Actions</th>
  </tr>
  </thead>
  <tbody>
<?php foreach($models as $n=>$model): ?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
    <td><?php echo CHtml::link($model->id,array('show','id'=>$model->id)); ?></td>
    <td><?php echo CHtml::encode($model->status); ?></td>
    <td><?php echo CHtml::encode($model->createTime); ?></td>
    <td><?php echo CHtml::encode($model->author); ?></td>
    <td><?php echo CHtml::encode($model->email); ?></td>
    <td><?php echo CHtml::encode($model->url); ?></td>
    <td><?php echo CHtml::encode($model->postId); ?></td>
    <td>
      <?php echo CHtml::link('Update',array('update','id'=>$model->id)); ?>
      <?php echo CHtml::linkButton('Delete',array(
      	  'submit'=>'',
      	  'params'=>array('command'=>'delete','id'=>$model->id),
      	  'confirm'=>"Are you sure to delete #{$model->id}?")); ?>
	</td>
  </tr>
<?php endforeach; ?>
  </tbody>
</table>
<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>