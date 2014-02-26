<?php

require_once('/usr/share/wordpress/wp-load.php');

$args = array(
  'posts_per_page' => 15,
  'orderby'        => 'post_date',
  'order'          => 'DESC',
  'poststatus'     => 'publish'
);
$posts = wp_get_recent_posts($args);

$mname = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
               'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

$month = $day = 0;

foreach ($posts as $p) {
  $date = date_parse($p['post_date_gmt']);

  if ($date['month'] != $month || $date['day'] != $day) {
    if ($day != 0) {
      # end the previous day
      print("</ul>\n</li>\n");
    }

    $month = $date['month'];
    $day = $date['day'];

    print("<li class=\"day\">\n");
    print("<div class=\"date\">{$mname[$month-1]}<br/>$day</div>\n");
    print("<ul class=\"events\">\n");
  }

  print("<li><a href=\"{$p['guid']}\">{$p['post_title']}</a></li>\n");
}

# end the last day
if ($day != 0) {
  print("</ul>\n</li>\n");
}

?>
