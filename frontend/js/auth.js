window.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('login-form');
  const registerForm = document.getElementById('register-form');
  if (loginForm) {
    loginForm.addEventListener('submit', (e) => {
      e.preventDefault(); // Empêche la soumission standard du formulaire
      const formData = new FormData(loginForm); // Prépare les données du formulaire pour l'envoi
      fetch('backend/login.php', { // Modifiez avec le chemin approprié si nécessaire
        method: 'POST',
        body: formData
      })
        .then((response) => response.json()) // Transforme la réponse du serveur en JSON
        .then((data) => {
          if (data.success) {
            window.location.href = 'homepage.php'; // Redirection en cas de connexion réussie
          } else {
            // eslint-disable-next-line no-alert
            alert(data.message); // Affiche un message d'erreur en cas d'échec
          }
        })
        .catch((error) => {
          console.error('Error:', error); // Affiche les erreurs de requête dans la console
        });
    });
  }
  if (registerForm) {
    registerForm.addEventListener('submit', (e) => {
      e.preventDefault(); // Empêche la soumission standard du formulaire
      const formData = new FormData(registerForm); // Prépare les données du formulaire pour l'envoi
      fetch('backend/register.php', { // Modifiez avec le chemin approprié si nécessaire
        method: 'POST',
        body: formData
      })
        .then((response) => response.json()) // Transforme la réponse du serveur en JSON
        .then((data) => {
          if (data.success) {
            window.location.href = 'login.php'; // Redirection vers la page de connexion
          } else {
            // eslint-disable-next-line no-alert
            alert(data.message); // Affiche un message d'erreur en cas d'échec
          }
        })
        .catch((error) => {
          console.error('Error:', error); // Affiche les erreurs de requête dans la console
        });
    });
  }
});
