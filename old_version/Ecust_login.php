<?php
require ("simple_html_dom.php");

date_default_timezone_get("Asia/Chongqing");
$EcustID = "";//填入学号
$EcustPW = "";//填入密码（jwc的）
$EcustID = "10142045";
$EcustPW = "10142045";

if (($EcustID == "") && ($EcustPW == "")) {
	$EcustID = $_GET['id'];
	$EcustPW = $_GET['pw'];

}



// 
// $function_num = $_GET['func'];

class Ecust{
	public $curlobj;
	public $html;

	public $name; // 个人信息->姓名
	public $phone; // 个人信息->手机 （未获取）
	public $gender; // 个人信息->姓别
	public $dorm; // 个人信息->寝室
	public $class; // 个人信息->班级
	public $major; // 个人信息->专业
	public $department; // 个人信息->学院
	public $timeOfEnrollment; // 个人信息->入学时间

	//public $success = false;
	public function __construct($EcustID,$EcustPW){
		// 发送Post并获取Cookie：Start
		$data="__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwUJMTg2MzE1NTYyD2QWAgIBD2QWAgIGDw8WAh4EVGV4dAVQ5a2m55Sf5Yid5aeL5a%2BG56CB5Li66Lqr5Lu96K%2BB5Y%2B35ZCO5YWt5L2N44CC5a%2BG56CB6ZW%2F5bqm5LiN6LaF6L%2BHMTDkuKrlrZfnrKbjgIJkZGTItFe6UDnNqdE2sz592HXKwZ7Fhw%3D%3D&TxtStudentId=".$EcustID."&TxtPassword=".$EcustPW."&BtnLogin=%E7%99%BB%E5%BD%95&__EVENTVALIDATION=%2FwEWBALplYnsCgK%2Fycb4AQLVqbaRCwLi44eGDNL1%2FUVfta6zTJ9DMRXMNe6Ao6Wm";
		$this->curlobj = curl_init();
		curl_setopt($this->curlobj,CURLOPT_URL, 'http://202.120.108.14/ecustedu/K_StudentQuery/K_StudentQueryLogin.aspx');
		curl_setopt($this->curlobj, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curlobj,CURLOPT_COOKIESESSION,true);
		curl_setopt($this->curlobj,CURLOPT_COOKIEFILE,"cookiefile");
		curl_setopt($this->curlobj,CURLOPT_COOKIEJAR,"cookiefile");
		curl_setopt($this->curlobj, CURLOPT_COOKIE, session_name()."=".session_id());
		curl_setopt($this->curlobj, CURLOPT_HEADER, 0);
		curl_setopt($this->curlobj, CURLOPT_FOLLOWLOCATION, 1);

		curl_setopt($this->curlobj,CURLOPT_POST,1);
		curl_setopt($this->curlobj,CURLOPT_POSTFIELDS,$data);
		curl_setopt($this->curlobj, CURLOPT_HTTPHEADER, array("application/x-www-form-urlencoded;charset=utf-8",
			"Content-length:".strlen($data)
			));
		curl_exec($this->curlobj);
		// 发送Post并获取Cookie：End

		// 检验是否成功（实际没卵用）：Start
		curl_setopt($this->curlobj,CURLOPT_URL,"http://202.120.108.14/ecustedu/K_StudentQuery/K_StudentQueryLeft.aspx");
		curl_setopt($this->curlobj,CURLOPT_POST,0);
		curl_setopt($this->curlobj,CURLOPT_HTTPHEADER,array("Content-type: text/xml"));
		$opt=curl_exec($this->curlobj);
		//curl_close($this->curlobj);
		//echo $opt;

		if (strpos($opt,"您好") !== false){
			$this->success = true ;
			echo "Suceess Login";
		}
		else {
			
			$this->success = false;
		}
		// 检验是否成功（实际没卵用）：End
		
		
		// 初始化 个人信息
		curl_setopt($this->curlobj,CURLOPT_URL,"http://202.120.108.14/ecustedu/K_StudentQuery/K_StudentInformationDetail.aspx?key=0");
		$opt=curl_exec($this->curlobj);
		$this->html = new DOMDocument();
		$html_source = str_replace("gb2312" , "utf-8" ,$opt);
		@$this->html->loadHTML($html_source);
		$this->html->validateOnParse = true;
		$arrayPersonInfo =new ArrayObject(array());
		foreach ( $this->html->getElementsByTagName("input") as $key){
			$arrayPersonInfo->append($key->getAttribute("value"));
		}
		$this->name = $arrayPersonInfo[17];
		$this->gender = $arrayPersonInfo[13];
		$this->dorm = $arrayPersonInfo[27];
		$this->class = $arrayPersonInfo[5];
		$this->major = $arrayPersonInfo[4];
		$this->department = $arrayPersonInfo[29];
		$this->timeOfEnrollment = $arrayPersonInfo[32];



		//echo $name;

		
	}


