document.addEventListener('DOMContentLoaded', function() {
  var botones = document.querySelectorAll(".boton_js");
  var contenidos = document.querySelectorAll(".contenido");
  var contenidoPrincipal = document.querySelector(".contenidos.principal");

  function mostrarContenido(contenido) {
    contenidos.forEach((element) => {
      if (element === contenido) {
        element.style.display = "block";
      } else {
        element.style.display = "none";
      }
    });
    if (contenido !== contenidoPrincipal) {
      contenidoPrincipal.style.display = "none";
    } else {
      contenidoPrincipal.style.display = "block";
    }
  }

  botones.forEach((boton, index) => {
    boton.addEventListener("click", function() {
      if (index < contenidos.length) {
        mostrarContenido(contenidos[index]);
      } else {
        mostrarContenido(contenidoPrincipal);
      }
    });
  });

  // Handle click events in offcanvas buttons
  var offcanvas = document.getElementById('offcanvasResponsive');
  offcanvas.addEventListener('shown.bs.offcanvas', function () {
    var botonesOffcanvas = offcanvas.querySelectorAll(".boton_js");
    botonesOffcanvas.forEach((boton, index) => {
      boton.addEventListener("click", function() {
        if (index < contenidos.length) {
          mostrarContenido(contenidos[index]);
        } else {
          mostrarContenido(contenidoPrincipal);
        }
      });
    });
  });

  //  contenido principal se muestre inicialmente
  mostrarContenido(contenidoPrincipal);

  var reset = document.getElementById('reset_b');
  reset.addEventListener("click",function(){
    mostrarContenido(contenidoPrincipal);

  });

});


//  abrirr modal perfil del profesor , 

$(document).ready(function() {
  window.abrir_mdl_p = function() {
      var modalElement = document.getElementById('modal1');
      var modal = new bootstrap.Modal(modalElement);
      modal.show();
  }

  if (localStorage.getItem("abrir_modal") === "true") {
    abrir_mdl_p();
   
    localStorage.removeItem("abrir_modal");
}
});


