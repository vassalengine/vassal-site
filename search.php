<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head profile="http://www.w3.org/2005/10/profile">
  <link rel="stylesheet" type="text/css" href="/css/style.css"/>
  <link rel="stylesheet" type="text/css" href="/css/site.css"/>
  <link rel="icon" type="image/png" href="/images/VASSAL.png"/>
  <title>VASSAL Search</title>

  <style type="text/css">
@import url(http://www.google.com/cse/api/branding.css);

#cse-search-results {
  margin-top: 2em;
}

.search-block {
  margin: 1ex 0;
}

.search-block input[type="submit"] {
  width: 9em;
}

#google-search {
  margin-bottom: -5pt;
}

#search-outer {
  margin: 3em 0;
}

#search-inner {
  margin: 0 auto;
  width: 45em;
}

#content {
  overflow: visible;
}
  </style>
</head>
<body>
<?php include('inc/header.php'); ?>

<div id="content">
<div id="content_box_full">
  <h1>Search</h1>
  <p>Looking for something? You can search our site in four ways: the whole site, only the <a href="/wiki">wiki</a>, only the <a href="/forum">forum</a>, or only the <a href="/tracker">tracker</a>. Additionally, each of the wiki, forum, and tracker provide an advanced search interface for special searches.</p>

<div id="search-outer">
  <div id="search-inner">
<!-- search code from Google -->
    <div class="search-block" id="google-search">
      <div class="cse-branding-right" style="background-color:#FFFFFF;color:#000000">
        <div class="cse-branding-form">
          <form action="" id="cse-search-box-main">
            <div>
              <input type="hidden" name="cx" value="009624425637032310479:wa4dtyo3sma" />
              <input type="hidden" name="cof" value="FORID:10" />
              <input type="hidden" name="ie" value="UTF-8" />
              <input type="text" name="q" size="31" />
              <input type="submit" name="sa" value="Search Site" />
            </div>
          </form>
        </div>
        <div class="cse-branding-logo">
          <img src="http://www.google.com/images/poweredby_transparent/poweredby_FFFFFF.gif" alt="Google" />
        </div>
        <div class="cse-branding-text">
          Custom Search
        </div>
      </div>
    </div>

<!-- search code from MediaWiki -->
    <div class="search-block">
      <form action="/wiki/Special:Search" method="get">
        <div>
          <input name="search" type="text" size="31" />
          <input type='submit' name="fulltext" value="Search Wiki" />
          <a href="/wiki/Special:Search">Advanced search</a>
        </div>
      </form>
    </div>
  
<!-- search code from phpBB -->
    <div class="search-block">
      <form action="/forum/search.php" method="post">
        <div>
          <input name="keywords" type="text" maxlength="128" size="31" /> 
          <input value="Search Forum" type="submit" />
          <a href="/forum/search.php" title="View the advanced search options">Advanced search</a>
        </div>
      </form>
    </div>

<!-- search code from Bugzilla -->
    <div class="search-block">
      <form action="/tracker/buglist.cgi" method="get">
        <div>
          <input type="text" name="quicksearch" size="31" />
          <input type="submit" value="Search Tracker" />
          <a href="/tracker/query.cgi?format=advanced">Advanced search</a>
        </div>
      </form>
    </div>

  </div> 
</div>

    <div id="cse-search-results"></div>
    <script type="text/javascript">
<!--
      var googleSearchIframeName = "cse-search-results";
      var googleSearchFormName = "cse-search-box-main";
      var googleSearchFrameWidth = 600;
      var googleSearchDomain = "www.google.com";
      var googleSearchPath = "/cse";
-->
    </script>
    <script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js"></script>
  </div>
</div>

<?php include('inc/footer.shtml'); ?>
</body>
</html>
