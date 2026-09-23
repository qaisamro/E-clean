<?php

namespace App\Repositories;

use App\Models\Website\Faq;

class FaqRepository extends Repository
{
    public function model()
    {
        return Faq::class;
    }

    public function getAll($category_id = null)
    {
        $query = $this->query()->latest();

        if ($category_id) {
            $query->where('faq_category_id', $category_id);
        }

        return $query->get();
    }

    public function search($search, $perPage = 10)
    {
        $query = $this->query()->latest();

        if ($search) {
            $query->where('content', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    public function findBySlug($slug)
    {
        return $this->query()->where('slug', $slug)->firstOrFail();
    }

    public function findById($id)
    {
        return $this->query()->findOrFail($id);
    }

    public function update($id, array $data)
    {
        $faq = $this->findById($id);
        $faq->update($data);
        return $faq;
    }

    public function create(array $data)
    {
        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = 'faq-' . time();
        }

        return $this->query()->create($data);
    }

    public function updateBySlug($slug, array $data)
    {
        $faq = $this->findBySlug($slug);
        $faq->update($data);
        return $faq;
    }

    public function delete($slug)
    {
        return $this->query()->where('slug', $slug)->delete();
    }

    public function validate($data)
    {
        $validator = validator($data, [
            'question' => 'required|string|min:3|max:500',
            'answer' => 'required|string|min:10',
            'faq_category_id' => 'required|exists:faq_categories,id',
            'status' => 'required|in:active,inactive',
        ], [
            'question.required' => 'The question field is required.',
            'question.min' => 'The question must be at least 3 characters.',
            'question.max' => 'The question may not be greater than 500 characters.',
            'answer.required' => 'The answer field is required.',
            'answer.min' => 'The answer must be at least 10 characters.',
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The selected category is invalid.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either active or inactive.',
        ]);

        if ($validator->fails()) {
            return $validator;
        }

        return true;
    }

    public function website()
    {
        $faqs = $this->getAll();
        $webSettings = app(WebsiteSettingsRepository::class)->index();
        return compact('faqs', 'webSettings');
    }
}
