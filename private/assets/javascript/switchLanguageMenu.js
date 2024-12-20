const translations = {
    "Vote on Poll": "Voter au Sondage",
    "My Dashboard": "Mon Tableau de Bord",
    "View Calendar": "Voir le Calendrier",
    "Vote on Poll": "Voter au Sondage",
    "Request Office Hours": "Demander Heures",
    "Request Equipment": "Demand Équipement",
    "Log Out": "Se Déconnecter",
    "FR": "EN",
    "Manage Bookings": "Gérer ma réservation",
    "Create Poll": "créer un sondage",
    "View Poll": "voir le sondage",
  };
  
  function translatePageToFrench() {
  
    document.querySelector('.link-dashboard').textContent = "🏠 " + translations["My Dashboard"];
    document.querySelector('.link-calendar').textContent = "🗓 " + translations["View Calendar"];
    document.querySelector('.menu-language').textContent = translations["FR"];

    if(document.querySelector('.link-poll')){
      document.querySelector('.link-poll').textContent = "📊 " + translations["Vote on Poll"];
    }
    if (document.querySelector('.link-office-hours')){
    document.querySelector('.link-office-hours').textContent = "📅 " + translations["Request Office Hours"];
    }
    if (document.querySelector('.link-equipment')){
    document.querySelector('.link-equipment').textContent = "💻 " + translations["Request Equipment"];
    }
    if (document.querySelector('.link-logout')){
      document.querySelector('.link-logout').textContent = "🔒 " + translations["Log Out"];
    }
    if(document.querySelector('.link-create-poll')){
      document.querySelector('.link-create-poll').textContent = "📊 " + translations["Create Poll"];
    }
    if(document.querySelector('.link-manage')){
      document.querySelector('.link-manage').textContent = "⚙ " + translations["Manage Bookings"];
    }
    if(document.querySelector('.link-view-poll')){
      document.querySelector('.link-view-poll').textContent = "📊 " + translations["View Poll"];
    }

  
  
    localStorage.setItem('language', 'french');
  }
  
  // Function to translate page back to English
  function translatePageToEnglish() {
    location.reload(); // Reload the page to reset to English
    localStorage.setItem('language', 'english');
  }
  
  // Check Local Storage for Language Preference
  function loadLanguagePreference() {
    const language = localStorage.getItem('language');
    if (language === 'french') {
      translatePageToFrench();
    }
  }
  
  document.querySelector('.menu-language').addEventListener('click', (e) => {
    e.preventDefault();
    const currentLanguage = e.target.textContent.trim();
    if (currentLanguage === "FR") {
      translatePageToFrench();
    } else {
      translatePageToEnglish();
    }
  });

  loadLanguagePreference();
  