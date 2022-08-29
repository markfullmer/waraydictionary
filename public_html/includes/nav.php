<?php
use markfullmer\waraydictionary\Render;
?>
<nav>
    <form class="navbar-form pull-right" method="post" action="/login.php">
      <?php echo Render::loginForm(); ?>
    </form>
    <h1><a href="./index.php">3NS Corpora Project Dictionary</a></h1>
    <ul>
      <li class="active"><a href="/">Home</a></li>
      <li><a href="https://corporaproject.org/index.php?type=article&amp;id=1">About</a></li>
      <li><a href="/tagger/">Part of Speech Tagger</a></li>
      <li><a href="https://corporaproject.org/index.php?type=article&amp;id=3">Contact</a></li>
      <li><a href="https://corporaproject.org/index.php?type=article&amp;id=8">Help</a></li>

    </ul>
</nav>
