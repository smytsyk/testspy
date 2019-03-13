<?php
/*
 * This file is part of smytsyk-dev/testspy.
 *
 * (c) Stas Mytsyk <denielm10@gmail.com>
 * (c) Vlad Mytsyk <vdmytsyk@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TestSpy;

class Utilities
{
    public function fileGetContents(string $filename)
    {
        return \file_get_contents($filename);
    }

    public function filePutContents(string $filename, $data, $flags = 0)
    {
        return \file_put_contents($filename, $data, $flags);
    }

    public function exec(string $command)
    {
        \exec($command, $output);

        return $output;
    }

    public function touch(string $filename)
    {
        return \touch($filename);
    }

    public function unlink(string $filename, $swallow = false)
    {
        if ($swallow) {
            return @\unlink($filename);
        }

        return \unlink($filename);
    }

    public function file(string $filename)
    {
        return \file($filename);
    }

    public function isFile(string $filename) : bool
    {
        return \is_file($filename);
    }

    public function getIncludedFiles()
    {
        return get_included_files();
    }
}
