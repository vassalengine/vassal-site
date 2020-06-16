<!DOCTYPE html>
<html lang="en-US">
<head>
  <?php include('inc/head.shtml'); ?>
  <title>Download VASSAL</title>
</head>
<body>
<?php include('inc/header.php'); ?>

<div id="content">
<div class="content_box_full">
  <h1>Download <abbr>Vassal</abbr></h1>

  <p>Download <abbr>Vassal</abbr>, the free, open-source boardgame engine. <abbr>Vassal</abbr> runs on Linux, Mac OS X, Windows, and any other system which has a Java JRE. Find your operating system below for instructions for downloading and installing <abbr>Vassal</abbr>.</p>

  <?php
    $version = trim(file_get_contents('util/release-stable'));
    $base_gh_url = "https://github.com/vassalengine/vassal";
    $base_dl_url = "$base_gh_url/releases/download/$version";
  ?>

  <h2><abbr>Vassal</abbr> <?php print($version); ?></h2>
  <p>This is the current release. See the <a href="<?php print("$base_gh_url/tree/$version#readme"); ?>">release notes</a> for details.</p>

  <table class="dl">
    <tr>
      <td>
        <img src="/images/linux.png" alt="Linux" />
        <dl>
          <dt>Linux</dt>
          <dd>Download <a href="<?php print("$base_dl_url/VASSAL-$version-linux.tar.bz2"); ?>">tarball</a> <br/>Unpack it and run <code>VASSAL.sh</code></dd>
        </dl>
      </td>
      <td>
        <span style="font-size: 48px;">*</span>
        <dl>
          <dt>Other</dt>
          <dd>Download <a href="<?php print("$base_dl_url/VASSAL-$version-other.zip"); ?>">ZIP archive</a> <br/>Unpack it and run <code>VASSAL.sh</code></dd>
        </dl>
      </td>
    </tr>
    <tr>
      <td>
        <img src="/images/macosx.png" alt="Mac OS X"/>
        <dl>
          <dt>Mac OS X</dt>
          <dd>Download <a href="<?php print("$base_dl_url/VASSAL-$version-macosx.dmg"); ?>">disk image</a></dd>
        </dl>
      </td>
    </tr>
    <tr>
      <td>
        <img src="/images/windows.png" alt="Windows" />
        <dl>
          <dt>Windows</dt>
          <dd>Download and run <a href="<?php print("$base_dl_url/VASSAL-$version-windows.exe"); ?>">installer</a></dd>
        </dl>
      </td>
      <td></td>
    </tr>
  </table>

  <h3>Notes</h3>

  <h4>All Operating Systems</h4>
  <p><abbr>Vassal</abbr> <?php print($version); ?> requires Java 11 or later. The Mac OS X disk image and the Windows installer come with the version of Java <abbr>Vassal</abbr> will use. For Linux and other operating systems, install Java before running <abbr>Vassal</abbr>.

  <h2>Older Releases</h2>
  <p>Older releases of <abbr>Vassal</abbr> are available in our <a href="/releases/">release archive</a>. We do not recommend older releases for normal use. If you find it necessary to use an older release due to a flaw in the current release, please <a href="/tracker/">file a bug report</a>.</p>

<!--
  <h2>Development Snapshots</h2>
  <p><abbr>Vassal</abbr> is under constant development. Current development snapshots are available <a href="http://vassalengine.sourceforge.net/builds/">here</a>. The development build named <code>VASSAL-x.y.z-svnNNNN-branch</code> is a development build for version <code>x.y.z</code> of <abbr>Vassal</abbr> based on revision <code>NNNN</code> on branch <code>branch</code> in our Subversion repository. We do not provide support for development builds, nor do we guarantee the they even work. If you use one for regular play or module design, you do so at your own risk. However, we greatfully accept bug reports from <em>current</em> development builds.</p>
-->

</div>
</div>

<?php include('inc/footer.shtml'); ?>
</body>
</html>
