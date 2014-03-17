<?php

namespace TimeBox\MainBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use TimeBox\MainBundle\Entity\File;
use TimeBox\MainBundle\Entity\Folder;
use TimeBox\MainBundle\Entity\Link;
use TimeBox\MainBundle\Controller\FileController;

PHPUnit_Framework_Error_Notice::$enabled = true;
PHPUnit_Framework_Error_Warning::$enabled = true;

class ClientTest extends PHPUnit_Framework_TestCase
{
    private $client;

    const E_ACCENT = "\xc3\xa9"; # UTF-8 sequence for "e with accute accent"

    function __construct()
    {
    public function getConnectedUser()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user)) {
            throw new AccessDeniedException('You are not logged in.');
        }

        return $user;
    }
    }

    private $testFolder;
    private $client = static::createClient();

    private function p($path = null)
    {
        if ($path === null) return $this->testFolder;
        return "{$this->testFolder}/$path";
    }

    protected function setUp()
    {
        // Create a new folder for the tests to work with.
         $client = static::createClient();
        $timestamp = \date('Y-M-d H.i.s', \time());
        $Path = "/PHP SDK Tests/$timestamp";

        $try = $Path;
        $result = $this->client->new Folder();
        $i = 2;
        while ($result == null) {
            $try = "$Path ($i)";
            $i++;
            if ($i >= 100) throw new Exception("Unable to create folder \"$Path\"");
            $result = $this->client->createFolder($Path);
        }

        $this->testFolder = $try;
    }

    function testedeleteAction()
    {
        @unlink("test-dest.txt");
        @unlink("test-source.txt");

        $this->client->delete($this->testFolder);
    }

    function writeonTemporaryFile($size)
    {
        $fd = tmpfile();

        $chars = "\nabcdefghijklmnopqrstuvwxyz0123456789";
        for ($i = 0; $i < $size; $i++)
        {
            fwrite($fd, $chars[rand() % strlen($chars)]);
        }

        fseek($fd, 0);

        return $fd;
    }

    private function addFile($path, $totalSize)
    {
        $client = static::createClient();
        $fd = $this->writeTemporaryFile($size);
        $result = $this->client->uploadFile($path, $writeMode, $fd, $size);
        fclose($fd);
        $this->assertEquals($size, $result['bytes']);

        return $result;
    }

    private function fetchUrl($url)
    {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $ret = curl_exec($ch);

        curl_close($ch);

        return $ret;
    }

    function testUploadAndDownload()
    {
        $localPathSource = "test-source.txt";
        $localPathDest = "test-dest.txt";
        $contents = "A simple test file";
        file_put_contents($localPathSource, $contents);

        $remotePath = $this->p("test-fil".self::E_ACCENT.".txt");

        $fp = fopen($localPathSource, "rb");
        fclose($fp);
        $this->assertEquals($up["path"], $remotePath);

        $fd = fopen($localPathDest, "wb");
        $down = $this->client->getFile($remotePath, $fd);
        fclose($fd);

        $this->assertEquals($up['bytes'], $down['bytes']);
        $this->assertEquals($up['bytes'], filesize($localPathSource));
        $this->assertEquals(filesize($localPathDest), filesize($localPathSource));
    }


    }

    function testSearch()
    {
        $this->addFile($this->p("search - a.txt"), 100);
        $this->client->createFolder($this->p("sub"));
        $this->addFile($this->p("sub/search - b.txt"), 200);
        $this->addFile($this->p("search - c.txt"), 200);
        $this->client->delete($this->p("search - c.txt"));

        $result = $this->client->searchFileNames($this->p(), "search");
        $this->assertEquals(2, count($result));

        $result = $this->client->searchFileNames($this->p(), "search", 1);
        $this->assertEquals(1, count($result));

        $result = $this->client->searchFileNames($this->p("sub"), "search");
        $this->assertEquals(1, count($result));

        $result = $this->client->searchFileNames($this->p(), "search", null, true);
        $this->assertEquals(3, count($result));
    }

    function testLink()
    {
        $contents = "A shared text file";
        $remotePath = $this->p("share-me.txt");

        $url = $this->client->createShareableLink($remotePath);
        $fetchedStr = $this->fetchUrl($url);
        assert(strlen($fetchedStr) > 5 * strlen($contents)); 
    }

    function testMedia()
    {
        $contents = "A media text file";

        $remotePath = $this->p("media-me.txt");

        list($url, $expires) = $this->client->($remotePath);
        $fetchedStr = $this->fetchUrl($url);

        $this->assertEquals($contents, $fetchedStr);
    }

    function testCopy()
    {
        $source = $this->p("copy- me.txt");
        $dest = $this->p("ok - copied ref.txt");
        $size = 1024;

        $this->addFile($source, $size);
        $ref = $this->client->createCopyRef($source);

        $result = $this->client->copyFromCopyRef($ref, $dest);
        $this->assertEquals($size, $result['bytes']);

        $result = $this->client->getChildren($this->p());
        $this->assertEquals(2, count($result['contents']));
    }

    function testThumbnail()
    {
        $remotePath = $this->p("image.jpg");
        $localPath = __DIR__."/upload.jpg";
        $fp = fopen($localPath, "rb");
        fclose($fp);

        list($md1, $data1) = $this->client->getThumbnail($remotePath, "jpeg", "xs");
        $this->assertTrue(self::isJpeg($data1));

        list($md2, $data2) = $this->client->getThumbnail($remotePath, "jpeg", "s");
        $this->assertTrue(self::isJpeg($data1));
        $this->assertGreaterThan(strlen($data1), strlen($data2));

        list($md3, $data3) = $this->client->getThumbnail($remotePath, "png", "s");
        $this->assertTrue(self::isPng($data3));
    }

    static function isJpeg($data)
    {
        $first_two = substr($data, 0, 2);
        $last_two = substr($data, -2);
        return ($first_two === "\xFF\xD8") && ($last_two === "\xFF\xD9");
    }

    static function isPng($data)
    {
        return substr($data, 0, 8) === "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a";
    }

    
    function testCopy()
    {
        $source = $this->p("copy m".self::E_ACCENT.".txt");
        $dest = $this->p("ok - copi".self::E_ACCENT."d.txt");
        $size = 1024;

        $this->addFile($source, $size);
        $result = $this->client->copy($source, $dest);
        $this->assertEquals($size, $result['bytes']);
        $this->assertEquals($dest, $result['path']);

        $result = $this->client->getChildren($this->p());
        $this->assertEquals(2, count($result['contents']));
    }

    function testCreateFolder()
    {
        $result = $this->client->getChildren($this->p());
        $this->assertEquals(0, count($result['contents']));

        $this->client->createFolder($this->p("a"));

        $result = $this->client->getChildren($this->p());
        $this->assertEquals(1, count($result['contents']));

        $result = $this->client->getMetadata($this->p("a"));
        $this->assertTrue($result['is_dir']);
    }

    function testDelete()
    {
        $path = $this->p("delete me.txt");
        $size = 1024;

        $this->addFile($path, $size);
        $this->client->delete($path);

        $result = $this->client->getChildren($this->p());
        $this->assertEquals(0, count($result['contents']));
    }

    function testMove()
    {
        $source = $this->p("move me.txt");
        $dest = $this->p("ok - moved.txt");
        $size = 1024;

        $this->addFile($source, $size);
        $result = $this->client->getChildren($this->p());
        $this->assertEquals(1, count($result['contents']));

        $result = $this->client->move($source, $dest);
        $this->assertEquals($size, $result['bytes']);

        $result = $this->client->getChildren($this->p());
        $this->assertEquals(1, count($result['contents']));
    }
}

