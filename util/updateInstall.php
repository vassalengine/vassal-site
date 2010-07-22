<?php
echo "<pre>";
$version=$_REQUEST['version'];
$jar=$_REQUEST['jar'];
$jarVersion=$_REQUEST['jarVersion'];
if ($jarVersion) {
	echo `cd ../install && ln -fs ../../../ws/${jar}__V$jarVersion.jar VASSAL-$version/lib/${jar}.jar`;
}
else {
	echo `cd ../install && ln -fs ../../../ws/${jar}.jar VASSAL-$version/lib/${jar}.jar`;
}
echo `cd ../install && zip VASSAL-$version.zip VASSAL-$version/lib/${jar}.jar`;
echo `cd ../install && rm VASSAL-$version/lib/${jar}.jar && unzip -l VASSAL-$version.zip`;
echo "</pre>";
?>