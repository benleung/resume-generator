<?php
include_once("config/cv_submit_config.php");

?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<title>Online CV Generator (UST)</title>
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
	<link rel="stylesheet" href="css/template.css" type="text/css"/>
	<link rel="stylesheet" href="css/button.css" type="text/css"/>
	<script src="js/jquery-1.8.2.min.js" type="text/javascript"></script>
	<script src="js/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/helper.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/cv_submit_core.js" type="text/javascript" charset="utf-8"></script>
	
	<script type="text/javascript" charset="utf-8">
		jQuery(document).ready(function(){
			// binds form submission and fields to the validation engine
			jQuery("#formID").validationEngine();

			//initialization
			var checkboxes = ['checkedbox_hkcee','checkedbox_hkale','checkedbox_hkdse','checkedbox_ibd','checkedbox_mainland','checkedbox_igcse','checkedbox_highschool_other'];
			for (var i=0; i<checkboxes.length; i++) {
				if($('#'+checkboxes[i]).attr('checked'))
					ToggleCheckboxState("."+checkboxes[i].substr(11));
			}
			
			<?php foreach($items as $item){
				//add_button binding
				echo('$(\'#'."{$item['title']}_add_btn').click(function() {
					additem('{$item['title']}');
					return false;
				});\n");
			}?>
			
			$('#save_as').click(function() {
				return window.confirm('Are you sure to submit? (Press \'OK\' to confirm)\n- Please preview before submission if you haven\'t done so!');
			});
		});

		// Dynamic Field ("Add Item")
		function additem(name){
			var name_num = $("#"+name+"_count").val();
			var append_html;
			name_num++;
			
			$("#"+name+"_count").val(name_num);	//bind to hidden field
			$("#"+name+"_num").val(name_num);	//bind to select number of items
			if(name=='exchange'){
				append_html = <?php echo json_encode(displayExchangeItems());?>;
			}
			else if(name=='act'){
				append_html = <?php echo json_encode(displayActItems());?>;
			}
			else if(name=='career'){
				append_html = <?php echo json_encode(displayCareerItems());?>;
			}
			else if(name=='lang'){
				append_html = <?php echo json_encode(displayLangItems());?>;
			}
			else if(name=='award'){
				append_html = <?php echo json_encode(displayAwardItems());?>;
			}
			append_html = append_html.format([name_num]);
			$('#'+name+'_items_container').append(append_html);	
			$('#'+name+name_num).hide();
			$('#'+name+name_num).slideDown('fast');
		}
		
		function RemoveSpecificItem(name,item_no){
			$('#'+name+item_no).slideUp('fast',function() {
				$(this).remove();
			});
			return false;
		}
		// End of Dynamic Field ("Add Item")
		
		</script>
</head>

