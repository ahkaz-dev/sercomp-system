<style>
.alert-toast {
    position: fixed;
    top: -100px; /* начальное положение - выше экрана */
    right: 20px;
    max-width: 90vw;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    z-index: 1050;
    color: #fff; /* по умолчанию белый текст */
    background-color: #333;

    opacity: 0;
    transition: top 0.6s ease, opacity 0.6s ease;
}

/* Типы сообщений */
.alert-toast.success {
    background-color: #28a745;
    color: #fff; /* явно белый */
}
.alert-toast.error {
    background-color: #dc3545;
    color: #fff; /* явно белый */
}
.alert-toast.warning {
    background-color: #ffc107;
    color: #fff; /* явно белый */
}

/* Dark Theme Support */
body.dark-theme .alert-toast {
    background-color: #1e1e1e;
    color: #f1f1f1; /* светлый цвет текста */
}
body.dark-theme .alert-toast.success {
    background-color: #198754;
    color: #f1f1f1;
}
body.dark-theme .alert-toast.error {
    background-color: #b02a37;
    color: #f1f1f1;
}
body.dark-theme .alert-toast.warning {
    background-color: #e0a800;
    color: #f1f1f1; /* сделаем для тёмной темы текст warning тёмнее для контраста */
}

</style>
<?php 
function showAlert($type, $message, $icon) {
    echo '
    <div class="alert-toast ' . $type . '" role="alert">
        <img src="' . $icon . '" alt="" width="20" height="20">
        <span>' . $message . '</span>
    </div>';
}

if (isset($_SESSION["log-mess-s"])) {
    showAlert('success', $_SESSION["log-mess-s"], $base_url.'/static/svg/circle-yes.svg');
    unset($_SESSION["log-mess-s"]);
}

if (isset($_SESSION['log-mess-e'])) {
    showAlert('error', $_SESSION["log-mess-e"], $base_url.'/static/svg/circle-no.svg');
    unset($_SESSION['log-mess-e']);
}

if (isset($_SESSION['log-mess-warn'])) {
    showAlert('warning', $_SESSION["log-mess-warn"], $base_url.'/static/svg/circle-warn.svg');
    unset($_SESSION['log-mess-warn']);
}

?>
