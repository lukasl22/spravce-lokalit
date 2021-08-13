<?php
/*
   Plugin Name: Správce lokalit
   Plugin URI: https://dcsoft.cz/
   description: Správce lokalit a generátor sitemap.
   Version: 0.1.0
   Author: DCSoft  
   Author URI: https://dcsoft.cz/
*/

// Create a new table
function customplugin_table()
{

   global $wpdb;
   $charset_collate = $wpdb->get_charset_collate();
   $tablename_mesta = $wpdb->prefix . "mesta";
   $tablename_cast = $wpdb->prefix . "cast";
   $tablename_ulice = $wpdb->prefix . "ulice";
   $tablename_tarify = $wpdb->prefix . "tarify";
   $tablename_relationship_town = $wpdb->prefix . "relationship_town";
   $tablename_relationship_cast = $wpdb->prefix . "relationship_cast";
   $tablename_relationship_ulice = $wpdb->prefix . "relationship_ulice";
   $tablename_tarif_group = $wpdb->prefix . "tarif_group";

   $sql_mesta = "CREATE TABLE $tablename_mesta (
   id int NOT NULL AUTO_INCREMENT, 
   town varchar(80) NOT NULL,  
   town_url varchar(80) NOT NULL,  
   PRIMARY KEY  (id)
   ) $charset_collate;";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql_mesta);

   $sql_casti = "CREATE TABLE $tablename_cast (
      id int NOT NULL AUTO_INCREMENT, 
      id_town int NOT NULL,
      cast varchar(100) NOT NULL, 
      cast_url varchar(100) NOT NULL,  
      PRIMARY KEY  (id),
      FOREIGN KEY (id_town) REFERENCES $tablename_mesta(id)
      ) $charset_collate;";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql_casti);

   $sql_ulice = "CREATE TABLE $tablename_ulice (
      id int NOT NULL AUTO_INCREMENT, 
      id_cast int NULL,
      id_town int NOT NULL,
      ulice varchar(100) NOT NULL,  
      ulice_url varchar(100) NOT NULL,
      PRIMARY KEY  (id),
      FOREIGN KEY (id_town) REFERENCES $tablename_mesta(id),
      FOREIGN KEY (id_cast) REFERENCES $tablename_cast(id)
      ) $charset_collate;";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql_ulice);

   $sql_tarify = "CREATE TABLE $tablename_tarify (
      id int NOT NULL AUTO_INCREMENT, 
      tarif_name varchar(100) NOT NULL,  
      spead varchar(100) NOT NULL,  
      descriptions varchar(100) NOT NULL,  
      price int NOT NULL,     
      descriptions2 varchar(100) NOT NULL,           
      PRIMARY KEY  (id),
      ) $charset_collate;";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql_tarify);

   $sql_relationship_town = "CREATE TABLE $tablename_relationship_town (
      id int NOT NULL AUTO_INCREMENT, 
      id_town int NOT NULL,      
      id_tarif int NOT NULL,
      PRIMARY KEY  (id),
      ) $charset_collate;";
   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql_relationship_town);

   $sql_relationship_cast = "CREATE TABLE $tablename_relationship_cast (
      id int NOT NULL AUTO_INCREMENT, 
      id_town int NOT NULL,  
      id_cast int NOT NULL, 
      id_tarif int NOT NULL,
      PRIMARY KEY  (id),
      ) $charset_collate;";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql_relationship_cast);

   $sql_relationship_ulice = "CREATE TABLE $tablename_relationship_ulice (
      id int NOT NULL AUTO_INCREMENT, 
      id_town int NOT NULL,  
      id_cast int NOT NULL,  
      id_ulice int NOT NULL,   
      id_tarif int NOT NULL,
      PRIMARY KEY  (id),
      ) $charset_collate;";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql_relationship_ulice);

$tablename_tarif_group = "CREATE TABLE wp_tarif_group ( 
id INT NOT NULL AUTO_INCREMENT ,
id_tarif_1 INT NOT NULL,
id_tarif_2 INT NOT NULL,
id_tarif_3 INT NOT NULL,
PRIMARY KEY (id)) ENGINE = InnoDB;";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql_relationship_ulice);
}
register_activation_hook(__FILE__, 'customplugin_table');


/* Add menu */

function customplugin_menu()
{

   add_menu_page("Spravce-lokalit", "Správce lokalit", "manage_options", "myplugin", "displayList", plugins_url('#'));
   add_submenu_page("myplugin", "Import", "Import", "manage_options", "myplugin-sub", "import", plugins_url('#'));
   add_submenu_page("myplugin", "Správce tarifů", "Správce tarifů", "manage_options", "myplugin-tarif", "tarif", plugins_url('#'));
}
add_action("admin_menu", "customplugin_menu");

function displayList()
{
   include "displaylist.php";
}
function import()
{
   include "import_spravce_lokalit.php";
}
function tarif()
{
   include "tariflist.php";
}


function register_my_session()
{
   if (!session_id()) {
      session_start();
   }
}

add_action('init', 'register_my_session');
