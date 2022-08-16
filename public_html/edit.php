<?php
session_start();

use markfullmer\waraydictionary\Db;
use markfullmer\waraydictionary\Render;

require '../vendor/autoload.php';
require '../variables.php';

if (!Db::isAuthenticated()) {
  header('Location: /index.php');
  die();
}
require './includes/head.php';

if (isset($_REQUEST['id'])) {
  if ($_REQUEST['id'] !== 'add') {
    $id = (int) $_REQUEST['id'];
    $type = 'Edit';
    $word = Db::getWord($id);
  }
  else {
    $id = 'add';
    $type = 'Add';
    foreach (Db::$keys as $key) {
      $word[$key] = '';
    }
  }
}
?>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <?php
      if ($id !== 'add' && !isset($word)) {
        echo 'There was an error retrieving the requested word. Please report this to the maintainers.';
        die();
      }
      if (isset($_GET['update'])) {
        echo 'Word successfully updated.';
      }
      ?>
      <h1><?php echo $type; ?> word <em><?php echo $word['word']; ?></em></h1>
      Pronunciation marks: ʔ Á á É é Í í Ó ó Ú ú
      <form method="post" action="save.php">
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <label>Word*<input type="text" name="word" value="<?php echo $word['word']; ?>" required /></label>
        <label>Pronunciation<input type="text" name="pronunciation" value="<?php echo $word['pronunciation']; ?>" /></label>
        <label>Root<input type="text" name="root" value="<?php echo $word['root']; ?>" /></label>
        <label>Synonyms<input type="text" name="synonym" value="<?php echo $word['synonym']; ?>" /><em>Separate multiple synonyms with a comma (e.g., yakan,busa)</em></label>
        <details open>
          <summary>Usage 1</summary>
          <label>Meaning<input type="text" name="one_def" value="<?php echo $word['one_def']; ?>" /></label>
          <label>Part of speech <select name="one_pos">
              <?php echo Render::getPosOptions($word['one_pos']); ?>
            </select>
          </label>
          Sample sentence:
          <textarea name="one_ex"><?php echo $word['one_ex']; ?></textarea>
        </details>
        <details open>
          <summary>Usage 2</summary>
          <label>Meaning<input type="text" name="two_def" value="<?php echo $word['two_def']; ?>" /></label>
          <label>Part of speech <select name="two_pos">
              <?php echo Render::getPosOptions($word['two_pos']); ?>
            </select>
          </label>
          Sample sentence:
          <textarea name="two_ex"><?php echo $word['two_ex']; ?></textarea>
        </details>
        <details open>
          <summary>Usage 3</summary>
          <label>Meaning<input type="text" name="three_def" value="<?php echo $word['three_def']; ?>" /></label>
          <label>Part of speech <select name="three_pos">
              <?php echo Render::getPosOptions($word['three_pos']); ?>
            </select>
          </label>
          Sample sentence:
          <textarea name="three_ex"><?php echo $word['three_ex']; ?></textarea>
        </details>
        <label><input value="Save" name="submit" type="submit"></label>

      </form>
    </div>
  </div>
</div>
