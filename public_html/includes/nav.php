<?php
use markfullmer\waraydictionary\Render;
?>
<nav>
    <form class="navbar-form pull-right" method="post" action="login.php">
      <?php echo Render::loginForm(); ?>
    </form>
    <h1><a href="./index.php">3NS Corpora Project Dictionary</a></h1>
    <ul>
      <li class="active"><a href="./index.php">Home</a></li>
      <li><a href="https://corporaproject.org/index.php?type=article&amp;id=1">About</a></li>
      <li><a href="https://corporaproject.org/index.php?type=word&amp;id=all">Words</a></li>
      <li><a href="https://corporaproject.org/index.php?type=text&amp;id=all">Texts</a></li>
      <li><a href="https://corporaproject.org/index.php?type=article&amp;id=3">Contact</a></li>
      <li><a href="https://corporaproject.org/index.php?type=article&amp;id=8">Help</a></li>

    </ul>
</nav>
