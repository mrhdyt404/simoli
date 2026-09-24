<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FileCompressionService
{
    /**
     * Default options for compression
     */
    protected const DEFAULT_IMAGE_QUALITY = 78;
    protected const DEFAULT_MAX_WIDTH = 1920;
    protected const DEFAULT_MAX_HEIGHT = 1920;
    protected const DEFAULT_PDF_SETTING = '/ebook'; // /screen (72dpi), /ebook (150dpi), /printer (300dpi)

    /**
     * Kompres dan simpan file (Gambar, PDF, atau file lainnya) ke direktori tujuan.
     *
     * @param UploadedFile|string $file Objek UploadedFile atau path string ke file sumber
     * @param string $destinationDir Direktori penyimpanan (misal: public_path('gallery'))
     * @param string|null $filename Nama file yang diinginkan (opsional)
     * @param array $options Opsi kompresi (quality, max_width, max_height, pdf_setting, dll)
     * @return string Nama file yang tersimpan
     */
    public static function compressAndSave($file, string $destinationDir, ?string $filename = null, array $options = []): string
    {
        if (!File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true, true);
        }

        $isUploadedFile = $file instanceof UploadedFile;
        $sourcePath = $isUploadedFile ? $file->getRealPath() : $file;
        $extension = strtolower($isUploadedFile ? $file->getClientOriginalExtension() : pathinfo($sourcePath, PATHINFO_EXTENSION));

        if (empty($filename)) {
            $filename = time() . '_' . uniqid() . '.' . $extension;
        }

        $targetPath = rtrim($destinationDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        // Cek tipe file untuk diarahkan ke metode kompresi yang sesuai
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            self::compressImage($sourcePath, $targetPath, $extension, $options);
        } elseif ($extension === 'pdf') {
            self::compressPdf($sourcePath, $targetPath, $options);
        } else {
            // File lain (ZIP, GeoJSON, SVG, JSON, dll) - simpan biasa tanpa ubah
            if ($isUploadedFile) {
                $file->move($destinationDir, $filename);
            } else {
                File::copy($sourcePath, $targetPath);
            }
        }

        return $filename;
    }

    /**
     * Kompres file gambar (JPG, PNG, WEBP, GIF) dengan penyesuaian resolusi dan orientasi EXIF
     *
     * @param string $sourcePath Path file gambar asli
     * @param string $targetPath Path tujuan penyimpanan file kompresi
     * @param string $extension Ekstensi file
     * @param array $options Opsi kompresi
     * @return bool True jika berhasil
     */
    public static function compressImage(string $sourcePath, string $targetPath, string $extension = '', array $options = []): bool
    {
        if (empty($extension)) {
            $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        }

        $quality = $options['quality'] ?? self::DEFAULT_IMAGE_QUALITY;
        $maxWidth = $options['max_width'] ?? self::DEFAULT_MAX_WIDTH;
        $maxHeight = $options['max_height'] ?? self::DEFAULT_MAX_HEIGHT;

        try {
            if (!extension_loaded('gd')) {
                return File::copy($sourcePath, $targetPath);
            }

            // Baca informasi gambar
            $imageInfo = @getimagesize($sourcePath);
            if (!$imageInfo) {
                return File::copy($sourcePath, $targetPath);
            }

            $origWidth = $imageInfo[0];
            $origHeight = $imageInfo[1];
            $mime = $imageInfo['mime'] ?? '';

            // Load GD Image resource
            $srcImage = null;
            switch ($mime) {
                case 'image/jpeg':
                case 'image/jpg':
                    $srcImage = @imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $srcImage = @imagecreatefrompng($sourcePath);
                    break;
                case 'image/webp':
                    $srcImage = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : null;
                    break;
                case 'image/gif':
                    $srcImage = @imagecreatefromgif($sourcePath);
                    break;
                default:
                    $srcImage = @imagecreatefromstring(file_get_contents($sourcePath));
                    break;
            }

            if (!$srcImage) {
                return File::copy($sourcePath, $targetPath);
            }

            // Auto-rotate berdasarkan EXIF Orientation (khusus kamera HP)
            if (function_exists('exif_read_data') && ($mime === 'image/jpeg' || $mime === 'image/jpg')) {
                try {
                    $exif = @exif_read_data($sourcePath);
                    if (!empty($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 3:
                                $srcImage = imagerotate($srcImage, 180, 0);
                                break;
                            case 6:
                                $srcImage = imagerotate($srcImage, -90, 0);
                                $temp = $origWidth;
                                $origWidth = $origHeight;
                                $origHeight = $temp;
                                break;
                            case 8:
                                $srcImage = imagerotate($srcImage, 90, 0);
                                $temp = $origWidth;
                                $origWidth = $origHeight;
                                $origHeight = $temp;
                                break;
                        }
                    }
                } catch (\Throwable $e) {
                    // Abaikan error EXIF
                }
            }

            // Hitung ukuran baru jika melebihi batas maksimum
            $newWidth = $origWidth;
            $newHeight = $origHeight;

            if ($origWidth > $maxWidth || $origHeight > $maxHeight) {
                $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
                $newWidth = (int) round($origWidth * $ratio);
                $newHeight = (int) round($origHeight * $ratio);
            }

            // Buat canvas gambar baru
            $dstImage = imagecreatetruecolor($newWidth, $newHeight);

            // Pertahankan transparansi untuk PNG, WEBP, GIF
            if (in_array($extension, ['png', 'webp', 'gif'])) {
                imagealphablending($dstImage, false);
                imagesavealpha($dstImage, true);
                $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
                imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Resample gambar
            imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

            // Simpan gambar sesuai format
            $tempTargetPath = $targetPath . '.tmp';
            $saved = false;

            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $saved = imagejpeg($dstImage, $tempTargetPath, $quality);
                    break;
                case 'png':
                    // Konversi quality 0-100 ke kompresi PNG 0-9
                    $pngQuality = (int) round(9 - (($quality / 100) * 9));
                    $pngQuality = max(0, min(9, $pngQuality));
                    $saved = imagepng($dstImage, $tempTargetPath, $pngQuality);
                    break;
                case 'webp':
                    if (function_exists('imagewebp')) {
                        $saved = imagewebp($dstImage, $tempTargetPath, $quality);
                    } else {
                        $saved = imagejpeg($dstImage, $tempTargetPath, $quality);
                    }
                    break;
                case 'gif':
                    $saved = imagegif($dstImage, $tempTargetPath);
                    break;
                default:
                    $saved = imagejpeg($dstImage, $tempTargetPath, $quality);
                    break;
            }

            // Bersihkan resource memori
            imagedestroy($srcImage);
            imagedestroy($dstImage);

            if ($saved && File::exists($tempTargetPath)) {
                // Periksa jika hasil kompresi lebih kecil dari aslinya, atau ganti jika resolusi berubah
                $origSize = @filesize($sourcePath) ?: 0;
                $newSize = @filesize($tempTargetPath) ?: 0;

                if ($newSize > 0 && ($newSize < $origSize || $origWidth > $maxWidth || $origHeight > $maxHeight)) {
                    if (File::exists($targetPath)) {
                        @unlink($targetPath);
                    }
                    File::move($tempTargetPath, $targetPath);
                    return true;
                } else {
                    // Jika file kompresi lebih besar (misal file sangat kecil), gunakan file asli
                    @unlink($tempTargetPath);
                    return File::copy($sourcePath, $targetPath);
                }
            }

            if (File::exists($tempTargetPath)) {
                @unlink($tempTargetPath);
            }

            return File::copy($sourcePath, $targetPath);
        } catch (\Throwable $e) {
            Log::warning('Image compression error: ' . $e->getMessage(), ['source' => $sourcePath]);
            return File::copy($sourcePath, $targetPath);
        }
    }

    /**
     * Kompres dokumen PDF menggunakan Ghostscript
     *
     * @param string $sourcePath Path file PDF asli
     * @param string $targetPath Path tujuan PDF kompresi
     * @param array $options Opsi Ghostscript
     * @return bool True jika kompresi berhasil
     */
    public static function compressPdf(string $sourcePath, string $targetPath, array $options = []): bool
    {
        $pdfSetting = $options['pdf_setting'] ?? self::DEFAULT_PDF_SETTING;
        $tempOutput = $targetPath . '.gs_tmp.pdf';

        try {
            // Periksa ketersediaan Ghostscript di server
            $gsBinary = self::findGhostscriptBinary();

            if (!$gsBinary) {
                return File::copy($sourcePath, $targetPath);
            }

            $sourceEscaped = escapeshellarg($sourcePath);
            $outputEscaped = escapeshellarg($tempOutput);
            $pdfSettingEscaped = escapeshellarg('-dPDFSETTINGS=' . $pdfSetting);

            // Jalankan perintah Ghostscript untuk optimasi & kompresi PDF
            $command = "{$gsBinary} -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 {$pdfSettingEscaped} -dNOPAUSE -dQUIET -dBATCH -dDetectDuplicateImages=true -dCompressFonts=true -sOutputFile={$outputEscaped} {$sourceEscaped} 2>&1";

            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            if ($returnVar === 0 && File::exists($tempOutput)) {
                $origSize = @filesize($sourcePath) ?: 0;
                $newSize = @filesize($tempOutput) ?: 0;

                // Gunakan PDF hasil kompresi jika ukurannya lebih kecil dan valid (> 0 bytes)
                if ($newSize > 0 && $newSize < $origSize) {
                    if (File::exists($targetPath)) {
                        @unlink($targetPath);
                    }
                    File::move($tempOutput, $targetPath);
                    return true;
                }
            }

            // Jika kompresi gagal atau file hasil lebih besar, bersihkan temp dan salin file asli
            if (File::exists($tempOutput)) {
                @unlink($tempOutput);
            }

            return File::copy($sourcePath, $targetPath);
        } catch (\Throwable $e) {
            Log::warning('PDF compression error: ' . $e->getMessage(), ['source' => $sourcePath]);
            if (File::exists($tempOutput)) {
                @unlink($tempOutput);
            }
            return File::copy($sourcePath, $targetPath);
        }
    }

    /**
     * Kompres dan simpan gambar dari string base64 (digunakan pada sinkronisasi offline mobile)
     *
     * @param string $base64String
     * @param string $destinationDir
     * @param string $prefix
     * @param array $options
     * @return string Nama file tersimpan
     */
    public static function compressBase64Image(string $base64String, string $destinationDir, string $prefix = 'img', array $options = []): string
    {
        if (!File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true, true);
        }

        $imageParts = explode(';base64,', $base64String);
        $imageTypeAux = explode('image/', $imageParts[0] ?? '');
        $imageType = count($imageTypeAux) > 1 ? strtolower($imageTypeAux[1]) : 'jpeg';
        if ($imageType === 'jpg') $imageType = 'jpeg';

        $rawBase64 = isset($imageParts[1]) ? $imageParts[1] : $base64String;
        $imageData = base64_decode($rawBase64);

        $ext = $imageType === 'jpeg' ? 'jpg' : $imageType;
        $fileName = $prefix . '_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $tempSource = tempnam(sys_get_temp_dir(), 'b64_') . '.' . $ext;

        File::put($tempSource, $imageData);

        $targetPath = rtrim($destinationDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;

        self::compressImage($tempSource, $targetPath, $ext, $options);

        @unlink($tempSource);

        return $fileName;
    }

    /**
     * Mencari path binary Ghostscript yang tersedia di sistem
     */
    protected static function findGhostscriptBinary(): ?string
    {
        $binaries = ['/usr/bin/gs', '/usr/local/bin/gs', 'gs'];

        foreach ($binaries as $bin) {
            $check = @shell_exec("which {$bin} 2>/dev/null");
            if (!empty($check)) {
                return trim($check);
            }
            if (file_exists($bin) && is_executable($bin)) {
                return $bin;
            }
        }

        return null;
    }
}