<body>
	<form id="formID" class="formular" accept-charset="utf-8" method="post" action="cv_generator/demo_merge.php">
		<h1>Online CV Generator</h1>
		
		<p><b>User Guide</b></p>
		<ol>
		<li>Please fill in this form. </li>
		<li>Click "<b>Save</b>" Button to download your Resume! </li>
    <li><font class="requiredSymbol">*</font> indicates required field</li>
		</ol>

		<fieldset class="fieldset_title">
			<legend>
				Basic Info
			</legend>
			<div>
				<table><tr><td><font class="requiredSymbol">*</font>Name:</td>
				<td style="width:100%"><input class="validate[required] text-input" type="text" name="yourname" id="yourname" style="width:80%"/></td></tr> 
				
				<tr><td><font class="requiredSymbol">*</font>Email:</td>
				<td><input class="validate[required,custom[email]] text-input" type="text" name="youremail" id="youremail"  style="width:80%"/></td></tr> 
				
				<tr><td><span><font class="requiredSymbol">*</font>Phone: </span></td>
				<td><input class="validate[required] text-input" type="text" name="phone" id="phone" style="width:80%"/></td></tr>
				</table>
			</div>
		</fieldset>
		
		<fieldset class="fieldset_title">
			<legend>
				Education - HKUST
			</legend>
			<div>
				<span><font class="requiredSymbol">*</font>Major:</span>
				<select name="ustmajor" id="ustmajor" class="validate[required]">
					<option value="">Select your Major</option>
					<?php bind_options($major_options,false); ?>
				</select>
			</div>
			<div>
				<span><font class="requiredSymbol">*</font>Current CGA (up to and including <?php echo $collect_yr-1 ?> Fall):</span>
				<input style="width:50px" class="validate[required,custom[number],min[0],max[4.3]] text-input" type="text" name="ustcga" id="ustcga" placeholder=""/> / 4.3
			</div>
			<div>
				<span><font class="requiredSymbol">*</font>Year of Entrance:</span>
				<select name="uststartyear" id="uststartyear" class="validate[required]">
					<option value="">Choose a Year</option>
					<?php bind_options($ust_startyr_options,false); ?>
				</select>
			</div>
			<p class="subtitle">
				Graduation Date
			</p>
			<div>
				<font class="requiredSymbol">*</font><span>Month:</span>
				<select name="ustgradmonth" id="ustgradmonth" class="validate[required]">
					<option value="June">June</option>
				</select>
				<span>Year:</span>
				<select name="ustgradyear" id="ustgradyear" class="validate[required]">
					<option value="">Choose a Year</option>
					<?php bind_options($ust_gradyr_options,false); ?>
				</select>
			</div>
			<p class="subtitle">
				Dean's list
			</p>
			<div>
				<?php bindcheckbox($deanlist_options,"deanlist",4); ?>
			</div>
		</fieldset>
		
		<fieldset class="fieldset_title">
			<legend>
				Education - International Exchange Program
			</legend>
			
			<div>Please order your exchange experience in chronological order (<b>latest at the top</b>)</div>
			<div id='exchange_items_container'></div>
			<br/><a href="#" id='exchange_add_btn' class="button add">Add Item</a>
			
		</fieldset>
		
		
		<fieldset>
			<legend class="fieldset_title">
				Education - Latest High School Education
			</legend>
			<table>
			<tr><td style="width:200px"><font class="requiredSymbol">* </font>School Name:</td>
				<td><input style="width:350px" value="" class="text-input validate[required]" type="text" name="highschoolname" id="highschoolname" />
			</td></tr>
			
			<tr><td style="width:200px">Country of Study<b> (if outside HK)</b>:</td>
				<td><input style="width:350px" value="" class="text-input" type="text" name="highschoolcountry" id="highschoolcountry" />
			</td></tr>
			
			
			<tr>
				<td><font class="requiredSymbol">*</font>Year of Entrance:</td>
				<td><select name="highschoolstartyear" id="highschoolstartyear" class="validate[required]">
					<option value="">Choose a Year</option>
					<?php bind_options($highschoolstartyear_options,false); ?>
				</select></td>
			</tr>
			<tr>
				<td><font class="requiredSymbol">*</font>Length of Study:</td>
				<td><select name="highschoollength" id="highschoollength" class="validate[required]">
					<?php bind_options($highschoollength_options,false); ?>
				</select>
				Year(s)
				</td>
			</tr>
			</table>
		</fieldset>
		
		<fieldset>
			<legend class="fieldset_title">
				Public Examination Result (High School)
			</legend>
			
			<!--EAS-->
			<font class="requiredSymbol">*</font>Admission to HKUST through the Early Admissions Scheme?
			<select name="is_eas" id="is_eas" class=""  >
				<option value="No">No</option>	
				<option value="Yes">Yes</option>	
			</select>
				
			<p class="hint">Please provide your public examination result in high school. </p>
			
			<!--Checkboxes-->
			<label><input type="checkbox" name="checkedbox_highschool[]" id="checkedbox_hkcee" value="checkedbox_hkcee"  onclick=javascript:ToggleCheckboxState(".hkcee") />Hong Kong Certificate of Education Examination (HKCEE)</label>
			
			<label><input type="checkbox" name="checkedbox_highschool[]" id="checkedbox_hkale" value="checkedbox_hkale"  onclick=javascript:ToggleCheckboxState(".hkale") />Hong Kong Advanced Level Examination (HKALE) </label>
			
			<label><input type="checkbox" name="checkedbox_highschool[]" id="checkedbox_hkdse" value="checkedbox_hkdse" onclick=javascript:ToggleCheckboxState(".hkdse") />Hong Kong Diploma of Secondary Education (HKDSE)</label>
			
			<label><input type="checkbox" name="checkedbox_highschool[]" id="checkedbox_ibd" value="checkedbox_ibd" onclick=javascript:ToggleCheckboxState(".ibd") />International Baccalaureate Diploma (IBD)</label>
			
			<label><input type="checkbox" name="checkedbox_highschool[]" id="checkedbox_mainland" value="checkedbox_mainland" onclick=javascript:ToggleCheckboxState(".mainland") />Mainland China College Entrance Examination</label>
			
			<label><input type="checkbox" name="checkedbox_highschool[]" id="checkedbox_igcse" value="checkedbox_igcse" onclick=javascript:ToggleCheckboxState(".igcse") />International General Certificate of Secondary Education (IGCSE)</label>
			
			
			
			<label><input type="checkbox" name="checkedbox_highschool[]" id="checkedbox_highschool_other" value="checkedbox_highschool_other" onclick=javascript:ToggleCheckboxState(".highschool_other") />Other</label>
			
			<!--HKCEE-->
			<div class="hkcee" style="display:none">
			<b>HKCEE:</b><br/>
			Number of <br/>
			<div style="float: left; width: 33%;">
			A / 5*: <input value="" class="text-input validate[groupRequired[group1],custom[integer],min[0]] highschool_gradeinput" type="text" name="ce_a" id="ce_a" placeholder="" /> </div>
			<div style="float: left; width: 33%;">B / 5: <input value="" class="text-input validate[groupRequired[group1],custom[integer],min[0]] highschool_gradeinput" type="text" name="ce_b" id="ce_b" placeholder=""/></div>
			<div style="float: left; width: 33%;">C / 4: <input value="" class="text-input validate[groupRequired[group1],custom[integer],min[0]] highschool_gradeinput" type="text" name="ce_c" id="ce_c" placeholder=""/></div>
			</div>
			
			<!--HKALE-->
			<div class="hkale" style="display:none" >
			<b>HKALE:</b> <br/>
			Number of <br/>
			<div style="float: left; width: 33%;">A: <input value="" class="text-input validate[groupRequired[group2],custom[integer],min[0]] highschool_gradeinput" type="text" name="alevel_a" id="alevel_a" data-prompt-position="inline" placeholder=""/></div>
			<div style="float: left; width: 33%;">B: <input value="" class="text-input validate[groupRequired[group2],custom[integer],min[0]] highschool_gradeinput" type="text" name="alevel_b" id="alevel_b" placeholder=""/></div>
			<div style="float: left; width: 33%;">C: <input value="" class="text-input validate[groupRequired[group2],custom[integer],min[0]] highschool_gradeinput" type="text" name="alevel_c" id="alevel_c" placeholder=""/></div>
			</div>
			
			<!--IBD-->
			<div class="ibd" style="display:none"><font class="requiredSymbol">*</font><b>IBD Score:</b> <input value="" class="text-input validate[required,custom[number],min[0],max[45]] highschool_gradeinput" type="text" name="idb_score" id="idb_score" placeholder=""/> / 45</div>

			<!--Mainland-->
			<div class="mainland" style="display:none"><font class="requiredSymbol">*</font><b>Mainland China College Entrance Examination Score:</b>
			<input value="" class="text-input validate[required,custom[number],min[0],max[750]] highschool_gradeinput" style="width: 50px" type="text" name="mainland_score" id="mainland_score" placeholder=""/> / 750</div>
			
			<!--IGCSE-->
			<div class="igcse" style="display:none">
			<b>International General Certificate of Secondary Education (IGCSE):</b><br/>
			Number of <br/>
			<div style="float: left; width: 33%;">
			A*: <input value="" class="text-input validate[groupRequired[group3],custom[integer],min[0]] highschool_gradeinput" type="text" name="igcse_aa" id="igcse_aa" placeholder="" /> </div>
			<div style="float: left; width: 33%;">A: <input value="" class="text-input validate[groupRequired[group3],custom[integer],min[0]] highschool_gradeinput" type="text" name="igcse_a" id="igcse_a" placeholder=""/></div>
			<div style="float: left; width: 33%;">B: <input value="" class="text-input validate[groupRequired[group3],custom[integer],min[0]] highschool_gradeinput" type="text" name="igcse_b" id="igcse_b" placeholder=""/></div>
			</div>
			
			<!--DSE-->
			<div class="hkdse" style="display:none">
			<b>Hong Kong Diploma of Secondary Education (HKDSE):</b><br/>
			Number of <br/>
			<div style="float: left; width: 33%;">
			5**: <input value="" class="text-input validate[groupRequired[group4],custom[integer],min[0]] highschool_gradeinput" type="text" name="hkdse_555" id="hkdse_555" placeholder="" /> </div>
			<div style="float: left; width: 33%;">5*: <input value="" class="text-input validate[groupRequired[group4],custom[integer],min[0]] highschool_gradeinput" type="text" name="hkdse_55" id="hkdse_55" placeholder=""/></div>
			<div style="float: left; width: 33%;">5: <input value="" class="text-input validate[groupRequired[group4],custom[integer],min[0]] highschool_gradeinput" type="text" name="hkdse_5" id="hkdse_5" placeholder=""/></div>
			</div>

			<!--Other-->
			<div class="highschool_other" style="display:none">
			<b>Please use 1 line to describe your result in public examination.</b><br/>
			<input value="" class="text-input highschool_gradeinput" type="text" name="highschool_other_desc" id="highschool_other_desc" placeholder="e.g. Fudan Early Admission Test: 800/800" style="width: 100%"/>
			</div>
				
		</fieldset>
		<fieldset>
			<legend>
				Experience (Internship, Part-time, Corporate Project)
			</legend>
			<p>Please input your experience in chronological order (<b>latest at the top</b>)</p>
			<div class="hint">It is advised that total number of job experience and activities listed is less than 10 to save keep the resume one page.</div>
			<div id="career_items_container"></div>
			<br/><a href="#" id='career_add_btn' class="button add">Add Item</a>
		</fieldset>
		
		<fieldset>
			<legend>
				Extra-curricular Activities
			</legend>
			<p>Please order your experience in chronological order (<b>latest at the top</b>).</p>
			<div class="hint">It is advised that total number of job experience and activities listed is less than 10 to save keep the resume one page.</div>
			<div id='act_items_container'></div>
			<br/><a href="#" id='act_add_btn' class="button add">Add Item</a>
		</fieldset>
		
		<fieldset>
			<legend>
				Achievement
			</legend>
			<p>Please order your achievement in chronological order (<b>latest at the top</b>).</p>
			<div class="hint">Exclude dean's list award in this section.</div>
			<div id='award_items_container'></div>
			<br/><a href="#" id='award_add_btn' class="button add">Add Item</a>
		</fieldset>
		
		<fieldset>
			<legend>
				Computer Skills
			</legend>
			<div>
				<span><font class="requiredSymbol">*</font>List of computer skills:</span>
				<p class="hint">Please give a comma-seperated list.</p>
				<input value="" class="validate[required] text-input" type="text" name="computer" id="computer" style="width:100%" placeholder="e.g. Microsoft Office, AutoCAD, C++, Java, PHP, MySQL"/>
			</div>	
		</fieldset>
		
		<fieldset>
			<legend>
				Language and Proficiency
			</legend>
			<div>Please list out the languages you know.</div>
			<div id='lang_items_container'></div>
			<br/><a href="#" id='lang_add_btn' class="button add">Add Item</a>
		</fieldset>
		
		<fieldset>
			<legend>
				Hobbies
			</legend>
			<div>
				<span><font class="requiredSymbol">*</font>List of hobbies:</span>
				<p class="hint">Please give a comma-seperated list.</p>
				<input value="" class="validate[required] text-input" type="text" name="hobbies" id="hobbies" placeholder="e.g. Table Tennis, Basketball, Soccer" style="width:100%"/>
			</div>	
		</fieldset>
		
		<input name="btn_go" class="submit" type="submit" value="Save"/>
		
		<!--hidden fields-->
		<?php bindHiddenField("career_count",$career['min'],false); 
		bindHiddenField("exchange_count",$exchange['min'],false); 
		bindHiddenField("award_count",$award['min'],false); 
		bindHiddenField("act_count",$act['min'],false); 
		bindHiddenField("lang_count",$lang['min'],false); 
		
		
		bindHiddenField("jobdesc_count",$jobdesc_count,false); 
		bindHiddenField("actdesc_count",$actdesc_count,false); 
		foreach($lang_proficiency_options as $lang_proficiency_option)
		{
		  echo '<input type="hidden" name="lang_proficiency_options[]" value="'. $lang_proficiency_option. '">';
		}
		?>
		<input type="hidden" name="tpl" value="demo_ms_word.docx">
	</form>
