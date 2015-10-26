<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

ini_set('display_errors', 'on');
error_reporting(E_ALL);

$getopt = new Getopt(
    [
        new Option('h', 'host', Getopt::REQUIRED_ARGUMENT),
        new Option('u', 'username', Getopt::REQUIRED_ARGUMENT),
        new Option('p', 'password', Getopt::REQUIRED_ARGUMENT),
        new Option('q', 'query', Getopt::REQUIRED_ARGUMENT),
        new Option('f', 'pattern', Getopt::OPTIONAL_ARGUMENT),
        new Option('l', 'limit', Getopt::OPTIONAL_ARGUMENT),
        new Option('d', 'dry-run', Getopt::OPTIONAL_ARGUMENT),
    ]
);

try {
    $getopt->parse();

    $query = $getopt['query'];
    $limit = $getopt['limit'] ? $getopt['limit'] : 100;
    $dryRun = $getopt['dry-run'] ? true : false;

    $youtrack = new YouTrack\Connection(
        $getopt['host'],
        $getopt['username'],
        $getopt['password']
    );

    $pattern = $getopt['pattern'];

    echo 'Executing query: "' . $query . '"' . PHP_EOL;

    $issues = $youtrack->getIssuesByFilter($query, null, $limit);

    echo count($issues) . ' issues found' . PHP_EOL;

    foreach ($issues as $issue) {

        $attachments = $issue->getAttachments();
        $ac = count($attachments);
        $ts = substr($issue->created, 0, -3);
        $created = new \DateTime('@' . $ts);
        echo $issue->getId() . ', created: ' . $created->format('Y-m-d H:i:s') . ': ' . $ac . ' attachments.' . PHP_EOL;
        foreach ($attachments as $attachment) {
            echo '    ID: ' . $attachment->getId() . ' (' . $attachment->getName() . ')';
            if (!$pattern || preg_match('/' . $pattern . '/', $attachment->getName())) {
                if ($dryRun) {
                    $success = $youtrack->deleteAttachment($issue, $attachment);
                    if ($success) {
                        echo ' deleted';
                    } else {
                        echo ' NOT deleted';
                    }
                } else {
                    echo ' deleted (not really!)';
                }
            } else {
                echo ' skipped';
            }
            echo PHP_EOL;
        }
        echo PHP_EOL;
    }
    exit(0);

} catch (UnexpectedValueException $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo $getopt->getHelpText();
    exit(1);
}
