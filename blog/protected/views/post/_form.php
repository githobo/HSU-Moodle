<div class="yiiForm">

<p>
Fields with <span class="required">*</span> are required.
</p>

<?php echo CHtml::beginForm(); ?>

<?php echo CHtml::errorSummary($model); ?>

<div class="simple">
<?php echo CHtml::activeLabelEx($model,'title'); ?>
<?php echo CHtml::activeTextField($model,'title',array('size'=>60,'maxlength'=>128)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'content'); ?>
<?php echo CHtml::activeTextArea($model,'content',array('rows'=>6, 'cols'=>50)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'contentDisplay'); ?>
<?php echo CHtml::activeTextArea($model,'contentDisplay',array('rows'=>6, 'cols'=>50)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'tags'); ?>
<?php echo CHtml::activeTextArea($model,'tags',array('rows'=>6, 'cols'=>50)); ?>
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
<?php echo CHtml::activeLabelEx($model,'updateTime'); ?>
<?php echo CHtml::activeTextField($model,'updateTime'); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'commentCount'); ?>
<?php echo CHtml::activeTextField($model,'commentCount'); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'authorId'); ?>
<?php echo CHtml::activeTextField($model,'authorId'); ?>
</div>

<div class="action">
<?php echo CHtml::submitButton($update ? 'Save' : 'Create'); ?>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->