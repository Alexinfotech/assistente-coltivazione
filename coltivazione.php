<?php
// Quando la pagina viene caricata per la prima volta (GET request), salva i dati di $_SERVER
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $serverData = "----------------------\n";
    $serverData .= "New User Access: " . date('Y-m-d H:i:s') . "\n";
    foreach ($_SERVER as $key => $value) {
        $serverData .= "$key: $value\n";
    }
    $serverData .= "----------------------\n";

    $filename = "chat_logs.txt";
    file_put_contents($filename, $serverData, FILE_APPEND);
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consigli di Coltivazione by Alex</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        @media (max-width: 600px) {
            body {
                padding: 5px;
            }

            .chat-container {
                width: 100%;
            }

            select,
            input[type="text"],
            button {
                font-size: 14px;
                padding: 8px;
            }
        }

        select,
        input[type="text"],
        button {
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            width: 100%;
            font-size: 16px;
        }

        #vegetableInput {
            background-color: green;
            color: yellow;
            width: 50%;
        }

        #soilType {
            background-color: brown;
            color: white;
        }

        button {
            background-color: #0072ff;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        #descriptionBox,
        #response {
            border: 1px solid #8a4b08;
            background-color: #d2a679;
            width: 300px;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            margin-top: 10px;
            display: none;
            position: relative;
            min-height: 50px;
            padding: 15px;
            font-size: 1.1em;
            color: black;
        }

        #messageBox {
            color: red;
            margin-top: 10px;
            display: none;
        }

        .chat-container {
            border: 1px solid #8a4b08;
            border-radius: 10px;
            background-color: white;
            padding: 10px;
            overflow-y: auto;
            height: 400px;
            margin-bottom: 10px;
        }

        .chat-message {
            padding: 8px 12px;
            border-radius: 10px;
            margin-bottom: 10px;
            max-width: 70%;
            display: block;
        }

        .user-message {
            background-color: #DCF8C6;
            margin-left: auto;
            margin-right: 0;
            text-align: right;
        }

        .bot-message {
            background-color: #ECE5DD;
            margin-left: 0;
            margin-right: auto;
        }

        .input-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .input-container label {
            margin-right: 10px;
        }

        .input-container button {
            width: 95px;
            padding: 5px;
        }

        button.terminate-chat {
            background-color: red;
            color: white;
            width: auto;
            padding: 10px 15px;
        }

        button.terminate-chat:hover {
            background-color: darkred;
        }

        .soil-description {
            background-color: #8a4b08;
        }

        .assistant-response {
            background-color: #ADD8E6;
        }
    </style>
</head>

<body>
    <h1 class="animate__animated animate__fadeInUp">Consigli di Coltivazione by Alex</h1>
    <h3 class="animate__animated animate__fadeInUp animate__delay-1s">Servizio Api OpenAI</h3>

    <label for="soilType" class="animate__animated animate__fadeInUp animate__delay-2s">Tipo di Terreno:</label>
    <select id="soilType" onchange="showDescription()" class="animate__animated animate__fadeInUp animate__delay-2s">
        <option value="indefinito" selected>Indefinito</option> <option value="argilloso">Argilloso</option>
        <option value="sabbioso">Sabbioso</option>
        <option value="limoso">Limoso</option>
        <option value="torboso">Torboso</option>
        <option value="roccioso">Roccioso o Ghiaioso</option>
        <option value="calcareo">Calcareo</option>
        <option value="siliceo">Siliceo</option>
        <option value="lateritico">Lateritico</option>
        <option value="salino">Salino</option>
        <option value="alluvionale">Alluvionale</option>
        <option value="loam">Loam o Franco</option>
        <option value="podzolico">Podzolico</option>
        <option value="vulcanico">Terreno vulcanico</option>
        </select>
    <div id="descriptionBox" class="animate__animated animate__fadeInUp animate__delay-3s"></div>

    <div class="chat-container" id="chatBox">
    <div class="chat-message bot-message assistant-response">
    Ciao! Sono il tuo assistente di coltivazione in fase di addestramento. Seleziona il tipo di terreno, leggi la didascalia e Scrivi il tipo di vegetale per cui vuoi consigli!
