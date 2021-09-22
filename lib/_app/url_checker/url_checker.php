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

use Exception;


/**
 * Used to check the status of URLs
 * @package new_dk\_app\url_checker
 */
class url_checker extends url_checker_abstract_base
{
  
  public function main()
  {
    $this->database();

    $this->tpl['side_box'] = '<div class="dk_pad"><a href="/dashboard" class="dk_button">Dashboard</a></div>';

    if ('POST' === $_SERVER['REQUEST_METHOD']) {
      $this->handle_post();
    } else {
      $this->status_sorter_list();
    }

    // Assume GET
    $this->show_urls_form();
  }

  private function handle_post()
  {
    if (empty($_POST['urls_to_check'])) {
      respond(400, '<p>This is a <b>Bad Request</b>.</p>');
    }

    $url_array = preg_split("/\r\n|\n|\r/", $_POST['urls_to_check']);

    for ($i = 0; $i < count($url_array); $i++) {
      $item = &$url_array["$i"];
      $item = strtolower($item);
      if (empty(trim($item))) {
        continue;
      }
      if ((stripos($item, 'https://') !== 0) && (stripos($item, 'http://') !== 0)) {
        // If protocol is not included, test both http and https
        $url_array[] = 'http://' . $item;
        $item = $url_array["$i"] = 'https://' . $item;
      }
      if (!filter_var($item, FILTER_VALIDATE_URL)) {
        continue;
      }
      // Update URLs in database
      // First check if the URL needs to be updated
      $md5 = md5($item);
      $skip_if_found = $this->db->query("SELECT * FROM urls WHERE md5='$md5' AND last_checked >= CURDATE() - INTERVAL 2 DAY");

      // To avoid spamming requests, we only allow sending 1 HTTP request for each URL in the given interval above
      if (false !== $skip_if_found) {
        continue;
      }

      $http_error = false;
      try {
        $response = $this->http->get($item);
        $extra = '';
      } catch (\Throwable $th) {
        $http_error = true;
        $extra = $th->getMessage();
      }

      if (true === $http_error) {
        // If there was some kind of HTTP error (DNS, unable to connect. Etc.)
        $this->db->prepared_query('INSERT INTO urls (url, status, soft_404, md5, last_checked, extra) VALUES (?, ?, ?, ?, NOW(), ?)', [$item, 0, 0, $md5, $extra]);
      } else {
        // If the Server responded, store some information about the response in the database table
        $headers = $response->headers();
        if (isset($headers['location'])) {
          $extra = $headers['location'];
        }
        $this->db->prepared_query('INSERT INTO urls (url, status, soft_404, md5, last_checked, extra) VALUES (?, ?, ?, ?, NOW(), ?) ON DUPLICATE KEY UPDATE status=VALUES(status), last_checked=VALUES(last_checked)', [$item, $response->status_code(), 0, $md5, $extra]);
      }
    }
    $tpl = &$this->tpl;
    $tpl['content'] = '<div class="dk_cbox dk_pad"><p>URLs has been processed. You can now view them in the Dashboard.</p><a href="\dashboard" class="dk_button">Dashboard</a></div>';
    $template = require_once 'tpl/d/default.php';

    respond(200, $template);
  }

  private function show_urls_form()
  {
    $tpl = &$this->tpl;
    $tpl['side_box'] = '<div class="dk_cbox">' . $this->tpl['side_box'] . '</div>';
    $tpl['content'] .= '<p>Checks if a given URL (E.g.: <i class="dk_ie">wp-content/uploads</i>) responds with 200. <b>One URL per line.</b></p>';
    $tpl['content'] .= '<p class="dk_indent">If protocol (E.g. <i class="dk_ie">https://</i>) is not included, both <i class="dk_ie">http</i> and <i class="dk_ie">https</i> will be checked.</p>';
    $tpl['content'] .= '<form action="/" class="dk_form" method="post">';
    $tpl['content'] .= '<label for="urls_to_check">';
    $tpl['content'] .= '<textarea name="urls_to_check" id="urls_to_check" style="min-width:98%;max-width:98%;min-height:400px;"></textarea>';
    $tpl['content'] .= '<input type="submit" class="dk_button">';
    $tpl['content'] .= '</form>';

    $template = require_once 'tpl/d/default.php';

    respond(200, $template);
  }
  
}
