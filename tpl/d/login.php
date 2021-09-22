<?php
/**
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-2021_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 * 
 *  @author JacobSeated
 */

$css_code = file_get_contents('tpl/d/dk_static.css') . file_get_contents('tpl/d/essential.css');

$_load_template = '<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <style>' . $css_code . '#login_form {max-width:600px;min-width:300px;width:100%;margin:0 auto;}</style>
</head>

<body>
  <article id="login_form">
   <h1>Login</h1>
   <form action="/login" class="dk_form" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" placeholder="username" class="dk_field_border_style dk_field">
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" class="dk_field_border_style dk_field">
    <input type="submit" value="Login" class="dk_button">
   </form>
  </article>
</body>

</html>';


return $_load_template;