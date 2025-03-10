<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>


<form class="row webarchive_form mb-5">
    <dvi class="col-10">
        <input type="text" class="form-control" name="sitemap_url" required placeholder="Введите ссылку на карту сайта">
    </dvi>
    <div class="col-2">
        <button type="submit" class="btn btn-success w-100">Отправить</button>
    </div>
</form>

<table class="table result">
  <tbody>
    <tr>
      <td>Здесь будут отображены результаты</td>
    </tr>
    <tr>
  </tbody>
</table>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>