
(function () {
    'use to notification';
    // pour cette fonctionnalité IP_SERVER = 127.0.0.1 qui doit être revu lors du déployement
    var _receiver = document.getElementById('ws-content-receiver');
    var ws = new webSocket("ws://IP_SERVER:8080");
    ws.onopen = function() {
        ws.send('Notification');
        _receiver.innerHTML = 'Connecté !';
    };
    ws.onmessage = function (event) {
        _receiver.innerHTML = event.data;
    };
    ws.onclose = function () {
        _receiver.innerHTML = 'Connection fermée';
    };
    ws.onerror = function () {
        _receiver.innerHTML = 'Une error trouvée !!!';
    };
})();