<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head profile="http://www.w3.org/2005/10/profile">
  <link rel="stylesheet" type="text/css" href="/style.css"/>
  <link rel="alternate" type="application/rss+xml" title="RSS" href="" />
  <link rel="icon" type="image/png" href="images/VASSAL.png"/>
  <title>VASSAL</title>
</head>
<body>
<div id="vassal-page">
  <div id="vassal-header">
    <div id="vassal-logo">
      <a href="/index.php"><img src="images/header.png"/></a>
    </div>

    <div id="vassal-search">
    </div>

    <div id="vassal-login">
<?php
  $key = $_COOKIE['VASSAL_login'];
  if (!empty($key)) { 
    require_once('sso/AuthDB.php');
  
    try {
      $auth = new AuthDB();
      $username = $auth->user_for_cookie($key);
      print '<a href="logout.php?returnto=/news.php">Log out</a>';
    }
    catch (ErrorException $e) {
      print '<a href="login.php?returnto=/news.php">Log in</a>';
    }
  }
  else {
    print '<a href="login.php?returnto=/news.php">Log in</a>';
  }
?>
    </div>
  </div>

  <div id="vassal-navigation">
    <ul id="vassal-nav-list">
      <li><a href="download.html">Download</a></li>
  	  <li><a href="">Modules</a></li>
  	  <li><a href="">FAQ</a></li>
	    <li><a href="">Documentation</a></li>
	    <li><a href="forum/">Forum</a></li>
  	  <li><a href="">News</a></li>
  	  <li><a href="">Help</a></li>
    </ul>
  </div>

  <div id="content">
    <div class="content_box_full">
      <h1>News</h1>
<?php

require_once('sso/NewsDB.php');

try {
  $news = new NewsDB();
  $query = 'SELECT DATE_FORMAT(date,"%b") AS month, DAYOFMONTH(date) AS day, headline, text FROM news ORDER BY date DESC LIMIT 50';
  $rows = $news->read_all($query);

  print '<ul>';

  $day = 0;

  foreach ($rows as $item) {
    if ($day != $item['day']) {
      if ($day != 0) {
        print '</ul></li>';
      }

      print '<li class="day"><div class="date">' .
        $item['month'] . '<br/>' . $item['day'] . '</div><ul class="events">';

    }

    print '<li><h2>' . $item['headline'] . '</h2><p>'
                     . $item['text'] . '</p></li>'; 
  }
  
  print '</ul></li></ul>';
}
catch (ErrorException $e) {
  warn($e->getMessage());
}
?>
    </div>
  </div>
  <?php virtual('/footer.shtml'); ?>
</div>
</body>
</html>
