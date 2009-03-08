<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head profile="http://www.w3.org/2005/10/profile">
  <link rel="stylesheet" type="text/css" href="/style.css"/>
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

<?php

require_once('sso/ssolib.php');
require_once('sso/NewsDB.php');

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  if (!is_numeric($id) || $id < 0) unset($id);
}

if (isset($_GET['from'])) {
  $from = $_GET['from'];
  if (!is_numeric($from) || $from < 0) unset($from);
}

if (isset($_GET['to'])) {
  $to = $_GET['to'];
  if (!is_numeric($to) || $to < 0) unset($to);
}

$rows = array();
$prev = -1;
$next = -1;

try {
  $news = new NewsDB();

  $limit = 10;

  $limit++;
  if (isset($id)) {
    $query = "SELECT id, DATE_FORMAT(date,\"%b\") AS month, DAYOFMONTH(date) AS day, headline, text FROM news WHERE id = $id";
  }
  else if (isset($from)) {
    $query = "SELECT id, DATE_FORMAT(date,\"%b\") AS month, DAYOFMONTH(date) AS day, headline, text FROM news WHERE id >= $from ORDER BY date ASC LIMIT $limit";
  }
  else if (isset($to)) {
    $query = "SELECT id, DATE_FORMAT(date,\"%b\") AS month, DAYOFMONTH(date) AS day, headline, text FROM news WHERE id <= $to ORDER BY date DESC LIMIT $limit";
  }
  else {
    $query = "SELECT id, DATE_FORMAT(date,\"%b\") AS month, DAYOFMONTH(date) AS day, headline, text FROM news ORDER BY date DESC LIMIT $limit";
  }
  $limit--;

  $rows = $news->read_all($query);
  if (isset($from)) $rows = array_reverse($rows);

  if (count($rows) > 0) {
    # are there entries newer thant the display window?
    $next = -1;
    if (isset($from) && count($rows) > $limit) {
      $next = $rows[1]['id'] + 1;
    }
    else if (isset($to)) {
      $query = "SELECT 1 FROM news WHERE id > $to LIMIT 1";
      if (count($news->read_all($query)) > 0) {
        $next = $to + 1;
      }
    }

    # are there entries older than the display window?
    $prev = -1;
    if (isset($to) && count($rows) > $limit) {
      $prev = $rows[count($rows)-2]['id'] - 1;
    }
    else if (!isset($id)) {
      if (!isset($from)) $from = $rows[count($rows)-1]['id'];

      $query = "SELECT 1 FROM news WHERE id < $from LIMIT 1";
      if (count($news->read_all($query)) > 0) {
        $prev = $from - 1;
      }
    }
  }
}
catch (ErrorException $e) {
  warn($e->getMessage());
  echo '</div></div>';
  print_bottom();
  exit;
}

# print the 'newer' link, if necessary
if ($next >= 0) {
  echo '<em><a class="news-nav" href="?from=', $next, '">newer</a></em>';
}

echo '<ul class="news">';

$month = '';
$day = 0;

# print each item
foreach ($rows as $item) {
  if ($day != $item['day'] || $month != $item['month']) {
    if ($day != 0) echo '</ul></li>';

    echo '<li class="day"><div class="date">',
        $item['month'], '<br/>', $item['day'], '</div><ul class="events">';

    $day = $item['day'];
    $month = $item['month'];
  }

  echo '<li><h2><a id="_', $item['id'], '">', $item['headline'],
       '</a></h2><p>', $item['text'], '</p></li>'; 
} 
  
echo '</ul></li></ul>';

# print the 'older' link, if necessary
if ($prev >= 0) {
  echo '<em><a class="news-nav" href="?to=', $prev, '">older</a></em>';
}
?>

  </div>
</div>
<?php include('inc/footer.shtml'); ?>
</body>
</html>
