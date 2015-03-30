<?php
/*
Template Name: report_frame
*/
if(!isset($_SESSION)) session_start();

if($_SESSION['user_id'] == "")
{
	echo "Please, login!";
	exit();
}

$program_id = $_GET['program_id'];
$reporter = $_GET['reporter'];
$reported = $_GET['reported'];
$videoURL = $_GET['videoURL'];

//$program_id = $this->input->get("program_id");
//$reporter = $this->input->get("reporter");
//$reported = $this->input->get("reported");
//$videoURL = $this->input->get("videoURL");

get_header();

?>


<head>


</head>

<body style="margin: 0px">
    <div id="voice-test-page" class="voice-test-page" style="min-width: 250px; max-width: 250px; margin-left: auto; margin-right: auto; font-family: 'Myriad Pro';">
        <div align="left" id="rootDiv" class="voice-test-form">
            <div style="background-color:#fff; width: 100%; height:355px; position: relative; padding-top: 1px; padding-left: 20px;">
                <p style="line-height: 35px;"><b style="color: #af121e; padding-left: 20%">Choose a reason: </b><Br>
                    <input type="radio" name="reason" value="sexual"> Sexual content<Br>
                    <input type="radio" name="reason" value="violent"> Violent or repulsive content<Br>
                    <input type="radio" name="reason" value="hateful"> Hateful or abusive content<Br>
                    <input type="radio" name="reason" value="dangerous"> Harmful dangerous acts<Br>
                    <input type="radio" name="reason" value="child"> Child abuse<Br>
                    <input type="radio" name="reason" value="spam"> Spam<Br>
                    <input type="radio" name="reason" value="rights"> Infringes my rights<Br>
                </p>

                <div style="width: 60%; float: left";>
                    <input type="button" style="width: 90px; height: 30px; font-size: 100%; font-family: 'Myriad Pro'; background-color: white; border-color: #af121e; border-width: 2px; color: #af121e; float: right" onclick="sendReport()" value="Report"/>
                </div>
            </div>
        </div>
    </div>
    <br>

    <script type="text/javascript">

        document.getElementById("main-header").style.display = "none";
        document.getElementById("headerShadowDiv").style.display = "none";

        var program_id = "<?php echo $program_id ?>";
        var reporter = "<?php echo $reporter ?>";
        var reported = "<?php echo $reported ?>";
        var videoURL = "<?php echo $videoURL ?>";



        </script>
    <script type="text/javascript">

        function sendReport()
        {
            var reason = document.getElementsByName("reason");
            var selectedReason = "";

            for(var i = 0; i < reason.length; i++)
            {
                if(reason[i].checked == true)
                {
                    selectedReason = reason[i].value;
                    break;
                }
            }

            if (selectedReason == "") return;

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
                    if (xmlhttp.responseText == "reported")
                    {
                        alert("Reported!")
                    }
                    else if (xmlhttp.responseText == "exists")
                    {
                        alert("You have already reported this video")
                    }
                    else
                    {
                        alert("Error. Please, try again later");
                    }

                    parent.fn_closePopup();
                }
            }


            xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxReportVideo.php?program_id=" + program_id+ "&reporter=" + reporter + "&reported=" + reported + "&videoURL=" + videoURL + "&reason=" + selectedReason, true);
            xmlhttp.send();
        }

    </script>
</body>






<?php
//get_footer(); 
?>