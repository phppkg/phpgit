<?php declare(strict_types=1);

namespace PhpGit\Info;

use PhpGit\Concern\AbstractInfo;
use Toolkit\Stdlib\Str;
use function preg_match;
use function trim;

/**
 * class StatusInfo - by run: `git status -bs`
 *
 * @author inhere
 * @date 2022/10/28
 */
class StatusInfo extends AbstractInfo
{
    public const PATTERN_FIRST = '#^([\w-]+)...([\w-]+)/(\w[\w/-]+)$#';

    /**
     * @var string current branch
     */
    public string $branch = '';

    /**
     * @var string upstream remote name
     */
    public string $upRemote = '';

    /**
     * @var string upstream branch name
     */
    public string $upBranch = '';

    /**
     * @var array
     */
    public array $renamed = [];

    /**
     * @var array
     */
    public array $deleted = [];

    /**
     * @var array
     */
    public array $modified = [];

    /**
     * @var array
     */
    public array $untracked = [];

    /**
     * parse text by: git status -bs -u
     *
     * - -b show branch info
     * - -u expand dir files
     *
     * @param string $text text by: git status -bsu
     *
     * @return self
     */
    public static function fromString(string $text): self
    {
        /*
         ## master...origin/master
         RM app/Common/GitLocal/GitFlow.php -> app/Common/GitLocal/GitFactory.php
          M app/Common/GitLocal/GitHub.php
         ?? app/Common/GitLocal/GitConst.php
          D some.file // delete
         */
        $self = new self();
        $lines = Str::split2Array($text, "\n");
        foreach ($lines as $index => $line) {
            if ($index === 0 ) {
                preg_match(self::PATTERN_FIRST, trim($line, " \t\n\r\0\x0B#"), $ms);
                if ($ms) {
                    $self->branch = $ms[1];
                    $self->upRemote = $ms[2];
                    $self->upBranch = $ms[3];
                }
                continue;
            }

            if ($line = trim($line)) {
                [$mark, $file] = Str::splitTrimmed($line, ' ', 2);
                switch ($mark) {
                    case 'RM':
                        $self->renamed[] = $file;
                        break;
                    case 'M':
                        $self->modified[] = $file;
                        break;
                    case 'D':
                        $self->deleted[] = $file;
                        break;
                    case '??':
                        $self->untracked[] = $file;
                        break;
                }
            }
        }

        return $self;
    }
}
