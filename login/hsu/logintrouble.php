<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="en-us" />

<title>Login Trouble?</title>

<link rel='stylesheet' type='text/css' media='all' href='styles/main.css' />
<style type='text/css' media='screen'>
@import "styles/main.css";
</style>
<script type="text/javascript" src="scripts/main.js"></script>
</head>

<body id="main" onLoad="toForm()">

<div id="container">

<div id="branding">
<p id="wordmark"><a href="http://www.humboldt.edu"><img src="images/wordmark.gif" alt="Humboldt State University" /></a></p>
</div>

<div class="skipper"><a href="#nav-secondary">Skip to secondary navigation</a></div><div class="skipper"><a href="#text">Skip to content</a></div>

<div id="nav-main">

    	<ul>
          <il><div id="login">
<h2 class="login">Login to Moodle</h2>
<?php
    if(!empty($_REQUEST['errorcode'])){
        print '<font color="red">';
        switch($_REQUEST['errorcode']){
            case '1':
                print 'You must enable cookies in your browser.';
                break;
            case '2':
            case '3':
                print 'Your HSU User Name and/or HSU Password were incorrect.';
                break;
            case '4':
                print 'Your previous session has timed out.';
                break;
        }
        print '</font><br />';
    }
?>
<form enctype="multipart/form-data" method="post" action="../index.php" id="loginform" name="loginform">
  <label for="username">HSU User Name: </label>
  <br /><input type="text" id="username" name="username" size="15"/><br />
  <label for="password">HSU Password: </label>
  <br /><input type="password" id="password" name="password" size="15"/><br />
  <input type="submit" name="Login" value="Login" />
</form>
<?php include('../../config.php');
      if ($CFG->guestloginbutton) {  ?>
      <div class="subcontent guestsub">
        <div class="desc">
        </div>
        <form action="../index.php" method="post" id="guestlogin">
          <div class="guestform">
            <input type="hidden" name="username" value="guest" />
            <input type="hidden" name="password" value="guest" />
            <input type="hidden" name="testcookies" value="1" />
            <input type="submit" value="<?php print_string("loginguest") ?>" />
          </div>
        </form>
      </div>
<?php } ?>
<a href="logintrouble.php">Login Trouble?</a>
</div></il>
		<li><a href="index.php">Home</a></li>
		<li><a href="http://www.humboldt.edu/its/moodle">Moodle Support</a></li>
		<li><a href="http://moodle.org">Moodle.org</a></li>
    	<li><a href="mailto:mdlsos@humboldt.edu">Contact Us</a></li>
		</ul>
	</div>

<div id="content">

<h1>Login Trouble?</h1>

<div id="news">

    <br/>
<p>If you are having trouble logging in to Moodle please check the following:
    <ul>
        <li>Verify that you are using your HSU user name (e.g. mdl42) in the user name field and your HSU Password</li>
        <li>Verify that your browser is configured to accept cookies</li>
    </ul>
</p>

<p>
    If you have forgotten your HSU user name or password you can visit the <a href="http://www.humboldt.edu/reset">Password Reset</a> page.
</p>

<p>
    If you still can't get logged in please send an email to <a href="mailto:help@humboldt.edu">help@humboldt.edu</a> or call the Help Desk at 707.826.HELP (4357).
</p>

</div>
</div>
<div class="clearer"></div>
</div>

<div id="contact"><p>Library 315 • 1 Harpst St., Arcata, CA 95521 • 707.826.3633 • <a href="mailto:mdlsos@humboldt.edu">Contact Us</a>.</p></div>

</body>
</html>