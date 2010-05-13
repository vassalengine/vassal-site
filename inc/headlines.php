<?php

$url = 'http://test.nomic.net/blog/feed/';

$rss = file_get_contents($url);
if ($rss === false) {
  die('Something went wrong.');
}

$xml = simplexml_load_string($rss);

$mname = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
               'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

$month = $day = 0;

foreach ($xml->channel->item as $item) {
  $date = date_parse($item->pubDate);

  if ($date['month'] != $month || $date['day'] != $day) {
    if ($day != 0) {
      # end the previous day
      echo "</ul>\n</li>\n";
    }

    $month = $date['month'];
    $day = $date['day'];

    echo "<li class=\"day\">\n";
    echo "<div class=\"date\">{$mname[$month-1]}<br/>$day</div>\n";
    echo "<ul class=\"events\">\n";
  }

  echo "<li><a href=\"{$item['link']}\">{$item['title']}</a></li>\n";
}

?>
