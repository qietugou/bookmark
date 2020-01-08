<?php
include './src/Bookmark.php';

use Qietugou\Bookmark\Bookmark;

$book = new Bookmark('path');
$book->getBookmarks()->toResult();