<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

<div class="row">
    <div class="col-4">
        <h2>Выберите сервер: </h2>
    </div>
    <div class="col-4">
        <input type="radio" class="btn-check" name="server" id="primaryServer" autocomplete="off" value="primary" checked>
        <label class="btn btn-light w-100" for="primaryServer">Primary Server</label>
    </div>
    <div class="col-4">
        <input type="radio" class="btn-check" name="server" id="secondaryServer" autocomplete="off" value="secondary">
        <label class="btn btn-light w-100" for="secondaryServer">Secondary Server</label>
    </div>
</div>

<hr>

<div class="row mb-5">
    <div class="col-12">
        <textarea name="isp_domains" cols="30" rows="10" class="form-control mb-3" placeholder="Введите домены. Каждый домен должен находиться на отдельной строке"></textarea>
        <button type="button" class="btn btn-success w-100" id="startISPAddDomains">Добавить домены</button>
    </div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>