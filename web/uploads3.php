<?php

// This file demonstrates file upload to an S3 bucket. This is for using file upload via a
// file compared to just having the link. If you are doing it via link, refer to this:
// https://gist.github.com/keithweaver/08c1ab13b0cc47d0b8528f4bc318b49a
//
// You must setup your bucket to have the proper permissions. To learn how to do this
// refer to:
// https://github.com/keithweaver/python-aws-s3
// https://www.youtube.com/watch?v=v33Kl-Kx30o

// I will be using composer to install the needed AWS packages.
// The PHP SDK:
// https://github.com/aws/aws-sdk-php
// https://packagist.org/packages/aws/aws-sdk-php
//
// Run:$ composer require aws/aws-sdk-php
require '../vendor/autoload.php';

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

// Connect to AWS

/**
 * Función para subir una imagen a Amazon S3.
 * @return void|S3Exception void si todo va bien, o una excepcion si falla algo.
 */
function uploadImagen($model)
{
    // AWS Info
    $bucketName = 'ucomment';
    $IAM_KEY = getenv('IAM_KEY');
    $IAM_SECRET = getenv('IAM_SECRET');

    //Comprueba si tiene foto antigua para eliminarla
    if (!empty($model->getOldAttribute('url_img'))) {
        $keyName = basename($model->getOldAttribute('url_img'));
        var_dump($model->getOldAttributes());

        $s3 = S3Client::factory(
            [
                'credentials' => [
                    'key' => $IAM_KEY,
                    'secret' => $IAM_SECRET,
                ],
                'version' => 'latest',
                'region' => 'eu-west-1',
            ]
        );
    }

    try {
        // You may need to change the region. It will say in the URL when the bucket is open
        // and on creation.
        $s3 = S3Client::factory(
            [
                'credentials' => [
                    'key' => $IAM_KEY,
                    'secret' => $IAM_SECRET,
                ],
                'version' => 'latest',
                'region' => 'eu-west-1',
            ]
        );
    } catch (Exception $e) {
        // We use a die, so if this fails. It stops here. Typically this is a REST call so this would
        // return a json object.
        die('Error: ' . $e->getMessage());
    }

    // For this, I would generate a unqiue random string for the key name. But you can do whatever.
    var_dump($_FILES['Usuarios']['tmp_name']['url_img']);

    $keyName = basename($_FILES['Usuarios']['name']['url_img']);
    $pathInS3 = 'https://s3.eu-west-3.amazonaws.com/' . $bucketName . '/' . $keyName;
    // Add it to S3
    try {
        // Uploaded:
        $file = $_FILES['Usuarios']['tmp_name']['url_img'];
        $s3->putObject(
            [
                'Bucket' => $bucketName,
                'Key' => $keyName,
                'SourceFile' => $file,
                'ACL' => 'public-read',
                'StorageClass' => 'REDUCED_REDUNDANCY',
            ]
        );
        $model->url_img = $pathInS3;
    } catch (S3Exception $e) {
        die('Error:' . $e->getMessage());
    } catch (Exception $e) {
        die('Error:' . $e->getMessage());
    }
}

function uploadComentario($model)
{
    // AWS Info
    $bucketName = 'ucomment';
    $IAM_KEY = getenv('IAM_KEY');
    $IAM_SECRET = getenv('IAM_SECRET');

    //Comprueba si tiene foto antigua para eliminarla
    if (!empty($model->getOldAttribute('url_img'))) {
        $keyName = basename($model->getOldAttribute('url_img'));
        var_dump($model->getOldAttributes());

        $s3 = S3Client::factory(
            [
                'credentials' => [
                    'key' => $IAM_KEY,
                    'secret' => $IAM_SECRET,
                ],
                'version' => 'latest',
                'region' => 'eu-west-1',
            ]
        );
    }

    try {
        // You may need to change the region. It will say in the URL when the bucket is open
        // and on creation.
        $s3 = S3Client::factory(
            [
                'credentials' => [
                    'key' => $IAM_KEY,
                    'secret' => $IAM_SECRET,
                ],
                'version' => 'latest',
                'region' => 'eu-west-1',
            ]
        );
    } catch (Exception $e) {
        // We use a die, so if this fails. It stops here. Typically this is a REST call so this would
        // return a json object.
        die('Error: ' . $e->getMessage());
    }

    // For this, I would generate a unqiue random string for the key name. But you can do whatever.
    var_dump($_FILES['Comentarios']['tmp_name']['url_img']);

    $keyName = basename($_FILES['Comentarios']['name']['url_img']);
    $pathInS3 = 'https://s3.eu-west-3.amazonaws.com/' . $bucketName . '/' . $keyName;
    // Add it to S3
    try {
        // Uploaded:
        $file = $_FILES['Comentarios']['tmp_name']['url_img'];
        $s3->putObject(
            [
                'Bucket' => $bucketName,
                'Key' => $keyName,
                'SourceFile' => $file,
                'ACL' => 'public-read',
                'StorageClass' => 'REDUCED_REDUNDANCY',
            ]
        );
    } catch (S3Exception $e) {
        die('Error:' . $e->getMessage());
    } catch (Exception $e) {
        die('Error:' . $e->getMessage());
    }
}

function s3GetUrl($name, $bucket)
{
    $credentials = new \Aws\Credentials\Credentials(getenv('IAM_KEY'), getenv('IAM_SECRET'));

    $s3Client = new S3Client([
        'version' => 'latest',
        'region' => 'eu-west-1',
        'credentials' => $credentials
    ]);

    return $s3Client->getObjectUrl($bucket, $name);
}
