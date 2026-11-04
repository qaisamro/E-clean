<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Repositories\AreaRepository;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public $areaRepo;
    public function __construct(AreaRepository $areaRepository)
    {
        $this->areaRepo = $areaRepository;
    }

    public function index()
    {
        $areas = $this->areaRepo->getAll();
        return view('area.index', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:areas,name']);

        $this->areaRepo->storeByRequest($request);
        return redirect()->route('areas.index')->with('success', 'تم الإنشاء بنجاح');
    }

    public function update(Request $request, Area $area)
    {
        $request->validate(['name' => 'required|unique:areas,name,' . $area->id]);

        $this->areaRepo->updateByRequest($area, $request);
        return redirect()->route('areas.index')->with('success', 'تم التحديث بنجاح');
    }

    public function toggle(Area $area)
    {
        $this->areaRepo->toggleStaus($area);
        return redirect()->route('areas.index')->with('success', 'تم تحديث الحالة بنجاح');
    }

    public function delete(Area $area)
    {
        $area->delete();
        return back()->with('success', 'تم الحذف بنجاح');
    }
}
