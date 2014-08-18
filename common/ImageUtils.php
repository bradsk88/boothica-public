<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/19/14
 * Time: 10:32 PM
 */
require_once "{$_SERVER['DOCUMENT_ROOT']}/common/boiler.php";
require_common("upload_utils");

class ImageUtils {

    /**
     * This function will echo any errors.
     *
     * @return The image obtained by decoding the base64 image string provided.
     */
    public static function makeFromEncoded($image) {
        define ("MAX_SIZE","5000");

        if (startsWith($image, "data:image/png;base64,")) {
            $image = str_replace('data:image/png;base64,', '', $image);
        } else if (startsWith($image, "data:image/jpeg;base64,")) {
            $image = str_replace('data:image/jpeg;base64,', '', $image);
        } else {
            if (startsWith($image, "data:image/")) {
                echo "Unsupported filetype [".substr($image, 11, 4)."].  Please use JPG or PNG";
            } else {
                echo "Unsupported filetype [UNKNOWN].  Please use JPG or PNG";
            }
            return null;
        }

        $image = str_replace(' ', '+', $image);

        if (!$image) {
            echo "I have no idea why... But this didn't work...";
            return null;
        }

        $uploadedfile = base64_decode($image);
        if (!$uploadedfile) {
            echo "Sorry.  We could not process this photo. [ERROR CODE 2]";
            return null;
        }
        return $uploadedfile;
    }

    public static function getExtensionOfEncoded($image) {
        if (startsWith($image, "data:image/png;base64,")) {
            return "png";
        } else if (startsWith($image, "data:image/jpeg;base64,")) {
            return "jpg";
        } else {
            if (startsWith($image, "data:image/")) {
                echo "Unsupported filetype [".substr($image, 11, 4)."].  Please use JPG or PNG";
            } else {
                echo "Unsupported filetype [UNKNOWN].  Please use JPG or PNG";
            }
            return null;
        }
    }

    public static function resize($image, $filename, $heightOverWidth, $newwidth) {
        list($width, $height) = getimagesize($filename);
        $newheight = $newwidth * $heightOverWidth;
        $small = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($small, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        return $small;
    }

    public static function testString() {
        return "Testing";
    }

} 