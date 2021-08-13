<?php
/* Plugin Správce lokalit  */
global $wpdb;
error_reporting(E_ERROR | E_PARSE);
require_once('sitemap-spravce-lokalit.php');
$tablename_mesta = $wpdb->prefix . 'mesta';
$tablename_cast = $wpdb->prefix . "cast";
$tablename_ulice = $wpdb->prefix . "ulice";
$tablename_tarify = $wpdb->prefix . "tarify";
$tablename_relationship_town = $wpdb->prefix . "relationship_town";
$tablename_relationship_cast = $wpdb->prefix . "relationship_cast";
$tablename_relationship_ulice = $wpdb->prefix . "relationship_ulice";
$tablename_tarif_group = $wpdb->prefix . "tarif_group";
$home_url = get_home_url();


if (isset($_GET['del_tarif_town'])) {
    $del_id = $_GET['del_id'];
    $del_id_tarif = $_GET['del_tarif_town'];

    $query = "DELETE FROM `$tablename_relationship_town` WHERE id_tarif=(%d) AND id_town=(%d)";
    $wpdb->query($wpdb->prepare($query,  $del_id_tarif, $del_id));
    $_SESSION['msg'] = "Tarif odstraněn";
    echo "<script>location.replace('admin.php?page=myplugin-tarif');</script>";
}

if (isset($_GET['del_tarif_town_all'])) {
    $del_id = $_GET['del_id'];
    $del_id_tarif = $_GET['del_tarif_town_all'];
    $query = "DELETE FROM `$tablename_relationship_town` WHERE id_tarif=(%d) AND id_town=(%d)";
    $wpdb->query($wpdb->prepare($query,  $del_id_tarif, $del_id));
    $query = "DELETE FROM `$tablename_relationship_cast` WHERE id_tarif=(%d) AND id_town=(%d)";
    $wpdb->query($wpdb->prepare($query,  $del_id_tarif, $del_id));
    $query = "DELETE FROM `$tablename_relationship_ulice` WHERE id_tarif=(%d) AND id_town=(%d)";
    $wpdb->query($wpdb->prepare($query,  $del_id_tarif, $del_id));
    echo "<script>location.replace('admin.php?page=myplugin-tarif');</script>";
}

if (isset($_GET['del_tarif_id_cast'])) {
    $del_id_tarif = $_GET['del_tarif_id_cast'];
    $del_id_cast = $_GET['del_id_cast'];
    $del_id = $_GET['del_id'];
    $query = "DELETE FROM `$tablename_relationship_cast` WHERE id_tarif=(%d) AND id_cast=(%d) AND id_town=(%d)";
    $wpdb->query($wpdb->prepare($query, $del_id_tarif, $del_id_cast, $del_id));
    $_SESSION['msg'] = "Tarif odstraněn";
    echo "<script>location.replace('admin.php?page=myplugin-tarif');</script>";
}

if (isset($_GET['del_tarif_id_ulice'])) {
    $del_tarif_id_ulice = $_GET['del_tarif_id_ulice'];
    $del_id_ulice = $_GET['del_id_ulice'];
    $del_id_cast = $_GET['del_id_cast'];
    $del_id = $_GET['del_id'];
    $query = "DELETE FROM `$tablename_relationship_ulice` WHERE id_tarif=(%d) AND id_ulice=(%d) AND id_cast=(%d) AND id_town=(%d)";
    $wpdb->query($wpdb->prepare($query, $del_tarif_id_ulice, $del_id_ulice, $del_id_cast, $del_id));
    $_SESSION['msg'] = "Tarif odstraněn";
    echo "<script>location.replace('admin.php?page=myplugin-tarif');</script>";
}
if (isset($_GET['del_tarif_group'])) {
    $del_id_group = $_GET['del_tarif_group'];
    $query = "DELETE FROM `$tablename_tarif_group` WHERE id=(%d)";
    $wpdb->query($wpdb->prepare($query,  $del_id_group));
    $_SESSION['msg'] = "Skupina odstraněna";
    echo "<script>location.replace('admin.php?page=myplugin-tarif');</script>";
}

if (isset($_POST['submit_skupina'])) {

    foreach ($_POST["tarif_skupina_checkbox"] as $tarif_skupina_checkbox) {
        ++$tarif;
    }
    if ($tarif == 3) {
        $pole = "";
        foreach ($_POST["tarif_skupina_checkbox"] as $tarif_skupina_checkbox) {
            $pole .= $tarif_skupina_checkbox . "a";
        }
        $idcka = explode("a", $pole);
        $query_group = "INSERT INTO $tablename_tarif_group (id_tarif_1,id_tarif_2,id_tarif_3) VALUES(%d,%d,%d)";
        $wpdb->query($wpdb->prepare($query_group, $idcka[0], $idcka[1], $idcka[2]));
    } else {
        print_r("Spatně vytvořená skupina");
    }
}