</div>
</div>

    </div>
    
    <div class="input-container animate__animated animate__fadeInUp animate__delay-4s">
        <input type="text" id="inputBox" placeholder="Inserisci il tipo di vegetale" onkeypress="checkEnter(event)" style="width: 80%;">
        <button onclick="processInput()" class="animate__animated animate__fadeInUp animate__delay-5s">Invia</button>
    </div>
    
    <!-- ... Altri elementi ... -->

    <div class="input-container animate__animated animate__fadeInUp animate__delay-7s">
        <button onclick="closeChat()" class="terminate-chat animate__animated animate__fadeInUp animate__delay-7s">Termina Chat</button>
    </div>
    <div id="messageBox"></div>

    <script src="apiRequestColtivazione.js"></script>
    <script>
        let inactivityTimer;

        const vegetaliAccettati = [
    "carota", "pomodoro", "lattuga", "cavolo", "peperone", "melanzana", "cipolla", 
    "aglio", "barbabietola", "broccolo", "bruxelles", "cavolfiore", "cetriolo", 
    "chicoria", "coriandolo", "fagiolino", "fagiolo", "finocchio", "indivia", 
    "mais", "patata", "pisello", "porro", "radicchio", "ravanello", "rosmarino", 
    "rucola", "sedano", "spinacio", "taccole", "zucca", "zucchina", "asparago", 
    "basilico", "bietola", "cardo", "cavolo nero", "cavolo riccio", "cavolo romano", 
    "cavolo rosso", "cavolo verza", "cipolla rossa", "cipollotto", "erba cipollina", 
    "menta", "origano", "prezzemolo", "salvia", "timo", "valerianella", "verza",
    "achillea", "agretti", "alchechengi", "amaranto", "aneto", "angelica", "anice", 
    "borragine", "calendula", "camomilla", "cannella", "capperi", "carciofo", 
    "cardamomo", "cicoria", "cipolla bianca", "cipolla di tropea", "coriandolo", 
    "crescione", "curcuma", "dragoncello", "endivia", "erba medica", "finocchietto", 
    "fragola", "lampascioni", "lavanda", "limone", "maggiorana", "malva", "melissa", 
    "mirtillo", "nasturzio", "noci", "nocciola", "origano selvatico", "ortica", 
    "papavero", "pepe", "pepe di cayenna", "pepe nero", "peperoncino", "peperoncino rosso", 
    "peperoncino verde", "pimpinella", "piselli mangiatutto", "pomodoro ciliegino", 
    "pomodoro san marzano", "pomodoro datterino", "portulaca", "ramolaccio", "rapa", 
    "ravanello nero", "ravanello daikon", "rosmarino selvatico", "rucola selvatica", 
    "salvia rossa", "santoreggia", "senape", "tarassaco", "tartufo", "timo limone", 
    "topinambur", "vaniglia", "zenzero","agretti", "alchechengi", "amaranto", "aneto",
     "angelica", "anice", "borragine", "calendula", "camomilla", "cannella", "capperi", "carciofo",
      "cardamomo", "cicoria", "cipolla bianca", "cipolla di tropea", "coriandolo", "crescione", "curcuma", 
      "dragoncello", "endivia", "erba medica", "finocchietto", "fragola", "lampascioni", "lavanda", "limone", 
      "maggiorana", "malva", "melissa", "mirtillo", "nasturzio", "noci", "nocciola", "origano selvatico", "ortica", 
      "papavero", "pepe", "pepe di cayenna", "pepe nero", "peperoncino", "peperoncino rosso", "peperoncino verde", "pimpinella", 
      "piselli mangiatutto", "pomodoro ciliegino", "pomodoro san marzano", "pomodoro datterino", "portulaca", "ramolaccio", "rapa", 
      "ravanello nero", "ravanello daikon", "rosmarino selvatico", "rucola selvatica", "salvia rossa", "santoreggia", "senape", 
      "tarassaco", "tartufo", "timo limone", "topinambur", "vaniglia", "zenzero","aglio", "aglio orsino", "basilico", "bietola",
       "broccolo", "cavolfiore", "cavolo nero", "cavolo riccio", "cavolo romano", "cavolo verza", "cetriolo", "chicco di grano", 
       "chiodi di garofano", "cioccolato", "cipolla rossa", "coriandolo", "cumino", "dill", "erba cipollina", "fagiolini", "fava", 
       "fave di cacao", "finocchio", "fragola di bosco", "germogli di soia", "ginseng", "insalata iceberg", "insalata romana", "kiwi",
        "lenticchie", "luppolo", "mango", "melone", "menta", "menta piperita", "menta verde", "mirtilli rossi", "mirtilli neri", "more", 
        "noccioline", "noce moscata", "olive", "origano", "panna", "papaya", "patata", "patata dolce", "pepe bianco", "pepe verde", 
        "peperone giallo", "peperone rosso", "peperone verde", "prezzemolo", "radicchio", "radice di liquirizia", "ravanelli", "rosmarino", 
        "rucola", "salvia", "sedano", "sedano rapa", "semi di chia", "semi di lino", "semi di papavero", "semi di sesamo", "semi di zucca",
         "senape nera", "soia", "spinaci", "timo", "valeriana", "verza", "zafferano", "zucchero", "zucchina", "zucca","piselli", "piselli spezzati",
          "porro", "rapa", "ravanello da foraggio", "ribes", "ribes nero", "ribes rosso", "rosmarino selvatico", "rucola selvatica", 
          "salvia romana", "scalogno", "scarola", "sedano di montagna", "semi di girasole", "semi di melograno", "semi di mostarda", 
          "semi di papavero nero", "semi di ravanello", "semi di senape", "semi di soia nera", "semi di soia verde", "semi di soia gialla", 
          "semi di soia rossa", "semi di tarassaco", "semi di tiglio", "semi di venere", "semi di verbasco", "semi di veronica", "semi di vite",
           "semi di zucca nera", "semi di zucca verde", "semi di zucca gialla", "semi di zucca rossa", "semi di zucca bianca",
            "semi di zucca a strisce", "semi di zucca maculata", "semi di zucca moscata", "semi di zucca ornamentale", "semi di zucca peruviana", 
            "semi di zucca selvatica", "semi di zucca tropicale", "semi di zucca zuccherina", "semi di zucca zucca", "semi di zucca zucca zucca",
             "semi di zucca zucca zucca zucca", "semi di zucca zucca zucca zucca zucca", "semi di zucca zucca zucca zucca zucca zucca", 
             "semi di zucca zucca zucca zucca zucca zucca zucca", "continua","semi di zucca zucca zucca zucca zucca zucca zucca zucca"



];

        function showDescription() {
            resetInactivityTimer();

            const descriptions = {
                "argilloso": "Composto principalmente da particelle di argilla. È un terreno pesante, freddo, umido e compatto. Tende a trattenere l'acqua, rendendo difficile la sua drenaggio.",
                "sabbioso": "Composto principalmente da particelle di sabbia, è leggero, ben drenante e si riscalda rapidamente.",
                "limoso": "Ha una buona quantità di particelle di limo. È ben drenante, ma trattiene più umidità e nutrienti rispetto al terreno sabbioso.",
                "torboso":"Ricco di materia organica in decomposizione, è scuro, umido e leggero.",
                "roccioso":"Contiene molte rocce e ghiaia. Ha un drenaggio eccellente ma non trattiene bene l'acqua e i nutrienti.",
                "calcareo":"Contiene una grande quantità di calcare e ha un pH alcalino.",
                "siliceo":"È un terreno acido, leggero e ben drenante. È composto principalmente da particelle di sabbia e silice.",
                "lateritico":"È ricco di ossidi di ferro e alluminio. Si trova spesso nelle regioni tropicali.",
                "salino":"Ha un alto contenuto di sali. Può impedire l'assorbimento d'acqua da parte delle piante.",
                "alluvionale":"Si forma dai depositi lasciati dalle acque fluviali e può essere molto fertile.",
                "loam":"È considerato il terreno ideale per la coltivazione perché combina le migliori caratteristiche dei terreni argilloso, sabbioso e limoso.",
                "podzolico":"Si trova spesso nelle foreste di conifere e ha uno strato superiore acido e sabbioso e uno strato inferiore argilloso.",
                "vulcanico":"Ricco di minerali e molto fertile, si trova nelle regioni con attività vulcanica.",
                "indefinito": "Per favore, scegli un tipo di terreno dal menu a tendina per ottenere una descrizione."

            };
            const selectedSoil = document.getElementById('soilType').value;
            const description = descriptions[selectedSoil];
            document.getElementById('descriptionBox').textContent = descriptions[selectedSoil];
            //document.getElementById('descriptionBox').style.display = 'block';
             // Aggiungi la descrizione al riquadro della chat
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('chat-message', 'bot-message');
    messageDiv.textContent = description;
    messageDiv.classList.add('soil-description'); // Aggiungi questa linea
    document.getElementById('chatBox').appendChild(messageDiv);
    scrollChatToBottom();

        }

        function showSuggestions() {
    const inputValue = document.getElementById('vegetableInput').value.toLowerCase();
    const suggestionsBox = document.getElementById('suggestionsBox');
    
    const filteredVegetables = vegetaliAccettati.filter(v => v.startsWith(inputValue));

    // Se non ci sono suggerimenti o l'input è vuoto, nascondi il box dei suggerimenti.
    if (filteredVegetables.length === 0 || inputValue === "") {
        suggestionsBox.style.display = 'none';
        return;
    }

    suggestionsBox.style.display = 'block';
    suggestionsBox.innerHTML = filteredVegetables.map(veg => `<p onclick="selectVegetable('${veg}')">${veg}</p>`).join('');
}