</body>
</html>

<?php
// Dynamic Fields ("Add Item")
function displayCareerItems(){
	global $jobdesc_count, $career;
	$return = '';
	$return .= "<div style=\"display:none\" id=\"career{0}\">
				<p><font class=\"title\">Experience Item</font>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"#\" id=\"career_rm_btn_{0}\" value=\"{0}\" onclick=\"javascript:RemoveSpecificItem('career',{0});return false;\" >Remove</a></p>
				<table>
				
				<tr><td><font class=\"requiredSymbol\">*</font>Title:</td>
				<td><input value=\"\" class=\"validate[required] text-input career\" type=\"text\" name=\"careers[{0}][jobtitle]\" id=\"jobtitle{0}\" placeholder=\"e.g. Summer Intern, or Corporate Project Team Member\" />
				</td></tr>
				
				<tr><td>Department (optional):</td>
				<td><input value=\"\" class=\"text-input career\" type=\"text\" name=\"careers[{0}][jobdepartment]\" id=\"jobdepartment{0}\" placeholder=\"e.g. Finance Department\" /></td></tr>
				
				<tr><td><font class=\"requiredSymbol\">*</font>Company / Competition Name:</td><td>
				<input value=\"\" class=\"validate[required] text-input career\" type=\"text\" name=\"careers[{0}][company]\" id=\"company{0}\" placeholder=\"e.g. IBM, or International Case Competition\" />
				</td></tr>
								
				<tr><td>Country<b>(if outside HK)</b>:</td>
				<td><input value=\"\" class=\"text-input career\" type=\"text\" name=\"careers[{0}][jobcountry]\" id=\"jobcountry{0}\" placeholder=\"e.g. United Kingdom\" /></td></tr>
				<tr><td><font class=\"requiredSymbol\">*</font>Start Date: </td><td>";
	$return .= bindMonthYear("careers[{0}][jobstartmonth]","careers[{0}][jobstartyear]",true);
	$return .= "</td></tr><tr><td style=\"vertical-align:text-top;\"><font class=\"requiredSymbol\">*</font>End Date: </td><td><div class=\"jobenddate{0}\">";
	$return .= bindMonthYear("careers[{0}][jobendmonth]","careers[{0}][jobendyear]",true);
	$return .= "</div><input type=\"checkbox\" name=\"careers[{0}][jobenddatepresent]\" id=\"jobenddatepresent{0}\" onclick=javascript:ToggleState(\"#jobenddatepresent{0}\",\".jobenddate{0}\") />To present";
	$return .= "</td></tr></table>
			<p class=\"hint\">Please use 1-3 lines to describe your experience (use <b>past</b> tense)</p>
			<input value=\"\" class=\"validate[required] text-input jobdesc\" type=\"text\" name=\"careers[{0}][jobdesc][]\" id=\"jobdesc1_{0}\" placeholder=\"e.g. Collaborated in a team to present an entrepreneurial proposal using CDN technology\" />";
	for($desc_i=2;$desc_i<=$jobdesc_count;$desc_i++){
		$return .= "
			<input value=\"\" class=\"text-input jobdesc career{0}\" type=\"text\" placeholder=\"e.g. Developed an online application to support daily operational activities\" name=\"careers[{0}][jobdesc][]\" id=\"jobdesc{$desc_i}_{0}\" />";
	}
	$return .= "<br/></div>";
	return $return;
}

