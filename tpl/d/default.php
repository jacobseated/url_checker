<?php
/**
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-2021_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 * 
 *  @author JacobSeated
 */


$css_code = file_get_contents('tpl/d/dk_static.css') . file_get_contents('tpl/d/essential.css');

$_load_template = '
<!doctype html>
        <html>
        <head>
          <title>'.$tpl['title'].'</title>
          <style>' . $css_code . '</style>
        </head>
        <body>
          <div id="dk_page_wrap">
            <h1>'.$tpl['h1'].'</h1>
            <div class="dk_flex">

              <div id="dk_page_content" class="dk_flex_item">
                '.$tpl['content'].'
              </div>

              <div id="dk_page_side_box" class="dk_flex_item">
                '.$tpl['side_box'].'
              </div>

            </div>

          </div>
        </body>
        </html>';


return $_load_template;