<?php
require_once('GTO_include_legacy.php');

// Common utilities
/**
 * @var float[]  List of timestamps or elapsed durations for a given code
 *               checkpoint
 *
 * @see checkpoint()
 * @see logPagePerformance()
 */
global $Checkpoints;
$Checkpoints = NULL;

function urlSelf()
{
   /******************************************************************************************
    *  find the url value  self and urlbase
    ******************************************************************************************/
    $sysHost = $_SERVER['HTTP_HOST']; 
    list($sysPath,$sysPart2) = explode("?",$_SERVER['REQUEST_URI']);
    $sysOption =explode("&",$sysPart2);
    $self = $sysHost.$sysPath;
//  $self = urlSelfBase();
    $cnt = 0; 
    foreach($sysOption as $x){ 
       if($cnt++ == 0){ $div = '?'; }else{ $div = '&amp;'; }
       if(isset($x) && $x != '') $self .= $div.$x;
    }    
    return $self;
}

function urlSelfBase()
{
   /******************************************************************************************
    *  find the urlbase address
    ******************************************************************************************/
    $sysHost = $_SERVER['HTTP_HOST']; 
    list($sysPath,$sysPart2) = explode("?",$_SERVER['REQUEST_URI']);
    $sysOption =explode("&",$sysPart2);
    $self = $sysHost.$sysPath;
    return $self;
}

   /******************************************************************************************
    *  this currently adds ERRORs and WARNINGs to the recipe via this_args[ERROR | WARNING]
    *  so pass $this args in and receive it back 
    *  $this->args = errorHandler('Some funny message  ','ERROR',$this->args);
    *  $this->args = errorHandler('Some funny message  ','WARNING',$this->args);
    ******************************************************************************************/
function errorHandler($msg,$level = NULL,array $args = NULL ){
    simpleLog('errorHandler '.$level);
    //simpleLog('errorHandler '.$this->args[$level]);// $this->args is not available here, not sure how to make it truly global.
    if($level == 'ERROR'){
       $cnt = 0; 
       if(array_key_exists("ERRORS",$args)) $cnt = 1 + count($args['ERRORS']);
       $args['ERRORS'][$cnt] = $msg;
    }    
    if($level == 'WARNING'){
       $cnt = 0; 
       if(array_key_exists("WARNING",$args)) $cnt = 1 + count($args['WARNING']);
       $args['WARNING'][$cnt] = $msg;
    }    
    simpleLog('errorHandler '.$msg);
    if($level == NULL) $level = 'error';
    $cwdWords = preg_split("/\//", getcwd());
    if(count($cwdWords) > 1) $endword = count($cwdWords) - 1; 
    if($cwdWords[$endword] == 'public') {
       $endpt    = $cwdWords[$endword - 1];
       $userPath = $cwdWords[$endword - 2];
       $user     = preg_split("/^www_/", $userPath);
    }    
    $logDir  = "../../log/";
    $script_tz = date_default_timezone_get();
    date_default_timezone_set("America/Chicago");
    $datenow = time();
    $wkday   = date("w",$datenow);// w is day of week 0-Su 1-mo 2-tu ...
    $time    = date("h:i:s",$datenow);
    // make sure the log directory exists.
    if(is_dir($logDir)){
       // delete log files older than 3 days old
       for ($i = 0; $i < 7; $i++){
          $fn = $logDir."errorLog".$i.".log";
          if(file_exists($fn)){
             $fage = filemtime($fn);
             $daysOld = ($datenow - $fage)/86400;
             if($daysOld > 3){ unlink($fn); }
          }
       }
    }else{ // if the log directory doesn't exist, create it. 
       mkdir($logDir);
    }
    // file name will be one of 7 based on the week day of the system clock.
    $fn = $logDir."errorLog".$wkday.".log";
    $fp = fopen($fn,"a+");
    fwrite($fp,"$time $level - $msg\n");
    fclose($fp);
    chmod($fn,0664);
    date_default_timezone_set($script_tz);
    return $args;
}

