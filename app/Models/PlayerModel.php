<?php

namespace App\Models;

class PlayerModel
{
    /**
     * .
     */
    public function getOne()
    {
    }

    /**
     * .
     */
    public function getAll($limit = 0, $offset = 0, $filters = [], $directory = '/')
    {
        // echo '<pre>';
        // print_r($filters);
        // echo '</pre>';
        // die();
        $result = [
            'recordsFiltered' => 0,
            'recordsTotal' => 0,
            'data' => []
        ];

        if (!is_dir($directory)) {
            return $result;
        }

        $allowedFileType = ['mp3', 'wav'];

        //Count files in $directory
        $fileCount = 0;
        $iterator = new \DirectoryIterator($directory);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile()) {
                $arrName = explode('.', $fileInfo->getFileName());
                if (in_array($arrName[1], $allowedFileType)) {
                    $fileCount++;
                }
            }
        }
        $result['recordsTotal'] = $fileCount;

        //Get list file
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $i = 0;
        foreach ($files as $file) {
            if ($i < $offset) {
                $i++;
                continue;
            }

            if ($file->isFile()) {
                $arrName = $file->getFileName();
                $arrName = explode('.', $arrName);

                if (!in_array($arrName[1], $allowedFileType)) {
                    $i++;
                    continue;
                }

                $arrName = $arrName[0];
                $arrName = explode(' ', $arrName);

                $dt = $arrName[0] . ' ' . str_replace('-', ':', $arrName[1]);
                $dt = new \DateTime($dt, new \DateTimeZone('UTC'));
                
                $dtStart = new \DateTime($filters['time_start'], new \DateTimeZone('UTC'));
                $dtEnd = new \DateTime($filters['time_end'], new \DateTimeZone('UTC'));

                $inTimes = ($dt > $dtStart) && ($dt <= $dtEnd);

                if (isset($filters['channel_name'])) {
                    if (isset($filters['file_name'])) { // filter by: channel_name & file_name
                        if (($arrName[3] == $filters['channel_name'])
                          && (strpos($arrName[0] . ' ' . $arrName[1], $filters['file_name']) !== false)
                          && $inTimes) {
                            $result['data'][] = [
                                'file_name' => $file->getFileName(),
                                'path' => base_url("recorded/{$file->getFileName()}")
                            ];
                            $i++;
                            continue;
                        }
                    } else { // filter by: channel_name
                        if ((intval($arrName[3]) == intval($filters['channel_name'])) && $inTimes) {
                            $result['data'][] = [
                                'file_name' => $file->getFileName(),
                                'path' => base_url("recorded/{$file->getFileName()}")
                            ];
                        }
                    }
                    $i++;
                    continue;
                }

                if (isset($filters['file_name'])) {
                    if (isset($filters['channel_name'])) { // filter by: file_name & channel_name
                        if ((strpos($arrName[0] . ' ' . $arrName[1], $filters['file_name']) !== false)
                          && ($arrName[3] == $filters['channel_name'])
                          && $inTimes) {
                            $result['data'][] = [
                                'file_name' => $file->getFileName(),
                                'path' => base_url("recorded/{$file->getFileName()}")
                            ];
                            $i++;
                            continue;
                        }
                    } else { // filter by: file_name
                        if ((strpos($arrName[0] . ' ' . $arrName[1], $filters['file_name']) !== false) && $inTimes) {
                            $result['data'][] = [
                                'file_name' => $file->getFileName(),
                                'path' => base_url("recorded/{$file->getFileName()}")
                            ];
                        }
                    }
                    $i++;
                    continue;
                }

                if($inTimes){
                    $result['data'][] = [
                        'file_name' => $file->getFileName(),
                        'path' => base_url("recorded/{$file->getFileName()}")
                    ];
                }               
                $i++;
            }
            if (count($result['data']) == $limit) {
                break;
            }
        }

        /**
         * count all files with allowed extension
         */
        foreach ($files as $file) {
            if ($file->isFile()) {
                $arrName = $file->getFileName();
                $arrName = explode('.', $arrName);

                if (!in_array($arrName[1], $allowedFileType)) {
                    continue;
                }

                $arrName = $arrName[0];
                $arrName = explode(' ', $arrName);

                $dt = $arrName[0] . ' ' . str_replace('-', ':', $arrName[1]);
                $dt = new \DateTime($dt, new \DateTimeZone('UTC'));
                
                $dtStart = new \DateTime($filters['time_start'], new \DateTimeZone('UTC'));
                $dtEnd = new \DateTime($filters['time_end'], new \DateTimeZone('UTC'));

                $inTimes = ($dt > $dtStart) && ($dt <= $dtEnd);

                if (isset($filters['channel_name'])) {
                    if (isset($filters['file_name'])) { // filter by: channel_name & file_name
                        if ($arrName[3] == $filters['channel_name']
                          && (strpos($arrName[0] . ' ' . $arrName[1], $filters['file_name']) !== false)
                          && $inTimes) {
                            $result['recordsFiltered']++;
                            continue;
                        }
                    } else { // filter by: channel_name
                        if (($arrName[3] == $filters['channel_name']) && $inTimes) {
                            $result['recordsFiltered']++;
                        }
                    }
                    continue;
                }
                if (isset($filters['file_name'])) {
                    if (isset($filters['channel_name'])) { // filter by: file_name & channel_name
                        if ((strpos($arrName[0] . ' ' . $arrName[1], $filters['file_name']) !== false)
                          && ($arrName[3] == $filters['channel_name'])
                          && $inTimes) {
                            $result['recordsFiltered']++;
                            continue;
                        }
                    } else { // filter by: file_name
                        if ((strpos($arrName[0] . ' ' . $arrName[1], $filters['file_name']) !== false) && $inTimes) {
                            $result['recordsFiltered']++;
                        }
                    }
                    continue;
                }
            }
            if($inTimes){
                $result['recordsFiltered']++;
            }
            if (count($result) == $limit) {
                break;
            }
        }

        // if (!isset($filters['channel_name']) && !isset($filters['file_name'])) {
        //     $result['recordsFiltered'] = $fileCount;
        // }

        return $result;
    }
}