	public function curl_POST($url,$data){
		/**
		 * 方便Post用
		 * 
		 */
		curl_setopt($this->curlobj,CURLOPT_URL, $url);
		curl_setopt($this->curlobj, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curlobj,CURLOPT_COOKIESESSION,true);
		curl_setopt($this->curlobj,CURLOPT_COOKIEFILE,"cookiefile");
		curl_setopt($this->curlobj,CURLOPT_COOKIEJAR,"cookiefile");
		curl_setopt($this->curlobj, CURLOPT_COOKIE, session_name()."=".session_id());
		curl_setopt($this->curlobj, CURLOPT_HEADER, 0);
		curl_setopt($this->curlobj, CURLOPT_FOLLOWLOCATION, 1);

		curl_setopt($this->curlobj,CURLOPT_POST,1);
		curl_setopt($this->curlobj,CURLOPT_POSTFIELDS,$data);
		curl_setopt($this->curlobj, CURLOPT_HTTPHEADER, array("application/x-www-form-urlencoded;charset=utf-8",
			"Content-length:".strlen($data)
			));
		$opt = curl_exec($this->curlobj);
		return $opt;
		//curl_close($this->curlobj);
	}


	public function changePW($newPW){
		$data = "__VIEWSTATE=%2FwEPDwUKMTYzMjI1NzMyN2Rk6a%2BHx5mRWLuy0dJqc2wHB2274JE%3D&txtNewPwd1=".$newPW."&txtNewPwd2=".$newPW."&BtnOK=%E6%8F%90%E4%BA%A4&__EVENTVALIDATION=%2FwEWBAKll4qhCgL7mJ6BBAL7mJqBBAL9mpmPAVpXeLI7SJ4Gqk%2BqA%2BCwBw1nWJFC";
		$url = "http://202.120.108.14/ecustedu/StudentChangePassword.aspx";
		$this->curl_POST($url,$data);

	}


	public function XuanKeXinXi($year,$term){

		/**
		 * year 年份 例如1996 2014（只能填在校时间！
		 * term 学期 1（上学期）或2（下学期）
		 * 
		 */
		$data = "__VIEWSTATE=%2FwEPDwUKMTM4MjMzNDI4NQ9kFgICAQ9kFgQCAw8QDxYGHg1EYXRhVGV4dEZpZWxkBQhZZWFyVGVybR4ORGF0YVZhbHVlRmllbGQFCFllYXJUZXJtHgtfIURhdGFCb3VuZGdkEBUCBTIwMTQxBTIwMTQyFQIFMjAxNDEFMjAxNDIUKwMCZ2dkZAIJDzwrAAsAZGSMX4%2FWtLgBIQKKTJKe0kctZawz%2Bg%3D%3D&drpdwn_YearTerm=".$year.$term."&bttn_search=%E6%9F%A5%E8%AF%A2&__EVENTVALIDATION=%2FwEWBAKers%2B8CQKkoPSnBgKroPSnBgK1man8CWwzHLXGvjcGP5XoZl2avsaU%2BolS";
		$url = "http://202.120.108.14/ecustedu/E_SelectCourse/ScInFormation/syllabusHistory.aspx";
		$opt = $this->curl_POST($url,$data);
		//通过切割截取数据（官网太丑= =
		//
		//如果有人看不下去可以正则重写一下。。。
		//
		//echo $opt;
		$part_1_Array = explode("<tr class=\"headcenter\" bgcolor=\"#6699FF\">" , $opt);
		//echo $part_1_Array[0]."cut here";
		//echo $part_1_Array[1];
		$part_2_Array = explode("</table></td>", $part_1_Array[1]);
		//var_dump($part_2_Array);
		$table = str_replace(" class=\"griditem\" onmouseout=\"javascript:this.style.backgroundColor='#dedfde';\" onmouseover=\"javascript:this.style.backgroundColor='#fff7ce';cursor='hand';\"", "", $part_2_Array[0]);
		$table = str_replace(" class=\"gridalteritem\" onmouseout=\"javascript:this.style.backgroundColor='#ffffff';\" onmouseover=\"javascript:this.style.backgroundColor='#fff7ce';cursor='hand';\"" , "" , $table);
		$table = str_replace("</tr><tr>","",$table);
		$table = str_replace("</tr>", "", $table);
		$table = str_replace("</td>","<td>" ,$table);
		$table = str_replace("td width=\"30\"","td" ,$table);
		$table = str_replace("\n" ,"" ,$table);
		$table = str_replace("\t", "" ,$table);
		$table = str_replace(" ", "" ,$table);
		$table = str_replace("<td>","td",$table);
		$table = str_replace("tdtd","td",$table);

		$Array = explode("td" ,$table);

		
		/**
		 *	$Array结果介绍：
		 *	首先
		 *	0 => ""
		 *	1 => "课程名"
		 *	2 => "教师"
		 *  3 => "开课系"
		 *  4 => "学分"
		 *  5 => "课程性质"
		 *  其次
		 *  每个序号mod 6 所得值对应上面的内容
		 * 
		 */
		var_dump($Array);

	}


