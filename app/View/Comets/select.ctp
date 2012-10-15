<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>FCBKcomplete Demo</title>
        <?php
            echo $this->Html->script('http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.6.min.js');
            echo $this->Html->script('jquery.fcbkcomplete.js');
            echo $this->Html->css('style','stylesheet');
         ?>
    </head>
    <body id="test">
        <h1>FCBKcomplete Demo</h1>
        <div id="text">
        </div>
        <form action="submit.php" method="POST" accept-charset="utf-8">
            <select id="select3" name="select3">
            </select>
            <br/>
            <br/>
            <input type="submit" value="Send">
        </form>
        <script type="text/javascript">
            $(document).ready(function(){                
                $("#select3").fcbkcomplete({
                    json_url: "http://localhost/instagram/meshtiles/searchtag",
                    addontab: true,                   
                    maxitems: 10,
                    input_min_size: 0,
                    height: 10,
                    cache: true,
                    newel: true,
                    select_all_text: "select",
                });
            });
        </script>
        
        <div id="testme"></div>
    </body>
</html>
