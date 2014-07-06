WARNING : This API will not work temporarily due to use of CAPTCHA in the eVarsity application.
===============================================================================================

SRM eVarsity API
================
An API for [*SRM University's eVarsity platform (ERP)*](http://evarsity.srmuniv.ac.in/srmswi/usermanager/youLogin.jsp) developed in PHP.


- Provides a flexible and easy to implement API in PHP to interact with the SRM University eVarsity platform.
- Uses the ["Simple Html DOM Parser"](http://simplehtmldom.sourceforge.net) to parse the DOM and extract data.

Release Highlight : 
-------------------
**Implemented functions to :**
- Login to eVarsity.
- Fetch student attendance.
- Fetch student marks.

Usage
-----

> **index.php**

> A template demonstrating the basic operations of the API.
> Logs in to *eVarsity* and fetches Info, Attendance and Marks and prints it as a table


    URL : index.php?uname=<username>&pass=<password>
    
**Functions**

> **evarsityAPI::evarsityLogin()**

> This function logs the user into eVarsity.
> The *Username* and *Password* are set by passing the values in an array to the constructor of class *evarsityAPI*

    $obj = new evarsityAPI(array(
			'uname' => $uname,
			'pass' => $password
		));
> The cookies for the session is stored in a file called **Cookies.txt**
> **Always call this function before invoking other functions**

> **evarsityAPI::fetchStudentInfo()**

> This function fetches the Student Information (General Info) from eVarsity.
> Returns data as an array which can be easily traversed through.

    $stuInfoArray = $obj->fetchStudentInfo();

    foreach($stuInfoArray as $key => $temp) {
			echo "<li>".$key." : ".$temp."</li>";
		}
	
> **evarsityAPI::fetchStudentAttendance()**

> This function fetches the student's attendance from eVarsity.
> Returns data in the form of an array.

    $stuAttArray =  $obj->fetchStudentAttendance();
    
    echo "<pre>";
    print_r($stuAttArray);
    echo "</pre>";

> **evarsityAPI::fetchStudentPerformance()**

> This function fetches the student's academic performance details from eVarsity.
> Returns data in the form of an array.

    $stuPerfArray = $obj->fetchStudentPerformance();
    
    echo "<pre>";
    print_r($stuPerfArray);
    echo "</pre>";
    
API Error Codes
---------------
All error codes are returned by the functions in arrays as 

    array("ERROR" => <error_code>);


<dl>
    <dt> SERVERDOWN </dt>
    <dd> Either the eVarsity server is down or is undergoing scheduled maintainence during when it cannot be accessed </dd>
    <dt> NOTLOGGEDIN </dt>
    <dd> Unable to login or session timed out with server </dd>
</dl>



>This API is a work under progress.

*Developed By*

Abishek | **Nair**
