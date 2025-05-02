<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::paginate(10);

        return Inertia::render('Projects/Index', [
            'projects' => $projects
        ]);
    }

    public function create()
    {
        $skills = Skill::select('id', 'name')->get();

        return Inertia::render('Projects/Create', [
            'skills' => $skills,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'skills' => 'array|required',
            'skills.*' => 'integer|exists:skills,id',
            'name' => 'required|string|max:255',
            'fileUpload' => 'required|image|max:30720',
            'link' => 'required|string',
        ]);

        $filePath = $request->file('fileUpload')->store('uploads', 'public');

        $project = Project::create([
            'name' => $request['name'],
            'link' => $request['link'],
            'image' => $filePath,
        ]);
        $project->skills()->attach($request->input('skills'));
    }

    public function edit(string $id)
    {
        $project = Project::with('skills')->findOrFail($id);
        $skills = Skill::select('id', 'name')->get();
    
        return Inertia::render("Projects/Edit", [
            "projects" => $project,
            "skills" => $skills
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|string',
            'fileUpload' => 'nullable|image|max:30720',
        ]);

        $project = Project::with('skills')->findOrFail($id);

        if ($request->hasFile('fileUpload')) {
            if ($project->image && file_exists(storage_path('app/public/' . $project->image))) {
                unlink(storage_path('app/public/' . $project->image));
            }
            $filePath = $request->file('fileUpload')->store('uploads', 'public');
            $project->image = $filePath;
        }

        $project->name = $request->name;
        $project->link = $request->link;
        $project->save();

        if ($request->has('skills')) {
            $project->skills()->sync($request->input('skills'));
        }

        return redirect()->route('projects.index')->with('success', 'Project updated successfully!');
    }

    public function delete(string $id)
    {
        $project = Project::findOrFail($id);

        if ($project->image && file_exists(storage_path('app/public/' . $project->image))) {
            unlink(storage_path('app/public/' . $project->image));
        }

        $project->skills()->detach();

        $project->delete();

        return redirect('/projects');
    }
}
