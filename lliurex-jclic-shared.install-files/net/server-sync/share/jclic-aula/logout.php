
<div id="logout" class="topbar">
    <form action="index.php" method="post">
        <input type="submit" class="topbarbutton" value="Logout" />
    </form>
    <?php
        echo ("<div class='topbarwidget'> Logged in as: ".$_SESSION["user"]."</div>");
    ?>
</div>