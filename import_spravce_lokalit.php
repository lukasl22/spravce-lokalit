<?php
/* Plugin Správce lokalit  */
global $wpdb;
error_reporting(E_ERROR | E_PARSE);
$tablename_mesta = $wpdb->prefix . 'mesta';
$tablename_cast = $wpdb->prefix . "cast";
$tablename_ulice = $wpdb->prefix . "ulice";
$_SESSION['msg'] = "Ulice byla odstraněna";
if (isset($_POST['import_submit'])) {
    $csv = $_POST['import_url'];
    $fh = fopen($csv, 'r');
    $_SESSION['msg'] = "Lokality byly úspěšně vytvořeny";
    while (list($mesto, $cast, $ulice) = fgetcsv($fh, 1024, ';')) {

        if ($mesto != NULL) {

            $sql = "SELECT * FROM `$tablename_mesta` WHERE town=(%s)";
            $sql = $wpdb->prepare($sql, $mesto);
            $result_mesto =  $wpdb->get_results($sql);

            if (empty($result_mesto)) {

                $query = "INSERT INTO `$tablename_mesta` (town,town_url) VALUES(%s,%s)";
                $mesto_url = sanitize_title($mesto);
                $wpdb->query($wpdb->prepare($query, $mesto, $mesto_url));

                $sql = "SELECT id FROM `$tablename_mesta` WHERE town=(%s)";
                $sql = $wpdb->prepare($sql, $mesto);
                $result_id =  $wpdb->get_results($sql);
                foreach ($result_id as $print_id) :
                    $id_town = $print_id->id;
                endforeach;
            } else {

                foreach ($result_mesto as $print_mesto) :
                    $id_town = $print_mesto->id;
                endforeach;
            }

            $sql_cast = "SELECT  * FROM `$tablename_cast` WHERE cast=(%s) AND id_town=(%d)";
            $sql_cast = $wpdb->prepare($sql_cast, $cast, $id_town);
            $result_cast =  $wpdb->get_results($sql_cast);
            if (empty($result_cast)) {

                $query = "INSERT INTO `$tablename_cast` (id_town,cast,cast_url) VALUES(%d, %s, %s)";
                $cast_url = sanitize_title($cast);
                $wpdb->query($wpdb->prepare($query, $id_town, $cast, $cast_url));

                $$sql_cast = "SELECT  * FROM `$tablename_cast` WHERE cast=(%s) AND id_town=(%d)";
                $sql_cast = $wpdb->prepare($sql_cast, $cast, $id_town);
                $result_cast_id =  $wpdb->get_results($sql_cast);
                foreach ($result_cast as $print_cast) :
                    $id_cast = $print_cast->id;
                endforeach;
            } else {
                foreach ($result_cast as $print_cast) :
                    $id_cast = $print_cast->id;
                endforeach;
            }

            if ($ulice != NULL) {

                $sql_ulice = "SELECT  * FROM `$tablename_ulice` WHERE ulice=(%s) AND id_cast=(%d)";
                $sql_ulice = $wpdb->prepare($sql_ulice, $ulice, $id_cast);
                $result_ulice =  $wpdb->get_results($sql_ulice);
                if (empty($result_ulice)) {
                    $query = "INSERT INTO `$tablename_ulice` (id_cast,id_town,ulice,ulice_url) VALUES(%d, %d, %s, %s)";
                    $ulice_url = sanitize_title($ulice);
                    $wpdb->query($wpdb->prepare($query, $id_cast, $id_town, $ulice, $ulice_url));
                }
            }
        }
    }
}


?>
<form action='' method='post'>
    <h1>Import lokalit ze souboru CSV.</h1>
    <label>Zadejte url adresu souboru</label>
    <input type="text" id="import_url" name="import_url"></input>
    <input type="submit" value="Submit" name="import_submit">
    <?php
    if (isset($_SESSION['msg'])) :
        echo $_SESSION['msg'];
        unset($_SESSION['msg']);
    endif;
    ?>

</form>