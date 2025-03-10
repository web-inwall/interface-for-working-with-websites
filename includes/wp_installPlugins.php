<?php

require '../vendor/autoload.php';

use phpseclib3\Net\SFTP;

class SFTPConnect {
	private $host;
	private $port;
	private $login;
	private $password;
	private $connected;
	private $remoteDir;
	private $sftp;

	public function __construct( string $host, int $port, string $login, string $password ) {
		$this->host = $host;
		$this->port = $port;
		$this->login = $login;
		$this->password = $password;
		$this->connected = false;
		$this->remoteDir = '/';
		$this->sftp = new SFTP( $this->host, $this->port );
	}

	private function setRemoteDir() {
		$this->remoteDir = ValidateData::validatePWD( $this->sftp->exec( 'pwd' ) );
	}

	public function getRemoteDir() {
		return $this->remoteDir;
	}

	public function getConntect() {
		return $this->sftp;
	}

	public function connect() {
		if ( $this->connected ) {
			return $this->sftp;
		}

		$login = $this->sftp->login( $this->login, $this->password );

		if ( !$login ) {
			throw new \Exception( 'Не удалось подключиться к удалённому серверу' ); 
		}

		$this->setRemoteDir();

		$this->connected = true;

		return $this->sftp;
	}

	public function disconenct() {
		$this->sftp->disconnect();
		$this->connected = false;
	}
}

class WordPressInstallPluginsTool {
	private $sftpConnect;
	private $wpdir;
	private $pluginsDir;

	public function __construct( SFTPConnect $sftp_connect ) {
		$this->sftpConnect = $sftp_connect;
		$this->wpdir = '';
		$this->pluginsDir = 'wp-content/plugins';

		$this->setWPDir();
	}

	private function buildRemoteFilePath( string $domain_path, string $file_name ) {
		$wpdir = $this->wpdir ? $this->wpdir . '/' : '';
		$remote_file_path = $domain_path . '/' . $wpdir . $this->pluginsDir . '/' . $file_name;

		return $remote_file_path;
	}

	private function setWPDir() {
		$sftp = $this->sftpConnect->getConntect();
		$remote_dir = $this->sftpConnect->getRemoteDir();
		// получить путь к wordpress внутри домена
		$dir = $sftp->exec( 'cat ' . $remote_dir . '/wpdir.txt' );
		$status = $sftp->getExitStatus();

		if ( $status !== 0 && $status !== 1 ) {
			throw new \Exception( 'Не удалось прочитать файл wpdir.txt: ошибка:' . $status );
		}

		$this->wpdir = $status === 0 ? ValidateData::validateWPDir( $dir ) : $this->wpdir;
	}

	private function install( string $archive, string $domain_path, string $file_name, string $sftp_flag_name ) {
		$flag = constant( SFTP::class . '::' . $sftp_flag_name );
		$sftp = $this->sftpConnect->getConntect();

		$remote_dir = $this->sftpConnect->getRemoteDir();
		$remote_file_path = $this->buildRemoteFilePath( $domain_path, $file_name );
		$remote_path = $remote_dir . '/' . $remote_file_path;

		$upload = $sftp->put( $remote_path, $archive, $flag );
		if ( !$upload ) {
			throw new \Exception( 'Не удалось загрузить файлы по SFTP: ' . $sftp->getLastSFTPError() );
		}

		// -o перезаписать существующие файлы с одинаковым названием
		// -d каталог куда разархивировать
		$unzip = $sftp->exec( 'unzip -o ' . $remote_path . ' -d ' . dirname( $remote_path ) );
		// удалить загруженный архив
		$delete = $sftp->delete( $remote_path );
		$this->sftpConnect->disconenct();
		if ( strpos( $unzip, 'extracting:' ) === false ) {
			throw new \Exception( 'Не удалось разархивировать архив на удалённом сервере' );
		}
	}

	public function installFromUploads( array $archive, string $domain_path ) {
		// $archive_path имеет расширение файла .tmp
		// добавить оригинальное название, чтобы сохранить оригинальное расширение
		$archive_path = $archive['tmp_name'];
		$file_name = basename( $archive_path ) . $archive['name'];
		$archive_content = file_get_contents( $archive_path );

		$this->install( $archive_content, $domain_path, $file_name, 'SOURCE_STRING' );
	}
}

class ValidateData {
	private static function checkNotEmpty( $value ) {
		// без учёта 0 и похожих значений
		if ( !$value ) {
			return false;
		}

		return $value;
	}

	private static function validate( array $rules, array $data ) {
		$validate_data = [];
		foreach ( $rules as $key => $rule ) {
			if ( !isset( $data[$key] ) ) {
				throw new \Exception( 'Обязательное поле ' . $key . ' не найдено' );
			}

			if ( !$rules[$key]( $data[$key] ) ) {
				throw new \Exception( 'Недопустимое значение в поле ' . $key );
			}

			$validate_data[$key] = $data[$key];
		}

		return $validate_data;
	}

	public static function validateSSHData( array $data ) {
		$rules = [
			'host' => function( $value ) {
				return self::checkNotEmpty( $value );
			},

			'port' => function( $value ) {
				if ( !self::checkNotEmpty( $value ) ) {
					return false;
				}

				return (int) $value;
			},

			'login' => function( $value ) {
				return self::checkNotEmpty( $value );
			},

			'password' => function( $value ) {
				return self::checkNotEmpty( $value );
			},

			'domain_path' => function( $value ) {
				if ( !self::checkNotEmpty( $value ) ) {
					return false;
				}

				return ltrim( rtrim( $value, '/' ), '/' );
			},
		];

		return self::validate( $rules, $data );
	}

	public static function validateUploadArchive( string $file_key ) {
		if ( !isset( $_FILES[$file_key] ) || $_FILES[$file_key]['error'] !== 0 ) {
			throw new \Exception( 'Не выбран архив с плагинами' );
		}

		if ( mime_content_type( $_FILES[$file_key]['tmp_name'] ) !== 'application/zip' ) {
			throw new \Exception( 'Выбранный файл не является архивом' );
		}

		return $_FILES[$file_key];
	}

	public static function validatePWD( string $pwd ) {
		// вдруг разные значения у разных хостингов
		// на тестовом хостинге pwd возвращает строку с пробелом в конце
		$remote_dir = '/' . trim( rtrim( ltrim( $pwd, '/' ), '/' ) );
		return $remote_dir;
	}

	public static function validateWPDir( string $wpdir ) {
		$wpdir = trim( rtrim( ltrim( $wpdir, '/' ), '/' ) );
		return $wpdir;
	}
}

try {
	$data = ValidateData::validateSSHData( $_POST );
	$archive = ValidateData::validateUploadArchive( 'plugins' );

	$sftpConnect = new SFTPConnect( $data['host'], $data['port'], $data['login'], $data['password'] );
	$sftpConnect->connect();

	$installPluginsTool = new WordPressInstallPluginsTool( $sftpConnect );

	// можно добавить в цикл для каждого домена
	$installPluginsTool->installFromUploads( $archive, $data['domain_path'] );

	echo json_encode([
		'error' => false,
		'message' => 'Установлено'
	]);
} catch ( \Exception $e ) {
	echo json_encode([
		'error' => true,
		'message' => $e->getMessage()
	]);
}