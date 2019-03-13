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

class BranchDetector
{
    /**
     * @var string
     */
    private $pathToGitRepo;

    /**
     * @var Utilities
     */
    private $utilities;

    public function __construct(string $pathToGitRepo, Utilities $utilities)
    {
        $this->pathToGitRepo = $pathToGitRepo;
        $this->utilities     = $utilities;
    }

    public function getChangedFiles()
    {
        $command = 'cd '.$this->pathToGitRepo.' && git diff origin/master... --name-only';

        return $this->utilities->exec($command);
    }
}
