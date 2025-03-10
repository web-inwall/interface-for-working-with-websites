<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

<div class="tool-content-wrapper">
	<div class="tool-content">
		<div class="tool-tabs-wrapper">
			<div class="tool-tabs">
				<ul class="tool-tabs-list">
					<li class="tool-tabs-list-item tool-tabs-list-item-active" data-id="install-plugins">Установить плагины</li>
					<li class="tool-tabs-list-item" data-id="other">Другой инструмент</li>
				</ul>
			</div>
		</div>

		<div class="tool-tabs-content-wrapper">
			<div class="tool-tabs-content">
				<div class="tool-tab-content-wrapper tool-tab-content-active" data-id="install-plugins">
					<div class="tool-tab-content">
						<div class="tool-settings-fields-wrapper">
							<div class="tool-settings-fields">
								<div class="tool-setting-field-wrapper">
									<div class="tool-setting-field upload-file">
										<div class="tool-setting-field-label">
											<span>Выбрерите архив с плагинами</span>
										</div>
										<div class="tool-setting-field-desc">
											<span>Плагины должны быть <b>разархивированы</b> внутри архива. Папки с плагинами должны находиться в корне архива. <b>Правильно: архив -> папки с плагинами</b>. Неправильно: архив -> плагины (папка) -> папки с плагинами.</span>
										</div>
										<div class="tool-setting-field-field">
											<input type="file" />
										</div>
									</div>
								</div>

								<div class="tool-setting-field-wrapper">
									<div class="tool-setting-field">
										<div class="tool-setting-field-label">
											<span>Список сайтов</span>
										</div>

										<div class="tool-setting-field-field">
											<div class="tool-sites-table-main-wrapper"></div>
										</div>
									</div>
								</div>

								<div class="tool-setting-field-wrapper">
									<div class="tool-setting-field">
										<div class="tool-setting-field-field">
											<button class="btn btn-success w-100" id="startWPInstallPlugins">Запустить установку плагинов</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="tool-tab-content-wrapper tool-tab-content-active" data-id="other">
					<div class="tool-tab-content">
						<p>Другой инструмент</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>

<script src="/assets/wordpress-scripts.js"></script>