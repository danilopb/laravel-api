<?php

namespace App\Services;

use App\Filters\PostFilter;
use App\Helpers\FileHelper;
use App\Models\Post;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostServiceImpl extends GenericServiceImpl implements IPostService
{
    public function __construct()
    {
        parent::__construct(new Post(), new PostFilter());
    }

    /**
     * Store data
     *
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function create(array $data): Model
    {
        try {
            \DB::beginTransaction();
            $file = $data['file'];
            $data['slug'] = $this->generateUniqueSlug($data['title']);
            $data['created_by'] = auth()->user()->id;
            $data['file'] = FileHelper::generateName($file->getClientOriginalExtension());
            $post = $this->model->create($data);
            //Save file
            Storage::putFileAs($this->model->storage_name, $file, $data['file']);
            \DB::commit();
            return $post;
        } catch (Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update data
     * @param Model $model
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function update(Model $model, array $data): Model
    {
        try {
            \DB::beginTransaction();
            $file = $data['file'] ?? null;
            $oldFileName = $model->file;
            if (isset($data['slug'])) {
                $slug = $data['slug'];
                $data['slug'] = !$this->existSlug($slug) ? $slug : $this->generateUniqueSlug($slug);
            }
            if ($file) {
                $data['file'] = FileHelper::generateName($file->getClientOriginalExtension());
            }
            $model->update($data);
            //Save file
            if ($file) {
                Storage::putFileAs($this->model->storage_name, $file, $data['file']);
                Storage::delete($this->model->storage_name."/".$oldFileName);
            }
            \DB::commit();
            return $model;
        } catch (Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    /**
     * Store data
     *
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function bulk(array $data): bool
    {
        try {
            \DB::beginTransaction();
            $rows = count($data['title']);
            $userId = auth()->user()->id;
            for ($i = 0; $i < $rows ;$i++) {
                $file = $data['file'][$i];
                $fileName = FileHelper::generateName($file->getClientOriginalExtension());
                Post::create([
                    'title' => $data['title'][$i],
                    'content' => $data['content'][$i],
                    'slug' => $this->generateUniqueSlug($data['title'][$i]),
                    'file' => $fileName,
                    'created_by' => $userId
                ]);
                Storage::putFileAs($this->model->storage_name, $file, $fileName);
            }
            \DB::commit();
            return true;
        } catch (Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    public function existSlug($slug): bool
    {
         return (bool) $this->model->where('slug', $slug)->first();
    }

    /**
     * Generate a unique slug
     * @param $title
     * @return string
     */
    public function generateUniqueSlug($title): string
    {
        $slug = Str::slug($title);
        // if exist
        if ($this->existSlug($slug)) {
            for ($i=1; $i < 100; $i++) {
                $newSlug = $slug."-".$i;
                // if not exist
                if (!$this->existSlug($newSlug)) {
                    $slug = $newSlug;
                    break;
                }
            }
        }
        return $slug;
    }
}
