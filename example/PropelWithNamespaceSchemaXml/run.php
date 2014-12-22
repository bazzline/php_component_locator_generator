<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-12-22
 */

$pathToGenerator = realpath(__DIR__ . '/../../bin/generateLocator.php');

$command = $pathToGenerator . ' ' . __DIR__ . '/configuration.php';
passthru($command);