function selectVegetable(vegetable) {
    document.getElementById('vegetableInput').value = vegetable;
    document.getElementById('suggestionsBox').style.display = 'none';
}
function processInput() {
    resetInactivityTimer();

    const inputVal = document.getElementById('inputBox').value.trim(); // Aggiunto .trim() per eliminare spazi bianchi iniziali e finali
    const selectedSoil = document.getElementById('soilType').value;

    // Controllo se il terreno è stato selezionato
    if (selectedSoil === "indefinito") {
        const botMessage = document.createElement('div');
        botMessage.classList.add('chat-message', 'bot-message', 'assistant-response');
        botMessage.textContent = "Per favore, scegli un tipo di terreno prima di inviare il tuo messaggio.";
        document.getElementById('chatBox').appendChild(botMessage);
        scrollChatToBottom();
        return; // Interrompe l'esecuzione della funzione
    }

    // Controllo se l'input dell'utente è vuoto o non valido
    if (inputVal === "" || !vegetaliAccettati.includes(inputVal.toLowerCase())) {
        const botMessage = document.createElement('div');
        botMessage.classList.add('chat-message', 'bot-message', 'assistant-response');
        botMessage.textContent = "Per favore, inserisci un tipo di vegetale valido.";
        document.getElementById('chatBox').appendChild(botMessage);
        scrollChatToBottom();
        return; // Interrompe l'esecuzione della funzione
    }

    // Mostra il messaggio dell'utente
    const userMessage = document.createElement('div');
    userMessage.classList.add('chat-message', 'user-message');
    userMessage.textContent = inputVal;
    document.getElementById('chatBox').appendChild(userMessage);

    // Mostra lo spinner
    const spinner = document.createElement('div');
    spinner.classList.add('chat-message', 'bot-message');
    spinner.innerHTML = 'Caricamento...';
    document.getElementById('chatBox').appendChild(spinner);
    
    fetchPlantInfo(inputVal)
        .then(data => {
            // Rimuove lo spinner
            document.getElementById('chatBox').removeChild(spinner);
                
            const botMessage = document.createElement('div');
            botMessage.classList.add('chat-message', 'bot-message', 'assistant-response');  // Qui abbiamo aggiunto la classe 'assistant-response'
            botMessage.textContent = data.choices[0].text.trim();
            document.getElementById('chatBox').appendChild(botMessage);
            scrollChatToBottom();
            
            // Salva il messaggio appena ricevuto nella chat
            saveChatMessage();
        })
        .catch(error => {
            // Rimuove lo spinner
            document.getElementById('chatBox').removeChild(spinner);

            const botMessage = document.createElement('div');
            botMessage.classList.add('chat-message', 'bot-message', 'assistant-response'); // Anche qui
            botMessage.textContent = "Mi dispiace, si è verificato un errore. Prova di nuovo.";
            document.getElementById('chatBox').appendChild(botMessage);
            scrollChatToBottom();
        });
    
    // Pulisci l'input
    document.getElementById('inputBox').value = '';
}


