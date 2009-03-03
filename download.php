<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head profile="http://www.w3.org/2005/10/profile">
  <link rel="stylesheet" type="text/css" href="/style.css"/>
  <link rel="stylesheet" type="text/css" href="/site.css"/>
  <link rel="icon" type="image/png" href="/images/VASSAL.png"/>

  <style type="text/css">
dt {
  font-weight: bold;
}

dd{
  margin: 0.5em 0 0.5em 0px;
  padding-left: 10px;
}

dl{
  padding: 0em 2em 1em 1em;
  display: inline-block;
  vertical-align: middle;
}

table {
  margin: 0 auto;
}

code {
  color: blue;
}

  </style>

  <title>Download VASSAL</title>
</head>
<body>
<div id="vassal-header">
  <div id="vassal-logo">
    <a href="/index.php"><img src="/images/header.png"/></a>
  </div>
  <?php virtual('/navigation.shtml'); ?>
</div>

<div id="content">
<div class="content_box_full">
  <h1>Download <acronym>Vassal</acronym></h1>

  <p>Download <acronym>Vassal</acronym>, the free, open-source boardgame engine. <acronym>Vassal</acronym> runs on Linux, Mac OS X, Windows, and any other system which has a Java JRE. Find your operating system below for instructions for downloading and installing <acronym>Vassal</acronym>.</p>

  <h2><acronym>Vassal</acronym> 3.1.0</h2>
  <p>This is the current release. <acronym>Vassal</acronym> 3.1.0 contains a large number of changes over 3.0.17. See the <a href="">release notes</a> for details.</p>

  <table>
    <tr>
      <td>
        <img src="/images/linux.png"/>
        <dl>
          <dt>Linux</dt>
          <dd>Download <a href="http://downloads.sourceforge.net/vassalengine/VASSAL-3.1.0-linux.zip">ZIP archive</a> (13MB) <br/>Unpack it and run <code>VASSAL.sh</code></dd>
        </dl>
      </td>
      <td>
        <span style="font-size: 48px;">*</span>
        <dl>
          <dt>Other</dt>
          <dd>Download <a href="http://downloads.sourceforge.net/vassalengine/VASSAL-3.1.0-other.zip">ZIP archive</a> (13MB)<br/>Unpack it and run <code>VASSAL.sh</code></dd>
        </dl>
      </td>
    </tr>
    <tr>
      <td>
        <img src="/images/macosx.png"/>
        <dl>
          <dt>Mac OS X</dt>
          <dd>Download <a href="http://downloads.sourceforge.net/vassalengine/VASSAL-3.1.0-macosx.dmg">disk image</a> (16MB)</dd>
        </dl>
      </td>
      <td>
        <span style="font-size: 24px;">{}</span>
        <dl>
          <dt>Source</dt>
          <dd>Download <a href="http://downloads.sourceforge.net/vassalengine/VASSAL-3.1.0-src.zip">ZIP archive</a> (13MB)<br/>Look <a href="">here</a> for build instructions.</dd>
        </dl>
      </td>
    </tr>
    <tr>
      <td>
        <img src="/images/windows.png"/>
        <dl>
          <dt>Windows</dt>
          <dd>Download and run <a href="">installer</a> (12MB)</dd>
        </dl>
      </td>
    </tr>
  </table>

  <h3>Notes</h3>

  <h4>All Operating Systems</h4>
  <p><acronym>Vassal</acronym> 3.1.0 requires Java Runtime Envionment (JRE) 5 or later. See the OS-specific notes below for how to get an appropriate JRE for your system.</p>

  <h4>Linux</h4>
  <p>Most Linux distributions ship a JRE. If you do not have a JRE installed, you must install one before running <acronym>Vassal</acronym>. On Fedora: <code>yum install java-1.6.0-openjdk</code>, and on Ubuntu: <code>apt-get openjdk-6-jre</code>.</p>

  <h4>Mac OS X</h4>
  <p>Mac OS X comes with Java already installed. However, if you use OS X 10.3 or earlier, Apple has decided not to provide you with Java 5 or later, and so the last version of <acronym>Vassal</acronym> you will be able to run without upgrading your OS is 2.9.9.</p>

  <h4>Windows</h4>
  <p>If you do not already have Java 5 or later, the <acronym>Vassal</acronym> installer will download and install it for you. Alternatively, you may install Java yourself before installing <acronym>Vassal</acronym>. Current versions of Java are available at <a href="http://www.java.com">java.com</a>.</p>

  <h4>Other</h4>
  <p>This is the package to use if you run an OS without a dedicated package.</p>

  <h4>Source</h4>
  <p>This is the source code for <acronym>Vassal</acronym>. If you want to <em>use</em> <acronym>Vassal</acronym>, you should consider installing the pre-built package for your operating system, found above. If, instead, you are interested in doing development work on <acronym>Vassal</acronym>, you might instead want to check out the current code from our <a href="">source repository</a>.</p>

  <h2>Older Releases</h2>
  <p>Older releases of <acronym>Vassal</acronym> are available in our <a href="/releases/">release archive</a>. We do not recommend older releases for normal use. If you find it necessary to use an older release due to a flaw in the current release, please <a href="/tracker/">file a bug report</a>.</p>

  <h2>Development Snapshots</h2>
  <p><acronym>Vassal</acronym> is under constant development. Current development snapshots are available <a href="/builds/">here</a>. The development build named <code>VASSAL-x.y.z-svnNNNN-branch</code> is a development build for version <code>x.y.z</code> of <acronym>Vassal</acronym> based on revision <code>NNNN</code> on branch <code>branch</code> in our Subversion repository. We do not provide support for development builds, nor do we guarantee the they even work. If you use one for regular play or module design, you do so at your own risk. However, we greatfully accept bug reports from <em>current</em> development builds.</p>
 

</div>
</div>

<?php virtual('/footer.shtml'); ?>
</body>
</html>
