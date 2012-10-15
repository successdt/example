<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<title>Apple.com style suggestion search</title>
        <?php
            echo $this->html->script('http://www.google.com/jsapi');
            echo $this->html->script('script');
            echo $this->html->css('style'); 
        ?>
	</head>
<body>
<div>
	<form id="searchform">
		<div>
			What are you looking for? <input type="text" size="30" value="" id="inputString" onkeyup="lookup(this.value);" />
		</div>
		<div id="suggestions"></div>
	</form>
</div>
</body>
</html>