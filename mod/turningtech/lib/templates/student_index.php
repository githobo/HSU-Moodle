<div id='turningtech-device-page'>

<?php echo turningtech_show_messages(); ?>

<p><?php echo get_string('toreceivecredit','turningtech'); ?></p>

<div class="rw-image-container">
	<div class="responsecard-container">
		<h3>
			<a href='#responsecard' class="responsecard-form-link">
		    <?php echo get_string('responsecard','turningtech'); ?>
		  </a>
		</h3>
		<p><?php echo get_string('handheldclickerdevice','turningtech'); ?></p>
		<a href='#responsecard' class='responsecard-form-link'>
			<img src='http://www.turningtechnologies.com/images/rcard1and2_varient.jpg' />
		</a>
	</div>
	<div class="responseware-container">
		<h3>
		  <a href='#responseware' class="responseware-form-link">
		    <?php echo get_string('responseware','turningtech'); ?>
		  </a>
		</h3>
		<p><?php echo get_string('onyourowndevice','turningtech'); ?></p>
		<a href='#responseware' class="responseware-form-link">
			<img src='http://www.turningtechnologies.com/images/rware.jpg' />
		</a>
	</div>
</div>
<div class="clear-both"></div>

<hr class="device-divider" />

<div class="my-devices-container">
  <h3><?php echo get_string('myregistereddevice','turningtech'); ?></h3>
  
  <?php echo $device_list; ?>
</div>

<hr class="device-divider" />

<h3><?php echo get_string('registeradevice','turningtech'); ?></h3>
<p><?php echo get_string('forhelp','turningtech'); ?></p>

<script type="text/javascript">
	var leaveOpen = false; 
</script>

<div class="form-container">
  <a id="responsecard-anchor" name="responsecard"></a>
  <div id="responsecard-collapse-group">
		<h3 class="uncollapsed">
	  	<a href='#responsecard' class='responsecard-form-link'>
	      <?php echo get_string('ifyouareusingresponsecard','turningtech'); ?>
	  	</a>
		</h3>
		<div class="uncollapsed">
	  	<p><?php echo get_string('responsecardheadertext','turningtech'); ?></p>
	  	<div class="responsecard-group">
		  	<table>
		  		<tr>
			  		<td><?php $editform->display(); ?></td>
		  			<td><img class="enterid" src="http://www.turningtechnologies.com/images/RCRF_StudentID3.jpg" /></td>
	 	 		</tr>
	  		</table>
    		<div class="clear-both"></div>
    	</div>
		</div>
	</div>

	<a id="responseware-anchor" name="responseware"></a>
	<div id="responseware-collapse-group">
		<h3 class="uncollapsed">
			<a href='#responseware' class='responseware-form-link'>
				<?php echo get_string('ifyouareusingresponseware','turningtech'); ?>
			</a>
		</h3>
		<div class="uncollapsed">
	  	<p><?php echo get_string('responsewareheadertext','turningtech'); ?></p>
	  	<div class="responseware-group">
      	<?php $rwform->display(); ?>
      	<p class="responseware-join-link"><?php echo get_string('tocreateanaccount','turningtech',TurningHelper::getResponseWareUrl()); ?></p>
      	<div class="clear-both"></div>
    	</div>
  	</div>
  </div>
</div> <!--  /form-container -->

</div><!--  /turningtech-device-page -->
