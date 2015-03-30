<?php
/*
Template Name: suspend_frame
*/
if(!isset($_SESSION)) session_start();

if($_SESSION['user_id'] == "")
{
	echo "Please, login!";
	exit();
}


$program_id = $_GET['program_id'];
$user_id = $_GET['user_id'];

get_header();

?>


<head>


</head>

<body style="margin: 0px">
    <div id="voice-test-page" class="voice-test-page" style="width: 380px; margin-left: auto; margin-right: auto; font-family: 'Myriad Pro';">
        <div align="left" id="rootDiv" class="voice-test-form">
            <div style="background-color:#fff; width: 100%; height:300px; position: relative; padding-top: 1px; padding-left: 15px;">
                <p style="line-height: 30px; padding-top: 1%"><b style="color: #af121e; margin-left: 30%">Choose an option: </b><Br>
                    <input type="radio" name="option" value="suspend" checked onchange="onOptionChange()">Suspend this user and delete this video<Br>
                        <input type="radio" name="period" style="margin-left: 10%;" value="1" checked>24 hours<Br>
                        <input type="radio" name="period" style="margin-left: 10%;" value="2">1 week<Br>
                        <input type="radio" name="period" style="margin-left: 10%;" value="3">Permanently<Br>
                    <input type="radio" name="option" value="warning" onchange="onOptionChange()"> Send this user a warning message and delete this video<Br>
                </p>

<!--                <span style="padding-bottom: 6%; float: left; width: 90%; line-height: 150%;">Are you sure you want to suspend this user and delete this video?</span>-->
                <div style="width: 57%; float: left; padding-left: 15%">
                    <input type="button" style="width: 90px; height: 30px; font-size: 100%; font-family: 'Myriad Pro'; background-color: white; border-color: #af121e; border-width: 2px; color: #af121e; float: left" onclick="suspendUser()" value="Suspend"/>
                    <input type="button" style="width: 90px; height: 30px; font-size: 100%; font-family: 'Myriad Pro'; background-color: white; border-color: #af121e; border-width: 2px; color: #af121e; float: right" onclick="parent.fn_closePopup()" value="Cancel"/>
                </div>
            </div>
        </div>
    </div>
    <br>

    <script type="text/javascript">

        document.getElementById("main-header").style.display = "none";
        document.getElementById("headerShadowDiv").style.display = "none";


        var program_id = "<?php echo $program_id ?>";
        var user_id = "<?php echo $user_id ?>";

        </script>
    <script type="text/javascript">

        function onOptionChange()
        {
            var period = document.getElementsByName("period");

            if (document.getElementsByName("option")[0].checked)
            {
                for (var i = 0; i < period.length; i++)
                {
                    period[i].disabled = false;

                }
            }
            else
            {
                for (var i = 0; i < period.length; i++)
                {
                    period[i].disabled = true;
                }
            }
        }

        function suspendUser()
        {
            var period = document.getElementsByName("period");
            var selectedPeriod = "";

            if (document.getElementsByName("option")[0].checked)
            {
                for (var i = 0; i < period.length; i++)
                {
                    if(period[i].checked == true)
                    {
                        switch (i) {
                            case 0:
                                selectedPeriod = 86400;
                                break;
                            case 1:
                                selectedPeriod = 604800;
                                break;
                            case 2:
                                selectedPeriod = -1;
                                break;
                            default:
                                selectedPeriod = "";
                        }
                        break;
                    }
                }

                if (selectedPeriod == "") return;

                if (window.XMLHttpRequest)
                {
                    xmlhttp=new XMLHttpRequest();
                }
                else
                {
                    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange=function()
                {
                    if(xmlhttp.readyState==4 && xmlhttp.status==200)
                    {
                        if (xmlhttp.responseText == "success")
                        {
                            alert("Suspended!")
                        }
                        else
                        {
                            alert("Error. Please, try again later");
                        }

                        parent.fn_closePopup();
                    }
                }


                xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxSuspendUser.php?program_id=" + program_id+ "&user_id=" + user_id + "&period=" + selectedPeriod, true);
                xmlhttp.send();
            }
            else if (document.getElementsByName("option")[1].checked)
            {
                if (window.XMLHttpRequest)
                {
                    xmlhttp=new XMLHttpRequest();
                }
                else
                {
                    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange=function()
                {
                    if(xmlhttp.readyState==4 && xmlhttp.status==200)
                    {
                        if (xmlhttp.responseText == "success")
                        {
                            alert("Message sent!")
                        }
                        else
                        {
                            alert("Error. Please, try again later");
                        }

                        parent.fn_closePopup();
                    }
                }


                xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxSuspendUser.php?program_id=" + program_id+ "&user_id=" + user_id + "&period=" + 0, true);
                xmlhttp.send();
            }
        }

    </script>
</body>






<?php
//get_footer(); 
?>