<?php

require 'vendor/autoload.php';

$args = $_SERVER['argv'];
array_shift($args);
$user = array_shift($args);
$command = array_shift($args);

switch ($command) {
    case null: timelineCommand($user); break;
    case '--': postCommand($user, join(' ', $args)); break;
    case 'follows': followCommand($user, join(' ', $args)); break;
    case 'wall': wallCommand($user); break;
    default: echo('Invalid command');
}

function timelineCommand(string $user)
{
    $data = fetchData();
    if (empty($data[$user]) || empty($data[$user]['posts'])) {
        echo sprintf("User %s has no posts to read.");
        return;
    }

    displayPosts($data[$user]['posts']);
}

function postCommand(string $user, string $message)
{
    $data = fetchData();
    $data = ensureUserExists($data, $user);
    $data[$user]['posts'][time()] = $message;
    saveData($data);

    echo "Message posted.\n";
}

function followCommand(string $user, string $target)
{
    $data = fetchData();
    $data = ensureUserExists($data, $user);
    $data[$user]['following'][] = $target;
    $data[$user]['following'] = array_unique($data[$user]['following']);
    saveData($data);

    echo sprintf("%s is now following %s\n", $user, $target);
}

function wallCommand(string $user)
{
    $data = fetchData();
    $data = ensureUserExists($data, $user);
    $posts = injectUserIntoMessage($data[$user]['posts'], $user);
    foreach ($data[$user]['following'] as $target) {
        if (empty($data[$target]) || empty($data[$target]['posts'])) {
            continue;
        }
        $posts += injectUserIntoMessage($data[$target]['posts'], $target);
    }
    krsort($posts);

    displayPosts($posts);
}

function fetchData()
{
    return json_decode(@file_get_contents(__DIR__ . '/data.json'), true) ?? [];
}

function saveData(array $data)
{
    file_put_contents(__DIR__ . '/data.json', json_encode($data));
}

function ensureUserExists(array $data, string $user)
{
    if (empty($data[$user])) {
        $data[$user] = [
            'posts' => [],
            'following' => [],
        ];
    }

    return $data;
}

function displayPosts(array $posts)
{
    foreach ($posts as $time => $msg) {
        $carbon = \Carbon\Carbon::parse(sprintf('@%d', $time));
        echo sprintf("%s (%s)\n", $msg, $carbon->diffForHumans());
    }
}

function injectUserIntoMessage(array $posts, string $user)
{
    return array_map(function($v) use ($user) {
        return sprintf('%s - %s', $user, $v);
    }, $posts);
}