document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('myButton');
  
    button.addEventListener('click', () => {
      // URL dello script PHP sul tuo server
      const url = 'http://localhost:8080/api';
  
      // Esempio di richiesta GET utilizzando fetch
      fetch(url)
        .then(response => {
          if (!response.ok) {
            throw new Error('Errore nella richiesta HTTP ' + response.status);
          }
          return response.text(); // Otteniamo il corpo della risposta come testo
        })
        .then(data => {
          console.log('Risposta dal server PHP:', data); // Visualizza la risposta del server PHP
          // Puoi fare qualcosa con la risposta qui, come mostrare i dati nella pagina
          alert('Risposta dal server PHP: ' + data);
        })
        .catch(error => {
          console.error('Si è verificato un errore:', error); // Gestisce gli eventuali errori
          // Puoi gestire l'errore qui, come mostrare un messaggio di errore all'utente
          alert('Si è verificato un errore: ' + error.message);
        });
    });
  });