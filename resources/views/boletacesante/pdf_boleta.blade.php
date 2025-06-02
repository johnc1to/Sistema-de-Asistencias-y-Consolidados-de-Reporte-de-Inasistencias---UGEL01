<html>
<head>
  <title><?=$boleta['apeBcl'].' '.$boleta['nomBcl'].' '.$boleta['mesBca'].'-'.$boleta['anoBca']?></title>
  <style>
    @page { margin: 10px 0px; }
        body{
            background-image: url("assets/images/ministeriodeeducacion1.jpg");
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-position: 50% 50%;
            background-size: 45%;
            opacity: 0.48;
            filter: alpha(opacity=48);
            zoom: 1;
        }
  </style>
  <?php if($esp){ ?><img style="position:absolute; z-index:3; opacity:1;width:200px;margin-top:300px;margin-left:15px;" src="./<?=$esp['firma']?>"> <?php } ?>
<body>
    <!--font-size: 9.9px;margin: 0px 180px;-->
  <pre style="font-size: 14px;margin: 0px 260px;opacity: 0;  filter: alpha(opacity=0);">
    <?php
    print_r($boleta['textBcl']);
    ?>
  </pre>
</body>
</html>
