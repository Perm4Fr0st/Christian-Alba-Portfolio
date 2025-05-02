<?php

namespace App\Http\Controllers\Skill;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::paginate(10);

        return Inertia::render('Skills/Index', [
            'skills' => $skills
        ]);
    }

    public function create()
    {
        return Inertia::render("Skills/Create");
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'fileUpload' => 'required|image|max:30720',
        ]);

        $filePath = $request->file('fileUpload')->store('uploads', 'public');

        Skill::create([
            'name' => $request['name'],
            'image' => $filePath,
        ]);
    }

    public function edit(string $id)
    {
        $skills = Skill::find($id);

        return Inertia::render("Skills/Edit", [
            "skills" => $skills
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'fileUpload' => 'nullable|image|max:30720',
        ]);

        $skills = Skill::find($id);

        if ($request->hasFile('fileUpload')) {
            if ($skills->image && file_exists(storage_path('app/public/' . $skills->image))) {
                unlink(storage_path('app/public/' . $skills->image));
            }
            $filePath = $request->file('fileUpload')->store('uploads', 'public');
            $skills->image = $filePath;
        }

        $skills->name = $request->name;
        $skills->save();

        return redirect('/skills');
    }

    public function delete(string $id)
    {
        $skill = Skill::findOrFail($id);
        if ($skill->image && file_exists(storage_path('app/public/' . $skill->image))) {
            unlink(storage_path('app/public/' . $skill->image));
        }

        $skill->delete();

        return redirect('/skills');
    }
}
