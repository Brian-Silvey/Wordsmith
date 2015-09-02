<?php

include_once('crawl.php');
// prevent the program from timing out
set_time_limit(0);
$crawler = new crawl();
$crawler->startCrawl();

?>
