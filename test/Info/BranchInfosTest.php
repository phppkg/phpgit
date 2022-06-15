<?php declare(strict_types=1);

namespace PhpGitTest\Info;

use PhpGit\Info\BranchInfos;
use PhpGitTest\BasePhpGitTestCase;

/**
 * class BranchInfosTest
 *
 * @author inhere
 * @date 2022/6/15
 */
class BranchInfosTest extends BasePhpGitTestCase
{
    public function testBranchInfos_basic(): void
    {
        $brInfos = BranchInfos::fromString(<<<TXT
  fea/new_br001 7668d4d enhance: update the group name match logic
* master        7668d4d enhance: update the group name match logic
  my_new_br     7668d4d enhance: update the group name match logic
TXT
);
        vdump($brInfos);
    }
}
