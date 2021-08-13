<?php
/* Plugin Správce lokalit  */
global $wpdb;
error_reporting(E_ERROR | E_PARSE);
require_once('sitemap-spravce-lokalit.php');
$tablename_mesta = $wpdb->prefix . 'mesta';
$tablename_cast = $wpdb->prefix . "cast";
$tablename_ulice = $wpdb->prefix . "ulice";
$home_url = get_home_url();
//Add new record into the table MESTA
if (isset($_POST['new_submit'])) {
  $array = (explode("\n", $_POST['new_location']));
  foreach ($array as $values) :
    $sql = "SELECT * FROM `$tablename_mesta` WHERE town = %s";
    $sql = $wpdb->prepare($sql, $values);
    $result =  $wpdb->get_results($sql);
    if (empty($result)) :
      $query = "INSERT INTO `$tablename_mesta` (town,town_url) VALUES(%s,%s)";
      $array = (explode("\n", $_POST['new_location']));
      foreach ($array as $values) {
        $values_url = sanitize_title($values);
        $wpdb->query($wpdb->prepare($query, $values, $values_url));
      };
      $_SESSION['msg'] = "Lokace byla vložena";
      echo "<script>location.replace('admin.php?page=myplugin');</script>";
      create_sitemap();
    else :
      $_SESSION['msg'] = "Zadaná lokace existuje";
      echo "<script>location.replace('admin.php?page=myplugin');</script>";
    endif;
  endforeach;
}
//Add new record into the table CAST
if (isset($_POST['new_submit_cast'])) {
  $id_town = $_GET['add_cast'];
  $query = "INSERT INTO `$tablename_cast` (id_town,cast,cast_url) VALUES(%d, %s, %s)";
  $array = (explode("\n", $_POST['new_cast']));
  foreach ($array as $values) {
    $values_url = sanitize_title($values);
    $wpdb->query($wpdb->prepare($query, $id_town, $values, $values_url));
  }
  $_SESSION['msg'] = "Část byla vložena";
  echo "<script>location.replace('admin.php?page=myplugin');</script>";
  create_sitemap();
}
//Add new record into the table ulice
if (isset($_POST['new_submit_ulice'])) {
  $id_cast = $_GET['add_ulice'];
  $id_mesto = $_GET['id_mesto'];
  $query = "INSERT INTO `$tablename_ulice` (id_cast,id_town,ulice,ulice_url) VALUES(%d, %d, %s, %s)";
  $array = (explode("\n", $_POST['new_ulice']));
  foreach ($array as $values) {
    $values_url = sanitize_title($values);
    $wpdb->query($wpdb->prepare($query, $id_cast, $id_mesto, $values, $values_url));
  }
  $_SESSION['msg'] = "Část byla vložena";
  echo "<script>location.replace('admin.php?page=myplugin');</script>";
  create_sitemap();
}
//Update record in table mesta
if (isset($_POST['upt_submit'])) {
  $id = $_POST['upt_id'];
  $town = $_POST['upt_location'];
  $mesto_url = sanitize_title($town);
  $query = "UPDATE `$tablename_mesta` SET town=(%s),town_url=(%s) WHERE id=(%d)";
  $wpdb->query($wpdb->prepare($query, $town, $mesto_url, $id));
  $_SESSION['msg'] = "Lokace byla aktualizována";
  echo "<script>location.replace('admin.php?page=myplugin');</script>";
  create_sitemap();
}
//Update record in table cast
if (isset($_POST['upt_submit_cast'])) {
  $id = $_POST['upt_id_cast'];
  $cast = $_POST['upt_cast'];
  $cast_url = sanitize_title($cast);
  $query = "UPDATE `$tablename_cast` SET cast=(%s),cast_url=(%s) WHERE id=(%d)";
  $wpdb->query($wpdb->prepare($query, $cast, $cast_url, $id));
  $_SESSION['msg'] = "Lokace byla aktualizována";
  echo "<script>location.replace('admin.php?page=myplugin');</script>";
  create_sitemap();
}
//Update record in table ulice
if (isset($_POST['upt_submit_ulice'])) {
  $id = $_POST['upt_id_ulice'];
  $ulice = $_POST['upt_ulice'];
  $ulice_url = sanitize_title($ulice);
  $query = "UPDATE `$tablename_ulice` SET ulice=(%s),ulice_url=(%s) WHERE id=(%d)";
  $wpdb->query($wpdb->prepare($query, $ulice, $ulice_url, $id));
  $_SESSION['msg'] = "Lokace byla aktualizována";
  echo "<script>location.replace('admin.php?page=myplugin');</script>";
  create_sitemap();
}
//Delete record from table  MESTA
if (isset($_GET['del'])) {
  $del_id = $_GET['del'];
  $query_ulice = "DELETE FROM `$tablename_ulice` WHERE id_town=(%d)";
  $wpdb->query($wpdb->prepare($query_ulice, $del_id));
  $query_cast = "DELETE FROM `$tablename_cast` WHERE id_town=(%d)";
  $wpdb->query($wpdb->prepare($query_cast, $del_id));
  $query = "DELETE FROM `$tablename_mesta` WHERE id=(%d)";
  $wpdb->query($wpdb->prepare($query, $del_id));
  $_SESSION['msg'] = "Lokace odstraněna";
  echo "<script>location.replace('admin.php?page=myplugin');</script>";
  create_sitemap();
}
//Delete record from table CAST
if (isset($_GET['del_cast'])) {
  $del_id_cast = $_GET['del_cast'];
  $query_ulice = "DELETE FROM `$tablename_ulice` WHERE id_cast=(%d)";
  $wpdb->query($wpdb->prepare($query_ulice, $del_id_cast));
  $query = "DELETE FROM `$tablename_cast` WHERE id=(%d)";
  $wpdb->query($wpdb->prepare($query, $del_id_cast));
  $_SESSION['msg'] = "Cast odstraněna";
  echo "<script>location.replace('admin.php?page=myplugin');</script>";
  create_sitemap();
}
//Delete record from table ulice
if (isset($_GET['del_ulice'])) {
  $del_id_ulice = $_GET['del_ulice'];
  $query = "DELETE FROM `$tablename_ulice` WHERE id=(%d)";
  $wpdb->query($wpdb->prepare($query, $del_id_ulice));
  $_SESSION['msg'] = "Ulice odstraněna";
  echo "<script>location.replace('admin.php?page=myplugin');</script>";
  create_sitemap();
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

  .row-town a {
    color: #000;
  }

  .row-streets {
    background-color: #dbedfe;
    display: grid;
    grid-template-columns: repeat(5, 2fr 1fr);
    padding-right: 140px;
    padding-left: .3rem;
  }

  @media only screen and (max-width:1250px) {
    .row-streets {
      grid-template-columns: repeat(4, 2fr 1fr);
    }
  }

  @media only screen and (max-width:900px) {
    .row-streets {
      grid-template-columns: repeat(3, 2fr 1fr);
    }
  }

  @media only screen and (max-width:620px) {
    .row-streets {
      grid-template-columns: repeat(2, 2fr 1fr);
    }
  }

  .row-streets div {
    display: flex;
    align-items: center;
    padding: .5rem .3rem;
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
    color: white !important;
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
</style>

<div>
  <form action='' method='post'>
    <?php
    if (isset($_GET['upt_cast'])) :
      $upt_id_cast = $_GET['upt_cast'];
      $sql_cast_print = "SELECT * FROM $tablename_cast WHERE id=(%d)";
      $sql_cast_print = $wpdb->prepare($sql_cast_print, $upt_id_cast);
      $result = $wpdb->get_results($sql_cast_print);
      foreach ($result as $print) :
        $cast = $print->cast;
      endforeach;
    ?>
      <h2>Úprava části města <?= $cast; ?> </h2>
      <div class="form-wrapper">
        <input type='hidden' id='upt_id_cast' name='upt_id_cast' value='<?php echo ($upt_id_cast); ?>'>
        <input type='text' id='upt_cast' name='upt_cast'>
        <div class="buttons-group">
          <button class="btn submit" id='upt_submit_cast' name='upt_submit_cast' type='submit'>
            <i class="fas fa-plus"></i>
          </button>
          <a href='admin.php?page=myplugin'>
            <button class="btn cancel" type='button'>
              <i class="fas fa-times"></i>
            </button>
          </a>
        </div>
      </div>
    <?php
    elseif (isset($_GET['upt_ulice'])) :
      $upt_id_ulice = $_GET['upt_ulice'];
      $sql_ulice_print = "SELECT * FROM $tablename_ulice WHERE id=(%d)";
      $sql_ulice_print = $wpdb->prepare($sql_ulice_print, $upt_id_ulice);
      $result = $wpdb->get_results($sql_ulice_print);
      foreach ($result as $print) :
        $ulice = $print->ulice;
      endforeach;
    ?>
      <h2>Úprava ulice <?= $ulice; ?> </h2>
      <div class="form-wrapper">
        <input type='hidden' id='upt_id_ulice' name='upt_id_ulice' value='<?php echo ($upt_id_ulice); ?>'>
        <input type='text' id='upt_ulice' name='upt_ulice'>
        <div class="buttons-group">
          <button class="btn submit" id='upt_submit_ulice' name='upt_submit_ulice' type='submit'>
            <i class="fas fa-plus"></i>
          </button>
          <a href='admin.php?page=myplugin'>
            <button class="btn cancel" type='button'>
              <i class="fas fa-times"></i>
            </button>
          </a>
        </div>
      </div>
    <?php
    elseif (isset($_GET['upt'])) :
      $upt_id = $_GET['upt'];
      $sql_mesto_print = "SELECT * FROM $tablename_mesta WHERE id=(%d)";
      $sql_mesto_print = $wpdb->prepare($sql_mesto_print, $upt_id);
      $result = $wpdb->get_results($sql_mesto_print);
      foreach ($result as $print) :
        $town = $print->town;
      endforeach;
    ?>
      <h2>Úprava lokality <?= $town; ?> </h2>
      <div class="form-wrapper">
        <input type='hidden' id='upt_id' name='upt_id' value='<?php echo ($upt_id); ?>'>
        <input type='text' id='upt_location' name='upt_location'>
        <div class="buttons-group">
          <button class="btn submit" id='upt_submit' name='upt_submit' type='submit'>
            <i class="fas fa-plus"></i>
          </button>
          <a href='admin.php?page=myplugin'>
            <button class="btn cancel" type='button'>
              <i class="fas fa-times"></i>
            </button>
          </a>
        </div>
      </div>
    <?php
    elseif (isset($_GET['add_cast'])) : ?>
      <h2>Vložte městkou část</h2>
      <div class="form-wrapper">
        <label>Zadejte název městské části</label>
        <textarea type="text" id="new_cast" name="new_cast"></textarea>
        <div class="buttons-group">
          <button class="btn submit" id="new_submit_cast" name="new_submit_cast" type="submit">
            <i class="fas fa-plus"></i>
          </button>
          <a href='admin.php?page=myplugin'>
            <button class="btn cancel" type='button'>
              <i class="fas fa-times"></i>
            </button>
          </a>
        </div>
      </div>
    <?php
    elseif (isset($_GET['add_ulice'])) : ?>
      <h2>Vložte ulici</h2>
      <div class="form-wrapper">
        <label>Zadejte název ulice</label>
        <textarea type="text" id="new_ulice" name="new_ulice"></textarea>
        <div class="buttons-group">
          <button class="btn submit" id="new_submit_ulice" name="new_submit_ulice" type="submit">
            <i class="fas fa-plus"></i>
          </button>
          <a href='admin.php?page=myplugin'>
            <button class="btn cancel" type='button'>
              <i class="fas fa-times"></i>
            </button>
          </a>
        </div>
      </div>
    <?php elseif (isset($_GET['add_mesto'])) : ?>
      <h2>Vložte novou lokaci</h2>
      <div class="form-wrapper">
        <label>Zadejte novou lokalitu</label>
        <textarea type="text" id="new_location" name="new_location"></textarea>
        <div class="buttons-group">
          <button class="btn submit" class="btn submit" id="new_submit" name="new_submit" type="submit">
            <i class="fas fa-plus"></i>
          </button>
          <a href='admin.php?page=myplugin'>
            <button class="btn cancel" type='button'>
              <i class="fas fa-times"></i>
            </button>
          </a>
        </div>
      </div>
    <?php else : ?>
      <h2>Vyhledávač</h2>
      <div class="form-wrapper">
        <label>Vložte lokaci kterou chcete vyhledat</label>
        <input type="text" id="search_town" name="search_town"></input>
        <button class="btn submit" class="btn submit" id="search" name="search" type="submit">
          <i class="fas fa-search"></i>
        </button>
        <a href='admin.php?page=myplugin&add_mesto' class="add-new">
          Přidat novou lokalitu
        </a>
      </div>

      <?php
    endif;
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
      ?>
          <div class="row-town">
            <div class="name"><?= $town; ?></div>
            <div class="buttons-group">
              <a href='<?= $home_url ?>/internet/<?= $town_url ?>'>
                <button type='button' class="btn show">
                  <i class="fas fa-eye"></i>
                </button>
              </a>
              <a href='admin.php?page=myplugin&add_cast=<?= $id; ?>'>
                <button type='button' class="btn submit">
                  <i class="fas fa-plus"></i>
                </button>
              </a>
              <a href='admin.php?page=myplugin&del=<?= $id; ?>'>
                <button type='button' class="btn remove">
                  <i class="fas fa-minus"></i>
                </button>
              </a>
              <a href='admin.php?page=myplugin&upt=<?= $id; ?>'>
                <button type='button' class="btn edit">
                  <i class="fas fa-edit"></i>
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
            ?>
              <div class="row-part">
                <div class="name"><?= $cast ?></div>
                <div class="buttons-group">
                  <a href='<?= $home_url ?>/internet/<?= $town_url ?>/<?= $cast_url ?>'>
                    <button type='button' class="btn show">
                      <i class="fas fa-eye"></i>
                    </button>
                  </a>
                  <a href='admin.php?page=myplugin&add_ulice=<?= $id_cast; ?>&id_mesto=<?= $id; ?>'>
                    <button type='button' class="btn submit">
                      <i class="fas fa-plus"></i>
                    </button>
                  </a>
                  <a href='admin.php?page=myplugin&del_cast=<?= $id_cast; ?>'>
                    <button type='button' class="btn remove">
                      <i class="fas fa-minus"></i>
                    </button>
                  </a>
                  <a href='admin.php?page=myplugin&upt_cast=<?= $id_cast; ?>'>
                    <button type='button' class="btn edit">
                      <i class="fas fa-edit"></i>
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
                ?>
                  <div class="name"><?= $ulice ?></div>
                  <div class="buttons-group">
                    <a href='<?= $home_url ?>/internet/<?= $town_url ?>/<?= $cast_url ?>/<?= $ulice_url ?>'>
                      <button type='button' class="btn show">
                        <i class="fas fa-eye"></i>
                      </button>
                    </a>
                    <a href='admin.php?page=myplugin&del_ulice=<?= $id_ulice; ?>'>
                      <button type='button' class="btn remove">
                        <i class="fas fa-minus"></i>
                      </button>
                    </a>
                    <a href='admin.php?page=myplugin&upt_ulice=<?= $id_ulice; ?>'>
                      <button type='button' class="btn edit">
                        <i class="fas fa-edit"></i>
                      </button>
                    </a>
                  </div>
                <?php endforeach; ?>
              </div>
          <?php
            endforeach;
          endforeach;
        else :
          ?>

          <a href='admin.php?page=myplugin'>
            <h1>Lokalita nebyla nalezena!!</h1>
            <button class="btn cancel" type='button'>
              <i class="fas fa-times"></i>
            </button>
        <?php
        endif;
      endif;
        ?>