# url_checker
Tracks the status of URLs (E.g. 200, 301, 404) in a database table.

To run this, you will need to add the following to your VHOST or .htaccess:
```
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^.*$ index.php [QSA,L]
```

You also need a `credentials.php` file. The content should look like this:

```
/**
  Password to use with the database
*/
$db_password = 'your_db_password';

/**
  Username to use with the database
*/
$db_user = 'your_db_user';

```

Database and tables should automatically be created when opening the app in your browser.



