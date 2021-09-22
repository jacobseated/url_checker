<?php

/**
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-2021_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 * 
 *  @author JacobSeated
 */

namespace new_dk\_app\url_checker;

require_once BASE_PATH . 'lib/_app/url_checker/url_checker_abstract_base.php';
use \new_dk\_app\url_checker\url_checker_abstract_base;

/**
 * Shows stored URLs from the database
 * @package new_dk\_app\url_checker
 */
class dashboard extends url_checker_abstract_base
{

  public function main()
  {
    $this->database();

    $this->tpl['title'] = 'Dashboard | ' . $this->tpl['title'];
    $this->tpl['h1'] = 'Dashboard';
    $this->tpl['side_box'] = '<div class="dk_pad"><a href="/" class="dk_button">URL Checker</a></div><div class="dk_mar dk_pad dk_border"><p>Database:</p> <a href="/dashboard?do=truncate" class="dk_button">Truncate</a></div>';

    // If Truncate was requested
    if ((isset($_GET['do'])) && ('truncate' === $_GET['do'])) {
      $tpl = &$this->tpl;

      try {
        $this->db->query('truncate urls');
      } catch (\Throwable $th) {
        //throw $th;
      }

      $this->tpl['content'] = '<p>The URLs table was truncated.</p>';
      $this->tpl['side_box'] = '<div class="dk_cbox">' . $this->tpl['side_box'] . '</div>';

      $template = require_once 'tpl/d/default.php';
      respond(200, $template);
    }

    // Attempt to build status sorter list
    if(!$this->status_sorter_list()) {
      $tpl = &$this->tpl;

      $tpl['content'] .= '<p>There are currently no URLs in the database.</p>';
      $tpl['side_box'] = '<div class="dk_cbox">' . $tpl['side_box'] . '</div>';

      $template = require_once 'tpl/d/default.php';
      respond(200, $template);
    }

    $this->dashboard();
  }

  private function dashboard() {
    $tpl = &$this->tpl;
    $tpl['side_box'] = '<div class="dk_cbox">' . $this->tpl['side_box'] . '</div>';

    if (isset($_GET['status'])) {
      if (!preg_match('/^[0-9]{1,3}$/', $_GET['status'])) {
        respond(400, '<p>Invalid status code requested.</p>');
      }
      $result = $this->db->prepared_query('SELECT * FROM urls WHERE status =? LIMIT 1000', [$_GET['status']]);
    } else {
      $result = $this->db->query('SELECT * FROM urls LIMIT 1000');
    }
  
  $tpl['content'] .= '<style>
  .http_url {font-weight:bold;background:#f7f7f7;margin: 0 0 0.5rem;}
  .http_response {justify-content:left;font-family:monospace;}
  .http_status {margin:0 1rem 0 0;padding:0.3rem;color:#3b3b3b;font-weight:bold;font-size:1.8rem;font-family:"Times New Roman", Georgia, serif;border-radius:50%;}
  .http_200 {color:#58c500}
  .http_other {color:#202020}
  .http_extra {}
  .http_failed {color:#c50000;background:#fff6f6;}
  .http_failed_bg {background:#fff6f6;}
  .http_failed .dk_cbox {border-color:#c50000;}
  .http_url, .http_extra {
    border:1px solid rgb(225,225,225);border-radius:0.2rem;padding:0.5rem;
    outline:none;
    filter:saturate(52%);
    transition: opacity 0.5s, border 0.5s, filter 0.5s, transform 0.7s;
    font-family:"Ubuntu Mono", monospace;
  }
  .http_url:focus, .http_extra:focus {
    filter:saturate(100%);
    border:1px solid rgb(190,190,190);
    transform:scale(1.1);
  }
  </style>';

    while ($row = $result->fetch_assoc()) {
      $failed_class = '';
      if (0 == $row['status']) {
        $st = '<div class="dk_border http_failed http_extra">'. $row['extra'] .'</div>';
        $failed_class = 'http_failed_bg';
      } else if (200 == $row['status']) {
        $st = '<div class="http_status http_200 dk_bg dk_border" style="transform:rotate('.rand(-5,8).'deg)">'. $row['status'] .'</div>';
      } else {
        $st = '<div class="http_status http_other dk_bg dk_border" style="transform:rotate('.rand(-5,8).'deg)">'. $row['status'] .'</div><div class="dk_ie http_extra" spellcheck="false" contenteditable>'.$row['extra'].'</div>';
      }
      $tpl['content'] .= '<div class="dk_cbox dk_bg '.$failed_class.'"><div class="http_url '.$failed_class.'" spellcheck="false" contenteditable>'.$row['url'].'</div><div class="dk_flex http_response">'. $st .'</div></div>';
    }

    $template = require_once 'tpl/d/default.php';
    respond(200, $template);
  }
}