function checkEnter(e) {
    // Se l'utente preme "invio", elabora l'input
    if (e.keyCode === 13) {
        processInput();
    }
}

function scrollChatToBottom() {
    const chatBox = document.getElementById('chatBox');
    chatBox.scrollTop = chatBox.scrollHeight;
}
function handleResponse(response) {
    const sentences = response.split(".");
    let trimmedResponse = sentences.slice(0, -1).join(".") + ".";
    return trimmedResponse;
}
function saveChatData() {
    const chatBox = document.getElementById('chatBox');
    const chatData = Array.from(chatBox.children).map(div => div.textContent).join('\n');

    let postData = 'chatData=' + encodeURIComponent(chatData);
    if (window.userData) {
        postData += '&ipAddress=' + encodeURIComponent(window.userData.ipAddress);
        postData += '&userAgent=' + encodeURIComponent(window.userData.userAgent);
        // Dopo aver inviato i dati dell'utente una volta, cancellali
        delete window.userData;
    }

    fetch('chat.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: postData
    })
    .then(response => response.text())
    .then(data => console.log(data))
    .catch(error => console.error('Error:', error));
}


function closeChat() {
    // Aggiungi un messaggio di ringraziamento alla chat
    const thanksMessage = document.createElement('div');
    thanksMessage.classList.add('chat-message', 'bot-message');
    thanksMessage.textContent = "Grazie per aver utilizzato il nostro servizio di chat.";
    document.getElementById('chatBox').appendChild(thanksMessage);
    scrollChatToBottom();

    // Aspetta 5 secondi e poi ricarica la pagina
    setTimeout(() => {
        location.reload();
    }, 3000);
}

