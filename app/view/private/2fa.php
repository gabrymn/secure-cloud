<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attiva/Disattiva 2FA</title>
    <style>
        /* Stile per il pulsante di tipo interruttore */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #4CAF50;
        }

        input:checked + .toggle-slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Stile per il testo di stato */
        #status {
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <h1>Attivazione/Disattivazione 2FA</h1>

    <label class="toggle-switch">
        <input type="checkbox" id="toggleSwitch" onclick="toggle2FA()">
        <span class="toggle-slider"></span>
    </label>

    <p id="status">2FA Disattivata</p>

    <!-- Script jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        // Variabile di stato per tenere traccia dell'attivazione/disattivazione
        var is2FAEnabled = false;

        function toggle2FA() {
            // Cambia lo stato
            is2FAEnabled = !is2FAEnabled;

            // Aggiorna il testo di stato
            var statusText = $('#status');
            statusText.text(is2FAEnabled ? '2FA Attivata' : '2FA Disattivata');

            // Puoi inserire qui la logica per inviare una richiesta al server per attivare/disattivare effettivamente la 2FA.
            // Ad esempio, potresti effettuare una richiesta Ajax al tuo server per eseguire l'operazione.

            // Esempio di richiesta Ajax (da personalizzare in base alle tue esigenze):
            /*
            $.ajax({
                url: '/path-to-toggle-2fa', // Sostituisci con il percorso del tuo endpoint sul server
                method: 'POST',
                data: { enabled: is2FAEnabled },
                success: function(response) {
                    console.log(response);
                },
                error: function(error) {
                    console.error(error);
                }
            });
            */
        }
    </script>
</body>
</html>