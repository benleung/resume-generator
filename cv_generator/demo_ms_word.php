<?php
// ------------------------------
// Initalization
// ------------------------------
try {
// Include classes
include_once('tbs_class.php'); // Load the TinyButStrong template engine
include_once('tbs_plugin_opentbs.php'); // Load the OpenTBS plugin

// prevent from a PHP configuration problem when using mktime() and date()
if (version_compare(PHP_VERSION,'5.1.0')>=0) {
	if (ini_get('date.timezone')=='') {
		date_default_timezone_set('Hongkong');
	}
}

// Initialize the TBS instance
$TBS = new clsTinyButStrong; // new instance of TBS
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load the OpenTBS plugin

// Form
$save_as = (isset($_POST['save_as']) && (trim($_POST['save_as'])!=='')) ? trim($_POST['save_as']) : '';	

// ------------------------------
// Data preparation
// ------------------------------
// Basic Info
$yourname = PostWrapper_TrimSplash('yourname');
$youremail = PostWrapper_TrimSplash('youremail');
$phone = PostWrapper_TrimSplash('phone');

// Dynamic Fields ("Add Item")
$exchanges = PostWrapper_Empty('exchanges');	//array
$careers = PostWrapper_Empty('careers');	//array
$acts = PostWrapper_Empty('acts');	//array
$langs = PostWrapper_Empty('langs');	//array
$awards = PostWrapper_Empty('awards');	//array

// Personal
$computer = PostWrapper_TrimSplash('computer');
$hobbies = PostWrapper_TrimSplash('hobbies');

// High School
$highschoolstartyear = PostWrapper_Empty('highschoolstartyear');
$highschoollength = PostWrapper_Empty('highschoollength');
$highschoolname = PostWrapper_TrimSplash('highschoolname');
$highschoolcountry = PostWrapper_TrimSplash('highschoolcountry');
$deanlist = isset($_POST['deanlist'])? $_POST['deanlist'] : false;	//array
$checkedbox_highschool = isset($_POST['checkedbox_highschool'])? $_POST['checkedbox_highschool'] : false;	//array (public exams)
$ce_a = PostWrapper_Empty('ce_a');
$ce_b = PostWrapper_Empty('ce_b');
$ce_c = PostWrapper_Empty('ce_c');
$alevel_a = PostWrapper_Empty('alevel_a');
$alevel_b = PostWrapper_Empty('alevel_b');
$alevel_c = PostWrapper_Empty('alevel_c');
$hkdse_555 = PostWrapper_Empty('hkdse_555');
$hkdse_55 = PostWrapper_Empty('hkdse_55');
$hkdse_5 = PostWrapper_Empty('hkdse_5');
$igcse_aa = PostWrapper_Empty('igcse_aa');
$igcse_a = PostWrapper_Empty('igcse_a');
$igcse_b = PostWrapper_Empty('igcse_b');
$idb_score = PostWrapper_Empty('idb_score');
$mainland_score = PostWrapper_Empty('mainland_score');
$highschool_other_desc = PostWrapper_TrimSplash('highschool_other_desc');
if($_POST['is_eas']=='Yes') {
  $is_eas=True;
}
else{
	$is_eas=False;
}

// Education - HKUST (part of business logic already)
$uststartyear = PostWrapper_Empty('uststartyear');
$cga = PostWrapper_TrimSplash('ustcga');
$cga_float = floatval($cga);
$ustgradyear = PostWrapper_TrimSplash('ustgradyear');
$ustgradmonth = PostWrapper_TrimSplash('ustgradmonth');
$major = PostWrapper_TrimSplash('ustmajor');
	
// -----------------
// Preprocessing of the collected data 
// -----------------

// -----------------
// Load the template
// -----------------
$template = 'demo_ms_word.docx';
$TBS->LoadTemplate($template); // Also merge some [onload] automatic fields (depends of the type of document).

// --------------------------------------------
// Business Logic and Prsentation: Merging and other operations on the template
// --------------------------------------------
// Basic info
$data = array();
$data[] = array('yourname'=> "$yourname", 'youremail'=>"$youremail" , 'phone'=>"$phone");

$TBS->MergeBlock('info', $data);

// Education - HKUST
$data = array();
if(!empty($deanlist))
	$deanlist = "\nDean's List: ".join(', ',$deanlist);
else
	$deanlist = '';
$grade = getGradeByCGA($cga_float);
$data[] = array('uststartyear'=> "$uststartyear", 'ustgradmonth'=>"$ustgradmonth" , 'ustgradyear'=>"$ustgradyear", 'major'=>"$major", 'grade'=>$grade, 'cga'=>"$cga", 'deanlist'=>"$deanlist");

$TBS->MergeBlock('ust', $data);

// Education - International Exchange Program
$data = array();
$exchangedesc = "International Exchange Program";
if(is_array($exchanges)){
  foreach($exchanges as $exchange){
    $exchangestartyear = $exchange['startyear'];
    if($exchangestartyear!=''){//(DEBUG)
      $exchangestartsemester = $exchange['startsemester'];
      $exchangelength = $exchange['length'];
      $exchangeschool = stripslashes(trim($exchange['school']));
      $exchangecountry = stripslashes(trim($exchange['country']));

      if($exchangecountry!='')
        $exchangeschool = "$exchangeschool, $exchangecountry";

      $exchangeperiod = "$exchangestartyear $exchangestartsemester";
      if($exchangelength=="semester"){
        $yearperiod = "$exchangestartyear $exchangestartsemester";
      }
      else if($exchangelength=="year"){
        $exchangeendyear = ((int)$exchangestartyear)+1;
        $yearperiod = "$exchangestartyear - $exchangeendyear";
      }
      $data[] = array('yearperiod'=> "$yearperiod", 'school'=>"$exchangeschool" , 'desc'=>"$exchangedesc");
    }
  }
}

// Education - High School Education
$highschoolendyear = ((int)$highschoolstartyear)+((int)$highschoollength);
$highschoolyearperiod = "$highschoolstartyear - $highschoolendyear";
if($highschoolcountry!=''){
	$highschoolname = "$highschoolname, $highschoolcountry";
}
$highschooldesc = '';
if(is_array($checkedbox_highschool)){
  foreach($checkedbox_highschool as $exam_type){ //public exam
    if($exam_type=="checkedbox_hkcee"){
      $highschooldesc .= ($highschooldesc=='') ? '': "\n";
      $highschooldesc .= "Hong Kong Certificate of Education Examination: " ;
      $ce_result = array();
      if($ce_a!='' && $ce_a!="0")
        $ce_result[] = "$ce_a"."A";
      if($ce_b!='' && $ce_b!="0")
        $ce_result[] = "$ce_b"."B";
      if($ce_c!='' && $ce_c!="0")
        $ce_result[] = "$ce_c"."C";
      $highschooldesc .= join(", ", $ce_result);
    }
    if($exam_type=="checkedbox_hkale"){
      if($alevel_a!='' || $alevel_b!='' || $alevel_c!=''){
        $highschooldesc .= ($highschooldesc=='') ? '': "\n";
        $highschooldesc .= "Hong Kong Advanced Level Examination: " ;
        $al_result = array();
        if($alevel_a!='' && $alevel_a!="0")
          $al_result[] = "$alevel_a"."A";
        if($alevel_b!='' && $alevel_b!="0")
          $al_result[] = "$alevel_b"."B";
        if($alevel_c!='' && $alevel_c!="0")
          $al_result[] = "$alevel_c"."C";
        $highschooldesc .= join(", ", $al_result);
      }
    }
    else if($exam_type=='checkedbox_ibd'){
      $highschooldesc .= ($highschooldesc=='') ? '': "\n";
      $highschooldesc .= "International Baccalaureate Diploma Score: $idb_score / 45";
    }
    else if($exam_type=="checkedbox_mainland"){
      $highschooldesc .= ($highschooldesc=='') ? '': "\n";
      $highschooldesc .= "Mainland China College Entrance Examination Score: $mainland_score / 750";
    }
    else if($exam_type=="checkedbox_hkdse"){
      $highschooldesc .= ($highschooldesc=='') ? '': "\n";
      $highschooldesc .= "Hong Kong Diploma of Secondary Education: " ;
      $dse_result = array();
      if($hkdse_555!='' && $hkdse_555!="0")
        $dse_result[] = number2word($hkdse_555)." 5**";
      if($hkdse_55!='' && $hkdse_55!="0")
        $dse_result[] = number2word($hkdse_55)." 5*";
      if($hkdse_5!='' && $hkdse_5!="0")
        $dse_result[] = number2word($hkdse_5)." 5";
      $highschooldesc .= join(", ", $dse_result);
    }
    else if($exam_type=="checkedbox_igcse"){
      $igcse_result = array();
      $highschooldesc .= ($highschooldesc=='') ? '': "\n";
      $highschooldesc .= "International General Certificate of Secondary Education: " ;
      if($igcse_aa!='' && $igcse_aa!="0")
        $igcse_result[] = $igcse_aa." A*";
      if($igcse_a!='' && $igcse_a!="0")
        $igcse_result[] = "$igcse_a"." A";
      if($igcse_b!='' && $igcse_b!="0")
        $igcse_result[] = "$igcse_b"." B";
      $highschooldesc .= join(", ", $igcse_result);
    }
    else if($exam_type=="checkedbox_highschool_other"){
      $highschooldesc .= ($highschooldesc=='') ? '': "\n";
      $highschooldesc .= $highschool_other_desc;
    }
  }
  if($is_eas){
    $highschooldesc .= ($highschooldesc=='') ? '': "\n";
    $highschooldesc .= "Admission to HKUST through the Early Admissions Scheme";
  }

  $data[] = array('yearperiod'=> "$highschoolyearperiod", 'school'=>"$highschoolname" , 'desc'=>"$highschooldesc");
}
$TBS->MergeBlock('edu', $data);


// Career Experience
$data = array();
if(is_array($careers)){
  foreach($careers as $career){
    $jobstartmonth = $career["jobstartmonth"];
    $jobstartyear = $career["jobstartyear"];
    $jobendmonth = $career["jobendmonth"];
    $jobendyear = $career["jobendyear"];
    $jobtitle = stripslashes(trim($career["jobtitle"]));
    $jobdepartment = stripslashes(trim($career["jobdepartment"]));
    if($jobdepartment!="")
      $jobdepartment = "$jobdepartment, ";
    $company = stripslashes(trim($career["company"]));
    $country = stripslashes(trim($career["jobcountry"]));
    
    
    $startdate = "$jobstartmonth $jobstartyear";
    if(isset($career['jobenddatepresent']))
      $enddate = "Present";
    else
      $enddate = "$jobendmonth $jobendyear";
    
    $jobdesc = '';	
    $jobdesc_array = $career["jobdesc"];
    
    for($i=0; $i<count($jobdesc_array); $i++){
      $jobdesc_item = stripslashes(trim($jobdesc_array[$i]));
      if($i==0){
        $jobdesc .= ($jobdesc_item!="") ? "-   $jobdesc_item" : "";	//(TOFIX: occasional encoding issues)mb_convert_encoding('&middot;', 'UTF-8', 'HTML-ENTITIES')
      }
      else{
        $jobdesc .= ($jobdesc_item!="") ? "\n-   $jobdesc_item" : "";
      }
    }
    
    $companydesc = "";
    if($country!="")
      $companydesc .= ", $country";
    
    $period = "$startdate - $enddate";
    $data[] = array('period'=> $period, 'jobtitle'=> $jobtitle, 'department'=>$jobdepartment, 'company'=> $company, "companydesc"=>$companydesc, 'jobdesc'=> $jobdesc );
  }
}
$TBS->MergeBlock('exp', $data);
	

	
	
// Extra-curricular Activities
$data = array();
if(is_array($acts)){
  foreach($acts as $act){
    $actstartdate = $act["startmonth"]." ".$act["startyear"];
    if(isset($act["enddatepresent"]))	//change to if($act[is_currently_working]) after retrive from db
      $actenddate = "Present";
    else
      $actenddate = $act["endmonth"]." ".$act["endyear"];
    $acttitle = stripslashes(trim($act["title"]));//trim
    $organization = stripslashes(trim($act["organization"]));//trim
    $actdesc_array = $act['actdesc'];
    //
    $actdesc = '';
    for($i=0; $i<count($actdesc_array); $i++){
      $actdesc_item = stripslashes(trim($actdesc_array[$i]));
      if($i==0){
        $actdesc .= ($actdesc_item!="") ? "-   $actdesc_item" : "";
      }
      else{
        $actdesc .= ($actdesc_item!="") ? "\n-   $actdesc_item" : "";
      }
    }
    $actcountry = stripslashes(trim($act["country"]));
    $companydesc = '';
    if($actcountry!='')
      $companydesc .= ", $actcountry";
    $period = "$actstartdate - $actenddate";

    $data[] = array('period'=> $period, 'title'=> $acttitle, 'company'=> $organization, 'desc'=> $actdesc, 'companydesc'=>$companydesc );
  }
}
$TBS->MergeBlock('act', $data);

// Award
$data = array();
if(is_array($awards)){
  foreach($awards as $award){
    $awardperiodto = $award["periodto"];
    $awardperiodfrom = $award["periodfrom"];
    $awardperiod = "$awardperiodfrom - $awardperiodto";
    $awardrank = stripslashes(trim($award["rank"]));
    if($awardrank!="")
      $awardrank = "$awardrank, ";
    $awardtitle = stripslashes(trim($award["title"]));
    $awardorganizer = stripslashes(trim($award["organizer"]));
    if($awardorganizer != '')
      $awardorganizer = ", $awardorganizer";
    
    
    $data[] = array('period'=> $awardperiod, 'rank'=>$awardrank, 'title'=> "$awardtitle", 'organizer'=>$awardorganizer);
  }
}
$TBS->MergeBlock('award', $data);

// Lang
$lang_out_list = array();   $lang_items = array();
if(is_array($langs)){
  foreach($langs as $lang){
    $lang_proficiency = $lang["proficiency"];
    $lang_name = stripslashes(trim($lang["name"]));
    
    if (empty($lang_items[$lang_proficiency]))
      $lang_items[$lang_proficiency] = array();
    $lang_items[$lang_proficiency][] = $lang_name;
  }
}
if(is_array($lang_items)){
  foreach($lang_items as $lang_item_key => $lang_item_value)
    $lang_out_list[] = "$lang_item_key in ".join(', ', $lang_item_value);
}
$lang_out = join('; ', $lang_out_list);

// Personal
$data = array();
$data[] = array('itemname'=> "Computer Skills:", 'desc'=> $computer);		//
$data[] = array('itemname'=> "Languages:", 'desc'=> $lang_out);
$data[] = array('itemname'=> "Hobbies:", 'desc'=> $hobbies);
$TBS->MergeBlock('personal', $data);

// -----------------
// Output the result
// -----------------

// Define the name of the output file
$output_file_name = "xxx.docx";  //filename (hkt)

if ($save_as==='') {
	// Output the result as a downloadable file (only streaming, no data saved in the server)
	$TBS->Show(OPENTBS_DOWNLOAD, $output_file_name); // Also merges all [onshow] automatic fields.
  exit();
}
	// Output the result as a file on the server
	// $TBS->Show(OPENTBS_FILE, $output_file_name);

} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
	
// ------------------------------
// Helper functions
// ------------------------------
function getGradeByCGA($cga){
	if($cga>=4.15)
		return "A+";
	else if($cga>=3.85)
		return "A";
	else if($cga>=3.5)
		return "A-";
	else if($cga>=3.15)
		return "B+";
	else if($cga>=2.85)
		return "B";
	else if($cga>=2.5)
		return "B-";
	else if($cga>=2.15)
		return "C+";
	else if($cga>=1.85)
		return "C";
	else if($cga>=1.35)
		return "C-";
	else if($cga>=0.5)
		return "D";
	else
		return "F";
}

function PostWrapper_Empty($para){
	return (isset($_POST[$para])) ? $_POST[$para] : '';
}

function PostWrapper_TrimSplash($para){
	return (isset($_POST[$para])) ? stripslashes(trim(''.$_POST[$para])) : '';
}

function number2word($para){
	$map = array(
		1 => "one",
		2 => "two",
		3 => "three",
		4 => "four",
		5 => "five",
		6 => "six",
		7 => "seven",
		8 => "eight",
		9 => "nine",
		10 => "ten",
		11 => "eleven",
		12 => "twelve"
	);
	return $map[$para];
}


?>