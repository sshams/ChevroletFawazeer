<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the "Database Connection"
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the "default" group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = "default";
$active_record = TRUE;

$db['default']['hostname'] = "localhost";
$db['default']['username'] = "root";
$db['default']['password'] = "password";
$db['default']['database'] = "chevrolet-fawazeer";
$db['default']['dbdriver'] = "mysql";
$db['default']['dbprefix'] = "";
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = "";
$db['default']['char_set'] = "utf8";
$db['default']['dbcollat'] = "utf8_general_ci";


/*$db['remote']['hostname'] = "localhost";
$db['remote']['username'] = "chevyfawazeer";
$db['remote']['password'] = "f@aVvz3&R";
$db['remote']['database'] = "chevrolet-fawazer";
$db['remote']['dbdriver'] = "mysql";
$db['remote']['dbprefix'] = "";
$db['remote']['pconnect'] = FALSE;
$db['remote']['db_debug'] = TRUE;
$db['remote']['cache_on'] = FALSE;
$db['remote']['cachedir'] = "";
$db['remote']['char_set'] = "utf8";
$db['remote']['dbcollat'] = "utf8_general_ci";*/


//the country database
$db['second_db']['hostname'] = "localhost";
$db['second_db']['username'] = "root";
$db['second_db']['password'] = "password";
$db['second_db']['database'] = "ips";
$db['second_db']['dbdriver'] = "mysql";
$db['second_db']['dbprefix'] = "";
$db['second_db']['pconnect'] = FALSE;
$db['second_db']['db_debug'] = TRUE;
$db['second_db']['cache_on'] = FALSE;
$db['second_db']['cachedir'] = "";
$db['second_db']['char_set'] = "utf8";
$db['second_db']['dbcollat'] = "utf8_general_ci";



/* End of file database.php */
/* Location: ./system/application/config/database.php */
?>