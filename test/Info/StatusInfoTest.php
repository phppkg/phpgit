<?php declare(strict_types=1);

namespace PhpGitTest\Info;

use PhpGit\Info\StatusInfo;
use PhpGitTest\BasePhpGitTestCase;
use function vdump;

/**
 * class StatusInfoTest
 *
 * @author inhere
 * @date 2022/10/28
 */
class StatusInfoTest extends BasePhpGitTestCase
{
    public function testStatusInfo_fromString(): void
    {
        $text = <<<TXT
 ## master...origin/fea/master
 RM app/Common/GitLocal/GitFlow.php -> app/Common/GitLocal/GitFactory.php
  M app/Common/GitLocal/GitHub.php
 ?? app/Common/GitLocal/GitConst.php
  D tmp/delete-some.file
TXT;

        $si = StatusInfo::fromString($text);
        $this->assertEquals('master', $si->branch);
        $this->assertEquals('origin', $si->upRemote);
        $this->assertEquals('fea/master', $si->upBranch);
        vdump($si);
    }
}
