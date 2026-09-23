<?php

namespace App\Repositories;

use App\Models\Website\FaqsCategory;

class FaqsCategoryRepository extends Repository
{
    public function model()
    {
        return FaqsCategory::class;
    }

    public function getAll()
    {
        return $this->query()->latest()->get();
    }

    public function getActive()
    {
        return $this->query()->where('status', 'active')->latest()->get();
    }

    public function findById($id)
    {
        return $this->query()->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->query()->create($data);
    }

    public function update($id, array $data)
    {
        $category = $this->findById($id);
        $category->update($data);
        return $category;
    }

    public function delete($id)
    {
        $category = $this->findById($id);

        // Check if category has FAQs
        if ($category->faqs()->count() > 0) {
            return false;
        }

        return $category->delete();
    }
}