function displayExchangeItems(){
	global $exchange_startyr_options, $exchange,$semester_options ;
	$return = ''; 
	$return .= "<div id=\"exchange{0}\">
			<p><font class=\"title\">Exchange Item</font>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"#\" id=\"exchange_rm_btn_{0}\" value=\"{0}\" onclick=\"javascript:RemoveSpecificItem('exchange',{0});return false;\" >Remove</a></p>
			<table>
				<tr>
				<td style=\"width:150px\"><font class=\"requiredSymbol\">*</font>School Name:</td>
				<td><input value=\"\" class=\"text-input validate[required]\" type=\"text\" name=\"exchanges[{0}][school]\" id=\"exchangeschool{0}\" placeholder=\"e.g. Harvard University\" style=\"width:300px\"/></td>
				</tr>
				
				<tr>
				<td style=\"width:150px\"><font class=\"requiredSymbol\">*</font>School Country:</td>
				<td><input value=\"\" class=\"text-input\" type=\"text\" name=\"exchanges[{0}][country]\" id=\"exchangecountry{0}\" placeholder=\"e.g. United States\" style=\"width:300px\"/></td>
				</tr>
				
				<tr>
				<td style=\"width:200px\"><font class=\"requiredSymbol\">*</font>Year of Entrance:</td>
				<td><select name=\"exchanges[{0}][startyear]\" id=\"exchangestartyear{0}\" class=\"validate[required]\">
					<option value=\"\">Choose a year</option>";
	$return .= bind_options($exchange_startyr_options,true);
	$return .= "</select></td>
				</tr>
				
				<tr>
				<td style=\"width:200px\"><font class=\"requiredSymbol\">*</font>Semester of Entrance:</td>
				<td><select name=\"exchanges[{0}][startsemester]\" id=\"exchangestartsemester{0}\" class=\"validate[required]\">
					<option value=\"\">Choose a semester</option>";
	$return .= bind_options($semester_options,true);
	$return .= "</select></td>
				</tr>
				
				<tr>
				<td style=\"width:200px\"><font class=\"requiredSymbol\">*</font>Length of Study:</td>
				<td><select name=\"exchanges[{0}][length]\" id=\"exchangelength{0}\" class=\"validate[required]\">
					<option value=\"\">Choose length of study</option>
					<option value=\"semester\">1 Semester</option>
					<option value=\"year\">1 Year</option>
				</select></td>
				</tr>
				</table>
				</div>";
				
	return $return;
}

