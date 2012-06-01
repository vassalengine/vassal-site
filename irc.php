<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head profile="http://www.w3.org/2005/10/profile">
  <link rel="stylesheet" type="text/css" href="/css/style.css"/>
  <link rel="stylesheet" type="text/css" href="/css/site.css"/>
  <link rel="icon" type="image/png" href="/images/VASSAL.png"/>
  <title>VASSAL on IRC</title>
</head>
<body>
<?php include('inc/header.php'); ?>

<div id="content">
  <div id="content_box_full">
    <h1>#vassal IRC</h1>

    <p>We maintain the #vassal IRC channel at <a href="irc://chat.freenode.net/%23vassal">chat.freenode.net</a> for real-time discussion of <acronym>VASSAL</acronym>, which you can join using your own IRC client or the web-based one below. If you ask a question, please wait in the channel for a reply; at slow times of day, this could be some minutes. See <a href="http://www.irchelp.org/irchelp/irctutorial.html">here</a> for a basic guide to IRC.</p>

    <div id="irc-container">
      <object id="irc-widget" data="http://webchat.freenode.net?channels=vassal&amp;uio=OT10cnVlde" type="text/html"> 
        <!--[if IE]>
          <iframe src="http://webchat.freenode.net?channels=vassal&amp;uio=OT10cnVlde" style="width: 100%; height: 100%; border: none;">
            <p>If you see this message, it means that your browser was unable to display the IRC widget. Please try joining the IRC channel via this <a href="ttp://webchat.freenode.net?channels=vassal&amp;uio=OT10cnVlde">link</a>.</p>
          </iframe>
        <![endif]-->
      </object>
    </div>

  </div>
</div>

<?php include('inc/footer.shtml'); ?>
</body>
</html>
