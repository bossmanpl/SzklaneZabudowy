<!doctype html>
<html lang="pl_PL">
<head>
    <meta charset="utf-8">
    <title>Szklane zabudowy - kreator</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="js/lib/slick/slick.css">
    <link rel="stylesheet" href="js/lib/slick/slick-theme.css">
    <link rel="stylesheet" href="css/app.css">
</head>
<body>

<div class="row">
    <div id="intro" class="col-xs-8 col-xs-offset-2 text-center">
        <h2>Szklane zabudowy</h2>

        <p>
            <input id="backgroundFileInput" type="file" value="Załaduj plik z dysku">
        </p>

        <p>lub użyj przykładowego zdjęcia</p>

        <p><input id="loadDefaultBackground" type="button" class="btn btn-primary" value="Załaduj przykładowe zdjęcie">
        </p>
    </div>
</div>

<div id="info"></div>

<div id="main" style="display: none;">
    <div class="row toolbar">
        <div class="col-xs-4">
            <div class="buttons">
                <div class="btn btn-default draw-mode"><span class="glyphicon glyphicon-pencil"></span></div>
                <div class="btn btn-default normal-mode"><span class="glyphicon glyphicon-move"></span></div>
                <div class="btn btn-default remove-button hidden"><span class="glyphicon glyphicon-trash"></span></div>
            </div>
        </div>
        <div class="col-xs-8 hidden">
            <form class="form-inline">
                <div class="form-group">
                    <label for="imgWidth">Rozmiar</label>
                    <input type="range" min="50" max="800" value="400" id="imgWidth">
                </div>
                <div class="form-group">
                    <label for="imgOffsetX">Pozycja pozioma</label>
                    <input type="range" min="0" max="800" value="400" id="imgOffsetX">
                </div>
                <div class="form-group">
                    <label for="imgOffsetY">Pozycja pionowa</label>
                    <input type="range" min="0" max="800" value="400" id="imgOffsetY">
                </div>
            </form>
        </div>
    </div>
    <canvas id="appCanvas" width="800" height="450"></canvas>
</div>

<div id="gallery" style="display: none;"></div>

<script src="js/lib/jquery-2.1.4.min.js"></script>
<script src="js/lib/fabric-1.5.8.min.js"></script>
<script src="js/lib/slick/slick.min.js"></script>
<script src="js/lib/bootstrap.min.js"></script>
<script src="js/app.js"></script>
<script>
    App.Main.init({
        galleryDataUrl: '../demo.json'
    });
</script>

</body>
</html>