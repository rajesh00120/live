<?php 

namespace App\Helpers;

class Helper{
    public static function SayHello()
    {
        return "SayHello";
    }
}


// if(!function_exists('time_elapsed')){
//     function time_elapsed($datetime, $full = false) {
//       $now = new DateTime;
//       $ago = new DateTime($datetime);
//       $diff = $now->diff($ago);

//       $diff->w = floor($diff->d / 7);
//       $diff->d -= $diff->w * 7;

//       $string = array(
//           'y' => 'year',
//           'm' => 'month',
//           'w' => 'week',
//           'd' => 'day',
//           'h' => 'hour',
//           'i' => 'minute',
//           's' => 'second',
//       );
//       foreach ($string as $k => &$v) {
//           if ($diff->$k) {
//               $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
//           } else {
//               unset($string[$k]);
//           }
//       }

//       if (!$full) $string = array_slice($string, 0, 1);
//       return $string ? implode(', ', $string) . ' ago' : 'just now';
//   }
//  }


// if(!function_exists('checkFunction')){
//   function checkFunction() {
//     echo 'Hi Shiv';die;
//  }
// }