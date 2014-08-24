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

  <?php $version = trim(file_get_contents('util/release-stable')); ?>

  <h2><abbr>Vassal</abbr> <?php print($version); ?></h2>
  <p>This is the current release. <abbr>Vassal</abbr> <?php print($version); ?> contains a large number of changes over 3.1. See the <a href="https://sourceforge.net/projects/vassalengine/files/VASSAL-current/VASSAL-<?php print($version); ?>/README/view">release notes</a> for details.</p>

  <table class="dl">
    <tr>
      <td>
        <img src="/images/linux.png" alt="Linux" />
        <dl>
          <dt>Linux</dt>
          <dd>Download <a href="http://downloads.sourceforge.net/vassalengine/VASSAL-<?php print($version); ?>-linux.tar.bz2">tarball</a> (16MB) <br/>Unpack it and run <code>VASSAL.sh</code></dd>
        </dl>
      </td>
      <td>
        <span style="font-size: 48px;">*</span>
        <dl>
          <dt>Other</dt>
          <dd>Download <a href="http://downloads.sourceforge.net/vassalengine/VASSAL-<?php print($version); ?>-other.zip">ZIP archive</a> (16MB)<br/>Unpack it and run <code>VASSAL.sh</code></dd>
        </dl>
      </td>
    </tr>
    <tr>
      <td>
        <img src="/images/macosx.png" alt="Mac OS X"/>
        <dl>
          <dt>Mac OS X</dt>
          <dd>Download <a href="http://downloads.sourceforge.net/vassalengine/VASSAL-<?php print($version); ?>-macosx.dmg">disk image</a> (20MB)</dd>
        </dl>
      </td>
      <td>
        <span style="font-size: 24px;">{}</span>
        <dl>
          <dt>Source</dt>
          <dd>Download <a href="http://downloads.sourceforge.net/vassalengine/VASSAL-<?php print($version); ?>-src.zip">ZIP archive</a> (17MB)<br/>Look <a href="">here</a> for build instructions.</dd>
        </dl>
      </td>
    </tr>
    <tr>
      <td>
        <img src="/images/windows.png" alt="Windows" />
        <dl>
          <dt>Windows</dt>
          <dd>Download and run <a href="http://downloads.sourceforge.net/vassalengine/VASSAL-<?php print($version); ?>-windows.exe">installer</a> (16MB)</dd>
        </dl>
      </td>
      <td></td>
    </tr>
  </table>

  <h3>Notes</h3>

  <h4>All Operating Systems</h4>
  <p><abbr>Vassal</abbr> <?php print($version); ?> requires Java Runtime Envionment (JRE) 5 or later. See the OS-specific notes below for how to get an appropriate JRE for your system.</p>

  <h4>Linux</h4>
  <p>Most Linux distributions ship a JRE. If you do not have a JRE installed, you must install one before running <abbr>Vassal</abbr>. On Fedora: <code>yum install java-1.7.0-openjdk</code>, and on Ubuntu: <code>apt-get openjdk-7-jre</code>.</p>

  <h4>Mac OS X</h4>
  <p>Mac OS X comes with Java already installed. However, if you use OS X 10.3 or earlier, Apple has decided not to provide you with Java 5 or later, and so the last version of <abbr>Vassal</abbr> you will be able to run without upgrading your OS is 2.9.9.</p>

  <h4>Windows</h4>
  <p>If you do not already have Java 5 or later, the <abbr>Vassal</abbr> installer will download and install it for you. Alternatively, you may install Java yourself before installing <abbr>Vassal</abbr>. Current versions of Java are available at <a href="http://www.java.com">java.com</a>.</p>

  <h4>Other</h4>
  <p>This is the package to use if you run an OS without a dedicated package. In order to run <abbr>Vassal</abbr>, you will need to obtain a JRE for your OS.</p>

  <h4>Source</h4>
  <p>This is the source code for <abbr>Vassal</abbr>. If you want to <em>use</em> <abbr>Vassal</abbr>, you should consider installing the pre-built package for your operating system, found above. If, instead, you are interested in doing development work on <abbr>Vassal</abbr>, you might instead want to check out the current code from our <a href="https://sourceforge.net/p/vassalengine/svn/HEAD/tree/VASSAL-src/">source repository</a>.</p>

  <h2>Older Releases</h2>
  <p>Older releases of <abbr>Vassal</abbr> are available in our <a href="/releases/">release archive</a>. We do not recommend older releases for normal use. If you find it necessary to use an older release due to a flaw in the current release, please <a href="/tracker/">file a bug report</a>.</p>

  <h2>Development Snapshots</h2>
  <p><abbr>Vassal</abbr> is under constant development. Current development snapshots are available <a href="http://vassalengine.sourceforge.net/builds/">here</a>. The development build named <code>VASSAL-x.y.z-svnNNNN-branch</code> is a development build for version <code>x.y.z</code> of <abbr>Vassal</abbr> based on revision <code>NNNN</code> on branch <code>branch</code> in our Subversion repository. We do not provide support for development builds, nor do we guarantee the they even work. If you use one for regular play or module design, you do so at your own risk. However, we greatfully accept bug reports from <em>current</em> development builds.</p>
 

</div>
</div>

<?php include('inc/footer.shtml'); ?>
</body>
</html>
