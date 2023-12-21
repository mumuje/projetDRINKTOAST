//////////////////////////CARTE JAUNE//////////////////////////

document.addEventListener('DOMContentLoaded', function() {
    var wrapJaune = document.querySelector('.wrapJaune');
    wrapJaune.addEventListener('click', function() {
        this.classList.toggle('active');
    });
});
//////////////////////////CARTE BLEU//////////////////////////

document.addEventListener('DOMContentLoaded', function() {
    var wrapJaune = document.querySelector('.wrap');
    wrapJaune.addEventListener('click', function() {
        this.classList.toggle('active');
    });
});
//////////////////////////CARTE ROUGE//////////////////////////

document.addEventListener('DOMContentLoaded', function() {
    var wrapJaune = document.querySelector('.wrapRouge');
    wrapJaune.addEventListener('click', function() {
        this.classList.toggle('active');
    });
});

//////////////////////////CARTE VERTE//////////////////////////

document.addEventListener('DOMContentLoaded', function() {
    var wrapJaune = document.querySelector('.wrapVerte');
    wrapJaune.addEventListener('click', function() {
        this.classList.toggle('active');
    });
})


//////////////////////////CARTE MULTICOLORE//////////////////////////

document.addEventListener('DOMContentLoaded', function() {
    var wrapJaune = document.querySelector('.wrapMulticolore');
    wrapJaune.addEventListener('click', function() {
        this.classList.toggle('active');
    });
});


//////////////////////////CARTE VIOLETTE//////////////////////////

document.addEventListener('DOMContentLoaded', function() {
    var wrapJaune = document.querySelector('.wrapViolette');
    wrapJaune.addEventListener('click', function() {
        this.classList.toggle('active');
    });
});











$(document).ready(function(){
    $(".navbar-toggler").click(function(){
      if ($("#navbarContent").hasClass("hidden")) {
        $("#navbarContent").removeClass("hidden").addClass("slide-in");
      } else {
        $("#navbarContent").addClass("hidden").removeClass("slide-in");
      }
    });
  });
  