function displayAwardItems(){
	global $award, $year_options;
	$return = '';
	$return .= "<div id=\"award{0}\">
	<p><font class=\"title\">Award Item</font>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"#\" id=\"award_rm_btn_{0}\" value=\"{0}\" onclick=\"javascript:RemoveSpecificItem('award',{0});return false;\" >Remove</a></p>
	
	<table>
	<tr><td><font class=\"requiredSymbol\">*</font>Name of Scholarship / Competition:</td>
	<td><input style=\"width:350px\" value=\"\" class=\"validate[required] text-input\" type=\"text\" name=\"awards[{0}][title]\" id=\"awardtitle{0}\" placeholder=\"e.g. Exchange Scholarship, or Eco-Business Innovation Award\" /></td></tr>
	
	<tr><td>Organizer (optional):</td>
	<td><input style=\"width:350px\" value=\"\" class=\"text-input award{0}\" type=\"text\" name=\"awards[{0}][organizer]\" id=\"awardorganizer{0}\" placeholder=\"e.g. HKUST\" /></td></tr>

	<tr><td>Rank in the competition (optional):</td>
	<td><input style=\"width:350px\" value=\"\" class=\"text-input award{0}\" type=\"text\" name=\"awards[{0}][rank]\" id=\"awardrank{0}\" placeholder=\"e.g. Champion\"/></td></tr>
	
	<tr><td><font class=\"requiredSymbol\">*</font>Academic Year:</td>
	<td>From<select name=\"awards[{0}][periodfrom]\" id=\"awardperiodfrom{0}\" class=\"validate[required] award{0}\">
		<option value=\"\">Choose a year</option>";
	$return .= bind_options($year_options,true);
	$return .= "</select> to <select name=\"awards[{0}][periodto]\" id=\"awardperiodto{0}\" class=\"validate[required] award{0}\">
		<option value=\"\">Choose a year</option>";
	$return .= bind_options($year_options,true);
	$return .= "</td></tr></table></div>";
	return $return;
}

