<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Sistema de solicitud de certificados académicos"
    >

    <title>
        Sistema de Certificados Académicos
    </title>


    <!-- =====================================================
         BOOTSTRAP
    ====================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- =====================================================
         BOOTSTRAP ICONS
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <!-- =====================================================
         GOOGLE FONTS
    ====================================================== -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Pacifico&display=swap"
        rel="stylesheet"
    >


    <!-- =====================================================
         ESTILOS
    ====================================================== -->

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        html,
        body {
            width: 100%;
            height: 100%;
        }


        body {
            font-family: 'Montserrat', sans-serif;
            overflow-x: hidden;
        }


        /* =================================================
           CONTENEDOR PRINCIPAL
        ================================================== */

        .hero {
            position: relative;

            min-height: 100vh;
            width: 100%;

            display: flex;
            align-items: center;

            background-image:
                 url('./assets/img/fondo-colegio.jpg');

            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            overflow: hidden;
        }


        /* =================================================
           CAPA OSCURA
        ================================================== */

        .hero::before {
            content: "";

            position: absolute;

            inset: 0;

            background:
                linear-gradient(
                    90deg,
                    rgba(3, 31, 83, 0.97) 0%,
                    rgba(3, 31, 83, 0.88) 35%,
                    rgba(3, 31, 83, 0.45) 65%,
                    rgba(3, 31, 83, 0.18) 100%
                );

            z-index: 1;
        }


        /* =================================================
           CONTENIDO
        ================================================== */

        .hero-contenido {

            position: relative;

            z-index: 3;

            width: 100%;

            max-width: 1400px;

            margin: auto;

            padding: 70px 70px 120px 70px;
        }


        /* =================================================
           PEQUEÑO ENCABEZADO
        ================================================== */

        .etiqueta {

            display: inline-flex;

            align-items: center;

            gap: 10px;

            padding: 9px 18px;

            margin-bottom: 25px;

            border: 1px solid rgba(255,255,255,0.35);

            border-radius: 50px;

            background: rgba(255,255,255,0.08);

            color: white;

            font-size: 13px;

            font-weight: 600;

            letter-spacing: 1px;

            text-transform: uppercase;

            backdrop-filter: blur(5px);
        }


        .etiqueta i {
            color: #ffc107;
        }


        /* =================================================
           TÍTULO
        ================================================== */

        .titulo {

            max-width: 800px;

            color: white;

            font-size: clamp(
                45px,
                6vw,
                82px
            );

            font-weight: 800;

            line-height: 1.05;

            letter-spacing: -2px;

            margin-bottom: 10px;
        }


        /* =================================================
           SLOGAN
        ================================================== */

        .slogan {

            max-width: 850px;

            color: #ffc107;

            font-family: 'Pacifico', cursive;

            font-size: clamp(
                32px,
                4vw,
                58px
            );

            line-height: 1.2;

            margin-bottom: 30px;
        }


        /* =================================================
           LÍNEA DECORATIVA
        ================================================== */

        .linea {

            width: 125px;

            height: 5px;

            background: #ffc107;

            border-radius: 20px;

            margin-bottom: 30px;
        }


        /* =================================================
           DESCRIPCIÓN
        ================================================== */

        .descripcion {

            max-width: 680px;

            color: rgba(255,255,255,0.92);

            font-size: 19px;

            line-height: 1.7;

            margin-bottom: 35px;
        }


        /* =================================================
           BOTÓN
        ================================================== */

        .btn-estudiante {

            display: inline-flex;

            align-items: center;

            gap: 20px;

            padding: 18px 30px;

            min-width: 360px;

            border: none;

            border-radius: 12px;

            background: #0758d9;

            color: white;

            text-decoration: none;

            font-size: 18px;

            font-weight: 700;

            box-shadow:
                0 12px 30px rgba(0,0,0,0.25);

            transition:
                transform 0.25s ease,
                background 0.25s ease,
                box-shadow 0.25s ease;
        }


        .btn-estudiante:hover {

            background: #0648b3;

            color: white;

            transform: translateY(-4px);

            box-shadow:
                0 18px 35px rgba(0,0,0,0.35);
        }


        .btn-icono {

            width: 48px;

            height: 48px;

            display: flex;

            align-items: center;

            justify-content: center;

            border: 2px solid rgba(255,255,255,0.9);

            border-radius: 50%;

            font-size: 22px;
        }


        .btn-texto {

            flex: 1;

            text-align: left;
        }


        .btn-flecha {

            font-size: 25px;

            transition: transform 0.25s ease;
        }


        .btn-estudiante:hover .btn-flecha {

            transform: translateX(6px);
        }


        /* =================================================
           TEXTO DEBAJO DEL BOTÓN
        ================================================== */

        .texto-ayuda {

            margin-top: 15px;

            margin-left: 5px;

            color: rgba(255,255,255,0.75);

            font-size: 14px;
        }


        /* =================================================
           ESCUDO
        ================================================== */

        .escudo {

            position: absolute;

            z-index: 4;

            right: 70px;

            bottom: 70px;

            width: 210px;

            max-width: 20vw;

            filter:
                drop-shadow(
                    0 15px 20px
                    rgba(0,0,0,0.4)
                );

            transition:
                transform 0.3s ease;
        }


        .escudo:hover {

            transform: scale(1.04);
        }


        /* =================================================
           DECORACIÓN INFERIOR
        ================================================== */

        .decoracion {

            position: absolute;

            z-index: 2;

            left: -5%;

            bottom: -90px;

            width: 110%;

            height: 150px;

            background: white;

            transform: rotate(-3deg);
        }


        .decoracion::before {

            content: "";

            position: absolute;

            top: -15px;

            left: 0;

            width: 100%;

            height: 8px;

            background: #ffc107;
        }


        /* =================================================
           PIE
        ================================================== */

        .pie {

            position: absolute;

            z-index: 5;

            bottom: 18px;

            left: 50%;

            transform: translateX(-50%);

            color: rgba(255,255,255,0.75);

            font-size: 12px;

            text-align: center;

            white-space: nowrap;
        }


        /* =================================================
           ANIMACIONES
        ================================================== */

        @keyframes aparecer {

            from {

                opacity: 0;

                transform: translateY(25px);
            }

            to {

                opacity: 1;

                transform: translateY(0);
            }
        }


        .animacion {

            animation:
                aparecer 0.9s ease forwards;
        }


        .animacion-2 {

            animation:
                aparecer 0.9s ease 0.15s forwards;

            opacity: 0;
        }


        .animacion-3 {

            animation:
                aparecer 0.9s ease 0.3s forwards;

            opacity: 0;
        }


        .animacion-4 {

            animation:
                aparecer 0.9s ease 0.45s forwards;

            opacity: 0;
        }


        /* =================================================
           TABLET
        ================================================== */

        @media (max-width: 992px) {

            .hero-contenido {

                padding:
                    60px
                    40px
                    150px
                    40px;
            }


            .titulo {

                max-width: 650px;
            }


            .slogan {

                max-width: 650px;
            }


            .escudo {

                right: 35px;

                bottom: 65px;

                width: 160px;

                max-width: 18vw;
            }

        }


        /* =================================================
           CELULAR
        ================================================== */

        @media (max-width: 768px) {

            .hero {

                align-items: flex-start;

                min-height: 100vh;
            }


            .hero::before {

                background:
                    linear-gradient(
                        180deg,
                        rgba(3, 31, 83, 0.95) 0%,
                        rgba(3, 31, 83, 0.85) 55%,
                        rgba(3, 31, 83, 0.65) 100%
                    );
            }


            .hero-contenido {

                padding:
                    45px
                    25px
                    180px
                    25px;
            }


            .etiqueta {

                font-size: 11px;

                padding: 8px 14px;
            }


            .titulo {

                font-size: 42px;

                letter-spacing: -1px;

                line-height: 1.08;
            }


            .slogan {

                font-size: 32px;

                line-height: 1.2;
            }


            .descripcion {

                font-size: 16px;

                line-height: 1.6;
            }


            .btn-estudiante {

                width: 100%;

                min-width: 0;

                padding: 15px 20px;

                gap: 12px;

                font-size: 15px;
            }


            .btn-icono {

                width: 42px;

                height: 42px;

                font-size: 18px;
            }


            .btn-flecha {

                font-size: 21px;
            }


            .escudo {

                right: 50%;

                bottom: 55px;

                transform: translateX(50%);

                width: 115px;

                max-width: none;
            }


            .escudo:hover {

                transform:
                    translateX(50%)
                    scale(1.04);
            }


            .decoracion {

                height: 100px;

                bottom: -65px;
            }


            .pie {

                bottom: 10px;

                width: 90%;

                white-space: normal;

                font-size: 10px;
            }

        }


        /* =================================================
           CELULARES PEQUEÑOS
        ================================================== */

        @media (max-width: 400px) {

            .hero-contenido {

                padding:
                    35px
                    20px
                    170px
                    20px;
            }


            .titulo {

                font-size: 35px;
            }


            .slogan {

                font-size: 27px;
            }


            .descripcion {

                font-size: 15px;
            }


            .btn-estudiante {

                font-size: 14px;
            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     HERO PRINCIPAL
========================================================= -->

<section class="hero">


    <!-- =====================================================
         CONTENIDO
    ====================================================== -->

    <div class="hero-contenido">


        <!-- ETIQUETA -->

        <div class="etiqueta animacion">

            <i class="bi bi-mortarboard-fill"></i>

            Sistema de Certificados Académicos

        </div>


        <!-- TÍTULO -->

        <h1 class="titulo animacion-2">

            Validamos certificados para ti, en segundos

        </h1>


        <!-- SLOGAN -->

        <div class="slogan animacion-3">

             con un clic.

        </div>


        <!-- LÍNEA -->

        <div class="linea animacion-3"></div>


        <!-- DESCRIPCIÓN -->

        <p class="descripcion animacion-3">

            Solicita tus certificados académicos de manera
            fácil, rápida y segura, sin complicaciones.

        </p>


        <!-- =================================================
             BOTÓN ESTUDIANTE
        ================================================== -->

        <a
            href="/certificado/estudiante"
            class="btn-estudiante animacion-4"
        >


            <span class="btn-icono">

                <i class="bi bi-person"></i>

            </span>


            <span class="btn-texto">

                Ingresar como Estudiante

            </span>


            <span class="btn-flecha">

                <i class="bi bi-arrow-right"></i>

            </span>


        </a>


        <!-- TEXTO AYUDA -->

        <div class="texto-ayuda animacion-4">

            <i class="bi bi-shield-check"></i>

            Accede para solicitar tus certificados académicos.

        </div>


    </div>



    <!-- =====================================================
         ESCUDO
    ====================================================== -->

    <img
        src="assets/img/escudo.png"
        alt="Escudo de la institución educativa"
        class="escudo"
    >



    <!-- =====================================================
         DECORACIÓN INFERIOR
    ====================================================== -->

    <div class="decoracion"></div>



    <!-- =====================================================
         PIE DE PÁGINA
    ====================================================== -->

    <div class="pie">

        Sistema de Certificados Académicos
        &nbsp;•&nbsp;
        Institución Educativa

    </div>


</section>



<!-- =========================================================
     BOOTSTRAP JS
========================================================= -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>