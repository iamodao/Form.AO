<?php
session_start();
const DS = DIRECTORY_SEPARATOR;
const PS = '/';









class oForm {
	private static $initialized;
	private static $storage;




	private static function initialize() {
		if (!self::$initialized) {
			self::$storage = __DIR__ . DS . 'storage' . DS;
			self::$initialized = true;
		}
	}




	public static function generate($extension = 'md', $prefix = null) {
		$counter = 1;
		if (!is_dir(self::$storage)) {
			mkdir(self::$storage, 0777, true);
		}
		if (!is_null($prefix)) {
			$existingFiles = glob(self::$storage . '*.' . $extension);
		} else {
			$existingFiles = glob(self::$storage . $prefix . '_*.' . $extension);
		}
		$existingNumbers = array_map(function ($file) use ($prefix, $extension) {
			if (!is_null($prefix)) {
				$pattern = "/$prefix" . '_' . "(\d+)\.$extension/";
			} else {
				$pattern = "/" . "(\d+)\.$extension/";
			}
			preg_match($pattern, $file, $matches);
			return isset($matches[1]) ? (int) $matches[1] : 0;
		}, $existingFiles);

		if (!empty($existingNumbers)) {
			$counter = max($existingNumbers) + 1;
		}

		if (!is_null($prefix)) {
			$newFileName = self::$storage . $prefix . '_' . $counter . '.' . $extension;
		} else {
			$newFileName = self::$storage . $counter . '.' . $extension;
		}
		return $newFileName;
	}




	public static function dump($var) {
		echo '<pre><tt>' . var_export($var, true) . '</tt></pre>';
	}




	public static function download($file = null) {
		self::initialize();
		if (is_null($file) && !empty($_GET['id'])) {
			$file = $_GET['id'];
		}
		$file = self::$storage . $file;
		if (!empty($file) && is_file($file)) {
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . basename($file) . '"');
			header('Content-Length: ' . filesize($file));
			readfile($file);
		}
		exit;
	}




	public static function create($extension = 'md', $prefix = 'Form') {
		// $amount = filter_input(INPUT_POST, 'Amount', FILTER_VALIDATE_INT);
		// if ($_SERVER["REQUEST_METHOD"] == "POST" && $amount !== false && $amount > 0) {
		if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {
			$data = 'Date: ' . date('Y-m-d H:i:s') . "\n";
			foreach ($_POST as $key => $value) {
				$data .= $key . ': ' . $value . "\n";
			}
			$filename = self::generate($extension, $prefix);
			$file = fopen($filename, "a");
			fwrite($file, $data);
			fclose($file);
			return true;
		}
		return false;
	}




	public static function list($extension = null) {
		self::initialize();
		if (!empty($extension)) {
			$files = glob(self::$storage . '/*' . $extension);
		} else {
			$files = scandir(self::$storage);
		}
		foreach ($files as $file) {
			if ($file != '.' && $file != '..' && !is_dir($file)) {
				$row[] = $file;
			}
		}
		if (!empty($row)) {
			return $row;
		}
		return false;
	}




	public static function read($file = null) {
		if (is_null($file) && !empty($_GET['id'])) {
			$file = $_GET['id'];
		}
		if (!empty($file) && is_file(self::$storage . $file)) {
			$content = file_get_contents($file);
			$lines = array_filter(explode("\n", $content));
			foreach ($lines as $line) {
				$entry = explode(':', $line);
				list($label, $value) = $entry;
				$label = strtoupper(trim($label));
				$value = trim($value);
				if (!array_key_exists($label, $row)) {
					$row[$label] = $value;
				}
			}
			return $row;
		}
		return;
	}




	public static function delete($file = null) {
		self::initialize();
		if (is_null($file) && !empty($_GET['id'])) {
			$file = $_GET['id'];
		}
		$file = self::$storage . $file;
		if (!empty($file) && is_file($file)) {
			unlink($file);
			header('Location: ./');
			exit;
		}
		return false;
	}




	public static function listing($extension = null) {
		$files = self::list($extension);
		if (!empty($files) && is_array($files)) {
			$link = '';
			$sn = 1;
			foreach ($files as $file) {
				$filename = pathinfo($file, PATHINFO_FILENAME);
				$link .= '<span class="link">' . $sn++ . '. ';
				$link .= '<strong>' . $filename . '</strong> → ';
				// $link .= '<a href="./?link=read&id=' . basename($file) . '" title="Read">Read</a> • ';
				$link .= '<a href="./?link=download&id=' . basename($file) . '" title="Download">Download</a> • ';
				$link .= '<a href="./?link=delete&id=' . basename($file) . '" class="accent" title="Delete">Delete</a>';
				$link .= '</span>';
			}
			return $link;
		}
		return '<span class="accent">No record found!</span>';
	}





	public static function frontend() {
		$link = 'list';
		if (!empty($_GET['link'])) {
			$link = $_GET['link'];
		}

		if ($link === 'download') {
			return self::download();
		}
		if ($link === 'delete') {
			return self::delete();
		}
		if ($link === 'list') {
			$listing = self::listing();
			return $listing;
		}
	}
}