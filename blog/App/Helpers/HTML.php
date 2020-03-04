<?php

namespace Simpleframework\Helpers;
use Simpleframework\Middleware\Csrf;
use Simpleframework\Middleware\Sanitize;
use Simpleframework\Helpers\Util;

class HTML
{

   public static function blogpost($data,$appearance)
   {
       $str ="<div class='card'>" .
             "<div class='card-header br {$appearance[6]->Fontcolor} {$appearance[6]->backgroundcolor} '>".
                 "{$data->Heading}" .
             "</div>".
            "<div class='card-body bt'>" .
             "<p class='card-text bt {$appearance[5]->Fontcolor} {$appearance[5]->backgroundcolor}'>" .
             "{$data->txtvalue}" .
             "</p>" .
             "</div>" .
             "<div class='card-footer'>".
             "<button type='button' value='{$data->ID}' class='btn btn-link comments' data-toggle='modal' data-target='#mcc'>Kommentera</button>";
                if (Util::checkBlogID())
                {
                    $str .= "<button type='button' value='{$data->ID}' class='btn btn-link delpost'>Radera</button>";
                }

                if ($data->Num > 0)
                {
                    $str .= "<button type='button' value='{$data->ID}' class='btn btn-link showcomments'>Visa ({$data->Num})</button>";
                }

        $str .= "</div>".
                "</div>";


        return $str;
   }


   public static function comment($data)
   {
    $str ="<div class='card'>" .
          "<div class='card-header'>".
          "Kommentar av $data->Username".
          "</div>" .
          "<div class='card-body'>" .
          "{$data->Comment}" .
          "</div>".
          "</div>";
    return $str;
  }

  public static function btLinkForBlog($blogname)
  {
      $url = URLROOT. 'blogs/'.$blogname;
      return "<a class='btn btn-link' href='{$url}'>{$blogname}</a>";
  }

}



