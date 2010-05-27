<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head profile="http://www.w3.org/2005/10/profile">
  <link rel="stylesheet" type="text/css" href="/css/style.css"/>
  <link rel="stylesheet" type="text/css" href="/css/site.css"/>
  <link rel="alternate" type="application/rss+xml" title="RSS" href="/news_rss.php" />
  <link rel="icon" type="image/png" href="/images/VASSAL.png"/>
  <title>VASSAL News</title>
</head>
<body>
<?php include('inc/header.php'); ?>

<div id="content">
  <div class="content_box_full">
  <h1>News <a href="/news_rss.php"><img src="/images/feed-icon-14x14.png" alt="RSS feed" /></a></h1>

  <?php include('/var/www/news/shownews.php'); ?>

  </div>
</div>
<?php include('inc/footer.shtml'); ?>
</body>
</html>