function simpleLog($msg)
{
    $simpleLogEnabled = true; // set to true if simpleLog to work
    if ($simpleLogEnabled) {
        /*
        *   6/2 am Not feeling well today.
        *          Thought I would take on something easy this morning
        *          and figure out how to set the prefix without editing this all the time
        */
        $prefix = 'u:';
        $cwdWords = preg_split("/\//", getcwd());
        if (count($cwdWords) > 1) $endword = count($cwdWords) - 1;
        if ($cwdWords[$endword] == 'public') {
            $endpt = $cwdWords[$endword - 1];
            $userPath = $cwdWords[$endword - 2];
            $user = preg_split("/^www_/", $userPath);
        }
        if ($user[1] == 'clynch3')
            $prefix = 'C:';
        elseif ($user[1] == 'dev4')
            $prefix = 'T:';
        elseif ($user[1] == 'qa4')
            $prefix = 'Q:';
        elseif (isset($cwdWords[$endpt])) {
            if ($cwdWords[$endpt] == 'tep_api') $prefix = 'd:';
            if ($cwdWords[$endpt] == 'tep4_api') $prefix = 't:';
        }
        $logDir = "../../log/";
        //$logDir  = "../log/";  //  I need to separate the test and dev log files, this is starting to bug me.
        //$script_tz = date_timezone_get();
        //date_timezone_set("America/Chicago");
        $script_tz = date_default_timezone_get();
        date_default_timezone_set("America/Chicago");
        $datenow = time();
        $wkday = date("w", $datenow);// w is day of week 0-Su 1-mo 2-tu ...
        $time = date("h:i:s", $datenow);
        // make sure the log directory exists.
        if (is_dir($logDir)) {
            // delete log files older than 3 days old
            for ($i = 0; $i < 7; $i++) {
                $fn = $logDir . "simpleLog" . $i . ".log";
                if (file_exists($fn)) {
                    $fage = filemtime($fn);
                    $daysOld = ($datenow - $fage) / 86400;
                    if ($daysOld > 3) {
                        unlink($fn);
                    }
                }
            }
        } else { // if the log directory doesn't exist, create it.
            mkdir($logDir);
        }
        // file name will be one of 7 based on the week day of the system clock.
        $fn = $logDir . "simpleLog" . $wkday . ".log";
        $fp = fopen($fn, "a+");
        fwrite($fp, "$prefix$time - $msg\n");
        fclose($fp);
        chmod($fn, 0664);
        date_default_timezone_set($script_tz);
        //date_timezone_set($script_tz);
    }
}

/*
**  Ok this looked good in my head, but the keys don't reduce by one when this extends out
**  So I get things like  23:a:08:42:07 - 5 + -   BN-D1QPC1251Q01-DP-OPC-ORC-references-dependencies-0--requiredLayerRev = AZ
**  Where the ORC should have replaced OPC and dependencies should have replaced references
**  Can't get my head around why this is happening so putting this on a shelf for now.
*/
function simpleArray1Log ($aray, $cnt = 0, $keys = NULL)
{
    $simpleLogEnabled = true; // set to true if simpleLog to work
    if ($simpleLogEnabled) {
        $logDir = "../../log/";
        $script_tz = date_default_timezone_get();
        date_default_timezone_set("America/Chicago");
        $datenow = time();
        $wkday = date("w", $datenow);// w is day of week 0-Su 1-mo 2-tu ...
        $time = date("h:i:s", $datenow);
        $fn = $logDir . "simpleLog" . $wkday . ".log";
        if ($msg == NULL) $msg = "a:$time - " . count($aray) . " + ";
        if ($keys == NULL) $keys = "\t";
        if (is_array($aray)) {
            foreach ($aray as $k1 => $v1) {
                if (is_array($v1)) {
                    //if(isset()
                    $keys .= $k1 . '-';
                    $cnt++;
                    simpleArray1Log($v1, $cnt, $keys);
                    //}elseif(!isset($v1)){
                    //     $keys = preg_replace("/(\-)\w+$/","$1",$keys);
                    //     $cnt = $cnt + 100;
                    //     if($cnt > 1000) return;
                    //     simpleArray1Log ($v1, $cnt, $keys);
                } else {
                    $fp = fopen($fn, "a+");
                    fwrite($fp, $cnt . ":" . $msg . "-" . $keys . "-" . $k1 . " = " . $v1 . "\n");
                    fclose($fp);
                }
                $cnt++;
            }
        }
        date_default_timezone_set($script_tz);
    }
}

