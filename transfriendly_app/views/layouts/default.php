<!doctype html>
<html>
    <head>
	    <meta charset="utf-8" />
        <title>NanoPHP Framework</title>

        <?php
            foreach (nanophp\Template\Template::getHeaders() as $header) {
                echo "{$header}\n";
            }
        ?>
    </head>
    <body>
        <?=$this->content?>
    </body>
</html>
