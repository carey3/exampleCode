<!DOCTYPE html>
<html>
<body>

<h2> Hello World! </h2>

    <?php
        echo "Hey y'all, I'mmmm baaaccck!<br /><br />";

        // this function will print all the system variables
        function systemstuff () {
            echo "<b>Here's all the system stuff you didn't ask for: </b> <br />";
            foreach ($GLOBALS as $gkey => $gval ) {
               print "$gkey ";
               //print "$gval ";
               print "<br />";
               if($gkey != "GLOBALS"){
                   foreach ($GLOBALS[$gkey] as $key1 => $val1 ){
                      print ">   $key1 ";
                      print "=   $val1 ";
                      print "<br />";
                   }
               }
            }
        }
        function gettime() {
                $date_array = getdate();
                $formated_date  = "Current time and date: <b><font size=\"+1\">";
                if($date_array['mon'] < 10)
                        $formated_date .= "0";
                $formated_date .= $date_array['mon'] . "/";
                if($date_array['mday'] < 10)
                        $formated_date .= "0";
                $formated_date .= $date_array['mday'] . "/";
                $formated_date .= $date_array['year'] . " ";
                // correct system time    subtract 6 hours
                $hr =  $date_array['hours']- 6;
                if($hr < 0)
                        $hr = $hr + 24;
                if($hr < 10)
                        $formated_date .= "0";
                $formated_date .= $hr . ":";
                if($date_array['hours'] < 10)
                        $formated_date .= "0";
                $formated_date .= $date_array['hours'] . ":";
                if($date_array['minutes'] < 10)
                        $formated_date .= "0";
                $formated_date .= $date_array['minutes'] . ":";
                if($date_array['seconds'] < 10)
                        $formated_date .= "0";
                $formated_date .= $date_array['seconds'] . "</b></font><br />";
                print $formated_date;
                $days_till_Christmas = "There are <font size=\"+2\">";
                if(358 - $date_array['yday'] >= 0 && 358 - $date_array['yday'] <= 100){
                    $days_till_Christmas .= 358 - $date_array['yday'];
                    $days_till_Christmas .= "</font> days until Christmas " .$date_array['year'] .". <br /><br />";
                }else{
                    $days_till_Christmas = "";
                }
                print $days_till_Christmas;
                return $date_array;
        }
        function SystemInfo() {
            if (preg_match('/PHP\/\d*\.*\d*\.*\d*/i', $_SERVER['SERVER_SOFTWARE'],$match)) {
                $sp = preg_split('/\//', $match[0]);
                print "<pre>       $sp[0]    version $sp[1]  </pre>";
            }
            if (preg_match('/apache\/\d*\.*\d*\.*\d*/i', $_SERVER['SERVER_SOFTWARE'],$match)) {
                $sp = preg_split('/\//', $match[0]);
                print "<pre>       $sp[0] version $sp[1]  </pre>";
            }
            print "<pre>       This script is: " . $_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME'] . "  </pre>";
            if (preg_match('/windows [a-zA-Z0-9. ]*\;/i',$_SERVER['HTTP_USER_AGENT'],$matches)){
                $platform = $matches[0];
            }
            if (preg_match('/linux\s*[a-zA-Z0-9. ]*/i',$_SERVER['HTTP_USER_AGENT'],$matches)){
                $platform = $matches[0];
            }
            print_r("<pre>       OS: " . $platform . "</pre>");
            if (isset($_SERVER['HTTP_X_FORWARDED_SERVER'])){
                print "<pre>       Server: " . $_SERVER['HTTP_X_FORWARDED_SERVER'] . "</pre>";
            }
            if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])){
                print "<pre>       Host: " . $_SERVER['HTTP_X_FORWARDED_HOST'] . "</pre>";
            }
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                print "<pre>       From: " . $_SERVER['HTTP_X_FORWARDED_FOR'] . "</pre><br />";
            }
        }
        gettime();
        systeminfo();
        //systemstuff();
    ?>
</body>
</html>