if (isset($_POST['insert_town'])) {
    $tarif = $_POST['tarifs'];
    $id = $_SESSION["id"];
    $radio = $_POST['town_radio'];
    if ($radio == "1") {
        $query_mesto = "INSERT INTO $tablename_relationship_town (id_town,id_tarif) VALUES(%d,%d)";
        $wpdb->query($wpdb->prepare($query_mesto, $id, $tarif));
        $sql_cast = "SELECT * FROM `$tablename_cast` WHERE id_town=(%d)";
        $sql_cast = $wpdb->prepare($sql_cast, $id);
        $result_cast =  $wpdb->get_results($sql_cast);
        foreach ($result_cast as $print_cast) :
            $id_cast = $print_cast->id;
            $query_cast = "INSERT INTO $tablename_relationship_cast (id_town,id_cast,id_tarif) VALUES(%d,%d,%d)";
            $wpdb->query($wpdb->prepare($query_cast, $id, $id_cast, $tarif));

            $sql_ulice = "SELECT * FROM `$tablename_ulice` WHERE id_cast=(%d)";
            $sql_ulice = $wpdb->prepare($sql_ulice, $id_cast);
            $result_ulice =  $wpdb->get_results($sql_ulice);
            foreach ($result_ulice as $print_ulice) :
                $id_ulice = $print_ulice->id;
                $query_ulice = "INSERT INTO $tablename_relationship_ulice (id_town,id_cast,id_ulice,id_tarif) VALUES(%d,%d,%d,%d)";
                $wpdb->query($wpdb->prepare($query_ulice, $id, $id_cast, $id_ulice, $tarif));
            endforeach;
        endforeach;
    } else if ($radio == "2") {
        $query = "INSERT INTO $tablename_relationship_town (id_town,id_tarif) VALUES(%d,%d)";
        $wpdb->query($wpdb->prepare($query, $id, $tarif));
    } else if ($radio == "3") {
        foreach ($_POST["tarif_checkbox"] as $tarif_checkbox) {
            $neco = sanitize_title($tarif_checkbox);
            $sql_cast = "SELECT * FROM `$tablename_cast` WHERE cast_url=(%s)";
            $sql_cast = $wpdb->prepare($sql_cast, $neco);
            $result_cast =  $wpdb->get_results($sql_cast);
            if ($result_cast != NULL) {
                foreach ($result_cast as $print_cast) {
                    $cast = $print_cast->cast;
                    $id_cast = $print_cast->id;
                    $cast_url = $print_cast->cast_url;
                    $id_town = $print_cast->id_town;
                    $query_cast = "INSERT INTO $tablename_relationship_cast (id_town,id_cast,id_tarif) VALUES(%d,%d,%d)";
                    $wpdb->query($wpdb->prepare($query_cast, $id_town, $id_cast, $tarif));
                }
            } else {
                $sql_ulice = "SELECT * FROM `$tablename_ulice` WHERE ulice_url=(%s)";
                $sql_ulice = $wpdb->prepare($sql_ulice, $neco);
                $result_ulice =  $wpdb->get_results($sql_ulice);
                foreach ($result_ulice as $print_ulice) {
                    $ulice = $print_ulice->ulice;
                    $id_ulice = $print_ulice->id;
                    $id_cast = $print_ulice->id_cast;
                    $id_town = $print_ulice->id_town;
                    $ulice_url = $print_ulice->ulice_url;
                    $query_ulice = "INSERT INTO $tablename_relationship_ulice (id_town,id_cast,id_ulice,id_tarif) VALUES(%d,%d,%d,%d)";
                    $wpdb->query($wpdb->prepare($query_ulice, $id_town, $id_cast, $id_ulice, $tarif));
                }
            }
        }
    } else {
        echo "<script>alert('Nebyla vybrána žádná možnost!!')</script>";
    }
}

