<?php
namespace BackupMigrate\Core\Tests\Util;

use \BackupMigrate\Core\Util\TempFile;

/**
 * @coversDefaultClass \BackupMigrate\Core\Util\TempFile
 */
class TempFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var tempFile
     */
    protected $tempFile;

    public function setUp()
    {
        $this->tempFile = new TempFile();
    }

    /**
     * @covers ::__constructor
     * @covers ::__destructor
     * @covers ::realpath
     */
    public function testSetupAndDestroy()
    {
        $file = new TempFile();
        // Make sure a temp file has been created somewhere.
        $this->assertNotEmpty(file_exists($file->realpath()));
        $this->assertNotEmpty(is_writable($file->realpath()));

        // Destroy the object
        $path = $file->realpath();
        unset($file);
        $this->assertEmpty(file_exists($path));
    }

}