	public function KaoShiBiao($year ,$term){
		$data = "__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwULLTE2NTU5MjUyNDUPZBYCAgEPZBYCAgEPZBYIAgEPZBYCZg9kFgQCAQ8QZBAVDQnor7fpgInmi6kFMjAxNTIFMjAxNTEFMjAxNDIFMjAxNDEFMjAxMzIFMjAxMzEFMjAxMjIFMjAxMjEFMjAxMTIFMjAxMTEFMjAxMDIFMjAxMDEVDQnor7fpgInmi6kFMjAxNTIFMjAxNTEFMjAxNDIFMjAxNDEFMjAxMzIFMjAxMzEFMjAxMjIFMjAxMjEFMjAxMTIFMjAxMTEFMjAxMDIFMjAxMDEUKwMNZ2dnZ2dnZ2dnZ2dnZ2RkAggPEGRkFgFmZAICD2QWAmYPZBYCZg8PFgIeBFRleHRlZGQCAw9kFgJmD2QWBmYPDxYCHwAFEeWtpuWPt%2B%2B8mjEwMTQyMDQ1ZGQCAg8PFgIfAAUS5aeT5ZCN77ya5q%2Bb6LCm5piCZGQCBA8PFgIfAAUZMjAxNS0yMDE25a2m5bm056ysMeWtpuacn2RkAgQPZBYCZg9kFgRmDzwrAAsBAA8WCB4IRGF0YUtleXMWAB4LXyFJdGVtQ291bnQCCR4JUGFnZUNvdW50AgEeFV8hRGF0YVNvdXJjZUl0ZW1Db3VudAIJZBYCZg9kFhICAQ9kFgxmDw8WAh8ABSPogIHlrZDmgJ3mg7PkuI7nvo7lm73miI%2Fliaco5Y%2BM6K%2BtKWRkAgEPDxYCHwAFCjE1MTQwMTgyMDFkZAICDw8WAh8ABQRFNDAxZGQCAw8PFgIfAAUlMjAxNSDlubQxMiDmnIgxNSDml6XmmZrkuIogNjowMC04OjAwIGRkAgQPDxYCHwAFBue7k%2Badn2RkAgUPDxYCHwAFCeWQtOaJv%2BmSp2RkAgIPZBYMZg8PFgIfAAUS5Yqo5oCB572R6aG16K6%2B6K6hZGQCAQ8PFgIfAAUKMTUxNDAxMzEwMWRkAgIPDxYCHwAFBEUzMjFkZAIDDw8WAh8ABSQyMDE1IOW5tDEyIOaciDE3IOaXpeaZmuS4ijU6NTAtNzo1MCBkZAIEDw8WAh8ABQbnu5PmnZ9kZAIFDw8WAh8ABQnorrjljavmnpdkZAIDD2QWDGYPDxYCHwAFDOiQpemUgOeuoeeQhmRkAgEPDxYCHwAFCjE1MTIwMzgyMDNkZAICDw8WAh8ABQRCMTAxZGQCAw8PFgIfAAUkMjAxNiDlubQxIOaciDQg5pel5LiK5Y2IIDk6MzAtMTE6MzAgZGQCBA8PFgIfAAUG57uT5p2fZGQCBQ8PFgIfAAUJ6ZmI5bO75p2%2BZGQCBA9kFgxmDw8WAh8ABRLlpKflraboi7Hor63kuInnuqdkZAIBDw8WAh8ABQoxNTEyMDM4NTAxZGQCAg8PFgIfAAUEQjIwMmRkAgMPDxYCHwAFJDIwMTYg5bm0MSDmnIg1IOaXpeS4iuWNiCA5OjMwLTExOjMwIGRkAgQPDxYCHwAFBue7k%2Badn2RkAgUPDxYCHwAFCeW8oOmDneiOiWRkAgUPZBYMZg8PFgIfAAU75q%2Bb5rO95Lic5oCd5oOz5ZKM5Lit5Zu954m56Imy56S%2B5Lya5Li75LmJ55CG6K665L2T57O7KOS4iilkZAIBDw8WAh8ABQoxNTEyMDAyOTAxZGQCAg8PFgIfAAUEQTIwNWRkAgMPDxYCHwAFIzIwMTYg5bm0MSDmnIg2IOaXpeS4i%2BWNiCAyOjAwLTQ6MDAgZGQCBA8PFgIfAAUG57uT5p2fZGQCBQ8PFgIfAAUG5Yav5YabZGQCBg9kFgxmDw8WAh8ABQ%2FotKLliqHkvJrorqEoSSlkZAIBDw8WAh8ABQoxNTEyMDM4MzAyZGQCAg8PFgIfAAUEQjMwMmRkAgMPDxYCHwAFJDIwMTYg5bm0MSDmnIg3IOaXpeS4iuWNiCA5OjMwLTExOjMwIGRkAgQPDxYCHwAFBue7k%2Badn2RkAgUPDxYCHwAFCemZiOWwj%2BW5s2RkAgcPZBYMZg8PFgIfAAUY5qaC546H6K665LiO5pWw55CG57uf6K6hZGQCAQ8PFgIfAAUKMTUxMjAzODAwMmRkAgIPDxYCHwAFBEExMDRkZAIDDw8WAh8ABSQyMDE2IOW5tDEg5pyIOCDml6XkuIrljYggOTozMC0xMTozMCBkZAIEDw8WAh8ABQbnu5PmnZ9kZAIFDw8WAh8ABQbmuKnmtptkZAIID2QWDGYPDxYCHwAFDOe6v%2BaAp%2BS7o%2BaVsGRkAgEPDxYCHwAFCjE1MTIwMzc5MDJkZAICDw8WAh8ABQRCMTAyZGQCAw8PFgIfAAUlMjAxNiDlubQxIOaciDExIOaXpeS4iuWNiCA5OjMwLTExOjMwIGRkAgQPDxYCHwAFBue7k%2Badn2RkAgUPDxYCHwAFCeeOi%2BWHoeWHoWRkAgkPZBYMZg8PFgIfAAUS55S15a2Q5ZWG5Yqh5qaC6K66ZGQCAQ8PFgIfAAUKMTUxMjAzODEwMWRkAgIPDxYCHwAFBEExMDFkZAIDDw8WAh8ABSQyMDE2IOW5tDEg5pyIMTQg5pel5LiL5Y2IIDI6MDAtNDowMCBkZAIEDw8WAh8ABQbnu5PmnZ9kZAIFDw8WAh8ABQblkajkvJ9kZAICDw8WAh4HVmlzaWJsZWdkZGRy%2FSTkh%2BjBMNiVzVEpk%2FfsCuKeAw%3D%3D&ddlYearTerm=".$year.$term."&btnSelect=%E6%9F%A5%E8%AF%A2&RdbCourse=%E4%B8%AA%E4%BA%BA%E8%80%83%E8%AF%95%E8%A1%A8&__EVENTVALIDATION=%2FwEWEgLai%2BvnBALekp65DQKP%2BokJAoD6iQkCj%2FqdogsCgPqdogsCj%2FrhxgICgPrhxgICj%2Fr1mwoCgPr1mwoCj%2FrZvAUCgPrZvAUCj%2Fqt0QwCgPqt0QwC2sfb1QYCuaHTqAgCj%2FnpnQ4CwZTn4wj2HJgE82CKqJCUCxGEAZ384PkhZQ%3D%3D";
		//真JB长
		$url = "http://202.120.108.14/ecustedu/K_StudentQuery/K_TestTableDetail.aspx";
		$opt = $this->curl_POST($url,$data);

		echo $opt;

		// 又到了切割机上场的时候了
	}