function displayActItems(){
	global $act, $year_options,$month_options,$actdesc_count;
	$return = '';
	$return .= "<div id=\"act{0}\">
		<p><font class=\"title\">Activity Item</font>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"#\" id=\"act_rm_btn_{0}\" value=\"{0}\" onclick=\"javascript:RemoveSpecificItem('act',{0});return false;\" >Remove</a></p>
			<table>
				<tr><td><font class=\"requiredSymbol\">*</font>Title:</td>
				<td><input style=\"width:400px\" value=\"\" class=\"validate[required] text-input act\" type=\"text\" name=\"acts[{0}][title]\" id=\"acttitle{0}\" placeholder=\"e.g. Student Ambassador\"/></td></tr>
				
				<tr><td><font class=\"requiredSymbol\">*</font>Organization:</td>
				<td><input style=\"width:400px\" value=\"\" class=\"validate[required] text-input act\" type=\"text\" name=\"acts[{0}][organization]\" id=\"organization{0}\" placeholder=\"e.g. Engineering School, HKUST\"/></td></tr>
				
				<tr><td>Country (if outside HK):</td>
				<td><input style=\"width:400px\" value=\"\" class=\"text-input act\" type=\"text\" name=\"acts[{0}][country]\" id=\"actcountry{0}\" placeholder=\"\" /></td></tr>
				<tr><td><font class=\"requiredSymbol\">*</font>Start Date:</td>
				<td>				
				<div>
					<span>Month:</span>
					<select name=\"acts[{0}][startmonth]\" id=\"actstartmonth{0}\" class=\"validate[required]\">
						<option value=\"\">Choose a Month</option>";
	$return .= bind_options($month_options,true);
	$return .= "</select>
					<span>Year:</span>
					<select name=\"acts[{0}][startyear]\" id=\"actstartyear{0}\" class=\"validate[required]\">
						<option value=\"\">Choose a Year</option>";
	$return .= bind_options($year_options,true);
	$return .= "</select>
				</div></td></tr>
				
				
				<tr><td style=\"vertical-align:text-top\"><font class=\"requiredSymbol\">*</font>End Date:</td>
				<td><div class=\"actenddate{0}\">
					<span>Month:</span>
					<select name=\"acts[{0}][endmonth]\" id=\"actendmonth{0}\" class=\"validate[required]\">
						<option value=\"\">Choose a Month</option>";
	$return .= bind_options($month_options,true);
	$return .= "</select>
					<span>Year:</span>
					<select name=\"acts[{0}][endyear]\" id=\actendyear{0}{0}\" class=\"validate[required]\">
						<option value=\"\">Choose a Year</option>";
	$return .= bind_options($year_options,true);
	$return .= "</select><br/></div>";
	$return .= "<input type=\"checkbox\" name=\"acts[{0}][enddatepresent]\" id=\"actenddatepresent{0}\" onclick=javascript:ToggleState(\"#actenddatepresent{0}\",\".actenddate{0}\") />To present";
	$return .= "</td>
				</tr>
			</table>
			<p class=\"hint\">Please use 1-3 lines to describe your role and responsibility for the activity (use <b>past</b> tense)</p>
			<input style=\"width:100%\" value=\"\" class=\"validate[required] text-input act\" type=\"text\" name=\"acts[{0}][actdesc][]\" id=\"actdesc1_{0}\" placeholder=\"e.g. Represented HKUST to promote engineering and the school to the general public\" />";
		for($desc_i=2;$desc_i<=$actdesc_count;$desc_i++){
			$return .= "
				<input style=\"width:100%\" value=\"\" class=\"text-input act act{0}\" type=\"text\" name=\"acts[{0}][actdesc][]\" id=\"actdesc{$desc_i}_{0}\" placeholder=\"e.g. Promote HKUST to the public\" d/>";
		}
	$return .= "</div>";
	return $return;
}