if (isset($_POST['insert_town_group'])) {
    $tarif_group = $_POST['tarifs-group'];
    $id = $_SESSION["id"];
    $radio = $_POST['town_radio_group'];
    $sql_tarify_group = "SELECT * FROM $tablename_tarif_group WHERE id=%d";
    $sql_tarify_group = $wpdb->prepare($sql_tarify_group, $tarif_group);
    $result_tarify_group =  $wpdb->get_results($sql_tarify_group);
    foreach ($result_tarify_group as $print_tarif_group) :
        $id_tarif_group = $print_tarif_group->id;
        $id_tarif_1 = $print_tarif_group->id_tarif_1;
        $id_tarif_2 = $print_tarif_group->id_tarif_2;
        $id_tarif_3 = $print_tarif_group->id_tarif_3;
        if ($radio == "1") {
            $query_mesto = "INSERT INTO $tablename_relationship_town (id_town,id_tarif) VALUES(%d,%d)";
            $wpdb->query($wpdb->prepare($query_mesto, $id, $id_tarif_1));
            $query_mesto = "INSERT INTO $tablename_relationship_town (id_town,id_tarif) VALUES(%d,%d)";
            $wpdb->query($wpdb->prepare($query_mesto, $id, $id_tarif_2));
            $query_mesto = "INSERT INTO $tablename_relationship_town (id_town,id_tarif) VALUES(%d,%d)";
            $wpdb->query($wpdb->prepare($query_mesto, $id, $id_tarif_3));
            $sql_cast = "SELECT * FROM `$tablename_cast` WHERE id_town=(%d)";
            $sql_cast = $wpdb->prepare($sql_cast, $id);
            $result_cast =  $wpdb->get_results($sql_cast);
            foreach ($result_cast as $print_cast) :
                $id_cast = $print_cast->id;
                $query_cast = "INSERT INTO $tablename_relationship_cast (id_town,id_cast,id_tarif) VALUES(%d,%d,%d)";
                $wpdb->query($wpdb->prepare($query_cast, $id, $id_cast, $id_tarif_1));
                $query_cast = "INSERT INTO $tablename_relationship_cast (id_town,id_cast,id_tarif) VALUES(%d,%d,%d)";
                $wpdb->query($wpdb->prepare($query_cast, $id, $id_cast, $id_tarif_2));
                $query_cast = "INSERT INTO $tablename_relationship_cast (id_town,id_cast,id_tarif) VALUES(%d,%d,%d)";
                $wpdb->query($wpdb->prepare($query_cast, $id, $id_cast, $id_tarif_3));

                $sql_ulice = "SELECT * FROM `$tablename_ulice` WHERE id_cast=(%d)";
                $sql_ulice = $wpdb->prepare($sql_ulice, $id_cast);
                $result_ulice =  $wpdb->get_results($sql_ulice);
                foreach ($result_ulice as $print_ulice) :
                    $id_ulice = $print_ulice->id;
                    $query_ulice = "INSERT INTO $tablename_relationship_ulice (id_town,id_cast,id_ulice,id_tarif) VALUES(%d,%d,%d,%d)";
                    $wpdb->query($wpdb->prepare($query_ulice, $id, $id_cast, $id_ulice, $id_tarif_1));
                    $query_ulice = "INSERT INTO $tablename_relationship_ulice (id_town,id_cast,id_ulice,id_tarif) VALUES(%d,%d,%d,%d)";
                    $wpdb->query($wpdb->prepare($query_ulice, $id, $id_cast, $id_ulice, $id_tarif_2));
                    $query_ulice = "INSERT INTO $tablename_relationship_ulice (id_town,id_cast,id_ulice,id_tarif) VALUES(%d,%d,%d,%d)";
                    $wpdb->query($wpdb->prepare($query_ulice, $id, $id_cast, $id_ulice, $id_tarif_3));
                endforeach;
            endforeach;
        } else if ($radio == "2") {
            print_r($radio);
            $query = "INSERT INTO $tablename_relationship_town (id_town,id_tarif) VALUES(%d,%d)";
            $wpdb->query($wpdb->prepare($query, $id, $id_tarif_1));
            $query = "INSERT INTO $tablename_relationship_town (id_town,id_tarif) VALUES(%d,%d)";
            $wpdb->query($wpdb->prepare($query, $id, $id_tarif_2));
            $query = "INSERT INTO $tablename_relationship_town (id_town,id_tarif) VALUES(%d,%d)";
            $wpdb->query($wpdb->prepare($query, $id, $id_tarif_3));
        } else if ($radio == "3") {

            foreach ($_POST["tarif_checkbox"] as $tarif_checkbox) {
                $neco = sanitize_title($tarif_checkbox);
                $sql_cast = "SELECT * FROM `$tablename_cast` WHERE cast_url=(%s)";
                $sql_cast = $wpdb->prepare($sql_cast, $neco);
                $result_cast =  $wpdb->get_results($sql_cast);
                if ($result_cast != NULL) {
                    foreach ($result_cast as $print_cast) {
                        $cast = $print_cast->cast;
                        $id_cast = $print_cast->id;
                        $cast_url = $print_cast->cast_url;
                        $id_town = $print_cast->id_town;
                        $query_cast = "INSERT INTO $tablename_relationship_cast (id_town,id_cast,id_tarif) VALUES(%d,%d,%d)";
                        $wpdb->query($wpdb->prepare($query_cast, $id_town, $id_cast, $id_tarif_1));
                        $query_cast = "INSERT INTO $tablename_relationship_cast (id_town,id_cast,id_tarif) VALUES(%d,%d,%d)";
                        $wpdb->query($wpdb->prepare($query_cast, $id_town, $id_cast, $id_tarif_2));
                        $query_cast = "INSERT INTO $tablename_relationship_cast (id_town,id_cast,id_tarif) VALUES(%d,%d,%d)";
                        $wpdb->query($wpdb->prepare($query_cast, $id_town, $id_cast, $id_tarif_3));
                    }
                } else {
                    $sql_ulice = "SELECT * FROM `$tablename_ulice` WHERE ulice_url=(%s)";
                    $sql_ulice = $wpdb->prepare($sql_ulice, $neco);
                    $result_ulice =  $wpdb->get_results($sql_ulice);
                    foreach ($result_ulice as $print_ulice) {
                        $ulice = $print_ulice->ulice;
                        $id_ulice = $print_ulice->id;
                        $id_cast = $print_ulice->id_cast;
                        $id_town = $print_ulice->id_town;
                        $ulice_url = $print_ulice->ulice_url;
                        $query_ulice = "INSERT INTO $tablename_relationship_ulice (id_town,id_cast,id_ulice,id_tarif) VALUES(%d,%d,%d,%d)";
                        $wpdb->query($wpdb->prepare($query_ulice, $id_town, $id_cast, $id_ulice, $id_tarif_1));
                        $query_ulice = "INSERT INTO $tablename_relationship_ulice (id_town,id_cast,id_ulice,id_tarif) VALUES(%d,%d,%d,%d)";
                        $wpdb->query($wpdb->prepare($query_ulice, $id_town, $id_cast, $id_ulice, $id_tarif_2));
                        $query_ulice = "INSERT INTO $tablename_relationship_ulice (id_town,id_cast,id_ulice,id_tarif) VALUES(%d,%d,%d,%d)";
                        $wpdb->query($wpdb->prepare($query_ulice, $id_town, $id_cast, $id_ulice, $id_tarif_3));
                    }
                }
            }
        } else {
            echo "<script>alert('Nebyla vybrána žádná možnost!!')</script>";
        }
    endforeach;
}

