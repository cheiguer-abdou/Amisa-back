<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployerController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1); // Default to page 1 if not provided
        $size = $request->input('size', 5); // Default to 5 rows per page if not provided

        $employees = Employer::orderBy('created_at', 'desc')->paginate($size, ['*'], 'page', $page);

        // Add image URLs to each employee
        $employees->getCollection()->transform(function ($employee) {
            if ($employee->image) {
                // Assuming image path is already correct, no need to modify it
                $employee->image_url = asset("storage/employees_images/{$employee->image}");
            } else {
                $employee->image_url = null; // Set image URL to null if no image is available
            }
            return $employee;
        });

        return response()->json($employees, 200);
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required',
    //         'phone' => 'required',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     $employeeData = $request->all();

    //     if ($request->hasFile('image')) {
    //         $imagePath = $request->file('image')->store('employees_images', 'public'); // stocke l'image dans le dossier public/employees_images
    //         $employeeData['image'] = $imagePath;
    //     }

    //     $employer = Employer::create($employeeData);

    //     return response()->json(['message' => 'employer created successfully', 'data' => $employer], 201);
    // }

    public function show($id)
    {
        $employer = Employer::find($id);
        if (!$employer) {
            return response()->json(['message' => 'employer not found'], 404);
        }
        return response()->json(['data' => $employer], 200);
    }

    public function update(Request $request, $id)
    {
        $employer = employer::find($id);
        if (!$employer) {
            return response()->json(['message' => 'employer not found'], 404);
        }

        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $employeeData = $request->all();

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($employer->image) {
                Storage::disk('public')->delete($employer->image);
            }

            $imagePath = $request->file('image')->store('employees_images', 'public'); // stocke la nouvelle image
            $employeeData['image'] = $imagePath;
        }

        $employer->update($employeeData);

        return response()->json(['message' => 'employer updated successfully', 'data' => $employer], 200);
    }

    public function destroy($id)
    {
        $employer = Employer::find($id);
        if (!$employer) {
            return response()->json(['message' => 'employer not found'], 404);
        }
        $employer->delete();
        return response()->json(['message' => 'employer deleted successfully'], 200);
    }

    public function searchEmployees(Request $request)
    {
        $keyword = $request->input('keyword');

        $employees = Employer::where('name', 'like', "%$keyword%")
            ->paginate(5);

        return response()->json($employees, 200);
    }
}
