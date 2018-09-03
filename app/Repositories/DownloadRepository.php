<?php
namespace App\Repositories;

use App\Models\Download;

class DownloadRepository extends AbstractRepository
{

    function __construct(Download $model)
    {
        $this->model = $model;
    }

    public function getByHash($hash)
    {
        return $this->model->whereHash($hash)->first();
    }

    public function markAsDownloaded(Download $download)
    {
        $download->download_count += 1;
        return $download->save();
    }
} 