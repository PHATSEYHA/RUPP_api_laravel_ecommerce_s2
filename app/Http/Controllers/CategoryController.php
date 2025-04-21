<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        // validate
        $request->validate([
            'name' => ['required', 'string', 'max:255','unique:categories,name'],
        ]);

        $category = new Category();
        $category->name = $request->input('name');
        $category->save();

        // res_success
        return $this->res_success('Save Category hx hx ', $category);
    }

    public function update(Request $request, $id)
    {
        $request->merge(['id' => $id]);
        $request->validate([
            'id' => ['required', 'integer', 'min:1', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255','unique:categories,name,'.$id],
        ]);

        // update data
        $data = $request->only(['name']);
        Category::where('id', $id)->update($data);

        // res_success
        return $this->res_success('Update Category hx hx ', Category::find($id));
    }

    public function destroy(Request $request, $id)
    {
        // validate
        $request->merge(['id' => $id]);
        $request->validate([
            'id' => ['required', 'min:1', 'exists:categories,id'],
        ]);

        // delete category
        Category::where('id', $id)->delete();

        // res_success
        return $this->res_success('Delete Category hx hx ');
    }

    public function index(Request $request)
    {
        //validate
        $request->validate([
            'scol' => ['nullable', 'string', 'in:id,name'],
            'sdir' => ['nullable', 'string', 'in:asc,desc'],
            'search' => ['nullable', 'string', 'max:50'],
            'num_pro' => ['nullable', 'integer', 'min:0', 'max:1']
        ]);

        // object and setup default data
        $category = new Category();
        $scol = $request->input('scol', 'id');
        $sdir = $request->input('sdir', 'asc');
        $numPro = $request->input('num_pro', 0);

        // add option search 
        if ($request->filled('search')) {
            $search = $request->input('search');
            $category = $category->where('name', 'like', '%' . $search . '%');
        }

        // add option count product
        if ($numPro == 1) {
            $category = $category->withCount('products');
        }

        // fetch categories
        $categories = $category->orderBy($scol, $sdir)->get();

        // res_success
        return $this->res_success('Get All Category hx hx ', CategoryResource::collection($categories));
    }
}
