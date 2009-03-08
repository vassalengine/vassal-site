<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<rss version="2.0">
  <channel>
    <title>VASSAL News</title>
    <link>http://www.test.nomic.net</link>
    <description>News about the VASSAL Engine and VASSAL modules</description>
    <language>en-us</language>
<!--    <image>
      <title>VASSAL News</title>
      <url>http://www.test.nomic.net/images/header.png</url>
      <link>http://www.test.nomic.net</link>
    </image>
-->
    <generator>news_rss.php</generator>
    <managingEditor>webmaster@www.test.nomic.net</managingEditor>
    <webMaster>webmaster@www.test.nomic.net</webMaster>
    <docs>http://blogs.law.harvard.edu/tech/rss</docs>
    <ttl>60</ttl>
<?php

require_once('sso/NewsDB.php');

try {
  $baseurl = 'http://www.test.nomic.net/news.php';

  $news = new NewsDB();
  $query = 'SELECT id, DATE_FORMAT(date, "%a, %d %b %Y %T") AS pubdate, headline, text FROM news ORDER BY date DESC LIMIT 10';
  $rows = $news->read_all($query);

  echo "<lastBuildDate>{$rows[0]['pubdate']}</lastBuildDate>";

  foreach ($rows as $item) {
    echo <<<END
    <item>
      <title>{$item['headline']}</title>
      <description><![CDATA[{$item['text']}]]></description>
      <pubDate>{$item['pubdate']} GMT</pubDate>
      <link>$baseurl?id={$item['id']}</link>
      <guid>$baseurl?id={$item['id']}</guid> 
    </item>
END;
  }
}
catch (ErrorException $e) {
  warn($e->getMessage());
}


?>

  </channel>
</rss>
