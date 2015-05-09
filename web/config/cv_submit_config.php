<?php
$month_options = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
$year_options = array('2000','2001','2002','2003','2004','2005','2006','2007','2008','2009','2010', '2011', '2012', '2013', '2014');
$semester_options = array('Fall','Spring','Summer','Winter');
$semester_regular_options = array('Spring','Fall');
$ust_startyr_options = array('2009','2010', '2011', '2012', '2013');
$exchange_startyr_options = array('2010', '2011', '2012', '2013', '2014');
$ust_gradyr_options = array('2014','2015','2016');
$highschoolstartyear_options = array('2003','2004','2005','2006','2007','2008','2009','2010','2011','2012');
$highschoollength_options = array('1','2','3','4','5','6','7','8','9','10');
$deanlist_years = array('2009','2010', '2011', '2012', '2013', '2014');
$deanlist_options = array();
foreach($deanlist_years as $year){
	foreach($semester_regular_options as $semester){
		$deanlist_options[] = "$year $semester";
	}
}
$major_options = array("Mechanical Engineering",
					   "Logistics Management & Engineering",
					   "Industrial Engineering & Engineering Management",
					   "Electronic Engineering",
					   "Civil Engineering",
					   "Chemical & Biomolecular Engineering",
					   "Chemical Engineering",
					   "Computer Engineering",
					   "Computer Science");
$lang_proficiency_options = array("Native","Fluent","Elementary");

$jobdesc_count = 3;	// number of lines to describe a job
$actdesc_count = 3;
$collect_yr = 2014;	// DDP Online CV Submission "2014"

// dynamic items ("Add Item")
$lang = array('title'=>'lang','max'=>5,'min'=>0);	//max: maximum number of language that can be selected
$act = array('title'=>'act','max'=>7,'min'=>0);
$career = array('title'=>'career','max'=>7,'min'=>0);
$exchange = array('title'=>'exchange','max'=>5,'min'=>0);
$award = array('title'=>'award','max'=>8,'min'=>0);
$items = array($lang,$act,$career,$exchange,$award);


?>