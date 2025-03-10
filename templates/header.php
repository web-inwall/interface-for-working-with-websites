<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Букварикс для проверки множества доменов</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/main.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>

<body>
    <div class="ajax-loader">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span class="ajax_loader__text"></span>
    </div>
    <header class="py-3 border-bottom">
        <div class="container d-flex justify-content-center ">
            <ul class="main_menu nav nav-pills">
                <li class="nav-item"><a href="/bukvarix" class="nav-link" aria-current="page">Bukvarix</a></li>
                <li class="nav-item"><a href="/cloudflare" class="nav-link">CloudFlare</a></li>
                <li class="nav-item"><a href="/indexing" class="nav-link">Индексация</a></li>
                <li class="nav-item"><a href="/webarchive" class="nav-link">WebArchive</a></li>
                <li class="nav-item"><a href="/isp" class="nav-link">ISP</a></li>
                <li class="nav-item"><a href="/wordpress" class="nav-link">WordPress</a></li>
                <li class="nav-item"><a href="/siteinspector" class="nav-link">SiteInspector</a></li>
            </ul>
        </div>
    </header>
    <main class="main container py-5">