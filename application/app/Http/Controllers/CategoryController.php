<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

use App\Http\Requests;

class CategoryController extends Controller
{

    /**
     * Create new job
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {

        /*$this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'salary' => 'required',
            'max_clients_count' => 'required',
            'category_id' => 'required',
        ]);*/


        Category::create([
            'title' => $request->title,
        ]);

        return redirect('/admin/categories');
    }

    public function update(Request $request){
        $category = Category::find($request->id);

        $category->fill($request->all());
        $category->save();

        die('success');
    }


    /**
     * Delete category
     *
     * @param  int $category_id
     */
    public function destroy($category_id, $new_catefory_id)
    {
        $category = Category::findOrFail($category_id);
        $newCategory = Category::findOrFail($new_catefory_id);
        $jobs = $category->jobs()->get();
        if($jobs->count() > 0){
            foreach ($jobs as $job){
                $job->category_id = $newCategory->id;
                $job->save();
            }
        }

        $category->delete();
        $category = Category::find($category_id);

        if(!isset($category->id)){
            die('success');
        }
        else{
            die('fail');
        }
    }
}
