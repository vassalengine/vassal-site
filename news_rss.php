<?php
  $phpns = array(
    'limit'                        => 15,
    'template'                     => 9,
    'mode'                         => 'rss',
    'order'                        => 'DESC',
    'sef'                          => 'news/',
    'script_link'                  => 'http://www.vassalengine.org/news.php',
  );

  include('/var/www/news/shownews.php');
?>