function simpleArrayLog ($aray)
{
    $simpleLogEnabled = true; // set to true if simpleLog to work
    if ($simpleLogEnabled) {

        $logDir = "../../log/";
        // $script_tz = date_timezone_get();
        // date_timezone_set("America/Chicago");
        $script_tz = date_default_timezone_get();
        date_default_timezone_set("America/Chicago");
        $datenow = time();
        $wkday = date("w", $datenow);// w is day of week 0-Su 1-mo 2-tu ...
        $time = date("h:i:s", $datenow);
        $fn = $logDir . "simpleLog" . $wkday . ".log";
        $msg = "a:$time -\n" . count($aray) . " -";
        if (is_array($aray) ) {
            foreach ($aray as $k1 => $v1) {
                if (is_array($v1) ) {
                    foreach ($v1 as $k2 => $v2) {
                        if (is_array($v2) ) {
                            foreach ($v2 as $k3 => $v3) {
                                if (is_array($v3) ) {
                                    foreach ($v3 as $k4 => $v4) {
                                        if (is_array($v4)) {
                                            foreach ($v4 as $k5 => $v5) {
                                                if (is_array($v5)) {
                                                    foreach ($v5 as $k6 => $v6) {
                                                        if (is_array($v6)) {
                                                            foreach ($v6 as $k7 => $v7) {
                                                                if (is_array($v7)) {
                                                                    foreach ($v7 as $k8 => $v8) {
                                                                        $msg .= "\n7+:    ['" . $k1 . "']['" . $k2 . "']['" . $k3 . "']['" .     $k4 . "']['" . $k5 . "']['" . $k6 . "']['" . $k7 . "']['" . $k8 . "'] '" . $v8 . "'";
                                                                    }
                                                                } else {
                                                                    $msg .= "\n7+:    ['" . $k1 . "']['" . $k2 . "']['" . $k3 . "']['" . $k4 .   "']['" . $k5 . "']['" . $k6 . "']['" . $k7 . "'] '" . $v7 . "'";
                                                                }
                                                            }
                                                        } else {
                                                            $msg .= "\n6+:    ['" . $k1 . "']['" . $k2 . "']['" . $k3 . "']['" . $k4 . "']['" .  $k5 . "']['" . $k6 . "'] '" . $v6 . "'";
                                                        }
                                                    }
                                                } else {
                                                    $msg .= "\n5+:    ['" . $k1 . "']['" . $k2 . "']['" . $k3 . "']['" . $k4 . "']['" . $k5 .    "'] '" . $v5 . "'";
                                                }
                                            }
                                        } else {
                                            $msg .= "\n4+:    ['" . $k1 . "']['" . $k2 . "']['" . $k3 . "']['" . $k4 . "'] '" . $v4 . "'";
                                        }
                                    }
                                } else {
                                    $msg .= "\n3+:    ['" . $k1 . "']['" . $k2 . "']['" . $k3 . "'] '" . $v3 . "'";
                                }
                            }
                        } else {
                            $msg .= "\n2+:    ['" . $k1 . "']['" . $k2 . "'] '" . $v2 . "'";
                        }
                    }
                 } else {
            $msg .= "          empty array";
        }
        $fp = fopen($fn, "a+");
        fwrite($fp, "$msg\n");
        fclose($fp);
        date_default_timezone_set($script_tz);
        //date_timezone_set($script_tz);
    }
}


/*********************************************************************************************************************
**      array or json to xml converter
**      modified from examples found on stack overflow -=C
*********************************************************************************************************************/
//function arrayToXml($array, SimpleXMLElement $xml){
function arrayToXml($array, &$xml){
    simpleLog('function arrayToXml');
    foreach ($array as $key => $val) {
       $jsonRecipe .= $val;
       $val = str_replace(array("\n","\r","\""," "),"",$val);
       $val = str_replace("http:","http;",$val);
       $val = str_replace("https:","https;",$val);
       $vals = preg_split('/\:/',$val);
       $vals[1] = str_replace("https;","https:",$vals[1]);
       $vals[1] = str_replace("http;","http:",$vals[1]);
       $key1 = $vals[0]; $val1 = $vals[1];
       if(is_array($val1)){
           if(is_int($key1)){
               $key1 = "e";
           }
           $label = $xml->addChild($key1);
           arrayToXml($val1, $label);
       }
       else {
           $xml->addChild($key1, $val1);
       }
    }
}

**
 * Dump the value of a variable  to the log file.  The filename and line number
 * of the calling routine are prefixed to the start of the log message.
 *
 * @param string  $legend    Text to append to the log file
 *
 * @param mixed   $data      Text to append to the log file
 *
 * @param string  $log       Optional filename to log the message to.  Defaults
 *                           to LOG_FILE if defined, otherwise null indicating
 *                           the Apache error log.
 *
 * @param int     $backstep  Optional number of entries to pop off the call stack
 *                           when determining the location to report as the
 *                           caller.  Defaults to 1.
 */
function logData ( $legend, $data, $log = null, $backstep = 1 )
{
  //$script_tz = date_default_timezone_get();
  //date_default_timezone_set("America/Chicago");
  //$datenow = time();
  //$wkday   = date("w",$datenow);// w is day of week 0-Su 1-mo 2-tu ...
  //$time    = date("h:i:s",$datenow);
  //$fn = $logDir."simpleLog".$wkday.".log";
  ob_start();
  var_dump( $data );
  $msg = ob_get_contents();
  ob_end_clean();
  logMessage( $legend." => ".$msg, $log, $backstep+1 );
  chmod($log,0664);
  //chmod($fn,0664);
  //date_default_timezone_set($script_tz);
}

/**
 * Dump a message to the log file.  The filename and line number of the calling
 * routine are prefixed to the start of the log message.
 *
 * @param string $msg Text to append to the log file
 *
 * @param string $log Optional filename to log the message to.  Defaults to
 *                    LOG_FILE if defined, otherwise null indicating the Apache
 *                    error log.
 *
 * @param int $backstep Optional number of entries to pop off the call stack
 *                         when determining the location to report as the caller.
 *                         Defaults to 1.
 */
function logMessage($msg, $log = null, $backstep = 1)
{
  if (empty( $log ) && defined( 'LOG_FILE' )) $log = LOG_FILE;
  $log_type = (empty( $log ) ? 0 : 3);
  $eol = ($log_type ? "\n" : '');
  $call_stack = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, $backstep );
  $caller = array_pop( $call_stack );
  error_log( "{$caller['file']}[{$caller['line']}]: {$msg}{$eol}",
             $log_type, $log );
  chmod($log,0664);
}
}

