<?php

namespace App\Http\Controllers\Admin\Helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobStatus;
use DateTime;
use Carbon\Carbon;

class HelperController extends Controller
{
  public function getTodayDate(){
    return date("Y/m/d");
  }
  // get month name for integer value
  public function getMonthName($monthId)
  {
    
    $dateObj   = DateTime::createFromFormat('!m', $monthId);
    return $monthName = $dateObj->format('F');
  }

  public static function getMonthNameByStaticMethod($monthId)
  {
    $dateObj   = DateTime::createFromFormat('!m', $monthId);
    return  $dateObj->format('F');
  }

  public function getYear()
  {
    return date('Y');
  }

  public function getEmployeeStatus()
  {
    return $status = JobStatus::all();
  }

  public function getNextFirdayDate($day, $month, $year)
  {

    $date = $year . '-' . $month . '-' . $day;
    $date    =  new DateTime($date);
    $day = $date->format('l');  //pass
    return $day;
  }
  public function getNumberOfDaysInMonthAndYear($month, $year)
  {
    // dd($month,$year);
    $noOfDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    if ($noOfDaysInMonth == null) {
      return 0;
    } else {
      return $noOfDaysInMonth;
    }
  }

  public function countTotalHolidayInThisMonth($month,$year){

    $days = $this->getNumberOfDaysInMonthAndYear($month,$year);
    $holidayArray = array_fill(1, 31, 0);
    $totalHolidays = 0;
    for ($i = 1; $i <= $days; $i++) {
      $date = $year . '-' . $month . '-' . $i;
      $date    =  new DateTime($date);
      $day = $date->format('l');
      if ($day == "Friday") {
        $totalHolidays++;
        $holidayArray[$i] = 1;
      }
    }
    return [$totalHolidays,$holidayArray];

  }
  public function checkThisDayIsFriday($date){

      $date    =  new DateTime($date);
      $day = $date->format('l');
      if ($day == "Friday") {
          return true;
      }
      return  false;

  }



  public function getAllDaysNameInMonth($month,$year){

    $day_name_in_month = array();
    $total_day = $this->getNumberOfDaysInMonthAndYear($month,$year);
    for($c = 1; $c <= $total_day; $c ++){

      $date = $year."-".$month."-".$c;
      $day_name_in_month[$c] =  substr((new DateTime($date))->format('l'),0,2);
    }
    return $day_name_in_month;
  }

  public function getMonthFromDateValue($dateValue)
  {
    if ($dateValue == null) {
      return 0;
    }
    $time = strtotime($dateValue);
    return  $month = (int) date("m", $time);
  }
  public function getCurrentMonthIntValue()
  {

    $time = strtotime(date("Y/m/d"));
    return  $month = (int) date("m", $time);
  }

  public function getYearFromDateValue($dateValue)
  {
    if ($dateValue == null) {
      return 0;
    }
    $time = strtotime($dateValue);
    return $year = (int) date("Y", $time);
  }
  public function getDayFromDateValue($dateValue)
  {
    if ($dateValue == null) {
      return 0;
    }
    $time = strtotime($dateValue);
    return  (int) date("d", $time);
  }

  public function getTodayDayFromCurrentMonth()
  {
    $time = strtotime(date("Y/m/d"));
    return  (int) date("d", $time);
  }

  public function getDayMonthAndYearFromDateValue($dateValue)
  {
    if ($dateValue == null || $dateValue == "") {
      return [0,0,0];
    }
     $time = strtotime($dateValue);
     $day =  (int) date("d", $time);
     $month =  (int) date("m", $time);
     $year =  (int) date("Y", $time);
     return [$day,$month,$year];
  }

  function getLastDateFromDateValue($a_date){
       return date("Y-m-t", strtotime($a_date));
  }

  function getMonthsInRangeOfDate($startDate, $endDate)
  {
    $months = array();

    while (strtotime($startDate) <= strtotime($endDate)) {
      $months[] = array(
        'year' => (int) date('Y', strtotime($startDate)),
        'month' => (int) date('m', strtotime($startDate)),
      );

      // Set date to 1 so that new month is returned as the month changes.
      $startDate = date('01 M Y', strtotime($startDate . '+ 1 month'));
    }

    return $months;
  }


