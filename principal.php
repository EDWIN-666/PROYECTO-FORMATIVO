<!DOCTYPE html>
<html lang="es">
<head>
    <title>Jardin Infantil Mundo Acuarela</title>
    <meta charset="UTF-8"><!--Caracteres especiales-->
    <link rel="stylesheet" type="text/css" href="CSS/MUNDOACUARELA.CSS">
    <link rel="icon" type="image/vnd.icon" href="IMG/LogoLibros.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/MUNDOACUARELA.CSS">

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Chewy&family=Handlee&family=Lobster&display=swap');
  body{
    overflow-x: hidden;
  }
  .imagen{
    width: 360px;
    height: 300px;
    margin: 10px;
  }
  .imagenx{
    width: 100%;
    height: 100%;
  }
.texto{
  text-shadow: -3px -3px rgb(0, 0, 0);
}
.fin{
    width: 100%;
    height: 80px;
    background: linear-gradient(to bottom, #5c61fe 0%, #72e0f1 100%);
}
ul, .servers{
    position: relative;
    justify-content: center;
    text-align: center;
    list-style: none;
    /* width: 22%;
    height: 300px;
    margin: 10px;
    list-style: none */
}
@media only screen and (max-width: 450px) {
        }
  </style>
</head>
<body>
      <!-- este el nav-->
<div class="row">
<div class="col-12">

  <nav class="navbar navbar-expand-lg bg-info col-12" data-bs-theme="dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#"><img src="img/aventuras.png" alt="" width="60px">MUNDO ACUARELA</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarScroll">
        <ul class="navbar-nav me-auto my-2 my-lg-0" style="--bs-scroll-height: 100px;">
          <li class="nav-item">
            <a class="nav-link active bg-success fs-5 mt-2 m-1 rounded-4 p-2 bg-opacity-50" aria-current="page" href="#inicio">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active bg-success fs-5 mt-2 m-2 rounded-4 p-2 bg-opacity-50" href="#servicios">Servicios</a>
          </li>
  
          <li class="nav-item">
            <a class="nav-link active bg-success fs-5 mt-2 m-2 rounded-4 p-2 bg-opacity-50" href="#">Institucionales</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active bg-success fs-5 mt-2 m-2 rounded-4 p-2 bg-opacity-50" aria-current="page" href="#">Matriculas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active bg-success fs-5 mt-2 m-2 rounded-4 p-2 bg-opacity-50" href="#escuela">Escuela Familiar</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active bg-success fs-5 mt-2 m-2 rounded-4 p-2 bg-opacity-50" href="#galeria">Galeria</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active bg-success fs-5 mt-2 m-2 rounded-4 p-2 bg-opacity-50" href="#contactenos">Contactenos</a>
          </li>
          <li class="nav-item">
            <button class="btn" type="submit"><a  href="index_sesion.php">  <img src="IMG/agregar-usuario.png" alt="" width="45px"></a></button></li>
          <li>
          <button class="btn" type="submit"><svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="currentColor" class="bi bi-search-heart" viewBox="0 0 16 16">
            <path d="M6.5 4.482c1.664-1.673 5.825 1.254 0 5.018-5.825-3.764-1.664-6.69 0-5.018"/>
            <path d="M13 6.5a6.47 6.47 0 0 1-1.258 3.844q.06.044.115.098l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1-.1-.115h.002A6.5 6.5 0 1 1 13 6.5M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11"/>
          </svg></button>
        </li>
        </ul>
      </div>
    </div>
  </nav>       
</div>
</div>
  <!-- fin del nav-->
        <!--Incio de bienvenida-->
        <div class="row" id="inicio">
            <div class="col-lg-3 col-sm-12 mt-2 position-relative">
            <div class=" mx-auto p-2">
                <img src="img/cuadro.png" class="mb-3 col-6 mx-auto d-block" data-bs-toggle="modal" data-bs-target="#modal2" alt="...">
                <p class="col-12 mt-2 fs-2 text-center text-primary">BIENVENIDOS AL MUNDO ACUARELA</p>
                <P class="text col-12 mt-2 fs-2 text-center text-danger">EDUCACIÓN Y CALIDAD POR AÑOS</P>
                <button class="Conócenos col-12 text-white bg-info m-2 p-2 btn fs-3 mx-auto" >Conócenos</button>
                <button class="SobreElJardin col-12 text-white bg-danger m-2 p-2 btn fs-3 mx-auto" >Sobre el jardín</button>
            </div>
              </div>
              
            <!--Fin de bienvenida-->
        <!--Inicio Carousel :D-->
        <div class="col-lg-9 col-sm-12 mt-5">
            <div id="carouselExampleSlidesOnly carouselExampleFade" class="col-lg-8 col-sm-12 mx-auto carousel slide carousel-fade" data-bs-ride="carousel">
              <div class="carousel-inner">
        <div class="carousel-item active" data-bs-interval="2000">
          <img src="img/carousel.jpg" height="450px" class="d-block w-100" alt="...">
          <div class="carousel-caption d-none d-md-block rounded-pill d-sm-block" style="--bs-bg-opacity: .7">
          </div>
        </div>
        <div class="carousel-item" data-bs-interval="2000">
          <img src="img/bcarousel.jpg" height="450px" class="d-block w-100" alt="...">
          <div class="carousel-caption d-none d-md-block rounded-pill d-sm-block" style="--bs-bg-opacity: .7">
          </div>
        </div>
        <div class="carousel-item" data-bs-interval="2000">
            <img src="img/acarousel.jpg" height="450px" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block rounded-pill d-sm-block" style="--bs-bg-opacity: .7">
            </div>
          </div>
        <div class="carousel-item" data-bs-interval="2000">
        <img src="img/D.jpg" height="450px" class="d-block w-100" alt="...">
        <div class="carousel-caption d-none d-md-block rounded-pill d-sm-block" style="--bs-bg-opacity: .7">
        </div>
      </div>
      <div class="carousel-item" data-bs-interval="2000">
        <img src="img/E.jpg" height="450px" class="d-block w-100" alt="...">
        <div class="carousel-caption d-none d-md-block rounded-pill d-sm-block" style="--bs-bg-opacity: .7">
        </div>
          </div>
          </div>
        </div>
          </div>
            </div>
        <!--Fin Del Carousel :3-->
        <!--Incio de Tarjetas-->
      <div class="row" id="servicios">
        <div class="col-lg-3 mx-auto col-sm-4 mt-5 position-relative">
        <div class="card bg-info-subtle bg-opacity-50 mb-3 mx-auto p-2" style="max-width: 22rem;">
          <div class="text-center fs-4 text-success">SALA CUNA</div>
          <div class="card-body">
            <img src="img/bebita.png" class="mb-3 col-4 rounded mx-auto d-block " data-bs-toggle="modal" data-bs-target="#modal2" alt="...">
            <h5 class="card-title text-center text-danger fs-4">DE 1 A 2 AÑOS</h5>
          </div>
        </div>
          </div>
        <div class="col-lg-3 mx-auto col-sm-4 mt-5 position-relative">
        <div class="card bg-danger-subtle bg-opacity-50 mb-3 mx-auto p-2" style="max-width: 22rem;">
          <div class="text-center fs-4 text-primary">PARVULOS</div>
          <div class="card-body">
            <img src="img/bloques.png" class="mb-3 col-4 rounded mx-auto d-block "data-bs-toggle="modal" data-bs-target="#modal3" alt="...">
            <h5 class="card-title text-center text-success fs-4">DE 2 A 3 AÑOS</h5>
          </div>
            </div>
          </div>
          <div class="col-lg-3 mx-auto col-sm-4 mt-5 position-relative">
            <div class="card bg-success-subtle bg-opacity-75 mb-3 mx-auto p-2" style="max-width: 22rem;">
              <div class="text-center fs-4 text-danger">PREJARDIN</div>
              <div class="card-body">
                <img src="img/jugando.png" class="mb-3 col-4 rounded mx-auto d-block "data-bs-toggle="modal" data-bs-target="#modal4" alt="...">
                <h5 class="card-title text-center text-primary fs-4">DE 3 A 4 AÑOS</h5>
              </div>
            </div>
              </div>
        </div>
        <!--Fin de Tarjetas-->
                <!--Incio de Tarjetas-->
        <div class="row">
        <div class="col-lg-3 mx-auto col-sm-4 mt-5 position-relative">
        <div class="card bg-warning-subtle bg-opacity-50 mb-3 mx-auto p-2" style="max-width: 22rem;">
          <div class="text-center fs-4 text-success">JARDIN</div>
          <div class="card-body">
            <img src="img/Jugar.png" class="mb-3 col-4 rounded mx-auto d-block " data-bs-toggle="modal" data-bs-target="#modal2" alt="...">
            <h5 class="card-title text-center text-danger fs-4">DE 4 A 5 AÑOS</h5>
          </div>
        </div>
          </div>
        <div class="col-lg-3 mx-auto col-sm-4 mt-5 position-relative">
        <div class="card bg-primary-subtle bg-opacity-50 mb-3 mx-auto p-2" style="max-width: 22rem;">
          <div class="text-center fs-4 text-primary">TRANSICION</div>
          <div class="card-body">
            <img src="img/confianza.png" class="mb-3 col-4 rounded mx-auto d-block "data-bs-toggle="modal" data-bs-target="#modal3" alt="...">
            <h5 class="card-title text-center text-success fs-4">DE 5 A 6 AÑOS</h5>
          </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-6 col-sm-12 mt-5">
            <img src="IMG/fondomensaje.png" class="col-lg-12 col-sm-8 img-fluid mx-auto d-block" alt="">
            </div>

            <div class="col-lg-6 col-sm-12">
                <h2 class="textobienvenidos fs-2">BIENVENIDOS</h2>
                <h3 class="textojardin fs-3">JARDIN INFANTIL MUNDO ACUARELA</h3>
                <p class="mensaje fs-5">En el jardín infantil Mundo Acuarela, ofrecemos un programa educativo basado en el juego,
                    la exploración y la creatividad. Nuestro objetivo es que cada niño se sienta feliz, seguro y valorado, y que
                    pueda desarrollar su potencial al máximo. Contamos con un equipo de profesionales cualificados y comprometidos, 
                    que brindan una atención personalizada y afectuosa a cada niño. Además, tenemos unas instalaciones modernas y equipadas,
                    con espacios amplios y luminosos, donde los niños pueden disfrutar de diversas actividades lúdicas y educativas.
                    En el jardín infantil Mundo Acuarela, respetamos el ritmo y el estilo de aprendizaje de cada niño, y fomentamos
                    su autonomía, su curiosidad y su confianza.
                </p>
            </div>
        </div>
        <!--Fin de Tarjetas-->
        <div class="row" id="escuela">
          <div class="col-12 ">
            <div class="bg-info bg-opacity-25">
            <h2 class="mt-4 text-center text-primary" style="font-family: chewy;">25 años educando con amor y calidad</h2>
            <h3 class="text-center text-danger" style="font-family: handlee;">Información Adicional</h3>
            </div>
          </div>
        </div>
        <div class="row">
        <div class="col-lg-3 col-sm-12">
        <p class="fs-3 mt-5 col-12 text-center pb-5"><a href="" style="font-family: handlee;" class="text-primary link-offset-2 link-underline link-underline-opacity-0">Metodo de aprendizaje</a></p>
        <p class="fs-3 mt-5 col-12 text-center pb-5"><a href="" style="font-family: handlee;" class="text-success link-offset-2 link-underline link-underline-opacity-0">Estimulacion Adecuada</a></p>
        <p class="fs-3 mt-5 col-12 text-center pb-5"><a href="" style="font-family: handlee;" class="text-danger link-offset-2 link-underline link-underline-opacity-0">Terapia ocupacional</a></p>
        </div>
        <div class="col-lg-6 col-sm-10 mx-auto">
        <img src="IMG/ninos-y-ninas-jugando-en-la-hierba.jpg" alt="" class="col-12 rounded ">
        </div>
        <div class="col-lg-3 col-sm-12">
        <p class="fs-3 mt-5 col-12 text-center pb-5"><a href="" style="font-family: handlee;" class="text-warning  link-offset-2 link-underline link-underline-opacity-0">Psicologia</a></p>
        <p class="fs-3 mt-5 col-12 text-center pb-5"><a href="" style="font-family: handlee;" class="text-dark link-offset-2 link-underline link-underline-opacity-0">Contenidos tematicos</a></p>
        <p class="fs-3 mt-5 col-12 text-center pb-5"><a href="" style="font-family: handlee;" class="text-success link-offset-2 link-underline link-underline-opacity-0">Atención emocinal</a></p>
        </div> 
        </div>

         <div class="row" id="galeria">
          <div class="col-lg-4 col-sm-8 mx-auto"> <img src="IMG/imagen11.jpg" class="imagen img-fluid  rounded" alt=""></div>
          <div class="col-lg-4 col-sm-8 mx-auto">  <img src="IMG/galeria.jpg" class="imagen img-fluid rounded" alt=""></div>
          <div class="col-lg-4 col-sm-8 mx-auto"> <img src="IMG/imagen13.jpg" class="imagen img-fluid  rounded" alt=""></div>
          <div class="col-lg-4 col-sm-8 mx-auto"> <img src="IMG/imagen21.jpg" class="imagen img-fluid  rounded" alt=""></div>
          <div class="col-lg-4 col-sm-8 mx-auto">  <img src="IMG/imagen22.jpg" class="imagen img-fluid rounded" alt=""></div>
          <div class="col-lg-4 col-sm-8 mx-auto"> <img src="IMG/imagen23.jpg" class="imagen img-fluid  rounded" alt=""></div>
          <div class="col-lg-4 col-sm-8 mx-auto"> <img src="IMG/imagen31.jpg" class="imagen img-fluid  rounded" alt=""></div>
          <div class="col-lg-4 col-sm-8 mx-auto">  <img src="IMG/imagen32.jpg" class="imagen img-fluid rounded" alt=""></div>
          <div class="col-lg-4 col-sm-8 mx-auto"> <img src="IMG/imagen33.jpg" class="imagen img-fluid  rounded" alt=""></div>
         </div>
         <div class="row">
          <div class="col-12"><h2 class="matriculas col-12">MATRICULAS ABIERTAS AÑO LECTIVO</h2>
           <b><p class="parrafomatricula col-12">Agradecemos la oportunidad y confianza de inscribir a sus hijos
                para ayudarlos a su desarrollo y brindarles nuestro conocimiento. Aprendemos de la manera
                mas divertida y educativa brindando un desarrollo de calidad
            </p></b> </div>
            <div class="col-lg-3 col-sm-6"> <img src="IMG/banner-ministerio-de-cultura.png" class="imagenx img-fluid  rounded" alt=""></div>
            <div class="col-lg-3 col-sm-6"> <img src="IMG/MINTIC (1).png" class="imagenx img-fluid  rounded" alt=""></div>
            <div class="col-lg-3 col-sm-6">  <img src="IMG/MINSALUD.png" class="imagenx img-fluid rounded" alt=""></div>
            <div class="col-lg-3 col-sm-6"> <img src="IMG/educacion.png" class="imagenx img-fluid  rounded" alt=""></div>
          </div>
          <div class="row mt-5 bg-primary bg-opacity-75 rounded-5" id="contactenos">
            <div class="col-lg-3 col-sm-12"><img src="IMG/cuadro.png" class="img-fluid col-8" alt="">         
            <p class="fs-2 text-info text-center texto">JARDIN INFANTIL <b>MUNDO ACUARELA</b></p>
            </div>
            <div class="col-lg-3 col-sm-12"><h2 class="text-center fs-1 text-info">Contáctenos</h2>
            <br>
            <p class="text-center text-light fs-5">Cel. 3212600725-3166449738
                <br>
            <br>Fijo.12345678
            <br>
            <br>En algún lugar del Mundo
            <br>
            <br>Horario:Lunes a Viernes <br>6am - 12pm</p></div>
            <div class="col-lg-3 col-sm-12 servers mt-5"><ul>
                <li><a class="link-underline link-underline-opacity-0 fs-4 text-light " href="">Matriculas</a></li>
                <br>
                <li><a class="link-underline link-underline-opacity-0 fs-4 text-light " href="">Escuela Familiar</a></li>
                <br>
                <li><a class="link-underline link-underline-opacity-0 fs-4 text-light " href="">Galeria</a></li>
                <br>
                <li><a class="link-underline link-underline-opacity-0 fs-4 text-light " href="">Contactos</a></li>
            </ul></div>
            <div class="col-lg-3 col-sm-12 mx-auto redesfooter">            
              <ul class="col-12">
                <li class=" col-12 f">
                    <span class="iconf"></span>
                    <span class="titulo">FACEBOOK</span>
                </li>
                <li class=" col-12 t">
                    <span class="icont"></span>
                    <span class="titulo">TWITTER</span>
                </li>
                <li class=" col-12 w">
                    <span class="iconw"></span>
                    <span class="titulo">WHATSAPP</span>
                </li >
                <li class=" col-12 i">
                    <span class="iconi"></span>
                    <span class="titulo">INSTAGRAM</span>
                </li>
            </ul>
        </div>
          </div>

          </div>
          <div class="row">
          <div class="col-12 fin">
            <p class="text-center fs-3 text-light mt-3">2023 Derechos reservados Jardín Infantil Mundo Acuarela Politica de Privacidad ° Términos y Condiciones</p>
        </div>
          </div>
          <script src="js/bootstrap.js"></script>
        </body>
        </html>