function displayLangItems(){
	global $lang, $lang_proficiency_options;
	$return = '';
	$return .= "<div id=\"lang{0}\">
		<p><font class=\"title\">Language Item</font>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"#\" id=\"lang_rm_btn_{0}\" value=\"{0}\" onclick=\"javascript:RemoveSpecificItem('lang',{0});return false;\" >Remove</a></p>
		<div>
			<select name=\"langs[{0}][proficiency]\" id=\"lang_proficiency{0}\" class=\"validate[required] lang{0}\">
				<option value=\"\">Select your proficiency</option>";
	$return .= bind_options($lang_proficiency_options,true);
	$return .= "</select>
			<span>in</span>
			<input value=\"\" class=\"validate[required] text-input lang{0}\" type=\"text\" name=\"langs[{0}][name]\" id=\"lang_name{0}\" placeholder=\"e.g. English\" style=\"width:250px\"/>
			</div></div>";
	return $return;
}
// End of dynamic fields

//helper functions
function bind_options($options,$is_return){		//$is_return: true or false, to determine whether to return or directly output to screen
	$return = '';
	foreach($options as $option){
		$return .= '<option value="'.$option.'"'.">$option</option>";
	}
	if($is_return)
		return $return;
	else
		echo $return;
}

function bindHiddenField($name,$value,$is_return){		//$is_return: true or false, to determine whether to return or directly output to screen
	$return = "<input type=\"hidden\" id=\"$name\" name=\"$name\" value=\"$value\"/>";
	if($is_return)
		return $return;
	else
		echo $return;
}