	public function KeChenBiao(){
		$data = "__VIEWSTATE=%2FwEPDwUKLTg3NzgzODIwNw9kFgICAQ9kFgYCAw8QDxYGHg1EYXRhVGV4dEZpZWxkBQhZZWFyVGVybR4ORGF0YVZhbHVlRmllbGQFAnNtHgtfIURhdGFCb3VuZGdkEBUCBTIwMTUyBTIwMTUxFQIJ5LiL5a2m5pyfCeacrOWtpuacnxQrAwJnZ2RkAgcPZBYMAgEPZBYOAgEPDxYIHgdSb3dTcGFuAgIeBFRleHQFEOWkmuWFg%2Be7n%2BiuoeWtpjUeCUJhY2tDb2xvcgocHgRfIVNCAghkZAICDw8WCB8DAgIfBAUM6L%2BQ6JCl566h55CGHwUKHB8GAghkZAIDDw8WCB8DAgIfBAUJ6L%2BQ56255a2mHwUKHB8GAghkZAIEDw8WCB8DAgIfBAUZ6auY562J5pWw5a2mKOS4iyk6OeWtpuWIhh8FChwfBgIIZGQCBQ8PFggfAwICHwRlHwUKGx8GAghkZAIGDw8WCB8DAgIfBAUS5LiT5Lia6K6k6K%2BG5a6e6Le1HwUKHB8GAghkZAIHDw8WCB8DAgIfBGUfBQobHwYCCGRkAgMPZBYOAgEPDxYIHwMCAh8EBQnov5DnrbnlraYfBQocHwYCCGRkAgIPDxYIHwMCAh8EBRLlvaLlir%2FkuI7mlL%2FnrZYoNCkfBQocHwYCCGRkAgMPDxYIHwMCAh8EBRDlpJrlhYPnu5%2ForqHlraY1HwUKHB8GAghkZAIEDw8WCB8DAgIfBAUM6L%2BQ6JCl566h55CGHwUKHB8GAghkZAIFDw8WCB8DAgIfBGUfBQobHwYCCGRkAgYPDxYIHwMCAh8EZR8FChsfBgIIZGQCBw8PFggfAwICHwRlHwUKGx8GAghkZAIFD2QWDgIBDw8WCB8DAgIfBAUY566h55CG5L%2Bh5oGv57O757uf5a%2B86K66HwUKHB8GAghkZAICDw8WCB8DAgIfBAU25Lit5Zu95paH5YyW5a%2B86K66MeWtpuWIhjxicj7pq5jnrYnmlbDlraYo5LiLKTo55a2m5YiGHwUKHB8GAghkZAIDDw8WCB8DAgIfBAU75q%2Bb5rO95Lic5oCd5oOz5ZKM5Lit5Zu954m56Imy56S%2B5Lya5Li75LmJ55CG6K665L2T57O7KOS4iykfBQocHwYCCGRkAgQPDxYIHwMCAh8EBQnkvZPogrIoNCkfBQocHwYCCGRkAgUPDxYIHwMCAh8EZR8FChsfBgIIZGQCBg8PFggfAwICHwRlHwUKGx8GAghkZAIHDw8WCB8DAgIfBGUfBQobHwYCCGRkAgcPZBYOAgEPDxYIHwMCAh8EBQ%2Fkv6Hmga%2Fnu4%2FmtY7lraYfBQocHwYCCGRkAgIPDxYIHwMCAh8EBRjkuJPkuJrlpJbor60o566h55CG57G7KTYfBQocHwYCCGRkAgMPDxYIHwMCAh8EBRjkurrlipvotYTmupDnrqHnkIbmpoLorrofBQocHwYCCGRkAgQPDxYIHwMCAh8EBRDph5Hono3lraYy5a2m5YiGHwUKHB8GAghkZAIFDw8WCB8DAgIfBGUfBQobHwYCCGRkAgYPDxYIHwMCAh8EZR8FChsfBgIIZGQCBw8PFggfAwICHwRlHwUKGx8GAghkZAIJD2QWDgIBDw8WCB8DAgIfBGUfBQobHwYCCGRkAgIPDxYIHwMCBB8EBSrmpoLnjoforrrkuI7mlbDnkIbnu5%2ForqHvvIjlvIDnj63ph43or7vvvIkfBQocHwYCCGRkAgMPDxYIHwMCAh8EZR8FChsfBgIIZGQCBA8PFggfAwIEHwQFGemrmOetieaVsOWtpijkuIspOjnlrabliIYfBQocHwYCCGRkAgUPDxYIHwMCAh8EZR8FChsfBgIIZGQCBg8PFggfAwICHwRlHwUKGx8GAghkZAIHDw8WCB8DAgIfBGUfBQobHwYCCGRkAgsPZBYOAgEPDxYIHwMCAh8EZR8FChsfBgIIZGQCAg8PFgofAwICHwRlHwUKGx8GAggeB1Zpc2libGVoZGQCAw8PFggfAwICHwRlHwUKGx8GAghkZAIEDw8WCh8DAgIfBGUfBQobHwYCCB8HaGRkAgUPDxYIHwMCAh8EZR8FChsfBgIIZGQCBg8PFggfAwICHwRlHwUKGx8GAghkZAIHDw8WCB8DAgIfBGUfBQobHwYCCGRkAgkPFgIfBAWiQDx0YWJsZSBhbGlnbj1jZW50ZXIgYm9yZGVyPTEgYm9yZGVyY29sb3JkYXJrPSNmZmZmZmYgY2VsbHNwYWNpbmc9MCBjZWxscGFkZGluZz0wIGJvcmRlcmNvbG9ybGlnaHQ9IzAwMDAwMCB3aWR0aD0xMDAlPjx0ciBiZ2NvbG9yPUdhaW5zYm9ybz48dGQgYWxpZ249Y2VudGVyPjxiPuivvueoi%2BWQjTwvYj48L3RkPjx0ZCBhbGlnbj1jZW50ZXI%2BPGI%2B6K%2B%2B56iL5Y%2B3PC9iPjwvdGQ%2BPHRkIGFsaWduPWNlbnRlcj48Yj7mlZnluIg8L2I%2BPC90ZD48dGQgYWxpZ249Y2VudGVyPjxiPuaXtumXtDwvYj48L3RkPjx0ZCBhbGlnbj1jZW50ZXI%2BPGI%2B5pWZ5a6kPC9iPjwvdGQ%2BPHRkIGFsaWduPWNlbnRlcj48Yj7lvIDor77ns7s8L2I%2BPC90ZD48dGQgYWxpZ249Y2VudGVyPjxiPuWtpuWIhjwvYj48L3RkPjx0ZCBhbGlnbj1jZW50ZXI%2BPGI%2B6K%2B%2B56iL5oCn6LSoPC9iPjwvdGQ%2BPC90cj48dHIgaGVpZ2h0PTI0Pjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT7mr5vms73kuJzmgJ3mg7PlkozkuK3lm73nibnoibLnpL7kvJrkuLvkuYnnkIborrrkvZPns7so5LiLKTwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPjE1MjIwNDQzMDE8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT7lpZrlu7rmraY8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0Pjxmb250IHNpemU9MT7lkajkuIkgNS026IqCIDEtMTjlhajlkag8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yND48Zm9udCBzaXplPTE%2BRDEwNDwvZm9udD48L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT7kuKTor77mlZnogrLkuK3lv4M8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT4zLjA8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT7lhazlhbHln7rnoYDor748L3RkPjwvdHI%2BPHRyIGhlaWdodD0yND48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B5Lit5Zu95paH5YyW5a%2B86K66MeWtpuWIhjwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPjE1MjIwNDQ0MDE8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT7mnLHlv4blpKk8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0Pjxmb250IHNpemU9MT7lkajkuowgNS026IqCIDEtOOWFqOWRqDwvZm9udD48L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0Pjxmb250IHNpemU9MT5BMTA0PC9mb250PjwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPuaWh%2BWMlue0oOi0qOaVmeiCsuS4reW%2FgzwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPjEuMDwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPuWFrOWFseWfuuehgOivvjwvdGQ%2BPC90cj48dHIgaGVpZ2h0PTI0Pjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT7kvZPogrIoNCk8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT4xNTIyMDQ0NTAxPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B5ZC05Lyf5b%2BgPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yND48Zm9udCBzaXplPTE%2B5ZGo5ZubIDUtNuiKgiAxLTE45YWo5ZGoPC9mb250PjwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQ%2BPGZvbnQgc2l6ZT0xPuS9k%2BiCsuWcujI8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B5L2T6IKy57O7PC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2BMS4wPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B5YWs5YWx5Z%2B656GA6K%2B%2BPC90ZD48L3RyPjx0ciBoZWlnaHQ9MjQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPuW9ouWKv%2BS4juaUv%2Betlig0KTwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPjE1MjIwNDQ2MDE8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT7njovluIU8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0Pjxmb250IHNpemU9MT7lkajkuowgMy006IqCIDgtOeWFqOWRqDwvZm9udD48L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0Pjxmb250IHNpemU9MT5BMjA1PC9mb250PjwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPuWtpuW3pemDqDwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPjAuMzwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPuWFrOWFseWfuuehgOivvjwvdGQ%2BPC90cj48dHIgaGVpZ2h0PTI0Pjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49Mz7pq5jnrYnmlbDlraYo5LiLKTo55a2m5YiGPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTM%2BMTUyMzAzMjkwMTwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0zPui1teW7uuS4mzwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9OD48Zm9udCBzaXplPTE%2B5ZGo5LqMIDUtNuiKgiAxLTE45YWo5ZGoPC9mb250PjwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9OD48Zm9udCBzaXplPTE%2BQTMwMTwvZm9udD48L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49Mz7mlbDlrabns7s8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49Mz40LjA8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49Mz7lhazlhbHpgInkv67or748L3RkPjwvdHI%2BPHRyID48dGQgYWxpZ249Y2VudGVyIGhlaWdodD04Pjxmb250IHNpemU9MT7lkajlm5sgMS0y6IqCIDEtMTjlhajlkag8L2ZvbnQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9OD48Zm9udCBzaXplPTE%2BQTMwMTwvZm9udD48L3RkPjwvdHI%2BPHRyID48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0xMj48Zm9udCBzaXplPTE%2B5ZGo5ZubIDktMTHoioIgMi0xOOWNleWRqDwvZm9udD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0xMj48Zm9udCBzaXplPTE%2BQTMwMzwvZm9udD48L3RkPjwvdHI%2BPHRyIGhlaWdodD0yND48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B5LiT5Lia6K6k6K%2BG5a6e6Le1PC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2BMTUyMjA0MzkwMzwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPuWImOeShzwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQ%2BPGZvbnQgc2l6ZT0xPuWRqOWFrSAxLTLoioIgMS0xOOWFqOWRqDwvZm9udD48L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0Pjxmb250IHNpemU9MT7kuI3pnIDopoHmlZnlrqQ8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B566h55CG56eR5a2m5LiO5bel56iL57O7PC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2BMi4wPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B5YW25a6DICAgICAgPC90ZD48L3RyPjx0ciBoZWlnaHQ9MjQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPueuoeeQhuS%2FoeaBr%2Bezu%2Be7n%2BWvvOiuujwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPjE1MjIwNDI0MDI8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT7lkajkvJ88L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0Pjxmb250IHNpemU9MT7lkajkuIAgNS026IqCIDEtMTjlhajlkag8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yND48Zm9udCBzaXplPTE%2BRDUwNDwvZm9udD48L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT7nrqHnkIbnp5HlrabkuI7lt6XnqIvns7s8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT4yLjA8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT7lrabnp5Hln7rnoYDmlZk8L3RkPjwvdHI%2BPHRyIGhlaWdodD0yND48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTI%2B5aSa5YWD57uf6K6h5a2mNTwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0yPjE1MjIwNDI5MDI8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49Mj7ku7vpo548L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTEyPjxmb250IHNpemU9MT7lkajkuIAgMS0y6IqCIDEtMTjlj4zlkag8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTI%2BPGZvbnQgc2l6ZT0xPkIyMDE8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTI%2B6YeR6J6N5a2m57O7PC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTI%2BMy4wPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTI%2B5a2m56eR5Z%2B656GA5pWZPC90ZD48L3RyPjx0ciA%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MTI%2BPGZvbnQgc2l6ZT0xPuWRqOS4iSAzLTToioIgMS0xOOWFqOWRqDwvZm9udD48L3RyPjx0ciBoZWlnaHQ9MjQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPuS4k%2BS4muWkluivrSjnrqHnkIbnsbspNjwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPjE1MjIwNDM0MDE8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT7mm7nmr4XnhLY8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0Pjxmb250IHNpemU9MT7lkajkuowgNy046IqCIDEtMTjlhajlkag8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2BPGZvbnQgc2l6ZT0xPkQzMDI8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B5aSn57G7PC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2BMi4wPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B5a2m56eR5Z%2B656GA5pWZPC90ZD48L3RyPjx0ciBoZWlnaHQ9MjQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0yPui%2FkOetueWtpjwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0yPjE1MjIwNDM2MDE8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49Mj7lkLTkuIDluIY8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTEyPjxmb250IHNpemU9MT7lkajkuIAgMy006IqCIDYtMTjlhajlkag8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTI%2BPGZvbnQgc2l6ZT0xPkU0MTU8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTI%2B566h55CG56eR5a2m5LiO5bel56iL57O7PC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTI%2BMy4wPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTI%2B5a2m56eR5Z%2B656GA5pWZPC90ZD48L3RyPjx0ciA%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MTI%2BPGZvbnQgc2l6ZT0xPuWRqOS4iSAxLTLoioIgNi0xOOWFqOWRqDwvZm9udD48L3RyPjx0ciBoZWlnaHQ9MjQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0yPui%2FkOiQpeeuoeeQhjwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0yPjE1MjIwNDM3MDE8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49Mj7pmbbls7A8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTEyPjxmb250IHNpemU9MT7lkajkuowgMS0y6IqCIDEtMTjljZXlkag8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTI%2BPGZvbnQgc2l6ZT0xPkQ0MDQ8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTI%2B566h55CG56eR5a2m5LiO5bel56iL57O7PC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTI%2BMy4wPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTI%2B5a2m56eR5Z%2B656GA5pWZPC90ZD48L3RyPjx0ciA%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MTI%2BPGZvbnQgc2l6ZT0xPuWRqOWbmyAzLTToioIgMS0xOOWFqOWRqDwvZm9udD48L3RyPjx0ciBoZWlnaHQ9MjQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPuS6uuWKm%2Bi1hOa6kOeuoeeQhuamguiuujwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPjE1MjIwNDQwMDM8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT7njovlvLo8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0Pjxmb250IHNpemU9MT7lkajkuIkgNy046IqCIDEtMTjlhajlkag8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2BPGZvbnQgc2l6ZT0xPkQ1MDg8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B5bel5ZWG566h55CG57O7PC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2BMi4wPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B5a2m56eR5Z%2B656GA5pWZPC90ZD48L3RyPjx0ciBoZWlnaHQ9MjQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPuamgueOh%2BiuuuS4juaVsOeQhue7n%2Biuoe%2B8iOW8gOePremHjeivu%2B%2B8iTwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPjE1MjQwMTQwMDI8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT7kv57nu43mloc8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0Pjxmb250IHNpemU9MT7lkajkuowgOS0xMeiKgiAzLTE45YWo5ZGoPC9mb250PjwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPjxmb250IHNpemU9MT7kuI3pnIDopoHmlZnlrqQ8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B5pWw5a2m57O7PC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2BMy4wPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B5a2m56eR5Z%2B656GA5pWZPC90ZD48L3RyPjx0ciBoZWlnaHQ9MjQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPuS%2FoeaBr%2Be7j%2Ba1juWtpjwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPjE1MjIwNDM4MDE8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT7pqazmtbfoi7E8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0Pjxmb250IHNpemU9MT7lkajkuIAgNy046IqCIDEtMTjlhajlkag8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2BPGZvbnQgc2l6ZT0xPkQ0MDI8L2ZvbnQ%2BPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B566h55CG56eR5a2m5LiO5bel56iL57O7PC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2BMi4wPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B5LiT5Lia6YCJ5L%2Bu6K%2B%2BPC90ZD48L3RyPjx0ciBoZWlnaHQ9MjQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPumHkeiejeWtpjLlrabliIY8L3RkPjx0ZCBhbGlnbj1jZW50ZXIgaGVpZ2h0PTI0IHJvd3NwYW49MT4xNTIyMDQ0MjAxPC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yNCByb3dzcGFuPTE%2B5YiY5bu65Zu9PC90ZD48dGQgYWxpZ249Y2VudGVyIGhlaWdodD0yND48Zm9udCBzaXplPTE%2B5ZGo5ZubIDctOOiKgiAxLTE45YWo5ZGoPC9mb250PjwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPjxmb250IHNpemU9MT5FMjIxPC9mb250PjwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPumHkeiejeWtpuezuzwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPjIuMDwvdGQ%2BPHRkIGFsaWduPWNlbnRlciBoZWlnaHQ9MjQgcm93c3Bhbj0xPuS4k%2BS4mumAieS%2FruivvjwvdGQ%2BPC90cj48L1RhYmxlPmRkQ9kOL513%2Fh7O0z%2BSPrFHg%2BCdyKM%3D&selyeartermflag=%E4%B8%8B%E5%AD%A6%E6%9C%9F&bttn_search=%E6%9F%A5%E8%AF%A2&__EVENTVALIDATION=%2FwEWBAK34di7CAKukO%2FqDwLJpuDqDwK1man8CQITP7yaJJfvC0yMN4tjqnyN2fmy";
		//wo cao ...
		$url = "http://202.120.108.14/ecustedu/E_SelectCourse/ScInFormation/syllabus.aspx";
		$opt = $this->curl_POST($url,$data);

		$html = str_get_html($opt);
		foreach ( $html->find("tr[height='24']") as $element ){
			echo $element->plaintext . "<br>";

		};

	}



}

$test = new Ecust($EcustID,$EcustPW);

//$test->KeChenBiao();

//$test->KaoShiBiao(2015,1);

//$test->XuanKeXinXi(2014,1);

//$tset->changePW('woshishabi');