<?php
require_once __DIR__ . '/Common.php'; use OSS\OssClient; use OSS\Core\OssException; $bucket = Common::getBucketName(); $ossClient = Common::getOssClient(); if (is_null($ossClient)) exit(1); $result = $ossClient->putObject($bucket, "b.file", "hi, oss"); Common::println("b.file is created"); Common::println($result['x-oss-request-id']); Common::println($result['etag']); Common::println($result['content-md5']); Common::println($result['body']); $result = $ossClient->uploadFile($bucket, "c.file", __FILE__); Common::println("c.file is created"); Common::println("b.file is created"); Common::println($result['x-oss-request-id']); Common::println($result['etag']); Common::println($result['content-md5']); Common::println($result['body']); $content = $ossClient->getObject($bucket, "b.file"); Common::println("b.file is fetched, the content is: " . $content); $content = $ossClient->putSymlink($bucket, "test-symlink", "b.file"); Common::println("test-symlink is created"); Common::println($result['x-oss-request-id']); Common::println($result['etag']); $content = $ossClient->getSymlink($bucket, "test-symlink"); Common::println("test-symlink refer to : " . $content[OssClient::OSS_SYMLINK_TARGET]); $options = array( OssClient::OSS_FILE_DOWNLOAD => "./c.file.localcopy", ); $ossClient->getObject($bucket, "c.file", $options); Common::println("b.file is fetched to the local file: c.file.localcopy"); Common::println("b.file is created"); $result = $ossClient->copyObject($bucket, "c.file", $bucket, "c.file.copy"); Common::println("lastModifiedTime: " . $result[0]); Common::println("ETag: " . $result[1]); $doesExist = $ossClient->doesObjectExist($bucket, "c.file.copy"); Common::println("file c.file.copy exist? " . ($doesExist ? "yes" : "no")); $result = $ossClient->deleteObject($bucket, "c.file.copy"); Common::println("c.file.copy is deleted"); Common::println("b.file is created"); Common::println($result['x-oss-request-id']); $doesExist = $ossClient->doesObjectExist($bucket, "c.file.copy"); Common::println("file c.file.copy exist? " . ($doesExist ? "yes" : "no")); $result = $ossClient->deleteObjects($bucket, array("b.file", "c.file")); foreach($result as $object) Common::println($object); sleep(2); unlink("c.file.localcopy"); listObjects($ossClient, $bucket); listAllObjects($ossClient, $bucket); createObjectDir($ossClient, $bucket); putObject($ossClient, $bucket); uploadFile($ossClient, $bucket); getObject($ossClient, $bucket); getObjectToLocalFile($ossClient, $bucket); copyObject($ossClient, $bucket); modifyMetaForObject($ossClient, $bucket); getObjectMeta($ossClient, $bucket); deleteObject($ossClient, $bucket); deleteObjects($ossClient, $bucket); doesObjectExist($ossClient, $bucket); getSymlink($ossClient, $bucket); putSymlink($ossClient, $bucket); function createObjectDir($ossClient, $bucket) { try { $ossClient->createObjectDir($bucket, "dir"); } catch (OssException $e) { printf(__FUNCTION__ . ": FAILED\n"); printf($e->getMessage() . "\n"); return; } print(__FUNCTION__ . ": OK" . "\n"); } function putObject($ossClient, $bucket) { $object = "oss-php-sdk-test/upload-test-object-name.txt"; $content = file_get_contents(__FILE__); $options = array(); try { $ossClient->putObject($bucket, $object, $content, $options); } catch (OssException $e) { printf(__FUNCTION__ . ": FAILED\n"); printf($e->getMessage() . "\n"); return; } print(__FUNCTION__ . ": OK" . "\n"); } function uploadFile($ossClient, $bucket) { $object = "oss-php-sdk-test/upload-test-object-name.txt"; $filePath = __FILE__; $options = array(); try { $ossClient->uploadFile($bucket, $object, $filePath, $options); } catch (OssException $e) { printf(__FUNCTION__ . ": FAILED\n"); printf($e->getMessage() . "\n"); return; } print(__FUNCTION__ . ": OK" . "\n"); } function listObjects($ossClient, $bucket) { $prefix = 'oss-php-sdk-test/'; $delimiter = '/'; $nextMarker = ''; $maxkeys = 1000; $options = array( 'delimiter' => $delimiter, 'prefix' => $prefix, 'max-keys' => $maxkeys, 'marker' => $nextMarker, ); try { $listObjectInfo = $ossClient->listObjects($bucket, $options); } catch (OssException $e) { printf(__FUNCTION__ . ": FAILED\n"); printf($e->getMessage() . "\n"); return; } print(__FUNCTION__ . ": OK" . "\n"); $objectList = $listObjectInfo->getObjectList(); $prefixList = $listObjectInfo->getPrefixList(); if (!empty($objectList)) { print("objectList:\n"); foreach ($objectList as $objectInfo) { print($objectInfo->getKey() . "\n"); } } if (!empty($prefixList)) { print("prefixList: \n"); foreach ($prefixList as $prefixInfo) { print($prefixInfo->getPrefix() . "\n"); } } } function listAllObjects($ossClient, $bucket) { for ($i = 0; $i < 100; $i += 1) { $ossClient->putObject($bucket, "dir/obj" . strval($i), "hi"); $ossClient->createObjectDir($bucket, "dir/obj" . strval($i)); } $prefix = 'dir/'; $delimiter = '/'; $nextMarker = ''; $maxkeys = 30; while (true) { $options = array( 'delimiter' => $delimiter, 'prefix' => $prefix, 'max-keys' => $maxkeys, 'marker' => $nextMarker, ); var_dump($options); try { $listObjectInfo = $ossClient->listObjects($bucket, $options); } catch (OssException $e) { printf(__FUNCTION__ . ": FAILED\n"); printf($e->getMessage() . "\n"); return; } $nextMarker = $listObjectInfo->getNextMarker(); $listObject = $listObjectInfo->getObjectList(); $listPrefix = $listObjectInfo->getPrefixList(); var_dump(count($listObject)); var_dump(count($listPrefix)); if ($nextMarker === '') { break; } } } function getObject($ossClient, $bucket) { $object = "oss-php-sdk-test/upload-test-object-name.txt"; $options = array(); try { $content = $ossClient->getObject($bucket, $object, $options); } catch (OssException $e) { printf(__FUNCTION__ . ": FAILED\n"); printf($e->getMessage() . "\n"); return; } print(__FUNCTION__ . ": OK" . "\n"); if (file_get_contents(__FILE__) === $content) { print(__FUNCTION__ . ": FileContent checked OK" . "\n"); } else { print(__FUNCTION__ . ": FileContent checked FAILED" . "\n"); } } function putSymlink($ossClient, $bucket) { $symlink = "test-samples-symlink"; $object = "test-samples-object"; try { $ossClient->putObject($bucket, $object, 'test-content'); $ossClient->putSymlink($bucket, $symlink, $object); $content = $ossClient->getObject($bucket, $symlink); } catch (OssException $e) { printf(__FUNCTION__ . ": FAILED\n"); printf($e->getMessage() . "\n"); return; } print(__FUNCTION__ . ": OK" . "\n"); if ($content == 'test-content') { print(__FUNCTION__ . ": putSymlink checked OK" . "\n"); } else { print(__FUNCTION__ . ": putSymlink checked FAILED" . "\n"); } } function getSymlink($ossClient, $bucket) { $symlink = "test-samples-symlink"; $object = "test-samples-object"; try { $ossClient->putObject($bucket, $object, 'test-content'); $ossClient->putSymlink($bucket, $symlink, $object); $content = $ossClient->getSymlink($bucket, $symlink); } catch (OssException $e) { printf(__FUNCTION__ . ": FAILED\n"); printf($e->getMessage() . "\n"); return; } print(__FUNCTION__ . ": OK" . "\n"); if ($content[OssClient::OSS_SYMLINK_TARGET]) { print(__FUNCTION__ . ": getSymlink checked OK" . "\n"); } else { print(__FUNCTION__ . ": getSymlink checked FAILED" . "\n"); } } function getObjectToLocalFile($ossClient, $bucket) { $object = "oss-php-sdk-test/upload-test-object-name.txt"; $localfile = "upload-test-object-name.txt"; $options = array( OssClient::OSS_FILE_DOWNLOAD => $localfile, ); try { $ossClient->getObject($bucket, $object, $options); } catch (OssException $e) { printf(__FUNCTION__ . ": FAILED\n"); printf($e->getMessage() . "\n"); return; } print(__FUNCTION__ . ": OK, please check localfile: 'upload-test-object-name.txt'" . "\n"); if (file_get_contents($localfile) === file_get_contents(__FILE__)) { print(__FUNCTION__ . ": FileContent checked OK" . "\n"); } else { print(__FUNCTION__ . ": FileContent checked FAILED" . "\n"); } if (file_exists($localfile)) { unlink($localfile); } } function copyObject($ossClient, $bucket) { $fromBucket = $bucket; $fromObject = "oss-php-sdk-test/upload-test-object-name.txt"; $toBucket = $bucket; $toObject = $fromObject . '.copy'; $options = array(); try { $ossClient->copyObject($fromBucket, $fromObject, $toBucket, $toObject, $options); } catch (OssException $e) { printf(__FUNCTION__ . ": FAILED\n"); printf($e->getMessage() . "\n"); return; } print(__FUNCTION__ . ": OK" . "\n"); } function modifyMetaForObject($ossClient, $bucket) { $fromBucket = $bucket; $fromObject = "oss-php-sdk-test/upload-test-object-name.txt"; $toBucket = $bucket; $toObject = $fromObject; $copyOptions = array( OssClient::OSS_HEADERS => array( 'Cache-Control' => 'max-age=60', 'Content-Disposition' => 'attachment; filename="xxxxxx"', ), ); try { $ossClient->copyObject($fromBucket, $fromObject, $toBucket, $toObject, $copyOptions); } catch (OssException $e) { printf(__FUNCTION__ . ": FAILED\n"); printf($e->getMessage() . "\n"); return; } print(__FUNCTION__ . ": OK" . "\n"); } function getObjectMeta($ossClient, $bucket) { $object = "oss-php-sdk-test/upload-test-object-name.txt"; try { $objectMeta = $ossClient->getObjectMeta($bucket, $object); } catch (OssException $e) { printf(__FUNCTION__ . ": FAILED\n"); printf($e->getMessage() . "\n"); return; } print(__FUNCTION__ . ": OK" . "\n"); if (isset($objectMeta[strtolower('Content-Disposition')]) && 'attachment; filename="xxxxxx"' === $objectMeta[strtolower('Content-Disposition')] ) { print(__FUNCTION__ . ": ObjectMeta checked OK" . "\n"); } else { print(__FUNCTION__ . ": ObjectMeta checked FAILED" . "\n"); } } function deleteObject($ossClient, $bucket) { $object = "oss-php-sdk-test/upload-test-object-name.txt"; try { $ossClient->deleteObject($bucket, $object); } catch (OssException $e) { printf(__FUNCTION__ . ": FAILED\n"); printf($e->getMessage() . "\n"); return; } print(__FUNCTION__ . ": OK" . "\n"); } function deleteObjects($ossClient, $bucket) { $objects = array(); $objects[] = "oss-php-sdk-test/upload-test-object-name.txt"; $objects[] = "oss-php-sdk-test/upload-test-object-name.txt.copy"; try { $ossClient->deleteObjects($bucket, $objects); } catch (OssException $e) { printf(__FUNCTION__ . ": FAILED\n"); printf($e->getMessage() . "\n"); return; } print(__FUNCTION__ . ": OK" . "\n"); } function doesObjectExist($ossClient, $bucket) { $object = "oss-php-sdk-test/upload-test-object-name.txt"; try { $exist = $ossClient->doesObjectExist($bucket, $object); } catch (OssException $e) { printf(__FUNCTION__ . ": FAILED\n"); printf($e->getMessage() . "\n"); return; } print(__FUNCTION__ . ": OK" . "\n"); var_dump($exist); } 