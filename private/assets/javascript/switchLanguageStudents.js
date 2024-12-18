const translations = {
    "Vote on Poll": "Voter au Sondage",
    "My Dashboard": "Mon Tableau de Bord",
    "View Calendar": "Voir le Calendrier",
    "Vote on Poll": "Voter au Sondage",
    "Request Office Hours": "Demander Heures",
    "Request Equipment": "Demand Équipement",
    "Log Out": "Se Déconnecter",
    "FR": "EN",
  };
  
  // Function to translate page to French
  function translatePageToFrench() {
  
    // Translate links in the sidebar
    document.querySelector('.link-dashboard').textContent = "🏠 " + translations["My Dashboard"];
    document.querySelector('.link-calendar').textContent = "🗓 " + translations["View Calendar"];
    document.querySelector('.link-poll').textContent = "📊 " + translations["Vote on Poll"];
    document.querySelector('.link-office-hours').textContent = "📅 " + translations["Request Office Hours"];
    document.querySelector('.link-equipment').textContent = "💻 " + translations["Request Equipment"];
    document.querySelector('.link-logout').textContent = "🔒 " + translations["Log Out"];
    document.querySelector('.menu-language').textContent = translations["FR"];
  
  
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
  
  // Event Listeners for Language Switch
  document.querySelector('.menu-language').addEventListener('click', (e) => {
    e.preventDefault();
    const currentLanguage = e.target.textContent.trim();
    if (currentLanguage === "FR") {
      translatePageToFrench();
    } else {
      translatePageToEnglish();
    }
  });
  
  // Load language on page load
  loadLanguagePreference();
  