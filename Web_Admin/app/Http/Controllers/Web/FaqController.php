<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\FaqRequest;
use App\Repositories\FaqRepository;
use App\Repositories\FaqsCategoryRepository;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    protected $faqRepo;
    protected $faqsCategoryRepo;

    public function __construct(FaqRepository $faqRepo, FaqsCategoryRepository $faqsCategoryRepo)
    {
        $this->faqRepo = $faqRepo;
        $this->faqsCategoryRepo = $faqsCategoryRepo;
    }

    public function index(Request $request)
    {
        $search = $request->search ?? null;
        $category_id = $request->category_id ?? null;

        $faqs = $this->faqRepo->getAll($category_id);
        $categories = $this->faqsCategoryRepo->getActive();

        return view('website.faq.index', compact('faqs', 'search', 'categories'));
    }


    public function create()
    {
        $categories = $this->faqsCategoryRepo->getActive();
        return view('website.faq.create', compact('categories'));
    }

    public function store(FaqRequest $request)
    {
        $validated = $request->validated();

        $content = [
            [
                'ques' => $validated['question'],
                'answer' => $validated['answer'],
            ]
        ];

        $this->faqRepo->create([
            'content' => json_encode($content),
            'faq_category_id' => $validated['category_id'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('web.faq.list')->with('success', 'تم إنشاء الأسئلة الشائعة بنجاح.');
    }

    public function edit($slug)
    {
        $faq = $this->faqRepo->findBySlug($slug);
        $categories = $this->faqsCategoryRepo->getActive();
        return view('website.faq.edit', compact('faq', 'categories'));
    }

    public function update(FaqRequest $request, $slug)
    {
        $validated = $request->validated();
        $content = [
            [
                'ques' => $validated['question'],
                'answer' => $validated['answer'],
            ]
        ];

        $this->faqRepo->updateBySlug($slug, [
            'content' => json_encode($content),
            'faq_category_id' => $validated['category_id'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('web.faq.list')->with('success', 'تم تحديث الأسئلة الشائعة بنجاح.');
    }

    public function destroy($slug)
    {
        $this->faqRepo->delete($slug);
        return back()->with('success', 'تم حذف الأسئلة الشائعة بنجاح.');
    }

    public function delete($slug)
    {
        $this->faqRepo->delete($slug);
        return redirect()->route('web.faq.list')->with('success', 'تم حذف الأسئلة الشائعة بنجاح.');
    }

    public function toggleStatus($id)
    {
        $faq = $this->faqRepo->findById($id);
        $newStatus = $faq->status == 'active' ? 'inactive' : 'active';
        $this->faqRepo->update($id, ['status' => $newStatus]);
        return back()->with('success', 'تم تحديث حالة الأسئلة الشائعة بنجاح.');
    }
}
