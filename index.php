<?php

    //Verify if there's a shorcut
    if(isset($_GET['q'])){

        $shortcut = htmlspecialchars($_GET['q']);

        $bdd = new PDO('mysql:host=localhost;dbname=shorty', 'root', '');

        //Verify is the shortcut has already been used
        $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links where shortcut = ?');
        $req->execute(array($shortcut));

        while($result = $req->fetch()){
            if($result['x'] != 1){
                header('Location: ../?error=true&message=Adresse url non connue');
                exit();
            }
        }

        //Redirection
        $req = $bdd->prepare('SELECT * FROM links WHERE shortcut = ?');
        $req->execute(array($shortcut));

        while($result = $req->fetch()){

            header('Location: ' .$result['url']);
            exit();
        }
    } 

    //Verify url is set
    if(isset($_POST['url'])){
        
        $url = $_POST['url'];

        //Verify url is valide
        if(!filter_var($url, FILTER_VALIDATE_URL)){
            //redirecting after submit
            header('Location: ../?error=true&message=Adresse url non valide');
            exit();
        }

        //create random shortcut
        $shortcut = crypt($url, rand());

        //connexion bdd
        $bdd = new PDO('mysql:host=localhost;dbname=', '', '');
        
        //request to verify if the url hasn't already been cut
        $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE url = ?');
        $req->execute(array($url));

        while($result = $req->fetch()){
            if($result['x'] !=0){
                header('Location: ../?error=true&message=Adresse déjà raccourcie');
                exit();
            }
        }

        //request to insert the link + the shortcut in the bdd
        $req = $bdd->prepare('INSERT INTO links(url, shortcut) VALUES (?, ?)');
        $req->execute(array($url, $shortcut));

        header('Location: ../?short=' . $shortcut);
        exit();
    }  
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Raccourcisseur d'url</title>
        <link rel="stylesheet" type="text/css" href="design/default.css" />
        <link rel="icon" type="image/png" href="pictures/favico.png" />
    </head>
<body>

    <section id="hello">
        <div class="container">

            <header>
                <img id="logo" src="pictures/logo.png" alt="logo">
            </header>

            <h1>UNE URL LONGUE ? RACCOURCISSEZ-LA</h1>

            <h2>Largement meilleur et plus court que les autres</h2>

            <form method="post" action="../">
                <input name="url" placeholder="Collez un lien à raccourcir"/>
                <input type="submit" value="Raccourcir">
            </form>

            <?php
            if(isset($_GET['error']) && isset($_GET['message'])){ ?>

            <div class="center">
                <div id="result">
                    <b><?= htmlspecialchars($_GET['message']);?></b>
                </div>
            </div>
                
           <?php  }  
           else if(isset($_GET['short'])){ ?>
                <div class="center">
                <div id="result">
                    <b>URL RACCOURCIE :</b> 
                    http://url.elodie-roger.fr/?q=<?= htmlspecialchars($_GET['short']);?>
                </div>
            </div>

          <?php }  ?>

        </div>
    </section>

    <section id="brands">
        <div class="container">
            <h3>CES MARQUES NOUS FONT CONFIANCE</h3>

            <img class="picture" src="pictures/1.png" alt="1">
            <img class="picture" src="pictures/2.png" alt="2">
            <img class="picture" src="pictures/3.png" alt="3">
            <img class="picture" src="pictures/4.png" alt="4">

        </div>
    </section>

    <footer>
        <img src="pictures/logo2.png" alt="logo2" id="logo">
        <br>2020 © Shorty<br>
        <a href="#">Contact</a> - <a href="#">A propos</a>
    </footer>
</body>
</html>