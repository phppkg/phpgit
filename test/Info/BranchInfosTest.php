<?php declare(strict_types=1);

namespace PhpGitTest\Info;

use PhpGit\Info\BranchInfos;
use PHPUnit\Framework\TestCase;

/**
 * class BranchInfosTest
 *
 * @author inhere
 * @date 2022/6/15
 */
class BranchInfosTest extends TestCase
{
    public function testBranchInfos_basic(): void
    {
        $brInfos = BranchInfos::fromString(<<<TXT
  fea/new_br001
* master
  my_new_br 
  remotes/origin/my_new_br 
TXT
);
        // vdump($brInfos);

        $this->assertNotEmpty($cur = $brInfos->getCurrentBranch());
        $this->assertTrue($cur->current);
    }

    public function testBranchInfos_verbose(): void
    {
        $brInfos = BranchInfos::fromString(<<<TXT
  fea/new_br001             73j824d the message 001
* master                    7r60d4f the message 002
  my_new_br                 6fb8dcd the message 003
  remotes/origin/my_new_br   6fb8dcd the message 003
TXT
);
        vdump($brInfos);

        $this->assertNotEmpty($cur = $brInfos->getCurrentBranch());
        $this->assertTrue($cur->current);
    }
}
