<?php declare(strict_types=1);

use PhpGit\Changelog\ChangeLogUtil;
use PhpGit\Changelog\Filter\KeywordsFilter;
use PhpGit\Changelog\Formatter\GithubReleaseFormatter;
use PhpGit\Changelog\Formatter\SimpleFormatter;
use PhpGit\Changelog\GitChangeLog;
use PhpGit\Repo;
use Toolkit\Cli\Cli;
use Toolkit\Cli\CliApp;
use Toolkit\Stdlib\Str;

require dirname(__DIR__) . '/test/bootstrap.php';

// run: php bin/chlog.php -h
$chlog = CliApp::new('chlog', 'auto generate change logs from git log');
$chlog
    ->addOpt('style', 's', 'The style for generate for changelog. allow: ghr,md,text')
    ->addOpt('output', 'o', 'export generated changelog to the output')
    ->addOpt('exclude', 'e', 'exclude contains given sub-string. multi by comma split.')
    ->addOpt('repo-url', '', 'The git repo URL address. default will get from git origin remote url')
    ->addOpt('fetch-tags', '', 'Update repo tags list by `git fetch --tags`', false)
    ->addOpt('no-merges', 'nm', 'No contains merge request logs', false)
    ->addOpt('with-author', '', 'Display commit author name', false)
    ->addArg('old', 'old version or hash. eg: v1.0.2, prev, last')
    ->addArg('new', 'new version or hash. eg: v1.0.3, last, head')
    ->setHelp(<<<'TXT'

<ylw0>Examples:</ylw0>
 {{fullCmd}} -s ghr v0.2.1 head
 {{fullCmd}} -s ghr prev last
TXT
);

$chlog->setHandler(function (CliApp $app) : int {
    $repo = Repo::new();
    if ($app->getOpt('fetch-tags')) {
        $fetch = $repo->newCmd('fetch', '--tags');
        // fix: fetch tags history error on github action.
        // see https://stackoverflow.com/questions/4916492/git-describe-fails-with-fatal-no-names-found-cannot-describe-anything
        // $fetch->addIf('--unshallow', $app->getOpt('unshallow'));
        $fetch->addArgs('--force');
        $fetch->runAndPrint();
    }

    $builder = $repo->newCmd('log');

    // git log v1.0.7...v1.0.8 --pretty=format:'<project>/commit/%H %s' --reverse
    // git log v1.0.7...HEAD --pretty=format:'<li> <a href="https://github.com/inhere/<project>/commit/%H">view commit &bull;</a> %s</li> ' --reverse
    $oldVersion = ChangeLogUtil::getVersion($app->getArg('old'));
    $newVersion = ChangeLogUtil::getVersion($app->getArg('new'));

    $logFmt = GitChangeLog::LOG_FMT_HS;
    if ($app->getOpt('with-author')) {
        $logFmt = GitChangeLog::LOG_FMT_HSA;
    }

    if ($oldVersion && $newVersion) {
        $builder->add("$oldVersion...$newVersion");
    }

    Cli::info('collect git log output contents');
    $builder->addf('--pretty=format:"%s"', $logFmt);

    // $b->addIf("--exclude $exclude", $exclude);
    // $b->addIf('--abbrev-commit', $abbrevID);
    $noMerges = $app->getOpt('no-merges');
    $builder
        ->addIf('--no-merges', $noMerges)
        ->add('--reverse')
        ->run();

    if (!$gitLog = $builder->getOutput()) {
        Cli::warn('empty git log output, quit generate');
        return 0;
    }

    $repoUrl = $app->getOpt('repo-url');
    if (!$repoUrl) {
        $rmtInfo = $repo->getRemoteInfo();
        $repoUrl = $rmtInfo->getHttpUrl();
    }

    Cli::info('project repo URL: ' . $repoUrl);
    Cli::info('config the change log generator');

    $gcl = GitChangeLog::new($gitLog);
    $gcl->setLogFormat($logFmt);
    $gcl->setRepoUrl($repoUrl);

    if ($exclude = $app->getOpt('exclude')) {
        $keywords = Str::explode($exclude, ',');
        $gcl->addItemFilter(new KeywordsFilter($keywords));
    }

    $style = $app->getOpt('style');
    if ($style === 'ghr' || $style === 'gh-release') {
        $gcl->setItemFormatter(new GithubReleaseFormatter());
    } elseif ($style === 'simple') {
        $gcl->setItemFormatter(new SimpleFormatter());
    }

    // parse and generate.
    Cli::info('parse logs and generate changelog');
    $gcl->generate();

    $outFile = $app->getOpt('output');
    Cli::info('total collected changelog number: ' . $gcl->getLogCount());

    if ($outFile && $outFile !== 'stdout') {
        Cli::info('export changelog to file: ' . $outFile);
        $gcl->export($outFile);
        Cli::success('Completed');
    } else {
        println();
        println($gcl->getChangelog());
    }

    return 0;
});

$chlog->run();
