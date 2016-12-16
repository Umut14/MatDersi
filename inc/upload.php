<?php

function file_control($target_file, $file_name) {
	$real_name = $_FILES[$file_name]['name'];
	$tmp_file  = $_FILES[$file_name]['tmp_name'];

	if ($tmp_file && $real_name) {
		if (!$_FILES[$file_name]['error']) {
			$type = pathinfo($real_name, PATHINFO_EXTENSION); // Resmin uzantısı

			$size = explode(".", filesize($tmp_file) / 1024); // Boyutunu çektir, kilobyte'a çevir.
			$size = $size[0]; // küsurat'ını alma.

			if ($pixels = @getimagesize($tmp_file))
				list($width, $height) = $pixels; // Resmin genişliğini ve uzunluğunu aldık.
			else
				list($width, $height) = array(0, 0);

			$control = image_control($type, $size, $width, $height);
			if ($control[0]) {
				$upload = upload_image($target_file, $real_name, $tmp_file); // Resmi yüklemeye gönder
				return $upload; // upload değişken array olacak geri dönecek
			}
			else return array(false, $control[1]);

		} else return array(false, "Hata! Dosya seçilmedi veya boyutu çok büyük, lütfen başka bir tane dene");
	}
	else return array(false, "Hata! Dosya seçilmedi veya boyutu çok büyük, lütfen başka bir tane dene");
}

function image_control($type, $size, $width, $height) {
	$allowed_types = array("JPG", "JPEG", "PNG", "GIF", "jpg", "jpeg", "png", "gif"); // İzinli uzantılar
	if (in_array($type, $allowed_types)) {
		if ($width > 10 || $height > 10) {
			if ($size > 1 && $size < 2048) {
				return array(true, true);
			}
			else return array(false, "Resmin boyutu çok büyük. (Maksimum 2MB)");
		}
		else return array(false, "Bunun gerçek bir resim olduğunu düşünmüyoruz, lütfen başka bir tane dene.");
	}
	else return array(false, "Dosya türü uygun değil. (Sadece JPG, PNG VE GIF)");
}

function upload_image($target_file, $real_name, $tmp_file) {
	$target = SITE_ROOT . "/img/" . $target_file . "/";

	$type = pathinfo($real_name, PATHINFO_EXTENSION); // UZANTI

	$random_name = random_string() . "." . $type;
	$to = $target . basename($random_name);

	if (move_uploaded_file($tmp_file, $to)) return array($random_name, "Resim güncellendi!");
	else return array(false, "Resim bilinmedik bir sebepten dolayı güncellenemedi.");
}