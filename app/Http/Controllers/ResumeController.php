<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResumeEditRequest;
use App\Models\Resume;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class ResumeController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * @var User
         */
        $user = auth()->user();

        $resumes = $user->resumes;
        return view('resumes.index', compact('resumes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //return response()->json(['response'=> 'Hello World']);
        return view('resumes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * @var User
         */
        $user = auth()->user();

        $resume = $user->resumes()->where('title', $request->title)->first();

        if($resume != null){
            return back()
                ->withErrors(['title'=>"You already have a resume with this title"])
                ->withInput(['title'=>$request->title]);
        }

        $resume = $user->resumes()->create([
            'title'=> $request['title'],
            'name'=> $user->name,
            'email'=> $user->email,
        ]);

        return redirect()->route('resumes.index')->with('alert', [
            'type'=> 'success',
            'message'=> "Resume $resume->title created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Resume  $resume
     * @return \Illuminate\Http\Response
     */
    public function show(Resume $resume)
    {
        return view('resumes.show', compact('resume'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Resume  $resume
     * @return \Illuminate\Http\Response
     */
    public function edit(Resume $resume)
    {   
        //if($resume->user->id !== auth()->user()->id){
        //    abort('403');
        //}
        $this->authorize('update', $resume);
        return view('resumes.edit', compact('resume'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Resume  $resume
     * @return \Illuminate\Http\Response
     */
    public function update(ResumeEditRequest $request, Resume $resume)
    {
        
        $data = $request->validated();
                
        if(array_key_exists('picture', $data)){
            if($resume->picture !== null){
                $pathToFile = public_path("storage/$resume->picture");
                File::delete($pathToFile);
            }
            $picture = $data['picture']->store('pictures', 'public');
            Image::make(public_path("storage/$picture"))->fit(800,800)->save();
            $data['picture'] = "/storage/$picture";
        }

        if(!array_key_exists('skills', $data)){
            $resume->skills = null;
        }

        $resume->update($data);

        return redirect()->route('resumes.index')->with('alert', [
            'type'=> 'success',
            'message'=> "Resume $resume->title updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Resume  $resume
     * @return \Illuminate\Http\Response
     */
    public function destroy(Resume $resume)
    {
        $this->authorize('delete', $resume);
        $resume->delete();

        return redirect()->route('resumes.index')->with('alert', [
            'type'=> 'danger',
            'message'=> "Resume $resume->title deleted"
        ]);
    }
}