?>
<style>
    body {
        font-size: 15px;
        padding-right: 1rem;
    }

    a {
        text-decoration: none;
    }

    .btn {
        cursor: pointer;
        border-radius: .25em;
        padding: 0;
        border: 0;
        outline: 0;
        margin: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn i {
        color: white;
        font-size: .75em;
    }

    .show {
        background-color: #01a2b9;
    }

    .submit {
        background-color: #198754;
    }

    .cancel,
    .remove {
        background-color: #dc3545;
    }

    .remove.all {
        width: max-content;
        font-weight: normal;
        padding: .4rem .5rem;
        margin-left: 0 !important;
        color: #fff;
    }

    .edit {
        background-color: #0d6efd;
    }

    .form-wrapper {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    @media only screen and (max-width: 820px) {
        .form-wrapper {
            flex-direction: column;
        }

        .form-wrapper .buttons-group,
        .form-wrapper textarea,
        .form-wrapper input,
        .form-wrapper .btn,
        .form-wrapper .add-new {
            margin-left: 0 !important;
        }
    }

    .form-wrapper>button {
        margin-left: 1rem !important;
    }

    .form-wrapper input,
    .form-wrapper textarea {
        margin-left: 1rem;
    }

    .form-wrapper label {
        height: 35px;
        display: flex;
        align-items: center;
    }

    .form-wrapper .btn {
        width: 25px;
        height: 25px;
        margin: 3px 0;
    }

    .form-wrapper .btn i {
        font-size: .9em;
    }

    .form-wrapper .buttons-group .submit {
        margin-right: .3rem !important;
    }

    .form-wrapper .buttons-group {
        margin: 0 0 0 1rem;
    }

    textarea {
        height: 35px;
        width: 275px;
        transition: .2s ease-in-out border;
    }

    textarea:focus {
        border: 1px solid #4ca5f9;
    }

    input {
        width: 275px;
    }

    .row-town,
    .row-part {
        background-color: #c9e4fd;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .5rem 1.1rem .5rem .3rem;
    }

    .row-town {
        background-color: #82c0fb;
        font-weight: bold;
    }

    .tarif-group {
        display: flex;
        margin: 0 1rem 0 auto;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .tarif-flex a,
    .tarif-flex {
        display: flex;
    }

    .tarif-flex .text {
        font-weight: normal;
        text-align: right;
    }

    .tarif-flex .btn {
        margin: auto .5rem;
    }

    .row-streets {
        background-color: #dbedfe;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        padding: 0 .3rem;
    }


    .row-streets .tarif-group {
        margin-right: .7rem;
    }

    @media only screen and (min-width: 1301px) {
        .row-streets>div:nth-child(3n) .buttons-group {
            border: none;
        }
    }

    @media only screen and (max-width:1300px) and (min-width: 1101px) {
        .row-streets {
            grid-template-columns: repeat(2, 1fr);
        }

        .row-streets>div:nth-child(2n) .buttons-group {
            border: none;
        }
    }

    @media only screen and (max-width:1100px) {
        .row-streets {
            grid-template-columns: repeat(1, 1fr);
        }

        .row-streets .buttons-group {
            border: none !important;
        }
    }

    .row-streets>div {
        display: flex;
    }

    .row-streets>div>div {
        display: flex;
        align-items: center;
        padding: .5rem .3rem;
    }

    .row-streets input[type=checkbox],
    .row-part input[type=checkbox] {
        margin: 0 1rem 0 0;
    }

    .row-part .name,
    .row-streets .name {
        display: flex;
        align-items: center;
    }

    .row-streets .buttons-group {
        border-right: 1px solid #82c0fb;
        padding-right: 12.8px;
    }

    @media only screen and (min-width:1250px) {
        .row-streets .buttons-group:nth-child(10n) {
            border: 0;
            padding-right: .3rem !important;
        }
    }

    @media only screen and (max-width:1250px) and (min-width:901px) {
        .row-streets .buttons-group:nth-child(8n) {
            border: 0;
            padding-right: .3rem !important;
        }
    }

    @media only screen and (max-width:900px) and (min-width:621px) {
        .row-streets .buttons-group:nth-child(6n) {
            border: 0;
            padding-right: .3rem !important;
        }
    }

    @media only screen and (max-width:620px) {
        .row-streets .buttons-group:nth-child(4n) {
            border: 0;
            padding-right: .3rem !important;
        }
    }

    .buttons-group {
        display: flex;
        align-items: center;
        justify-content: flex-end !important;
    }

    .buttons-group>a+a {
        margin: 2.5px 0 2.5px .3rem;
    }

    .name {
        padding-left: .8rem !important;
    }

    .add-new {
        background-color: #0d6efd;
        cursor: pointer;
        color: white;
        border-radius: .25em;
        padding: .2rem .5rem;
        margin: .1rem 0 0 1rem;
        border: 0;
        outline: 0;
    }

    #upt_cast,
    #upt_ulice {
        margin: 0;
    }

    .tarifs {
        border-collapse: collapse;
    }

    .tarifs th,
    .tarifs td {
        padding: .5rem 1.5rem;
    }

    .tarifs thead {
        background-color: #82c0fb;
    }

    .tarifs tbody {
        background-color: #c9e4fd;
    }

    .tarifs-seach-form {
        margin-bottom: 1rem;
        display: flex;
        flex-direction: column;
    }

    .tarifs-seach-form select {
        margin-bottom: .5rem;
        max-width: 200px;
    }

    .tarifs-seach-form .radio-input {
        display: flex;
        align-items: center;
        margin-bottom: .5rem;
    }

    .tarifs-seach-form .radio-input label {
        min-width: 130px;
    }

    .tarifs-seach-form .radio-input input {
        margin: 0 0 0 .5rem;
    }

    .tarifs-seach-form input[type=submit] {
        margin: 0;
        width: fit-content;
        padding: .3rem .6rem;
    }

    .not-found {
        display: flex;
        align-items: center;
    }

    .not-found button {
        margin-left: 1rem;
    }

    .flex-tables {
        display: flex;
    }

    @media only screen and (max-width: 1300px) {
        .flex-tables {
            flex-direction: column;
        }
    }

    .flex-tables .add-new {
        margin: .5rem 0 0;
    }

    .flex-tables .tarif-group-table {
        margin-left: 1rem;
    }

    @media only screen and (max-width: 1300px) {
        .flex-tables .tarif-group-table {
            margin: .5rem 0 0;
        }
    }

    .flex-tarif-form {
        display: flex;
    }

    @media only screen and (max-width: 700px) {
        .flex-tarif-form {
            flex-direction: column;
        }
    }

    .flex-tarif-form .tarifs-seach-form {
        margin-right: 3rem;
    }

    @media only screen and (max-width: 700px) {
        .flex-tarif-form .tarifs-seach-form {
            margin: 0 0 1rem;
        }
    }
</style>

<div>
    <form action='' method='post'>
        <h2>Vyhledávač</h2>
        <div class="form-wrapper">
            <label>Vložte lokaci kterou chcete vyhledat</label>
            <input type="text" id="search_town" name="search_town"></input>
            <button class="btn submit" class="btn submit" id="search" name="search" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <?php
        if (isset($_SESSION['msg'])) :
            echo  $_SESSION['msg'];
            unset($_SESSION['msg']);
        endif;
        if (isset($_POST['search'])) :
            $town = $_POST['search_town'];
            $url_search = sanitize_title($town);
            $sql = "SELECT * FROM `$tablename_mesta` WHERE town_url = %s";
            $sql = $wpdb->prepare($sql, $url_search);
            $result =  $wpdb->get_results($sql);
            if (!empty($result)) :
                foreach ($result as $print) :
                    $town = $print->town;
                    $id = $print->id;
                    $town_url = $print->town_url;
                    $_SESSION["id"] =  $id;
        ?>
                    <div class="flex-tarif-form">
                        <div class="tarifs-seach-form">
                            <select name="tarifs">
                                <option value="">----</option>
                                <?php
                                $sql_tarify = "SELECT * FROM `$tablename_tarify`";
                                $sql_tarify = $wpdb->prepare($sql_tarify);
                                $result_tarify =  $wpdb->get_results($sql_tarify);
                                foreach ($result_tarify as $print_tarif) :
                                    $id_tarif = $print_tarif->id;
                                    $tarif_name = $print_tarif->tarif_name;
                                    $spead = $print_tarif->spead;
                                    $price = $print_tarif->price;
                                    echo " <option value=' $id_tarif'> $id_tarif $tarif_name  $spead $price</option>";
                                ?>
                                <?php endforeach; ?>
                            </select>
                            <div class="radio-input">
                                <label for="town-radio-1">Tarif pro celé město</label>
                                <input type="radio" value="1" name="town_radio" id="town-radio-1">
                            </div>
                            <div class="radio-input">
                                <label for="town-radio-2">Tarif jen pro město</label>
                                <input type="radio" value="2" name="town_radio" id="town-radio-2">
                            </div>
                            <div class="radio-input">
                                <label for="town-radio-3">Vlastní volba tarifů</label>
                                <input type="radio" value="3" name="town_radio" id="town-radio-3">
                            </div>
                            <input type="submit" class="add-new" name="insert_town" value="Přiřadit tarif" />
                        </div>
                        <!--Insert tarif group-->
                        <div class="tarifs-seach-form">
                            <select name="tarifs-group">
                                <option value="">----</option>
                                <?php
                                $sql_tarify_group = "SELECT * FROM `$tablename_tarif_group`";
                                $sql_tarify_group = $wpdb->prepare($sql_tarify_group);
                                $result_tarify_group =  $wpdb->get_results($sql_tarify_group);
                                foreach ($result_tarify_group as $print_tarif_group) :
                                    $id_tarif_group = $print_tarif_group->id;
                                    $id_tarif_1 = $print_tarif_group->id_tarif_1;
                                    $id_tarif_2 = $print_tarif_group->id_tarif_2;
                                    $id_tarif_3 = $print_tarif_group->id_tarif_3;

                                    echo " <option value=' $id_tarif_group'> Skupina tarifů " . $id_tarif_group . "</option>";
                                ?>
                                <?php endforeach; ?>
                            </select>
                            <div class="radio-input">
                                <label for="town-radio-group-1">Přiřadit skupinu tarifů pro celé město</label>
                                <input type="radio" value="1" name="town_radio_group" id="town-radio-group-1">
                            </div>
                            <div class="radio-input">
                                <label for="town-radio-group-2">Přiřadit skupinu tarifů jen pro město</label>
                                <input type="radio" value="2" name="town_radio_group" id="town-radio-group-2">
                            </div>
                            <div class="radio-input">
                                <label for="town-radio-group-3">Vlastní volba přiřazení skupiny tarifů</label>
                                <input type="radio" value="3" name="town_radio_group" id="town-radio-group-3">
                            </div>
                            <input type="submit" class="add-new" name="insert_town_group" value="Přiřadit skupinu tarifů" />
                        </div>
                    </div>
                    <div class="row-town">
                        <div class="name"><?= $town; ?></div>
                        <div class="tarif-group">
                            <?php
                            $sq = "SELECT * FROM `$tablename_relationship_town` WHERE id_town = %d";
                            $sq = $wpdb->prepare($sq, $id);
                            $res =  $wpdb->get_results($sq);
                            foreach ($res as $print_tarif_2) :
                                $id_tarif_c = $print_tarif_2->id_tarif;
                                $sql_tarify = "SELECT * FROM `$tablename_tarify` WHERE id=%d";
                                $sql_tarify = $wpdb->prepare($sql_tarify, $id_tarif_c);
                                $result_tarify =  $wpdb->get_results($sql_tarify);
                                foreach ($result_tarify as $print_tarif) :
                                    $tarif_name = $print_tarif->tarif_name;
                                    $tarif_speed = $print_tarif->spead;
                            ?>
                                    <div class="tarif-flex">
                                        <div class="text"><?= $tarif_name . " " . $tarif_speed  ?></div>
                                        <a href='admin.php?page=myplugin-tarif&del_tarif_town=<?= $id_tarif_c; ?>&del_id=<?= $id; ?>'>
                                            <button type='button' class="btn remove">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </a>
                                        <a href='admin.php?page=myplugin-tarif&del_tarif_town_all=<?= $id_tarif_c; ?>&del_id=<?= $id; ?>'>
                                            <button type='button' class="btn remove all">
                                                Smazat u všeho
                                            </button>
                                        </a>
                                    </div>
                            <?php endforeach;
                            endforeach; ?>
                        </div>
                        <div class="buttons-group">
                            <a href='<?= $home_url ?>/internet/<?= $town_url ?>'>
                                <button type='button' class="btn show">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <div>
                        <?php
                        $sql_cast = "SELECT * FROM `$tablename_cast` WHERE id_town=(%d)";
                        $sql_cast = $wpdb->prepare($sql_cast, $id);
                        $result_cast =  $wpdb->get_results($sql_cast);
                        foreach ($result_cast as $print_cast) :
                            $cast = $print_cast->cast;
                            $id_cast = $print_cast->id;
                            $cast_url = $print_cast->cast_url;
                            $_SESSION["id_cast"] = $id_cast;

                        ?>
                            <div class="row-part">
                                <div class="name">
                                    <input type="checkbox" name="tarif_checkbox[]" id="<?= $cast ?>" value="<?= $cast ?>" />
                                    <label for="<?= $cast ?>"><?= $cast ?></label>
                                </div>
                                <div class="tarif-group">
                                    <?php
                                    $sq = "SELECT * FROM `$tablename_relationship_cast` WHERE id_town = %d AND id_cast = %d";
                                    $sq = $wpdb->prepare($sq, $id, $id_cast);
                                    $res =  $wpdb->get_results($sq);
                                    foreach ($res as $print_tarif_2) :
                                        $id_tarif_c = $print_tarif_2->id_tarif;
                                        $sql_tarify = "SELECT * FROM `$tablename_tarify` WHERE id=%d";
                                        $sql_tarify = $wpdb->prepare($sql_tarify, $id_tarif_c);
                                        $result_tarify =  $wpdb->get_results($sql_tarify);
                                        foreach ($result_tarify as $print_tarif) :
                                            $tarif_name = $print_tarif->tarif_name;
                                            $tarif_speed = $print_tarif->spead;
                                    ?>
                                            <div class="tarif-flex">
                                                <div class="text"><?= $tarif_name . " " . $tarif_speed  ?></div>
                                                <a href='admin.php?page=myplugin-tarif&del_tarif_id_cast=<?= $id_tarif_c; ?>&del_id_cast=<?= $id_cast; ?>&del_id=<?= $id; ?>'>
                                                    <button type='button' class="btn remove">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </a>
                                            </div>
                                    <?php endforeach;
                                    endforeach; ?>
                                </div>
                                <div class="buttons-group">
                                    <a href='<?= $home_url ?>/internet/<?= $town_url ?>/<?= $cast_url ?>'>
                                        <button type='button' class="btn show">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </a>
                                </div>
                            </div>
                            <div class="row-streets">
                                <?php
                                $sql_ulice = "SELECT * FROM `$tablename_ulice` WHERE id_cast=(%d)";
                                $sql_ulice = $wpdb->prepare($sql_ulice, $id_cast);
                                $result_ulice =  $wpdb->get_results($sql_ulice);
                                foreach ($result_ulice as $print_ulice) :
                                    $ulice = $print_ulice->ulice;
                                    $id_ulice = $print_ulice->id;
                                    $ulice_url = $print_ulice->ulice_url;
                                    $_SESSION["id_ulice"] = $id_ulice;
                                ?>
                                    <div>

                                        <div class="name">
                                            <input type="checkbox" name="tarif_checkbox[]" id="<?= $ulice ?>" value="<?= $ulice ?>" />
                                            <label for="<?= $ulice ?>"><?= $ulice ?></label>
                                        </div>
                                        <div class="tarif-group">
                                            <?php
                                            $sq = "SELECT * FROM `$tablename_relationship_ulice` WHERE id_town = %d AND id_cast = %d AND id_ulice = %d";
                                            $sq = $wpdb->prepare($sq, $id, $id_cast, $id_ulice);
                                            $res =  $wpdb->get_results($sq);
                                            foreach ($res as $print_tarif_2) :
                                                $id_tarif_c = $print_tarif_2->id_tarif;
                                                $sql_tarify = "SELECT * FROM `$tablename_tarify` WHERE id=%d";
                                                $sql_tarify = $wpdb->prepare($sql_tarify, $id_tarif_c);
                                                $result_tarify =  $wpdb->get_results($sql_tarify);
                                                foreach ($result_tarify as $print_tarif) :
                                                    $tarif_name = $print_tarif->tarif_name;
                                                    $tarif_speed = $print_tarif->spead;
                                            ?>
                                                    <div class="tarif-flex">
                                                        <div class="text"><?= $tarif_name . " " . $tarif_speed  ?></div>
                                                        <a href='admin.php?page=myplugin-tarif&del_tarif_id_ulice=<?= $id_tarif_c; ?>&del_id_ulice=<?= $id_ulice ?>&del_id_cast=<?= $id_cast; ?>&del_id=<?= $id; ?>'>
                                                            <button type='button' class="btn remove">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                        </a>
                                                    </div>
                                            <?php endforeach;
                                            endforeach; ?>
                                        </div>
                                        <div class="buttons-group">
                                            <a href='<?= $home_url ?>/internet/<?= $town_url ?>/<?= $cast_url ?>/<?= $ulice_url ?>'>
                                                <button type='button' class="btn show">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                    <?php
                        endforeach;
                    endforeach;
                else :
                    ?>
                    <a class="not-found" href='admin.php?page=myplugin-tarif'>
                        <h2>Lokalita nebyla nalezena!</h2>
                        <button class="btn cancel" type='button'>
                            <i class="fas fa-times"></i>
                        </button>
                    </a>
                <?php
                endif;
            else :
                ?>
                <div class="flex-tables">
                    <div>
                        <h2>Seznam tarifů</h2>
                        <table class="tarifs demo">
                            <thead>
                                <tr>
                                    <th>Název tarifu</th>
                                    <th>Rychlost</th>
                                    <th>Cena</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sql_tarify = "SELECT * FROM `$tablename_tarify`";
                                $sql_tarify = $wpdb->prepare($sql_tarify);
                                $result_tarify =  $wpdb->get_results($sql_tarify);
                                foreach ($result_tarify as $print_tarif) :
                                    $id = $print_tarif->id;
                                    $tarif_name = $print_tarif->tarif_name;
                                    $spead = $print_tarif->spead;
                                    $price = $print_tarif->price;
                                ?>
                                    <tr>
                                        <td><?= $tarif_name ?></td>
                                        <td><?= $spead ?></td>
                                        <td><?= $price ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tarif-group-table">
                        <h2>Skupiny tarifů</h2>
                        <table class="tarifs demo">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Tarif 1</th>
                                    <th>Tarif 2</th>
                                    <th>Tarif 3</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sql_tarif_group = "SELECT * FROM `$tablename_tarif_group`";
                                $sql_tarif_group = $wpdb->prepare($sql_tarif_group);
                                $result_tarify =  $wpdb->get_results($sql_tarif_group);
                                foreach ($result_tarify as $print_tarif) :
                                    $id_group = $print_tarif->id;
                                    $id_tarif1 = $print_tarif->id_tarif_1;
                                    $id_tarif2 = $print_tarif->id_tarif_2;
                                    $id_tarif3 = $print_tarif->id_tarif_3;
                                ?>
                                    <tr>
                                        <td><?= $id_group ?></td>
                                        <?php
                                        $sql_tarify = "SELECT * FROM $tablename_tarify WHERE id=(%d)";
                                        $sql_tarify = $wpdb->prepare($sql_tarify, $id_tarif1);
                                        $result_tarify = $wpdb->get_results($sql_tarify);
                                        foreach ($result_tarify as $print_tarif) :
                                            $id = $print_tarif->id;
                                            $tarif_name = $print_tarif->tarif_name;
                                            $spead = $print_tarif->spead;
                                            $price = $print_tarif->price;
                                        ?>
                                            <td><?= $tarif_name . " " . $spead ?></td>
                                        <?php endforeach;

                                        $sql_tarify = "SELECT * FROM $tablename_tarify WHERE id=(%d)";
                                        $sql_tarify = $wpdb->prepare($sql_tarify, $id_tarif2);
                                        $result_tarify = $wpdb->get_results($sql_tarify);
                                        foreach ($result_tarify as $print_tarif) :
                                            $id = $print_tarif->id;
                                            $tarif_name = $print_tarif->tarif_name;
                                            $spead = $print_tarif->spead;
                                            $price = $print_tarif->price;
                                        ?>
                                            <td><?= $tarif_name . " " . $spead ?></td>
                                        <?php endforeach;

                                        $sql_tarify = "SELECT * FROM $tablename_tarify WHERE id=(%d)";
                                        $sql_tarify = $wpdb->prepare($sql_tarify, $id_tarif3);
                                        $result_tarify = $wpdb->get_results($sql_tarify);
                                        foreach ($result_tarify as $print_tarif) :
                                            $id = $print_tarif->id;
                                            $tarif_name = $print_tarif->tarif_name;
                                            $spead = $print_tarif->spead;
                                            $price = $print_tarif->price;
                                        ?>
                                            <td><?= $tarif_name . " " . $spead ?></td>

                                        <?php endforeach; ?>
                                        <td><a href='admin.php?page=myplugin-tarif&del_tarif_group=<?= $id_group; ?>'>
                                                <button type='button' class="btn remove all">
                                                    OSDTRANIT
                                                </button>
                                            </a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button id="create" class="add-new" name="create">Vytvořit skupinu</button>
                    </div>
                    <?php
                    if (isset($_POST['create'])) :
                    ?>
                        <div class="tarif-group-table">
                            <h2>Vyberte 3 tarify na vytvoření skupiny</h2>
                            <table class="tarifs demo">
                                <thead>
                                    <tr>
                                        <th>Výběr tarifů</th>
                                        <th>Název tarifu</th>
                                        <th>Rychlost</th>
                                        <th>Cena</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sql_tarify = "SELECT * FROM `$tablename_tarify`";
                                    $sql_tarify = $wpdb->prepare($sql_tarify);
                                    $result_tarify =  $wpdb->get_results($sql_tarify);
                                    foreach ($result_tarify as $print_tarif) :
                                        $id = $print_tarif->id;
                                        $tarif_name = $print_tarif->tarif_name;
                                        $spead = $print_tarif->spead;
                                        $price = $print_tarif->price;
                                    ?>
                                        <tr>
                                            <td><input type="checkbox" name="tarif_skupina_checkbox[]" id="<?= $id ?>" value="<?= $id ?>" /></td>
                                            <td><?= $tarif_name ?></td>
                                            <td><?= $spead ?></td>
                                            <td><?= $price ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <button type="submit" class="add-new" name="submit_skupina">Vytvořit</button>
                        </div>
                </div>
        <?php
                    endif;
                endif;
