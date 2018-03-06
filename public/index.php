<?php

$dataFile = 'bbs.dat';

// CSRF対策

session_start();

function setToken() {
    $token = sha1(uniqid(mt_rand(), true));
    $_SESSION['token'] = $token;
}

function checkToken() {
    if (empty($_SESSION['token']) || ($_SESSION['token'] != $_POST['token'])) {
        echo "不正なPOSTが行われました！";
        exit;
    }
}

function h($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_POST['message']) &&
    isset($_POST['user'])) {

    checkToken();

    $message = trim($_POST['message']);
    $user = trim($_POST['user']);

    if ($message !== '') {

        $user = ($user === '') ? 'ななしさん' : $user;

        $message = str_replace("\t", ' ', $message);
        $user = str_replace("\t", ' ', $user);

        $postedAt = date('Y-m-d H:i:s');

        $newData = $message . "\t" . $user . "\t" . $postedAt. "\n";

        $fp = fopen($dataFile, 'a');
        fwrite($fp, $newData);
        fclose($fp);
    }
} else {
    setToken();
}

$posts = file($dataFile, FILE_IGNORE_NEW_LINES);

$posts = array_reverse($posts);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>THE BBS</title>
</head>
<body>
    <h1>THE BBS</h1>
    <form action="" method="post">
        user: <input type="text" name="user">
        message: <input type="text" name="message">
        <input type="submit" value="post!">
        <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
    </form>
    <h2>index（<?php echo count($posts); ?>posts）</h2>
    <ul>
        <?php if (count($posts)) : ?>
            <?php foreach ($posts as $post) : ?>
            <?php list($message, $user, $postedAt) = explode("\t", $post); ?>
                <li><?php echo h($message); ?> (<?php echo h($user); ?>) - <?php echo h($postedAt); ?></li>
            <?php endforeach; ?>
        <?php else : ?>
            <li>まだ投稿はありません。</li>
        <?php endif; ?>
    </ul>
</body>
</html>
