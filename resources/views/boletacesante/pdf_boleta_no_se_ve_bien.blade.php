<?php
/*
?>
<html>
<head>
  <title><?=$boleta['apeBcl'].' '.$boleta['nomBcl'].' '.$boleta['mesBca'].'-'.$boleta['anoBca']?></title>
  <style>
    @page { margin: 10px 0px; }
        body{
            background-image: url("assets/images/ministeriodeeducacion1.png");
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-position: 50% 50%;
            background-size: 45%;
            opacity: 0.2;
            filter: alpha(opacity=20);
            zoom: 1;
        }
  </style>
<body>
    <div id="fondo"></div>
    <!--font-size: 9.9px;margin: 0px 180px;-->
  <pre style="font-size: 14px;margin: 0px 260px;opacity: 0;  filter: alpha(opacity=0);">
    <?php
    print_r($boleta['textBcl']);
    ?>
  </pre>
</body>
</html>
<?php
*/
?>

<html>
<head>
  <title><?=$boleta['apeBcl'].' '.$boleta['nomBcl'].' '.$boleta['mesBca'].'-'.$boleta['anoBca']?></title>
  <style>
    @page { margin: 10px 50px; 0px; 0px; }
    body{
            background-image: url("assets/images/ministeriodeeducacion1.png");
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-position: 45% 50%;
            background-size: 45%;
            opacity: 0.2;
            filter: alpha(opacity=20);
            zoom: 1;
        }
    #header { position: fixed; left: 0px; top: -10px; right: 0px; height: 0px; background-color: orange; text-align: center; }
    #footer { position: fixed; left: 0px; bottom: -10px; right: 0px; height: 0px; text-align: center;  }
  </style>
<body>

  <div id="footer"><?=(false)?'<img style="width:150px;" src="./'.$esp['firma'].'">':''?></div>
  <div id="content">
    <pre style="font-size: 14px;margin: 0px 260px;opacity: 0;  filter: alpha(opacity=0);">
    <?php
    print_r($boleta['textBcl']);
    ?>
    </pre>
  </div>
</body>
</html>
