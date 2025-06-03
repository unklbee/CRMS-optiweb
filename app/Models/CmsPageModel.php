<?php
// app/Models/CmsPageModel.php
namespace App\Models;

use CodeIgniter\Model;

class CmsPageModel extends Model
{
    protected $table = 'cms_pages';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 'slug', 'content', 'meta_title', 'meta_description',
        'status', 'featured_image', 'template', 'created_by'
    ];

    protected $validationRules = [
        'title' => 'required|min_length[2]|max_length[200]',
        'slug' => 'required|min_length[2]|max_length[200]|is_unique[cms_pages.slug,id,{id}]',
        'content' => 'permit_empty',
        'status' => 'required|in_list[draft,published]'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];

    protected function generateSlug(array $data): array
    {
        if (isset($data['data']['title']) && empty($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['title'], '-', true);
        }
        return $data;
    }

    public function getPublishedPages(): array
    {
        return $this->where('status', 'published')->findAll();
    }

    public function getPageBySlug($slug): object|array|null
    {
        return $this->where('slug', $slug)
            ->where('status', 'published')
            ->first();
    }
}
