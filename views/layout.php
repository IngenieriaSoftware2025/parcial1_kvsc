<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="build/js/app.js"></script>
    <link rel="shortcut icon" href="<?= asset('images/garra.jpg') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <title>App de Carlos</title>
    <style>
        body{
            background-color:rgb(54, 54, 69);
        }
        .btn-thistle {
            background-color: palevioletred; 
            color: black; 
            border-color: pink; 
        }
        .btn-pink {
            background-color: papayawhip; 
            color: black; 
            border-color: purple; 
        }
        .btn-midni {
            background-color: rgb(5, 1, 24);
            color: white;
            border-color: white;
        }
        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background-color:rgb(11, 1, 56);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.8rem;
            padding: 1rem;
            border: none;
        }

        .table tbody td {
            padding: 0.875rem;
            vertical-align: middle;
            border-color: #f3f4f6;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }
        .bg-da{
            background-color: rgb(10, 5, 37);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-da">
        
        <div class="container-fluid">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="/parcial1_kvsc/">
                <img src="<?= asset('./images/king.avif') ?>" width="50px'" alt="rose" >
                ¡Buen dia Carlos!
            </a>
            <div class="collapse navbar-collapse" id="navbarToggler">
                
                <ul class="navbar-nav me-auto mb-2 mb-lg-0" style="margin: 0;">
                         <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/parcial1_kvsc/asistencia"><i class="bi bi-calendar-date"></i> Asistencia</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/parcial1_kvsc/actividad"><i class="bi bi-card-checklist"></i> Actividad</a>
                    </li>
                         <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/parcial1_kvsc/"><i class="bi bi-bicycle"></i> ¿Listo para otra actividad?</a>
                    </li>
                    </div> 

                </ul> 
                <div class="col-lg-1 d-grid mb-lg-0 mb-2">

                    <a href="/parcial1_kvsc/" class="btn btn-midni">
                        <i class="bi bi-box-arrow-in-down-right"></i> INICIO
                    </a>
                </div>

            
            </div>
        </div>
        
    </nav>
    <div class="progress fixed-bottom" style="height: 6px;">
        <div class="progress-bar progress-bar-animated bg-danger" id="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="container-fluid pt-5 mb-4" style="min-height: 85vh">
        
        <?php echo $contenido; ?>
    </div>
    <div class="container-fluid" >
        <div class="row justify-content-center text-center">
            <div class="col-12">
                <p style="font-size:medium; font-weight: bold; color: white;">
                        Vamos Carlos, recuerda que cada día es una nueva oportunidad para crecer y aprender. ¡Sigue adelante cada minuto cuenta. ¡Ánimo y éxito!
                </p>
            </div>
        </div>
    </div>
</body>
</html>