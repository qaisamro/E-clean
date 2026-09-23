<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\FaqsCategoryRequest;
use App\Repositories\FaqsCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebFaqCategoryController extends Controller
{
    protected $faqsCategoryRepo;

    public function __construct(FaqsCategoryRepository $faqsCategoryRepo)
    {
        $this->faqsCategoryRepo = $faqsCategoryRepo;
    }

    public function index()
    {
        $categories = $this->faqsCategoryRepo->getAll();
        return view('website.faq-category.index', compact('categories'));
    }

    public function create()
    {
        return view('website.faq-category.create');
    }

    public function store(FaqsCategoryRequest $request)
    {
        $validated = $request->validated();

        $num = 1;

        $slug = Str::slug($validated['name']);
        $existingCategory = $this->faqsCategoryRepo->query()->where('slug', $slug)->first();
        if ($existingCategory) {
            $slug .= '-' . $num;
            while ($this->faqsCategoryRepo->query()->where('slug', $slug)->exists()) {
                $num++;
                $slug = Str::slug($validated['name']) . '-' . $num;
            }
        }

        $this->faqsCategoryRepo->create([
            'name' => $validated['name'],
            'slug' => $slug,
            'status' => $validated['status'],
        ]);

        return redirect()->route('web.faq.category.index')->with('success', 'FAQ Category created successfully');
    }

    public function edit($id)
    {
        $category = $this->faqsCategoryRepo->findById($id);
        return view('website.faq-category.edit', compact('category'));
    }

    public function update(FaqsCategoryRequest $request, $id)
    {
        $validated = $request->validated();

        $this->faqsCategoryRepo->update($id, [
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'status' => $validated['status'],
        ]);

        return redirect()->route('web.faq.category.index')->with('success', 'FAQ Category updated successfully');
    }

    public function destroy($id)
    {
        $deleted = $this->faqsCategoryRepo->delete($id);
        if ($deleted) {
            return redirect()->route('web.faq.category.index')->with('success', 'FAQ Category deleted successfully');
        }
        return redirect()->route('web.faq.category.index')->with('error', 'Cannot delete category with existing FAQs');
    }

    public function delete($id)
    {
        $deleted = $this->faqsCategoryRepo->delete($id);
        if ($deleted) {
            return redirect()->route('web.faq.category.index')->with('success', 'FAQ Category deleted successfully');
        }
        return redirect()->route('web.faq.category.index')->with('error', 'Cannot delete category with existing FAQs');
    }


    public function toggleStatus($id)
    {
        $category = $this->faqsCategoryRepo->findById($id);
        $newStatus = $category->status == 'active' ? 'inactive' : 'active';
        $this->faqsCategoryRepo->update($id, ['status' => $newStatus]);
        return back()->with('success', 'FAQ Category status updated successfully');
    }
}
