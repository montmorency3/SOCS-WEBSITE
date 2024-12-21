//DOMinatrix- Alex
const translations = {
    "Home": "Accueil",
    "About": "À propos",
    "Help": "Aide",
    "Login": "Connexion",
    "Register": "Inscription",
    "FR": "EN",
    "Welcome to the SOCS Registration!": "Bienvenue sur l'inscription SOCS!",
    "About Us": "À propos de Nous",
    "welcome": "Bienvenue sur SOCS, une plateforme dédiée à la simplification et à l'amélioration des processus d'inscription des étudiants. Notre mission est de fournir une expérience utilisateur transparente et de garantir un accès facile aux ressources pédagogiques.",
    "Our team" : "Notre équipe est composée de développeurs et d'éducateurs passionnés et engagés dans l'innovation, la convivialité et la qualité. Si vous avez des questions ou avez besoin d'aide, veuillez nous contacter via notre section Aide."
}

  function translatePageToFrench() {
    document.querySelector('.home-link').textContent = translations["Home"];
    document.querySelector('.about-link').textContent = translations["About"];
    document.querySelector('.login-link').textContent = translations["Login"];
    document.querySelector('.register-link').textContent = translations["Register"];
    document.querySelector('.menu-home-link').textContent = translations["Home"];
    document.querySelector('.menu-about-link').textContent = translations["About"];

    if (document.querySelector('.menu-help-link')){
      document.querySelector('.menu-help-link').textContent = translations["Help"];
    }

    if ( document.querySelector('.welcome')){
      document.querySelector('.welcome').textContent = translations["Welcome to the SOCS Registration!"];
    }
    if ( document.querySelector('.about')){
      document.querySelector('.about').textContent = translations["About Us"];
    }
    if ( document.querySelector('.firstP')){
      document.querySelector('.firstP').textContent = translations["welcome"];
    }
    if ( document.querySelector('.secondP')){
      document.querySelector('.secondP').textContent = translations["Our team"];
    }

    // Change button text
    document.querySelector('.language').textContent = translations["FR"];
    document.querySelector('.menu-language').textContent = translations["FR"];

    localStorage.setItem('language', 'french');
  }


  function translatePageToEnglish() {
    location.reload(); 
    localStorage.setItem('language', 'english');
  }

  function loadLanguagePreference() {
    const language = localStorage.getItem('language');
    if (language === 'french') {
      translatePageToFrench();
    }
  }

  document.querySelector('.language').addEventListener('click', (e) => {
    e.preventDefault();
    const currentLanguage = e.target.textContent;
    if (currentLanguage === "FR") {
      translatePageToFrench();
    } else {
      translatePageToEnglish();
    }
  });

  document.querySelector('.menu-language').addEventListener('click', (e) => {
    e.preventDefault();
    const currentLanguage = e.target.textContent;
    if (currentLanguage === "FR") {
      translatePageToFrench();
    } else {
      translatePageToEnglish();
    }
  });


  // Load language on page load
  loadLanguagePreference();