function bindMonthYear($monthname, $yearname, $is_return){		//$is_return: true or false, to determine whether to return or directly output to screen
	global $month_options, $year_options;
	$return = '';
	$return .= '<div>';
	$return .= '<span>Month:</span>';
	$return .= "<select name=\"$monthname\" class=\"validate[required] \" >";
	$return .= "<option value=\"\">Choose a Month</option>";
	$return .= bind_options($month_options, true);
	$return .= "</select>";
	
	$return .= "<span>Year:</span>";
	$return .= "<select name=\"$yearname\" class=\"validate[required] \" >";
	$return .= "<option value=\"\">Choose a Year</option> ";
	$return .= bind_options($year_options,true);
	$return .= "</select>";	
	$return .= "</div>";
	
	if($is_return)
		return $return;
	else
		echo $return;
}

function bindcheckbox($items,$name,$col=1,$minCheckbox=0){	//col: number of columns
	$width_percent = 100/$col;
	if($minCheckbox)
		$validate = "minCheckbox[$minCheckbox]";
	else
		$validate = '';
		
	foreach($items as $item){
		echo("<div style=\"float: left; width: $width_percent%;\"><label><input type=\"checkbox\" name=\"$name".'[]"'." class=\"validate[$validate] \" value=\"$item\"/>" );
		echo("$item  </label></div>");
	}
}
//end of helper function

?>