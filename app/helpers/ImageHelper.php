<?php
class ImageHelper
{
    public static function uploadImages($files, $uploadDir = 'uploads/ads/')
    {
        $uploadedImages = [];
        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];

        // Ensure directory exists
        // Use absolute path based on public folder being the entry point
        // But better to use __DIR__ to find root.
        // APP_ROOT is c:\xampp\htdocs\OLX\app
        // We want c:\xampp\htdocs\OLX\public\uploads...

        $targetDir = dirname(APP_ROOT) . '/public/' . $uploadDir;

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Loop through files
        // Handle single file upload structure vs multiple
        if (is_array($files['name'])) {
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($files['error'][$i] === 0) {
                    $fileName = $files['name'][$i];
                    $fileTmp = $files['tmp_name'][$i];
                    $fileSize = $files['size'][$i];

                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    if (in_array($fileExt, $allowedTypes)) {
                        if ($fileSize < 5000000) { // 5MB limit
                            $newFileName = uniqid('', true) . "." . $fileExt;
                            $fileDestination = $targetDir . $newFileName;

                            if (move_uploaded_file($fileTmp, $fileDestination)) {
                                // In a real scenario, we would compress here using GD or Imagick
                                // self::compressImage($fileDestination, $fileDestination, 75);
                                $uploadedImages[] = $uploadDir . $newFileName;
                            }
                        }
                    }
                }
            }
        }

        return $uploadedImages;
    }

    // Basic compression (placeholder for more advanced logic)
    public static function compressImage($source, $destination, $quality)
    {
        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg')
            $image = imagecreatefromjpeg($source);
        elseif ($info['mime'] == 'image/gif')
            $image = imagecreatefromgif($source);
        elseif ($info['mime'] == 'image/png')
            $image = imagecreatefrompng($source);
        elseif ($info['mime'] == 'image/webp')
            $image = imagecreatefromwebp($source);
        else
            return false;

        imagejpeg($image, $destination, $quality);
        return true;
    }
}
