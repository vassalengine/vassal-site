<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head profile="http://www.w3.org/2005/10/profile">
  <link rel="stylesheet" type="text/css" href="/style.css"/>
  <link rel="stylesheet" type="text/css" href="/css/site.css"/>
  <link rel="alternate" type="application/rss+xml" title="RSS" href="" />
  <link rel="icon" type="image/png" href="images/VASSAL.png"/>
  <title>VASSAL</title>
</head>
<body>
<?php include('inc/header.php'); ?>

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

<?php include('inc/footer.shtml'); ?>
</body>
</html>
