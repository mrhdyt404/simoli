<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\FileCompressionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class FileCompressionServiceTest extends TestCase
{
    protected string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = storage_path('framework/testing/compression_test_' . uniqid());
        if (!File::exists($this->tempDir)) {
            File::makeDirectory($this->tempDir, 0755, true);
        }
    }

    protected function tearDown(): void
    {
        if (File::exists($this->tempDir)) {
            File::deleteDirectory($this->tempDir);
        }
        parent::tearDown();
    }

    public function test_compress_large_jpeg_image(): void
    {
        $srcPath = $this->tempDir . '/large_input.jpg';
        $im = imagecreatetruecolor(2500, 2500);
        $red = imagecolorallocate($im, 255, 0, 0);
        $blue = imagecolorallocate($im, 0, 0, 255);
        imagefilledrectangle($im, 0, 0, 2500, 1250, $red);
        imagefilledrectangle($im, 0, 1250, 2500, 2500, $blue);
        imagejpeg($im, $srcPath, 100);
        imagedestroy($im);

        $origSize = filesize($srcPath);
        $this->assertGreaterThan(0, $origSize);

        $destDir = $this->tempDir . '/output';
        $savedFilename = FileCompressionService::compressAndSave($srcPath, $destDir, 'compressed.jpg');

        $compressedPath = $destDir . '/' . $savedFilename;
        $this->assertFileExists($compressedPath);

        $compressedSize = filesize($compressedPath);
        $this->assertLessThan($origSize, $compressedSize, "Compressed size ($compressedSize) should be less than original ($origSize)");

        // Verify dimensions resized to max (<= 1920)
        [$width, $height] = getimagesize($compressedPath);
        $this->assertLessThanOrEqual(1920, $width);
        $this->assertLessThanOrEqual(1920, $height);
    }

    public function test_compress_png_image(): void
    {
        $srcPath = $this->tempDir . '/large_input.png';
        $im = imagecreatetruecolor(1000, 800);
        $green = imagecolorallocate($im, 0, 200, 50);
        imagefilledrectangle($im, 0, 0, 1000, 800, $green);
        imagepng($im, $srcPath, 0); // Uncompressed PNG
        imagedestroy($im);

        $origSize = filesize($srcPath);
        $destDir = $this->tempDir . '/output';
        $savedFilename = FileCompressionService::compressAndSave($srcPath, $destDir, 'compressed.png');

        $compressedPath = $destDir . '/' . $savedFilename;
        $this->assertFileExists($compressedPath);

        $compressedSize = filesize($compressedPath);
        $this->assertLessThanOrEqual($origSize, $compressedSize);
    }

    public function test_compress_pdf_document(): void
    {
        $srcPath = $this->tempDir . '/sample.pdf';
        // Generate valid PDF via Ghostscript
        exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=\"{$srcPath}\" -c \"/Helvetica findfont 24 scalefont setfont 100 700 moveto (Simoli Document Testing) show showpage\"");

        if (File::exists($srcPath)) {
            $destDir = $this->tempDir . '/output';
            $savedFilename = FileCompressionService::compressAndSave($srcPath, $destDir, 'sample_saved.pdf');
            $this->assertFileExists($destDir . '/' . $savedFilename);
            $this->assertGreaterThan(0, filesize($destDir . '/' . $savedFilename));
        } else {
            $this->markTestSkipped('Ghostscript PDF test generation skipped');
        }
    }

    public function test_compress_base64_image(): void
    {
        $im = imagecreatetruecolor(400, 400);
        $yellow = imagecolorallocate($im, 255, 255, 0);
        imagefilledrectangle($im, 0, 0, 400, 400, $yellow);
        ob_start();
        imagejpeg($im, null, 90);
        $imgData = ob_get_clean();
        imagedestroy($im);

        $base64 = 'data:image/jpeg;base64,' . base64_encode($imgData);
        $destDir = $this->tempDir . '/output';

        $savedFilename = FileCompressionService::compressBase64Image($base64, $destDir, 'base64_test');
        $this->assertFileExists($destDir . '/' . $savedFilename);
    }
}
