<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized</title>
</head>

<body>
    <h1>Error 401 page Unauthorized</h1>
    
    <?php
        //redirection vers la route précédente
        //header("Location:" .  $_SERVER['HTTP_REFERER']);
        header("location:javascript://history.go(-1)");
    ?>
</body>

</html>