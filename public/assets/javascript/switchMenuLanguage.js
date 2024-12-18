const translations = {
    "Home": "Accueil",
    "About": "À propos",
    "Help": "Aide",
    "Login": "Connexion",
    "Register": "Inscription",
    "FR": "EN",
}

  // Function to translate page text
  function translatePageToFrench() {
    document.querySelector('.home-link').textContent = translations["Home"];
    document.querySelector('.about-link').textContent = translations["About"];
    document.querySelector('.login-link').textContent = translations["Login"];
    document.querySelector('.register-link').textContent = translations["Register"];
    document.querySelector('.menu-home-link').textContent = translations["Home"];
    document.querySelector('.menu-about-link').textContent = translations["About"];
    document.querySelector('.menu-help-link').textContent = translations["Help"];


    // Change button text
    document.querySelector('.language').textContent = translations["FR"];
    document.querySelector('.menu-language').textContent = translations["FR"];

    localStorage.setItem('language', 'french');
  }

  // Function to translate page back to English
  function translatePageToEnglish() {
    location.reload(); // Reloads the page to reset to English
    localStorage.setItem('language', 'english');
  }

  // Check Local Storage for Language Preference
  function loadLanguagePreference() {
    const language = localStorage.getItem('language');
    if (language === 'french') {
      translatePageToFrench();
    }
  }

  // Event Listeners for Language Switch
  document.querySelector('.language').addEventListener('click', (e) => {
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
