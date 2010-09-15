<?php session_start()?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>CS 8550 - Search Engine</title>
<link rel="stylesheet" href="styles.css" type="text/css" />
</head>
<body>
<div class="header">
	<div class="outernav">
		<div class="nav">
			<div class="innernav">
				<ul>
					<li><a href="index.php">Home</a></li>
				</ul>
			</div>
		</div>
	</div>
		
	<div class="clear"></div>
	
	<div class="title">
		<div class="innertitle">
			
			<!-- TITLE -->
			<h1><a href="#">CS 8550 Search Engine</a></h1>
			<h2>Searches Multiple NCBI Databases</h2>
      <h2>Written By: Steven C Jackson</h2>
			<!-- END TITLE -->
			
		</div>
	</div>
</div>
<div id="wrap">
	<div class="pagewrapper">
		<div class="innerpagewrapper">
			<div class="page">
				<div class="content">
          <?php require_once 'engine.php'; ?>
				</div>
				<div class="sidebar">	
					<h4>About</h4>
					<p>This project searches various NCBI Databases to deliver results and links to NCBI based on the queried term.</p>
          <h4>Use</h4>
					<p>Enter a search term into the query box and hit 'Submit'. The default database is PUBMED. The user may select any of the database tabs to query that database.</p>
				</div>
				<div class="footer">
					<p>This is a project for CS 8550 at Kennesaw State University</p>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