  function getListOfMonthInRangeOfTwoDate($startDate, $endDate)
  {
    $months = array();
    $counter =0;

    while (strtotime($startDate) <= strtotime($endDate)) {

        $months[$counter++] =  (int) date('m', strtotime($startDate));
      // Set date to 1 so that new month is returned as the month changes.
      $startDate = date('01 M Y', strtotime($startDate . '+ 1 month'));
    }

    return $months;
  }


  // File Extension Check
public function checkUploadedFileFormatAndUploadFileSize($extension, $fileSize)
{
        $valid_extension = array("csv", "xlsx"); //Only want csv and excel files
        $maxFileSize = 5242888; // Uploaded file size limit is 5mb
        if (in_array(strtolower($extension), $valid_extension)) {
            if ($fileSize <= $maxFileSize) {
                return true;
            } else {
                 return false;
            }
        } else {
           return false;
        }
}


  public function numberToWord($num = '')
  {
      $num    = ( string ) ( ( int ) $num );

      if( ( int ) ( $num ) && ctype_digit( $num ) )
      {
          $words  = array( );

          $num    = str_replace( array( ',' , ' ' ) , '' , trim( $num ) );

          $list1  = array('','one','two','three','four','five','six','seven',
              'eight','nine','ten','eleven','twelve','thirteen','fourteen',
              'fifteen','sixteen','seventeen','eighteen','nineteen');

          $list2  = array('','ten','twenty','thirty','forty','fifty','sixty',
              'seventy','eighty','ninety','hundred');

          $list3  = array('','thousand','million','billion','trillion',
              'quadrillion','quintillion','sextillion','septillion',
              'octillion','nonillion','decillion','undecillion',
              'duodecillion','tredecillion','quattuordecillion',
              'quindecillion','sexdecillion','septendecillion',
              'octodecillion','novemdecillion','vigintillion');

          $num_length = strlen( $num );
          $levels = ( int ) ( ( $num_length + 2 ) / 3 );
          $max_length = $levels * 3;
          $num    = substr( '00'.$num , -$max_length );
          $num_levels = str_split( $num , 3 );
          foreach( $num_levels as $num_part )
          {
              $levels--;
              $hundreds   = ( int ) ( $num_part / 100 );
              $hundreds   = ( $hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ( $hundreds == 1 ? '' : 's' ) . ' ' : '' );
              $tens       = ( int ) ( $num_part % 100 );
              $singles    = '';

              if( $tens < 20 ) { $tens = ( $tens ? ' ' . $list1[$tens] . ' ' : '' ); } else { $tens = ( int ) ( $tens / 10 ); $tens = ' ' . $list2[$tens] . ' '; $singles = ( int ) ( $num_part % 10 ); $singles = ' ' . $list1[$singles] . ' '; } $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_part ) ) ? ' ' . $list3[$levels] . ' ' : '' ); } $commas = count( $words ); if( $commas > 1 )
          {
              $commas = $commas - 1;
          }

          $words  = implode( ', ' , $words );


          $words  = trim( str_replace( ' ,' , ',' , ucwords( $words ) )  , ', ' );
          if( $commas )
          {
              $words  = str_replace( ',' , ' and' , $words );
          }

          return $words;
      }
      else if( ! ( ( int ) $num ) )
      {
          return 'Zero';
      }
      return '';
  }


  public function convertNumber($number)
  {
      list($integer, $fraction) = explode(".", (string) $number);

      $output = "";

      if ($integer[0] == "-")
      {
          $output = "negative ";
          $integer    = ltrim($integer, "-");
      }
      else if ($integer[0] == "+")
      {
          $output = "positive ";
          $integer    = ltrim($integer, "+");
      }

      if ($integer[0] == "0")
      {
          $output .= "zero";
      }
      else
      {
          $integer = str_pad($integer, 36, "0", STR_PAD_LEFT);
          $group   = rtrim(chunk_split($integer, 3, " "), " ");
          $groups  = explode(" ", $group);

          $groups2 = array();
          foreach ($groups as $g)
          {
              $groups2[] = $this->convertThreeDigit($g[0], $g[1], $g[2]);
          }

          for ($z = 0; $z < count($groups2); $z++)
          {
              if ($groups2[$z] != "")
              {
                  $output .= $groups2[$z] . $this->convertGroup(11 - $z) . (
                          $z < 11
                          && !array_search('', array_slice($groups2, $z + 1, -1))
                          && $groups2[11] != ''
                          && $groups[11][0] == '0'
                              ? " and "
                              : ", "
                      );
              }
          }

          $output = rtrim($output, ", ");
      }

      if ($fraction > 0)
      {
          $output .= " point";
          for ($i = 0; $i < strlen($fraction); $i++)
          {
              $output .= " " . $this->convertDigit($fraction[$i]);
          }
      }

      return $output;
  }

  public function convertGroup($index)
  {
      switch ($index)
      {
          case 11:
              return " decillion";
          case 10:
              return " nonillion";
          case 9:
              return " octillion";
          case 8:
              return " septillion";
          case 7:
              return " sextillion";
          case 6:
              return " quintrillion";
          case 5:
              return " quadrillion";
          case 4:
              return " trillion";
          case 3:
              return " billion";
          case 2:
              return " million";
          case 1:
              return " thousand";
          case 0:
              return "";
      }
  }

  public function convertThreeDigit($digit1, $digit2, $digit3)
  {
      $buffer = "";

      if ($digit1 == "0" && $digit2 == "0" && $digit3 == "0")
      {
          return "";
      }

      if ($digit1 != "0")
      {
          $buffer .= $this->convertDigit($digit1) . " hundred";
          if ($digit2 != "0" || $digit3 != "0")
          {
              $buffer .= " and ";
          }
      }

      if ($digit2 != "0")
      {
          $buffer .= $this->convertTwoDigit($digit2, $digit3);
      }
      else if ($digit3 != "0")
      {
          $buffer .= $this->convertDigit($digit3);
      }

      return $buffer;
  }

  public function convertTwoDigit($digit1, $digit2)
  {
      if ($digit2 == "0")
      {
          switch ($digit1)
          {
              case "1":
                  return "ten";
              case "2":
                  return "twenty";
              case "3":
                  return "thirty";
              case "4":
                  return "forty";
              case "5":
                  return "fifty";
              case "6":
                  return "sixty";
              case "7":
                  return "seventy";
              case "8":
                  return "eighty";
              case "9":
                  return "ninety";
          }
      } else if ($digit1 == "1")
      {
          switch ($digit2)
          {
              case "1":
                  return "eleven";
              case "2":
                  return "twelve";
              case "3":
                  return "thirteen";
              case "4":
                  return "fourteen";
              case "5":
                  return "fifteen";
              case "6":
                  return "sixteen";
              case "7":
                  return "seventeen";
              case "8":
                  return "eighteen";
              case "9":
                  return "nineteen";
          }
      } else
      {
          $temp = $this->convertDigit($digit2);
          switch ($digit1)
          {
              case "2":
                  return "twenty-$temp";
              case "3":
                  return "thirty-$temp";
              case "4":
                  return "forty-$temp";
              case "5":
                  return "fifty-$temp";
              case "6":
                  return "sixty-$temp";
              case "7":
                  return "seventy-$temp";
              case "8":
                  return "eighty-$temp";
              case "9":
                  return "ninety-$temp";
          }
      }
  }

  public function convertDigit($digit)
  {
      switch ($digit)
      {
          case "0":
              return "zero";
          case "1":
              return "one";
          case "2":
              return "two";
          case "3":
              return "three";
          case "4":
              return "four";
          case "5":
              return "five";
          case "6":
              return "six";
          case "7":
              return "seven";
          case "8":
              return "eight";
          case "9":
              return "nine";
      }
  }

//  $num = 500254.89;
//  $test = convertNumber($num);

//  echo $test;



}
