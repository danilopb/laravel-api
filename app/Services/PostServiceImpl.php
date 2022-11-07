<?php

namespace App\Services;

use App\Filters\PostFilter;
use App\Models\Post;
use Exception;
use Illuminate\Database\Eloquent\Model;
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
     */
    public function create(array $data): Model
    {
        $data['slug'] = $this->generateUniqueSlug($data['title']);
        $data['created_by'] = auth()->user()->id;
        return $this->model->create($data);
    }

    /**
     * Update data
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function update(Model $model, array $data): Model
    {
        if (isset($data['slug'])) {
            $slug = $data['slug'];
            $data['slug'] = !$this->existSlug($slug) ? $slug : $this->generateUniqueSlug($slug);
        }

        $model->update($data);
        return $model;
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
                $this->create([
                    'title' => $data['title'][$i],
                    'content' => $data['content'][$i],
                    'slug' => $this->generateUniqueSlug($data['title'][$i]),
                    'created_by' => $userId
                ]);
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
