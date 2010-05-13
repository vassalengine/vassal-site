<?php

$url = 'http://test.nomic.net/blog/feed/';

$rss = file_get_contents($url);
if ($rss === false) {
  die('Something went wrong.');
}

$xml = simplexml_load_string($rss);

$cal = array();

foreach ($xml->channel->item as $item) {
  $date = date_parse($item->pubDate);

  $cal[$date['month']][$date['day']][] = array(
    'title' => $item->title,
    'link'  => $item->link
  );
}

$mname = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
               'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

krsort($cal, SORT_NUMERIC);

foreach ($cal as $month => $days) {
  krsort($days, SORT_NUMERIC);

  foreach ($days as $day => $items) {

    echo "<li class=\"day\">\n";
    echo "<div class=\"date\">{$mname[$month-1]}<br/>$day</div>\n";
    echo "<ul class=\"events\">\n";

    foreach ($items as $item) {
      echo "<li><a href=\"{$item['link']}\">{$item['title']}</a></li>\n";
    }

    echo "</ul>\n</li>\n";
  }
}

?>