document.addEventListener("DOMContentLoaded", function() {
    const userData = {
        ipAddress: "<?php echo $_SERVER['REMOTE_ADDR']; ?>",
        userAgent: "<?php echo $_SERVER['HTTP_USER_AGENT']; ?>"
    };

    // Salva questi dati per l'uso successivo
    window.userData = userData;
});
function saveChatMessage() {
    const chatBox = document.getElementById('chatBox');
    const chatData = Array.from(chatBox.children).map(div => div.textContent).join('\n');
    
    const userData = {
        ipAddress: window.location.hostname,
        userAgent: navigator.userAgent
    };

    const formData = new FormData();
    formData.append('chatData', chatData);
    formData.append('ipAddress', userData.ipAddress);
    formData.append('userAgent', userData.userAgent);

    fetch('chat.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => console.log(data))
    .catch(error => console.error('Error:', error));
}
function resetInactivityTimer() {
    clearTimeout(inactivityTimer); // Cancella il timer precedente

    // Imposta un nuovo timer che chiuderà la chat dopo 5 minuti di inattività
    inactivityTimer = setTimeout(function() {
        showInactivityMessage();
        closeChat();
    }, 5 * 60 * 1000); // 5 minuti
}
function showInactivityMessage() {
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('chat-message', 'bot-message');
    messageDiv.textContent = "La chat verrà chiusa a causa di inattività.";
    document.getElementById('chatBox').appendChild(messageDiv);
    scrollChatToBottom();
}

    </script>

</body>

</html>


