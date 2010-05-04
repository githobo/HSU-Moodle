<div class="yiiForm">

<p>
Fields with <span class="required">*</span> are required.
</p>

<?php echo CHtml::beginForm(); ?>

<?php echo CHtml::errorSummary($model); ?>

<div class="simple">
<?php echo CHtml::activeLabelEx($model,'content'); ?>
<?php echo CHtml::activeTextArea($model,'content',array('rows'=>6, 'cols'=>50)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'contentDisplay'); ?>
<?php echo CHtml::activeTextArea($model,'contentDisplay',array('rows'=>6, 'cols'=>50)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'status'); ?>
<?php echo CHtml::activeTextField($model,'status'); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'createTime'); ?>
<?php echo CHtml::activeTextField($model,'createTime'); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'author'); ?>
<?php echo CHtml::activeTextField($model,'author',array('size'=>60,'maxlength'=>128)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'email'); ?>
<?php echo CHtml::activeTextField($model,'email',array('size'=>60,'maxlength'=>128)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'url'); ?>
<?php echo CHtml::activeTextField($model,'url',array('size'=>60,'maxlength'=>128)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'postId'); ?>
<?php echo CHtml::activeTextField($model,'postId'); ?>
</div>

<div class="action">
<?php echo CHtml::submitButton($update ? 'Save' : 'Create'); ?>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->